<?php

namespace Differ\Formatters\Json;

use JsonException;

/**
 * Форматирует diff-дерево в JSON формат.
 *
 * @param array $data Данные diff.
 *
 * @return string Отформатированная строка.
 * @throws JsonException
 */

function formatedAsJson(array $data): string
{
    $json = json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
    if ($json === false) {
        throw new \RuntimeException('Failed to encode JSON');
    }
    return $json;
}
