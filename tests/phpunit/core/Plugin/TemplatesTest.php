<?php
/**
 * Тесты класса Eresus_Plugin_Templates
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

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Тесты класса Eresus_Plugin_Templates
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Plugin_TemplatesTest extends Eresus_TestCase
{
    /**
     * Тестовый плагин
     * @var Eresus_Plugin
     */
    protected $plugin;

    /**
     * Подготовка окружения
     */
    protected function setUp()
    {
        vfsStreamWrapper::register();
        vfsStream::setup('site', null, array(
            'ext' => array(
                'foo' => array(
                    'admin' => array(
                        'templates' => array(
                            'Foo.html' => 'admin-foo'
                        ),
                    ),
                    'client' => array(
                        'templates' => array(
                            'Foo.html' => 'client-foo-ro'
                        )
                    )
                )
            ),
            'templates' => array(
                'foo' => array(
                    'Foo.html' => 'client-foo'
                )
            ),
            'var' => array('cache' => array('templates' => array())),
        ));

        $cms = $this->getMock('stdClass', array('getFsRoot'));
        $cms->expects($this->any())->method('getFsRoot')
            ->will($this->returnValue(vfsStream::url('site')));
        $this->setStaticProperty('Eresus_Kernel', $cms, 'app');

        $this->plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()
            ->setMethods(array('getName'))->getMock();
        $this->plugin->expects($this->any())->method('getName')->will($this->returnValue('foo'));
    }

    /**
     * Тест метода admin
     * @covers Eresus_Plugin_Templates::admin
     */
    public function testAdmin()
    {
        $templates = new Eresus_Plugin_Templates($this->plugin);
        $tmpl = $templates->admin('Foo.html');
        $this->assertInstanceOf('Eresus_Template', $tmpl);
        $this->assertEquals('admin-foo', $tmpl->getSource());
    }

    /**
     * Тест метода client
     * @covers Eresus_Plugin_Templates::client
     */
    public function testClient()
    {
        $templates = new Eresus_Plugin_Templates($this->plugin);
        $tmpl = $templates->client('Foo.html');
        $this->assertInstanceOf('Eresus_Template', $tmpl);
        $this->assertEquals('client-foo', $tmpl->getSource());
    }

    /**
     * Тест метода clientRead
     * @covers Eresus_Plugin_Templates::clientRead
     */
    public function testClientRead()
    {
        $templates = new Eresus_Plugin_Templates($this->plugin);
        $text = $templates->clientRead('Foo.html');
        $this->assertEquals('client-foo', $text);
    }

    /**
     * Тест метода clientWrite
     * @covers Eresus_Plugin_Templates::clientWrite
     */
    public function testClientWrite()
    {
        $templates = new Eresus_Plugin_Templates($this->plugin);
        $templates->clientWrite('Foo.html', 'bar');
        $this->assertEquals('bar',
            file_get_contents(vfsStream::url('site/templates/foo/Foo.html')));
    }
}

