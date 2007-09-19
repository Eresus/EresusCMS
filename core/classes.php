	<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������� ���������� ��������� Eresus�
# ������ 2.10
# � 2004-2007, ProCreat Systems
# � 2007, Eresus Group
# http://eresus.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������ �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ����� "�������"
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TPlugins {
  var
    $list = array(), # ������ ���� ��������
    $items = array(), # ������ ��������
    $events = array(); # ������� ������������ �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function  TPlugins()
  {
  global $db;

    $items = $db->select('`plugins`', '', '`position`');
    if (count($items)) foreach($items as $item) $this->list[$item['name']] = $item;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function install($name)
  # ��������� ������ �������
  {
  global $db;

    $filename = filesRoot.'ext/'.$name.'.php';
    if (file_exists($filename)) {
      include_once($filename);
      $Class = 'T'.$name;
      $this->items[$name] = new $Class;
      $this->items[$name]->install();
      $db->insert('plugins', $this->items[$name]->createPluginItem());
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function uninstall($name)
  # �������� �������
  {
  global $db;

    if (!isset($this->items[$name])) $this->load($name);
    if (isset($this->items[$name])) $this->items[$name]->uninstall();
    $item = $db->selectItem('plugins', "`name`='".$name."'");
    if (!is_null($item)) {
      $db->delete('plugins', "`name`='".$name."'");
      $db->update('plugins', "`position` = `position`-1", "`position` > '".$item['position']."'");
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
        $Class = 'T'.$name;
        $this->items[$name] = new $Class;
        $result = $this->items[$name];
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
  var $name = 'noname';
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
}

/**
* ������� ����� ��� ��������, ��������������� ��� ��������
*
*
*/
class TContentPlugin extends TPlugin {
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