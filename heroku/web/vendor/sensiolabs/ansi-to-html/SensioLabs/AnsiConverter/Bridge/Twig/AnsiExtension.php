<?php

namespace SensioLabs\AnsiConverter\Bridge\Twig;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AnsiExtension extends AbstractExtension
{
    /** @var AnsiToHtmlConverter */
    private $converter;

    public function __construct(AnsiToHtmlConverter $converter = null)
    {
        $this->converter = $converter ?: new AnsiToHtmlConverter();
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('ansi_to_html', [$this, 'ansiToHtml'], ['is_safe' => ['html']]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ansi_css', [$this, 'css'], ['is_safe' => ['css']]),
        ];
    }

    public function ansiToHtml($string)
    {
        return $this->converter->convert($string);
    }

    public function css(): string
    {
        return $this->converter->getTheme()->asCss();
    }

    public function getName(): string
    {
        return 'sensiolabs_ansi';
    }
}
