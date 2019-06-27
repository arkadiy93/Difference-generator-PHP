<?php

namespace Gendiff\index;

use Symfony\Component\Yaml\Yaml;
use function Funct\Collection\last;
use function Funct\Collection\union;

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

function getDataStatus($data1, $data2)
{
    $mergedData = union(array_keys($data1), array_keys($data2));
    $result = [];
    foreach ($mergedData as $key) {
        if (!array_key_exists($key, $data2)) {
            $result[$key] = "deleted";
        } elseif (!array_key_exists($key, $data1)) {
            $result[$key] = "added";
        } else {
            $result[$key] = $data1[$key] === $data2[$key] ? "unchanged" : "changed";
        }
    }
    return $result;
}

function parse($dataStatus, $data1, $data2)
{
    $result = [];
    foreach ($dataStatus as $value => $key) {
        if ($key === "unchanged") {
            $dataValue = var_export($data1[$value], true);
            $result[] = "    $value: $dataValue";
        } elseif ($key === "deleted") {
            $dataValue = var_export($data1[$value], true);
            $result[] = "  - $value: $dataValue";
        } elseif ($key === "added") {
            $dataValue = var_export($data2[$value], true);
            $result[] = "  + $value: $dataValue";
        } else {
            $beforeValue = var_export($data1[$value], true);
            $afterValue = var_export($data2[$value], true);
            $result[] = "  - $value: $beforeValue";
            $result[] = "  + $value: $afterValue";
        }
    }
    $connectedResult = implode("\n", $result);
    return "{\n$connectedResult\n}";
}

function genDiff($firstFilePath, $secondFilePath)
{
    $extension = getFileExtension($firstFilePath);
    $parse = getParsingMethod($extension);
    $firstData = $parse($firstFilePath);
    $secondData = $parse($secondFilePath);
    $dataStatus = getDataStatus($firstData, $secondData);
    $parsedData = parse($dataStatus, $firstData, $secondData);
    return $parsedData;
}
