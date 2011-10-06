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

require_once dirname(__FILE__) . '/../../stubs.php';
require_once TESTS_SRC_DIR . '/core/classes.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class PluginTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Plugin::getDataURL
	 */
	public function test_getDataURL()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Plugin();
		$this->assertEquals('http://exmaple.org/data/plugin/', $test->getDataURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Plugin::getCodeURL
	 */
	public function test_getCodeURL()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Plugin();
		$this->assertEquals('http://exmaple.org/ext/plugin/', $test->getCodeURL());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Plugin::getStyleURL
	 */
	public function test_getStyleURL()
	{
		$GLOBALS['Eresus'] = new stdClass();
		$GLOBALS['Eresus']->froot = '/home/exmaple.org/';
		$GLOBALS['Eresus']->fdata = '/home/exmaple.org/data/';
		$GLOBALS['Eresus']->fstyle = '/home/exmaple.org/style/';
		$GLOBALS['Eresus']->root = 'http://exmaple.org/';
		$GLOBALS['Eresus']->data = 'http://exmaple.org/data/';
		$GLOBALS['Eresus']->style = 'http://exmaple.org/style/';
		$test = new Plugin();
		$this->assertEquals('http://exmaple.org/style/plugin/', $test->getStyleURL());
	}
	//-----------------------------------------------------------------------------

	/* */
}
