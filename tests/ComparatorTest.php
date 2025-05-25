<?php

namespace ComparatorTest;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use function Differ\DIffer\getDiff;

class ComparatorTest extends TestCase
{
    public function getFixtureFullPath($fixtureName): false|string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    #[DataProvider('getDiffStylishProvider')]
    public function testGetDiffStylish(string $result, string $file1, string $file2): void
    {
        $pathToResult = $this->getFixtureFullPath($result);
        $pathToFile1 = $this->getFixtureFullPath($file1);
        $pathToFile2 = $this->getFixtureFullPath($file2);
        $result = getDiff($pathToFile1, $pathToFile2);
        $this->assertStringEqualsFile($pathToResult, $result);
    }

    #[DataProvider('getDiffPlainProvider')]
    public function testGetDiffPlain(string $result, string $file1, string $file2): void
    {
        $pathToResult = $this->getFixtureFullPath($result);
        $pathToFile1 = $this->getFixtureFullPath($file1);
        $pathToFile2 = $this->getFixtureFullPath($file2);
        $result = getDiff($pathToFile1, $pathToFile2, 'plain');
        $this->assertStringEqualsFile($pathToResult, $result);
    }

    public static function getDiffStylishProvider(): array
    {
        return [
            'json' => ['nested_result_stylish.txt', 'nested1.json', 'nested2.json'],
            'yml' => ['nested_result_stylish.txt', 'nested1.yml', 'nested2.yml'],
            'mixed' => ['nested_result_stylish.txt', 'nested1.yml', 'nested2.json'],
        ];
    }

    public static function getDiffPlainProvider(): array
    {
        return [
            'json' => ['nested_result_plain.txt', 'nested1.json', 'nested2.json'],
            'yml' => ['nested_result_plain.txt', 'nested1.yml', 'nested2.yml'],
            'mixed' => ['nested_result_plain.txt', 'nested1.yml', 'nested2.json'],
        ];
    }
}
