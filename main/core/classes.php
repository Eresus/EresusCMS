<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004-2007, ProCreat Systems, http://procreat.ru/
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
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
 * $Id$
 */

/**
 * ������������ ����� ���-�����������
 *
 * @package Eresus2
 */
class WebPage
{
	/**
	 * ������������� �������� �������
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * HTTP-��������� ������
	 *
	 * @var array
	 */
	public $headers = array();

	/**
	 * �������� ������ HEAD
	 *
	 * -	meta-http - ����-���� HTTP-����������
	 * -	meta-tags - ����-����
	 * -	link - ����������� ������� ��������
	 * -	style - CSS
	 * -	script - �������
	 * -	content - ������
	 *
	 * @var array
	 */
	protected $head = array (
		'meta-http' => array(),
		'meta-tags' => array(),
		'link' => array(),
		'style' => array(),
		'script' => array(),
		'content' => '',
	);

	/**
	 * �������� �� ���������
	 * @var array
	 */
	protected $defaults = array(
		'pageselector' => array(
			'<div class="pages">$(pages)</div>',
			'&nbsp;<a href="$(href)">$(number)</a>&nbsp;',
			'&nbsp;<b>$(number)</b>&nbsp;',
			'<a href="$(href)">&larr;</a>',
			'<a href="$(href)">&rarr;</a>',
		),
	);

	/**
	 * �����������
	 *
	 * @return WebPage
	 */
	public function __construct()
	{
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ����-��� HTTP-���������
	 *
	 * ��������� ��� �������� ����-��� <meta http-equiv="$httpEquiv" content="$content" />
	 *
	 * @param string $httpEquiv  ��� ��������� HTTP
	 * @param string $content  	  �������� ���������
	 */
	public function setMetaHeader($httpEquiv, $content)
	{
		$this->head['meta-http'][$httpEquiv] = $content;
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ����-����
	 *
	 * @param string $name  		 ��� ����
	 * @param string $content  �������� ����
	 */
	public function setMetaTag($name, $content)
	{
		$this->head['meta-tags'][$name] = $content;
	}
	//------------------------------------------------------------------------------

	/**
	 * ����������� CSS-�����
	 *
	 * @param string $url    URL �����
	 * @param string $media  ��� ��������
	 */
	public function linkStyles($url, $media = '')
	{
		/* ���������, �� �������� �� ��� ���� URL  */
		for ($i = 0; $i < count($this->head['link']); $i++)
			if ($this->head['link'][$i]['href'] == $url)
				return;

		$item = array('rel' => 'StyleSheet', 'href' => $url, 'type' => 'text/css');

		if (!empty($media))
			$item['media'] = $media;

		$this->head['link'][] = $item;
	}
	//------------------------------------------------------------------------------

	/**
	 * ����������� CSS
	 *
	 * @param string $content  ����� CSS
	 * @param string $media 	  ��� ��������
	 */
	public function addStyles($content, $media = '')
	{
		$content = preg_replace(array('/^(\s)+/m', '/^(\S)/m'), array('		', '	\1'), $content);
		$content = rtrim($content);
		$item = array('content' => $content);
		if (!empty($media))
			$item['media'] = $media;
		$this->head['style'][] = $item;
	}
	//------------------------------------------------------------------------------

	/**
	 * ����������� ����������� �������
	 *
	 * @param string $url   URL �������
	 * @param string $type  ��� �������
	 */
	public function linkScripts($url, $type = 'javascript')
	{
		for ($i = 0; $i < count($this->head['script']); $i++)
		if (
			isset($this->head['script'][$i]['src']) &&
			$this->head['script'][$i]['src'] == $url
		)
			return;

		if (strpos($type, '/') === false)
			switch (strtolower($type))
			{
				case 'emca': $type = 'text/emcascript'; break;
				case 'javascript': $type = 'text/javascript'; break;
				case 'jscript': $type = 'text/jscript'; break;
				case 'vbscript': $type = 'text/vbscript'; break;
				default: return;
			}

		$this->head['script'][] = array('type' => $type, 'src' => $url);
	}
	//------------------------------------------------------------------------------

	/**
	 * ���������� ���������� ��������
	 *
	 * @param string $content  ��� �������
	 * @param string $type     ��� �������
	 */
	public function addScripts($content, $type = 'javascript')
	{
		if (strpos($type, '/') === false)
			switch (strtolower($type))
			{
				case 'emca': $type = 'text/emcascript'; break;
				case 'javascript': $type = 'text/javascript'; break;
				case 'jscript': $type = 'text/jscript'; break;
				case 'vbscript': $type = 'text/vbscript'; break;
				default: return;
			}

		$content = preg_replace(array('/^(\s)+/m', '/^(\S)/m'), array('		', '	\1'), $content);
		$this->head['script'][] = array('type' => $type, 'content' => $content);
	}
	//------------------------------------------------------------------------------

	/**
	 * ��������� ������ <head>
	 *
	 * @return string  ������������ ������ <head>
	 */
	protected function renderHeadSection()
	{
		$result = array();
		# <meta> ����
		if (count($this->head['meta-http'])) foreach($this->head['meta-http'] as $key => $value)
			$result[] = '	<meta http-equiv="'.$key.'" content="'.$value.'" />';
		if (count($this->head['meta-tags'])) foreach($this->head['meta-tags'] as $key => $value)
			$result[] = '	<meta name="'.$key.'" content="'.$value.'" />';
		# <link>
		if (count($this->head['link'])) foreach($this->head['link'] as $value)
			$result[] = '	<link rel="'.$value['rel'].'" href="'.$value['href'].'" type="'.$value['type'].'"'.(isset($value['media'])?' media="'.$value['media'].'"':'').' />';
		# <script>
		if (count($this->head['script'])) foreach($this->head['script'] as $value) {
			if (isset($value['content'])) {
				$value['content'] = trim($value['content']);
				$result[] = "	<script type=\"".$value['type']."\">\n	//<!-- <![CDATA[\n		".$value['content']."\n	//]] -->\n	</script>";
			} elseif (isset($value['src'])) $result[] = '	<script src="'.$value['src'].'" type="'.$value['type'].'"></script>';
		}
		# <style>
		if (count($this->head['style'])) foreach($this->head['style'] as $value)
			$result[] = '	<style type="text/css"'.(isset($value['media'])?' media="'.$value['media'].'"':'').'>'."\n".$value['content']."\n  </style>";

		$this->head['content'] = trim($this->head['content']);
		if (!empty($this->head['content'])) $result[] = $this->head['content'];

		$result = implode("\n" , $result);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ������ URL GET-������� �� ������ ���������� ����������
	 *
	 * URL ����� �������� �� ���� ������:
	 * 1. ����� �������� ������� ($Eresus->request['path'])
	 * 2. key=value ���������
	 *
	 * ������ ���������� ������������ ������������ ������ ���������� �������� �������
	 * � ��������� ������� $args. �������� $args ����� ��������� ��� ����������� ��������
	 * �������.
	 *
	 * ���� �������� ��������� - ������ ������, �� ����� ����� �� �������.
	 *
	 * @param array $args  ���������� ���������
	 * @return string
	 */
	public function url($args = array())
	{
		global $Eresus;

		/* ���������� ��������� ������ � ��������� �������� ������� */
		$args = array_merge($Eresus->request['arg'], $args);

		/* ���������� ��������-������� � ������, �������� �������� ������� */
		foreach ($args as $key => $value)
			if (is_array($value))
				$args[$key] = implode(',', $value);

		$result = array();
		foreach ($args as $key => $value)
			if ($value !== '')
				$result []= "$key=$value";

		$result = implode('&amp;', $result);
		$result = $Eresus->request['path'].'?'.$result;
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ���������� URL �������� � ��������������� $id
	 *
	 * @param int $id  ������������� ��������
	 * @return string URL �������� ��� NULL ���� ������� $id �� ����������
	 */
	public function clientURL($id)
	{
		global $Eresus;

		$parents = $Eresus->sections->parents($id);

		if (is_null($parents)) return null;

		array_push($parents, $id);
		$items = $Eresus->sections->get( $parents);

		$list = array();
		for($i = 0; $i < count($items); $i++) $list[array_search($items[$i]['id'], $parents)-1] = $items[$i]['name'];
		$result = $Eresus->root;
		for($i = 0; $i < count($list); $i++) $result .= $list[$i].'/';

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� ������������� �������
	 *
	 * @param int     $total      ����� ���������� �������
	 * @param int     $current    ����� ������� ��������
	 * @param string  $url        ������ ������ ��� �������� � �����������.
	 * @param array   $templates  ������� ����������
	 * @return string
	 */
	public function pageSelector($total, $current, $url = null, $templates = null)
	{
		global $Eresus;

		$result = '';
		# �������� ��������
		if (!is_array($templates))
			$templates = array();
		for ($i=0; $i < 5; $i++)
			if (!isset($templates[$i]))
				$templates[$i] = $this->defaults['pageselector'][$i];

		if (is_null($url))
			$url = $Eresus->request['path'].'p%d/';

		$pages = array(); # ������������ ��������
		# ���������� ������ ������ � ��������� ������������ �������
		$visible = option('clientPagesAtOnce'); # TODO: �������� ���������� ��� ������� ���� client/admin
		if ($total > $visible)
		{
			# ����� �������� �� ��� ��������
			$from = floor($current - $visible / 2); # �������� ����� � ������� ����� �������� �������
			if ($from < 1)
				$from = 1; # ������� ������ 1-� �� ����������
			$to = $from + $visible - 1; # �� ������ �������� $visible �������
			if ($to > $total)
			{ # �� ���� ��� ������ ��� ������� �����, ������ �����������
				$to = $total;
				$from = $to - $visible + 1;
			}
		}
			else
		{
			# ����� �������� ��� ��������
			$from = 1;
			$to = $total;
		}
		for($i = $from; $i <= $to; $i++)
		{
			$src['href'] = sprintf($url, $i);
			$src['number'] = $i;
			$pages[] = replaceMacros($templates[$i != $current ? 1 : 2], $src);
		}

		$pages = implode('', $pages);
		if ($from != 1)
			$pages = replaceMacros($templates[3], array('href' => sprintf($url, 1))).$pages;
		if ($to != $total)
			$pages .= replaceMacros($templates[4], array('href' => sprintf($url, $total)));
		$result = replaceMacros($templates[0], array('pages' => $pages));

		return $result;
	}
	//------------------------------------------------------------------------------

}

/**
 * ������ � ���������
 */
class Plugins {
	var $list = array(); # ������ ���� ��������
	var $items = array(); # ������ ��������
	var $events = array(); # ������� ������������ �������
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function Plugins()
	{
		global $Eresus;

		$items = $Eresus->db->select('`plugins`', '', '`position`');
		if (count($items)) foreach($items as $item) $this->list[$item['name']] = $item;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

	/**
	 * ������������� ������
	 * @param string $name  ��� �������
	 * @return void
	 */
	function install($name)
	{
		global $Eresus;

		$filename = filesRoot.'ext/'.$name.'.php';
		if (FS::exists($filename))
		{
			Core::safeInclude($filename);
			$ClassName = $name;
			if (!class_exists($ClassName, false) && class_exists('T'.$ClassName, false))
				$ClassName = 'T'.$ClassName; # FIXME: �������� ������������� � �������� �� 2.10b2
			if (class_exists($ClassName, false))
			{
				$this->items[$name] = new $ClassName;
				$this->items[$name]->install();
				$Eresus->db->insert('plugins', $this->items[$name]->__item());
			}
				else FatalError(sprintf(errClassNotFound, $ClassName));
		}
	}
	//-----------------------------------------------------------------------------

	function uninstall($name)
	# �������� �������
	{
		global $Eresus;

		if (!isset($this->items[$name])) $this->load($name);
		if (isset($this->items[$name])) $this->items[$name]->uninstall();
		$item = $Eresus->db->selectItem('plugins', "`name`='".$name."'");
		if (!is_null($item)) {
			$Eresus->db->delete('plugins', "`name`='".$name."'");
			$Eresus->db->update('plugins', "`position` = `position`-1", "`position` > '".$item['position']."'");
		}
		$filename = filesRoot.'ext/'.$name.'.php';
		#if (file_exists($filename)) unlink($filename);
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
		if (!is_null($exclude))
			eresus_log(__METHOD__, LOG_NOTICE, '$exclude argument is deprecated');

		if (!is_null($include))
			eresus_log(__METHOD__, LOG_NOTICE, '$include argument is deprecated');

		if (count($this->list))
			foreach($this->list as $item)
				if ($item['active'])
					$this->load($item['name']);
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function load($name)
	{
		$result = isset($this->items[$name]) ? $this->items[$name] : false;
		if (isset($this->list[$name]) && !$result) {
			$filename = filesRoot.'ext/'.$name.'.php';
			if (file_exists($filename)) {
				include_once($filename);
				$ClassName = $name;
				if (!class_exists($ClassName) && class_exists('T'.$ClassName)) $ClassName = 'T'.$ClassName; # FIX: �������� ������������� � �������� �� 2.10b2
				if (class_exists($ClassName)) {
					$this->items[$name] = new $ClassName;
					$result = $this->items[$name];
				} else FatalError(sprintf(errClassNotFound, $name));
			} else $result = false;
		}
		return $result;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
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

				$subitems = $Eresus->db->select('pages', "(`owner`='".$page->id."') AND (`active`='1') AND (`access` >= '".($Eresus->user['auth'] ? $Eresus->user['access'] : GUEST)."')", "`position`");
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
				HTTP::redirect($page->replaceMacros($page->content));
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
 * @package Eresus2
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
	protected $settings = array();

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
	 * @uses FS::isFile
	 * @uses Core::safeInclude
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
		$filename = filesRoot.'lang/'.$this->name.'/'.$locale['lang'].'.php';
		if (FS::isFile($filename))
			Core::safeInclude($filename);
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
		$result['position'] = is_null($item) ? $Eresus->db->count('plugins') : $item['position'];
		$result['settings'] = $Eresus->db->escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
		$result['title'] = $this->title;
		$result['version'] = $this->version;
		$result['description'] = $this->description;
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ������ �������� ������� �� ��
	 *
	 * @return bool  ��������� ����������
	 */
	protected function loadSettings()
	{
		global $Eresus;

		$result = $Eresus->db->selectItem('plugins', "`name`='".$this->name."'");
		if ($result)
			$this->settings = decodeOptions($result['settings'], $this->settings);
		return (bool)$result;
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
				$this->settings[$key] = arg($key, 'dbsafe');
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
	 * @param string $table  ��� �������
	 * @param array  $item   ����������� �������
	 */
	public function dbInsert($table, $item)
	{
		global $Eresus;

		$result = $Eresus->db->insert($this->__table($table), $item);
		$result = $this->dbItem($table, $Eresus->db->getInsertedId());

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
*
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
		HTTP::redirect(arg('submitURL'));
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
			'name' => 'content',
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
 * ������� ����� ���������� ��������� ����������
 *
 * @package Eresus2
 */
class EresusExtensionConnector
{
	/**
	 * �������� URL ����������
	 *
	 * @var string
	 */
	protected $root;

	/**
	 * �������� ���� ����������
	 *
	 * @var string
	 */
	protected $froot;

	/**
	 * �����������
	 *
	 * @return EresusExtensionConnector
	 */
	function EresusExtensionConnector()
	{
		global $Eresus;

		$name = strtolower(substr(get_class($this), 0, -9));
		$this->root = $Eresus->root.'ext-3rd/'.$name.'/';
		$this->froot = $Eresus->froot.'ext-3rd/'.$name.'/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * ����� ���������� ��� ������������� ������ �������� � ����������
	 *
	 */
	function proxy()
	{
		global $Eresus;

		if (!UserRights(EDITOR))
			die;

		$ext = strtolower(substr($Eresus->request['file'], strrpos($Eresus->request['file'], '.') + 1));
		$filename = $Eresus->request['path'] . $Eresus->request['file'];
		$filename = $Eresus->froot . substr($filename, strlen($Eresus->root));
		switch (true)
		{
			case in_array($ext, array('png', 'jpg', 'jpeg', 'gif')):
				$info = getimagesize($filename);
				header('Content-type: '.$info['mime']);
				echo file_get_contents($filename);
			break;

			case $ext == 'js':
				header('Content-type: text/javascript');
				echo file_get_contents($filename);
			break;

			case $ext == 'css':
				header('Content-type: text/css');
				echo file_get_contents($filename);
			break;

			case $ext == 'html':
			case $ext == 'htm':
				header('Content-type: text/html');
				echo file_get_contents($filename);
			break;

			case $ext == 'php':
				$Eresus->conf['debug']['enable'] = false;
				restore_error_handler();
				chdir(dirname($filename));
				require $filename;
			break;
		}
	}
	//-----------------------------------------------------------------------------
}



/**
 * ����� ��� ������ � ������������ �������
 */
class EresusExtensions {
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
