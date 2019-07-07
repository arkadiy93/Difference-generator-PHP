<?php namespace Gendiff\renders\index;

use function Gendiff\renders\deepRender\render as deepRendering;
use function Gendiff\renders\plainRender\render as plainRendering;
use function Gendiff\renders\jsonRender\render as jsonRendering;
use function Gendiff\renders\flatRender\render as flatRendering;

function getRenderMethod($renderType)
{
    $renderers = [
        "pretty" => function ($ast) {
            return deepRendering($ast);
        },
        "plain" => function ($ast) {
            return plainRendering($ast);
        },
        "json" => function ($ast) {
            return jsonRendering($ast);
        },
        "flat" => function ($ast) {
            return flatRendering($ast);
        }
    ];
    return $renderers[$renderType];
}
