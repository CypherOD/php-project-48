<?php

namespace Hexlet\Code\Comparator;

use Exception;
use JsonException;
use RuntimeException;

use function Hexlet\Code\FileReader\getFileContents;
use function Hexlet\Code\Parsers\parseJson;
use function Hexlet\Code\Parsers\parseYaml;
use function Hexlet\Code\Formatters\formatedAsJson;
use function Hexlet\Code\Formatters\formatAsStylish;

enum Operation: string
{
    case ADDED = '+ ';
    case REMOVED = '- ';
    case UNCHANGED = '  ';
}

/**
 * Строит дерево различий между двумя файлами и форматирует результат в указанный формат.
 *
 * Загружает и парсит два файла, сравнивает их содержимое, а затем форматирует результат.
 * Пока реализован только формат JSON через функцию `formatedAsJson()`.
 *
 * @param string $path1 Путь к первому входному файлу.
 * @param string $path2 Путь ко второму входному файлу.
 * @param string $format Формат вывода (например, 'json', 'stylish', 'plain').
 * @return string Строковое представление различий между файлами.
 */
function getDiff(string $path1, string $path2, string $format = 'stylish'): string
{
    $data1 = parseFile($path1);
    $data2 = parseFile($path2);
    $result = compareTwoArrays($data1, $data2);

    return formatAsStylish($result);
}

/**
 * Парсит содержимое файла (JSON или YAML) и возвращает данные в виде массива.
 *
 * @param string $path Путь к файлу, который необходимо распарсить
 *
 * @return array Распарсенные данные из файла
 *
 * @throws RuntimeException В случае:
 *   - невозможности прочитать файл
 *   - отсутствия расширения у файла
 *   - неподдерживаемого формата файла
 *   - ошибок парсинга содержимого файла
 *
 * @example
 * $data = parseFile('/path/to/config.json');
 * $data = parseFile('/path/to/config.yaml');
 */
function parseFile(string $path): array
{
    try {
        $content = getFileContents($path);
        $pathInfo = pathinfo($path);

        if (!isset($pathInfo['extension'])) {
            throw new RuntimeException('File has no extension' . PHP_EOL);
        }

        $ext = strtolower($pathInfo['extension']);
        return match ($ext) {
            'json' => parseJson($content),
            'yml', 'yaml' => parseYaml($content),
            default => throw new RuntimeException("Unsupported format: $ext"),
        };
    } catch (RuntimeException $e) {
        throw new RuntimeException("Filed parse file '$path': " . $e->getMessage() . PHP_EOL, 0, $e);
    }
}

/**
 * Сравнивает два ассоциативных массива и возвращает различия в виде дерева.
 *
 * Каждое отличие представлено в результирующем массиве с префиксом,
 * определяемым через enum Operation. Вложенные структуры сравниваются рекурсивно.
 *
 * @param array $arr1 Первый массив для сравнения.
 * @param array $arr2 Второй массив для сравнения.
 * @return array Массив различий, сгруппированных по ключам с префиксами изменений.
 */
function compareTwoArrays(array $arr1, array $arr2): array
{
    $keys = collect(array_keys(array_merge($arr1, $arr2)));
    $result = $keys->sort()->reduce(fn ($acc, $key) => buildDiff($acc, $key, $arr1, $arr2), []);
    return $result;
}

/**
 * Добавляет различие по конкретному ключу в аккумулятор результата.
 *
 * Обрабатывает наличие ключа в каждом из массивов, сравнивает значения:
 * - если ключ есть только в одном из массивов, результат помечается как ADDED или REMOVED;
 * - если значения разные — добавляется два состояния (REMOVED и ADDED);
 * - если значения равны — сохраняется как UNCHANGED;
 * - если оба значения — массивы, вызывается рекурсивное сравнение.
 *
 * @param array $acc Аккумулятор для сбора различий.
 * @param string $key Ключ, по которому происходит сравнение.
 * @param array $data1 Первый массив.
 * @param array $data2 Второй массив.
 * @return array Обновлённый аккумулятор с добавленным результатом по текущему ключу.
 */
function buildDiff(array $acc, string $key, array $data1, array $data2): array
{
    $has1 = array_key_exists($key, $data1);
    $has2 = array_key_exists($key, $data2);

    $val1 = $data1[$key] ?? null;
    $val2 = $data2[$key] ?? null;

    if ($has1 && !$has2) {
        $acc[Operation::REMOVED->value . $key] = $val1;
    } elseif (!$has1 && $has2) {
        $acc[Operation::ADDED->value . $key] = $val2;
    } elseif (is_array($val1) && is_array($val2)) {
        $acc[Operation::UNCHANGED->value . $key] = compareTwoArrays($val1, $val2);
    } elseif ($val1 !== $val2) {
        $acc[Operation::REMOVED->value . $key] = $val1;
        $acc[Operation::ADDED->value . $key] = $val2;
    } else {
        $acc[Operation::UNCHANGED->value . $key] = $val1;
    }

    return $acc;
}
