<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
define('YANDEX_METHOD_INLINE', 0);
define('YANDEX_METHOD_STARTVALUE', 1);

class TYandex extends TListContentPlugin {
  var $name = 'yandex';
  var $title = '<span style="color:red">�</span>�����';
  var $type = 'admin';
  var $version = '2.00b3';
  var $kernel = '2.08';
  var $description = '������ ������� ����� � ������� <a href="http://yandex.ru/">������</a>';
  var $settings = array (
        'depth' => 10,
        'host' => '',
        'lastcheck' => '0000-00-00 00:00:00',
        'countMethod' => YANDEX_METHOD_STARTVALUE,
        'startValue' => '!<ol class="results" start="(\d+)">!',
        'explode' => '</li>',
        'pattern' => '!<a tabindex="(\d+)" .*? href="http://(www\.)?{%host}/"!s',
      );
  var $table = array (
    'name' => 'yandex',
    'key'=> 'id',
    'sortMode' => 'position',
    'sortDesc' => false,
    'columns' => array(
      array('name' => 'keyword', 'caption' => '�����', 'value' => '<a href="http://yandex.ru/yandsearch?text=$(keyword)">$(keyword)</a>', 'macros' => true),
      array('name' => 'place', 'caption' => '�������', 'align'=>'right', 'replace' => array('0' => '�� �������')),
    ),
    'controls' => array (
      'delete' => '',
      'edit' => '',
      'toggle' => '',
      'position' => '',
    ),
    'tabs' => array(
      'width'=>'180px',
      'items'=>array(
       array('caption'=>'�������', 'name'=>'action', 'value'=>'list'),
       array('caption'=>'������', 'name'=>'action', 'value'=>'other'),
       array('caption'=>'�������� �����', 'name'=>'action', 'value'=>'create'),
      ),
    ),
    'sql' => "(
      `id` int(10) unsigned NOT NULL auto_increment,
      `section` int(10) unsigned default 0,
      `keyword` varchar(255) default NULL,
      `active` tinyint(1) unsigned default NULL,
      `position` int(10) unsigned default NULL,
      `place` int(10) unsigned default NULL,
      PRIMARY KEY  (`id`),
      KEY `active` (`active`),
      KEY `section` (`section`),
      KEY `position` (`position`),
      KEY `place` (`place`)
    ) TYPE=MyISAM;",
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TYandex()
  # ���������� ����������� ������������ �������
  {
    global $plugins;
  
    parent::TListContentPlugin();
    $plugins->events['adminOnMenuRender'][] = $this->name;
    $this->table['hint'] = '��������� ��������: <b id="yandexLastcheck">'.FormatDate($this->settings['lastcheck'], DATETIME_LONG).'</b>';
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
    global $db, $request;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = true;
    dbReorderItems($this->table['name']);
    $item['position'] = $db->count($this->table['name']);
    $db->insert($this->table['name'], $item);
    sendNotify('��������� �������� �����: '.$item['keyword']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
    global $db, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $old = $item['keyword'];
    $item = setArgs($item);
    if (!isset($request['arg']['active'])) $item['active'] = false;
    $db->updateItem($this->table['name'], $item, "`id`='".$request['arg']['update']."'");
    sendNotify('�������� �������� �����: '.$old.' &raquo; '.$item['keyword']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function readURL($url)
  {
    $result = false;
    $result = @file_get_contents($url);
    if ($result) $result = StripSlashes($result);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function checkPhrase($phrase) 
  {
    global $db;
    
    $urlStart = "http://yandex.ru/yandsearch?text=%s";
    $page = 1;
    $url = sprintf($urlStart, urlencode($phrase));
    $status = true;
    $pattern = str_replace('{%host}', str_replace('.', '\.', empty($this->settings['host'])?httpHost:$this->settings['host']), $this->settings['pattern']);
    do {
    	# �������� �������� �������
      $text = $this->readURL($url);
      if ($text) { # ���� ������� �������� ��������...
        if (empty($this->settings['explode'])) $frags = array($text); 
        else $frags = explode($this->settings['explode'], $text);
        $found = false;
        
        for ($i=0; $i<count($frags); $i++) {
          $found = preg_match($pattern, $frags[$i], $match);
          if ($found) break;
        }
        if (!$found) {
          $page++;
          if (preg_match('/<a href="(\/yandsearch\?[^"]*?)">'.$page.'<\/a>/i', $text, $match)) $url = 'http://yandex.ru'.$match[1];
          else $page = $this->settings['depth'];
        }
      } else {
        $status = false;
        break;
      }
    } while (!$found && ($page < $this->settings['depth']));
    if ($status && $found) switch ($this->settings['countMethod']) {
      case YANDEX_METHOD_INLINE:
        $result = $match[1];
      break;
      case YANDEX_METHOD_STARTVALUE:
        preg_match($this->settings['startValue'], $text, $start);
        $result = $i+$start[1]; 
      break;
    } else $result = 0;
    $item = $db->selectItem($this->table['name'], "`keyword`='".$phrase."'");
    if ($item) {
	    $item['place'] = $result;
	    $db->updateItem($this->table['name'], $item, "`id`='".$item['id']."'");
    }
    $this->settings['lastcheck'] = gettime();
    $item = $db->selectItem('plugins', "`name`='".$this->name."'");
    $item['settings'] = decodeOptions($item['settings']);
    $item['settings']['lastcheck'] = $this->settings['lastcheck'];
    $item['settings'] = encodeOptions($this->settings);
    $db->updateItem('plugins', $item, "`name`='".$item['name']."'");
    Header('Content-Type: text/xml');
    echo 
      '<?xml version="1.0" encoding="'.CHARSET.'"?>'."\n".
      '<answer>'."\n".
      ' <status>'.$status.'</status>'."\n".
      ' <phrase>'.$phrase.'</phrase>'."\n".
      ' <time>'.FormatDate($this->settings['lastcheck'], DATETIME_LONG).'</time>'."\n".
      ' <position>'.$result.'</position>'."\n".
      '</answer>'."\n";
    exit;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function checkOther()
  {
  global $page; 
    
    $result = $page->renderTabs($this->table['tabs']);
    $body = '';
    $host = empty($this->settings['host'])?httpHost:$this->settings['host'];

    $body .= '<div style="text-align: center; padding: 10px;"><img src="http://yandex.ru/cycounter?'.$host.'" width=88 height=31 border=0 alt="���"></div>';
    $url = 'http://yandex.ru/yandsearch?ras=1&mime=all&Link='.$host;
    $text = $this->readURL($url);
    preg_match('/<div class="refblock">.*?<b>(.*?)<\/b>.*?<b>(.*?)<\/b>/si', $text, $match);
    $body .= '<a href="'.$url.'">����������� ������</a>: <b>'.$match[2].' ('.$match[1].')</b><br>';
    $wnd = array(
      'caption' => '���������� �������',
      'body' => $body,
    );
    $result .= $page->window($wnd);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminAddItem()
  {
  global $page, $db;

    $form = array(
      'name' => 'formCreate',
      'caption' => '�������� �������� �����',
      'width' => '500px',
      'fields' => array (
        array ('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array ('type' => 'edit', 'name' => 'keyword', 'label' => '', 'width' => '100%', 'maxlength' => '255'),
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
    $form = array(
      'name' => 'formEdit',
      'caption' => '�������� �������� �����',
      'width' => '500px',
      'fields' => array (
        array ('type' => 'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'keyword', 'label' => '', 'width' => '100%', 'maxlength' => '255'),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'�������'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );

    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRender()
  {
  global $request, $page;

    switch (arg('action')) {
      case 'check': $this->checkWords(); break;
      case 'other': $result = $this->checkOther(); break;
      case 'list': 
      default: $result = $this->adminRenderContent();
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminRenderContent()
  {
  global $db, $page, $user, $request, $session;
  
    $result = '';
    $request['arg']['section'] = 0;
    if (arg('check')) $this->checkPhrase(arg('check'));
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
    } elseif (isset($request['arg']['up']) && isset($this->table['controls']['position'])) {
      if (method_exists($this, 'up')) $result = $this->table['sortDesc']?$this->down($request['arg']['up']):$this->up($request['arg']['up']); else $session['errorMessage'] = sprintf(errMethodNotFound, 'up', get_class($this));
    } elseif (isset($request['arg']['down']) && isset($this->table['controls']['position'])) {
      if (method_exists($this, 'down')) $result = $this->table['sortDesc']?$this->up($request['arg']['down']):$this->down($request['arg']['down']); else $session['errorMessage'] = sprintf(errMethodNotFound, 'down', get_class($this));
    } elseif (isset($request['arg']['id']) && isset($this->table['controls']['edit'])) {
      if (method_exists($this, 'adminEditItem')) $result = $this->adminEditItem(); else $session['errorMessage'] = sprintf(errMethodNotFound, 'adminEditItem', get_class($this));
    } elseif (isset($request['arg']['action'])) switch ($request['arg']['action']) {
      case 'create': if(isset($this->table['controls']['edit']))
        if (method_exists($this, 'adminAddItem')) $result = $this->adminAddItem(); 
        else $session['errorMessage'] = sprintf(errMethodNotFound, 'adminAddItem', get_class($this));
      break;
      case 'insert':
        if (method_exists($this, 'insert')) $result = $this->insert(); 
        else $session['errorMessage'] = sprintf(errMethodNotFound, 'insert', get_class($this));
      break;
    } else {
      if (isset($request['arg']['section'])) $this->table['condition'] = "`section`='".$request['arg']['section']."'";
      $page->scripts .= "
      
        var yandexIndex = 1;
        
        function yandexHandler()
        {
          if ((HttpRequest.readyState == 4) && (HttpRequest.status == 200)) {
            var Node = document.getElementById('yandexKeywords');
            if (Node) {
              Node = Node.getElementsByTagName('table')[1];
              if (HttpRequest.responseXML.getElementsByTagName('status')[0].firstChild.data) {
                Node.rows[yandexIndex].cells[2].innerHTML = HttpRequest.responseXML.getElementsByTagName('position')[0].firstChild.data;
                if (Node.rows[yandexIndex].cells[2].innerHTML == '0') Node.rows[yandexIndex].cells[2].innerHTML = '�� �������';
              } else {
                Node.rows[yandexIndex].cells[2].innerHTML = '������!';
              }
              Node = document.getElementById('yandexLastcheck').innerHTML = HttpRequest.responseXML.getElementsByTagName('time')[0].firstChild.data;
              yandexIndex++;
              yandexCheck();
            }
          }
        }
      
        function yandexCheck()
        {
          var Node = document.getElementById('yandexKeywords');
          Node = Node.getElementsByTagName('table')[1];
          if (yandexIndex < Node.rows.length) {
            SendRequest('".$request['url']."&check='+Node.rows[yandexIndex].cells[1].getElementsByTagName('a')[0].innerHTML, yandexHandler);
            Node.rows[yandexIndex].cells[2].innerHTML = '�����������...';
          } else yandexIndex = 1;
        }

        function yandexStop()
        {
          HttpRequest.abort();
          var Node = document.getElementById('yandexKeywords');
          Node = Node.getElementsByTagName('table')[1];
          Node.rows[yandexIndex].cells[2].innerHTML = '�����������';
          yandexIndex = 1;
        }
      ";
      unset($request['arg']['action']);
      $result = 
        '<div id="yandexKeywords">'.$page->renderTable($this->table).'</div>'.
        '<br />&nbsp;<input type="button" class="button" value="���������" onclick="yandexCheck()" />'.
        '&nbsp;<input type="button" class="button" value="����������" onclick="yandexStop()" />';
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function settings()
  {
  global $page;

    $form = array(
      'name' => 'settings',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'edit','name'=>'depth','label'=>'�������','width'=>'50px', 'maxlength'=>'3', 'comment'=>'�������'),
        array('type'=>'edit','name'=>'host','label'=>'���� ����','width'=>'100%', 'maxlength'=>'255'),
        array('type'=>'divider'),
        array('type'=>'select','name'=>'countMethod','label'=>'����� ��������', 'items'=>array('������� ������� ��� ������ ������','������� ��������� ������� ����� ������'), 'values'=>array(YANDEX_METHOD_INLINE, YANDEX_METHOD_STARTVALUE)),
        array('type'=>'edit','name'=>'startValue','label'=>'������ ������','width'=>'100%'),
        array('type'=>'edit','name'=>'explode','label'=>'��������� ��','width'=>'100%'),
        array('type'=>'edit','name'=>'pattern','label'=>'�������','width'=>'100%'),
        array('type'=>'text', 'value'=>'<b>{%host}</b> - ���� ����� (��. ����)<br>������� ����� ������ ���� ������� � ������, ��� ���: <b>(\\d+)</b>'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminOnMenuRender()
  {
  global $page;
  
    $page->addMenuItem(admExtensions, array ('access'  => EDITOR, 'link'  => $this->name, 'caption'  => $this->title, 'hint'  => strip_tags($this->description)));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>