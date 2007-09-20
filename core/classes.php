<?php
/**
 * �������� ������ �������
 * 
 * ������� ���������� ��������� Eresus� 2
 * � 2004-2007, ProCreat Systems, http://procreat.ru/
 * � 2007, Eresus Group, http://eresus.ru/
 * 
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ����� "�������"
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

class Plugins {
  var $list = array(); # ������ ���� ��������
  var $items = array(); # ������ ��������
  var $events = array(); # ������� ������������ �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function  Plugins()
  {
  	global $Eresus;

    $items = $Eresus->db->select('`plugins`', '', '`position`');
    if (count($items)) foreach($items as $item) $this->list[$item['name']] = $item;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function install($name)
  # ��������� ������ �������
  {
  	global $Eresus;

    $filename = filesRoot.'ext/'.$name.'.php';
    if (file_exists($filename)) {
      include_once($filename);
      $ClassName = $name;
      if (!class_exists($ClassName) && class_exists('T'.$ClassName)) $ClassName = 'T'.$ClassName; # FIX: �������� ������������� � �������� �� 2.10b2
      if (class_exists($ClassName)) {
	      $this->items[$name] = new $ClassName;
	      $this->items[$name]->install();
	      $Eresus->db->insert('plugins', $this->items[$name]->__item());
      } else FatalError(sprintf(errClassNotFound, $ClassName));
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
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
  function preload($include, $exclude)
  {
    if (count($this->list)) foreach($this->list as $item) if ($item['active']) {
      $type = explode(',', $item['type']);
      if (count(array_intersect($include, $type)) && count(array_diff($exclude, $type))) $this->load($item['name']);
    }
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
  function clientRenderContent()
  {
  global $page, $db, $user, $session, $request;

    $result = '';
    switch ($page->type) {
      case 'default':
        $plugin = new TContentPlugin;
        $result = $plugin->clientRenderContent();
      break;
      case 'list':
        if (isset($page->topic)) $page->httpError('404');
        $subitems = $db->select('pages', "(`owner`='".$page->id."') AND (`active`='1') AND (`access` >= '".($user['auth'] ? $user['access'] : GUEST)."')", "`position`");
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
              $request['url'].($page->name == 'main' && !$page->owner ? 'main/' : '').$item['name'].'/',
            ),
            $template['html']
          );
          $result = str_replace('$(items)', $items, $page->content);
        }
      break;
      case 'url':
        goto($page->replaceMacros($page->content));
      break;
      default:
      if ($this->load($page->type)) {
        if (method_exists($this->items[$page->type], 'clientRenderContent'))
        $result = $this->items[$page->type]->clientRenderContent();
        else $session['errorMessage'] = sprintf(errMethodNotFound, 'clientRenderContent', get_class($this->items[$page->type]));
      }
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
    if (isset($this->events['adminOnMenuRender'])) foreach($this->events['adminOnMenuRender'] as $plugin) $this->items[$plugin]->adminOnMenuRender();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
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
 * @var  string  $name        ��� �������
 * @var  string  $version	   	������ �������
 * @var  string  $kernel      ����������� ������ Eresus
 * @var  string  $title       �������� �������
 * @var  string  $description	�������� �������
 * @var  string  $type        ��� �������, ������������ ����� ������� �������� �����:
 *                            	client   - ��������� ������ � ��
 *                              admin    - ��������� ������ � ��
 *                              content  - ������ ������������� ��� ��������
 *                              ondemand - �� ��������� ������ �������������
 * @var  array   $settings    ��������� �������
 */
class Plugin {
  var $name;
  var $version = '0.00';
  var $kernel = '2.10b2';
  var $title = 'no title';
  var $description = '';
  var $type;
  var $settings = array();
  var $dirData; # ���������� ������ (/data/���_�������)
  var $dirCode; # ���������� �������� (/ext/���_�������)
/**
 * �����������
 *
 * ���������� ������ �������� ������� � ����������� �������� ������
 */
function Plugin()
{
	global $Eresus, $plugins, $locale;

	$this->name = strtolower(get_class($this));
	# �������� ������������� � �������� �� 2.10b2
	if (!property_exists($this, 'kernel')) $this->name = substr($this->name, 1);
	
  if (!empty($this->name) && isset($plugins->list[$this->name])) {
    $this->settings = decodeOptions($plugins->list[$this->name]['settings'], $this->settings);
		# ���� ����������� ������ ������� �������� �� ������������� �����
		# �� ���������� ���������� ���������� ���������� � ������� � ��
    if ($this->version != $plugins->list[$this->name]['version']) $this->resetPlugin();
  }
  $this->dirData = $Eresus->fdata.$this->name.'/'; 
  $this->dirCode = $Eresus->froot.'ext/'.$this->name.'/'; 
  $filename = filesRoot.'lang/'.$this->name.'/'.$locale['lang'].'.inc';
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
  $result['type'] = $this->type;
  $result['active'] = is_null($item)? true : $item['active'];
  $result['position'] = is_null($item) ? $Eresus->db->count('plugins') : $item['position'];
  $result['settings'] = is_null($item) ? encodeOptions($this->settings) : $item['settings'];
  $result['title'] = $this->title;
  $result['version'] = $this->version;
  $result['description'] = $this->description;
  return $result;
}
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
  $result = $Eresus->db->updateItem('plugins', $this->__item(), "`name`='".$this->name."'");
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
function uninstall()
{
	global $Eresus;
	
	$tables = $Eresus->db->query_array("SHOW TABLES LIKE '{$Eresus->db->prefix}{$this->name}_%'");
	for ($i=0; $i < count($tables); $i++)
		$this->dbDropTable(substr(current($tables[$i]), strlen($this->name)+1));
	$this->dbDropTable();
}
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

  foreach ($this->settings as $key => $value) if (isset($Eresus->request['arg'][$key])) $this->settings[$key] = $Eresus->request['arg'][$key];
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
/**
 * �������� ����� ����������
 * 
 * @param string $name ��� ����������
 * @return bool ���������
 */
function mkdir($name = '')
{
	$result = true;
	$umask = umask(0000);
	# �������� � �������� �������� ���������� ������
	if (!is_dir($this->dirData)) $result = mkdir($this->dirData);
	if ($result) {
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
function rmdir($name = '')
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
 * �������� ������� � ��
 * 
 * @param string $SQL �������� �������
 * @param string $name ��� �������
 * 
 * @return bool ��������� �����������
 */
function dbCreateTable($SQL, $name = '')
{
	global $Eresus;
	
	$result = $Eresus->db->create($this->name.(empty($name)?'':'_'.$name), $SQL);
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
function dbDropTable($name = '')
{
	global $Eresus;
	
	$result = $Eresus->db->drop($this->name.(empty($name)?'':'_'.$name));
	return $result;
}
//------------------------------------------------------------------------------
}

/**
* ������� ����� ��� ��������, ��������������� ��� ��������
*
*
*/
class ContentPlugin extends Plugin {
/**
* �����������
*
* ������������� ������ � �������� ������� �������� � ������ ��������� ���������
*/
function TContentPlugin()
{
	global $page;

  parent::TPlugin();
  if (isset($page)) {
    $page->plugin = $this->name;
    if (count($page->options)) foreach ($page->options as $key=>$value) $this->settings[$key] = $value;
  }
}
//------------------------------------------------------------------------------
/**
* ��������� ������� �������� � ��
* 
* @param  string  $content  �������
*/
function updateContent($content)
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
function update()
{
	$this->updateContent(arg('content'));
  goto(arg('submitURL'));
}
//------------------------------------------------------------------------------
/**
* ��������� ���������� �����
*
* @return  string  �������
*/
function clientRenderContent()
{
	global $page;

  return $page->content;
}
//------------------------------------------------------------------------------
/**
* ��������� ���������������� �����
*
* @return  string  �������
*/
function adminRenderContent()
{
	global $page, $Eresus;

  $item = $Eresus->db->selectItem('pages', "`id`='".$page->id."'");
  $form = array(
    'name' => 'content',
    'caption' => $page->title,
    'width' => '100%',
    'fields' => array (
      array ('type'=>'hidden','name'=>'update'),
      array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
    ),
    'buttons' => array('apply', 'reset'),
  );

  $result = $page->renderForm($form, $item);
  return $result;
}
//------------------------------------------------------------------------------
}
?>