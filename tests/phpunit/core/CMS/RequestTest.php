<?php
/**
 * Тесты класса Eresus_CMS_Request
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
 * Тесты класса Eresus_CMS_Request
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_CMS_RequestTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_CMS_Request::getScheme
     * @covers Eresus_CMS_Request::setScheme
     */
    public function testScheme()
    {
        $request = new Eresus_CMS_Request();
        $this->assertEquals('http', $request->getScheme());
    }

    /**
     * @covers Eresus_CMS_Request::getMethod
     * @covers Eresus_CMS_Request::setMethod
     */
    public function testMethod()
    {
        $request = new Eresus_CMS_Request();
        $this->assertEquals('GET', $request->getMethod());
    }

    /**
     * @covers Eresus_CMS_Request::getHost
     * @covers Eresus_CMS_Request::setHost
     */
    public function testHost()
    {
        $request = new Eresus_CMS_Request();
        $this->assertEquals('localhost', $request->getHost());
    }

    /**
     * @covers Eresus_CMS_Request::getSiteRoot
     * @covers Eresus_CMS_Request::setSiteRoot
     */
    public function testSiteRoot()
    {
        $request = new Eresus_CMS_Request();
        $this->assertEquals('http://localhost', $request->getSiteRoot());

        $request = new Eresus_CMS_Request('http://example.org/site/path/file.php');
        $request->setSiteRoot('http://example.org/site/');
        $this->assertEquals('http://example.org/site', $request->getSiteRoot());
    }

    /**
     * @covers Eresus_CMS_Request::getPath
     * @covers Eresus_CMS_Request::setPath
     * @covers Eresus_CMS_Request::setSiteRoot
     */
    public function testPath()
    {
        $request = new Eresus_CMS_Request();
        $this->assertEquals('/', $request->getPath());

        $request = new Eresus_CMS_Request('http://example.org/site/path/file.php');
        $this->assertEquals('/site/path/file.php', $request->getPath());
        $request->setSiteRoot('http://example.org/site/');
        $this->assertEquals('/path/file.php', $request->getPath());
        $request->setSiteRoot('site');
        $this->assertEquals('/path/file.php', $request->getPath());
        $request->setSiteRoot(false);
        $this->assertEquals('/site/path/file.php', $request->getPath());
    }

    /**
     * @covers Eresus_CMS_Request::getDirectory
     * @covers Eresus_CMS_Request::getFile
     */
    public function testDirectoryFile()
    {
        $request = new Eresus_CMS_Request();
        $this->assertEquals('', $request->getDirectory());
        $this->assertEquals('', $request->getFile());

        $request = new Eresus_CMS_Request('http://example.org/site/path/file.php');
        $request->setSiteRoot('http://example.org/site/');
        $this->assertEquals('/path', $request->getDirectory());
        $this->assertEquals('file.php', $request->getFile());
    }

    /**
     * Формирование URL
     *
     * @covers Eresus_CMS_Request::__toString
     */
    public function testToString()
    {
        $request = new Eresus_CMS_Request();
        $this->assertEquals('http://localhost/', strval($request));

        $request = new Eresus_CMS_Request('http://example.org/site/path/file.php?foo=bar');
        $request->setSiteRoot('http://example.org/site/');
        $this->assertEquals('http://example.org/site/path/file.php?foo=bar', strval($request));
    }

    /**
     * @covers Eresus_CMS_Request::__construct
     * @covers Eresus_CMS_Request::getHttpRequest
     */
    public function testGetHttpRequest()
    {
        $httpRequest = new Eresus_HTTP_Request();
        $cmsRequest = new Eresus_CMS_Request($httpRequest);
        $this->assertSame($httpRequest, $cmsRequest->getHttpRequest());

        $cmsRequest = new Eresus_CMS_Request();
        $this->assertInstanceOf('Eresus_HTTP_Request', $cmsRequest->getHttpRequest());
    }
}

