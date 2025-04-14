<?php

namespace Hexlet\Code\Differ;

function getDiff(string $path1, string $path2): string
{
    $data1 = readJson($path1);
    $data2 = readJson($path2);

    $keys = array_unique(array_merge(array_keys($data1), array_keys($data2)));
    asort($keys);

    $result = [];
    $result[] = '{';
    foreach ($keys as $key) {
        $result[] = getDiffLine($key, $data1, $data2);
    }
    $result[] = '}';
    return implode(PHP_EOL, $result);
}

function getDiffLine(string $key, array $data1, array $data2, string $ident = '  '): string
{
    if (array_key_exists($key, $data1) && array_key_exists($key, $data2)) {
        $value1 = strValue($data1[$key]);
        if ($data1[$key] !== $data2[$key]) {
            $value2 = strValue($data2[$key]);
            return "$ident- $key: $value1" . PHP_EOL . "  + $key: $value2";
        } else {
            return "$ident  $key: $value1";
        }
    } else if (array_key_exists($key, $data1) && !array_key_exists($key, $data2)) {
        $value1 = strValue($data1[$key]);
        return "$ident- $key: $value1";
    } else {
        $value2 = strValue($data2[$key]);
        return "$ident+ $key: $value2";
    }
}

function readJson(string $path): array
{
    $data = file_get_contents($path);
    $jsonData = json_decode($data);
    return get_object_vars($jsonData);
}

function strValue($value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }
    if (is_null($value)) {
        return 'null';
    }
    return $value;
}
