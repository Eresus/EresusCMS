<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * ���������� ��
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
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
 *
 * $Id$
 */


ini_set('display_errors', true);


/**
 * ����� ��� ������ ��������
 *
 * @package EresusCMS
 * @since 2.16
 */
class Core
{
	/**
	 * ��������
	 *
	 * @var array
	 */
	public static $values;

	/**
	 * �������� �������� � ���������� 'eresus.cms.dsn' � $dsn.
	 *
	 * @param string $key
	 * @param mixed $value
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public static function setValue($key, $value)
	{
		self::$values[$key] = $value;
	}
	//-----------------------------------------------------------------------------
}


$root = dirname(__FILE__) . '/../..';

$conf = $root . '/cfg/main.php';

if (!file_exists($conf))
{
	fputs(STDERR, "Configuration file '$conf' not found!\n");
	exit(-1);
}

/**
 * ����������� ��������
 */
include $conf;

try
{
	/**
	 * ����������� Doctrine
	 */
	include_once $root . '/core/Doctrine.php';
	spl_autoload_register(array('Doctrine', 'autoload'));
	spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));
	require_once $root . '/core/classes/EresusActiveRecord.php';

	Doctrine_Manager::connection(Core::$values['eresus.cms.dsn'], 'doctrine')->
		setCharset('cp1251'); // TODO ������ ����� �������� �� UTF

	$manager = Doctrine_Manager::getInstance();
	$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
	$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);

	$prefix = Core::$values['eresus.cms.dsn.prefix'];
	if ($prefix)
	{
		$manager->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT, $prefix . '%s');
	}

	Doctrine_Core::createTablesFromModels($root . '/core/models');

	$user = new User();
	$user->login = 'root';
	$user->hash = md5(md5(''));
	$user->active = true;
	$user->access = 1;
	$user->name = '������� �������������';
	$user->mail = 'root@example.org';
	$user->save();

	$section = new Section();
	$section->name = 'main';
	$section->owner = 0;
	$section->title = '������� ��������';
	$section->caption = '�������';
	$section->active = true;
	$section->access = 5;
	$section->visible = true;
	$section->template = 'default';
	$section->type = 'default';
	$section->content = '<h1>Eresus CMS</h1>';
	$section->save();
}
catch (Exception $e)
{
	fputs(STDERR, get_class($e) .': ' . $e->getMessage() . ' in ' . $e->getFile() . ' on ' .
		$e->getLine() . "\n" . $e->getTraceAsString());
	exit(-1);
}
