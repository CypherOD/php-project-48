<?php

namespace Hexlet\Code\Comparator;

use Exception;
use JsonException;
use RuntimeException;
use function Hexlet\Code\FileReader\getFileContents;
use function Hexlet\Code\Parsers\parseJson;
use function Hexlet\Code\Parsers\parseYaml;

function getDiff(string $path1, string $path2, string $format): array
{
    $data1 = parseFile($path1);
    $data2 = parseFile($path2);

    return [$data1, $data2];
}

/**
 * @throws JsonException
 */
function parseFile(string $path): array
{
    try {
        $content = getFileContents($path);
    } catch (RuntimeException $e) {
        echo $e->getMessage();
    }

//    if (!isset($pathInfo['extension'])) {
//        throw new RuntimeException("File has no extension");
//    }

    $ext = strtolower(pathinfo($path)['extension']);

    return match($ext) {
        'json' => parseJson($content),
        'yml', 'yaml' => parseYaml($content),
        default => throw new RuntimeException("Unsupported format: $ext"),
    };
}

