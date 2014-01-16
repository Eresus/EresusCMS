<?php
/**
 * Тесты класса Eresus_HTTP_Redirect
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
 * Тесты класса Eresus_HTTP_Redirect
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTTP_RedirectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Общий тест
     * @covers Eresus_HTTP_Redirect::__construct
     * @covers Eresus_HTTP_Redirect::getStatusCode
     */
    public function testBrief()
    {
        $response = new Eresus_HTTP_Redirect('foo');
        $this->assertEquals(303, $response->getStatusCode());
    }

    /**
     * @covers Eresus_HTTP_Redirect::setStatusCode
     */
    public function testSetStatusCode()
    {
        $response = new Eresus_HTTP_Redirect();
        $response->setStatusCode(307);
        $this->assertEquals(307, $response->getStatusCode());
        $response->setProtocolVersion('1.0');
        $response->setStatusCode(303);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * @covers Eresus_HTTP_Redirect::sendContent
     */
    public function testSendContent()
    {
        $response = new Eresus_HTTP_Redirect('http://example.com/');
        $this->expectOutputRegex('#http://example\.com/#');
        $response->sendContent();
    }
}

