<?php namespace Gendiff\parsers;

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
