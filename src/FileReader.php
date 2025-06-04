<?php

namespace Differ\FileReader;

use RuntimeException;

/**
 * Читает содержимое файла и возвращает его.
 *
 * @param string $path Путь к файлу.
 * @return string Содержимое файла.
 * @throws RuntimeException Если файл не найден или произошла ошибка чтения.
 */

function getFileContents(string $path): string
{
    if (!file_exists($path)) {
        throw new RuntimeException("Cannot read file: $path");
    }

    $content = file_get_contents($path);

    if ($content === false) {
        throw new RuntimeException("Failed to read file: $path");
    }

    return $content;
}
