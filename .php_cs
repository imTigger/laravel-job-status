<?php

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PSR2' => true,
        'array_syntax' => ['syntax' => 'short'],
        'protected_to_private' => false,
        'compact_nullable_typehint' => true,
        'concat_space' => ['spacing' => 'one'],
        'phpdoc_separation' => false,
        'ordered_imports' => true,
        'yoda_style' => null,
    ])
    ->setRiskyAllowed(true)
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in([
                __DIR__ . '/src',
                __DIR__ . '/tests',
            ])
            ->notPath('#c3.php#')
            ->append([__FILE__])
    );
