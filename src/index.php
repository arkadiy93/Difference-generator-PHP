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
        if ($firstCondition && $secondCondition) {
            return [
            "type" => "nested",
            "key" => $key,
            "children" => getAst($firstData[$key], $secondData[$key])
            ];
        } elseif (!array_key_exists($key, $firstData)) {
            return [
            "type" => "added",
            "value" => $secondData[$key],
            "key" => $key,
            ];
        } elseif (!array_key_exists($key, $secondData)) {
            return [
            "type" => "removed",
            "value" => $firstData[$key],
            "key" => $key,
            ];
        } elseif ($firstData[$key] === $secondData[$key]) {
            return [
              "type" => "unchanged",
              "value" => $firstData[$key],
              "key" => $key
            ];
        } else {
            return [
            "type" => "changed",
            "value" => $secondData[$key],
            "oldValue" => $firstData[$key],
            "key" => $key
            ];
        }
    }, $allKeys);

    return $ast;
}

function genDiff($firstFilePath, $secondFilePath, $format = "pretty")
{
    $extension = getFileExtension($firstFilePath);
    $parse = getParsingMethod($extension);
    $firstConfig = $parse($firstFilePath);
    $secondConfig = $parse($secondFilePath);
    $ast = getAst($firstConfig, $secondConfig);
    $render = getRenderMethod($format);
    return $render($ast);
}
