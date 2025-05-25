<?php

namespace Differ\Formatters;

use function Differ\Formatters\Json\formatedAsJson;
use function Differ\Formatters\Stylish\formatAsStylish;
use function Differ\Formatters\Plain\formatAsPlain;

/**
 * Выбирает и вызывает нужный форматтер.
 *
 * @param array  $data   Данные для форматирования.
 * @param string $format Формат: 'stylish', 'plain', 'json'.
 *
 * @return string Отформатированный результат.
 *
 * @throws \InvalidArgumentException Если формат не поддерживается.
 */

function formatOutput(array $data, string $format): string
{
    return match ($format) {
        'json' => formatedAsJson($data),
        'stylish' => formatAsStylish($data),
        'plain' => formatAsPlain($data),
        default => throw new \InvalidArgumentException("Неизвестный формат: $format"),
    };
}
