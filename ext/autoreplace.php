<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# AutoReplace (CMS Eresus� Plugin)
# � 2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TAutoReplace extends TListContentPlugin {
  var 
    $name = 'autoreplace',
    $title = '����������',
    $type = 'client,admin',
    $version = '0.02',
    $description = '���������� ���������� ��������',
    $settings = array(
    );
  var $table = array (
    'name' => 'autoreplace',
    'key'=> 'id',
    'sortMode' => 'position',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'caption', 'caption' => '������'),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'position' => '',
      'toggle' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>strAdd, 'name'=>'action', 'value'=>'create')
      ),
    ),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `active` tinyint(1) unsigned NOT NULL default '1',
      `position` int(10) unsigned default NULL,
      `caption` varchar(255) default '',
      `src` varchar(255) default '',
      `dst` varchar(255) default '',
      `regexp` tinyint(1) default '0',
      PRIMARY KEY  (`id`),
      KEY `active` (`active`),
      KEY `position` (`position`)
    ) TYPE=MyISAM;",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TAutoReplace()
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

    $item = GetArgs($db->fields($this->table['name']), array('regexp'));
    $db->insert($this->table['name'], $item);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $item = GetArgs($item, array('regexp'));
    $item['src'] = AddSlashes($item['src']);
    $db->updateItem($this->table['name'], $item, "`id`='".$request['arg']['update']."'");
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
      'width'=>'100%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '��������', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'edit', 'name' => 'src', 'label' => '��� ��������', 'width' => '100%', 'maxlength' => '255', 'pattern' => '/.+/', 'errormsg' => '�� ������ ������� ����� � ���� "��� ��������"'),
        array ('type' => 'checkbox', 'name' => 'regexp', 'label' => '���������� ���������'),
        array ('type' => 'edit', 'name' => 'dst', 'label' => '�� ��� ��������', 'width' => '100%', 'maxlength' => '255'),
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

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'EditForm',
      'caption' => '������������� ����������',
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '��������', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'edit', 'name' => 'src', 'label' => '��� ��������', 'width' => '100%', 'maxlength' => '255', 'pattern' => '/.+/', 'errormsg' => '�� ������ ������� ����� � ���� "��� ��������"'),
        array ('type' => 'checkbox', 'name' => 'regexp', 'label' => '���������� ���������'),
        array ('type' => 'edit', 'name' => 'dst', 'label' => '�� ��� ��������', 'width' => '100%', 'maxlength' => '255'),
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
    
    $items = $db->select($this->table['name'], '`active`=1', $this->table['sortMode'], $this->table['sortDesc']);
    if (count($items)) foreach ($items as $item) {
      if ($item['regexp'])
        $text = preg_replace(StripSlashes($item['src']), $item['dst'], $text);
      else
        $text = str_replace(StripSlashes($item['src']), $item['dst'], $text);
    }
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminOnMenuRender()
  {
  global $page;
  
    $page->addMenuItem('����������', array ("access"  => EDITOR, "link"  => $this->name, "caption"  => $this->title, "hint"  => $this->description));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>