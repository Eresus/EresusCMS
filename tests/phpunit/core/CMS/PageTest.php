<?php
/**
 * Тесты класса Eresus_CMS_Page
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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
 */

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Тесты класса Eresus_CMS_Page
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_PageTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_CMS_Page::__get
     */
    public function testMagicGet()
    {
        $page = $this->getMockBuilder('Eresus_CMS_Page')
            ->setMethods(array('getFoo', 'getTitle', 'setTitle', 'getDescription', 'getKeywords'))
            ->getMock();
        $page->expects($this->any())->method('getFoo')->will($this->returnValue('bar'));
        $this->assertEquals('bar', $page->foo);
        $this->assertNull($page->bar);
    }

    /**
     * @covers Eresus_CMS_Page::addErrorMessage
     * @covers Eresus_CMS_Page::getErrorMessages
     * @covers Eresus_CMS_Page::clearErrorMessages
     */
    public function testErrorMessages()
    {
        $page = $this->getMockForAbstractClass('Eresus_CMS_Page');
        /** @var Eresus_CMS_Page $page */
        $page->addErrorMessage('foo');
        $page->addErrorMessage('bar');
        $this->assertEquals(array('foo', 'bar'), $page->getErrorMessages());
        $page->clearErrorMessages();
        $this->assertEquals(array(), $page->getErrorMessages());
    }
}

