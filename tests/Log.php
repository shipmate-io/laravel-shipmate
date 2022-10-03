<?php

namespace Shipmate\Shipmate\Tests;

class Log
{
    public static function clear(): void
    {
        file_put_contents(__DIR__.'/test.log', '');
    }

    public static function read(): array
    {
        $lines = explode(PHP_EOL, file_get_contents(__DIR__.'/test.log'));
        array_pop($lines);

        return $lines;
    }

    public static function write(string $line): void
    {
        file_put_contents(__DIR__.'/test.log', $line.PHP_EOL, FILE_APPEND);
    }
}
