<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__.'/config',
        __DIR__.'/src',
        __DIR__.'/tests'
    ])
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'binary_operator_spaces' => [
            'align_equals' => false,
            'align_double_arrow' => true,
        ],
        'array_syntax' => ['syntax' => 'short'],
        'cast_spaces' => false,
        'linebreak_after_opening_tag' => true,
        'ordered_imports' => true,
        'phpdoc_add_missing_param_annotation' => ['only_untyped' => false],
        'phpdoc_order' => true,
        'yoda_style' => null,
    ])
    ->setFinder($finder)
;
