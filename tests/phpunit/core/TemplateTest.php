<?php
/**
 * Тесты класса Template
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
 * Тесты класса Template
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_TemplateTest extends Eresus_TestCase
{
    /**
     * Тест метода setContents
     *
     * @covers Eresus_Template::setSource
     */
    public function testSetContents()
    {
        $dwoo = $this->getMock('stdClass', array('get'));
        $dwoo->expects($this->once())->method('get')->will($this->returnArgument(0));
        $this->setStaticProperty('Template', $dwoo, 'dwoo');
        $tmpl = new Eresus_Template();
        $tmpl->setSource('foo');
        $template = $tmpl->compile();
        $this->assertInstanceOf('Dwoo_Template_String', $template);
    }
}

