<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

use function Differ\FileReader\getFileContents;

/**
 * Парсит строку в формате JSON в ассоциативный массив.
 *
 * @param string $data Строка в формате JSON.
 * @return array Результирующий ассоциативный массив.
 *
 * @throws \JsonException Если JSON содержит синтаксическую ошибку.
 */
function parseJson(string $data): array
{
    $jsonData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    return $jsonData;
}

/**
 * Парсит строку в формате YAML в ассоциативный массив.
 *
 * @param string $data Строка в формате YAML.
 * @return array Результирующий ассоциативный массив.
 *
 * @throws \Symfony\Component\Yaml\Exception\ParseException Если YAML содержит синтаксическую ошибку.
 */
function parseYaml(string $data): array
{
    $yamlData = Yaml::parse($data);
    return $yamlData;
}

/**
 * Читает и парсит файл в зависимости от его расширения (json/yaml/yml).
 *
 * @param string $path Путь к файлу.
 *
 * @return array Ассоциативный массив содержимого файла.
 *
 * @throws RuntimeException В случае отсутствия расширения или неподдерживаемого формата.
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
