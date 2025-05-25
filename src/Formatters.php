<?php

namespace Differ\Formatters;

use Differ\enums\Status;

/**
 * Преобразует значение в строку в зависимости от заданного формата.
 *
 * @param mixed  $value        Значение для форматирования.
 * @param string $format       Формат вывода ('plain' или 'stylish').
 * @param string $replacer     Строка для отступов (обычно пробел).
 * @param int    $spacesCount  Количество пробелов в одном уровне отступа.
 * @param int    $depth        Текущий уровень вложенности.
 *
 * @return string Строковое представление значения.
 *
 * @throws \Exception Если формат не поддерживается.
 */

function stringifyValue(
    mixed $value,
    string $format = 'plain',
    string $replacer = ' ',
    int $spacesCount = 4,
    int $depth = 1
): string {
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        return match ($format) {
            'plain' => '[complex value]',
            'stylish' => stringifyStylishArray($value, $replacer, $spacesCount, $depth + 1),
            default => throw new \Exception("Формат {$format} не поддерживается для массивов"),
        };
    }

    return match ($format) {
        'plain' => "'{$value}'",
        'stylish' => (string) $value,
        default => throw new \Exception("Формат {$format} не поддерживается для скаляров"),
    };
}

/**
 * Преобразует ассоциативный массив в форматированную строку для "stylish"-вывода.
 *
 * @param array  $value        Вложенный массив.
 * @param string $replacer     Строка для отступов.
 * @param int    $spacesCount  Количество пробелов в одном уровне отступа.
 * @param int    $depth        Текущий уровень вложенности.
 *
 * @return string Форматированная строка.
 */

function stringifyStylishArray(array $value, string $replacer, int $spacesCount, int $depth): string
{
    $currentIndent = str_repeat($replacer, $spacesCount * $depth);
    $bracketIndent = str_repeat($replacer, $spacesCount * ($depth - 1));
    $lines = ['{'];

    foreach ($value as $key => $val) {
        $stringValue = stringifyValue($val, 'stylish', $replacer, $spacesCount, $depth);
        $lines[] = "{$currentIndent}{$key}: {$stringValue}";
    }

    $lines[] = "{$bracketIndent}}";
    return implode("\n", $lines);
}

/**
 * Рекурсивно форматирует diff-массив в стиль "stylish".
 *
 * @param array  $value        Массив различий.
 * @param string $replacer     Символ отступа.
 * @param int    $spacesCount  Размер отступа.
 * @param int    $depth        Уровень вложенности.
 *
 * @return string Строка в стиле stylish.
 *
 * @throws \Exception Если встречен неизвестный статус.
 */

function formatAsStylish(
    array $value,
    string $replacer = ' ',
    int $spacesCount = 4,
    int $depth = 1
): string {
    $currentIndent = str_repeat($replacer, $spacesCount * $depth - 2);
    $bracketIndent = str_repeat($replacer, $spacesCount * ($depth - 1));

    $lines = ['{'];

    foreach ($value as $node) {
        $key = $node['key'];
        $status = $node['status'];

        $value1 = $node['value1'] ?? null;
        $value2 = $node['value2'] ?? null;
        $childValue = $node['value'] ?? null;

        $stylish = fn($val) => stringifyValue($val, 'stylish', $replacer, $spacesCount, $depth);

        switch ($status) {
            case Status::NESTED->value:
                $nested = formatAsStylish($childValue, $replacer, $spacesCount, $depth + 1);
                $lines[] = "{$currentIndent}  {$key}: {$nested}";
                break;

            case Status::ADDED->value:
                $lines[] = "{$currentIndent}+ {$key}: {$stylish($childValue)}";
                break;

            case Status::REMOVED->value:
                $lines[] = "{$currentIndent}- {$key}: {$stylish($childValue)}";
                break;

            case Status::UNCHANGED->value:
                $lines[] = "{$currentIndent}  {$key}: {$stylish($childValue)}";
                break;

            case Status::UPDATED->value:
                $old = $stylish($value1);
                $new = $stylish($value2);
                $lines[] = "{$currentIndent}- {$key}: {$old}";
                $lines[] = "{$currentIndent}+ {$key}: {$new}";
                break;

            default:
                throw new \Exception("Неизвестный статус: {$status}");
        }
    }

    $lines[] = "{$bracketIndent}}";
    return implode("\n", $lines);
}

/**
 * Форматирует diff в плоский текстовый стиль.
 *
 * @param array $nodes Массив узлов diff-дерева.
 * @param array $path  Текущий путь в дереве свойств.
 *
 * @return string Отформатированная строка.
 */

function formatAsPlain(array $nodes, array $path = []): string
{
    $lines = [];

    foreach ($nodes as $node) {
        $key = $node['key'];
        $propertyPath = [...$path, $key];
        $fullPath = implode('.', $propertyPath);
        $status = $node['status'];

        switch ($status) {
            case Status::NESTED->value:
                $lines[] = formatAsPlain($node['value'], $propertyPath);
                break;

            case Status::ADDED->value:
                $value = stringifyValue($node['value'], 'plain');
                $lines[] = "Property '{$fullPath}' was added with value: {$value}";
                break;

            case Status::REMOVED->value:
                $lines[] = "Property '{$fullPath}' was removed";
                break;

            case Status::UPDATED->value:
                $oldValue = stringifyValue($node['value1'], 'plain');
                $newValue = stringifyValue($node['value2'], 'plain');
                $lines[] = "Property '{$fullPath}' was updated. From {$oldValue} to {$newValue}";
                break;
        }
    }

    return implode("\n", $lines);
}

/**
 * Форматирует diff-дерево в JSON формат.
 *
 * @param array $data Данные diff.
 *
 * @return string Строка в формате JSON.
 */

function formatedAsJson(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Выбирает и вызывает нужный форматтер.
 *
 * @param array  $data   Данные для форматирования.
 * @param string $format Формат: 'stylish', 'plain', 'json'.
 *
 * @return string Отформатированный результат.
 *
 * @throws \InvalidArgumentException Если формат не поддерживается.
 */

function formatOutput(array $data, string $format): string
{
    return match ($format) {
        'json' => formatedAsJson($data),
        'stylish' => formatAsStylish($data),
        'plain' => formatAsPlain($data),
        default => throw new \InvalidArgumentException("Неизвестный формат: $format"),
    };
}
