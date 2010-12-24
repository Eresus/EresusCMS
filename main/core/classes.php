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
 * @package EresusCMS
 *
 * $Id$
 */


/**
 * ������ ��������
 *
 * ������ ��� �������� �������������� ������ � ����� �������.
 *
 * @package EresusCMS
 */
class EresusSourceParseException extends RuntimeException {};


/**
 * ������ � ���������
 *
 * @package EresusCMS
 */
class Plugins
{
	var $list = array(); # ������ ���� ��������
	var $items = array(); # ������ ��������
	var $events = array(); # ������� ������������ �������

	/**
	 * �����������
	 */
	function __construct()
	{
		//global $Eresus;

		$items = ORM::getTable('PluginInfo')->findAll();
		//$items = $Eresus->db->select('`plugins`');
		if (count($items))
		{
			foreach($items as $item)
			{
				$this->list[$item->name] = $item;
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������������� ������
	 *
	 * @param string $name  ��� �������
	 *
	 * @return void
	 *
	 * @throws EresusSourceParseException
	 */
	public function install($name)
	{
		global $Eresus;

		EresusLogger::log(__METHOD__, LOG_DEBUG, '("%s")', $name);

		$filename = Core::app()->getFsRoot() . '/ext/'.$name.'.php';
		if (is_file($filename))
		{
			/*
			 * ���������� ������ ����� eval ����� ��������� � ���������� ��������� ��������������
			 * ������. ���� � �� ����, ��� ��� �� ���������.
			 */
			$code = file_get_contents($filename);
			$code = preg_replace('/^\s*<\?php|\?>\s*$/m', '', $code);
			$code = str_replace('__FILE__', "'$filename'", $code);
			@$valid = eval($code) !== false;
			if (!$valid)
			{
				throw new EresusSourceParseException(
					sprintf('Parsing error in file "%s"', $filename),
					sprintf('Plugin "%s" is broken (parse error)', $name)
				);
			}

			$ClassName = $name;
			if (!class_exists($ClassName, false) && class_exists('T'.$ClassName, false))
				$ClassName = 'T'.$ClassName; # FIXME: �������� ������������� � �������� �� 2.10b2
			if (class_exists($ClassName, false))
			{
				$this->items[$name] = new $ClassName();
				$this->items[$name]->install();
				$Eresus->db->insert('plugins', $this->items[$name]->__item());
			}
				else FatalError(sprintf(errClassNotFound, $ClassName));
		}
		else
		{
			EresusLogger::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
				$name);
			$msg = I18n::getInstance()->getText('Can not find main file "%s" for plugin "%s"', __CLASS__);
			$msg = sprintf($msg, $filename, $name);
			ErrorMessage($msg);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������������� ������
	 *
	 * @param string $name  ��� �������
	 */
	public function uninstall($name)
	{
		global $Eresus;

		if (!isset($this->items[$name]))
		{
			$this->load($name);
		}
		if (isset($this->items[$name]))
		{
			$this->items[$name]->uninstall();
		}
		$pluginInfo = ORM::getTable('PluginInfo')->find($name);
		if ($pluginInfo)
		{
			$pluginInfo->delete();
		}
		$filename = Core::app()->getFsRoot() . '/ext/'.$name.'.php';
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * ���������� ��������������� �������� ��������
	 *
	 * @param array $include [deprecated]
	 * @param array $exclude [deprecated]
	 * @return void
	 */
	function preload($include = null, $exclude = null)
	{
		EresusLogger::log(__METHOD__, LOG_DEBUG, '()');

		if (!is_null($exclude))
		{
			EresusLogger::log(__METHOD__, LOG_NOTICE, '$exclude argument is deprecated');
		}

		if (!is_null($include))
		{
			EresusLogger::log(__METHOD__, LOG_NOTICE, '$include argument is deprecated');
		}

		if (count($this->list))
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'Preloading plugins...');
			foreach($this->list as $item)
			{
				if ($item['active'])
				{
					$this->load($item['name']);
				}
			}
		}
		else
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'Nothing to preload');
		}
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * ��������� ������ � ���������� ��� ���������
	 *
	 * @param string $name  ��� �������
	 * @return Plugin|TPlugin|false  ��������� ������� ��� FASLE ���� �� ������� ��������� ������
	 */
	public function load($name)
	{
		EresusLogger::log(__METHOD__, LOG_DEBUG, '("%s")', $name);
		/* ���� ������ ��� ��� �������� ���������� ��������� �� ������� */
		if (isset($this->items[$name]))
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" already loaded', $name);
			return $this->items[$name];
		}

		/* ���� ����� ������ �� ���������������, ���������� FASLE */
		if (!isset($this->list[$name]))
		{
			EresusLogger::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" not registered', $name);
			return false;
		}

		// ���� � ����� �������
		$filename = Core::app()->getFsRoot() . '/ext/' . $name . '.php';

		/* ���� ������ ����� ���, ���������� FASLE */
		if (!file_exists($filename))
		{
			EresusLogger::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"', $filename,
				$name);
			return false;
		}

		include $filename;
		$className = $name;

		/* TODO: �������� ������������� � �������� �� 2.10b2. ���������� � ����� ������� */
		if (!class_exists($className, false) && class_exists('T' . $className))
		{
			$className = 'T' . $className;
		}

		if (!class_exists($className, false))
		{
			EresusLogger::log(__METHOD__, LOG_ERR, 'Main class %s for plugin "%s" not found in "%s"',
				$className, $name, $filename);
			FatalError(sprintf(errClassNotFound, $name));
		}

		// ������� ��������� � ������
		$this->items[$name] = new $className();
		EresusLogger::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" loaded', $name);

		return $this->items[$name];
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� �������� �������
	 *
	 * @return stirng  �������
	 */
	function clientRenderContent()
	{
		global $Eresus, $page;

		$result = '';
		switch ($page->type)
		{

			case 'default':
				$plugin = new ContentPlugin;
				$result = $plugin->clientRenderContent();
			break;

			case 'list':
				/* ���� � URL ������� ���-���� ����� ������ �������, ���������� ����� 404 */
				if ($Eresus->request['file'] || $Eresus->request['query'] || $page->subpage || $page->topic)
					$page->httpError(404);

				$subitems = $Eresus->db->select('pages', "(`owner`='".$page->id."') AND (`active`='1') " .
					"AND (`access` >= '".($_SESSION['user_auth'] ? $Eresus->user['access'] : GUEST)."')", "`position`");
				if (empty($page->content)) $page->content = '$(items)';
				$template = loadTemplate('std/SectionListItem');
				if ($template === false) $template['html'] = '<h1><a href="$(link)" title="$(hint)">$(caption)</a></h1>$(description)';
				$items = '';
				foreach($subitems as $item) {
					$items .= str_replace(
						array(
							'$(id)',
							'$(name)',
							'$(title)',
							'$(caption)',
							'$(description)',
							'$(hint)',
							'$(link)',
						),
						array(
							$item['id'],
							$item['name'],
							$item['title'],
							$item['caption'],
							$item['description'],
							$item['hint'],
							$Eresus->request['url'].($page->name == 'main' && !$page->owner ? 'main/' : '').$item['name'].'/',
						),
						$template['html']
					);
					$result = str_replace('$(items)', $items, $page->content);
				}
			break;
			case 'url':
				HttpResponse::redirect($page->replaceMacros($page->content));
			break;
			default:
			if ($this->load($page->type)) {
				if (method_exists($this->items[$page->type], 'clientRenderContent'))
					$result = $this->items[$page->type]->clientRenderContent();
				else ErrorMessage(sprintf(errMethodNotFound, 'clientRenderContent', get_class($this->items[$page->type])));
			} else ErrorMessage(sprintf(errContentPluginNotFound, $page->type));
		}
		return $result;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnStart()
	{
		if (isset($this->events['clientOnStart'])) foreach($this->events['clientOnStart'] as $plugin) $this->items[$plugin]->clientOnStart();
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnURLSplit($item, $url)
	{
		if (isset($this->events['clientOnURLSplit'])) foreach($this->events['clientOnURLSplit'] as $plugin) $this->items[$plugin]->clientOnURLSplit($item, $url);
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnTopicRender($text, $topic = null, $buttonBack = true)
	{
	global $page;
		if (isset($this->events['clientOnTopicRender'])) foreach($this->events['clientOnTopicRender'] as $plugin) $text = $this->items[$plugin]->clientOnTopicRender($text, $topic);
		if ($buttonBack) $text .= '<br /><br />'.$page->buttonBack();
		return $text;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnContentRender($text)
	{
		if (isset($this->events['clientOnContentRender']))
			foreach($this->events['clientOnContentRender'] as $plugin) $text = $this->items[$plugin]->clientOnContentRender($text);
		return $text;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientOnPageRender($text)
	{
		if (isset($this->events['clientOnPageRender']))
			foreach($this->events['clientOnPageRender'] as $plugin) $text = $this->items[$plugin]->clientOnPageRender($text);
		return $text;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function clientBeforeSend($text)
	{
		if (isset($this->events['clientBeforeSend']))
			foreach($this->events['clientBeforeSend'] as $plugin) $text = $this->items[$plugin]->clientBeforeSend($text);
		return $text;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	/* function clientOnFormControlRender($formName, $control, $text)
	{
		if (isset($this->events['clientOnFormControlRender'])) foreach($this->events['clientOnFormControlRender'] as $plugin) $text = $this->items[$plugin]->clientOnFormControlRender($formName, $control, $text);
		return $text;
	}*/
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function adminOnMenuRender()
	{
		if (isset($this->events['adminOnMenuRender'])) foreach($this->events['adminOnMenuRender'] as $plugin)
			if (method_exists($this->items[$plugin], 'adminOnMenuRender')) $this->items[$plugin]->adminOnMenuRender();
			else ErrorMessage(sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
 /**
	* ������� ajaxOnRequest
	*/
	function ajaxOnRequest()
	{
		if (isset($this->events['ajaxOnRequest']))
			foreach($this->events['ajaxOnRequest'] as $plugin)
				$this->items[$plugin]->ajaxOnRequest();
	}
	//-----------------------------------------------------------------------------
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

/* * * * * * * * * * * * * * * * * * * * * * * *
*
*     ������-������ ��� �������� ��������
*
* * * * * * * * * * * * * * * * * * * * * * * */

/**
 * ������������ ����� ��� ���� ��������
 *
 * @package EresusCMS
 */
class Plugin
{
	/**
	 * ��� �������
	 *
	 * @var string
	 */
	public $name;

	/**
	 * ������ �������
	 *
	 * ������� ������ ����������� ��� ����� ���������
	 *
	 * @var string
	 */
	public $version = '0.00';

	/**
	 * ����������� ������ Eresus
	 *
	 * ������� ����� ����������� ��� ����� ���������
	 *
	 * @var string
	 */
	public $kernel = '2.10b2';

	/**
	 * �������� �������
	 *
	 * ������� ������ ����������� ��� ����� ���������
	 *
	 * @var string
	 */
	public $title = 'no title';

	/**
	 * �������� �������
	 *
	 * ������� ������ ����������� ��� ����� ���������
	 *
	 * @var string
	 */
	public $description = '';

	/**
	 * ��������� �������
	 *
	 * ������� ����� ����������� ��� ����� ���������
	 *
	 * @var array
	 */
	public $settings = array();

	/**
	 * ���������� ������
	 *
	 * /data/���_�������
	 *
	 * @var string
	 */
	protected $dirData;

	/**
	 * URL ������
	 *
	 * @var string
	 */
	protected $urlData;

	/**
	 * ���������� ��������
	 *
	 * /ext/���_�������
	 *
	 * @var string
	 */
	protected $dirCode;

	/**
	 * URL ��������
	 *
	 * @var string
	 */
	protected $urlCode;

	/**
	 * ���������� ����������
	 *
	 * style/���_�������
	 *
	 * @var string
	 */
	protected $dirStyle;

	/**
	 * URL ����������
	 *
	 * @var string
	 */
	protected $urlStyle;

	/**
	 * �����������
	 *
	 * ���������� ������ �������� ������� � ����������� �������� ������
	 *
	 * @uses $Eresus
	 * @uses $locale
	 * @uses Plugin::resetPlugin
	 */
	public function __construct()
	{
		global $Eresus, $locale;

		$this->name = strtolower(get_class($this));
		if (!empty($this->name) && isset($Eresus->plugins->list[$this->name]))
		{
			$this->settings = decodeOptions($Eresus->plugins->list[$this->name]['settings'], $this->settings);
			# ���� ����������� ������ ������� �������� �� ������������� �����
			# �� ���������� ���������� ���������� ���������� � ������� � ��
			if ($this->version != $Eresus->plugins->list[$this->name]['version'])
				$this->resetPlugin();
		}
		$this->dirData = $Eresus->fdata.$this->name.'/';
		$this->urlData = $Eresus->data.$this->name.'/';
		$this->dirCode = $Eresus->froot.'ext/'.$this->name.'/';
		$this->urlCode = $Eresus->root.'ext/'.$this->name.'/';
		$this->dirStyle = $Eresus->fstyle.$this->name.'/';
		$this->urlStyle = $Eresus->style.$this->name.'/';
		$filename = Core::app()->getFsRoot() . '/lang/'.$this->name.'/'.$locale['lang'].'.php';
		if (is_file($filename))
		{
			include $filename;
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ���������� � �������
	 *
	 * @param  array  $item  ���������� ������ ���������� (�� ��������� null)
	 *
	 * @return  array  ������ ����������, ��������� ��� ������ � ��
	 */
	public function __item($item = null)
	{
		global $Eresus;

		$result['name'] = $this->name;
		$result['content'] = false;
		$result['active'] = is_null($item)? true : $item['active'];
		$result['settings'] = $Eresus->db->escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
		$result['title'] = $this->title;
		$result['version'] = $this->version;
		$result['description'] = $this->description;
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ����������� ��������� � �������������� ������� ��������
	 *
	 * @param string $method  ��� ���������� ������
	 * @param array  $args    ���������� ���������
	 *
	 * @throws EresusMethodNotExistsException
	 */
	public function __call($method, $args)
	{
		throw new EresusMethodNotExistsException($method, get_class($this));
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� URL ���������� ������ �������
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getDataURL()
	{
		return $this->urlData;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� URL ���������� ������ �������
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getCodeURL()
	{
		return $this->urlCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� URL ���������� ������ �������
	 *
	 * @return string
	 *
	 * @since 2.15
	 */
	public function getStyleURL()
	{
		return $this->urlStyle;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ������ �������� ������� �� ��
	 *
	 * @return bool  ��������� ����������
	 */
	protected function loadSettings()
	{
		global $Eresus;

		$pluginInfo = ORM::getTable('PluginInfo')->find($this->name);
		if ($pluginInfo)
		{
			$this->settings = $pluginInfo->settings;
		}
		return (bool)$pluginInfo;
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� �������� ������� � ��
	 *
	 * @return bool  ��������� ����������
	 */
	protected function saveSettings()
	{
		global $Eresus;

		$result = $Eresus->db->selectItem('plugins', "`name`='{$this->name}'");
		$result = $this->__item($result);
		$result['settings'] = $Eresus->db->escape(encodeOptions($this->settings));
		$result = $Eresus->db->updateItem('plugins', $result, "`name`='".$this->name."'");

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ������ � ������� � ��
	 */
	protected function resetPlugin()
	{
		$this->loadSettings();
		$this->saveSettings();
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������, ����������� ��� ����������� �������
	 */
	public function install() {}
	//------------------------------------------------------------------------------

	/**
	 * ��������, ����������� ��� ������������� �������
	 */
	public function uninstall()
	{
		global $Eresus;

		# TODO: ��������� � IDataSource
		$tables = $Eresus->db->query_array("SHOW TABLES LIKE '{$Eresus->db->prefix}{$this->name}_%'");
		$tables = array_merge($tables, $Eresus->db->query_array("SHOW TABLES LIKE '{$Eresus->db->prefix}{$this->name}'"));
		for ($i=0; $i < count($tables); $i++)
			$this->dbDropTable(substr(current($tables[$i]), strlen($this->name)+1));
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ��� ��������� ��������
	 */
	public function onSettingsUpdate() {}
	//------------------------------------------------------------------------------

	/**
	 * ��������� � �� ��������� �������� �������
	 */
	public function updateSettings()
	{
		global $Eresus;

		foreach ($this->settings as $key => $value)
			if (!is_null(arg($key)))
				$this->settings[$key] = arg($key);
		$this->onSettingsUpdate();
		$this->saveSettings();
	}
	//------------------------------------------------------------------------------

	/**
	 * ������ ��������
	 *
	 * @param  string  $template  ������ � ������� ��������� �������� ������ ��������
	 * @param  mixed   $item      ������������� ������ �� ���������� ��� ����������� ������ ��������
	 *
	 * @return  string  ������������ ������
	 */
	protected function replaceMacros($template, $item)
	{
		$result = replaceMacros($template, $item);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ����� ����������
	 *
	 * @param string $name ��� ����������
	 * @return bool ���������
	 */
	protected function mkdir($name = '')
	{
		$result = true;
		$umask = umask(0000);
		# �������� � �������� �������� ���������� ������
		if (!is_dir($this->dirData)) $result = mkdir($this->dirData);
		if ($result) {
			# ������� ���������� ���� "." � "..", � ����� ��������� � ���������� �����
			$name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
			if ($name) {
				$name = explode('/', $name);
				$root = substr($this->dirData, 0, -1);
				for($i=0; $i<count($name); $i++) if ($name[$i]) {
					$root .= '/'.$name[$i];
					if (!is_dir($root)) $result = mkdir($root);
					if (!$result) break;
				}
			}
		}
		umask($umask);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ���������� � ������
	 *
	 * @param string $name ��� ����������
	 * @return bool ���������
	 */
	protected function rmdir($name = '')
	{
		$result = true;
		$name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
		$name = $this->dirData.$name;
		if (is_dir($name)) {
			$files = glob($name.'/{.*,*}', GLOB_BRACE);
			for ($i = 0; $i < count($files); $i++) {
				if (substr($files[$i], -2) == '/.' || substr($files[$i], -3) == '/..') continue;
				if (is_dir($files[$i])) $result = $this->rmdir(substr($files[$i], strlen($this->dirData)));
				elseif (is_file($files[$i])) $result = filedelete($files[$i]);
				if (!$result) break;
			}
			if ($result) $result = rmdir($name);
		}
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� �������� ��� �������
	 *
	 * @param string $table  ��������� ��� �������
	 * @return string �������� ��� �������
	 */
	protected function __table($table)
	{
		return $this->name.(empty($table)?'':'_'.$table);
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ������� � ��
	 *
	 * @param string $SQL �������� �������
	 * @param string $name ��� �������
	 *
	 * @return bool ��������� �����������
	 */
	protected function dbCreateTable($SQL, $name = '')
	{
		global $Eresus;

		$result = $Eresus->db->create($this->__table($name), $SQL);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ������� ��
	 *
	 * @param string $name ��� �������
	 *
	 * @return bool ��������� �����������
	 */
	protected function dbDropTable($name = '')
	{
		global $Eresus;

		$result = $Eresus->db->drop($this->__table($name));
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ������� �� ������� ��
	 *
	 * @param string	$table				��� ������� (������ �������� - ������� �� ���������)
	 * @param string	$condition		������� �������
	 * @param string	$order				������� �������
	 * @param string	$fields				������ �����
	 * @param int			$limit				������� �� ������ ����� ��� limit
	 * @param int			$offset				�������� �������
	 * @param bool		$distinct			������ ���������� ����������
	 *
	 * @return array|bool  ��������� �������� � ���� ������� ��� FALSE � ������ ������
	 */
	public function dbSelect($table = '', $condition = '', $order = '', $fields = '', $limit = 0,
		$offset = 0, $group = '', $distinct = false)
	{
		global $Eresus;

		$result = $Eresus->db->select($this->__table($table), $condition, $order, $fields, $limit,
			$offset, $group, $distinct);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ������ �� ��
	 *
	 * @param string $table  ��� �������
	 * @param mixed  $id   	 ������������� ��������
	 * @param string $key    ��� ��������� ����
	 *
	 * @return array �������
	 */
	public function dbItem($table, $id, $key = 'id')
	{
		global $Eresus;

		$result = $Eresus->db->selectItem($this->__table($table), "`$key` = '$id'");

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ������� � ������� ��
	 *
	 * @param string $table          ��� �������
	 * @param array  $item           ����������� �������
	 * @param string $key[optional]  ��� ��������� ����. �� ��������� "id"
	 */
	public function dbInsert($table, $item, $key = 'id')
	{
		global $Eresus;

		$result = $Eresus->db->insert($this->__table($table), $item);
		$result = $this->dbItem($table, $Eresus->db->getInsertedId(), $key);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ������ � ��
	 *
	 * @param string $table      ��� �������
	 * @param mixed  $data       ���������� �������� / ���������
	 * @param string $condition  �������� ���� / ������� ��� ������
	 *
	 * @return bool ���������
	 */
	public function dbUpdate($table, $data, $condition = '')
	{
		global $Eresus;

		if (is_array($data)) {
			if (empty($condition)) $condition = 'id';
			$result = $Eresus->db->updateItem($this->__table($table), $data, "`$condition` = '{$data[$condition]}'");
		} elseif (is_string($data)) {
			$result = $Eresus->db->update($this->__table($table), $data, $condition);
		}

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� �������� �� ��
	 *
	 * @param string $table  ��� �������
	 * @param mixed  $item   ��������� ������� / �������������
	 * @param string $key    �������� ����
	 *
	 * @return bool ���������
	 */
	public function dbDelete($table, $item, $key = 'id')
	{
		global $Eresus;

		$result = $Eresus->db->delete($this->__table($table), "`$key` = '".(is_array($item)? $item[$key] : $item)."'");

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ������� ���������� ������� � ��
	 *
	 * @param string $table      ��� �������
	 * @param string $condition  ������� ��� ��������� � �������
	 *
	 * @return int ���������� �������, ��������������� �������
	 */
	public function dbCount($table, $condition = '')
	{
		global $Eresus;

		$result = $Eresus->db->count($this->__table($table), $condition);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ���������� � ��������
	 *
	 * @param string $table  ����� ����� �������
	 * @param string $param  ������� ������ ��������� �������
	 *
	 * @return mixed
	 */
	public function dbTable($table, $param = '')
	{
		global $Eresus;

		$result = $Eresus->db->tableStatus($this->__table($table), $param);

		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ����������� ������������ �������
	 *
	 * @param string $event1  ��� �������1
	 * ...
	 * @param string $eventN  ��� �������N
	 */
	protected function listenEvents()
	{
		global $Eresus;

		for($i=0; $i < func_num_args(); $i++)
			$Eresus->plugins->events[func_get_arg($i)][] = $this->name;
	}
	//------------------------------------------------------------------------------
}

/**
* ������� ����� ��� ��������, ��������������� ��� ��������
*
* @package EresusCMS
*/
class ContentPlugin extends Plugin
{
	/**
	 * �����������
	 *
	 * ������������� ������ � �������� ������� �������� � ������ ��������� ���������
	 */
	public function __construct()
	{
		global $page;

		parent::__construct();
		if (isset($page))
		{
			$page->plugin = $this->name;
			if (isset($page->options) && count($page->options))
				foreach ($page->options as $key=>$value)
					$this->settings[$key] = $value;
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ���������� � �������
	 *
	 * @param  array  $item  ���������� ������ ���������� (�� ��������� null)
	 *
	 * @return  array  ������ ����������, ��������� ��� ������ � ��
	 */
	public function __item($item = null)
	{
		$result = parent::__item($item);
		$result['content'] = true;
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * �������� ��� �������� ������� ������� ����
	 * @param int     $id     ������������� ���������� �������
	 * @param string  $table  ��� �������
	 */
	public function onSectionDelete($id, $table = '')
	{
		if (count($this->dbTable($table)))
			$this->dbDelete($table, $id, 'section');
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������� �������� � ��
	 *
	 * @param  string  $content  �������
	 */
	public function updateContent($content)
	{
		global $Eresus, $page;

		$item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
		$item['content'] = $content;
		$Eresus->db->updateItem('pages', $item, "`id`='".$page->id."'");
	}
	//------------------------------------------------------------------------------

	/**
	* ��������� ������� ��������
	*/
	function adminUpdate()
	{
		$this->updateContent(arg('content', 'dbsafe'));
		HttpResponse::redirect(arg('submitURL'));
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ���������� �����
	 *
	 * @return  string  �������
	 */
	public function clientRenderContent()
	{
		global $Eresus, $page;

		/* ���� � URL ������� ���-���� ����� ������ �������, ���������� ����� 404 */
		if ($Eresus->request['file'] || $Eresus->request['query'] || $page->subpage || $page->topic)
			$page->httpError(404);

		return $page->content;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ���������������� �����
	 *
	 * @return  string  �������
	 */
	public function adminRenderContent()
	{
		global $page, $Eresus;

		if (arg('action') == 'update') $this->adminUpdate();
		$item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
		$form = array(
			'name' => 'editForm',
			'caption' => $page->title,
			'width' => '100%',
			'fields' => array (
				array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
				array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
			),
			'buttons' => array('apply', 'reset'),
		);

		$result = $page->renderForm($form, $item);
		return $result;
	}
	//------------------------------------------------------------------------------
}



/**
 * ����� ��� ������ � ������������ �������
 *
 * @package EresusCMS
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
