<?php

namespace Differ\Differ;

use RuntimeException;

use function Differ\Parsers\parseFile;
use function Differ\Formatters\formatOutput;

/**
 * Перечисление возможных статусов различий между значениями.
 */
enum Status: string
{
    case ADD = 'add';
    case REMOVE = 'remove';
    case UNCHANGED = 'unchanged';
    case NESTED = 'nested';
    case UPDATED = 'updated';
}

/**
 * Сравнивает содержимое двух файлов и возвращает различия в указанном формате.
 *
 * @param string $path1 Путь к первому файлу.
 * @param string $path2 Путь ко второму файлу.
 * @param string $format Формат вывода (по умолчанию 'stylish').
 *
 * @return string|array Возвращает строку в заданном формате или массив различий.
 *
 * @throws RuntimeException В случае ошибки при чтении или парсинге файлов.
 */
function getDiff(string $path1, string $path2, string $format = 'stylish'): string | array
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
 * @return bool Возвращает true, если массив ассоциативный.
 */
function isAssoc(array $arr): bool
{
    return array_keys($arr) !== range(0, count($arr) - 1);
}

/**
 * Рекурсивно сравнивает два ассоциативных массива и строит diff-массив.
 *
 * @param array $arr1 Первый массив.
 * @param array $arr2 Второй массив.
 *
 * @return array Массив различий, отсортированный по ключам.
 */
function compareTwoArrays(array $arr1, array $arr2): array
{
    $keys = collect(array_keys(array_merge($arr1, $arr2)));
    return $keys->sort()->reduce(function ($acc, $key) use ($arr1, $arr2) {
        return buildDiffLine($acc, $key, $arr1, $arr2);
    }, []);
}

/**
 * Определяет тип различия между значениями по ключу.
 *
 * @param mixed $val1 Значение из первого массива.
 * @param mixed $val2 Значение из второго массива.
 * @param bool $has1 Присутствует ли ключ в первом массиве.
 * @param bool $has2 Присутствует ли ключ во втором массиве.
 *
 * @return Status Статус (добавлен, удалён, вложенный, неизменён, обновлён).
 */
function getNodeStatus(mixed $val1, mixed $val2, bool $has1, bool $has2): Status
{
    return match (true) {
        $has1 && !$has2 => Status::REMOVE,
        !$has1 && $has2 => Status::ADD,
        is_array($val1) && is_array($val2) && isAssoc($val1) && isAssoc($val2) => Status::NESTED,
        $val1 === $val2 => Status::UNCHANGED,
        default => Status::UPDATED,
    };
}

/**
 * Формирует строку/пару diff'а по ключу и статусу.
 *
 * @param string $key Ключ, по которому сравниваются значения.
 * @param Status $status Статус различия.
 * @param mixed $val1 Значение из первого массива.
 * @param mixed $val2 Значение из второго массива.
 *
 * @return array Один или два элемента с префиксами ключей и значениями различий.
 */
function buildNodeDiffLine(string $key, Status $status, mixed $val1, mixed $val2): array
{
    return match ($status) {
        Status::REMOVE => [Status::REMOVE->value . ' ' . $key => $val1],
        Status::ADD => [Status::ADD->value . ' ' . $key => $val2],
        Status::UNCHANGED => [Status::UNCHANGED->value . ' ' . $key => $val1],
        Status::UPDATED => [
            Status::REMOVE->value . ' ' . $key => $val1,
            Status::ADD->value . ' ' . $key => $val2,
        ],
        Status::NESTED => [Status::NESTED->value . ' ' . $key => compareTwoArrays($val1, $val2)],
    };
}

/**
 * Добавляет строку различий по ключу в аккумулятор результата.
 *
 * @param array $acc Аккумулятор для результата.
 * @param string $key Ключ, по которому происходит сравнение.
 * @param array $data1 Первый массив.
 * @param array $data2 Второй массив.
 *
 * @return array Обновлённый аккумулятор с добавленным diff-элементом.
 */
function buildDiffLine(array $acc, string $key, array $data1, array $data2): array
{
    $has1 = array_key_exists($key, $data1);
    $has2 = array_key_exists($key, $data2);

    $val1 = $data1[$key] ?? null;
    $val2 = $data2[$key] ?? null;

    $status = getNodeStatus($val1, $val2, $has1, $has2);
    $line = buildNodeDiffLine($key, $status, $val1, $val2);

    return array_merge($acc, $line);
}
