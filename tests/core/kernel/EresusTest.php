<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * ��������� ����� ������ Eresus
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

/**
 * Test Eresus::init method
 */
require_once 'PHPUnit/Framework.php';

class EresusTest extends PHPUnit_Framework_TestCase {

	function testInit()
	{
		global $Eresus;

		load_server('Apache', 'basic');
		$this->assertNull($Eresus->init());
	}
	//-----------------------------------------------------------------------------

	/**/
}
