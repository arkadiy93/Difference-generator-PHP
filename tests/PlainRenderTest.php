<?php namespace Gendiff\tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\index\genDiff;

class PlainRenderTest extends TestCase
{
    protected $beforePath;
    protected $afterPath;
    protected $correctSolution;

    protected function setUp()
    {
        $this->beforePath = __DIR__ . "/__fixtures__/deep/before.json";
        $this->afterPath = __DIR__ . "/__fixtures__/deep/after.json";
        $correctSolutionPath = __DIR__ . "/__fixtures__/solutions/plainSolution.txt";
        $solutionContent = file_get_contents($correctSolutionPath);
        $this->correctSolution = trim($solutionContent);
    }

    public function testRender()
    {
        $renderingType = "plain";
        $gendiffResult = genDiff($this->beforePath, $this->afterPath, $renderingType);
        $this->assertSame($gendiffResult, $this->correctSolution);
    }
}
