<?php

namespace Differ\Differ;

use Differ\enums\Status;

use function Differ\Parsers\parseFile;
use function Differ\Formatters\formatOutput;

/**
 * Генерирует различия между двумя файлами и возвращает результат в заданном формате.
 *
 * @param string $path1 Путь к первому файлу.
 * @param string $path2 Путь ко второму файлу.
 * @param string $format Формат вывода (по умолчанию 'stylish').
 *                      Допустимые значения: 'stylish', 'plain', 'json'.
 *
 * @return string Форматированный результат различий.
 */

function genDiff(string $path1, string $path2, string $format = 'stylish'): string
{
    $data1 = parseFile($path1);
    $data2 = parseFile($path2);
    $result = compareTwoArrays($data1, $data2);
    return formatOutput($result, $format);
}

/**
 * Проверяет, является ли массив ассоциативным.
 *
 * @param array $arr Входной массив.
 *
 * @return bool true, если массив ассоциативный, иначе false.
 */
function isAssoc(array $arr): bool
{
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * Рекурсивно сравнивает два ассоциативных массива и возвращает массив различий.
 *
 * @param array $arr1 Первый массив.
 * @param array $arr2 Второй массив.
 *
 * @return array Массив различий, где каждый элемент — это ассоциативный массив с ключами:
 *               - key (string)
 *               - status (string)
 *               - value (mixed) или oldValue/newValue (если статус 'updated')
 */
function compareTwoArrays(array $arr1, array $arr2): array
{
    $keys = collect(array_keys(array_merge($arr1, $arr2)));
    return $keys->sort()->reduce(function ($acc, $key) use ($arr1, $arr2) {
        return buildDiffLine($acc, $key, $arr1, $arr2);
    }, []);
}

/**
 * Строит одну строку различий по ключу между двумя массивами.
 *
 * @param array  $acc   Аккумулирующий массив различий.
 * @param string $key   Текущий ключ, сравниваемый в обоих массивах.
 * @param array  $data1 Первый массив.
 * @param array  $data2 Второй массив.
 *
 * @return array Обновлённый аккумулятор различий с добавленным элементом.
 */
function buildDiffLine(array $acc, string $key, array $data1, array $data2): array
{

    $val1 = $data1[$key] ?? null;
    $val2 = $data2[$key] ?? null;

    $has1 = array_key_exists($key, $data1);
    $has2 = array_key_exists($key, $data2);


    if ($has1 && !$has2) {
        $acc[] = [
            'key' => $key,
            'value' => $val1,
            'status' => Status::REMOVED->value,
        ];
    } elseif (!$has1 && $has2) {
        $acc[] = [
            'key' => $key,
            'value' => $val2,
            'status' => Status::ADDED->value,
        ];
    } elseif (is_array($val1) && is_array($val2) && isAssoc($val1) && isAssoc($val2)) {
        $acc[] = [
            'key' => $key,
            'value' => compareTwoArrays($val1, $val2),
            'status' => Status::NESTED->value,
        ];
    } elseif ($val1 !== $val2) {
        $acc[] = [
            'key' => $key,
            'value1' => $val1,
            'value2' => $val2,
            'status' => Status::UPDATED->value,
        ];
    } else {
        $acc[] = [
            'key' => $key,
            'value' => $val1,
            'status' => Status::UNCHANGED->value,
        ];
    }

    return $acc;
}
