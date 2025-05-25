<?php

namespace Differ\Formatters\Helpers;

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
