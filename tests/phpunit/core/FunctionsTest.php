<?php
/**
 * ${product.title}
 *
 * Модульные тесты
 *
 * @version ${product.version}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 * @subpackage Tests
 *
 * $Id$
 */

require_once __DIR__ . '/../bootstrap.php';
require_once TESTS_SRC_DIR . '/core/kernel-legacy.php';

class Functions_Test extends PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function test_macroConst()
    {
        define('Functions_Test_Foo', 'bar');
        $this->assertEquals('bar', __macroConst(array(null, 'Functions_Test_Foo')));
    }
    //-----------------------------------------------------------------------------

    /**
     *
     */
    public function test_macroVar()
    {
        $GLOBALS['Functions_Test_Foo'] = 'bar';
        $this->assertEquals('bar', __macroVar(array(null, null, 'Functions_Test_Foo')));
        $this->assertEquals('barbaz', __macroVar(array(null, null, 'Functions_Test_Foo', '."baz"')));
    }
    //-----------------------------------------------------------------------------

    /**
     *
     */
    public function test_FormatSize()
    {
        $this->assertEquals('1 023 Байт', FormatSize(1023));
        $this->assertEquals('1.00 Кб', FormatSize(1025));
        $this->assertEquals('1.00 Мб', FormatSize(1048577));
        $this->assertEquals('1.00 Гб', FormatSize(1073741825));
    }

    /**
     *
     */
    public function test_ErrorBox()
    {
        $this->assertEquals(
            "<div class=\"errorBoxCap\">заголовок</div>\n<div class=\"errorBox\">\nтекст</div>\n",
            ErrorBox('текст','заголовок'));
        $this->assertEquals("<div class=\"errorBox\">\nтекст</div>\n", ErrorBox('текст',''));
        $this->assertEquals(
            "<div class=\"errorBoxCap\">заголовок</div>\n<div class=\"errorBox\">\n</div>\n",
            ErrorBox('','заголовок'));
    }

    /**
     *
     */
    public function test_InfoBox()
    {
        $this->assertEquals(
            "<div class=\"infoBoxCap\">заголовок</div>\n<div class=\"infoBox\">\nтекст</div>\n",
            InfoBox('текст','заголовок'));
        $this->assertEquals("<div class=\"infoBox\">\nтекст</div>\n", InfoBox('текст',''));
        $this->assertEquals(
            "<div class=\"infoBoxCap\">заголовок</div>\n<div class=\"infoBox\">\n</div>\n",
            InfoBox('','заголовок'));
    }

    /**
     *
     */
    public function test_gettime()
    {
        $this->assertEquals(date('Y-m-d'),gettime('Y-m-d'));
    }

    /**
     *
     */
    public function test_encodeHTML()
    {
        $test_encodeHTML_str='<a href="#">foo</a>';
        $test_encodeHTML_mas_in= array('<a href="#">foo</a>');
        $test_encodeHTML_mas_out= array ('&lt;a href=&quot;#&quot;&gt;foo&lt;/a&gt;');
        $this->assertEquals('&lt;a href=&quot;#&quot;&gt;foo&lt;/a&gt;',
            encodeHTML($test_encodeHTML_str));
        $this->assertEquals($test_encodeHTML_mas_out, encodeHTML($test_encodeHTML_mas_in));
    }

    /**
     *
     */
    public function test_decodeHTML()
    {
        $this->assertEquals('(<a href="#">foo</a>)',
            decodeHTML('%28&lt;a href=&quot;#&quot;&gt;foo&lt;/a&gt;%29'));
    }

    /**
     *
     */
    public function test_text2array()
    {
        $test_mas1 = array
        (
            'foo'=>'bar',
            'key'=>'value'
        );
        $test_mas2 =	array('foo=bar', 'key=value');
        $test_mas3 =	array();
        $this->assertEquals($test_mas1, text2array("foo=bar\nkey=value", true));
        $this->assertEquals($test_mas2, text2array("foo=bar\nkey=value"));
        $this->assertEquals($test_mas3, text2array(""));
    }

    /**
     *
     */
    public function test_array2text()
    {
        $test_mas1 = array
        (
            'foo'=>'bar',
            'key'=>'value'
        );
        $test_mas2 =	array('foo=bar', 'key=value');
        $test_mas3 =	array();
        $this->assertEquals("foo=bar\nkey=value", array2text($test_mas1, true));
        $this->assertEquals("foo=bar\nkey=value", array2text($test_mas2));
        $this->assertEquals("", array2text($test_mas3));
    }

    /**
     *
     */
    public function test_encodeOptions_decodeOptions()
    {
        $options = array('foo' => 'bar', 'baz' => false);
        $encoded = encodeOptions($options);
        $actual = decodeOptions($encoded);
        $this->assertEquals($options, $actual);

        $options['key'] = 'value';
        $actual = decodeOptions($encoded, array('key' => 'value'));
        $this->assertEquals($options, $actual);
        $this->assertEquals($options, decodeOptions('', $options));
        $this->assertEquals($options, decodeOptions('foo' . $encoded, $options));
    }

    /**
     *
     */
    public function test___isset()
    {
        $myTestMas = array('foo' => 'bar');
        $this->assertEquals(true, __isset($myTestMas, 'foo'));
        $this->assertEquals(false, __isset($myTestMas, 'bar'));

        $myTestVariable = 5;
        $this->assertEquals(false, __isset($myTestVariable, ''));

        $x = new stdClass;
        $x->foo = 'bar';
        $this->assertEquals(true, __isset($x, 'foo'));
        $this->assertEquals(false, __isset($x, 'bar'));
    }

    /**
     *
     */
    public function test__property()
    {
        $x = new stdClass;
        $x->foo = 'bar';
        $this->assertEquals('bar', __property($x, 'foo'));

        $myTestMas = array('foo' => 'bar');
        $this->assertEquals('bar', __property($myTestMas, 'foo'));

        $myTestVariable = 5;
        $this->assertEquals('', __property($myTestVariable, ''));
    }


    /**
     *
     */
    public function test__replaceMacros()
    {
        $this->assertEquals('foobar', replaceMacros('foo$(foo)', array('foo' => 'bar')));

        $this->assertEquals('b', replaceMacros('$(a?b:c)', array('a' => true)));
        $this->assertEquals('c', replaceMacros('$(a?b:c)', array('a' => false)));
    }


}

