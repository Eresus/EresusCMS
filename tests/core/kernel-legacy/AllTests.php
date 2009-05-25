<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * ��������� ����� ���� �������
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

#require_once 'ArgTest.php';
#require_once 'EresusInitTest.php';
#require_once 'EresusTest.php';

require_once 'LegacyTest.php';

class Core_Kernel_AllTests
{
	public static function suite()
	{
		$suite = new PHPUnit_Framework_TestSuite('Kernel Tests');

		#$suite->addTestSuite('ArgTest');
		#$suite->addTestSuite('EresusInitTest');
		#$suite->addTestSuite('EresusTest');

		$suite->addTestSuite('LegacyTest');

		return $suite;
	}
}
