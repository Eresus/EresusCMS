<?php
/**
 * Тесты класса Eresus_Plugin_Controller_Admin_LegacySettings
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

require_once __DIR__ . '/../../../../bootstrap.php';

/**
 * Тесты класса Eresus_Plugin_Controller_Admin_LegacySettings
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Plugin_Controller_Admin_LegacySettingsTest extends Eresus_TestCase
{
    /**
     * @covers Eresus_Plugin_Controller_Admin_LegacySettings::call
     * @expectedException Eresus_CMS_Exception_NotFound
     */
    public function testCallNoMethod()
    {
        $call = new ReflectionMethod('Eresus_Plugin_Controller_Admin_LegacySettings', 'call');
        $call->setAccessible(true);

        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->getMock();
        $controller = new Eresus_Plugin_Controller_Admin_LegacySettings($plugin);
        $call->invoke($controller, 'foo');
    }

    /**
     * @covers Eresus_Plugin_Controller_Admin_LegacySettings::getHtml
     * @covers Eresus_Plugin_Controller_Admin_LegacySettings::call
     */
    public function testGet()
    {
        $kernel = new stdClass();
        $kernel->request = array('method' => 'GET');
        $app = $this->getMock('stdClass', array('getLegacyKernel'));
        $app->expects($this->any())->method('getLegacyKernel')->will($this->returnValue($kernel));
        $this->setStaticProperty('Eresus_Kernel', $app, 'app');
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('settings'))->getMock();
        $plugin->expects($this->once())->method('settings');
        $controller = new Eresus_Plugin_Controller_Admin_LegacySettings($plugin);
        $controller->getHtml(new Eresus_CMS_Request(array()));
    }

    /**
     * @covers Eresus_Plugin_Controller_Admin_LegacySettings::getHtml
     * @covers Eresus_Plugin_Controller_Admin_LegacySettings::call
     */
    public function testPost()
    {
        $request = new Eresus_CMS_Request(array());
        $request->setMethod('POST');
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('updateSettings'))->getMock();
        $plugin->expects($this->once())->method('updateSettings');
        $controller = new Eresus_Plugin_Controller_Admin_LegacySettings($plugin);
        $controller->getHtml($request);
    }
}

