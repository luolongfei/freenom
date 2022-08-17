<?php

/*
 * This file is part of ansi-to-html.
 *
 * (c) 2013 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SensioLabs\AnsiConverter\Tests;

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

class AnsiToHtmlConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getConvertData
     */
    public function testConvert($expected, $input)
    {
        $converter = new AnsiToHtmlConverter();
        $this->assertEquals($expected, $converter->convert($input));
    }

    public function getConvertData()
    {
        return array(
            // text is escaped
            array('<span style="background-color: black; color: white">foo &lt;br /&gt;</span>', 'foo <br />'),

            // newlines are preserved
            array("<span style=\"background-color: black; color: white\">foo\nbar</span>", "foo\nbar"),

            // backspaces
            array('<span style="background-color: black; color: white">foo   </span>', "foobar\x08\x08\x08   "),
            array('<span style="background-color: black; color: white">foo</span><span style="background-color: black; color: white">   </span>', "foob\e[31;41ma\e[0mr\x08\x08\x08   "),

            // color
            array('<span style="background-color: darkred; color: darkred">foo</span>', "\e[31;41mfoo\e[0m"),

            // color with [m as a termination (equivalent to [0m])
            array('<span style="background-color: darkred; color: darkred">foo</span>', "\e[31;41mfoo\e[m"),

            // bright color
            array('<span style="background-color: red; color: red">foo</span>', "\e[31;41;1mfoo\e[0m"),

            // carriage returns
            array('<span style="background-color: black; color: white">foobar</span>', "foo\rbar\rfoobar"),

            // underline
            array('<span style="background-color: black; color: white; text-decoration: underline">foo</span>', "\e[4mfoo\e[0m"),

            // non valid unicode codepoints substitution (only available with PHP >= 5.4)
            PHP_VERSION_ID < 50400 ?: array('<span style="background-color: black; color: white">foo '."\xEF\xBF\xBD".'</span>', "foo \xF4\xFF\xFF\xFF"),
        );
    }
}
