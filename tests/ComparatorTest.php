<?php

namespace Hexlet\Code\ComparatorTest;

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Comparator\getDiff;

class ComparatorTest extends TestCase
{
    public function getFixtureFullPath($fixtureName): false|string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }
    public function testGetDiffNested(): void
    {
        $pathToResult = $this->getFixtureFullPath('nested_result_json_stylish.txt');
        $pathToFile1 = $this->getFixtureFullPath('nested_json_1.json');
        $pathToFile2 = $this->getFixtureFullPath('nested_json_2.json');
        $result = getDiff($pathToFile1, $pathToFile2);

        $this->assertStringEqualsFile($pathToResult, $result);
    }
}
