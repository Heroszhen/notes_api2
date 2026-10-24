<?php

declare(strict_types=1);

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['var', 'migrations', 'node_modules', 'assets'])
    ->notPath([
        'config/bundles.php',
        'config/reference.php',
    ]);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setRules([
        '@Symfony' => true,
        'phpdoc_to_comment' => false,
        'declare_strict_types' => true,
    ])
    ->setFinder($finder);
