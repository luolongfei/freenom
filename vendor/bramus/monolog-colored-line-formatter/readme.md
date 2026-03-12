# Monolog Colored Line Formatter

[![Build Status](https://github.com/bramus/monolog-colored-line-formatter/workflows/CI/badge.svg)](https://github.com/bramus/monolog-colored-line-formatter/actions) [![Source](http://img.shields.io/badge/source-bramus/monolog--colored--line--formatter-blue.svg?style=flat-square)](https://github.com/bramus/monolog-colored-line-formatter) [![Supported PHP Versions](https://img.shields.io/packagist/php-v/bramus/monolog-colored-line-formatter)](https://github.com/bramus/monolog-colored-line-formatter) [![Version](https://img.shields.io/packagist/v/bramus/monolog-colored-line-formatter.svg?style=flat-square)](https://packagist.org/packages/bramus/monolog-colored-line-formatter) [![Downloads](https://img.shields.io/packagist/dt/bramus/monolog-colored-line-formatter.svg?style=flat-square)](https://packagist.org/packages/bramus/monolog-colored-line-formatter/stats) [![License](https://img.shields.io/packagist/l/bramus/monolog-colored-line-formatter.svg?style=flat-square)](https://github.com/bramus/monolog-colored-line-formatter/blob/master/LICENSE.txt)

A Formatter for Monolog with color support
Built by Bramus! - [https://www.bram.us/](https://www.bram.us/)

## About

`bramus/monolog-colored-line-formatter` is a formatter for use with [Monolog](https://github.com/Seldaek/monolog). It augments the [Monolog LineFormatter](https://github.com/Seldaek/monolog/blob/master/src/Monolog/Formatter/LineFormatter.php) by adding color support. To achieve this `bramus/monolog-colored-line-formatter` uses ANSI Escape Sequences – [provided by `bramus/ansi-php`](https://github.com/bramus/ansi-php) – which makes it perfect for usage on text based terminals (viz. the shell).

`bramus/monolog-colored-line-formatter` ships with a default color scheme, yet it can be adjusted to fit your own needs.

## Prerequisites/Requirements

- PHP 8.1 or greater
- Monolog 3.0 or greater

_Looking for a version compatible with Monolog 1.x? Check out the `monolog-1.x` branch then. The version of `monolog-colored-line-formatter` that is compatible with Monolog 1.x, is `monolog-colored-line-formatter` version `~2.0`_

_Looking for a version compatible with Monolog 2.x? Check out the `monolog-2.x` branch then. The version of `monolog-colored-line-formatter` that is compatible with Monolog 1.x, is `monolog-colored-line-formatter` version `~3.0.0`_

## Installation

Installation is possible using Composer.

Install `monolog-colored-line-formatter`, compatible with Monolog 3.x:

```bash
composer require bramus/monolog-colored-line-formatter ~3.1
```

Install `monolog-colored-line-formatter`, compatible with Monolog 2.x:

```bash
composer require bramus/monolog-colored-line-formatter ~3.0.0
```

Install `monolog-colored-line-formatter`, compatible with Monolog 1.x:

```bash
composer require bramus/monolog-colored-line-formatter ~2.0
```

## Usage

Create an instance of `\Bramus\Monolog\Formatter\ColoredLineFormatter` and set it as the formatter for the `\Monolog\Handler\StreamHandler` that you use with your `\Monolog\Logger` instance.

```php
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Bramus\Monolog\Formatter\ColoredLineFormatter;

$log = new Logger('DEMO');
$handler = new StreamHandler('php://stdout', Logger::WARNING);
$handler->setFormatter(new ColoredLineFormatter());
$log->pushHandler($handler);

$log->addError('Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
```

## Color Schemes

### Available Color Schemes

#### Color Scheme: DefaultScheme

![Monolog Colored Line Formatter](https://user-images.githubusercontent.com/11269635/28756233-c9f63abe-756a-11e7-883f-a084f35c55e7.gif)

#### Color Scheme: TrafficLight

![Monolog Colored Line Formatter](https://user-images.githubusercontent.com/11269635/28756238-df0a5598-756a-11e7-929a-201bef89e6a2.gif)

### Activating a Color Scheme

Color Schemes are defined as classes. If you do not provide any color scheme the default one will be used.

To activate a color scheme pass it as the first argument of the `ColoredLineFormatter` Constructor. All successive arguments are the ones as required by the `\Monolog\Formatter\LineFormatter` class.

```php
use \Monolog\Logger;
use \Monolog\Handler\StreamHandler;
use \Bramus\Monolog\Formatter\ColoredLineFormatter;
use \Bramus\Monolog\Formatter\ColorSchemes\TrafficLight;

$log = new Logger('DEMO');
$handler = new StreamHandler('php://stdout', Logger::WARNING);
$handler->setFormatter(new ColoredLineFormatter(new TrafficLight()));
$log->pushHandler($handler);
```

Alternatively it's also possible to activate it using the `setColorScheme()` method of a `ColoredLineFormatter` instance.

### Creating your own Custom Color Scheme

To define your own color scheme make a class that implements the `\Bramus\Monolog\Formatter\ColorSchemes\ColorSchemeInterface` interface. To make things more easy a trait `ColorSchemeTrait` is defined.

```php
namespace Bramus\Monolog\Formatter\ColorSchemes;

use Monolog\Logger;
use Monolog\Level;
use Bramus\Ansi\Ansi;
use Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

class TrafficLight implements ColorSchemeInterface
{
    /**
     * Use the ColorSchemeTrait and alias its constructor
     */
    use ColorSchemeTrait {
        ColorSchemeTrait::__construct as private __constructTrait;
    }

    /**
     * [__construct description]
     */
    public function __construct()
    {
        // Call Trait Constructor, so that we have $this->ansi available
        $this->__constructTrait();

        // Our Color Scheme
        $this->setColorizeArray(array(
            Level::Debug => $this->ansi->color(SGR::COLOR_FG_WHITE)->get(),
            Level::Info => $this->ansi->color(SGR::COLOR_FG_GREEN)->get(),
            Level::Notice => $this->ansi->color(SGR::COLOR_FG_CYAN)->get(),
            Level::Warning => $this->ansi->color(SGR::COLOR_FG_YELLOW)->get(),
            Level::Error => $this->ansi->color(SGR::COLOR_FG_RED)->get(),
            Level::Critical => $this->ansi->color(SGR::COLOR_FG_RED)->underline()->get(),
            Level::Alert => $this->ansi->color([SGR::COLOR_FG_WHITE, SGR::COLOR_BG_RED_BRIGHT])->get(),
            Level::Emergency => $this->ansi->color(SGR::COLOR_BG_RED_BRIGHT)->blink()->color(SGR::COLOR_FG_WHITE)->get(),
        ));
    }
}
```

Please refer to [the `bramus/ansi-php` documentation](https://github.com/bramus/ansi-php) to define your own styles and colors.

## Unit Testing

`bramus/monolog-colored-line-formatter` ships with unit tests using [PHPUnit](https://github.com/sebastianbergmann/phpunit/).

- If PHPUnit is installed globally run `phpunit` to run the tests.

- If PHPUnit is not installed globally, install it locally through composer by running `composer install --dev`. Run the tests themselves by calling `vendor/bin/phpunit`.

Unit tests are also automatically run [on GitHub Actions](https://github.com/bramus/monolog-colored-line-formatter/actions?query=workflow%3ACI)

## License

`bramus/monolog-colored-line-formatter` is released under the MIT public license. See the enclosed `LICENSE.txt` for details.
