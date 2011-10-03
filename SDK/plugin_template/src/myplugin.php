<?php
/**
 * [������� �������� �������]
 *
 * [�������� ������� (��������� ��������� �����)]
 *
 * @version ${product.version}
 *
 * @copyright [���], [��������], [�����, ���� �����]
 * @license http://www.gnu.org/licenses/gpl.txt	GPL License 3
 * @author [�����1 <E-mail ������1>]
 * @author [�����N <E-mail ������N>]
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
 * @package [��� ������]
 *
 * $Id$
 */

/**
 * �������� ����� �������
 *
 * @package [��� ������]
 */
class MyPlugin extends Plugin
{
	/**
	 * ������ �������
	 * @var string
	 */
	public $version = '${product.version}';

	/**
	 * ��������� ������ ����
	 * @var string
	 */
	public $kernel = '2.xx';

	/**
	 * �������� �������
	 * @var string
	 */
	public $title = '��������';

	/**
	 * ������� �������
	 * @var string
	 */
	public $description = '��������';

	/**
	 * ��������� �������
	 *
	 * @var array
	 */
	public $settings = array(
	);

	/**
	 * �����������
	 *
	 * @return MyPlugin
	 *
	 * @since 1.00
	 */
	public function __construct()
	{
		parent::__construct();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������ �������� �������
	 *
	 * @return string  ����� ��������
	 */
	public function settings()
	{
		$form = array(
			'name' => 'SettingsForm',
			'caption' => $this->title . ' ' . $this->version,
			'width' => '500px',
			'fields' => array (
				array('type' => 'hidden', 'name' => 'update', 'value' => $this->name),
				// ����������� ���� �����
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);
		$html = $GLOBALS['page']->renderForm($form, $this->settings);
		return $html;
	}
	//-----------------------------------------------------------------------------
}
