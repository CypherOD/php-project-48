<?php

namespace Hexlet\Code\ComparatorTest;

use PHPUnit\Framework\TestCase;

class ComparatorTest extends TestCase
{
    public function getFixtureFullPath($fixtureName): false|string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }

    public function testGetDiff(): void
    {
        $pathToResult = $this->getFixtureFullPath('flatten_result_json_stylish.txt');
        $pathToFile1 = $this->getFixtureFullPath('flatten_json_1.json');
        $pathToFile2 = $this->getFixtureFullPath('flatten_json_2.json');
        $result = getDiff($pathToFile1, $pathToFile2);

        $this->assertStringEqualsFile($pathToResult, $result);
    }
}
