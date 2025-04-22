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
        $pathToResult = $this->getFixtureFullPath('resultJson.txt');
        $pathToFile1 = $this->getFixtureFullPath('file1.json');
        $pathToFile2 = $this->getFixtureFullPath('file2.json');
        $result = getDiff($pathToFile1, $pathToFile2);

        $this->assertStringEqualsFile($pathToResult, $result);
    }
}
