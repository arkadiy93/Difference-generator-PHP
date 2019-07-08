<?php namespace Gendiff\parsers;

use Symfony\Component\Yaml\Yaml;

function getParsingMethod($extension)
{
    $parsers = [
      "json" => function ($pathToFile) {
        $fileContent = file_get_contents($pathToFile);
        return json_decode($fileContent, true);
      },
      "yml" => function ($pathToFile) {
        return Yaml::parseFile($pathToFile);
      }
    ];
    return $parsers[$extension];
}
