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

require_once dirname(__FILE__) . '/../../stubs.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS.php';
require_once dirname(__FILE__) . '/../../../../main/core/CMS/Service.php';
require_once dirname(__FILE__) . '/../../../../main/core/DB/ORM.php';
require_once dirname(__FILE__) . '/../../../../main/core/DB/Record.php';
require_once dirname(__FILE__) . '/../../../../main/core/Model/User.php';
require_once dirname(__FILE__) . '/../../../../main/core/Security/AuthService.php';

/**
 * @package EresusCMS
 * @subpackage Tests
 */
class Eresus_Security_AuthService_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers Eresus_Security_AuthService::getInstance
	 */
	public function test_interface()
	{
		$test = Eresus_Security_AuthService::getInstance();
		$this->assertInstanceOf('Eresus_CMS_Service', $test);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::getUser
	 */
	public function test_getUser()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$test = Eresus_Security_AuthService::getInstance();

		$userProperty = new ReflectionProperty('Eresus_Security_AuthService', 'user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($test, 123);
		$this->assertEquals(123, $test->getUser());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::login
	 */
	public function test_login_UNKNOWN_USER()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->returnValue(array()));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::UNKNOWN_USER, $test->login('noexistent_user', 'pass'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::login
	 */
	public function test_login_ACCOUNT_DISABLED()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$user = new stdClass();
		$user->active = false;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::ACCOUNT_DISABLED, $test->login('disabled_user', 'pass'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::login
	 */
	public function test_login_BRUTEFORCING()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$user = new stdClass();
		$user->active = true;
		$user->lastLoginTime = time()-50;
		$user->loginErrors = 100;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::BRUTEFORCING, $test->login('user', 'pass'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::login
	 */
	public function test_login_BAD_PASSWORD()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$user = new stdClass;
		$user->active = true;
		$user->password = '';
		$user->lastLoginTime = time() - 1000;
		$user->loginErrors = 0;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::BAD_PASSWORD, $test->login('user', 'bad_pass'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::login
	 */
	public function test_login_SUCCESS()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$test = Eresus_Security_AuthService::getInstance();

		$userProperty = new ReflectionProperty('Eresus_Security_AuthService', 'user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($test, null);

		$user = $this->getMock('stdClass', array('save'));
		$user->expects($this->once())->method('save');
		$user->id = 123;
		$user->password = Eresus_Model_User::passwordHash('pass');
		$user->active = true;
		$user->lastLoginTime = time() - 1000;
		$user->loginErrors = 0;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::SUCCESS, $test->login('user', 'pass'));
		$this->assertSame($user, $userProperty->getValue($test));
		$this->assertEquals(123, $_SESSION['user']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::loginByHash
	 */
	public function test_loginByHash_UNKNOWN_USER()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->returnValue(array()));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::UNKNOWN_USER, $test->loginByHash('noexistent_user', 'hash'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::loginByHash
	 */
	public function test_loginByHash_ACCOUNT_DISABLED()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$user = new stdClass();
		$user->active = false;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::ACCOUNT_DISABLED, $test->loginByHash('disabled_user', 'hash'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::loginByHash
	 */
	public function test_loginByHash_BRUTEFORCING()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$user = new stdClass();
		$user->active = true;
		$user->lastLoginTime = time()-50;
		$user->loginErrors = 100;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::BRUTEFORCING, $test->loginByHash('user', 'hash'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::loginByHash
	 */
	public function test_loginByHash_BAD_PASSWORD()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$user = new stdClass;
		$user->active = true;
		$user->password = '';
		$user->lastLoginTime = time() - 1000;
		$user->loginErrors = 0;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::BAD_PASSWORD, $test->loginByHash('user', 'bad_hash'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::loginByHash
	 */
	public function test_loginByHash_SUCCESS()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$test = Eresus_Security_AuthService::getInstance();

		$userProperty = new ReflectionProperty('Eresus_Security_AuthService', 'user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($test, null);

		$user = $this->getMock('stdClass', array('save'));
		$user->expects($this->once())->method('save');
		$user->id = 123;
		$user->password = 'hash';
		$user->active = true;
		$user->lastLoginTime = time() - 1000;
		$user->loginErrors = 0;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$this->assertEquals(Eresus_Security_AuthService::SUCCESS, $test->loginByHash('user', 'hash'));
		$this->assertSame($user, $userProperty->getValue($test));
		$this->assertEquals(123, $_SESSION['user']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::loginByHash
	 * @expectedException DomainException
	 */
	public function test_loginByHash_saveError()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$test = Eresus_Security_AuthService::getInstance();

		$userProperty = new ReflectionProperty('Eresus_Security_AuthService', 'user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($test, null);

		$user = $this->getMock('stdClass', array('save'));
		$user->expects($this->once())->method('save')->will($this->
			throwException(new DomainException));
		$user->id = 123;
		$user->password = 'hash';
		$user->active = true;
		$user->lastLoginTime = time() - 1000;
		$user->loginErrors = 0;

		$table = $this->getMock('stdClass', array('findByUsername'));
		$table->expects($this->once())->method('findByUsername')->will($this->
			returnValue(array($user)));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$test->loginByHash('user', 'hash');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::logout
	 */
	public function test_logout()
	{
		if (version_compare(PHP_VERSION, '5.3', '<'))
		{
			$this->markTestSkipped('PHP 5.3 required');
		}

		$test = Eresus_Security_AuthService::getInstance();

		$userProperty = new ReflectionProperty('Eresus_Security_AuthService', 'user');
		$userProperty->setAccessible(true);
		$userProperty->setValue($test, true);

		$_SESSION['user'] = 123;

		$test->logout();

		$this->assertNull($userProperty->getValue($test));
		$this->assertNotContains('user', $_SESSION);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::init
	 */
	public function test_init_fromSession()
	{
		$test = Eresus_Security_AuthService::getInstance();

		$table = $this->getMock('stdClass', array('find'));
		$table->expects($this->once())->method('find')->will($this->returnArgument(0));

		$core = $this->getMock('stdClass', array('getTable'));
		$core->expects($this->once())->method('getTable')->will($this->returnValue($table));
		Doctrine_Core::setMock($core);

		$_SESSION['user'] = 123;

		$test->init();

		$this->assertEquals(123, $test->getUser());
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Security_AuthService::init
	 */
	public function test_init_fromCookies()
	{
		$test = $this->getMockBuilder('Eresus_Security_AuthService')->setMethods(array('loginByHash'))
			->disableOriginalConstructor()->getMock();
		$test->expects($this->once())->method('loginByHash')->
			with('root', '74be16979710d4c4e7c6647856088456');

		$_COOKIE['eresus_auth'] = 'a:2:{s:1:"u";s:4:"root";s:1:"h";s:32:"74be16979710d4c4e7c6647856088456";}';

		$test->init();
	}
	//-----------------------------------------------------------------------------

	/* */
}
