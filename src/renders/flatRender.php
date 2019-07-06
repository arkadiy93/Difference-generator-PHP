<?php

namespace Gendiff\renders\flatRender;

function render($dataStatus, $data1, $data2)
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
