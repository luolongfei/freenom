# Monolog Colored Line Formatter

A Formatter for Monolog with color support
Built by Bramus! - [https://www.bram.us/](https://www.bram.us/)

[![Build Status](https://api.travis-ci.org/bramus/monolog-colored-line-formatter.png)](http://travis-ci.org/bramus/monolog-colored-line-formatter)

## About

`bramus/monolog-colored-line-formatter` is a formatter for use with [Monolog](https://github.com/Seldaek/monolog). It augments the [Monolog LineFormatter](https://github.com/Seldaek/monolog/blob/master/src/Monolog/Formatter/LineFormatter.php) by adding color support. To achieve this `bramus/monolog-colored-line-formatter` uses ANSI Escape Sequences – [provided by `bramus/ansi-php`](https://github.com/bramus/ansi-php) – which makes it perfect for usage on text based terminals (viz. the shell).

`bramus/monolog-colored-line-formatter` ships with a default color scheme, yet it can be adjusted to fit your own needs.

## Prerequisites/Requirements

- PHP 5.4.0 or greater

## Installation

Installation is possible using Composer

```
composer require bramus/monolog-colored-line-formatter ~2.0
```

## Usage

Create an instance of `\Bramus\Monolog\Formatter\ColoredLineFormatter` and set it as the formatter for the `\Monolog\Handler\StreamHandler` that you use with your `\Monolog\Logger` instance.

```
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

![Monolog Colored Line Formatter](https://raw.githubusercontent.com/bramus/monolog-colored-line-formatter/master/screenshots/colorscheme-default.gif)

#### Color Scheme: TrafficLight

![Monolog Colored Line Formatter](https://raw.githubusercontent.com/bramus/monolog-colored-line-formatter/master/screenshots/colorscheme-trafficlight.gif)

### Activating a Color Scheme

Color Schemes are defined as classes. If you do not provide any color scheme the default one will be used.

To activate a color scheme pass it as the first argument of the `ColoredLineFormatter` Constructor. All successive arguments are the ones as required by the `\Monolog\Formatter\LineFormatter` class.

```
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

```
namespace Bramus\Monolog\Formatter\ColorSchemes;

use Monolog\Logger;
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
            Logger::DEBUG => $this->ansi->color(SGR::COLOR_FG_WHITE)->get(),
            Logger::INFO => $this->ansi->color(SGR::COLOR_FG_GREEN)->get(),
            Logger::NOTICE => $this->ansi->color(SGR::COLOR_FG_CYAN)->get(),
            Logger::WARNING => $this->ansi->color(SGR::COLOR_FG_YELLOW)->get(),
            Logger::ERROR => $this->ansi->color(SGR::COLOR_FG_RED)->get(),
            Logger::CRITICAL => $this->ansi->color(SGR::COLOR_FG_RED)->underline()->get(),
            Logger::ALERT => $this->ansi->color(array(SGR::COLOR_FG_WHITE, SGR::COLOR_BG_RED_BRIGHT))->get(),
            Logger::EMERGENCY => $this->ansi->color(SGR::COLOR_BG_RED_BRIGHT)->blink()->color(SGR::COLOR_FG_WHITE)->get(),
        ));
    }
}
```

Please refer to [the `bramus/ansi-php` documentation](https://github.com/bramus/ansi-php) to define your own styles and colors.

## Unit Testing

`bramus/monolog-colored-line-formatter` ships with unit tests using [PHPUnit](https://github.com/sebastianbergmann/phpunit/).

- If PHPUnit is installed globally run `phpunit` to run the tests.

- If PHPUnit is not installed globally, install it locally through composer by running `composer install --dev`. Run the tests themselves by calling `vendor/bin/phpunit`.

Unit tests are also automatically run [on Travis CI](http://travis-ci.org/bramus/monolog-colored-line-formatter)

## License

`bramus/monolog-colored-line-formatter` is released under the MIT public license. See the enclosed `LICENSE.txt` for details.
