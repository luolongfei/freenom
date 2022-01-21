# ANSI PHP

[![Build Status](https://img.shields.io/travis/bramus/ansi-php.svg?style=flat-square)](http://travis-ci.org/bramus/ansi-php) [![Source](http://img.shields.io/badge/source-bramus/ansi--php-blue.svg?style=flat-square)](https://github.com/bramus/ansi-php) [![Version](https://img.shields.io/packagist/v/bramus/ansi-php.svg?style=flat-square)](https://packagist.org/packages/bramus/ansi-php) [![Downloads](https://img.shields.io/packagist/dt/bramus/ansi-php.svg?style=flat-square)](https://packagist.org/packages/bramus/ansi-php/stats) [![License](https://img.shields.io/packagist/l/bramus/ansi-php.svg?style=flat-square)](https://github.com/bramus/ansi-php/blob/master/LICENSE)

ANSI Control Functions and ANSI Control Sequences for PHP CLI Apps

Built by Bramus! - [https://www.bram.us/](https://www.bram.us/)

## About

`bramus/ansi-php` is a set of classes to working with ANSI Control Functions and ANSI Control Sequences on text based terminals.

- ANSI Control Functions control an action such as line spacing, paging, or data flow.
- ANSI Control Sequences allow one to clear the screen, move the cursor, set text colors, etc.

_(Sidenote: An “ANSI Escape Sequence” is a special type of “ANSI Control Sequence” which starts with the ESC ANSI Control Function. The terms are not interchangeable.)_

## Features

When it comes to ANSI Control Functions `bramus/ansi-php` supports:

- `BS`: Backspace
- `BEL`: Bell
- `CR`: Carriage Return
- `ESC`: Escape
- `LF`: Line Feed
- `TAB`: Tab

When it comes to ANSI Escape Sequences `bramus/ansi-php` supports:

- CUB _(Cursor Back)_: Move cursor back.
- CUD _(Cursor Down)_: Move cursor down.
- CUF _(Cursor Forward)_: Move cursor forward.
- CUP _(Cursor Position)_: Move cursor to position.
- CUU _(Cursor Up)_: Move cursor up.
- ED _(Erase Display)_: Erase (parts of) the display.
- EL _(Erase In Line)_: Erase (parts of) the current line.
- SGR _(Select Graphic Rendition)_: Manipulate text styling (bold, underline, blink, colors, etc.).

Other Control Sequences – such as DCH, NEL, etc. – are not (yet) supported.

An example library that uses `bramus/ansi-php` is [`bramus/monolog-colored-line-formatter`](https://github.com/bramus/monolog-colored-line-formatter). It uses `bramus/ansi-php`'s SGR support to colorize the output:

![Monolog Colored Line Formatter](https://user-images.githubusercontent.com/11269635/28756233-c9f63abe-756a-11e7-883f-a084f35c55e7.gif)

## Prerequisites/Requirements

- PHP 5.4.0 or greater

## Installation

Installation is possible using Composer

```shell
composer require bramus/ansi-php ~3.1
```

## Usage

The easiest way to use _ANSI PHP_ is to use the bundled `Ansi` helper class which provides easy shorthands to working with `bramus/ansi-php`. The `Ansi` class is written in such a way that you can chain calls to one another.

If you're feeling adventurous, you're of course free to use the raw `ControlFunction` and `ControlSequence` classes.

### Quick example

```php
use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

// Create Ansi Instance
$ansi = new Ansi(new StreamWriter('php://stdout'));

// Output some styled text on screen, along with a Line Feed and a Bell
$ansi->color(array(SGR::COLOR_FG_RED, SGR::COLOR_BG_WHITE))
     ->blink()
     ->text('I will be blinking red on a white background.')
     ->nostyle()
     ->text(' And I will be normally styled.')
     ->lf()
     ->text('Ooh, a bell is coming ...')
     ->bell();
```

See more examples further down on how to use these.

## Concepts

Since v3.0 `bramus/ansi-php` uses the concept of writers to write the data to. By default a `StreamWriter` writing to `php://stdout` is used.

The following writers are provided

- `StreamWriter`: Writes the data to a stream. Just pass in the path to a file and it will open a stream for you. Defaults to writing to `php://stdout`.
- `BufferWriter`: Writes the data to a buffer. When calling `flush()` the contents of the buffer will be returned.
- `ProxyWriter`: Acts as a proxy to another writer. Writes the data to an internal buffer. When calling `flush()` the writer will first write the data to the other writer before returning it.

## The `Ansi` helper class functions

### Core functions:

- `text($text)`: Write a piece of data to the writer
- `setWriter(\Bramus\Ansi\Writers\WriterInterface $writer)`: Sets the writer
- `getWriter()`: Gets the writer

### ANSI Control Function shorthands:

These shorthands write a Control Character to the writer.

- `bell()`:  Bell Control Character (`\a`)
- `backspace()`:  Backspace Control Character (`\b`)
- `tab()`:  Tab Control Character (`\t`)
- `lf()`:  Line Feed Control Character (`\n`)
- `cr()`:  Carriage Return Control Character (`\r`)
- `esc()`:  Escape Control Character

### SGR ANSI Escape Sequence shorthands:

These shorthands write SGR ANSI Escape Sequences to the writer.

- `nostyle()` or `reset()`: Remove all text styling (colors, bold, etc)
- `color()`: Set the foreground and/or backgroundcolor of the text. _(see further)_
- `bold()` or `bright()`: Bold: On. On some systems "Intensity: Bright"
- `normal()`: Bold: Off. On some systems "Intensity: Normal"
- `faint()`: Intensity: Faint. _(Not widely supported)_
- `italic()`: Italic: On. _(Not widely supported)_
- `underline()`: Underline: On.
- `blink()`: Blink: On.
- `negative()`: Inverse or Reverse. Swap foreground and background.
- `strikethrough()`: Strikethrough: On. _(Not widely supported)_

__IMPORTANT:__ Select Graphic Rendition works in such a way that text styling you have set will remain active until you call `nostyle()` or `reset()` to return to the default styling.

### ED ANSI Escape Sequence shorthands:

These shorthands write ED ANSI Escape Sequences to the writer.

- `eraseDisplay()`: Erase the entire screen and moves the cursor to home.
- `eraseDisplayUp()`: Erase the screen from the current line up to the top of the screen.
- `eraseDisplayDown()`: Erase the screen from the current line down to the bottom of the screen.

### EL ANSI Escape Sequence shorthands:

These shorthands write EL ANSI Escape Sequences to the writer.

- `eraseLine()`: Erase the entire current line.
- `eraseLineToEOL()`: Erase from the current cursor position to the end of the current line.
- `eraseLineToSOL()`: Erases from the current cursor position to the start of the current line.

### CUB/CUD/CUF/CUP/CUU ANSI Escape Sequence shorthands:

- `cursorBack($n)`: Move cursor back `$n` positions _(default: 1)_
- `cursorForward($n)`: Move cursor forward `$n` positions _(default: 1)_
- `cursorDown($n)`: Move cursor down `$n` positions _(default: 1)_
- `cursorUp($n)`: Move cursor up `$n` positions _(default: 1)_
- `cursorPosition($n, $m)`: Move cursor to position `$n,$m` _(default: 1,1)_

### Extra functions

- `flush()` or `get()`: Retrieve contents of a `FlushableWriter` writer.
- `e()`: Echo the contents of a `FlushableWriter` writer.

## Examples

### The Basics

```php
// Create Ansi Instance
$ansi = new \Bramus\Ansi\Ansi();

// This will output a Bell
$ansi->bell();

// This will output some text
$ansi->text('Hello World!');
```

_NOTE:_ As no `$writer` is passed into the constructor of `\Bramus\Ansi\Ansi`, the default `StreamWriter` writing to `php://stdout` is used.

### Using a `FlushableWriter`

Flushable Writers are writers that cache the data and only output it when flushed using its `flush()` function. The `BufferWriter` and `ProxyWriter` implement this interface.

```php
// Create Ansi Instance
$ansi = new \Bramus\Ansi\Ansi(new \Bramus\Ansi\Writers\BufferWriter());

// This will append a bell to the buffer. It will not output it.
$ansi->bell();

// This will append a bell to the buffer. It will not output it.
$ansi->text('Hello World!');

// Now we'll output it
echo $ansi->get();
```

### Chaining

`bramus/ansi-php`'s wrapper `Ansi` class supports chaining.

```php
// Create Ansi Instance
$ansi = new \Bramus\Ansi\Ansi();

// This will output a Line Feed, some text, a Bell, and a Line Feed
$ansi->lf()->text('hello')->bell()->lf();

```

### Styling Text: The Basics

```php
$ansi = new \Bramus\Ansi\Ansi();
$ansi->bold()->underline()->text('I will be bold and underlined')->lf();
```

__IMPORTANT__ Select Graphic Rendition works in such a way that text styling  you have set will remain active until you call `nostyle()` or `reset()` to return to the default styling.


```php
$ansi = new \Bramus\Ansi\Ansi();

$ansi->bold()->underline()->text('I will be bold and underlined')->lf();
$ansi->text('I will also be bold because nostyle() has not been called yet')->lf();
$ansi->nostyle()->blink()->text('I will be blinking')->nostyle()->lf();
$ansi->text('I will be normal because nostyle() was called on the previous line');

```

### Styling Text: Colors

Colors, and other text styling options, are defined as contants on `\Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR`.

#### Foreground (Text) Colors

- `SGR::COLOR_FG_BLACK`: Black Foreground Color
- `SGR::COLOR_FG_RED`: Red Foreground Color
- `SGR::COLOR_FG_GREEN`: Green Foreground Color
- `SGR::COLOR_FG_YELLOW`: Yellow Foreground Color
- `SGR::COLOR_FG_BLUE`: Blue Foreground Color
- `SGR::COLOR_FG_PURPLE`: Purple Foreground Color
- `SGR::COLOR_FG_CYAN`: Cyan Foreground Color
- `SGR::COLOR_FG_WHITE`: White Foreground Color
- `SGR::COLOR_FG_BLACK_BRIGHT`: Black Foreground Color (Bright)
- `SGR::COLOR_FG_RED_BRIGHT`: Red Foreground Color (Bright)
- `SGR::COLOR_FG_GREEN_BRIGHT`: Green Foreground Color (Bright)
- `SGR::COLOR_FG_YELLOW_BRIGHT`: Yellow Foreground Color (Bright)
- `SGR::COLOR_FG_BLUE_BRIGHT`: Blue Foreground Color (Bright)
- `SGR::COLOR_FG_PURPLE_BRIGHT`: Purple Foreground Color (Bright)
- `SGR::COLOR_FG_CYAN_BRIGHT`: Cyan Foreground Color (Bright)
- `SGR::COLOR_FG_WHITE_BRIGHT`: White Foreground Color (Bright)
- `SGR::COLOR_FG_RESET`: Default Foreground Color

#### Background Colors

- `SGR::COLOR_BG_BLACK`: Black Background Color
- `SGR::COLOR_BG_RED`: Red Background Color
- `SGR::COLOR_BG_GREEN`: Green Background Color
- `SGR::COLOR_BG_YELLOW`: Yellow Background Color
- `SGR::COLOR_BG_BLUE`: Blue Background Color
- `SGR::COLOR_BG_PURPLE`: Purple Background Color
- `SGR::COLOR_BG_CYAN`: Cyan Background Color
- `SGR::COLOR_BG_WHITE`: White Background Color
- `SGR::COLOR_BG_BLACK_BRIGHT`: Black Background Color (Bright)
- `SGR::COLOR_BG_RED_BRIGHT`: Red Background Color (Bright)
- `SGR::COLOR_BG_GREEN_BRIGHT`: Green Background Color (Bright)
- `SGR::COLOR_BG_YELLOW_BRIGHT`: Yellow Background Color (Bright)
- `SGR::COLOR_BG_BLUE_BRIGHT`: Blue Background Color (Bright)
- `SGR::COLOR_BG_PURPLE_BRIGHT`: Purple Background Color (Bright)
- `SGR::COLOR_BG_CYAN_BRIGHT`: Cyan Background Color (Bright)
- `SGR::COLOR_BG_WHITE_BRIGHT`: White Background Color (Bright)
- `SGR::COLOR_BG_RESET`: Default Background Color

Pass one of these into `$ansi->color()` and the color will be set.

```php
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

$ansi = new \Bramus\Ansi\Ansi();

$ansi->color(SGR::COLOR_FG_RED)
     ->text('I will be red')
     ->nostyle();
```

To set the foreground and background color in one call, pass them using an array to `$ansi->color()`

```php
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

$ansi = new \Bramus\Ansi\Ansi();

$ansi->color(array(SGR::COLOR_FG_RED, SGR::COLOR_BG_WHITE))
     ->blink()
     ->text('I will be blinking red on a wrhite background.')
     ->nostyle();
```

### Creating a loading Spinner

By manipulating the cursor position one can create an in-place spinner

```php
use \Bramus\Ansi\Ansi;
use \Bramus\Ansi\Writers\StreamWriter;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\EL;
use \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\SGR;

// Create Ansi Instance
$ansi = new Ansi(new StreamWriter('php://stdout'));

// Parts of our spinner
$spinnerParts = ['⣷','⣯','⣟','⡿','⢿','⣻','⣽','⣾'];

$ansi->text('Loading Data')->lf();
for ($i = 0; $i < 100; $i++) {
    $ansi
        // Erase entire line
        ->el(EL::ALL)
        // Go back to very first position on current line
        ->cursorBack(9999)
        // Add a blue spinner
        ->color(SGR::COLOR_FG_BLUE)->text($spinnerParts[$i % sizeof($spinnerParts)])
        // Write percentage
        ->nostyle()->text(' ' . str_pad($i, 3, 0, STR_PAD_LEFT) . '%');

    usleep(50000);
}
$ansi
    ->el(EL::ALL)
    ->cursorBack(9999)
    ->color(SGR::COLOR_FG_GREEN)->text('✔')
    ->nostyle()->text(' 100%')
    ->lf();
```

This snippet will output a little loading spinner icon + the current percentage (e.g. `⣯ 009%`) that constantly updates in-place. When 100% is reached, the line will read `✔ 100%`.

### Using the raw classes

As all raw `ControlFunction` and `ControlSequence` classes are provided with a `__toString()` function it's perfectly possible to directly `echo` some `bramus/ansi-php` instance.

```php
// Output a Bell Control Character
echo new \Bramus\Ansi\ControlFunctions\Bell();

// Output an ED instruction, to erase the entire screen
echo new \Bramus\Ansi\ControlSequences\EscapeSequences\ED(
    \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\ED::ALL
);
```

To fetch their contents, use the `get()` function:

```php
// Get ANSI string for a Bell Control Character
$bell = (new \Bramus\Ansi\ControlFunctions\Bell())->get();

// Get ANSI string for an ED instruction, to erase the entire screen
$eraseDisplay = (new \Bramus\Ansi\ControlSequences\EscapeSequences\ED(
    \Bramus\Ansi\ControlSequences\EscapeSequences\Enums\ED::ALL
))->get();

echo $bell . $bell . $eraseDisplay . $bell;
```

## Unit Testing

`bramus/ansi-php` ships with unit tests using [PHPUnit](https://github.com/sebastianbergmann/phpunit/).

- If PHPUnit is installed globally run `phpunit` to run the tests.

- If PHPUnit is not installed globally, install it locally throuh composer by running `composer install --dev`. Run the tests themselves by calling `vendor/bin/phpunit` or `composer test`

Unit tests are also automatically run [on Travis CI](http://travis-ci.org/bramus/ansi-php)

## License

`bramus/ansi-php` is released under the MIT public license. See the enclosed `LICENSE` for details.

## ANSI References

- [http://en.wikipedia.org/wiki/ANSI_escape_code](http://en.wikipedia.org/wiki/ANSI_escape_code)
- [http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-048.pdf](http://www.ecma-international.org/publications/files/ECMA-ST/Ecma-048.pdf)
- [http://wiki.bash-hackers.org/scripting/terminalcodes](http://wiki.bash-hackers.org/scripting/terminalcodes)
- [http://web.mit.edu/gnu/doc/html/screen_10.html](http://web.mit.edu/gnu/doc/html/screen_10.html)
- [http://www.isthe.com/chongo/tech/comp/ansi_escapes.html](http://www.isthe.com/chongo/tech/comp/ansi_escapes.html)
- [http://www.termsys.demon.co.uk/vtansi.htm](http://www.termsys.demon.co.uk/vtansi.htm)
- [http://rrbrandt.dee.ufcg.edu.br/en/docs/ansi/](http://rrbrandt.dee.ufcg.edu.br/en/docs/ansi/)
- [http://tldp.org/HOWTO/Bash-Prompt-HOWTO/c327.html](http://tldp.org/HOWTO/Bash-Prompt-HOWTO/c327.html)
