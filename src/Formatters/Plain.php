<?php

namespace Differ\Formatters\Plain;

use Differ\Enums\Status;

/**
 * Преобразует значение в строку.
 *
 * @param mixed  $value        Значение для форматирования.
 *
 * @return string Строковое представление значения.
 */

function stringifyPlainValue(mixed $value): string
{
    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        is_null($value) => 'null',
        is_array($value) => '[complex value]',
        is_numeric($value) => (string) $value,
        default => "'{$value}'",
    };
}

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
    $lines = array_reduce($nodes, function ($acc, $node) use ($path) {
        $key = $node['key'];
        $propertyPath = [...$path, $key];
        $fullPath = implode('.', $propertyPath);
        $status = $node['status'];

        switch ($status) {
            case Status::NESTED->value:
                return [...$acc, ...explode("\n", formatAsPlain($node['value'], $propertyPath))];

            case Status::ADDED->value:
                $value = stringifyPlainValue($node['value']);
                return [...$acc, "Property '{$fullPath}' was added with value: {$value}"];

            case Status::REMOVED->value:
                return [...$acc, "Property '{$fullPath}' was removed"];

            case Status::UPDATED->value:
                $oldValue = stringifyPlainValue($node['value1']);
                $newValue = stringifyPlainValue($node['value2']);
                return [...$acc, "Property '{$fullPath}' was updated. From {$oldValue} to {$newValue}"];

            default:
                return $acc;
        }
    }, []);

    return implode("\n", $lines);
}
