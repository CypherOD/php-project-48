<?php

namespace Differ\Parsers;

use JsonException;
use RuntimeException;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * Парсит строку в формате JSON в ассоциативный массив.
 *
 * @param string $data Строка в формате JSON.
 * @return array Результирующий ассоциативный массив.
 *
 * @throws JsonException Если JSON содержит синтаксическую ошибку.
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
 * @throws ParseException Если YAML содержит синтаксическую ошибку.
 */
function parseYaml(string $data): array
{
    $yamlData = Yaml::parse($data);
    return $yamlData;
}

/**
 * Преобразует строку данных в ассоциативный массив на основе формата.
 *
 * @param string $data   Строка, содержащая данные в формате JSON или YAML.
 * @param string $format Формат данных ('json', 'yaml', 'yml').
 *
 * @return array Ассоциативный массив, полученный после парсинга.
 *
 * @throws RuntimeException Если указан неподдерживаемый формат.
 * @throws JsonException Если данные в формате JSON содержат синтаксические ошибки.
 * @throws ParseException Если данные YAML некорректны.
 */
function parse(string $data, string $format): array
{
    return match (strtolower($format)) {
        'json' => parseJson($data),
        'yml', 'yaml' => parseYaml($data),
        default => throw new RuntimeException("Unsupported format: $format"),
    };
}
