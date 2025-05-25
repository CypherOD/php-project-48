<?php

namespace Differ\Formatters\Plain;

use Differ\enums\Status;

use function Differ\Formatters\Helpers\stringifyValue;

/**
 * Форматирует diff-дерево в plain формат..
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
