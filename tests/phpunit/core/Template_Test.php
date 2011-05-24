<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package Templates
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Template.php';

class Eresus_Template_Test extends PHPUnit_Framework_TestCase
{
	private $error_log;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$this->error_log = ini_get('error_log');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		ini_set('error_log', $this->error_log);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::compile
	 * @expectedException InvalidArgumentException
	 */
	public function test_unexistent()
	{
		/*$exception = new ErrorException('file not found', 0, E_ERROR, 'some_file', 123);
		$dwoo = $this->getMockBuilder('stdClass')->setMethods(array('get'))->getMock();
		$dwoo->expects($this->once())->method('get')->will($this->throwException($exception));

		$test = new Eresus_Template();

		$dwooProp = new ReflectionProperty('Eresus_Template', 'dwoo');
		$dwooProp->setAccessible(true);
		$dwooProp->setValue($test, $dwoo);

		ini_set('error_log', false);
		$this->assertEmpty($test->compile());*/

		$test = new Eresus_Template();
		$test->compile();
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::setGlobalValue
	 * @covers Eresus_Template::getGlobalValue
	 * @covers Eresus_Template::removeGlobalValue
	 */
	public function testSetGetRemove()
	{
		Eresus_Template::setGlobalValue('test', 'testValue');
		$this->assertEquals('testValue', Eresus_Template::getGlobalValue('test'));
		Eresus_Template::removeGlobalValue('test');
		$this->assertNull(Eresus_Template::getGlobalValue('test'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Template::__construct
	 */
	public function test_setCharset()
	{
		Eresus_Config::set('core.template.charset', 'windows-1251');
		$test = new Eresus_Template();
	}
	//-----------------------------------------------------------------------------

	/* */
}
