<?php

/*
 * This file is part of ansi-to-html.
 *
 * (c) 2013 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\AnsiConverter\Theme;

/**
 * Solarized XTerm theme.
 *
 * @see http://ethanschoonover.com/solarized
 */
class SolarizedXTermTheme extends Theme
{
    public function asArray()
    {
        return array(
            // normal
            'black' => '#262626',
            'red' => '#d70000',
            'green' => '#5f8700',
            'yellow' => '#af8700',
            'blue' => '#0087ff',
            'magenta' => '#af005f',
            'cyan' => '#00afaf',
            'white' => '#e4e4e4',

            // bright
            'brblack' => '#1c1c1c',
            'brred' => '#d75f00',
            'brgreen' => '#585858',
            'bryellow' => '#626262',
            'brblue' => '#808080',
            'brmagenta' => '#5f5faf',
            'brcyan' => '#8a8a8a',
            'brwhite' => '#ffffd7',
        );
    }
}
