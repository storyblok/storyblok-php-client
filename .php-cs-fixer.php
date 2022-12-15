<?php

$finder = PhpCsFixer\Finder::create()
    ->in([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new PhpCsFixer\Config();
$config->setRiskyAllowed(true);
$config->setRules([
    '@PSR2' => true,
    '@PhpCsFixer' => true,
    '@PhpCsFixer:risky' => true,
    '@PHP56Migration:risky' => true,
    '@PHPUnit57Migration:risky' => true,
    'fopen_flags' => true,
    'linebreak_after_opening_tag' => true,
    'native_constant_invocation' => false,
    'native_function_invocation' => [
        "strict" => false,
    ],
    'blank_line_before_statement' => ['statements' => ['break', 'case', 'continue', 'declare', 'default', 'exit', 'goto', 'return', 'switch', 'throw', 'try']],
    'concat_space' => ['spacing' => 'one'],
    'visibility_required' => false,
    'no_null_property_initialization' => false,
]);

$config->setFinder($finder);

return $config;