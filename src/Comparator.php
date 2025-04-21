<?php

namespace Hexlet\Code\Comparator;

use Exception;
use RuntimeException;
use function Hexlet\Code\FileReader\getFileContents;
use function Hexlet\Code\Parsers\parseJson;
use function Hexlet\Code\Parsers\parseYaml;
function getDiff(string $path1, string $path2, string $format): array
{
    $data1 = parseFile($path1, $format);
    $data2 = parseFile($path2, $format);

    return [$data1, $data2];
}


//
function parseFile(string $path, string $format)
{
    try {
        $content = getFileContents($path);
    } catch (RuntimeException $e) {
        echo $e->getMessage();
    }

    return match($format) {
        'json' => parseJson($content),
        'yml', 'yaml' => parseYaml($content),
        default => throw new Exception("Unsupported format: $format"),
    };
}

