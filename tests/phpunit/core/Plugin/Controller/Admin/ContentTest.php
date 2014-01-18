<?php
/**
 * Тесты класса Eresus_Plugin_Controller_Admin_Content
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
 * Тесты класса Eresus_Plugin_Controller_Admin_Content
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Plugin_Controller_Admin_ContentTest extends PHPUnit_Framework_TestCase
{
    /**
     * Общий тест
     * @covers Eresus_Plugin_Controller_Admin_Content::getHtml
     * @covers Eresus_Plugin_Controller_Admin_Content::getAction
     */
    public function testOverall()
    {
        $controller = $this->getMockBuilder('Eresus_Plugin_Controller_Admin_Content')
            ->disableOriginalConstructor()->setMethods(array('actionIndex'))->getMock();
        $controller->expects($this->once())->method('actionIndex');
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->request = array('arg' => array());
        /** @var Eresus_Plugin_Controller_Admin_Content $controller */
        $controller->getHtml(new Eresus_CMS_Request(array()));
    }

    /**
     * Тест реакции на несуществующее действие
     * @covers Eresus_Plugin_Controller_Admin_Content::getHtml
     * @expectedException Eresus_CMS_Exception_NotFound
     */
    public function testActionNotFound()
    {
        $plugin = $this->getMockBuilder('Eresus_Plugin')->disableOriginalConstructor()->getMock();
        $controller = $this->getMockForAbstractClass('Eresus_Plugin_Controller_Admin_Content',
            array($plugin));
        $GLOBALS['Eresus'] = new stdClass();
        $GLOBALS['Eresus']->request = array('arg' => array());
        /** @var Eresus_Plugin_Controller_Admin_Content $controller */
        $controller->getHtml(new Eresus_CMS_Request(array()));
    }
}

