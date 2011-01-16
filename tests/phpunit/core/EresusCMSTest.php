<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package EresusCMS
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/main.php';
require_once dirname(__FILE__) . '/../../../main/core/classes/WebServer.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class EresusCMSTest extends PHPUnit_Framework_TestCase
{
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		Core::$app = null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusCMS::app
	 */
	public function test_app()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		Core::$app = new stdClass();
		$this->assertSame(Core::$app, EresusCMS::app());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusCMS::detectWebRoot
	 */
	public function test_detectWebRoot()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$driver = $this->getMock('stdClass', array('canonicalForm'));
		$driver->expects($this->once())->method('canonicalForm')->will($this->returnArgument(0));
		FS::$driver = $driver;

		/* Подменяем DOCUMENT_ROOT */
		$webServer = WebServer::getInstance();
		$documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$obj = new EresusCMS;
		// Подменяем результат getFsRoot
		$obj->fsRoot = '/home/user/public_html';
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('EresusCMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('EresusCMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('', $httpRequest->localRoot);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusCMS::detectWebRoot
	 */
	public function test_detectWebRoot_notRoot()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = WebServer::getInstance();
		$documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, '/home/user/public_html');

		$obj = new EresusCMS;
		// Подменяем результат getFsRoot
		$obj->fsRoot = '/home/user/public_html/example.org';
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('EresusCMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('EresusCMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('/example.org', $httpRequest->localRoot);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusCMS::detectWebRoot
	 */
	public function test_detectWebRoot_windows()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		/* Подменяем DOCUMENT_ROOT */
		$webServer = WebServer::getInstance();
		$documentRoot = new ReflectionProperty('WebServer', 'documentRoot');
		$documentRoot->setAccessible(true);
		$documentRoot->setValue($webServer, FS::canonicalForm('C:\Program Files\Apache Webserver\docs'));

		$obj = new EresusCMS;
		// Подменяем результат getFsRoot
		$obj->fsRoot = FS::canonicalForm('C:\Program Files\Apache Webserver\docs\example.org');
		$httpRequest = new HttpRequest();

		$request = new ReflectionProperty('EresusCMS', 'request');
		$request->setAccessible(true);
		$request->setValue($obj, $httpRequest);

		$detectWebRoot = new ReflectionMethod('EresusCMS', 'detectWebRoot');
		$detectWebRoot->setAccessible(true);
		$detectWebRoot->invoke($obj);

		$this->assertEquals('/example.org', $httpRequest->localRoot);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusCMS::getDataDir
	 */
	public function test_getDataDir()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$mock = $this->getMockBuilder('EresusCMS')->setMethods(array('getFsRoot'))->
			disableOriginalConstructor()->getMock();
		$mock->expects($this->once())->method('getFsRoot')->
			will($this->returnValue('/home/example.org'));

		$this->assertEquals('/home/example.org/data', $mock->getDataDir());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusCMS::getFrontController
	 */
	public function test_getFrontController()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$test = new stdClass();

		$obj = new EresusCMS;
		$frontController = new ReflectionProperty('EresusCMS', 'frontController');
		$frontController->setAccessible(true);
		$frontController->setValue($obj, $test);

		$this->assertSame($test, $obj->getFrontController());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers EresusCMS::initSession
	 */
	public function test_initSession()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('PHP 5.3.2 required');
		}

		$initSession = new ReflectionMethod('EresusCMS', 'initSession');
		$initSession->setAccessible(true);

		ini_set('session.save_path', '/tmp');
		$EresusCMS = new EresusCMS();
		$initSession->invoke($EresusCMS);
	}
	//-----------------------------------------------------------------------------

	/* */
}
