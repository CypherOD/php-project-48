<?php

namespace DifferTest;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function Differ\DIffer\genDiff;

class DifferTest extends TestCase
{
    public function getFixtureFullPath($fixtureName): false|string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    #[DataProvider('genDiffStylishProvider')]
    public function testGetDiffStylish(string $result, string $file1, string $file2): void
    {
        $pathToResult = $this->getFixtureFullPath($result);
        $pathToFile1 = $this->getFixtureFullPath($file1);
        $pathToFile2 = $this->getFixtureFullPath($file2);
        $result = genDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToResult, $result);
    }

    #[DataProvider('genDiffPlainProvider')]
    public function testGetDiffPlain(string $result, string $file1, string $file2): void
    {
        $pathToResult = $this->getFixtureFullPath($result);
        $pathToFile1 = $this->getFixtureFullPath($file1);
        $pathToFile2 = $this->getFixtureFullPath($file2);
        $result = genDiff($pathToFile1, $pathToFile2, 'plain');
        $this->assertStringEqualsFile($pathToResult, $result);
    }

    #[DataProvider('genDiffJsonProvider')]
    public function testGetDiffJson(string $result, string $file1, string $file2): void
    {
        $pathToResult = $this->getFixtureFullPath($result);
        $pathToFile1 = $this->getFixtureFullPath($file1);
        $pathToFile2 = $this->getFixtureFullPath($file2);
        $result = genDiff($pathToFile1, $pathToFile2, 'json');
        $this->assertStringEqualsFile($pathToResult, $result);
    }

    public static function genDiffStylishProvider(): array
    {
        return [
            'json' => ['nested_result_stylish.txt', 'nested1.json', 'nested2.json'],
            'yml' => ['nested_result_stylish.txt', 'nested1.yml', 'nested2.yml'],
            'mixed' => ['nested_result_stylish.txt', 'nested1.yml', 'nested2.json'],
        ];
    }

    public static function genDiffPlainProvider(): array
    {
        return [
            'json' => ['nested_result_plain.txt', 'nested1.json', 'nested2.json'],
            'yml' => ['nested_result_plain.txt', 'nested1.yml', 'nested2.yml'],
            'mixed' => ['nested_result_plain.txt', 'nested1.yml', 'nested2.json'],
        ];
    }

    public static function genDiffJsonProvider(): array
    {
        return [
            'json' => ['nested_result_json.txt', 'nested1.json', 'nested2.json'],
            'yml' => ['nested_result_json.txt', 'nested1.yml', 'nested2.yml'],
            'mixed' => ['nested_result_json.txt', 'nested1.yml', 'nested2.json'],
        ];
    }
}
