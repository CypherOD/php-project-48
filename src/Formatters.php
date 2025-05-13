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
        default => (string) $value,
    };
}

//function toString($value)
//{
//    return trim(var_export($value, true), "'");
//}

function stringify($value, $replacer = ' ', $spacesCount = 4, $level = 1)
{
    if (!is_array($value)) {
        return strValue($value);
    }


    $lines = ['{'];

    $indent = str_repeat($replacer, ($spacesCount * $level - 2));

    foreach ($value as $key => $val) {
        $line = $indent;
        if (is_array($value)) {
            $line .= $key . ': ';
        }
        $line .= is_array($val) ? stringify($val, $replacer, $spacesCount, $level + 1) : strValue($val);
        $lines[] = $line;
    }
    $lines[] = $indent  . '}';
    return implode("\n", $lines);
}

function formatedAsJson(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function formattedAsYml(array $data): string
{
    return Yaml::dump($data, 20);
}

function formatAsStylish($value): string
{
    return stringify($value);
}
