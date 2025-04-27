<?php

namespace Hexlet\Code\Formatters;

use Symfony\Component\Yaml\Yaml;

/**
 * Преобразует значение в строку с поддержкой всех типов данных.
 *
 * @param mixed $value Любое значение для преобразования
 * @return string Строковое представление значения
 */

function strValue(mixed $value): string
{
    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        is_null($value) => 'null',
        default => (string)$value,
    };
}

function formatedAsJson(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function formattedAsYml(array $data): string
{
    return Yaml::dump($data, 20);
}
