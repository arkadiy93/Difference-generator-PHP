<?php

namespace Gendiff\index;

use Funct;

function getDataStatus($data1, $data2)
{
    $mergedData = Funct\Collection\union(array_keys($data1), array_keys($data2));
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
    $result = "";
    foreach ($dataStatus as $value => $key) {
        if ($key === "unchanged") {
            $dataValue = var_export($data1[$value], true);
            $result = $result . "    $value: $dataValue\n";
        } elseif ($key === "deleted") {
            $dataValue = var_export($data1[$value], true);
            $result = $result . "  - $value: $dataValue\n";
        } elseif ($key === "added") {
            $dataValue = var_export($data2[$value], true);
            $result = $result . "  + $value: $dataValue\n";
        } else {
            $beforeValue = var_export($data1[$value], true);
            $afterValue = var_export($data2[$value], true);
            $result = $result . "  - $value: $beforeValue\n";
            $result = $result . "  + $value: $afterValue\n";
        }
    }
    return "{\n$result\n}";
}

function genDiff($firstFilePath, $secondFilePath)
{
    $firstFile = file_get_contents($firstFilePath);
    $firstData = json_decode($firstFile, true);
    $secondFile = file_get_contents($secondFilePath);
    $secondData = json_decode($secondFile, true);
    $dataStatus = getDataStatus($firstData, $secondData);
    $parsedData = parse($dataStatus, $firstData, $secondData);
    return $parsedData;
}
