ANSI to HTML5 Converter
=======================

This small library only does one thing: converting a text containing ANSI
codes to an HTML5 fragment:

```php
require_once __DIR__.'/vendor/autoload.php';

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

$converter = new AnsiToHtmlConverter();

$html = $converter->convert($ansi);
```

The `$ansi` variable should contain a text with ANSI codes, and `$html` will
contain the converted HTML5 version of it.

You can then output the HTML5 fragment in any HTML document:

```html+php
<html>
    <body>
        <pre style="background-color: black; overflow: auto; padding: 10px 15px; font-family: monospace;"
        ><?php echo $html ?></pre>
    </body>
</html>
```

The converter supports different color themes:

```php
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;

$theme = new SolarizedTheme();
$converter = new AnsiToHtmlConverter($theme);
```

By default, the colors are inlined into the HTML, but you can also use classes
by turning off style inlining:

```php
$converter = new AnsiToHtmlConverter($theme, false);
```

And the `asCss()` method of the theme object lets you retrieve the theme styles
as a CSS snippet:

```php
$styles = $theme->asCss();
```

which you can then use in your HTML document:

```html+php
<html>
    <head>
        <style>
            <?php echo $styles ?>

            .ansi_box { overflow: auto; padding: 10px 15px; font-family: monospace; }
        </style>
    </head>
    <body>
        <pre class="ansi_color_bg_black ansi_color_fg_white ansi_box"><?php echo $html ?></pre>
    </body>
</html>
```

Twig Integration
----------------

Register the extension:

```php
use SensioLabs\AnsiConverter\Bridge\Twig\AnsiExtension;

$twig->addExtension(AnsiExtension());
```

It's possible to use a custom ``AnsiToHtmlConverter``:

```php
use SensioLabs\AnsiConverter\Bridge\Twig\AnsiExtension;
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;

$theme = new SolarizedTheme();
$converter = new AnsiToHtmlConverter($theme, false);

$twig->addExtension(AnsiExtension($converter));
```

Then:

```jinja
<html>
    <head>
        <style>
            {# This is only need if the inline styling is disabled #}
            {{ ansi_css }}
        </style>
    </head>
    <body>
        {{ some_ansi_code|ansi_to_html }}
    </body>
</html>
```

