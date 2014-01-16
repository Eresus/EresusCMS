<?php
/**
 * Тесты класса Eresus_CMS
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
 * Тесты класса Eresus_CMS
 *
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMSTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_CMS::detectWebRoot
     */
    public function testDetectWebRoot()
    {
        /* Подменяем DOCUMENT_ROOT */
        $webServer = WebServer::getInstance();
        $documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
        $documentRoot->setAccessible(true);
        $documentRoot->setValue($webServer, '/home/user/public_html');

        $obj = new Eresus_CMS;
        // Подменяем результат getFsRoot
        $fsRoot = new ReflectionProperty('Eresus_CMS', 'fsRoot');
        $fsRoot->setAccessible(true);
        $fsRoot->setValue($obj, '/home/user/public_html');

        $detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
        $detectWebRoot->setAccessible(true);
        $this->assertEquals('', $detectWebRoot->invoke($obj));
    }

    /**
     * @covers Eresus_CMS::detectWebRoot
     */
    public function testDetectWebRootNotRoot()
    {
        /* Подменяем DOCUMENT_ROOT */
        $webServer = WebServer::getInstance();
        $documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
        $documentRoot->setAccessible(true);
        $documentRoot->setValue($webServer, '/home/user/public_html');

        $obj = new Eresus_CMS;
        // Подменяем результат getFsRoot
        $fsRoot = new ReflectionProperty('Eresus_CMS', 'fsRoot');
        $fsRoot->setAccessible(true);
        $fsRoot->setValue($obj, '/home/user/public_html/example.org');

        $detectWebRoot = new ReflectionMethod('Eresus_CMS', 'detectWebRoot');
        $detectWebRoot->setAccessible(true);

        $this->assertEquals('/example.org', $detectWebRoot->invoke($obj));
    }

    /**
     * @covers Eresus_CMS::getPage
     */
    public function testGetPage()
    {
        $p_page = new ReflectionProperty("Eresus_CMS", "page");
        $p_page->setAccessible(true);

        $eresus = new Eresus_CMS();
        $p_page->setValue($eresus,'foo');

        $this->assertEquals('foo', $eresus->getPage());
    }

    /**
     * @covers Eresus_CMS::getEventDispatcher
     */
    public function testGetEventDispatcher()
    {
        $cms = new Eresus_CMS();
        $this->assertInstanceOf('Eresus_Event_Dispatcher', $cms->getEventDispatcher());
    }
}

