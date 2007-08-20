<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Vars (CMS Eresus� Plugin)
# � 2006-2007, ProCreat Systems
# Web: http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TVars extends TListContentPlugin {
  var 
    $name = 'vars',
    $title = 'Vars',
    $type = 'client,admin',
    $version = '1.04',
    $description = '�������� ����������� ��������� ����������',
    $settings = array(
    );
  var $table = array (
    'name' => 'vars',
    'key'=> 'name',
    'sortMode' => 'caption',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'caption', 'caption' => '����������'),
      array('name' => 'name', 'caption' => '���', 'value' => '&#36;($(name))', 'macros' => true),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>strAdd, 'name'=>'action', 'value'=>'create')
      ),
    ),
    'sql' => "(
      `name` varchar(31) NOT NULL,
      `caption` varchar(63) NOT NULL,
      `value` text NOT NULL,
      PRIMARY KEY  (`name`)
    ) TYPE=MyISAM;",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TVars()
  # ���������� ����������� ������������ �������
  {
    global $plugins;
  
    parent::TPlugin();
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
    $db->insert($this->table['name'], $item);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function update()
  {
    global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`name`='".$request['arg']['update']."'");
    $item = GetArgs($item);
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
      'caption' => '�������� ����������',
      'width'=>'500px',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '���������� ', 'width' => '100%', 'maxlength' => '63', 'pattern' => '/.+/', 'errormsg' => '�� ������� �������� ����������'),
        array ('type' => 'edit', 'name' => 'name', 'label' => '��� $(', 'width' => '300px', 'maxlength' => '31', 'comment' => ')', 'pattern' => '/.+/', 'errormsg' => '�� ������� ��� ����������'),
        array ('type' => 'memo', 'name' => 'value', 'label' => '��������', 'height' => '10'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
    global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`name`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'EditForm',
      'caption' => '�������� ����������',
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['name']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '���������� ', 'width' => '100%', 'maxlength' => '63', 'pattern' => '/.+/', 'errormsg' => '�� ������� �������� ����������'),
        array ('type' => 'edit', 'name' => 'name', 'label' => '��� $(', 'width' => '300px', 'maxlength' => '31', 'comment' => ')', 'pattern' => '/.+/', 'errormsg' => '�� ������� ��� ����������'),
        array ('type' => 'memo', 'name' => 'value', 'label' => '��������', 'height' => '10'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
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
    global $db;
    
    $items = $db->select($this->table['name']);
    if (count($items)) foreach ($items as $item) {
      $text= str_replace('$('.$item['name'].')', $item['value'], $text);
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminOnMenuRender()
  {
    global $page;
  
    $page->addMenuItem('����������', array ("access"  => EDITOR, "link"  => $this->name, "caption"  => '����������', "hint"  => "���������� ���������� �����������"));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>