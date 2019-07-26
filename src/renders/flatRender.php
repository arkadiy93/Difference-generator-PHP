<?php namespace Gendiff\renders\flatRender;

use function Funct\Collection\flatten;

DEFINE("ELEMENTS_SPACING", 2);

function getRenderMethod($elementType)
{
    $typeMethods = [
        "added" => function ($el) {
            ["key" => $key, "newValue" => $value] = $el;
            $spaces = str_repeat(" ", ELEMENTS_SPACING);
            $stringValue = var_export($value, true);
            return "$spaces+ $key: $stringValue";
        },
        "removed" => function ($el) {
            ["key" => $key, "oldValue" => $value] = $el;
            $spaces = str_repeat(" ", ELEMENTS_SPACING);
            $stringValue = var_export($value, true);
            return "$spaces- $key: $stringValue";
        },
        "unchanged" => function ($el) {
            ["key" => $key, "value" => $value] = $el;
            $spaces = str_repeat(" ", ELEMENTS_SPACING);
            $stringValue = var_export($value, true);
            return "$spaces  $key: $stringValue";
        },
        "changed" => function ($el) {
            ["key" => $key, "newValue" => $newValue, "oldValue" => $oldValue] = $el;
            $spaces = str_repeat(" ", ELEMENTS_SPACING);
            $newStringValue = var_export($newValue, true);
            $oldStringValue = var_export($oldValue, true);
            return ["$spaces+ $key: $newStringValue", "$spaces- $key: $oldStringValue"];
        },
    ];
    return $typeMethods[$elementType];
}

function render($ast)
{
    $astArray = array_map(function ($el) {
        $runProperRenderer = getRenderMethod($el["type"]);
        return $runProperRenderer($el);
    }, $ast);

    $flattenedAst = flatten($astArray);
    $connectedResult = implode("\n", $flattenedAst);
    return "{\n$connectedResult\n}";
}
