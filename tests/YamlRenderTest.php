<?php namespace Gendiff\tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\index\genDiff;

class YamlRenderTest extends TestCase
{
    protected static $beforePath;
    protected static $afterPath;
    protected static $correctSolution;

    public static function setUpBeforeClass()
    {
        self::$beforePath = __DIR__ . "/__fixtures__/flat/before.yml";
        self::$afterPath = __DIR__ . "/__fixtures__/flat/after.yml";
        $correctSolutionPath = __DIR__ . "/__fixtures__/solutions/flatSolution.txt";
        $solutionContent = file_get_contents($correctSolutionPath);
        self::$correctSolution = trim($solutionContent);
    }

    public function testRender()
    {
        $gendiffResult = genDiff(self::$beforePath, self::$afterPath);
        $this->assertSame($gendiffResult, self::$correctSolution);
    }
}
