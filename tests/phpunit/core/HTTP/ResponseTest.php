<?php
/**
 * Тесты класса Eresus_HTTP_Response
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
 * Тесты класса Eresus_HTTP_Response
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTTP_ResponseTest extends PHPUnit_Framework_TestCase
{
    /**
     * Общий тест
     * @covers Eresus_HTTP_Response::__construct
     * @covers Eresus_HTTP_Response::sendContent
     */
    public function testBrief()
    {
        $response = new Eresus_HTTP_Response('foo');
        $this->expectOutputString('foo');
        $response->sendContent();
    }

    /**
     * Попытка установить неправильный номер версии
     *
     * @covers Eresus_HTTP_Response::setProtocolVersion
     * @expectedException InvalidArgumentException
     */
    public function testSetProtocolVersionInvalid()
    {
        $response = new Eresus_HTTP_Response();
        $response->setProtocolVersion('1');
    }

    /**
     * Установка и получение номера версии
     *
     * @covers Eresus_HTTP_Response::setProtocolVersion
     * @covers Eresus_HTTP_Response::getProtocolVersion
     */
    public function testSetGetProtocolVersion()
    {
        $response = new Eresus_HTTP_Response();
        $this->assertEquals('1.1', $response->getProtocolVersion());
        $response->setProtocolVersion('1.0');
        $this->assertEquals('1.0', $response->getProtocolVersion());
        $response->setProtocolVersion('3.2.1');
        $this->assertEquals('3.2.1', $response->getProtocolVersion());
    }

    /**
     * Установка и получение кода состояния
     *
     * @covers Eresus_HTTP_Response::setStatusCode
     * @covers Eresus_HTTP_Response::getStatusCode
     */
    public function testSetGetStatusCode()
    {
        $response = new Eresus_HTTP_Response();
        $this->assertEquals(200, $response->getStatusCode());
        $response->setStatusCode(404);
        $this->assertEquals(404, $response->getStatusCode());
        $response->setStatusCode('404');
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * Установка и получение тела ответа
     *
     * @covers Eresus_HTTP_Response::setContent
     * @covers Eresus_HTTP_Response::getContent
     */
    public function testSetGetContent()
    {
        $response = new Eresus_HTTP_Response();
        $this->assertEquals('', $response->getContent());
        $response->setContent('foo');
        $this->assertEquals('foo', $response->getContent());
        $response->setContent(array('bar'));
        $this->assertEquals(array('bar'), $response->getContent());
    }
}

