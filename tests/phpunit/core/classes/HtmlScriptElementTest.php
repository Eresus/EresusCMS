<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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
 */

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class HtmlScriptElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * Проверяем простую конструкцию
     *
     * @covers HtmlScriptElement::__construct
     */
    public function test_simple()
    {
        $test = new HtmlScriptElement();
        $this->assertEquals('<script type="text/javascript"></script>', $test->getHTML());
    }

    /**
     * Проверяем конструктор с URL
     *
     * @covers HtmlScriptElement::__construct
     */
    public function test_construct_URL()
    {
        $test = new HtmlScriptElement('http://example.org/some_script');
        $this->assertEquals(
            '<script type="text/javascript" src="http://example.org/some_script"></script>',
            $test->getHTML()
        );

        $test = new HtmlScriptElement('/some_script.js');
        $this->assertEquals(
            '<script type="text/javascript" src="/some_script.js"></script>',
            $test->getHTML()
        );
    }

    /**
     * Проверяем конструктор с кодом
     *
     * @covers HtmlScriptElement::__construct
     * @covers HtmlScriptElement::setContents
     */
    public function test_construct_code()
    {
        $test = new HtmlScriptElement('alert("Hello world");');
        $this->assertEquals(
            "<script type=\"text/javascript\">//<!-- <![CDATA[\n" .
            "alert(\"Hello world\");\n//]] --></script>",
            $test->getHTML()
        );

        $test = new HtmlScriptElement('jQuery.webshims.polyfill();');
        $this->assertEquals(
            "<script type=\"text/javascript\">//<!-- <![CDATA[\n" .
            "jQuery.webshims.polyfill();\n//]] --></script>",
            $test->getHTML()
        );
    }

    /**
     * Проверяем async и defer
     *
     * @covers HtmlScriptElement::__construct
     */
    public function test_construct_async_defer()
    {
        $test = new HtmlScriptElement('script.js');
        $test->setAttribute('defer');
        $this->assertEquals('<script type="text/javascript" src="script.js" defer></script>',
            $test->getHTML());
    }

    /* */
}
