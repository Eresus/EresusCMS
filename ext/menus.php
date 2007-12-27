<?php
/**
 * Menus
 *
 * Eresus 2
 *
 * ���������� ����������� ����
 *
 * @version 2.00
 *
 * @copyright   2007, ProCreat Systems, http://procreat.ru/
 * @copyright   2007, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @maintainer  Mikhail Krasilnikov <mk@procreat.ru>
 * @author      Mikhail Krasilnikov <mk@procreat.ru>
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
 */

class Menus extends Plugin {
  var $version = '2.00a';
  var $kernel = '2.10rc';
  var $title = '���������� ����';
  var $description = '�������� ����';
  var $type = 'client,admin';
 /**
  * @var array
  */
  var $menu = null;
 /**
  * ���� �� ���������
  * @var array
  */
  var $pages = array();
 /**
  * ���� �� ��������� (������ ��������������)
  * @var array
  */
  var $ids = array();
 /**
  * �����������
  * @return Menus
  */
  function Menus()
  {
    parent::Plugin();
    $this->listenEvents('clientOnURLSplit', 'clientOnPageRender', 'adminOnMenuRender');
  }
  //-----------------------------------------------------------------------------
 /**
  * �������� ������
  */
  function install()
  {
  	parent::install();
  	$this->dbCreateTable("
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
  	");
  }
  //-----------------------------------------------------------------------------
 /**
  * �������� ������
  */
  function uninstall()
  {
  	$this->dbDropTable();
  	parent::uninstall();
  }
  //-----------------------------------------------------------------------------
 /**
  * ��������� ���� � ��
  *
  * @param array $item  �������� ����
  */
  function insert($item)
  {
    $this->dbInsert('', $item);
    sendNotify('��������� ����: '.$item['caption']);
  }
  //-----------------------------------------------------------------------------
 /**
  * �������� ���� � ��
  *
  * @param array $item  �������� ����
  */
  function update($item)
  {
    $this->dbUpdate('', $item);
    sendNotify('�������� ����: '.$item['caption']);
  }
  //-----------------------------------------------------------------------------
 /**
  * ������ ��������
  *
  * @param string $template
  * @param array $item
  * @return string
  */
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
  //-----------------------------------------------------------------------------
 /**
  * ���������� ������ ��������
  *
  * @param int $owner  ID ��������� �������
  * @param int $level  ������� ������� �����������
  * @return array
  */
  function pagesBranch($owner = 0, $level = 0)
  {
    global $Eresus;

    $result = array(array(), array());
    $items = $Eresus->sections->children($owner, GUEST, SECTIONS_ACTIVE);
    if (count($items)) foreach($items as $item) {
      $result[0][] = str_repeat('- ', $level).$item['caption'];
      $result[1][] = $item['id'];
      $sub = $this->pagesBranch($item['id'], $level+1);
      if (count($sub[0])) {
        $result[0] = array_merge($result[0], $sub[0]);
        $result[1] = array_merge($result[1], $sub[1]);
      }
    }
    return $result;
  }
	//-----------------------------------------------------------------------------
  /**
  * ��������� ����� ���� � ��
  */
  function adminInsert()
  {
    $item['name'] = arg('name', 'word');
		$item['caption'] = arg('caption', 'dbsafe');
    $item['active'] = true;
    $item['root'] = arg('root', 'int');
    $item['rootLevel'] = arg('rootLevel', 'int');
    $item['expandLevelAuto'] = arg('expandLevelAuto', 'int');
    $item['expandLevelMax'] = arg('expandLevelMax', 'int');
    $item['glue'] = arg('glue', 'dbsafe');
    $item['tmplList'] = arg('tmplList', 'dbsafe');
    $item['tmplItem'] = arg('tmplItem', 'dbsafe');
    $item['tmplSpecial'] = arg('tmplSpecial', 'dbsafe');
    $item['specialMode'] = arg('specialMode', 'int');
    $item['invisible'] = arg('invisible', 'int');
    if (empty($item['name']) || empty($item['caption'])) {
    	saveRequest();
    	ErrorMessage('��������� �� ��� ������������ ����!');
    	goto($GLOBALS['Eresus']->request['referer']);
    }
    $this->insert($item);
    goto(arg('submitURL'));
  }
  //-----------------------------------------------------------------------------
 /**
  * ��������� ����� ���� � ��
  */
  function adminUpdate()
  {
		$item = $this->dbItem('', arg('update', 'int'));
    $item['name'] = arg('name', 'word');
		$item['caption'] = arg('caption', 'dbsafe');
    $item['root'] = arg('root', 'int');
    $item['rootLevel'] = arg('rootLevel', 'int');
    $item['expandLevelAuto'] = arg('expandLevelAuto', 'int');
    $item['expandLevelMax'] = arg('expandLevelMax', 'int');
    $item['glue'] = arg('glue', 'dbsafe');
    $item['tmplList'] = arg('tmplList', 'dbsafe');
    $item['tmplItem'] = arg('tmplItem', 'dbsafe');
    $item['tmplSpecial'] = arg('tmplSpecial', 'dbsafe');
    $item['specialMode'] = arg('specialMode', 'int');
    $item['invisible'] = arg('invisible', 'int');
    /*if (empty($item['name']) || empty($item['caption'])) {
    	saveRequest();
    	ErrorMessage('��������� �� ��� ������������ ����!');
    	goto($GLOBALS['Eresus']->request['referer']);
    }*/
    $this->update($item);
    goto(arg('submitURL'));
  }
  //-----------------------------------------------------------------------------
 /**
  * �������� ���������� ����
  */
  function adminToggle()
  {
  	global $Eresus;
		$item = $this->dbItem('', arg('toggle', 'int'));
		$item['active'] = !$item['active'];
    $this->dbUpdate('', $item);
    goto($Eresus->request['referer']);
  }
  //-----------------------------------------------------------------------------
  /**
  * ������ ������� ����� ������� ��������/��������� ����
  *
  * @return array
  */
  function adminDialogTemplate()
  {
    $sections = $this->pagesBranch();
    array_unshift($sections[0], '������� ������');
    array_unshift($sections[1], -1);
    array_unshift($sections[0], '������');
    array_unshift($sections[1], 0);
    $form = array(
      'name' => 'FormCreate',
      'width' => '500px',
      'fields' => array (
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
        array('type'=>'text', 'value' => '�������:<ul><li><b>$(level)</b> - ����� �������� ������</li><li><b>$(items)</b> - ������ ����</li></ul>'),
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
          '<li><b>��� ������� �������</b> - $(id), $(title), $(caption), $(hint), $(description), $(keywords) � �.�.</li>'.
        	'<li><b>$(href)</b> - ������</li>'.
          '<li><b>$(num)</b> - ���������� ����� ������� � ������� ������</li>'.
        	'<li><b>$(level)</b> - ����� �������� ������</li><li>'.
          '<li><b>$(submenu)</b> - ����� ��� ������� �������</li>'.
          '<li><b>{%selected?������1:������2}</b> - ���� ������� ������, �������� ������1, ����� ������2</li>'.
          '<li><b>{%parent?������1:������2}</b> - ���� ������� ��������� ����� ������������ �������� ���������� ��������, �������� ������1, ����� ������2</li>'.
          '</ul>'),
        array('type'=>'divider'),
        array('type'=>'text', 'value' => '��� ������� ���� ����������� ������ <b>$(Menus:���_����)</b>'),
      ),
    );
  	return $form;
  }
  //-----------------------------------------------------------------------------
 /**
  * ������ �������� ����
  *
  * @return string
  */
  function adminCreateDialog()
  {
    global $Eresus, $page;

    $form = $this->adminDialogTemplate();
    $form['caption']  = '������� ����';
    $form['fields'][] = array('type'=>'hidden','name'=>'action', 'value'=>'insert');
    $form['buttons'] = array('ok', 'cancel');
    restoreRequest();
    $result = $page->renderForm($form, $Eresus->request['arg']);
    return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * ������ ��������� ����
  *
  * @return string
  */
  function adminEditDialog()
  {
    global $Eresus, $page;

    $item = $this->dbItem('', arg('id', 'int'));
    $form = $this->adminDialogTemplate();
    $form['caption']  = '��������� ����';
    $form['fields'][] = array('type'=>'hidden','name'=>'update', 'value'=>$item['id']);
    $result = $page->renderForm($form, $item);
    return $result;
  }
 /**
  * ���������� �������� ���� �� ��������
  *
  * @param array $item �������� �������
  * @param string $url URI �������
  */
  function clientOnURLSplit($item, $url)
  {
    $this->pages[] = $item;
    $this->ids[] = $item['id'];
  }
  //-----------------------------------------------------------------------------
 /**
  * C����� ����� ���� ������� �� �������� � id = $owner
  *
  * @param int $owner    id ��������� ������
  * @param string $path  ����������� ���� � ���������
  * @param int $level		 ������� �����������
  * @return string
  */
  function menuBranch($owner = 0, $path = '', $level = 1)
  {
    global $Eresus, $page;

    $result = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    if ($owner == -1) $owner = $page->id;
    $items = $Eresus->sections->children($owner, $Eresus->user['auth'] ? $Eresus->user['access'] : GUEST, SECTIONS_ACTIVE | ($this->menu['invisible']? 0 : SECTIONS_VISIBLE));
    if (count($items)) {
      $result = array();
      for($i = 0; $i < count($items); $i++) {
        $template = $this->menu['tmplItem'];
        if ($items[$i]['type'] == 'url') {
          $items[$i] = $Eresus->sections->get($items[$i]['id']);
          $items[$i]['url'] = $items[$i]['href'] = $page->replaceMacros($items[$i]['content']); #FIXME: ������ 'url' � ����������� ������� (�������� �������������)
        } else $items[$i]['url'] = $items[$i]['href'] = httpRoot.$path.($items[$i]['name']=='main'?'':$items[$i]['name'].'/'); #FIXME: ������ 'url' � ����������� ������� (�������� �������������)
				$items[$i]['num'] = $i+1;
        $items[$i]['level'] = $level;
        $items[$i]['is-selected'] = $items[$i]['id'] == $page->id;
        $items[$i]['is-parent'] = !$items[$i]['is-selected'] && in_array($items[$i]['id'], $this->ids);
        if ((!$this->menu['expandLevelAuto'] || ($level < $this->menu['expandLevelAuto'])) || (($items[$i]['is-parent'] || $items[$i]['is-selected']) && (!$this->menu['expandLevelMax'] || $level < $this->menu['expandLevelMax']))) {
          $items[$i]['submenu'] = $this->menuBranch($items[$i]['id'], $path.$items[$i]['name'].'/', $level+1);
        }
        switch ($this->menu['specialMode']) {
          case 0: # ���
          break;
          case 1: # ������ ��� ���������� ������
            if ($items[$i]['is-selected']) $template = $this->menu['tmplSpecial'];
          break;
          case 2: # ��� ���������� ������ ���� ������ ��� ��������
            if ((strpos($Eresus->request['path'], $items[$i]['href']) === 0) && $items[$i]['name'] != 'main') $template = $this->menu['tmplSpecial'];
          break;
          case 3: # ��� �������, ������� ���������
            if (!empty($items[$i]['submenu'])) $template = $this->menu['tmplSpecial'];
          break;
        }
        $result[] = $this->replaceMacros($template, $items[$i]);
      }
      $result = implode($this->menu['glue'], $result);
      $result = array('level'=>($level), 'items'=>$result);
      $result = $this->replaceMacros($this->menu['tmplList'], $result);
    }
    return $result;
  }
	//-----------------------------------------------------------------------------
 /**
  * ����� � ��������� ����
  *
  * @param string $text
  * @return string
  */
  function clientOnPageRender($text)
  {
    global $Eresus, $page;

    preg_match_all('/\$\(menus:(.+?)\)/si', $text, $menus, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
    $delta = 0;
    for($i = 0; $i < count($menus); $i++) {
      $this->menu = $this->dbItem('', $menus[$i][1][0], 'name');
      if (!is_null($this->menu) && $this->menu['active']) {
        if ($this->menu['root'] == -1 && $this->menu['rootLevel']) {
          $parents = $Eresus->sections->parents($page->id);
          $level = count($parents);
          if ($level == $this->menu['rootLevel']) $this->menu['root'] = -1;
          elseif ($level > $this->menu['rootLevel']) $this->menu['root'] = $this->menu['root'] = $parents[$this->menu['rootLevel']];
          else $this->menu['root'] = -2;
        }
        $path = $this->menu['root'] > -1 ? $page->clientURL($this->menu['root']) : $Eresus->request['path'];
        $menu = $this->menuBranch($this->menu['root'], $path);
        $text = substr_replace($text, $menu, $menus[$i][0][1]+$delta, strlen($menus[$i][0][0]));
        $delta += strlen($menu) - strlen($menus[$i][0][0]);
      }
    }
    return $text;
  }
  //-----------------------------------------------------------------------------
 /**
  * ��������� ������ ����
  * @return string
  */
  function adminRenderList()
  {
  	global $Eresus, $page;

  	$result = '';
		$tabs = array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>'������� ����', 'name'=>'action', 'value'=>'create')
      ),
    );
    $result .= $page->renderTabs($tabs);

    # ��������� ������
    $root = $Eresus->root.'admin.php?mod=pages&amp;';
    $items = $this->dbSelect('', '', 'caption');
    useLib('admin/lists');
    $list = new AdminList();
    $list->setHead('', '��������', '���');
    for($i=0; $i<count($items); $i++) {
      $row = array();
      $row[] =
      	$list->control('delete', $page->url(array('delete' => $items[$i]['id']))).'&nbsp;'.
      	$list->control($items[$i]['active']? 'off' : 'on', $page->url(array('toggle' => $items[$i]['id']))).'&nbsp;'.
      	$list->control('edit', $page->url(array('id' => $items[$i]['id'])));
      $row[] = $items[$i]['caption'];
      $row[] = $items[$i]['name'];
     	$list->addRow($row);
    }
    $result .= $list->render();
    return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * ��������� �������� ��
  * @return string
  */
  function adminRender()
  {
  	$result = '';
  	switch (arg('action')) {
  		case 'create': $result = $this->adminCreateDialog(); break;
  		case 'insert': $this->adminInsert(); break;
  		default: switch (true) {
  			case arg('update'): $this->adminUpdate(); break;
  			case arg('toggle'): $this->adminToggle(); break;
  			case arg('id'): $result = $this->adminEditDialog(); break;
  			default: $result = $this->adminRenderList();
  		}
  	}
  	return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * ���������� ������ � ���� "����������"
  */
  function adminOnMenuRender()
  {
  	global $page;
    $page->addMenuItem(admExtensions, array ('access'  => ADMIN, 'link'  => $this->name, 'caption'  => $this->title, 'hint'  => $this->description));
  }
  //-----------------------------------------------------------------------------
}
?>