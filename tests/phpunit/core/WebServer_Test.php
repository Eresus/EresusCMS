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
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/WebServer.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_WebServer_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_WebServer::__construct
	 * @covers Eresus_WebServer::getInstance
	 * @covers Eresus_WebServer::getDocumentRoot
	 */
	public function test_getDocumentRoot()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}
		$instnce = new ReflectionProperty('Eresus_WebServer', 'instance');
		$instnce->setAccessible(true);
		$instnce->setValue('Eresus_WebServer', null);

		$dir = dirname(__FILE__);
		$_SERVER['DOCUMENT_ROOT'] = $dir;

		$driver = $this->getMock('stdClass', array('canonicalForm'));
		$driver->expects($this->once())->method('canonicalForm')->will($this->returnArgument(0));
		FS::$driver = $driver;

		$server = Eresus_WebServer::getInstance();
		$this->assertEquals($dir, $server->getDocumentRoot());
	}
	//-----------------------------------------------------------------------------

	/* */
}