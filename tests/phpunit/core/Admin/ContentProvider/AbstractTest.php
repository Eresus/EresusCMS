<?php
/**
 * Тесты класса Eresus_Admin_ContentProvider_Abstract
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

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * Тесты класса Eresus_Admin_ContentProvider_Abstract
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Admin_ContentProvider_AbstractTest extends Eresus_TestCase
{
    /**
     * @covers Eresus_Admin_ContentProvider_Abstract::adminRender
     * @expectedException LogicException
     */
    public function testAdminRenderNoMethod()
    {
        $provider = $this->getMockBuilder('Eresus_Admin_ContentProvider_Abstract')
            ->setMethods(array('getModule', 'getModuleName'))->getMock();
        $provider->expects($this->any())->method('getModule')
            ->will($this->returnValue(new stdClass()));
        /** @var Eresus_Admin_ContentProvider_Abstract $provider */
        $provider->adminRender(new Eresus_CMS_Request(array()));
    }

    /**
     * @covers Eresus_Admin_ContentProvider_Abstract::adminRender
     * @expectedException RuntimeException
     */
    public function testAdminRenderException()
    {
        $module = $this->getMock('stdClass', array('adminRender'));
        $module->expects($this->once())->method('adminRender')
            ->will($this->throwException(new RuntimeException('Bar baz')));
        $provider = $this->getMockBuilder('Eresus_Admin_ContentProvider_Abstract')
            ->setMethods(array('getModule', 'getModuleName'))->getMock();
        $provider->expects($this->any())->method('getModuleName')
            ->will($this->returnValue('Foo'));
        $provider->expects($this->any())->method('getModule')
            ->will($this->returnValue($module));
        /** @var Eresus_Admin_ContentProvider_Abstract $provider */
        $provider->adminRender(new Eresus_CMS_Request(array()));
    }

    /**
     * @covers Eresus_Admin_ContentProvider_Abstract::adminRenderContent
     * @expectedException LogicException
     */
    public function testAdminRenderContentNoMethod()
    {
        $provider = $this->getMockBuilder('Eresus_Admin_ContentProvider_Abstract')
            ->setMethods(array('getModule', 'getModuleName'))->getMock();
        $provider->expects($this->any())->method('getModule')
            ->will($this->returnValue(new stdClass()));
        /** @var Eresus_Admin_ContentProvider_Abstract $provider */
        $provider->adminRenderContent(new Eresus_CMS_Request(array()));
    }

    /**
     * @covers Eresus_Admin_ContentProvider_Abstract::adminRenderContent
     * @expectedException RuntimeException
     */
    public function testAdminRenderContentException()
    {
        $module = $this->getMock('stdClass', array('adminRenderContent'));
        $module->expects($this->once())->method('adminRenderContent')
            ->will($this->throwException(new RuntimeException('Bar baz')));
        $provider = $this->getMockBuilder('Eresus_Admin_ContentProvider_Abstract')
            ->setMethods(array('getModule', 'getModuleName'))->getMock();
        $provider->expects($this->any())->method('getModuleName')
            ->will($this->returnValue('Foo'));
        $provider->expects($this->any())->method('getModule')
            ->will($this->returnValue($module));
        /** @var Eresus_Admin_ContentProvider_Abstract $provider */
        $provider->adminRenderContent(new Eresus_CMS_Request(array()));
    }
}

