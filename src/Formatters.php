<?php

namespace Differ\Formatters;

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
 * Заменяет статусный префикс ключа (add/remove/unchanged/nested) на символ (+/-/ )
 *
 * @param string $key Ключ с префиксом.
 * @return string Ключ с заменённым префиксом.
 */
function convertStatusPrefix(string $key): string
{
    return preg_replace_callback(
        '/^(add|remove|unchanged|nested)\s+/i',
        function ($matches) {
            return match ($matches[1]) {
                'add' => '+ ',
                'remove' => '- ',
                'unchanged' => '  ',
                default => '',
            };
        },
        $key
    );
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
function stringify(array $value, string $replacer = ' ', int $spacesCount = 4, int $level = 1): string
{
    if (!is_array($value)) {
        return strValue($value);
    }
    $indent = str_repeat($replacer, ($spacesCount * $level - 2));
    $bracketIndent = str_repeat($replacer, $spacesCount * ($level - 1));
    $lines = ['{'];
    foreach ($value as $key => $val) {
        $line = $indent;

        $line .= convertStatusPrefix($key) . ': ';
        $line .= is_array($val) ? stringify($val, $replacer, $spacesCount, $level + 1) : strValue($val);
        $lines[] = $line;
    }
    $lines[] = $bracketIndent  . '}';
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
