<?php

use PHPUnit\Framework\TestCase;

use function Hexlet\Code\Differ\strValue;
use function Hexlet\Code\Differ\getDiffLine;
use function Hexlet\Code\Differ\getDiff;
class DifferTest extends TestCase
{
    public function getFixtureFullPath($fixtureName): false|string
    {
        $parts = [__DIR__, 'fixtures', $fixtureName];
        return realpath(implode('/', $parts));
    }
    public function testStrValue(): void
    {
        $this->assertEquals('false', strValue(false));
        $this->assertEquals('null', strValue(null));
        $this->assertEquals('20', strValue(20));
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