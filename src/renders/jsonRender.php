<?php namespace Gendiff\renders\jsonRender;

function render($ast)
{
    return json_encode($ast, JSON_PRETTY_PRINT);
}
