<?php
/**
* �������� ���������� ���� �����
*
* Eresus 2, PHP 4,5 
*
* � 2005-2007, ProCreat Systems, http://procreat.ru/
* � 2007, Eresus Group, http://eresus.ru/
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @author: dkDimon <dkdimon@mail.ru>
*
* @version: 1.03
* @modified: 2007-08-22
*/

class TForms extends TListContentPlugin {
  var $name = 'forms';
  var $type = 'client,admin';
  var $title = '����� �����';
  var $version = '1.03';
  var $kernel = '2.10';
  var $description = '�������� ����������� ���� �����';
  var $table = array (
    'name' => 'forms',
    'key'=> 'id',
    'sortMode' => 'caption',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'name', 'caption' => '���'),
      array('name' => 'caption', 'caption' => '��������'),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'toggle' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>'����� �����', 'name'=>'action', 'value'=>'create')
      ),
    ),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `active` tinyint(1) unsigned NOT NULL default '1',
      `name` varchar(63) NOT NULL default '',
      `caption` varchar(100) NOT NULL default '',
      `actionMode` varchar(15) default NULL,
      `actionValue` varchar(255) default NULL,
      `form` text default NULL,
      PRIMARY KEY  (`id`),
      KEY `active` (`active`),
      KEY `name` (`name`),
      KEY `caption` (`caption`)
    ) TYPE=MyISAM;",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TForms()
  {
    global $plugins, $request;
  
    parent::TListContentPlugin();
    if (isset($request['arg']['FormsActionMailto'])) $this->sendMail($request['arg']['FormsActionMailto']);
    $plugins->events['clientOnPageRender'][] = $this->name;
    $plugins->events['adminOnMenuRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function sendMail($form)
  {
    global $request, $db, $plugins;

    $item = $db->selectItem($this->table['name'], "`name`='".$form."' AND `active`='1'");
    if (!is_null($item)) {
      $form = '$form = array('.$item['form'].');';
      eval($form);

      $letter = '';
      $subject = $item['caption'];
      if (count($form['fields'])) foreach($form['fields'] as $field) {
        switch ($field['type']) {
          case 'text':; case 'header':; break;
          case 'checkbox': 
						if (isset($request['arg'][$field['name']])) $letter .= $field['label']."\n"; 
					break;
          case 'listbox': 
            $letter .= (isset($request['arg'][$field['name']]) ? $field['label'].': '.implode(', ', $request['arg'][$field['name']]):'')."\n" ;
					break;
          default:
            $letter .= $field['label'].': '.$request['arg'][$field['name']]."\n";
          break;
        }
      }
      if (isset($plugins->items['vistat'])) $letter .= "\n\n--\n".$plugins->items['vistat']->dumpStats();
      sendMail($item['actionValue'], $subject, $letter);
    }
    InfoMessage(isset($form['message'])?$form['message']:'������ ����� ����������.');
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminInstallScripts()
  {
    global $page;
    $page->scripts .= "
      function ".$this->name."ActionCnange(oSender)
      {
        var Row = oSender.parentNode.offsetParent.rows[oSender.parentNode.parentNode.rowIndex+1];
        oSender.form.actionValue.disabled = oSender.value == 'none';
        switch (oSender.value) {
          case 'none': Row.cells[0].innerHTML = ''; break;
          case 'action': Row.cells[0].innerHTML = 'URL'; break;
          case 'mailto': Row.cells[0].innerHTML = 'E-mail'; break;
        }
      }
    ";
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
    global $db, $request;

    $item = getArgs($db->fields($this->table['name']));
    $db->insert($this->table['name'], $item);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function update()
  {
    global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`name`='".$request['arg']['update']."' AND `active`='1'");
    $item = setArgs($item);
    $item['form'] = $item['form'];
    $db->updateItem($this->table['name'], $item, "`name`='".$request['arg']['update']."'");
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminAddItem()
  {
    global $page, $request;

    $form = array(
      'name' => 'AddForm',
      'caption' => '����� �����',
      'width'=>'100%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'edit', 'name' => 'name', 'label' => '���', 'width' => '200px', 'maxlength' => '63', 'comment' => '(��������� �����, �����, "_")'),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '��������', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'select', 'name' => 'actionMode', 'label' => '��� ��������', 'items'=>array('�� ��������� ��������','������� ������','��������� ������'), 'values'=>array('none','action','mailto'), 'extra'=>'onchange="'.$this->name.'ActionCnange(this)"'),
        array ('type' => 'edit', 'name' => 'actionValue', 'label' => '', 'width' => '100%', 'maxlength' => 255, 'disabled'=>true),
        array ('type' => 'memo', 'name' => 'form', 'label' => '�����', 'height' => '20'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    $this->adminInstallScripts();
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
    global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`".$this->table['key']."`='".$request['arg']['id']."'");
    switch($item['actionMode']) {
      case 'action': $actionLabel = 'URL'; break;
      case 'mailto': $actionLabel = 'E-mail'; break;
      default: $actionLabel = '';
    }
    $form = array(
      'name' => 'EditForm',
      'caption' => '�������� �����',
      'width' => '100%',
      'fields' => array (
        array ('type' => 'hidden','name' => 'update', 'value'=>$item['name']),
        array ('type' => 'edit', 'name' => 'name', 'label' => '���', 'width' => '200px', 'maxlength' => '63', 'comment' => '(��������� �����, �����, "_")'),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '��������', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'select', 'name' => 'actionMode', 'label' => '��� ��������', 'items'=>array('�� ��������� ��������','������� ������','��������� ������'), 'values'=>array('none','action','mailto'), 'extra'=>'onchange="'.$this->name.'ActionCnange(this)"'),
        array ('type' => 'edit', 'name' => 'actionValue', 'label' => $actionLabel, 'width' => '100%', 'maxlength' => 255, 'disabled'=>$item['actionMode'] == 'none'),
        array ('type' => 'memo', 'name' => 'form', 'label' => '�����', 'height' => '20'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $this->adminInstallScripts();
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
    return $this->adminRenderContent();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnPageRender($text)
  {
    global $db, $page;
    
    preg_match_all('/\$\(Forms:([\d\w_]+)\)/i', $text, $matches);
    for($i = 0; $i < count($matches[0]); $i++) {
      $item = $db->selectItem($this->table['name'], "`name`='".$matches[1][$i]."' AND `active`='1'");
      if (!is_null($item)) {
        $form = '$form = array('.$item['form'].');';
        eval(StripSlashes($form));
        switch ($item['actionMode']) {
          case 'action': $form['action'] = $item['actionValue']; break;
          case 'mailto': array_unshift($form['fields'], array('type'=>'hidden', 'name'=>'FormsActionMailto', 'value'=>$item['name'])); break;
        }
        $text = str_replace($matches[0][$i], $page->renderForm($form), $text);
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