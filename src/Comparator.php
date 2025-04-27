<?php

namespace Hexlet\Code\Comparator;

use Exception;
use JsonException;
use RuntimeException;

use function Hexlet\Code\FileReader\getFileContents;
use function Hexlet\Code\Parsers\parseJson;
use function Hexlet\Code\Parsers\parseYaml;
use function Hexlet\Code\Formatters\formatedAsJson;

enum Operation: string
{
    case ADDED = '+ ';
    case REMOVED = '- ';
    case UNCHANGED = '  ';
}

function getDiff(string $path1, string $path2, string $format): string
{
    $data1 = parseFile($path1);
    $data2 = parseFile($path2);
    $result = compareTwoArrays($data1, $data2);

    return formatedAsJson($result);
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
    $sharedKeys = collect(array_keys(array_merge($arr1, $arr2)));
        $result = $sharedKeys->sort()->reduce(fn ($acc, $key) => buildDiff($acc, $key, $arr1, $arr2), []);
    return $result;
}

function buildDiff(array $acc, string $key, array $arr1, array $arr2): array
{
    if (array_key_exists($key, $arr1) && !array_key_exists($key, $arr2)) {
        $acc[Operation::REMOVED->value . $key] = $arr1[$key];
    } elseif (!array_key_exists($key, $arr1) && array_key_exists($key, $arr2)) {
        $acc[Operation::ADDED->value . $key] = $arr2[$key];
    } else {
        if ($arr1[$key] !== $arr2[$key]) {
            $acc[Operation::REMOVED->value . $key] = $arr1[$key];
            $acc[Operation::ADDED->value . $key] = $arr2[$key];
        } else {
            $acc[Operation::UNCHANGED->value . $key] = $arr1[$key];
        }
    }
    return $acc;
}
