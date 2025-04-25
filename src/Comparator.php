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
 * Парсит содержимое файла (JSON или YAML) и возвращает данные в виде массива.
 *
 * @param string $path Путь к файлу, который необходимо распарсить
 *
 * @return array Распарсенные данные из файла
 *
 * @throws RuntimeException В случае:
 *   - невозможности прочитать файл
 *   - отсутствия расширения у файла
 *   - неподдерживаемого формата файла
 *   - ошибок парсинга содержимого файла
 *
 * @example
 * $data = parseFile('/path/to/config.json');
 * $data = parseFile('/path/to/config.yaml');
 */
function parseFile(string $path): array
{
    try {
        $content = getFileContents($path);
        $pathInfo = pathinfo($path);

        if (!isset($pathInfo['extension'])) {
            throw new RuntimeException('File has no extension' . PHP_EOL);
        }

        $ext = strtolower($pathInfo['extension']);
        return match ($ext) {
            'json' => parseJson($content),
            'yml', 'yaml' => parseYaml($content),
            default => throw new RuntimeException("Unsupported format: $ext"),
        };
    } catch (RuntimeException $e) {
        throw new RuntimeException("Filed parse file '$path': " . $e->getMessage() . PHP_EOL, 0, $e);
    }
}

function compareTwoArrays(array $arr1, array $arr2): array
{

}

function valuesChanges()
{

}
