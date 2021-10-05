<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests'
    ])
;

return (new PhpCsFixer\Config())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        'linebreak_after_opening_tag' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => true,
        'phpdoc_order' => true,
        'yoda_style' => false,
        'no_superfluous_phpdoc_tags' => false,
        'declare_strict_types' => true,
    ])
;
