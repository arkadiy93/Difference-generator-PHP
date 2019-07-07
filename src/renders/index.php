<?php namespace Gendiff\renders\index;

use function Gendiff\renders\deepRender\render as deepRendering;
use function Gendiff\renders\plainRender\render as plainRendering;

function getRenderMethod($renderType)
{
    $renderers = [
        "pretty" => function ($ast) {
            return deepRendering($ast);
        },
        "plain" => function ($ast) {
            return plainRendering($ast);
        },
    ];
    return $renderers[$renderType];
}
