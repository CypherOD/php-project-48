<?php

namespace Hexlet\Code\Parsers;

use Symfony\Component\Yaml\Yaml;

function parseJson(string $data): array
{
    $jsonData = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
    return $jsonData;
}

function parseYaml(string $data): array
{
    $yamlData = Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
    return $yamlData;
}