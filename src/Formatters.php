<?php

namespace Hexlet\Code\Formatters;
use Symfony\Component\Yaml\Yaml;

const INDENT = 2;


/**
 * Преобразует значение в строку с поддержкой всех типов данных.
 *
 * @param mixed $value Любое значение для преобразования
 * @return string Строковое представление значения
 */
function strValue(mixed $value): string
{
    // TODO: Изменить функцию под stylish
    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        is_null($value) => 'null',
        default => (string) $value,
    };
}

function toString($value)
{
    return trim(var_export($value, true), "'");
}

// BEGIN (write your solution here)
// TODO - исправить отступы!
function stringify($value, $replacer = ' ', $spacesCount = 4, $level = 0)
{
    // Простые типы — просто возвращаем как строку
    if (!is_array($value)) {
        return toString($value);
    }

    // Если массив
    $isAssoc = array_keys($value) !== range(0, count($value) - 1);
    $indent = str_repeat($replacer, $spacesCount * $level);
    $nextIndent = str_repeat($replacer, $spacesCount * ($level + 1));

    $lines = ['{'];
    foreach ($value as $key => $val) {
        $line = $nextIndent;
        if ($isAssoc) {
            $line .= $key . ': ';
        }
        $line .= is_array($val) ? stringify($val, $replacer, $spacesCount, $level + 1) : toString($val);
        $lines[] = $line;
    }
    $lines[] = $indent . '}';
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
