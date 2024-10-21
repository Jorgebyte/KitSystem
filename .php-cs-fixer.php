<?php

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        '@PSR12' => true,
        'strict_param' => true,
        'declare_strict_types' => true,
        'function_typehint_space' => true,
        'align_multiline_comment' => ['comment_type' => 'phpdocs_only'],
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
        'visibility_required' => ['elements' => ['method', 'property']],
        'binary_operator_spaces' => [
            'default' => 'single_space'
        ],
        'phpdoc_align' => [
            'align' => 'vertical',
        ],
        'no_extra_blank_lines' => ['tokens' => ['extra']],
        'header_comment' => [
            'header' => '   KitSystem
   Api: 5.3.0
   Version: 1.0.0
   Author: Jorgebyte',
        ],
        'single_blank_line_at_eof' => true,
        'no_trailing_whitespace' => true,
        'no_unused_imports' => true,
        'array_syntax' => ['syntax' => 'short'],
        'cast_spaces' => ['space' => 'single'],
    ])
    ->setFinder($finder);