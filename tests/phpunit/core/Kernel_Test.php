<?php
/**
 * ${product.title} ${product.version}
 *
 * Модульные тесты
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 *
 * $Id$
 */
require_once 'PHPUnit/Extensions/OutputTestCase.php';

require_once dirname(__FILE__) . '/../stubs.php';
require_once dirname(__FILE__) . '/../../../main/core/Kernel.php';

/**
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Kernel_Test extends PHPUnit_Framework_TestCase
{
	/**
	 * @var mixed
	 */
	private $error_log;

	/**
	 * @var string
	 */
	private $inclue_path;

	/**
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	protected function setUp()
	{
		$this->inclue_path = get_include_path();
		$this->error_log = ini_get('error_log');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @see PHPUnit_Framework_TestCase::tearDown()
	 */
	protected function tearDown()
	{
		ini_set('error_log', $this->error_log);
		set_include_path($this->inclue_path);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::initExceptionHandling
	 */
	public function test_initExceptionHandling()
	{
		if (version_compare(PHP_VERSION, '5.3.2', '<'))
		{
			$this->markTestSkipped('This test requires at PHP 5.3.2 or higher');
		}
		$method = new ReflectionMethod('Eresus_Kernel', 'initExceptionHandling');
		$method->setAccessible(true);
		$method->invoke(null);

		$this->assertTrue(isset($GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER']), 'No emergency buffer');
		$this->assertEquals(0, ini_get('html_errors'), '"html_errors" option is set');

	}
	//-----------------------------------------------------------------------------

	/**
	 * @ covers Eresus_Kernel::handleException
	 * /
	public function test_handleException()
	{
		$e = new Exception;
		ini_set('error_log', '/dev/null');
		$this->expectOutputString("Unhandled Exception!\n");
		Eresus_Kernel::handleException($e);
	}
	//-----------------------------------------------------------------------------

	/**
	 */
	public function test_errorHandler_at()
	{
		@Eresus_Kernel::errorHandler(E_ERROR, 'Error', '/some/file', 123);
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 */
	public function test_errorHandler_NOTICE()
	{
		Eresus_Kernel::errorHandler(E_NOTICE, 'Notice', '/some/file', 123);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @expectedException ErrorException
	 */
	public function test_errorHandler_WARNING()
	{
		Eresus_Kernel::errorHandler(E_WARNING, 'Warning', '/some/file', 123);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @expectedException ErrorException
	 */
	public function test_errorHandler_ERROR()
	{
		Eresus_Kernel::errorHandler(E_ERROR, 'Error', '/some/file', 123);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 */
	public function testExecOk()
	{
		$this->assertEquals(123, Eresus_Kernel::exec('Eresus_Kernel_Test_Application1'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 * @expectedException LogicException
	 */
	public function testExecInvalidClass()
	{
		Eresus_Kernel::exec('StdClass');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 * @expectedException LogicException
	 */
	public function testExecUnexistentClass()
	{
		Eresus_Kernel::exec('UnexistentClass');
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 */
	public function testExecAppWithException()
	{
		$this->assertEquals(0xFFFF, Eresus_Kernel::exec('Eresus_Kernel_Test_Application2'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::exec
	 */
	public function test_ExecAppWith_with_SuccessException()
	{
		$this->assertEquals(0, Eresus_Kernel::exec('Eresus_Kernel_Test_Application3'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel::isCGI
	 */
	public function test_lint_isCGI()
	{
		Eresus_Kernel::isCGI();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel::isCLI
	 */
	public function test_lint_isCLI()
	{
		Eresus_Kernel::isCLI();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Just make sure that method can be executed
	 *
	 * @covers Eresus_Kernel::isModule
	 */
	public function test_lint_isModule()
	{
		Eresus_Kernel::isModule();
		$this->assertTrue(true);
	}
	//-----------------------------------------------------------------------------

	/**
	 * @covers Eresus_Kernel::classExists
	 */
	public function test_classExists()
	{
		$this->assertFalse(Eresus_Kernel::classExists('UnexistentClass'));
		$this->assertTrue(Eresus_Kernel::classExists('Eresus_Kernel_Test_Class'));
		$this->assertTrue(Eresus_Kernel::classExists('Eresus_Kernel_Test_Interface'));
	}
	//-----------------------------------------------------------------------------

	/* */
}


// @codeCoverageIgnoreStart
/**
 * EresusApplication stub
 */
class Eresus_Kernel_Test_Application1
{
	/**
	 * (non-PHPdoc)
	 * @see core/EresusApplication#main()
	 */
	public function main()
	{
		return 123;
	}
	//-----------------------------------------------------------------------------
}

/**
 * EresusApplication stub
 *
 */
class Eresus_Kernel_Test_Application2
{
	/**
	 * (non-PHPdoc)
	 * @see core/EresusApplication#main()
	 */
	public function main()
	{
		throw new RuntimeException('Message');
	}
	//-----------------------------------------------------------------------------
}

/**
 * EresusApplication stub
 *
 */
class Eresus_Kernel_Test_Application3
{
	/**
	 * (non-PHPdoc)
	 * @see core/EresusApplication#main()
	 */
	public function main()
	{
		throw new Eresus_ExitException;
	}
	//-----------------------------------------------------------------------------
}

/**
 * Autoloader stub
 * @param string $class
 */
function Eresus_Kernel_Test_autoloader($class)
{

}
//-----------------------------------------------------------------------------

/**
 *
 */
function Eresus_Kernel_Test_error_handler()
{

}
//-----------------------------------------------------------------------------
 /* */

interface Eresus_Kernel_Test_Interface {};
class Eresus_Kernel_Test_Class {};

// @codeCoverageIgnoreEnd

