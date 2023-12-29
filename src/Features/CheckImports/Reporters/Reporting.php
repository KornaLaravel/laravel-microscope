<?php

namespace Imanghafoori\LaravelMicroscope\Features\CheckImports\Reporters;

use Imanghafoori\LaravelMicroscope\FileReaders\FilePath;

trait Reporting
{
    public static function green(string $string)
    {
        return '<fg=green>'.$string.'</>';
    }

    public static function hyphen($string = '')
    {
        return '   ➖  '.$string;
    }

    public static function files($count)
    {
        return '<fg=white> ( '.$count.' file'.($count == 1 ? '' : 's').' )</>';
    }

    public static function addLine($path, $count)
    {
        $output = PHP_EOL.'    '.self::hyphen();
        $output .= self::green(self::normalize($path));
        $output .= self::files($count);

        return $output;
    }

    public static function blue($filesCount)
    {
        return self::hyphen().'<fg=blue>'.$filesCount.'</> ';
    }

    public static function normalize($dirPath)
    {
        $path = trim(FilePath::normalize(str_replace(base_path(), '', $dirPath)), DIRECTORY_SEPARATOR);

        return str_replace(DIRECTORY_SEPARATOR, '/', $path).'/';
    }
}
