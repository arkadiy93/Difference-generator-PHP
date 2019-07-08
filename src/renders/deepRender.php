<?php namespace Gendiff\renders\deepRender;

use Funct\Collection;

function toString($value, $spacesAmount)
{
    $stringTypes = [
      "array" => function ($el) use ($spacesAmount) {
          $keys = array_keys($el);
          $innerSpace = 6;
          $clossingSpace = 2;
          $elementSpacing = str_repeat(" ", $spacesAmount + $innerSpace);
          $closingBracketSpace = str_repeat(" ", $spacesAmount + $clossingSpace);
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
    $flattenedAst = Collection\flatten($astArray);
    $stringAst = implode("\n", $flattenedAst);
    $closingBracketSpace = str_repeat(" ", $spacesAmount - 2);
    return "{\n$stringAst\n$closingBracketSpace}";
}
