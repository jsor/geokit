<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers(array(
        'concat_with_spaces',
        'ordered_use',
        'long_array_syntax',
    ))
    ->finder($finder);
