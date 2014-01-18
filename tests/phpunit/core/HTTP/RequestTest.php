<?php
/**
 * Тесты класса Eresus_HTTP_Request
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
 * Тесты класса Eresus_HTTP_Request
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_HTTP_RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_HTTP_Request::getScheme
     * @covers Eresus_HTTP_Request::setScheme
     */
    public function testScheme()
    {
        $request = new Eresus_HTTP_Request();
        $this->assertEquals('http', $request->getScheme());
    }

    /**
     * @covers Eresus_HTTP_Request::getMethod
     * @covers Eresus_HTTP_Request::setMethod
     */
    public function testMethod()
    {
        $request = new Eresus_HTTP_Request();
        $request->setMethod('POST');
        $this->assertEquals('POST', $request->getMethod());
    }

    /**
     * @covers Eresus_HTTP_Request::getHost
     * @covers Eresus_HTTP_Request::setHost
     */
    public function testHost()
    {
        $request = new Eresus_HTTP_Request(array(), array(), array(), array(), array(),
            array('HTTP_HOST' => 'example.org'));
        $this->assertEquals('example.org', $request->getHost());
    }

    /**
     * @covers Eresus_HTTP_Request::getPath
     * @covers Eresus_HTTP_Request::setPath
     */
    public function testPath()
    {
        $request = new Eresus_HTTP_Request(array(), array(), array(), array(), array(),
            array('REQUEST_URI' => '/foo'));
        $this->assertEquals('/foo', $request->getPath());
    }

    /**
     * @covers Eresus_HTTP_Request::getQueryString
     * @covers Eresus_HTTP_Request::setQueryString
     */
    public function testQuery()
    {
        $request = new Eresus_HTTP_Request(array(), array(), array(), array(), array(),
            array('QUERY_STRING' => 'foo=bar&bar=baz'));
        $this->assertEquals('bar=baz&foo=bar', $request->getQueryString());
    }

    /**
     * @covers Eresus_HTTP_Request::getDirectory
     * @covers Eresus_HTTP_Request::getFile
     */
    public function testDirectoryFile()
    {
        $request = new Eresus_HTTP_Request(array(), array(), array(), array(), array(),
            array('REQUEST_URI' => '/foo/bar'));
        $this->assertEquals('/foo', $request->getDirectory());
        $this->assertEquals('bar', $request->getFile());

        $request = new Eresus_HTTP_Request(array(), array(), array(), array(), array(),
            array('REQUEST_URI' => '/foo/'));
        $this->assertEquals('/foo', $request->getDirectory());
        $this->assertEquals('', $request->getFile());
    }

    /**
     * Создание запроса
     *
     * @covers Eresus_HTTP_Request::__construct
     */
    public function testConstruct()
    {
        $request = new Eresus_HTTP_Request('http://example.org/site/path/file?foo=bar');
        $this->assertEquals('http', $request->getScheme());
        $this->assertEquals('example.org', $request->getHost());
        $this->assertEquals('/site/path/file', $request->getPath());
        $this->assertEquals('foo=bar', $request->getQueryString());

        $request = new Eresus_HTTP_Request();
        $this->assertEquals('http', $request->getScheme());
        $this->assertEquals('', $request->getHost());
        $this->assertEquals('/', $request->getPath());
        $this->assertEquals('', $request->getQueryString());
    }

    /**
     * Создание запроса из другого запроса
     *
     * @covers Eresus_HTTP_Request::__construct
     */
    public function testConstructFromRequest()
    {
        $request1 = new Eresus_HTTP_Request('http://example.org/');
        $request1->setMethod('POST');
        $request1->request->add(array('foo' => 'bar'));
        $request2 = new Eresus_HTTP_Request($request1);
        $this->assertEquals('example.org', $request2->getHost());
        $this->assertEquals('POST', $request2->getMethod());
        $this->assertTrue($request2->request->has('foo'));
    }
}

