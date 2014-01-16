<?php
/**
 * Тесты класса Eresus_Plugin
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

require_once __DIR__ . '/../bootstrap.php';

/**
 * Тесты класса Eresus_Plugin
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_PluginTest extends Eresus_TestCase
{
    /**
     * Подготовка окружения
     */
    protected function setUp()
    {
        $plugins = $this->getMockBuilder('Eresus_Plugin_Registry')->disableOriginalConstructor()
            ->setMethods(array('registerBcEventListeners'))->getMock();
        $plugins->expects($this->any())->method('registerBcEventListeners')
            ->will($this->returnValue(null));
        $this->setStaticProperty('Eresus_Plugin_Registry', $plugins);
    }

    /**
     * Тест метода getName
     * @covers Eresus_Plugin::getName
     */
    public function testGetName()
    {
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('_'))->getMock();
        /** @var Eresus_Plugin $plugin */
        $this->assertRegExp('/^mock_eresus_plugin_/', $plugin->getName());

        $name = new ReflectionProperty('Eresus_Plugin', 'name');
        $name->setAccessible(true);
        $name->setValue($plugin, 'foo');
        $this->assertEquals('foo', $plugin->getName());
    }

    /**
     * @covers Eresus_Plugin::getDataUrl
     */
    public function testGetDataURL()
    {
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->froot = '/home/example.org/';
        $GLOBALS['Eresus']->fdata = '/home/example.org/data/';
        $GLOBALS['Eresus']->fstyle = '/home/example.org/style/';
        $GLOBALS['Eresus']->root = 'http://example.org/';
        $GLOBALS['Eresus']->data = 'http://example.org/data/';
        $GLOBALS['Eresus']->style = 'http://example.org/style/';
        $GLOBALS['Eresus']->plugins = new stdClass();
        $GLOBALS['Eresus']->plugins->list = array();
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('getName'))->getMock();
        $plugin->expects($this->any())->method('getName')->will($this->returnValue('plugin'));

        /** @var Eresus_Plugin $plugin */
        $plugin->__construct();
        $this->assertEquals('http://example.org/data/plugin/', $plugin->getDataUrl());
    }

    /**
     * @covers Eresus_Plugin::getCodeUrl
     */
    public function testGetCodeURL()
    {
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->froot = '/home/example.org/';
        $GLOBALS['Eresus']->fdata = '/home/example.org/data/';
        $GLOBALS['Eresus']->fstyle = '/home/example.org/style/';
        $GLOBALS['Eresus']->root = 'http://example.org/';
        $GLOBALS['Eresus']->data = 'http://example.org/data/';
        $GLOBALS['Eresus']->style = 'http://example.org/style/';
        $GLOBALS['Eresus']->plugins = new stdClass();
        $GLOBALS['Eresus']->plugins->list = array();
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('getName'))->getMock();
        $plugin->expects($this->any())->method('getName')->will($this->returnValue('plugin'));
        /** @var Eresus_Plugin $plugin */
        $plugin->__construct();
        $this->assertEquals('http://example.org/ext/plugin/', $plugin->getCodeUrl());
    }

    /**
     * @covers Eresus_Plugin::getStyleUrl
     */
    public function testGetStyleURL()
    {
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->froot = '/home/example.org/';
        $GLOBALS['Eresus']->fdata = '/home/example.org/data/';
        $GLOBALS['Eresus']->fstyle = '/home/example.org/style/';
        $GLOBALS['Eresus']->root = 'http://example.org/';
        $GLOBALS['Eresus']->data = 'http://example.org/data/';
        $GLOBALS['Eresus']->style = 'http://example.org/style/';
        $GLOBALS['Eresus']->plugins = new stdClass();
        $GLOBALS['Eresus']->plugins->list = array();
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('getName'))->getMock();
        $plugin->expects($this->any())->method('getName')->will($this->returnValue('plugin'));
        /** @var Eresus_Plugin $plugin */
        $plugin->__construct();
        $this->assertEquals('http://example.org/style/plugin/', $plugin->getStyleUrl());
    }

    /**
     * Тест метода templates
     *
     * @covers Eresus_Plugin::templates
     */
    public function testTemplates()
    {
        $legacyKernel = $this->getMock('Eresus');
        /** @var Eresus $legacyKernel */
        $legacyKernel->plugins = new stdClass();
        $legacyKernel->plugins->list = array();
        $GLOBALS['Eresus'] = $legacyKernel;
        $plugin = $this->getMock('Eresus_Plugin', array('_'));
        /** @var Eresus_Plugin $plugin */
        $templates = $plugin->templates();
        $this->assertInstanceOf('Eresus_Plugin_Templates', $templates);
        $this->assertSame($templates, $plugin->templates());
    }

    /**
     * Тест метода Eresus_Plugin::install
     *
     * @covers Eresus_Plugin::install
     */
    public function testInstall()
    {
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('installTemplates'))->getMock();
        $plugin->expects($this->once())->method('installTemplates');
        /** @var Eresus_Plugin $plugin */
        $plugin->install();
    }

    /**
     * Тест метода Eresus_Plugin::uninstall
     *
     * @covers Eresus_Plugin::uninstall
     */
    public function testUninstall()
    {
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('uninstallTemplates', 'cleanupDB'))->getMock();
        $plugin->expects($this->once())->method('uninstallTemplates');
        $plugin->expects($this->once())->method('cleanupDB');
        /** @var Eresus_Plugin $plugin */
        $plugin->uninstall();
    }
}

