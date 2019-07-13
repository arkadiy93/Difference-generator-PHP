<?php namespace Gendiff\renders\jsonRender;

function normalize($arr)
{
    $array = array_map(function ($el) {
        if ($el["type"] == "nested") {
            $normalizedChildren = normalize($el["children"]);
            $el["children"] = $normalizedChildren;
        }
        return $el;
    }, $arr);
    return array_values($array);
}

function render($ast)
{
    $normalizedAst = normalize($ast);
    return json_encode($normalizedAst, JSON_PRETTY_PRINT);
}
