<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus� 2.00+
# � 2007, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TMenus extends TListContentPlugin {
  var $name = 'menus';
  var $title = '���������� ����';
  var $type = 'client,admin';
  var $version = '1.02b';
  var $kernel = '2.10b';
  var $description = '�������� ����';
  var $table = array (
    'name' => 'menus',
    'key'=> 'id',
    'sortMode' => 'id',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'caption', 'caption' => '��������'),
      array('name' => 'name', 'caption' => '���'),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'toggle' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>'������� ����', 'name'=>'action', 'value'=>'create')
      ),
    ),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `name` varchar(31) default NULL,
      `caption` varchar(255) default NULL,
      `active` tinyint(1) unsigned default NULL,
      `root` int(10) default NULL,
      `rootLevel` int(10) unsigned default 0,
      `expandLevelAuto` int(10) unsigned default 0,
      `expandLevelMax` int(10) unsigned default 0,
      `glue` varchar(63) default '',
      `tmplList` text,
      `tmplItem` text,
      `tmplSpecial` text,
      `specialMode` tinyint(3) unsigned default 0,
      `invisible` tinyint(1) unsigned default 0,
      PRIMARY KEY  (`id`),
      KEY `name` (`name`),
      KEY `active` (`active`)
    ) TYPE=MyISAM COMMENT='Menu collection';",
  );
  var $settings = array(
  );
  var $menu = null;
  var $pages = array(); # ���� �� ���������
  var $ids = array(); # ���� �� ��������� (������ ��������������)
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TMenus()
  # ���������� ����������� ������������ �������
  {
  global $plugins;
  
    parent::TPlugin();
    $plugins->events['clientOnURLSplit'][] = $this->name;
    $plugins->events['clientOnPageRender'][] = $this->name;
    $plugins->events['adminOnMenuRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
    global $db, $request;

    $item = GetArgs($db->fields($this->table['name']));
    $item['active'] = true;
    $db->insert($this->table['name'], $item);
    sendNotify('��������� ����: '.$item['caption']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
    global $db, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $item = GetArgs($item);
    $db->updateItem($this->table['name'], $item, "`id`='".$item['id']."'");
    sendNotify('�������� ����: '.$item['caption']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item)
  {
    preg_match_all('|{%selected\?(.*?):(.*?)}|i', $template, $matches);
    for($i = 0; $i < count($matches[0]); $i++)
      $template = str_replace($matches[0][$i], $item['is-selected']?$matches[1][$i]:$matches[2][$i], $template);
    preg_match_all('|{%parent\?(.*?):(.*?)}|i', $template, $matches);
    for($i = 0; $i < count($matches[0]); $i++)
      $template = str_replace($matches[0][$i], $item['is-parent']?$matches[1][$i]:$matches[2][$i], $template);
    $template = parent::replaceMacros($template, $item);
    return $template;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function pagesBrunch($owner = 0, $level = 0)
  {
    global $Eresus;
    
    $result = array(array(), array());
    $items = $Eresus->sections->children($owner, GUEST, SECTIONS_ACTIVE);
    if (count($items)) foreach($items as $item) {
      $result[0][] = str_repeat('- ', $level).$item['caption'];
      $result[1][] = $item['id'];
      $sub = $this->pagesBrunch($item['id'], $level+1);
      if (count($sub[0])) {
        $result[0] = array_merge($result[0], $sub[0]);
        $result[1] = array_merge($result[1], $sub[1]);
      }
    }    
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function menuBrunch($owner = 0, $path = '', $level = 1)
  # ������� ������ ����� ���� ������� �� �������� � id = $owner
  #   $owner - id ��������� ������
  #   $path - ����������� ���� � ���������
  #   $level - ������� �����������
  {
    global $Eresus, $user, $page, $request;

    $result = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    if ($owner == -1) $owner = $page->id;
    $items = $Eresus->sections->children($owner, $user['auth'] ? $user['access'] : GUEST, SECTIONS_ACTIVE | ($this->menu['invisible']? 0 : SECTIONS_VISIBLE));
    if (count($items)) {
      $result = array();
      foreach($items as $item) {
        $template = $this->menu['tmplItem'];
        if ($item['type'] == 'url') {
          $item = $Eresus->sections->get($item['id']);
          $item['url'] = $page->replaceMacros($item['content']);
        } else $item['url'] = httpRoot.$path.($item['name']=='main'?'':$item['name'].'/');
        $item['level'] = $level;
        $item['is-selected'] = $item['id'] == $page->id;
        $item['is-parent'] = !$item['is-selected'] && in_array($item['id'], $this->ids);
        if ((!$this->menu['expandLevelAuto'] || ($level < $this->menu['expandLevelAuto'])) || (($item['is-parent'] || $item['is-selected']) && (!$this->menu['expandLevelMax'] || $level < $this->menu['expandLevelMax']))) {
          $item['submenu'] = $this->menuBrunch($item['id'], $path.$item['name'].'/', $level+1);
        }
        switch ($this->menu['specialMode']) {
          case 0: # ���
          break;
          case 1: # ������ ��� ���������� ������
            if ($item['is-selected']) $template = $this->menu['tmplSpecial'];
          break;
          case 2: # ��� ���������� ������ ���� ������ ��� ��������
            if ((strpos($request['path'], $item['url']) === 0) && $item['name'] != 'main') $template = $this->menu['tmplSpecial'];
          break;
          case 3: # ��� �������, ������� ���������
            if (!empty($item['submenu'])) $template = $this->menu['tmplSpecial'];
          break;
        }
        $result[] = $this->replaceMacros($template, $item);
      }
      $result = implode($this->menu['glue'], $result);
      $result = array('level'=>($level), 'items'=>$result);
      $result = $this->replaceMacros($this->menu['tmplList'], $result);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminAddItem()
  {
    global $page, $db;

    $sections = $this->pagesBrunch();
    array_unshift($sections[0], '������� ������');
    array_unshift($sections[1], -1);
    array_unshift($sections[0], '������');
    array_unshift($sections[1], 0);
    $form = array(
      'name' => 'FormCreate',
      'caption' => '������� ����',
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array('type'=>'edit','name'=>'name','label'=>'<b>���</b>', 'width' => '100px', 'comment' => '��� ������������� � ��������', 'pattern'=>'/[a-z]\w*/i', 'errormsg'=>'��� ������ ���������� � ����� � ����� ��������� ������ ��������� ����� � �����'),
        array('type'=>'edit','name'=>'caption','label'=>'<b>��������</b>', 'width' => '100%', 'hint' => '��� ����������� �������������', 'pattern'=>'/.+/i', 'errormsg'=>'�������� �� ����� ���� ������'),
        array('type'=>'select','name'=>'root','label'=>'�������� ������', 'values'=>$sections[1], 'items'=>$sections[0], 'extra' =>'onchange="this.form.rootLevel.disabled = this.value != -1"'),
        array('type'=>'edit','name'=>'rootLevel','label'=>'����. �������', 'width' => '20px', 'comment' => '(0 - ������� �������)', 'default' => 0, 'disabled' => true),
        array('type'=>'checkbox','name'=>'invisible','label'=>'���������� ������� �������'),
        array('type'=>'header', 'value'=>'������ ����'),
        array('type'=>'edit','name'=>'expandLevelAuto','label'=>'������ ����������', 'width' => '20px', 'comment' => '������� (0 - ���������� ���)', 'default' => 0),
        array('type'=>'edit','name'=>'expandLevelMax','label'=>'������������� ��������', 'width' => '20px', 'comment' => '������� (0 - ��� �����������)', 'default' => 0),
        array('type'=>'header', 'value'=>'�������'),
        array('type'=>'memo','name'=>'tmplList','label'=>'������ ����� ������ ������ ����', 'height' => '3'),
        array('type'=>'text', 'value' => '�������:<ul><li><b><li><b>$(level)</b> - ����� �������� ������</li><li><b>$(items)</b> - ������ ����</li></ul>'),
        array('type'=>'edit','name'=>'glue','label'=>'����������� �������', 'width' => '100%', 'maxlength' => 63),
        array('type'=>'memo','name'=>'tmplItem','label'=>'������ ������ ����', 'height' => '3'),
        array('type'=>'memo','name'=>'tmplSpecial','label'=>'����������� ������ ������ ����', 'height' => '3'),
        array('type'=>'text', 'value' => '������������ ����������� ������'),
        array('type'=>'select','name'=>'specialMode','items'=>array(
          '���',
          '������ ��� ���������� ������',
          '��� ���������� ������ ���� ������ ��� ��������',
          '��� �������, ������� ���������'
          )
        ),
        array('type'=>'divider'),
        array('type'=>'text', 'value' => 
          '�������:<ul>'.
          '<li><b>��� �������� ��������</b></li>'.
          '<li><b>$(level)</b> - ����� �������� ������</li><li><b>$(url)</b> - ������</li>'.
          '<li><b>$(submenu)</b> - ����� ��� ������� �������</li>'.
          '<li><b>{%selected?������1:������2}</b> - ���� ������� ������, �������� ������1, ����� ������2</li>'.
          '<li><b>{%parent?������1:������2}</b> - ���� ������� ��������� ����� ������������ �������� ���������� ��������, �������� ������1, ����� ������2</li>'.
          '</ul>'),
        array('type'=>'divider'),
        array('type'=>'text', 'value' => '��� ������� ���� ����������� ������ <b>$(Menus:���_����)</b>'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
    global $page, $db, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['id']."'");
    $sections = $this->pagesBrunch();
    array_unshift($sections[0], '������� ������');
    array_unshift($sections[1], -1);
    array_unshift($sections[0], '������');
    array_unshift($sections[1], 0);
    $form = array(
      'name' => 'FormEdit',
      'caption' => '�������� ����',
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array('type'=>'edit','name'=>'name','label'=>'<b>���</b>', 'width' => '100px', 'comment' => '��� ������������� � ��������', 'pattern'=>'/[a-z]\w*/i', 'errormsg'=>'��� ������ ���������� � ����� � ����� ��������� ������ ��������� ����� � �����'),
        array('type'=>'edit','name'=>'caption','label'=>'<b>��������</b>', 'width' => '100%', 'hint' => '��� ����������� �������������', 'pattern'=>'/.+/i', 'errormsg'=>'�������� �� ����� ���� ������'),
        array('type'=>'select','name'=>'root','label'=>'�������� ������', 'values'=>$sections[1], 'items'=>$sections[0], 'extra' =>'onchange="this.form.rootLevel.disabled = this.value != -1"'),
        array('type'=>'edit','name'=>'rootLevel','label'=>'����. �������', 'width' => '20px', 'comment' => '(0 - ������� �������)', 'default' => 0, 'disabled' => $item['root'] != -1),
        array('type'=>'header', 'value'=>'������ ����'),
        array('type'=>'edit','name'=>'expandLevelAuto','label'=>'������ ����������', 'width' => '20px', 'comment' => '������� (0 - ���������� ���)', 'default' => 0),
        array('type'=>'edit','name'=>'expandLevelMax','label'=>'������������� ��������', 'width' => '20px', 'comment' => '������� (0 - ��� �����������)', 'default' => 0),
        array('type'=>'checkbox','name'=>'invisible','label'=>'���������� ������� �������'),
        array('type'=>'header', 'value'=>'�������'),
        array('type'=>'memo','name'=>'tmplList','label'=>'������ ����� ������ ������ ����', 'height' => '3'),
        array('type'=>'text', 'value' => '�������:<ul><li><b><li><b>$(level)</b> - ����� �������� ������</li><li><b>$(items)</b> - ������ ����</li></ul>'),
        array('type'=>'edit','name'=>'glue','label'=>'����������� �������', 'width' => '100%', 'maxlength' => 63),
        array('type'=>'memo','name'=>'tmplItem','label'=>'������ ������ ����', 'height' => '3'),
        array('type'=>'memo','name'=>'tmplSpecial','label'=>'����������� ������ ������ ����', 'height' => '3'),
        array('type'=>'text', 'value' => '������������ ����������� ������'),
        array('type'=>'select','name'=>'specialMode','items'=>array(
          '���',
          '������ ��� ���������� ������',
          '��� ���������� ������ ���� ������ ��� ��������',
          '��� �������, ������� ���������'
          )
        ),
        array('type'=>'divider'),
        array('type'=>'text', 'value' => 
          '�������:<ul>'.
          '<li><b>��� �������� ��������</b></li>'.
          '<li><b>$(level)</b> - ����� �������� ������</li><li><b>$(url)</b> - ������</li>'.
          '<li><b>$(submenu)</b> - ����� ��� ������� �������</li>'.
          '<li><b>{%selected?������1:������2}</b> - ���� ������� ������, �������� ������1, ����� ������2</li>'.
          '<li><b>{%parent?������1:������2}</b> - ���� ������� ��������� ����� ������������ �������� ���������� ��������, �������� ������1, ����� ������2</li>'.
          '</ul>'),
        array('type'=>'divider'),
        array('type'=>'text', 'value' => '��� ������� ���� ����������� ������ <b>$(Menus:���_����)</b>'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
    $result = $this->adminRenderContent();
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnURLSplit($item, $url)
  { 
    $this->pages[] = $item;
    $this->ids[] = $item['id'];
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
    global $Eresus, $page, $request, $db;
    
    preg_match_all('/\$\(Menus:(.+)?\)/Usi', $text, $menus, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    $delta = 0;
    for($i = 0; $i < count($menus); $i++) {
      $this->menu = $db->selectItem($this->table['name'], "`name`='".$menus[$i][1][0]."' AND `active` = 1");
      if (!is_null($this->menu)) {
        if ($this->menu['root'] == -1 && $this->menu['rootLevel']) {
          $parents = $Eresus->sections->parents($page->id);
          $level = count($parents);
          if ($level == $this->menu['rootLevel']) $this->menu['root'] = -1;
          elseif ($level > $this->menu['rootLevel']) $this->menu['root'] = $this->menu['root'] = $parents[$this->menu['rootLevel']];
          else $this->menu['root'] = -2;
        }
        $path = $this->menu['root'] > -1 ? $page->clientURL($this->menu['root']) : $request['path'];
        $menu = $this->menuBrunch($this->menu['root'], $path);
        $text = substr_replace($text, $menu, $menus[$i][0][1]+$delta, strlen($menus[$i][0][0]));
        $delta += strlen($menu) - strlen($menus[$i][0][0]);
      }
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminOnMenuRender()
  {
  global $page;
  
    $page->addMenuItem(admExtensions, array ('access'  => ADMIN, 'link'  => $this->name, 'caption'  => $this->title, 'hint'  => $this->description));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>