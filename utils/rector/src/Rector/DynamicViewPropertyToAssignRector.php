<?php

declare(strict_types=1);

namespace Utils\Rector\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\Assign;
use PhpParser\Node\Expr\PropertyFetch;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class DynamicViewPropertyToAssignRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change view dynamic property assignment to assign() method call', [
            new CodeSample(
                '$this->view->dynamicProperty = $value',
                '$this->view->assign(\'dynamicProperty\', $value)'
            ),
        ]);
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Assign::class];
    }

    /**
     * @param Assign $node
     */
    public function refactor(Node $node): ?Node
    {
        if (
            !$node->var instanceof PropertyFetch
        ) {
            return null;
        }

        $propertyName = $this->getName($node->var);
        $expr = $node->expr;
        $shouldBeModified = false;

        $this->traverseNodesWithCallable($node->var, function (Node $singleNode) use (&$shouldBeModified) {
            if (!$singleNode instanceof PropertyFetch) {
                return null;
            }

            $leftOperand = $singleNode->var;

            /*
            Vérifie qu'il y a un double property fetch
            (e.g. $this->view->variable et pas $this->variable)
            */
            if ('view' !== $this->getName($leftOperand)) {
                return null;
            }

            $this->traverseNodesWithCallable($leftOperand, function (Node $singleLeftOperand) use (&$shouldBeModified) {
                if (!$singleLeftOperand instanceof PropertyFetch) {
                    return null;
                }

                $shouldBeModified = true;
            });
        });

        if ($shouldBeModified) {
            return $this->nodeFactory->createMethodCall('this->view', 'assign', [
                $propertyName,
                $expr,
            ]);
        }

        return $node;
    }
}
