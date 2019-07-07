<?php namespace Gendiff\renders\flatRender;

use function Funct\Collection\flatten;

DEFINE(SPACES_AMOUNT, 2);

function getRenderMethod($elementType)
{
    $typeMethods = [
        "added" => function ($el) {
            ["key" => $key, "value" => $value] = $el;
            $spaces = str_repeat(" ", SPACES_AMOUNT);
            $stringValue = var_export($value, true);
            return "$spaces+ $key: $stringValue";
        },
        "removed" => function ($el) {
            ["key" => $key, "value" => $value] = $el;
            $spaces = str_repeat(" ", SPACES_AMOUNT);
            $stringValue = var_export($value, true);
            return "$spaces- $key: $stringValue";
        },
        "unchanged" => function ($el) {
            ["key" => $key, "value" => $value] = $el;
            $spaces = str_repeat(" ", SPACES_AMOUNT);
            $stringValue = var_export($value, true);
            return "$spaces  $key: $stringValue";
        },
        "changed" => function ($el) {
            ["key" => $key, "value" => $value, "oldValue" => $oldValue] = $el;
            $spaces = str_repeat(" ", SPACES_AMOUNT);
            $stringValue = var_export($value, true);
            $oldStringValue = var_export($oldValue, true);
            return ["$spaces+ $key: $stringValue", "$spaces- $key: $oldStringValue"];
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
