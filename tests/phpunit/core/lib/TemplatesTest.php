<?php
/**
 * Тесты класса Eresus_Templates
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
 * Тесты класса Eresus_Templates
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_TemplatesTest extends Eresus_TestCase
{
    /**
     * @covers Templates::get
     */
    public function testGet()
    {
        $kernel = $this->getMock('stdClass', array('getFsRoot'));
        $kernel->expects($this->any())->method('getFsRoot')->
            will($this->returnValue(TESTS_FIXT_DIR . '/core/lib'));
        $GLOBALS['kernel'] = $kernel;

        $templates = new Templates();

        $tpl = $templates->get('foo', '', true);
        $this->assertInternalType('array', $tpl);
        $this->assertEquals('Foo', $tpl['desc']);

        $tpl = $templates->get('bar', '', true);
        $this->assertInternalType('array', $tpl);
        $this->assertEquals('Default', $tpl['desc']);

        $this->assertFalse($templates->get('bar', 'sub'));
    }
}
