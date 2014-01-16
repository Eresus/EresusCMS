<?php
/**
 * Тесты класса Eresus_Admin_ContentProvider_Plugin
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

use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStream;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * Тесты класса Eresus_Admin_ContentProvider_Plugin
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Admin_ContentProvider_PluginTest extends Eresus_TestCase
{
    /**
     * @covers Eresus_Admin_ContentProvider_Plugin::getSettingsController
     */
    public function testGetSettingsController()
    {
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()->getMock();
        $provider = new Eresus_Admin_ContentProvider_Plugin($plugin);
        $this->assertFalse($provider->getSettingsController());

        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('settings'))->getMock();

        $page = $this->getMock('stdClass');
        $app = $this->getMock('stdClass', array('getPage'));
        $app->expects($this->any())->method('getPage')->will($this->returnValue($page));
        $this->setStaticProperty('Eresus_Kernel', $app, 'app');

        $provider = new Eresus_Admin_ContentProvider_Plugin($plugin);
        $this->assertInstanceOf('Eresus_Admin_Controller_Content_Interface',
            $provider->getSettingsController());
    }

    /**
     * @covers Eresus_Admin_ContentProvider_Plugin::linkAdminResources
     */
    public function testLinkAdminResources()
    {
        vfsStreamWrapper::register();
        vfsStream::setup('foo', null, array(
            'bar' => array(
                'admin' => array(
                    'default.css' => 'css',
                    'scripts.js' => 'js',
                )
            ),
        ));

        $plugin = $this->getMock('stdClass', array('getCodeDir', 'getCodeUrl'));
        $plugin->expects($this->any())->method('getCodeDir')
            ->will($this->returnValue(vfsStream::url('foo/bar')));
        $plugin->expects($this->any())->method('getCodeUrl')
            ->will($this->returnValue('http://foo/bar/'));

        $provider = $this->getMockBuilder('Eresus_Admin_ContentProvider_Plugin')
            ->disableOriginalConstructor()->setMethods(array('getModule'))->getMock();
        $provider->expects($this->any())->method('getModule')->will($this->returnValue($plugin));

        $linkAdminResources = new ReflectionMethod('Eresus_Admin_ContentProvider_Plugin',
            'linkAdminResources');
        $linkAdminResources->setAccessible(true);

        $page = $this->getMock('stdClass', array('linkStyles', 'linkScripts'));
        $page->expects($this->once())->method('linkStyles')
            ->with('http://foo/bar/admin/default.css');
        $page->expects($this->once())->method('linkScripts')
            ->with('http://foo/bar/admin/scripts.js');

        $app = $this->getMock('stdClass', array('getPage'));
        $app->expects($this->any())->method('getPage')->will($this->returnValue($page));

        $this->setStaticProperty('Eresus_Kernel', $app, 'app');

        $linkAdminResources->invoke($provider);
    }
}

