<?php namespace Gendiff\parsers;

use Symfony\Component\Yaml\Yaml;

function getParsingMethod($dataType)
{
    $parsers = [
        "json" => function ($data) {
            return json_decode($data, true);
        },
        "yml" => function ($data) {
            return Yaml::parse($data);
        }
    ];
    
    if (!isset($parsers[$dataType])) {
        throw new \Exception("Unsuported file format");
    }
    return $parsers[$dataType];
}
