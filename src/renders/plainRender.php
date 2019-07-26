<?php namespace Gendiff\renders\plainRender;

use Funct\Collection;

function toString($value)
{
    $stringTypes = [
      "array" => function ($el) {
          return "complex value";
      },
      "default" => function ($value) {
        return is_bool($value) ? var_export($value, true) : $value;
      }
    ];

    return is_array($value) ? $stringTypes["array"]($value) : $stringTypes["default"]($value);
}

function getPathString($path, $key)
{
    $path = implode(".", $path);
    return empty($path) ? $key : "$path.$key";
}

function getRenderMethod($elementType)
{
    $typeMethods = [
        "nested" => function ($el, $path, $renderFunc) {
            ["children" => $children, "key" => $key] = $el;
            $path[] = $key;
            return $renderFunc($children, $path);
            return "hello";
        },
        "added" => function ($el, $path, $renderFunc) {
            ["key" => $key, "newValue" => $value] = $el;
            $propertyPath = getPathString($path, $key);
            $propertyValue = toString($value);
            return "Property '$propertyPath' was added with value: '$propertyValue'";
        },
        "removed" => function ($el, $path, $renderFunc) {
            ["key" => $key, "oldValue" => $value] = $el;
            $propertyPath = getPathString($path, $key);
            return "Property '$propertyPath' was removed";
        },
        "changed" => function ($el, $path, $renderFunc) {
            ["key" => $key, "newValue" => $newValue, "oldValue" => $oldValue] = $el;
            $propertyPath = getPathString($path, $key);
            $newPropertyValue = toString($newValue);
            $oldPropertyValue = toString($oldValue);
            return "Property '$propertyPath' was changed. From '$oldPropertyValue' to '$newPropertyValue'";
        },
    ];
    return $typeMethods[$elementType];
}

function render($ast, $path = [])
{
    $filtered = array_filter($ast, function ($el) {
        return $el["type"] != "unchanged";
    });
    $astArray = array_map(function ($el) use ($path) {
        $runProperRenderer = getRenderMethod($el["type"]);
        return $runProperRenderer($el, $path, function ($children, $path) {
            return render($children, $path);
        });
    }, $filtered);

    return implode("\n", $astArray);
}
