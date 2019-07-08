<?php namespace Gendiff\tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\index\genDiff;

class JsonRenderTest extends TestCase
{
    protected static $beforePath;
    protected static $afterPath;
    protected static $correctSolution;

    public static function setUpBeforeClass()
    {
        self::$beforePath = __DIR__ . "/__fixtures__/deep/before.json";
        self::$afterPath = __DIR__ . "/__fixtures__/deep/after.json";
        $correctSolutionPath = __DIR__ . "/__fixtures__/solutions/jsonSolution.txt";
        $solutionContent = file_get_contents($correctSolutionPath);
        self::$correctSolution = trim($solutionContent);
    }

    public function testRender()
    {
        $renderingType = "json";
        $gendiffResult = genDiff(self::$beforePath, self::$afterPath, $renderingType);
        $this->assertSame($gendiffResult, self::$correctSolution);
    }
}
