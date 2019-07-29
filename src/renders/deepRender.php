<?php namespace Gendiff\renders\deepRender;

use Funct\Collection;

const COMPLEX_VALUE_SPACE =  6;
const CLOSING_SPACE = 2;

function complexValueToString($value, $spacesAmount)
{
    $keys = array_keys($value);
    $elementSpacing = str_repeat(" ", $spacesAmount + COMPLEX_VALUE_SPACE);
    $closingBracketSpace = str_repeat(" ", $spacesAmount + CLOSING_SPACE);
    $lines = array_map(function ($key) use ($value, $elementSpacing) {
        $value = is_bool($value[$key]) ? var_export($value[$key], true) : $value[$key];
        return "$elementSpacing$key: $value";
    }, $keys);
    $oneLine = implode("\n", $lines);
    return "{\n$oneLine\n$closingBracketSpace}";
}

function defaultValueToString($value)
{
    return is_bool($value) ? var_export($value, true) : $value;
}

function toString($value, $spacesAmount)
{
    return is_array($value) ? complexValueToString($value, $spacesAmount) : defaultValueToString($value);
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
        "added" => function ($el, $spacesAmount) {
            ["key" => $key, "newValue" => $value] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringValue = toString($value, $spacesAmount);
            return "$spaces+ $key: $stringValue";
        },
        "removed" => function ($el, $spacesAmount) {
            ["key" => $key, "oldValue" => $value] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringValue = toString($value, $spacesAmount);
            return "$spaces- $key: $stringValue";
        },
        "unchanged" => function ($el, $spacesAmount) {
            ["key" => $key, "value" => $value] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringValue = toString($value, $spacesAmount);
            return "$spaces  $key: $stringValue";
        },
        "changed" => function ($el, $spacesAmount) {
            ["key" => $key, "newValue" => $newValue, "oldValue" => $oldValue] = $el;
            $spaces = str_repeat(" ", $spacesAmount);
            $stringNewValue = toString($newValue, $spacesAmount);
            $stringOldValue = toString($oldValue, $spacesAmount);
            return ["$spaces+ $key: $stringNewValue", "$spaces- $key: $stringOldValue"];
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
