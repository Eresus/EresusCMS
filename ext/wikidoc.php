<?php
/**
* WikiDoc - ������������, ������������� � ����� wiki
*
* ������ Eresus 2 (http://eresus.ru/)
*
* PHP 4.3.3
*
* @author: Mikhail Krasilnikov <mk@procreat.ru>
* @version: 1.00
* @modified: 2007-08-29
*/

class TWikidoc extends TListContentPlugin {
  var $name = 'wikidoc';
  var $type = 'client,content,ondemand';
  var $title = 'Wiki-������������';
  var $version = '1.00';
  var $description = '������������ � ����� Wiki';
  var $settings = array(
  );
  var $table = array (
    'name' => 'wikidoc',
    'key'=> 'id',
    'sql' => "(
      `section` int(10) unsigned default NULL,
      `name` varchar(255) NOT NULL default '',
      `keyword` varchar(255) NOT NULL default '',
      `caption` varchar(255) NOT NULL default '',
      `text` text NOT NULL,
      `user` int(10) unsigned default NULL,
      PRIMARY KEY  (`section`, `name`),
      KEY `keyword` (`keyword`)
    ) TYPE=MyISAM;",
  );
	/**
  * �������� ��������
	*		
	*   �������� ����� -> <br />
	*   **bold**
 	*   //italic//
	*   __underline__
	*   ++striked++
	*   ~������
  */
  function parse_basics($text)
  {
		$text = preg_replace(
			array('/\*\*(.*?)\*\*/s', '#(?<!:)//(.*?)//#s', /*'/__([^]*?)__/s',*/ '/\+\+(.*?)\+\+/s', '/^~(.*)$/m'),
			array('<b>$1</b>', /*'<em>$1</em>',*/ '<span class="underline">$1</span>', '<span class="striked">$1</span>', '<div class="indent">$1</div>'),
			$text
		);
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * ���������
	*		
	*   == ��������� 1 ==
	*   === ��������� 2 ===
 	*   ==== ��������� 3 ====
  */
  function parse_headings($text)
  {
		$text = preg_replace(
			array('/====(.*?)====/s', '/===(.*?)===/s', '/==(.*?)==/s'),
			array('<h3>$1</h3>', '<h2>$1</h2>', '<h1>$1</h1>'),
			$text
		);
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * ���������� ������
	*		
	*   [[���/��������]]
	*   [[���/�������� | ����� ��� �����������]]
  */
  function parse_local_links($text)
  {
		global $Eresus;
		
		preg_match_all('/\[\[(.+?)(\|(.+?))?\]\]/s', $text, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		$delta = 0;
		foreach($matches as $match) {
			$href = $Eresus->request['path'].trim($match[1][0]);
			$name = $this->findPageName($match[1][0]);
			if (!$name) $href .= '/edit" class="create';
			$caption = trim((count($match) == 4 ? $match[3][0] : $match[1][0]));
			if (strpos($caption, '/') !== false) $caption = substr($caption, strrpos($caption, '/')+1);
			$href = '<a href="'.$href.'">'.$caption.'</a>';
			$text = substr_replace($text, $href, $match[0][1]+$delta, strlen($match[0][0]));
			$delta += strlen($href) - strlen($match[0][0]);
		}
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * ��������� �������
  *
	* * ����� 1
	* * ����� 2
	* # ����� 1
	* # ����� 2
	*
  */
  function parse_lists($text)
  {
		preg_match('/^\s*\*[^\n]*/mU', $text, $match, PREG_OFFSET_CAPTURE);
		if ($match) {
			$list = array($match);
			print_r($match);
			preg_match('/^\*[^\n]*/sU', $text, $match, PREG_OFFSET_CAPTURE, $list[count($list)-1][0][1]+strlen($list[count($list)-1][0][0]));
			print_r($match); die;
		}
		die;
		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * �������������� �������������� ������
  */
  function parse_content($text)
  {
		$text = $this->parse_local_links($text);
		#$text = $this->parse_external_links($text);
		#$text = $this->parse_lists($text);
		$text = $this->parse_headings($text);
		$text = $this->parse_basics($text);

		$text = preg_replace('![\n\r]*(</(div|h\d)>)[\n\r]*!', '$1', $text);
		$text = nl2br(rtrim($text));

		return $text;
  }
  //------------------------------------------------------------------------------
	/**
  * ��������� ���������������� �����
  */
	function adminRenderContent()
	{
		global $page;
		goto($page->clientURL(arg('section')));
	}
  //------------------------------------------------------------------------------
	/**
  * ����� �������� � �� �� �����
  *
  * @param  string  $name  ��� ��������
  *
  * @return  mixed  ������ ��� �������� ��� false ���� ��� �� �������
  */
  function findPageName($name)
  {
		global $Eresus, $page;
		
		$result = $Eresus->db->selectItem($this->table['name'], "`section` = '".$page->id."' AND `name` = '".mysql_real_escape_string($name)."'");
		if (!$result) {
			$result = $Eresus->db->select($this->table['name'], "`section` = '".$page->id."' AND `name` LIKE '%/".mysql_real_escape_string($name)."'");
			if ($result) $result = $result[0];
		}
		if ($result) $result = $result['name'];
		return $result;
  }
  //------------------------------------------------------------------------------
	/**
  * ������ �������� �� ��
  *
  * @param  string  $name  ��� ��������
  *
  * @return  array  ��������
  */
  function readPage($name)
  {
		global $Eresus, $page;
		
		$result = $Eresus->db->selectItem($this->table['name'], "`section` = '".$page->id."' AND `name` = '".mysql_real_escape_string($name)."'");
		return $result;
  }
  //------------------------------------------------------------------------------
	/**
  * ������ �������� � ��
  *
  * @param  string  $name    ��� ��������
  * @param  array   $item    ��������
  */
  function writePage($name, $item)
  {
		global $Eresus, $page;
		
		if ($this->readPage($name))
			$Eresus->db->updateItem($this->table['name'], $item, "`section` = '".$page->id."' AND `name` = '".mysql_real_escape_string($name)."'");
		else
			$Eresus->db->insert($this->table['name'], $item);
  }
  //------------------------------------------------------------------------------
	/**
  * ���������� ��������
  */
	function updatePage($name)
	{
		global $Eresus, $page;
		
		$item = $Eresus->db->selectItem($this->table['name'], "`section` = '".$page->id."' AND `name` = '".mysql_real_escape_string($name)."'");
		if (!$item) $item = array(
			'section' => $page->id,
			'name' => $name,
			'keyword' => $name,
			'caption' => $name,
			'text' => '',
			'user' => $Eresus->user['id'],
		);
		$item['keyword'] = arg('keyword') ? arg('keyword') : $name;
		$item['caption'] = arg('caption') ? arg('caption') : $name;
		$item['text'] = arg('text');
		$this->writePage($name, $item);
		goto($Eresus->request['path'].$name);
	}
  //------------------------------------------------------------------------------
	/**
  * ����� �������������� ��������
  *
  * @param  string  $name  description
  *
  * @return  type  description
  */
  function editPage($name)
  {
		global $Eresus, $page;
		
		$item = $this->readPage($name);
		if (UserRights(USER)) {
			$form = array(
	      'name' => 'EditPage',
				'action' => $Eresus->request['path'].$name.'/update',
	      'caption' => '��������� �������� "'.$name.'"',
	      'width' => '100%',
	      'fields' => array (
	        array('type'=>'hidden','name'=>'action', 'value'=>'update'),
	        array('type'=>'edit','name'=>'caption','label'=>'���������', 'width' => '100%'),
	        array('type'=>'edit','name'=>'keyword','label'=>'������', 'width' => '300px'),
	        array('type'=>'memo','name'=>'text', 'height' => '25', 'width' => '100%'),
	      ),
	      'buttons' => array('ok', 'cancel'),
	    );
	    $result = $page->renderForm($form, $item);
		}
		return $result;
  }
  //------------------------------------------------------------------------------
	/**
  * ���������� ��������
  *
  * @param  string  $name  ��� ��������
  *
  */
  function showPage($name)
  {
		global $Eresus, $page;
		
		$item = $this->readPage($name);
		if ($item) {
			$page->section[] = $page->title = $item['caption'];
			$result = '<div class="WikiDoc">'.$this->parse_content($item['text']).'</div>';
			$result .= 
				'<div class="WikiDocControls">'.
				'[ <a href="'.$Eresus->request['path'].$this->page.'/edit">�������������</a> ] '.
				'[ <a href="'.$Eresus->request['path'].$this->page.'/delete">�������</a> ]'.
				'</div>';
		} else $result = $this->editPage($name);
		return $result;
  }
  //------------------------------------------------------------------------------
	/**
  * ��������� ���������� �����
  *
  * @param  type  $arg  description
  *
  * @return  type  description
  */
  function clientRenderContent()
  {
		global $Eresus, $page;

		$result = '';
		$this->page = substr($Eresus->request['url'], strlen($Eresus->request['path']));
		$action = end($Eresus->request['params']);
		if (in_array($action, array('edit', 'update', 'delete'))) $this->page = substr($this->page, 0, -strlen($action)-1);
		if (substr($this->page, -1) == '/') $this->page = substr($this->page, 0, -1);
		switch($action) {
			case 'edit': $result = $this->editPage($this->page); break;
			case 'update': $result = $this->updatePage($this->page); break;
			default: $result = $this->showPage($this->page);
		}
		return $result;
  }
  //------------------------------------------------------------------------------
}
?>