<?php

namespace Differ\Formatters;

use Differ\enums\Status;
use InvalidArgumentException;

/**
 * Преобразует значение в строку для вывода.
 *
 * - Булевы значения переводятся в 'true' / 'false'
 * - null в 'null'
 * - Все остальные приводятся к строке
 *
 * @param mixed $value Значение для преобразования.
 * @return string Строковое представление значения.
 */
function strValue(mixed $value): string
{
    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        is_null($value) => 'null',
        default => (string) $value,
    };
}

/**
 * Рекурсивно форматирует ассоциативный массив в стиле "stylish".
 *
 * @param mixed $value Значение (массив или примитив).
 * @param string $replacer Символ отступа.
 * @param int $spacesCount Количество пробелов в отступе.
 * @param int $level Текущий уровень вложенности.
 * @return string Отформатированная строка.
 */
function stringify(mixed $value, string $replacer = ' ', int $spacesCount = 4, int $depth = 1): string
{
    if (!is_array($value)) {
        return strValue($value);
    }
    $currentIndent = str_repeat(' ', $spacesCount * $depth - 2);
    $bracketIndent = str_repeat(' ', $spacesCount * ($depth - 1));

    $lines = array_reduce($value, function ($acc, $item) use ($currentIndent, $replacer, $spacesCount, $depth) {
        $status = $item['status'];

        switch ($status) {
            case (Status::REMOVE->value):
                $acc[] = "-{$currentIndent}{$item['key']}: {$item['value']}";
                break;
            case (Status::ADDED->value):
                $acc[] = "+{$currentIndent}{$item['key']}: {$item['value']}";
                break;
            case (Status::NESTED->value):
                $nestedValue = stringify($item, $replacer, $spacesCount, $depth + 1);
                $acc[] = "{$currentIndent}{$item['key']}: {$nestedValue}";
                break;
            case (Status::UPDATED->value):

        }

        return $acc;
    }, ['{']);

    $lines[] = "{$bracketIndent}}";
    return implode("\n", $lines);
}

/**
 * Преобразует массив различий в форматированный JSON.
 *
 * @param array $data Ассоциативный массив различий.
 * @return string Отформатированный JSON.
 */
function formatedAsJson(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

/**
 * Форматирует массив различий в виде "stylish".
 *
 * @param mixed $value Значение для форматирования.
 * @return string Строковое представление в стиле stylish.
 */
function formatAsStylish(array $value): string
{
    return stringify($value);
}

/**
 * Форматирует результат сравнения в указанный формат.
 *
 * Поддерживаемые форматы:
 * - 'json' — вывод в формате JSON
 * - 'stylish' — человекочитаемый стиль (по умолчанию)
 *
 * @param array $data Массив различий, полученный после сравнения данных.
 * @param string $format Формат вывода ('json' или 'stylish').
 * @return string Отформатированная строка.
 *
 * @throws InvalidArgumentException Если передан неподдерживаемый формат.
 */
function formatOutput(array $data, string $format): string
{
    return match ($format) {
        'json' => formatedAsJson($data),
        'stylish' => formatAsStylish($data),
        default => throw new \InvalidArgumentException("Неизвестный формат: $format"),
    };
}
