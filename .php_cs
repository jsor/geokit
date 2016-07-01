<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers(array(
        'concat_with_spaces',
        'ordered_use',
        'extra_empty_lines',
        'phpdoc_params',
        'remove_lines_between_uses',
        'return',
        'unused_use',
        'whitespacy_lines',
        'long_array_syntax',
        'spaces_cast'
    ))
    ->finder($finder);
