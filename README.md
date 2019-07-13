# GenDiff
[![Maintainability](https://api.codeclimate.com/v1/badges/d315ef17b75ca8eda7c3/maintainability)](https://codeclimate.com/github/arkadiy93/php-project-lvl2/maintainability)[![Build Status](https://travis-ci.org/arkadiy93/php-project-lvl2.svg?branch=master)](https://travis-ci.org/arkadiy93/php-project-lvl2)
### Description
A command line utility for generating differences between two configurational files.
Supporting file formats:
* json
* yaml

This is an educational project for hexlet.

### Installation
In order to install and run the utility:
```
composer global require arkadiy/php-project2
```
[![asciicast](https://asciinema.org/a/253282.svg)](https://asciinema.org/a/253282)

### Usage
```
gendiff -h

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  --format <fmt>                Report format [default: pretty]
```
It is possible to run the utility directly in the terminal with the command:
```
composer global require arkadiy/php-project2
```
It is possible to use the code as a library as well:
```
use function \Gendiff\index\genDiff;
```

### Supported config formats

This util supports two different configuration files formats:

* json
* yaml

#### JSON
[![asciicast](https://asciinema.org/a/kytgpqlfCXN0GvfNnLzhLeuHl.svg)](https://asciinema.org/a/kytgpqlfCXN0GvfNnLzhLeuHl)

#### YAML
[![asciicast](https://asciinema.org/a/ZZd8kKWJtTXQl0N6hoSRTogMp.svg)](https://asciinema.org/a/ZZd8kKWJtTXQl0N6hoSRTogMp)

###### Deep nested configurational files
The util is able to read config files with deep nesting also.

[![asciicast](https://asciinema.org/a/WANjsv3MgLGeiMzdYOFehtBJG.svg)](https://asciinema.org/a/WANjsv3MgLGeiMzdYOFehtBJG)

### Output formats
The output formats are 3:
* pretty (default format, example is shown in previous videp)
* plain
* json

#### PLAIN FORMAT
[![asciicast](https://asciinema.org/a/257251.svg)](https://asciinema.org/a/257251)

#### JSON FORMAT
[![asciicast](https://asciinema.org/a/257253.svg)](https://asciinema.org/a/257253)

### Tests
This project includes unit testing. It is possible to run the tests with:
```
make test
```
