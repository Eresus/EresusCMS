<?php
/**
 * Тесты класса Eresus_CMS_Page_Admin
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
 * Тесты класса Eresus_CMS_Page_Admin
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_Page_AdminTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_CMS_Page_Admin::getTitle
     */
    public function testGetTitle()
    {
        $page = new Eresus_CMS_Page_Admin;
        $this->assertEquals('', $page->getTitle());
    }

    /**
     * @covers Eresus_CMS_Page_Admin::getDescription
     */
    public function testGetDescription()
    {
        $page = new Eresus_CMS_Page_Admin;
        $this->assertEquals('', $page->getDescription());
    }

    /**
     * @covers Eresus_CMS_Page_Admin::getKeywords
     */
    public function testGetKeywords()
    {
        $page = new Eresus_CMS_Page_Admin;
        $this->assertEquals('', $page->getKeywords());
    }
}

