<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class WebPageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Проверяем WebPage::linkScripts
     *
     * @covers WebPage::linkScripts
     * @covers WebPage::renderHeadSection
     * @covers WebPage::renderBodySection
     */
    public function test_linkScripts()
    {
        if (version_compare(PHP_VERSION, '5.3', '<'))
        {
            $this->markTestSkipped('PHP 5.3 required');
        }

        $page = $this->getMockForAbstractClass('WebPage');
        /** @var WebPage $page */
        $page->linkScripts('head.js');
        $page->linkScripts('body.js', 'defer');

        $renderHeadSection = new ReflectionMethod('WebPage', 'renderHeadSection');
        $renderHeadSection->setAccessible(true);
        $this->assertEquals('<script type="text/javascript" src="head.js"></script>', $renderHeadSection->invoke($page));

        $renderBodySection = new ReflectionMethod('WebPage', 'renderBodySection');
        $renderBodySection->setAccessible(true);
        $this->assertEquals('<script type="text/javascript" src="body.js" defer></script>', $renderBodySection->invoke($page));
    }
    //-----------------------------------------------------------------------------

    /**
     * Проверяем WebPage::addScripts
     *
     * @covers WebPage::addScripts
     * @covers WebPage::renderHeadSection
     * @covers WebPage::renderBodySection
     */
    public function test_addScripts()
    {
        $page = $this->getMockForAbstractClass('WebPage');
        /** @var WebPage $page */
        $page->addScripts('var head;');
        $page->addScripts('var body;', 'body');

        $this->assertEquals(
            "<script type=\"text/javascript\">//<!-- <![CDATA[\nvar head;\n//]] --></script>",
            $page->renderHeadSection());

        $renderBodySection = new ReflectionMethod('WebPage', 'renderBodySection');
        $renderBodySection->setAccessible(true);
        $this->assertEquals(
            "<script type=\"text/javascript\">//<!-- <![CDATA[\nvar body;\n//]] --></script>",
            $renderBodySection->invoke($page)
        );
    }
    //-----------------------------------------------------------------------------

    /**
     * @covers WebPage::setMetaHeader
     */
    public function test_setMetaHeader()
    {
        $p_head = new ReflectionProperty('WebPage', 'head');
        $p_head->setAccessible(true);

        $page = $this->getMockForAbstractClass('WebPage');
        /** @var WebPage $page */
        $page->setMetaHeader('foo', 'bar');

        $head = $p_head->getValue($page);

        $this->assertArrayHasKey('foo', $head['meta-http']);
        $this->assertEquals('bar', $head['meta-http']['foo']);
    }

    /**
     * @covers WebPage::setMetaTag
     */
    public function test_setMetaTag()
    {
        $p_head = new ReflectionProperty('WebPage', 'head');
        $p_head->setAccessible(true);

        $page = $this->getMockForAbstractClass('WebPage');
        /** @var WebPage $page */
        $page->setMetaTag('bar', 'foo');

        $head = $p_head->getValue($page);

        $this->assertArrayHasKey('bar', $head['meta-tags']);
        $this->assertEquals('foo', $head['meta-tags']['bar']);
    }

    /**
     * @covers WebPage::linkStyles
     */
    public function test_linkStyles()
    {
        $p_head = new ReflectionProperty('WebPage', 'head');
        $p_head->setAccessible(true);

        $page = $this->getMockForAbstractClass('WebPage');
        /** @var WebPage $page */
        $page->linkStyles('foo', 'bar');

        $head = $p_head->getValue($page);

        $this->assertCount(1, $head['link']);
        $this->assertEquals('foo', $head['link'][0]['href']);
        $this->assertEquals('bar', $head['link'][0]['media']);

        $page->linkStyles('bar', 'foo');
        $head = $p_head->getValue($page);

        $page->linkStyles('foo', 'bar');
        $head = $p_head->getValue($page);

        $this->assertCount(2, $head['link']);

    }

    /**
     * @covers WebPage::addStyles
     */
    public function test_addStyles()
    {
        $p_head = new ReflectionProperty('WebPage', 'head');
        $p_head->setAccessible(true);

        $page = $this->getMockForAbstractClass('WebPage');
        /** @var WebPage $page */
        $page->addStyles(' foo', 'bar');
        $head = $p_head->getValue($page);

        $this->assertCount(1, $head['style']);
        $this->assertEquals('		foo', $head['style'][0]['content']);
        $this->assertEquals('bar', $head['style'][0]['media']);

        $page->addStyles('bar', 'foo');
        $head = $p_head->getValue($page);

        $this->assertCount(2, $head['style']);
        $this->assertEquals("	bar", $head['style'][1]['content']);
        $this->assertEquals('foo', $head['style'][1]['media']);
    }
}

