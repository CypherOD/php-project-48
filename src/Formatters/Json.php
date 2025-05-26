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
    return json_encode($data, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
}
