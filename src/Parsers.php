<?php

namespace Hexlet\Code\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseJson(string $data): array
{
    $jsonData = json_decode($data);
    return get_object_vars($jsonData);
}

function parseYaml(string $data): array
{
    $yamlData = Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
    return $yamlData;
}