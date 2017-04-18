<?php

return PhpCsFixer\Config::create()
        ->setRiskyAllowed(true)
        ->setUsingCache(true)
        ->setRules([
            "@PSR2" => true,
            "is_null" => true,
            "lowercase_cast" => true,
            "no_useless_return" => true,
            "self_accessor" => true,
            "ternary_operator_spaces" => true,
            "visibility_required" => true,
            "method_separation" => true,
            "method_argument_space" => true,
            "include" => true,
            "encoding" => true,
            "cast_spaces" => true,
            "blank_line_after_opening_tag" => true,
            "binary_operator_spaces" => true,
            "phpdoc_single_line_var_spacing" => true,
            "phpdoc_types" => true,
            "phpdoc_var_without_name" => true,
            "phpdoc_align" => true,
            "phpdoc_order" => true,
            "phpdoc_scalar" => true,
            "non_printable_character" => true,
            "no_trailing_comma_in_singleline_array" => true,
            "no_empty_statement" => true,
            "no_empty_comment" => true,
            "no_closing_tag" => true,
            "lowercase_keywords" => true,
            "linebreak_after_opening_tag" => true,
            "array_syntax" => ["syntax" => "short"]
        ])
        ->setFinder(
            PhpCsFixer\Finder::create()
            ->exclude(['bin', 'vendor'])
            ->in(__DIR__)
        );
