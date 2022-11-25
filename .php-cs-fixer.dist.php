<?php
$finder = PhpCsFixer\Finder::create()
    ->exclude(__DIR__ . '/vendor')
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setLineEnding("\n")
    ->setUsingCache(false)
    ->setRules([
        '@PSR2' => true,
        'strict_param' => true,
        'return_type_declaration' => true,
        'void_return' => true,
        'global_namespace_import' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
    ->setFinder($finder);