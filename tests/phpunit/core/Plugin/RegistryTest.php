<?php
/**
 * Тесты класса Eresus_Plugin_Registry
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
 * Тесты класса Eresus_Plugin_Registry
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Plugin_RegistryTest extends Eresus_TestCase
{
    /**
     * @covers Eresus_Plugin_Registry::autoload
     */
    public function testAutoload()
    {
        $plugins = $this->getMockBuilder('Eresus_Plugin_Registry')->setMethods(array('load'))
            ->disableOriginalConstructor()->getMock();
        $plugins->expects($this->any())->method('load')->
            will($this->returnCallback(
                function ($a)
                {
                    return 'foo' == $a;
                }
            ));

        $app = $this->getMock('stdClass', array('getFsRoot'));
        $app->expects($this->any())->method('getFsRoot')->
            will($this->returnValue(TESTS_FIXT_DIR . '/core/Plugins/'));
        $this->setStaticProperty('Eresus_Kernel', $app, 'app');

        /** @var Eresus_Plugin_Registry $plugins */
        // Нет такого файла
        $this->assertFalse($plugins->autoload('Baz_Foo_Bar'));

        // Файл есть, но плагин не активирован
        $this->assertFalse($plugins->autoload('Bar_Foo_Baz'));

        // Файл есть и плагин активирован
        $this->assertTrue($plugins->autoload('Foo_Bar_Baz'));
        $this->assertTrue(class_exists('Foo_Bar_Baz', false));
    }
}

