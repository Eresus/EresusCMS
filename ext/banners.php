<?php
/**
* ������� ������ ��������
*
* Eresus 2, PHP 4,5 
*
* � 2005-2006, ProCreat Systems, http://procreat.ru/
* � 2007, Eresus Group, http://eresus.ru/
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @author: dkDimon <dkdimon@mail.ru>
*
* @version: 1.09
* @modified: 2007-08-22
*/

class TBanners extends TListContentPlugin {
  var $name = 'banners';
  var $title = '�������';
  var $type = 'client,admin';
  var $version = '1.09';
  var $description = '������� ������ ��������';
  var $table = array (
    'name' => 'banners',
    'key'=> 'id',
    'sortMode' => 'id',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'caption', 'caption' => '��������'),
      array('name' => 'block', 'caption' => '����', 'align'=> 'right'),
      array('name' => 'priority', 'caption' => '<span title="���������" style="cursor: default;">&nbsp;&nbsp;*</span>', 'align'=>'center'),
      array('name' => 'showTill', 'caption' => '�� ����', 'replace'=> array('0000-00-00'=> '��� �������.')),
      array('name' => 'showCount', 'caption' => '����.�����.', 'align'=>'right', 'replace' => array('0'=> '��� �������.')),
      array('name' => 'shows', 'caption' => '�������', 'align'=>'right'),
      array('name' => 'clicks', 'caption' => '������', 'align'=>'right'),
      array('name' => 'mail', 'caption' => '��������', 'value' => '<a href="mailto:$(mail)">$(mail)</a>', 'macros' => true),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'toggle' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>'�������� ������', 'name'=>'action', 'value'=>'create')
      ),
    ),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `caption` varchar(255) default NULL,
      `active` tinyint(1) unsigned default NULL,
      `section` varchar(255) default NULL,
      `priority` int(10) unsigned default NULL,
      `block` varchar(31) default NULL,
      `showFrom` date default NULL,
      `showTill` date default NULL,
      `showCount` int(10) unsigned default NULL,
      `html` text,
      `image` varchar(255) default NULL,
      `width` varchar(15) default NULL,
      `height` varchar(15) default NULL,
      `url` varchar(255) default NULL,
      `target` tinyint(1) unsigned default NULL,
      `shows` bigint(20) unsigned default NULL,
      `clicks` bigint(20) unsigned default NULL,
      PRIMARY KEY  (`id`),
      KEY `active` (`active`),
      KEY `priority` (`priority`),
      KEY `showFrom` (`showFrom`),
      KEY `showTill` (`showTill`),
      KEY `showCount` (`showCount`),
      KEY `shows` (`shows`)
    ) TYPE=MyISAM COMMENT='Banner system';",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function install()
  {
    parent::install();
    umask(0000);
    if (!file_exists(filesRoot.'data/'.$this->name)) mkdir(filesRoot.'data/'.$this->name, 0777);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TBanners()
  # ���������� ����������� ������������ �������
  {
  global $plugins;

    parent::TPlugin();
    if (defined('CLIENTUI')) $plugins->events['clientOnPageRender'][] = $this->name;
    else $plugins->events['adminOnMenuRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function menuBrunch($owner = 0, $level = 0)
  {
  global $db;
    $result = array(array(), array());
    $items = $db->select('`pages`', "(`access`>='".USER."')AND(`owner`='".$owner."') AND (`active`='1')", "`position`", false, "`id`,`caption`");
    if (count($items)) foreach($items as $item) {
      $result[0][] = str_repeat('- ', $level).$item['caption'];
      $result[1][] = $item['id'];
      $sub = $this->menuBrunch($item['id'], $level+1);
      if (count($sub[0])) {
        $result[0] = array_merge($result[0], $sub[0]);
        $result[1] = array_merge($result[1], $sub[1]);
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
    global $db, $request;

    $item = GetArgs($db->fields($this->table['name']));
    $item['section'] = ':'.implode(':', arg('section')).':';
    if ($item['showTill'] == '') unset($item['showTill']);
    $db->insert($this->table['name'], $item);
    $item['id'] = $db->getInsertedID();
    if (is_uploaded_file($_FILES['image']['tmp_name'])) {
      $filename = 'banner'.$item['id'].substr($_FILES['image']['name'], strrpos($_FILES['image']['name'], '.'));
      upload('image', filesRoot.'data/'.$this->name.'/'.$filename);
      $item['image'] = $filename;
      $db->updateItem($this->table['name'], $item, "`id`='".$item['id']."'");
    }
    sendNotify('�������� ������: '.$item['caption']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
    global $db, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $old_file = $item['image'];
    $item = GetArgs($item);
    $item['section'] = ':'.implode(':', arg('section')).':';
    if ($item['showTill'] == '') unset($item['showTill']);
    if (arg('flushShowCount')) $item['shows'] = 0;
    if (is_uploaded_file($_FILES['image']['tmp_name'])) {
      $path = filesRoot.'data/'.$this->name.'/';
      if (is_file($path.$old_file)) unlink($path.$old_file);
      $filename = 'banner'.$item['id'].substr($_FILES['image']['name'], strrpos($_FILES['image']['name'], '.'));
      upload('image', $path.$filename);
      $item['image'] = $filename;
    }
    $db->updateItem($this->table['name'], $item, "`id`='".$item['id']."'");
    sendNotify('������� ������: '.$item['caption']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function toggle($id)
  {
    global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$id."'");
    $item['active'] = !$item['active'];
    $db->updateItem($this->table['name'], $item, "`id`='".$id."'");
    sendNotify(($item['active']?admActivated:admDeactivated).': '.'<a href="'.str_replace('toggle','id',$request['url']).'">'.$item['caption'].'</a>', array('title'=>$this->title));
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete($id)
  {
    global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$id."'");
    $path = dataFiles.$this->name.'/';
    if (!empty($item['image']) && file_exists($path.$item['image'])) unlink($path.$item['image']);
    sendNotify(admDeleted.': '.'<a href="'.str_replace('delete','id',$request['url']).'">'.$item['caption'].'</a>', array('title'=>$this->title));
    parent::delete($id);
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function create()
  {
    global $page, $db;

    $sections = array(array(), array());
    $sections = $this->menuBrunch();
    array_unshift($sections[0], '��� �������');
    array_unshift($sections[1], 'all');
    $form = array(
      'name' => 'formCreate',
      'caption' => '�������� ������',
      'width' => '95%',
      'fields' => array (
        array ('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '<b>���������</b>', 'width' => '100%', 'maxlength' => '255', 'pattern'=>'/.+/', 'errormsg'=>'��������� �� ����� ���� ������!'),
        array ('type' => 'listbox', 'name' => 'section', 'label' => '<b>�������</b>', 'height'=> 5,'items'=>$sections[0], 'values'=>$sections[1]),
        array ('type' => 'edit', 'name' => 'block', 'label' => '<b>���� �������</b>', 'width' => '100px', 'maxlength' => 31, 'comment' => '��� ������� ������� ����������� ������ <b>$(Banners:���_�����)</b>','pattern'=>'/.+/', 'errormsg'=>'�� ������ ���� �������!'),
        array ('type' => 'edit', 'name' => 'priority', 'label' => '���������', 'width' => '20px', 'comment' => '���� ��� ������ ������� � ������ ����� ������ ��������� ��������, ����� ������� � ������� �����������', 'default'=>0, 'pattern'=>'/\d+/', 'errormsg'=>'��������� �������� ������ �������!'),
        array ('type' => 'edit', 'name' => 'showFrom', 'label' => '������ �������', 'width' => '100px', 'comment' => '����-��-��', 'default'=>gettime('Y-m-d'), 'pattern'=>'/[12]\d{3,3}-[01]\d-[0-3]\d/', 'errormsg'=>'������������ ������ ����!'),
        array ('type' => 'edit', 'name' => 'showTill', 'label' => '����� �������', 'width' => '100px', 'comment' => '����-��-��; ������ - ��� �����������', 'pattern'=>'/([12]\d{3,3}-[01]\d-[0-3]\d)|(^$)/', 'errormsg'=>'������������ ������ ����!'),
        array ('type' => 'edit', 'name' => 'showCount', 'label' => '����. ���-�� �������', 'width' => '100px', 'comment' => '0 - ��� �����������', 'default'=>0, 'pattern'=>'/(\d+)|(^$)/', 'errormsg'=>'���-�� ������� �������� ������ �������!'),
        array ('type' => 'edit', 'name' => 'mail', 'label' => 'e-mail ���������', 'width' => '200px', 'maxlength' => '63'),
        array ('type' => 'checkbox', 'name' => 'active', 'label' => '������������', 'default' => true),
        array ('type' => 'header', 'value' => '�������� �������'),
        array ('type' => 'file', 'name' => 'image', 'label' => '�������� ��� Flash', 'width'=>'50'),
        array ('type' => 'edit', 'name' => 'width', 'label' => '������', 'width' => '100px', 'comment'=>'������ ��� Flash'),
        array ('type' => 'edit', 'name' => 'height', 'label' => '������', 'width' => '100px', 'comment'=>'������ ��� Flash'),
        array ('type' => 'edit', 'name' => 'url', 'label' => 'URL ��� ������', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'select', 'name' => 'target', 'label' => '���������', 'items'=>array('� ����� ����', '� ��� �� ����')),
        array ('type' => 'header', 'value' => 'HTML-��� �������'),
        array ('type' => 'memo', 'name' => 'html', 'label' => 'HTML-��� (���� ����� HTML-���, �� ���������� �������� ������������ � ����� �� �����������)', 'height' => '4'),
      ),
      'buttons' => array('ok', 'cancel'),
    );

    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function edit()
  {
  global $page, $db, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['id']."'");
    $item['section'] = explode(':', $item['section']);
    $sections = array(array(), array());
    $sections = $this->menuBrunch();
    array_unshift($sections[0], '��� �������');
    array_unshift($sections[1], 'all');
    $form = array(
      'name' => 'formEdit',
      'caption' => '�������� ������',
      'width' => '95%',
      'fields' => array (
        array ('type' => 'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '<b>���������</b>', 'width' => '100%', 'maxlength' => '255', 'pattern'=>'/.+/', 'errormsg'=>'��������� �� ����� ���� ������!'),
        array ('type' => 'listbox', 'name' => 'section', 'label' => '<b>�������</b>', 'height'=> 5,'items'=>$sections[0], 'values'=>$sections[1]),
        array ('type' => 'edit', 'name' => 'block', 'label' => '<b>���� �������</b>', 'width' => '100px', 'maxlength' => 15, 'comment' => '��� ������� ������� ����������� ������ <b>$(Banners:���_�����)</b>','pattern'=>'/.+/', 'errormsg'=>'�� ������ ���� �������!'),
        array ('type' => 'edit', 'name' => 'priority', 'label' => '���������', 'width' => '20px', 'comment' => '���� ��� ������ ������� � ������ ����� ������ ��������� ��������, ����� ������� � ������� �����������', 'default'=>0, 'pattern'=>'/\d+/', 'errormsg'=>'��������� �������� ������ �������!'),
        array ('type' => 'edit', 'name' => 'showFrom', 'label' => '������ �������', 'width' => '100px', 'comment' => '����-��-��', 'default'=>gettime('Y-m-d'), 'pattern'=>'/[12]\d{3,3}-[01]\d-[0-3]\d/', 'errormsg'=>'������������ ������ ����!'),
        array ('type' => 'edit', 'name' => 'showTill', 'label' => '����� �������', 'width' => '100px', 'comment' => '����-��-��; ������ - ��� �����������', 'pattern'=>'/(\d{4,4}-[01]\d-[0-3]\d)|(^$)/', 'errormsg'=>'������������ ������ ����!'),
        array ('type' => 'edit', 'name' => 'showCount', 'label' => '����. ���-�� �������', 'width' => '100px', 'comment' => '0 - ��� �����������', 'default'=>0, 'pattern'=>'/(\d+)|(^$)/', 'errormsg'=>'���-�� ������� �������� ������ �������!'),
        array ('type' => 'edit', 'name' => 'mail', 'label' => 'e-mail ���������', 'width' => '200px', 'maxlength' => '63'),
        array ('type' => 'checkbox', 'name' => 'active', 'label' => '������������'),
        array ('type' => 'header', 'value' => '�������� �������'),
        array ('type' => 'file', 'name' => 'image', 'label' => '�������� ��� Flash', 'width'=>'50', 'comment' => '<a></a>'),
        array ('type' => 'edit', 'name' => 'width', 'label' => '������', 'width' => '100px', 'comment'=>'������ ��� Flash'),
        array ('type' => 'edit', 'name' => 'height', 'label' => '������', 'width' => '100px', 'comment'=>'������ ��� Flash'),
        array ('type' => 'edit', 'name' => 'url', 'label' => 'URL ��� ������', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'select', 'name' => 'target', 'label' => '���������', 'items'=>array('� ����� ����', '� ��� �� ����')),
        array ('type' => 'header', 'value' => 'HTML-��� �������'),
        array ('type' => 'memo', 'name' => 'html', 'label' => 'HTML-��� (���� ����� HTML-���, �� ���������� �������� ������������ � ����� �� �����������)', 'height' => '4'),
        array ('type' => 'divider'),
        array ('type' => 'checkbox', 'name' => 'flushShowCount', 'label' => '�������� ���-�� �������'),
/*        array ('type' => 'edit', 'name' => 'caption', 'label' => '���������', 'width' => '100%', 'maxlength' => '255', 'pattern'=>'/.+/', 'errormsg'=>'��������� �� ����� ���� ������!'),
        array ('type' => 'listbox', 'name' => 'section[]', 'label' => '�������', 'height'=> 5,'items'=>$sections[0], 'values'=>$sections[1]),
        array ('type' => 'edit', 'name' => 'priority', 'label' => '���������', 'width' => '20px', 'comment' => '������� �������� - ������� ���������', 'pattern'=>'/\d+/', 'errormsg'=>'��������� �������� ������ �������!'),
        array ('type' => 'edit', 'name' => 'block', 'label' => '����', 'width' => '100px', 'maxlength' => 31),
        array ('type' => 'edit', 'name' => 'showFrom', 'label' => '������ �������', 'width' => '100px', 'comment' => '����-��-��', 'value'=>gettime('Y-m-d'), 'pattern'=>'/[12]\d{3,3}-[01]\d-[0-3]\d/', 'errormsg'=>'������������ ������ ����!'),
        array ('type' => 'edit', 'name' => 'showTill', 'label' => '����� �������', 'width' => '100px', 'comment' => '����-��-��; ������ - ��� �����������', 'pattern'=>'/(\d{4,4}-[01]\d-[0-3]\d)|(^$)/', 'errormsg'=>'������������ ������ ����!'),
        array ('type' => 'edit', 'name' => 'showCount', 'label' => '����. ���-�� �������', 'width' => '100px', 'comment' => '0 - ��� �����������', 'value'=>0, 'pattern'=>'/(\d+)|(^$)/', 'errormsg'=>'���-�� ������� �������� ������ �������!'),
        array ('type' => 'memo', 'name' => 'html', 'label' => 'HTML-���', 'height' => '4'),
        array ('type' => 'file', 'name' => 'image', 'label' => '��������', 'width'=>'50'),
        array ('type' => 'edit', 'name' => 'url', 'label' => 'URL ��� ������', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'select', 'name' => 'target', 'label' => '���������', 'items'=>array('� ����� ����', '� ��� �� ����')),
        array ('type' => 'checkbox', 'name' => 'active', 'label' => '������������'),
        array ('type' => 'edit', 'name' => 'mail', 'label' => 'e-mail ���������', 'width' => '200px', 'maxlength' => '63'),
*/      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );

    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $db, $page, $user, $request, $session;

    $result = '';
    if (isset($request['arg']['id'])) {
      $item = $db->selectItem($this->table['name'], "`".$this->table['key']."` = '".$request['arg']['id']."'");
      $page->title .= empty($item['caption'])?'':' - '.$item['caption'];
    }
    if (isset($request['arg']['update']) && isset($this->table['controls']['edit'])) {
      if (method_exists($this, 'update')) $result = $this->update(); else $session['errorMessage'] = sprintf(errMethodNotFound, 'update', get_class($this));
    } elseif (isset($request['arg']['toggle']) && isset($this->table['controls']['toggle'])) {
      if (method_exists($this, 'toggle')) $result = $this->toggle($request['arg']['toggle']); else $session['errorMessage'] = sprintf(errMethodNotFound, 'toggle', get_class($this));
    } elseif (isset($request['arg']['delete']) && isset($this->table['controls']['delete'])) {
      if (method_exists($this, 'delete')) $result = $this->delete($request['arg']['delete']); else $session['errorMessage'] = sprintf(errMethodNotFound, 'delete', get_class($this));
    } elseif (isset($request['arg']['id']) && isset($this->table['controls']['edit'])) {
      if (method_exists($this, 'edit')) $result = $this->edit(); else $session['errorMessage'] = sprintf(errMethodNotFound, 'edit', get_class($this));
    } elseif (isset($request['arg']['action'])) switch ($request['arg']['action']) {
      case 'create': $result = $this->create(); break;
      case 'insert':
        if (method_exists($this, 'insert')) $result = $this->insert();
        else $session['errorMessage'] = sprintf(errMethodNotFound, 'insert', get_class($this));
      break;
    } else {
      $result = $page->renderTable($this->table);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  /*function settings()
  {
  global $page;

    $form = array(
      'name'=>'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'select','name'=>'displayMode','label'=>'�����', 'items'=>array('�������� ������ $(plgBanners)', '������������ SideBars')),
        array('type'=>'edit','name'=>'caption','label'=>'���������','width'=>'100px'),
        array('type'=>'select','name'=>'SideBarsPanel','label'=>'������� SideBars', 'values'=>array('left', 'right'), 'items'=>array('�����', '������')),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }*/
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminOnMenuRender()
  {
  global $page;

    $page->addMenuItem(admExtensions, array ('access'  => EDITOR, 'link'  => $this->name, 'caption'  => $this->title, 'hint'  => $this->description));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
  global $db, $page, $request;

    if (arg('banners-click')) {
      $item = $db->selectItem($this->name, "`id`='".arg('banners-click')."'");
      $item['clicks']++;
      $db->updateItem($this->name, $item, "`id`='".$item['id']."'");
      goto($item['url']);
      exit;
    } else {
      # ���� ��� ����� ������ ��������
      preg_match_all('/\$\(Banners:([^)]+)\)/', $text, $blocks, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
      $delta = 0;
      foreach($blocks as $block) {
        $sql = "(`active`=1) AND (`section` LIKE '%:".$page->id.":%' OR `section` LIKE '%:all:%') AND (`block`='".$block[1][0]."') AND (`showFrom`<='".gettime()."') AND (`showCount`=0 OR (`shows` < `showCount`)) AND (`showTill` = '0000-00-00' OR `showTill` IS NULL OR `showTill` > '".gettime()."')";
        # �������� ������� ��� ����� ����� � ������� ���������� ����������
        $items = $db->select($this->name, $sql, '`priority`', true);
        if (count($items)) {
          # �������� ������� � ������ �����������
          $priority = $items[0]['priority'];
          for($i=0; $i<count($items); $i++) if ($items[$i]['priority'] != $priority) {
            $items = array_slice($items, 0, $i);
            break;
          }
          # �������� ��������� ������
          $item = $items[mt_rand(0, count($items)-1)];
          if (empty($item['html'])) {
            if (substr(strtolower($item['image']), -4) == '.swf') {
              $banner =
              '<object type="application/x-shockwave-flash" data="'.dataRoot.$this->name.'/'.$item['image'].'" width="'.$item['width'].'" height="'.$item['height'].'">
                <param name="movie" value="'.dataRoot.$this->name.'/'.$item['image'].'" />
                <param name="quality" value="high" />
              </object>';
            } else {
              $banner = img(dataRoot.$this->name.'/'.$item['image']);
              if (!empty($item['url'])) $banner = '<a href="'.$request['path'].execScript.'?banners-click='.$item['id'].'"'.($item['target']?'':' target="_blank"').'>'.$banner.'</a>';
            }
          } else {
            $banner = StripSlashes($item['html']);
          }
          $item['shows']++;
          $db->updateItem($this->name, $item, "`id`='".$item['id']."'");
          $text = substr_replace($text, $banner, $block[0][1]+$delta, strlen($block[0][0]));
          $delta += strlen($banner) - strlen($block[0][0]);
        }
      }
      $items = $db->select($this->table['name'], "(`showCount` != 0 AND `shows` > `showCount`) AND ((`showTill` < '".gettime()."') AND (`showTill` != '0000-00-00'))");
      if (count($items)) {
        foreach($items as $item) {
          sendMail($item['mail'], '��� ������ �������������', '��� ������ "'.$item['caption'].' ��� ��������, �.�. ��� ��� ��������� ���������� ������� ���� ���� ������."');
          sendMail(getOption('sendNotifyTo'), '������ �������������', '������ "'.$item['caption'].' ��� �������� �������� ���������� ������."');
        }
        $db->update($this->table['name'], "`active`='0'", "(`showCount` != 0 AND `shows` > `showCount`) AND ((`showTill` < '".gettime()."') AND (`showTill` != '0000-00-00'))");
      }
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>
