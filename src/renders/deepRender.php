<?php namespace Gendiff\renders\deepRender;

use Funct\Collection;

DEFINE("COMPLEX_VALUE_SPACE", 6);
DEFINE("CLOSING_SPACE", 2);

function toString($value, $spacesAmount)
{
    $stringTypes = [
        "array" => function ($el) use ($spacesAmount) {
            $keys = array_keys($el);
            $elementSpacing = str_repeat(" ", $spacesAmount + COMPLEX_VALUE_SPACE);
            $closingBracketSpace = str_repeat(" ", $spacesAmount + CLOSING_SPACE);
            $lines = array_map(function ($key) use ($el, $elementSpacing) {
                $value = is_bool($el[$key]) ? var_export($el[$key], true) : $el[$key];
                return "$elementSpacing$key: $value";
            }, $keys);
            $oneLine = implode("\n", $lines);
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
            ["key" => $key, "newValue" => $value] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringValue = toString($value, $spacesAmount);
            return "$spaces+ $key: $stringValue";
        },
        "removed" => function ($el, $spacesAmount, $renderFunc) {
            ["key" => $key, "oldValue" => $value] = $el;
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
            ["key" => $key, "newValue" => $newValue, "oldValue" => $oldValue] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringNewValue = toString($newValue, $spacesAmount);
            $stringOldValue = toString($oldValue, $spacesAmount);
            return ["$spaces+ $key: $stringNewValue", "$spaces- $key: $oldValue"];
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
    $flattenedAst = Collection\flatten($astArray);
    $stringAst = implode("\n", $flattenedAst);
    $closingBracketSpace = str_repeat(" ", $spacesAmount - CLOSING_SPACE);
    return "{\n$stringAst\n$closingBracketSpace}";
}
