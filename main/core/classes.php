<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @package BusinessLogic
 *
 * $Id$
 */


/**
 * ������ ��������
 *
 * ������ ��� �������� �������������� ������ � ����� �������.
 *
 * @package Core
 */
class EresusSourceParseException extends RuntimeException {};


/**
 * ����� ��� ������ � ������������ �������
 *
 * @package CoreExtensionsAPI
 */
class EresusExtensions
{
 /**
	* ����������� ����������
	*
	* @var array
	*/
	var $items = array();
 /**
	* ����������� ����� ����������
	*
	* @param string $class     ����� ����������
	* @param string $function  ����������� �������
	* @param string $name      ��� ����������
	*
	* @return mixed  ��� ���������� ��� false ���� ����������� ���������� �� �������
	*/
	function get_name($class, $function, $name = null)
	{
		global $Eresus;

		$result = false;
		if (isset($Eresus->conf['extensions'])) {
			if (isset($Eresus->conf['extensions'][$class])) {
				if (isset($Eresus->conf['extensions'][$class][$function])) {
					$items = $Eresus->conf['extensions'][$class][$function];
					reset($items);
					$result = isset($items[$name]) ? $name : key($items);
				}
			}
		}

		return $result;
	}
	//-----------------------------------------------------------------------------
 /**
	* �������� ����������
	*
	* @param string $class     ����� ����������
	* @param string $function  ����������� �������
	* @param string $name      ��� ����������
	*
	* @return mixed  ��������� ������ EresusExtensionConnector ��� false ���� �� ������� ��������� ����������
	*/
	function load($class, $function, $name = null)
	{
		global $Eresus;

		$result = false;
		$name = $this->get_name($class, $function, $name);

		if (isset($this->items[$name]))
		{
			$result = $this->items[$name];
		}
			else
		{
			$filename = $Eresus->froot.'ext-3rd/'.$name.'/eresus-connector.php';
			if (is_file($filename)) {
				include_once $filename;
				$class = $name.'Connector';
				if (class_exists($class)) {
					$this->items[$name] = new $class();
					$result = $this->items[$name];
				}
			}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}
