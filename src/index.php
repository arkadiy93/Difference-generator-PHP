<?php

namespace Gendiff\index;

use Symfony\Component\Yaml\Yaml;
use function Funct\Collection\last;
use function Funct\Collection\union;
use function Funct\Collection\flatten;

function getFileExtension($file)
{
    return last(explode(".", $file));
}

function getParsingMethod($extension)
{
    $parsers = [
      "json" => function ($pathToFile) {
        $fileContent = file_get_contents($pathToFile);
        return json_decode($fileContent, true);
      },
      "yml" => function ($pathToFile) {
        return Yaml::parseFile($pathToFile);
      }
    ];
    return $parsers[$extension];
}

function toString($value, $spacesAmount)
{
    $stringTypes = [
      "array" => function ($el) use ($spacesAmount) {
          $keys = array_keys($el);
          $elementSpacing = str_repeat(" ", $spacesAmount + 4);
          $lines = array_map(function ($key) use ($el, $elementSpacing) {
            $value = is_bool($el[$key]) ? var_export($el[$key], true) : $el[$key];
            return "$elementSpacing$key: $value";
          }, $keys);
          $oneLine = implode("\n", $lines);
          $closingBracketSpace = str_repeat(" ", $spacesAmount + 2);
          return "{\n$oneLine\n$closingBracketSpace}";
      },
      "default" => function ($value) {
        return is_bool($value) ? var_export($value, true) : $value;
      }
    ];

    return is_array($value) ? $stringTypes["array"]($value) : $stringTypes["default"]($value);
}

function getRenderMethod($elementType)
{
    $typeMethods = [
        "nested" => function ($el, $spacesAmount, $renderFunc) {
            ["children" => $children, "key" => $key] = $el;
            $stringValue = $renderFunc($children, $spacesAmount + 4);
            $spaces = str_repeat(" ", $spacesAmount);
            return "$spaces  $key: $stringValue";
        },
        "added" => function ($el, $spacesAmount, $renderFunc) {
            ["key" => $key, "value" => $value] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringValue = toString($value, $spacesAmount);
            return "$spaces+ $key: $stringValue";
        },
        "removed" => function ($el, $spacesAmount, $renderFunc) {
            ["key" => $key, "value" => $value] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringValue = toString($value, $spacesAmount);
            return "$spaces- $key: $stringValue";
        },
        "unchanged" => function ($el, $spacesAmount, $renderFunc) {
            ["key" => $key, "value" => $value] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringValue = toString($value, $spacesAmount);
            return "$spaces  $key: $stringValue";
        },
        "changed" => function ($el, $spacesAmount, $renderFunc) {
            ["key" => $key, "value" => $value, "oldValue" => $oldValue] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringValue = toString($value, $spacesAmount);
            $stringOldValue = toString($oldValue, $spacesAmount);
            return ["$spaces+ $key: $stringValue", "$spaces- $key: $oldValue"];
        },
    ];
    return $typeMethods[$elementType];
}

function render($ast, $spacesAmount = 2)
{
    $astArray = array_map(function ($el) use ($spacesAmount) {
        $runProperRenderer = getRenderMethod($el["type"]);
        return $runProperRenderer($el, $spacesAmount, function ($children, $spacesAmount) {
            return render($children, $spacesAmount);
        });
    }, $ast);
    $flattenedAst = flatten($astArray);
    $stringAst = implode("\n", $flattenedAst);
    $closingBracketSpace = str_repeat(" ", $spacesAmount - 2);
    return "{\n$stringAst\n$closingBracketSpace}";
}


function getAst($firstData, $secondData)
{
    $keys1 = array_keys($firstData);
    $keys2 = array_keys($secondData);
    $allKeys = union($keys1, $keys2);
    $ast = array_map(function ($key) use ($firstData, $secondData) {
        if (is_array($firstData[$key]) && is_array($secondData[$key])) {
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
            "value" => $firstData[$key],
            "oldValue" => $secondData[$key],
            "key" => $key
            ];
        }
    }, $allKeys);

    return $ast;
}

function genDiff($firstFilePath, $secondFilePath)
{
    $extension = getFileExtension($firstFilePath);
    $parse = getParsingMethod($extension);
    $firstData = $parse($firstFilePath);
    $secondData = $parse($secondFilePath);
    $ast = getAst($firstData, $secondData);
    $result = render($ast);
    var_dump($result);
}
