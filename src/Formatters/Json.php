<?php

namespace Differ\Formatters\Json;

/**
 * Форматирует diff-дерево в JSON формат.
 *
 * @param array $data Данные diff.
 *
 * @return string Отформатированная строка.
 */

function formatedAsJson(array $data): string
{
    return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
