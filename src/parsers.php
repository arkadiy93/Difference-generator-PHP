<?php namespace Gendiff\parsers;

use Symfony\Component\Yaml\Yaml;

function getParsingMethod($extension)
{
    $parsers = [
        "json" => function ($fileContent) {
            return json_decode($fileContent, true);
        },
        "yml" => function ($fileContent) {
            return Yaml::parse($fileContent);
        }
    ];
    
    if (!isset($parsers[$extension])) {
        throw new \Exception("Unsuported file format");
    }
    return $parsers[$extension];
}
