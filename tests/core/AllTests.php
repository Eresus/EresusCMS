<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * ��������� ����� ������� ����� �������
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-${build.year}, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 *
 * @package EresusCMS
 * @subpackage Tests
 *
 * $Id$
 */

require_once 'PHPUnit/Framework.php';

require_once 'kernel/AllTests.php';
/*require_once 'lib/AllTests.php';
require_once 'classes_php/AllTests.php';
require_once 'client_php/AllTests.php';
*/

class Core_AllTests
{
	/**
	 *
	 */
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Core Tests');

		$suite->addTest(Core_Kernel_AllTests::suite());
		/*$suite->addTest(Core_Lib_AllTests::suite());
		$suite->addTest(Core_Classes_php_AllTests::suite());
		$suite->addTest(Core_Client_php_AllTests::suite());*/

		return $suite;
	}
}
