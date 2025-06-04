<?php

namespace Differ\Loader;

use JsonException;
use RuntimeException;

use function Differ\FileReader\getFileContents;
use function Differ\Parsers\parse;

/**
 * Загружает и парсит содержимое файла в ассоциативный массив.
 *
 * @param string $path Путь к файлу.
 * @return array
 * @throws RuntimeException|JsonException В случае отсутствия расширения или ошибок парсинга.
 */
function loadAndParse(string $path): array
{
    $content = getFileContents($path);
    $ext = pathinfo($path, PATHINFO_EXTENSION);

    if ($ext === '') {
        throw new RuntimeException("File has no extension: $path");
    }

    return parse($content, $ext);
}
