<?php namespace Gendiff\index;

use Funct\Collection;

use function Gendiff\parsers\getParsingMethod;
use function Gendiff\renders\index\getRenderMethod;

function getFileExtension($file)
{
    return Collection\last(explode(".", $file));
}

function getAst($firstData, $secondData)
{
    $keys1 = array_keys($firstData);
    $keys2 = array_keys($secondData);
    $allKeys = Collection\union($keys1, $keys2);
    $ast = array_map(function ($key) use ($firstData, $secondData) {
        $firstCondition = isset($firstData[$key]) && is_array($firstData[$key]);
        $secondCondition = isset($secondData[$key]) && is_array($secondData[$key]);
        $currentNode = [];
        if ($firstCondition && $secondCondition) {
            $currentNode = [
                "type" => "nested",
                "key" => $key,
                "children" => getAst($firstData[$key], $secondData[$key])
            ];
        } elseif (!array_key_exists($key, $firstData)) {
            $currentNode = [
                "type" => "added",
                "newValue" => $secondData[$key],
                "key" => $key,
            ];
        } elseif (!array_key_exists($key, $secondData)) {
            $currentNode = [
                "type" => "removed",
                "oldValue" => $firstData[$key],
                "key" => $key,
            ];
        } elseif ($firstData[$key] === $secondData[$key]) {
            $currentNode = [
                "type" => "unchanged",
                "value" => $firstData[$key],
                "key" => $key
            ];
        } else {
            $currentNode = [
                "type" => "changed",
                "newValue" => $secondData[$key],
                "oldValue" => $firstData[$key],
                "key" => $key
            ];
        }
        return $currentNode;
    }, $allKeys);

    return $ast;
}

function genDiff($firstFilePath, $secondFilePath, $format = "pretty")
{
    $extension = getFileExtension($firstFilePath);
    $parse = getParsingMethod($extension);
    $firstFileContent = file_get_contents($firstFilePath);
    $secondFileContent = file_get_contents($secondFilePath);
    $firstData = $parse($firstFileContent);
    $secondData = $parse($secondFileContent);
    $ast = getAst($firstData, $secondData);
    $render = getRenderMethod($format);
    return $render($ast);
}
