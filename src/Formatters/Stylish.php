<?php

namespace Differ\Formatters\Stylish;

use Differ\enums\Status;

use function Differ\Formatters\Helpers\stringifyValue;

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

        $stylish = fn($val): string => stringifyValue($val, 'stylish', $replacer, $spacesCount, $depth);

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
