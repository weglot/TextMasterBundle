<?php

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->in(__DIR__)
    ->exclude('vendor')
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setParallelConfig(PhpCsFixer\Runner\Parallel\ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PHP82Migration' => true,
        '@Symfony' => true,
        '@Symfony:risky' => true,

        // Override Symfony config
        'method_argument_space' => [
            'after_heredoc' => true,
            'on_multiline' => 'ensure_fully_multiline',
            'attribute_placement' => 'same_line',
        ],
        'no_superfluous_phpdoc_tags' => ['allow_mixed' => true, 'remove_inheritdoc' => true],
        'single_line_throw' => false,

        // Added rules
        'array_indentation' => true,
        'attribute_empty_parentheses' => true,
        'explicit_string_variable' => true,
        'general_phpdoc_annotation_remove' => [
            'annotations' => ['author', 'since', 'package', 'subpackage', 'group'],
        ],
        'header_comment' => ['header' => ''],
        'no_superfluous_elseif' => true,
        'no_useless_else' => true,
        'nullable_type_declaration_for_default_null_value' => true,
        'operator_linebreak' => true,
        'phpdoc_no_empty_return' => true,
        'phpdoc_separation' => ['groups' => [
            [
                'phpstan-template',
                'phpstan-template-covariant',
                'phpstan-extends',
                'phpstan-implements',
                'phpstan-var',
                'phpstan-param',
                'phpstan-return',
            ],
            ['Assert\\*', 'EmailCheckerAssert\\*', 'WeglotAssert\\*'],
            ['ORM\\*'],
            ['Groups', 'SerializedName'],
        ]],
        'phpdoc_to_comment' => ['ignored_tags' => ['phpstan-var', 'phpstan-throws']],
        'php_unit_test_case_static_method_calls' => true,
    ])
    ->setFinder($finder);
