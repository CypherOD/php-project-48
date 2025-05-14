<?php

namespace Differ\ComparatorTest;

use PHPUnit\Framework\TestCase;

use function Differ\DIffer\getDiff;

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
        $pathToFile1 = $this->getFixtureFullPath('nested1.json');
        $pathToFile2 = $this->getFixtureFullPath('nested2.json');
        $result = getDiff($pathToFile1, $pathToFile2);

        $this->assertStringEqualsFile($pathToResult, $result);
    }
}
