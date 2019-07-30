<?php namespace Gendiff\tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\differ\genDiff;

class GendiffTest extends TestCase
{
    protected $beforeDeepPath;
    protected $afterDeepPath;
    protected $beforeFlatPath;
    protected $afterFlatPath;
    protected $beforeYamlPath;
    protected $afterYamlPath;

    protected function setUp()
    {
        $this->beforeDeepPath = __DIR__ . "/__fixtures__/deep/before.json";
        $this->afterDeepPath = __DIR__ . "/__fixtures__/deep/after.json";
        $this->beforeFlatPath = __DIR__ . "/__fixtures__/flat/before.json";
        $this->afterFlatPath = __DIR__ . "/__fixtures__/flat/after.json";
        $this->beforeYamlPath = __DIR__ . "/__fixtures__/flat/before.yml";
        $this->afterYamlPath = __DIR__ . "/__fixtures__/flat/after.yml";
    }

    public function testPrettyFlatRender()
    {
        $correctSolutionPath = __DIR__ . "/__fixtures__/solutions/flatSolution.txt";
        $solutionContent = file_get_contents($correctSolutionPath);
        $trimmedSolution = trim($solutionContent);

        $gendiffResult = genDiff($this->beforeFlatPath, $this->afterFlatPath);
        $this->assertSame($gendiffResult, $trimmedSolution);
    }

    public function testYamlRender()
    {
        $correctSolutionPath = __DIR__ . "/__fixtures__/solutions/flatSolution.txt";
        $solutionContent = file_get_contents($correctSolutionPath);
        $trimmedSolution = trim($solutionContent);

        $gendiffResult = genDiff($this->beforeYamlPath, $this->afterYamlPath);
        $this->assertSame($gendiffResult, $trimmedSolution);
    }

    public function testPrettyDeepRender()
    {
        $correctSolutionPath = __DIR__ . "/__fixtures__/solutions/deepSolution.txt";
        $solutionContent = file_get_contents($correctSolutionPath);
        $trimmedSolution = trim($solutionContent);

        $gendiffResult = genDiff($this->beforeDeepPath, $this->afterDeepPath);
        $this->assertSame($gendiffResult, $trimmedSolution);
    }

    public function testJsonRender()
    {
        $correctSolutionPath = __DIR__ . "/__fixtures__/solutions/jsonSolution.txt";
        $solutionContent = file_get_contents($correctSolutionPath);
        $trimmedSolution = trim($solutionContent);
        $renderingType = "json";

        $gendiffResult = genDiff($this->beforeDeepPath, $this->afterDeepPath, $renderingType);
        $this->assertSame($gendiffResult, $trimmedSolution);
    }

    public function testPlainRender()
    {
        $correctSolutionPath = __DIR__ . "/__fixtures__/solutions/plainSolution.txt";
        $solutionContent = file_get_contents($correctSolutionPath);
        $trimmedSolution = trim($solutionContent);
        $renderingType = "plain";

        $gendiffResult = genDiff($this->beforeDeepPath, $this->afterDeepPath, $renderingType);
        $this->assertSame($gendiffResult, $trimmedSolution);
    }
}
