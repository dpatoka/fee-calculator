<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/../bin',
        __DIR__ . '/../src',
        __DIR__ . '/../tests'
    ])
    ->name('*.php')
;

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRules([
        '@PSR12' => true,
        'cast_spaces' => ['space' => 'single'],
        'class_attributes_separation' => [
            'elements' => [
                'const' => 'none',
                'method' => 'one',
                'property' => 'one',
                'trait_import' => 'none',
                'case' => 'none',
            ],
        ],
        'concat_space' => ['spacing' => 'one'],
        'declare_strict_types' => true,
        'global_namespace_import' => true,
        'no_unused_imports' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait', 'case',
                'constant_public', 'constant_protected', 'constant_private',
                'property_public_static', 'property_protected_static', 'property_private_static',
                'property_public', 'property_protected', 'property_private',
                'method_public_abstract_static', 'method_protected_abstract_static',
                'method_public_static', 'method_protected_static', 'method_private_static',
                'construct', 'destruct', 'magic', 'phpunit',
                'method_public_abstract', 'method_protected_abstract',
                'method_public', 'method_protected', 'method_private',
            ],
        ],
        'ordered_imports' => [
            'sort_algorithm' => 'alpha',
            'imports_order' => ['class', 'function', 'const'],
        ],
        'phpdoc_align' => [
            'align' => 'left',
        ],
        'phpdoc_order' => true,
        'phpdoc_order_by_value' => [
            'annotations' => ['throws'],
        ],
        'single_line_throw' => false,
        'static_lambda' => true,
        'strict_comparison' => true,
        'types_spaces' => [
            'space' => 'none',
            'space_multiple_catch' => 'single',
        ],
        'yoda_style' => [
            'equal' => false,
            'identical' => false,
            'less_and_greater' => false,
        ],
    ])
    ->setFinder($finder)
    ;