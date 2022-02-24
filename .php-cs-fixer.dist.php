<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('public/min')
    ->notPath('application/Bootstrap.php')
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PhpCsFixer' => true,
    ])
    ->setFinder($finder)
;