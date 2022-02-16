<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('public/min')
    ->in(__DIR__)
;

$config = new PhpCsFixer\Config();
return $config->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder)
;