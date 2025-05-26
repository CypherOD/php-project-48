<?php

namespace Differ\Formatters\Stylish;

use Differ\enums\Status;
use Exception;

/**
 * Преобразует значение в строку.
 *
 * @param mixed  $value        Значение для форматирования.
 * @param string $replacer     Строка для отступов (обычно пробел).
 * @param int    $spacesCount  Количество пробелов в одном уровне отступа.
 * @param int    $depth        Текущий уровень вложенности.
 *
 * @return string Строковое представление значения.
 *
 * @throws Exception Если формат не поддерживается.
 */

function stringifyStylishValue(
    mixed $value,
    string $replacer = ' ',
    int $spacesCount = 4,
    int $depth = 1
): string {
    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        is_null($value) => 'null',
        is_array($value) => stringifyArray($value, $replacer, $spacesCount, $depth + 1),
        default => (string) $value,
    };
}

/**
 * Преобразует ассоциативный массив в форматированную строку для "stylish"-формата.
 *
 * @param array $value Вложенный массив.
 * @param string $replacer Строка для отступов.
 * @param int $spacesCount Количество пробелов в одном уровне отступа.
 * @param int $depth Текущий уровень вложенности.
 *
 * @return string Форматированная строка.
 * @throws Exception
 */

function stringifyArray(array $value, string $replacer, int $spacesCount, int $depth): string
{
    $currentIndent = str_repeat($replacer, $spacesCount * $depth);
    $bracketIndent = str_repeat($replacer, $spacesCount * ($depth - 1));

    $lines = array_reduce(
        array_keys($value),
        function (array $acc, $key) use ($value, $replacer, $spacesCount, $depth, $currentIndent) {
            $stringValue = stringifyStylishValue($value[$key], $replacer, $spacesCount, $depth);
            return [...$acc, "{$currentIndent}{$key}: {$stringValue}"];
        },
        ['{']
    );

    $lines = [...$lines, "{$bracketIndent}}"];
    return implode("\n", $lines);
}


/**
 * Форматирует diff-дерево в Stylish формат..
 *
 * @param array  $value        Массив различий.
 * @param string $replacer     Символ отступа.
 * @param int    $spacesCount  Размер отступа.
 * @param int    $depth        Уровень вложенности.
 *
 * @return string Отформатированная строка.
 *
 * @throws Exception Если встречен неизвестный статус.
 */

function formatAsStylish(
    array $value,
    string $replacer = ' ',
    int $spacesCount = 4,
    int $depth = 1
): string {
    $currentIndent = str_repeat($replacer, $spacesCount * $depth - 2);
    $bracketIndent = str_repeat($replacer, $spacesCount * ($depth - 1));

    $lines = array_reduce($value, function (array $acc, $node) use ($currentIndent, $replacer, $spacesCount, $depth) {
        $key = $node['key'];
        $status = $node['status'];

        $value1 = $node['value1'] ?? null;
        $value2 = $node['value2'] ?? null;
        $childValue = $node['value'] ?? null;

        $stylish = fn($val): string => stringifyStylishValue($val, $replacer, $spacesCount, $depth);

        switch ($status) {
            case Status::NESTED->value:
                $nested = formatAsStylish($childValue, $replacer, $spacesCount, $depth + 1);
                return [...$acc, "{$currentIndent}  {$key}: {$nested}"];

            case Status::ADDED->value:
                return [...$acc, "{$currentIndent}+ {$key}: {$stylish($childValue)}"];

            case Status::REMOVED->value:
                return [...$acc, "{$currentIndent}- {$key}: {$stylish($childValue)}"];

            case Status::UNCHANGED->value:
                return [...$acc, "{$currentIndent}  {$key}: {$stylish($childValue)}"];

            case Status::UPDATED->value:
                $old = $stylish($value1);
                $new = $stylish($value2);
                return [...$acc, "{$currentIndent}- {$key}: {$old}", "{$currentIndent}+ {$key}: {$new}"];

            default:
                throw new \RuntimeException("Неизвестный статус: {$status}");
        }
    }, ['{']);

    $lines = [...$lines, "{$bracketIndent}}"];
    return implode("\n", $lines);
}
