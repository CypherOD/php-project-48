<?php

namespace Differ\Formatters;

use Differ\enums\Status;

use function Differ\Differ\isAssoc;

function strValue(mixed $value, string $replacer = ' ', int $spacesCount = 4, int $depth = 1): string
{
    return match (true) {
        is_bool($value) => $value ? 'true' : 'false',
        is_null($value) => 'null',
        is_array($value) => stringifyPlainArray($value, $replacer, $spacesCount, $depth + 1),
        default => (string) $value,
    };
}

function stringifyPlainArray(array $value, string $replacer, int $spacesCount, int $depth): string
{
    $currentIndent = str_repeat($replacer, $spacesCount * $depth);
    $bracketIndent = str_repeat($replacer, $spacesCount * ($depth - 1));
    $lines = ['{'];

    foreach ($value as $key => $val) {
        $stringValue = strValue($val, $replacer, $spacesCount, $depth);
        $lines[] = "{$currentIndent}{$key}: {$stringValue}";
    }

    $lines[] = "{$bracketIndent}}";
    return implode("\n", $lines);
}

function stringify(array $value, string $replacer = ' ', int $spacesCount = 4, int $depth = 1): string
{
    $currentIndent = str_repeat($replacer, $spacesCount * $depth - 2);
    $bracketIndent = str_repeat($replacer, $spacesCount * ($depth - 1));

    $lines = ['{'];

    foreach ($value as $node) {
        $key = $node['key'];
        $status = $node['status'];

        $lines[] = match ($status) {
            Status::NESTED->value =>
                "{$currentIndent}  {$key}: " . stringify($node['value'], $replacer, $spacesCount, $depth + 1),

            Status::ADDED->value =>
                "{$currentIndent}+ {$key}: " . strValue($node['value'], $replacer, $spacesCount, $depth),

            Status::REMOVED->value =>
                "{$currentIndent}- {$key}: " . strValue($node['value'], $replacer, $spacesCount, $depth),

            Status::UNCHANGED->value =>
                "{$currentIndent}  {$key}: " . strValue($node['value'], $replacer, $spacesCount, $depth),

            Status::UPDATED->value => implode("\n", [
                "{$currentIndent}- {$key}: " . strValue($node['value1'], $replacer, $spacesCount, $depth),
                "{$currentIndent}+ {$key}: " . strValue($node['value2'], $replacer, $spacesCount, $depth),
            ]),

            default => throw new \Exception("Неизвестный статус: {$status}"),
        };
    }

    $lines[] = "{$bracketIndent}}";
    return implode("\n", $lines);
}

function formatedAsJson(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function formatAsStylish(array $value): string
{
    return stringify($value);
}


function toString(mixed $value): string
{
    return match (true) {
        is_array($value) => '[complex]',
        default => (string) $value,
    };
}

function formatAsPlain(mixed $value, array $path = []): string
{
    $lines = [];
    foreach ($value as $node) {
        $status = $node['status'];
        $path[] = $node['key'];


        // $lines[] = match ($status) {
        //     Status::NESTED->value =>
        //         formatAsPlain($node['value'], $path),

        //     Status::ADDED->value =>
        //         //'Propery ' .  implode('.', $path) . ' added ' . toString($node['value']),
        //         "Property '{implode('.', $path)}' was added with value: false"

        //     Status::REMOVE->value =>
        //         'Propery remove ' . toString($node['value']),

        //     Status::UNCHANGED->value =>
        //         'Propery unchanged ' . toString($node['value']),

        //     Status::UPDATED->value => implode("\n", [
        //         'Propery added ' . toString($node['value1']),
        //         'Propery remove ' . toString($node['value2'])
        //     ]),
        // };
        $lines = [];
        switch($status) {
            case Status::NESTED->value:
                $lines[] = formatAsPlain($node['value'], $path);
                break;
            case Status::ADDED->value:
                $strPath = implode('.', $path);
                $lines[] = "Property '$strPath' was added with value: {$value['value']}";
                break;
            case Status::REMOVED->value:
                $lines[] = "Property '$strPath' was removed";
                break;
            case Status::UPDATED->value:
                break;
            default:
                break;
        }
    };

    return implode("\n", $lines);
}

function formatOutput(array $data, string $format): string
{
    return match ($format) {
        'json' => formatedAsJson($data),
        'stylish' => formatAsStylish($data),
        'plain' => formatAsPlain($data),
        default => throw new \InvalidArgumentException("Неизвестный формат: $format"),
    };
}
