<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * @package Tests
 *
 * $Id$
 */

require_once dirname(__FILE__) . '/../helpers.php';

require_once TEST_DIR_ROOT . '/core/lib/mysql.php';

/**
 * @package Tests
 */
class MySQLTest extends PHPUnit_Framework_TestCase
{

	/**
	 * Проверка метода MySQL::init
	 */
	public function testInit()
	{
		$fixture = new MySQL();

		$this->assertFalse($fixture->init('-unexistent-', 'user', 'password', 'test'));
		preg_match('/mysql:\/\/(.*):(.*)@(.*)\/(.*)(\?charset=(.*))/', $GLOBALS['TESTCONF']['DB']['dsn'], $m);
		if (!defined('LOCALE_CHARSET'))
			define('LOCALE_CHARSET', $m[6]);

		$this->assertTrue($fixture->init($m[3], $m[1], $m[2], $m[4]));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::escape
	 */
	public function testEscape()
	{
		$fixture = new MySQL();
		$this->assertEquals('test', $fixture->escape('test'));
		$this->assertEquals(array('a' => 'test'), $fixture->escape(array('a' => 'test')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::fields
	 */
	public function testFields()
	{
		$fixture = new MySQL();
		$fields = array('access', 'active', 'hash', 'id', 'lastLoginTime', 'lastVisit',
			'login', 'loginErrors', 'mail', 'name', 'profile');

		$this->assertEquals($fields, $fixture->fields('users'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::select
	 */
	public function testSelect()
	{
		$fixture = new MySQL();
		$items = $fixture->select('users', "active = 1", '-access', 'id,access', 2, 1, '', true);
		$this->assertEquals(2, count($items));
		$this->assertEquals(1, $items[1]['access']);
		$this->assertFalse(isset($items[0]['login']));

		$items = $fixture->select('users', "active = 1", '+access,lastVisit', 'id,access', 2);
		$this->assertEquals(2, count($items));
		$this->assertEquals(2, $items[1]['access']);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка метода MySQL::selectItem
	 */
	public function testSelectItem()
	{
		$fixture = new MySQL();
		$item = $fixture->selectItem('users', "login = 'root'", 'id,access');
		$this->assertEquals(2, count($item));
		$this->assertEquals(1, $item['access']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка выборки методом MySQL::selectItem несуществующего элемента
	 */
	public function testSelectItemFail()
	{
		$fixture = new MySQL();
		$item = $fixture->selectItem('users', "login = '-nobody-'");
		$this->assertFalse($item);
	}
	//-----------------------------------------------------------------------------

	/**/
}
