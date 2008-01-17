<?php
/**
 * ���������� ������
 *
 * Eresus 2
 * 
 * ������ ������������ ���������� �� ����� ������, � ��������� �� ����������
 *
 * � 2007, Eresus Group, http://eresus.ru/
 *
 * @version: 1.00
 * @modified: 2007-09-21
 * 
 * @author: Mikhail Krasilnikov <mk@procreat.ru>
 */

class Files extends ContentPlugin {
  var $version = '1.00a';
  var $kernel = '2.10b2';
  var $title = '�����';
  var $description = '���������� ������';
  var $type = 'client,content,ondemand';
  var $settings = array(
  	'icons' => "catalog.gif=doc\nexcel.gif=xls",
  );
  /**
   * ����������� �������
   */
  function install()
  {
    parent::install();
    $this->dbCreateTable("
			`id` int(10) unsigned NOT NULL auto_increment,
		  `section` int(10) unsigned default NULL,
		  `position` int(10) unsigned default 0,
		  `caption` varchar(127) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `section` (`section`),
		  KEY `position` (`position`)
  	", 'sections');
    $this->dbCreateTable("
			`id` int(10) unsigned NOT NULL auto_increment,
		  `owner` int(10) unsigned default NULL,
		  `caption` varchar(255) default NULL,
		  `position` int(10) unsigned default 0,
		  `size` int(10) unsigned default 0,
		  `filename` varchar(255) default NULL,
		  PRIMARY KEY  (`id`),
		  KEY `owner` (`owner`),
		  KEY `position` (`position`)
		", 'files');
    $this->mkdir();
  }
  //------------------------------------------------------------------------
  /**
   * ������������� �������
   *
   */
  function uninstall()
  {
  	$this->rmdir();
  	parent::uninstall();
  }
  //------------------------------------------------------------------------
  /**
   * ���������� ������ ��������
   *
   * @param int $section ������ �����
   * 
   * @return array ������ ��������
   */
  function sectionEnum($section)
  {
  	$result = $this->dbSelect('sections', "`section` = '$section'", 'position');
  	return $result;
  }
  //------------------------------------------------------------------------
  /**
   * ������ ����� ������
   *
   * @param int $section ������ �����
   * @param string $caption  �������� �������
   */
  function sectionCreate($section, $caption)
  {
  	$result = $this->dbSelect('sections', "`section` = '$section'", 'position');
  	$item = array(
  		'section' => $section,
  		'caption' => $caption,
  		'position' => count($result) ? $result[count($result)-1]['position'] + 1 : 0
  	);
  	$result = $this->dbInsert('sections', $item);
  	return $result;
  }
  //------------------------------------------------------------------------
  /**
   * ���������� ������ ����� � ������
   *
   * @param int $id ������������� �������
   */
  function sectionUp($id)
  {
  	$item = $this->dbItem('sections', $id);
  	if ($item['position']) {
  		$this->dbUpdate('sections', "`position` = `position` + 1", "`section`={$item['section']} AND `position` = ".($item['position']-1));
  		$item['position']--;
  		$this->dbUpdate('sections', $item);
  	}
  }
  //------------------------------------------------------------------------  
  /**
   * ���������� ������ ���� � ������
   *
   * @param int $id ������������� �������
   */
  function sectionDown($id)
  {
  	$item = $this->dbItem('sections', $id);
  	if ($item['position'] < $this->dbCount('sections', "`section`={$item['section']}") - 1) {
  		$this->dbUpdate('sections', "`position` = `position` - 1", "`section`={$item['section']} AND `position` = ".($item['position']+1));
  		$item['position']++;
  		$this->dbUpdate('sections', $item);
  	}
  }
  //------------------------------------------------------------------------  
  /**
   * ���������� ������ ���������� �������
   *
   * @return string  ������ ���������� �������
   */
  function sectionAddDialog()
  {
  	global $page;
  	
  	$form = array(
  		'name' => 'AddDialog',
  		'caption' => '����� ���������',
  		'width' => '500px',
  		'fields' => array(
  			array('type' => 'hidden', 'name' => 'action', 'value' => 'section_insert'),
  			array('type' => 'edit', 'name' => 'caption', 'label' => '��������', 'width' => '100%', 'maxlength' => 127),
  		),
  		'buttons' => array('ok', 'cancel'),
  	);
  	$result = $page->renderForm($form);
  	return $result;
  }
  //------------------------------------------------------------------------
  /**
   * ���������� ������ ������
   *
   * @param int $owner ������������� ����������
   * 
   * @return array ������ ������
   */
  function filesEnum($owner)
  {
  	$result = $this->dbSelect('files', "`owner` = '$owner'", 'position');
  	return $result;
  }
  //------------------------------------------------------------------------  
  /**
   * ���������� ������ ���������� �����
   *
   * @param int $owner  ������������� ������������� �������
   * @return string  ������ ���������� �����
   */
  function fileAddDialog($owner)
  {
  	global $page;
  	
  	$form = array(
  		'name' => 'AddDialog',
  		'caption' => '����� ����',
  		'width' => '500px',
  		'fields' => array(
  			array('type' => 'hidden', 'name' => 'action', 'value' => 'file_insert'),
  			array('type' => 'hidden', 'name' => 'owner', 'value' => $owner),
  			array('type' => 'file', 'name' => 'file', 'label' => '����', 'width' => 50, 'pattern' => '/.+/', 'errormsg' => '�� �� ������� ����!'),
  			array('type' => 'edit', 'name' => 'caption', 'label' => '��������', 'width' => '100%', 'maxlength' => 255, 'pattern' => '/.+/', 'errormsg' => '�������� �� ����� ���� ������!'),
  			),
  		'buttons' => array('ok', 'cancel'),
  	);
  	$result = $page->renderForm($form);
  	return $result;
  }
  //------------------------------------------------------------------------
  /**
   * ��������� ����� ����
   */
  function fileAdd()
  {
  	$result = $this->dbSelect('files', "`owner` = '".arg('owner')."'", 'position');
  	$item = array(
  		'owner' => arg('owner'),
  		'position' => count($result) ? $result[count($result)-1]['position'] + 1 : 0,
  		'caption' => arg('caption'),
  		'size' => $_FILES['file']['size'],
  		'filename' => $_FILES['file']['name'],
  	);
  	$item['id'] = $this->dbInsert('files', $item);
  	if ($item['id']) {
  		if (upload('file', $this->dirData.$item['id'])) $result = true;
  		else {
  			$this->dbDelete('files', $item);
  			$result = false;
  		}
  	}
  	
  	return $result > 0;
  }
  //------------------------------------------------------------------------
  /**
   * ���������� ���� ����� � ������
   *
   * @param int $id ������������� �����
   */
  function fileUp($id)
  {
  	$item = $this->dbItem('files', $id);
  	if ($item['position']) {
  		$this->dbUpdate('files', "`position` = `position` + 1", "`owner`={$item['owner']} AND `position` = ".($item['position']-1));
  		$item['position']--;
  		$this->dbUpdate('files', $item);
  	}
  }
  //------------------------------------------------------------------------  
  /**
   * ���������� ���� ���� � ������
   *
   * @param int $id ������������� �����
   */
  function fileDown($id)
  {
  	$item = $this->dbItem('files', $id);
  	if ($item['position'] < $this->dbCount('files', "`owner`={$item['owner']}") - 1) {
  		$this->dbUpdate('files', "`position` = `position` - 1", "`owner`={$item['owner']} AND `position` = ".($item['position']+1));
  		$item['position']++;
  		$this->dbUpdate('files', $item);
  	}
  }
  //------------------------------------------------------------------------  
  /**
   * ������� ����
   *
   * @param int $id ������������� �����
   */
  function fileDelete($id)
  {
  	$item = $this->dbItem('files', $id);
  	filedelete($this->dirData.$item['id']);
  	$this->dbDelete('files', $item);
  	$this->dbUpdate('files', "`position` = `position` - 1", "`owner`={$item['owner']} AND `position` > ".($item['position']));
  }
  //------------------------------------------------------------------------  
  /**
   * ��������� ������ ������
   * 
   * @param int $section  ������ �����
   *
   * @return string �������
   */
  function adminRenderList($section)
  {
  	global $page;
  	
		$result = '';
		$tabs = array(
			'items' => array(
				array('caption' => '�������� ������', 'name' => 'action', 'value' => 'section_add'),
			),
		);
		$result .= $page->renderTabs($tabs);
		$table = array(
	    'key'=> 'id',
	    'sortMode' => 'position',
	    'sortDesc' => false,
	    'columns' => array(
	      array('name' => 'caption', 'caption' => '��������'),
	      #array('name' => 'size', 'caption' => '������', 'align' => 'right'),
	      array('name' => 'filename', 'caption' => '��� �����'),
	      ),
	    'controls' => array (
	      'delete' => '',
	      #'edit' => '',
	      'position' => '',
	    ),
		);
		$sections = $this->sectionEnum($section);
		for ($i=0; $i < count($sections); $i++) {
			$result .= "<br /><div><b>{$sections[$i]['caption']}</b>
				<a href=\"".$page->url(array('action' => 'file_add', 'owner' => $sections[$i]['id']))."\" title=\"�������� ���� � ���� ������\">[+]</a>
				<a href=\"".$page->url(array('action' => 'section_up', 'id' => $sections[$i]['id']))."\" title=\"����������� ����\">[&uarr;]</a>
				<a href=\"".$page->url(array('action' => 'section_down', 'id' => $sections[$i]['id']))."\" title=\"����������� ����\">[&darr;]</a>
				<a href=\"".$page->url(array('action' => 'section_delete', 'id' => $sections[$i]['id']))."\" title=\"������� ������ � ��� ��� �����\">[-]</a></div>";
			$files = $this->filesEnum($sections[$i]['id']);
			if (count($files)) $result .= $page->renderTable($table, $files, 'file_'); 
		}
		return $result;
  }
  //------------------------------------------------------------------------
  /**
	 * ��������� ���������������� �����
	 *
	 * @return  string  �������
	 */
	function adminRenderContent()
	{
		global $page, $Eresus;

		$action = arg('action');
		if (arg('file_up')) $action = 'file_up';
		if (arg('file_down')) $action = 'file_down';
		if (arg('file_delete')) $action = 'file_delete';
		#if (arg('file_id')) $action = 'file_delete';
		
		switch ($action) {
			case 'section_add': $result = $this->sectionAddDialog(); break;
			case 'section_insert': $this->sectionCreate($page->id, arg('caption')); goto(arg('submitURL')); break;
			case 'section_up': $this->sectionUp(arg('id')); goto($Eresus->request['referer']); break;
			case 'section_down': $this->sectionDown(arg('id')); goto($Eresus->request['referer']); break;
			case 'file_add': $result = $this->fileAddDialog(arg('owner')); break;
			case 'file_insert': $this->fileAdd(); goto(arg('submitURL')); break;
			case 'file_up': $this->fileUp(arg('file_up')); goto($Eresus->request['referer']); break;
			case 'file_down': $this->fileDown(arg('file_down')); goto($Eresus->request['referer']); break;
			case 'file_delete': $this->fileDelete(arg('file_delete')); goto($Eresus->request['referer']); break;
			default:
				$result = $this->adminRenderList($page->id);
			break;
		}
		
		return $result;
	}
	//------------------------------------------------------------------------------
 /**
	 * ������ �������� �������
	 *
	 * @return string  ������ ��������
	 */
	function settings()
	{
	  global $Eresus, $page;
	
	  $form = array(
	    'name'=>'SettingsForm',
	    'caption' => $this->title.' '.$this->version,
	    'width' => '500px',
	    'fields' => array (
	      array('type' => 'hidden', 'name' => 'update', 'value' => $this->name),
	      array('type' => 'memo', 'name' => 'icons', 'height' => '6', 'label' => '�����������:'),
	      array('type' => 'text', 'value' => '������ ������ ����� ����������� ��� ������ ���������� ������.<br />������ ������:<br /><b>&lt;���� �����������&gt;=&lt;���������� 1&gt;,&lt;���������� N&gt;</b><br />������:<br /><b>image.png=png,jpeg,jpg,gif</b>'),
	      array('type' => 'text', 'value' => '����������� ������ ���������� � ���������� <b>'.substr($this->dirStyle, strlen($Eresus->froot)-1).'</b>'),
	    ),
	    'buttons' => array('ok', 'apply', 'cancel'),
	  );
	  $result = $page->renderForm($form, $this->settings);
	  return $result;
	}	
	//------------------------------------------------------------------------------
	/**
	 * ��������� ����������� ��������
	 *
	 * @return string  ������������ �������
	 */
	function clientRenderContent()
	{
		global $page;
  	
		$result = '';

		if ($page->topic) {
			$file = $this->dbItem('files', $page->topic);
			if ($file) {
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Length: '.$file['size']);
				header('Content-Disposition: attachment; filename="'.$file['filename'].'"');
				readfile($this->dirData.$file['id']);
				die;
			} else ErrorBox('����������� ���� �� ������');
		}
		$items = explode("\n", $this->settings['icons']);
		$icons = array();
		for($i=0; $i<count($items); $i++) {
			$items[$i] = explode('=', trim($items[$i]));
			$items[$i][1] = explode(',', trim($items[$i][1]));
			foreach($items[$i][1] as $key) $icons[$key] = $items[$i][0];
		}
		$sections = $this->sectionEnum($page->id);
		for ($i=0; $i < count($sections); $i++) {
			$result .= "<h1>{$sections[$i]['caption']}</h1>\n";
			$files = $this->filesEnum($sections[$i]['id']);
			if (count($files)) {
				$result .= "<p>";
				for ($j = 0; $j < count($files); $j++) {
					$icon = strtolower(substr($files[$j]['filename'], strpos($files[$j]['filename'], '.')+1));
					$icon = isset($icons[$icon]) ? '<img src="'.$this->urlStyle.$icons[$icon].'" alt="" />' : ''; 
					$result .= '<a href="'.$files[$j]['id'].'/">'.$files[$j]['caption'].'</a> ('.FormatSize($files[$j]['size']).')'.$icon.'<br />';
				}
				$result .= "</p>";
			}
		}
		return $result;
	}
	//------------------------------------------------------------------------------
}

?>