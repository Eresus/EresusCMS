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
 * @package Eresus2
 *
 * $Id$
 */


/**
* ������������ ����� ��� ���� ��������
*
* @var  string  $name         ��� �������
* @var  string  $version	    ������ �������
* @var  string  $kernel       ����������� ������ Eresus
* @var  string  $title        �������� �������
* @var  string  $description	�������� �������
* @var  string  $type         ��� �������, ������������ ����� ������� �������� �����:
*                               client   - ��������� ������ � ��
*                               admin    - ��������� ������ � ��
*                               content  - ������ ������������� ��� ��������
*                               ondemand - �� ��������� ������ �������������
* @var  array   $settings     ��������� �������
*
* @package Eresus2
* @deprecated ����������� Plugin
*/
class TPlugin
{
	public $name;
	public $version;
	public $title;
	public $description;

	/**
	 * �� ������������ ������� � 2.13
	 * @var void
	 */
	public $type;
	public $settings = array();

	/**
	 * �����������
	 *
	 * ���������� ������ �������� ������� � ����������� �������� ������
	 */
	public function __construct()
	{
		global $Eresus, $locale;

		if (!empty($this->name) && isset($Eresus->plugins->list[$this->name])) {
			$this->settings = decodeOptions($Eresus->plugins->list[$this->name]['settings'], $this->settings);
			# ���� ����������� ������ ������� �������� �� ������������� �����
			# �� ���������� ���������� ���������� ���������� � ������� � ��
			if ($this->version != $Eresus->plugins->list[$this->name]['version']) $this->resetPlugin();
		}
		$filename = filesRoot.'lang/'.$this->name.'/'.$locale['lang'].'.php';
		if (is_file($filename)) include_once($filename);
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ���������� � �������
	 *
	 * @param  array  $item  ���������� ������ ���������� (�� ��������� null)
	 *
	 * @return  array  ������ ����������, ��������� ��� ������ � ��
	 */
	function __item($item = null)
	{
		global $Eresus;

		$result['name'] = $this->name;
		$result['content'] = preg_match('/ContentPlugin$/i', get_class($this)) > 0;
		$result['active'] = is_null($item) ? true : $item['active'];
		$result['position'] = is_null($item) ? $Eresus->db->count('plugins') : $item['position'];
		$result['settings'] = $Eresus->db->escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
		$result['title'] = $this->title;
		$result['version'] = $this->version;
		$result['description'] = $this->description;
		return $result;
	}
	//------------------------------------------------------------------------------
# �������� �������������
function createPluginItem($item = null) {return $this->__item($item);}
//------------------------------------------------------------------------------
/**
* ������ �������� ������� �� ��
*
* @return  bool  ��������� ����������
*/
function loadSettings()
{
	global $Eresus;
	$result = $Eresus->db->selectItem('plugins', "`name`='".$this->name."'");
	if ($result) $this->settings = decodeOptions($result['settings'], $this->settings);
	return (bool)$result;
}
//------------------------------------------------------------------------------
/**
* ���������� �������� ������� � ��
*
* @return  bool  ��������� ����������
*/
function saveSettings()
{
	global $Eresus;
	$item = $Eresus->db->selectItem('plugins', "`name`='{$this->name}'");
	$item = $this->__item($item);
	$item['settings'] = $Eresus->db->escape(encodeOptions($this->settings));
	$result = $Eresus->db->updateItem('plugins', $item, "`name`='".$this->name."'");
	return $result;
}
//------------------------------------------------------------------------------
/**
* ���������� ������ � ������� � ��
*/
function resetPlugin()
{
	$this->loadSettings();
	$this->saveSettings();
}
//------------------------------------------------------------------------------
/**
* ��������, ����������� ��� ����������� �������
*/
function install() {}
//------------------------------------------------------------------------------
/**
* ��������, ����������� ��� ������������� �������
*/
function uninstall() {}
//------------------------------------------------------------------------------
/**
* �������� ��� ��������� ��������
*/
function onSettingsUpdate() {}
//------------------------------------------------------------------------------
/**
* ��������� � �� ��������� �������� �������
*/
function updateSettings()
{
	global $Eresus;

	foreach ($this->settings as $key => $value) if (!is_null(arg($key))) $this->settings[$key] = arg($key);
	$this->onSettingsUpdate();
	$this->saveSettings();
}
//------------------------------------------------------------------------------
/**
* ������ ��������
*
* @param  string  $template  ������ � ������� ��������� �������� ������ ��������
* @param  arrya   $item      ������������� ������ �� ���������� ��� ����������� ������ ��������
*
* @return  string  ����� ���������� ������, � ������� �������� ��� �������, ����������� � ������ ������� item
*/
function replaceMacros($template, $item)
{
	preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
	if (count($matches[1])) foreach($matches[1] as $macros)
		if (isset($item[$macros])) $template = str_replace('$('.$macros.')', $item[$macros], $template);
	return $template;
}
//------------------------------------------------------------------------------
}

?>