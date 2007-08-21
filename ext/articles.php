<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005-2007, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# 2.07 - ����� onURLSplit
# 2.08 - bersz: ������ ��������������� ����������� �� ������
define('_ARTICLES_BLOCK_NONE', 0);
define('_ARTICLES_BLOCK_LAST', 1);
define('_ARTICLES_BLOCK_MANUAL', 2);

define('_ARTICLES_TMPL_BLOCK', '<img src="'.httpRoot.'core/img/info.gif" width="16" height="16" alt="" title="���������� � �����">');

class TArticles extends TListContentPlugin {
  var $name = 'articles';
  var $type = 'client,content,ondemand';
  var $title = '������';
  var $version = '2.08';
  var $description = '���������� ������';
  var $settings = array(
    'itemsPerPage' => 10,
    'tmplListItem' => '
      <div class="ArticlesItem">
        <h3>$(caption)</h3>
        $(posted)
        <br /><br />
        <img src="$(image)" alt="$(caption)" width="$(imageWidth)" height="$(imageHeight)" />
        $(text)
      </div>
    ',
    'tmplItem' => '
      <div class="ArticlesListItem">
        <div class="caption">
          $(caption) ($(posted))
        </div>
        <img src="$(image)" alt="$(caption)" width="$(imageWidth)" height="$(imageHeight)" style="float:left" />
        <div style="margin-left: $(imageWidth)px; padding-left: 5px;">
          $(preview)
          <div class="controls">
            <a href="$(link)">������ �����...</a>
          </div>
        </div>
        <br /><br />
      </div>
    ',
    'tmplBlockItem' => '<b>$(posted)</b><br /><a href="$(link)">$(caption)</a><br />',
    'previewMaxSize' => 500,
    'previewSmartSplit' => true,
    'listSortMode' => 'posted',
    'listSortDesc' => true,
    'dateFormatPreview' => DATE_SHORT,
    'dateFormatFullText' => DATE_LONG,
    'blockMode' => 0, # 0 - ���������, 1 - ���������, 2 - ���������
    'blockCount' => 5,
    'imageWidth' => 120,
    'imageHeight' => 90,
    'imageColor' => '#000000',
  );
  var $table = array (
    'name' => 'articles',
    'key'=> 'id',
    'sortMode' => 'posted',
    'sortDesc' => true,
    'columns' => array(
      array('name' => 'caption', 'caption' => '���������'),
      array('name' => 'posted', 'align'=>'center', 'value'=>templPosted, 'macros' => true),
      array('name' => 'preview', 'caption' => '������', 'maxlength'=>255, 'striptags' => true),
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
      `section` int(10) unsigned default NULL,
      `active` tinyint(1) unsigned NOT NULL default '1',
      `position` int(10) unsigned default NULL,
      `posted` datetime default NULL,
      `block` tinyint(1) unsigned NOT NULL default '0',
      `caption` varchar(255) NOT NULL default '',
      `preview` text NOT NULL,
      `text` text NOT NULL,
      PRIMARY KEY  (`id`),
      KEY `active` (`active`),
      KEY `section` (`section`),
      KEY `position` (`position`),
      KEY `posted` (`posted`),
      KEY `block` (`block`)
    ) TYPE=MyISAM COMMENT='Articles';",
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
  function TArticles()
  # ���������� ����������� ������������ �������
  {
    global $plugins;

    parent::TListContentPlugin();
    if ($this->settings['blockMode']) $plugins->events['clientOnPageRender'][] = $this->name;
    $this->table['sortMode'] = $this->settings['listSortMode'];
    $this->table['sortDesc'] = $this->settings['listSortDesc'];
    if ($this->table['sortMode'] == 'position') $this->table['controls']['position'] = '';
    if ($this->settings['blockMode'] == _ARTICLES_BLOCK_MANUAL) {
      $temp = array_shift($this->table['columns']);
      array_unshift($this->table['columns'], array('name' => 'block', 'align'=>'center', 'replace'=>array(0 => '', 1 => _ARTICLES_TMPL_BLOCK)), $temp);
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function updateSettings()
  {
    global $db, $request;

    $item = $db->selectItem('`plugins`', "`name`='".$this->name."'");
    $item['settings'] = decodeOptions($item['settings']);
    foreach ($this->settings as $key => $value) $this->settings[$key] = isset($request['arg'][$key])?$request['arg'][$key]:'';
    if ($this->settings['blockMode']) $item['type'] = 'client,content'; else $item['type'] = 'client,content,ondemand';
    $item['settings'] = encodeOptions($this->settings);
    $db->updateItem('plugins', $item, "`name`='".$this->name."'");
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function createPreview($text)
  {
    $text = trim(preg_replace('/<.+>/Us',' ',$text));
    $text = str_replace(array("\n", "\r"), ' ', $text);
    $text = preg_replace('/\s{2,}/', ' ', $text);
    if (!$this->settings['previewMaxSize']) $this->settings['previewMaxSize'] = 500;
    if ($this->settings['previewSmartSplit']) {
      preg_match("/\A(.{1,".$this->settings['previewMaxSize']."})(\.\s|\.|\Z)/s", $text, $result);
      $result = $result[1].'...';
    } else {
      $result = substr($text, 0, $this->settings['previewMaxSize']);
      if (strlen($text)>$this->settings['previewMaxSize']) $result .= '...';
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function thumbnail($srcFile, $dstFile, $width, $height, $fill = null)
  {
    $type = getimagesize($srcFile);
    switch ($type[2]) {
      case IMG_GIF: $src = imageCreateFromGIF($srcFile); break;
      case IMG_JPG:
      case IMG_JPEG: $src = imageCreateFromJPEG($srcFile); break;
      case IMG_PNG: $src = imageCreateFromPNG($srcFile); break;
    }
    if ($src) {
      $sW = imageSX($src);
      $sH = imageSY($src);
      $resizer = ($sW/$width > $sH/$height) ? ($sW / $width) : ($sH / $height);
      $dW = floor($sW / $resizer);
      $dH = floor($sH / $resizer);
      if (is_null($fill)) {
        $dst = imageCreateTrueColor($dW, $dH);
        imageCopyResampled($dst, $src, 0, 0, 0, 0, $dW, $dH, $sW, $sH);
      } else {
        $dst = imageCreateTrueColor($width, $height);
        if ($fill[0] == '#') {
          $R = hexdec(substr($fill, 1, 2));
          $G = hexdec(substr($fill, 3, 2));
          $B = hexdec(substr($fill, 5, 2));
        } else {
          $fill = explode(',', $fill);
          $R = trim($fill[0]);
          $G = trim($fill[1]);
          $B = trim($fill[2]);
        }
        imageFill($dst, 0, 0, 0);
        imageCopyResampled($dst, $src, round(($width-$dW)/2), round(($height-$dH)/2), 0, 0, $dW, $dH, $sW, $sH);
      }
      ImageJPEG($dst, $dstFile);
      ImageDestroy($src);
      ImageDestroy($dst);
    }
  }
  #-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
  function insert()
  {
    global $db, $request, $page;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = true;
    if (empty($item['preview'])) $item['preview'] = $this->createPreview($item['text']);
    $item['posted'] = gettime();
    $db->insert($this->table['name'], $item);
    $item['id'] = $db->getInsertedID();
    if (is_uploaded_file($_FILES['image']['tmp_name'])) {
      $filename = filesRoot.'data/articles/'.$item['id'].'.jpg';
      $this->thumbnail($_FILES['image']['tmp_name'], $filename, $this->settings['imageWidth'], $this->settings['imageHeight'], '#000000');
    }
    sendNotify(admAdded.': <a href="'.httpRoot.'admin.php?mod=content&section='.$item['section'].'&id='.$item['id'].'">'.$item['caption'].'</a><br />'.$item['text']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
    global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $item = GetArgs($item, array('active', 'block'));
    if (empty($item['preview']) || $request['arg']['updatePreview']) $item['preview'] = $this->createPreview($item['text']);
    $db->updateItem($this->table['name'], $item, "`id`='".$request['arg']['update']."'");
    if (is_uploaded_file($_FILES['image']['tmp_name'])) {
      $filename = filesRoot.'data/articles/'.$item['id'].'.jpg';
      $this->thumbnail($_FILES['image']['tmp_name'], $filename, $this->settings['imageWidth'], $this->settings['imageHeight'], '#000000');
    }
    sendNotify(admUpdated.': <a href="'.$page->url().'">'.$item['caption'].'</a><br />'.$item['text']);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function replaceMacros($template, $item, $dateFormat)
  {
    global $plugins, $page, $request;

    if (file_exists(filesRoot.'data/articles/'.$item['id'].'.jpg'))
    {
      $image = httpRoot.'data/articles/'.$item['id'].'.jpg';
      $width = $this->settings['imageWidth'];
      $height = $this->settings['imageHeight'];
    }
    else
    {
      $image = styleRoot.'dot.gif';
      $width = 1;
      $height = 1;
    }

    $result = str_replace(
      array(
        '$(caption)',
        '$(preview)',
        '$(text)',
        '$(posted)',
        '$(link)',
        '$(image)',
        '$(imageWidth)',
        '$(imageHeight)',
      ),
      array(
        strip_tags(htmlspecialchars(StripSlashes($item['caption']))),
        StripSlashes($item['preview']),
        StripSlashes($item['text']),
        FormatDate($item['posted'], $dateFormat),
        $page->clientURL($item['section']).$item['id'].'/',
        $image,
        $width,
        $height,
      ),
      $template
    );
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminAddItem()
  {
    global $page, $request;

    $form = array(
      'name' => 'newArticles',
      'caption' => '�������� ������',
      'width' => '95%',
      'fields' => array (
        array ('type'=>'hidden','name'=>'action', 'value'=>'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '���������', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'html', 'name' => 'text', 'label' => '������ �����', 'height' => '200px'),
        array ('type' => 'memo', 'name' => 'preview', 'label' => '������� ��������', 'height' => '10'),
        array ('type' => ($this->settings['blockMode'] == _ARTICLES_BLOCK_MANUAL)?'checkbox':'hidden', 'name' => 'block', 'label' => '���������� � �����'),
        array ('type' => 'file', 'name' => 'image', 'label' => '��������', 'width' => '100'),
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
      'name' => 'editArticles',
      'caption' => '�������� ������',
      'width' => '95%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '���������', 'width' => '100%', 'maxlength' => '100'),
        array ('type' => 'html', 'name' => 'text', 'label' => '������ �����', 'height' => '200px'),
        array ('type' => 'memo', 'name' => 'preview', 'label' => '������� ��������', 'height' => '5'),
        array ('type' => 'checkbox', 'name'=>'updatePreview', 'label'=>'�������� ������� �������� �������������', 'value' => false),
        array ('type' => ($this->settings['blockMode'] == _ARTICLES_BLOCK_MANUAL)?'checkbox':'hidden', 'name' => 'block', 'label' => '���������� � �����'),
        array ('type' => 'file', 'name' => 'image', 'label' => '��������', 'width' => '100'),
        array ('type' => 'divider'),
        array ('type' => 'edit', 'name' => 'section', 'label' => '������', 'access'=>ADMIN),
        array ('type' => 'edit', 'name'=>'posted', 'label'=>'��������'),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'�������'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);

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
        array('type'=>'memo','name'=>'tmplItem','label'=>'������ ��������������� ���������','height'=>'5'),
        array('type'=>'edit','name'=>'dateFormatFullText','label'=>'������ ����', 'width'=>'100px'),
        array('type'=>'header', 'value' => '��������� ������'),
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'������ �� ��������','width'=>'50px', 'maxlength'=>'2'),
        array('type'=>'memo','name'=>'tmplListItem','label'=>'������ ��������','height'=>'5'),
        array('type'=>'edit','name'=>'dateFormatPreview','label'=>'������ ����', 'width'=>'100px'),
        array('type'=>'select','name'=>'listSortMode','label'=>'����������', 'values' => array('posted', 'position'), 'items' => array('�� ���� ����������', '������')),
        array('type'=>'checkbox','name'=>'listSortDesc','label'=>'� �������� �������'),
        array('type'=>'header', 'value' => '���� ������'),
        array('type'=>'select','name'=>'blockMode','label'=>'����� ����� ������', 'values' => array(_ARTICLES_BLOCK_NONE, _ARTICLES_BLOCK_LAST, _ARTICLES_BLOCK_MANUAL), 'items' => array('���������','��������� ������','������ ����� ������')),
        array('type'=>'memo','name'=>'tmplBlockItem','label'=>'������ �������� �����','height'=>'3'),
        array('type'=>'edit','name'=>'blockCount','label'=>'����������', 'width'=>'50px'),
        array('type'=>'header', 'value' => '������� ��������'),
        array('type'=>'edit','name'=>'previewMaxSize','label'=>'����. ������ ��������','width'=>'50px', 'maxlength'=>'4', 'comment'=>'��������'),
        array('type'=>'checkbox','name'=>'previewSmartSplit','label'=>'"�����" �������� ��������'),
        array('type'=>'header', 'value' => '��������'),
        array('type'=>'edit','name'=>'imageWidth','label'=>'������', 'width'=>'100px'),
        array('type'=>'edit','name'=>'imageHeight','label'=>'������', 'width'=>'100px'),
        array('type'=>'edit','name'=>'imageColor','label'=>'����� ����', 'width'=>'100px', 'comment' => '#RRGGBB'),
        array('type'=>'divider'),
        array('type'=>'text', 'value'=>
          "��� �������� �������� ����� ������������ �������:<br />\n".
          "<b>$(caption)</b> - ���������<br />\n".
          "<b>$(preview)</b> - ������� �����<br />\n".
          "<b>$(text)</b> - ������ �����<br />\n".
          "<b>$(posted)</b> - ���� ����������<br />\n".
          "<b>$(link)</b> - ����� ������ (URL)<br />\n".
          "<b>$(image)</b> - ����� �������� (URL)<br />\n".
          "<b>$(imageWidth)</b> - ������ ��������<br />\n".
          "<b>$(imageHeight)</b> - ������ ��������<br />\n".
          "��� ������� ����� ������ ����������� ������ <b>$(ArticlesBlock)</b><br />\n"
       ),
    ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderArticlesBlock()
  {
    global $db;

    $result = '';
    $items = $db->select($this->table['name'], "`active`='1'".($this->settings['blockMode']==_ARTICLES_BLOCK_MANUAL?" AND `block`='1'":''), $this->table['sortMode'], $this->table['sortDesc'], '', $this->settings['blockCount']);
    if (count($items)) foreach($items as $item)
      $result .= $this->replaceMacros($this->settings['tmplBlockItem'], $item, $this->settings['dateFormatPreview']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderListItem($item)
  {
    $result = $this->replaceMacros($this->settings['tmplListItem'], $item, $this->settings['dateFormatPreview']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderItem()
  {
    global $db, $page, $plugins, $request;

    $item = $db->selectItem($this->table['name'], "(`id`='".$page->topic."')AND(`active`='1')");
    if (is_null($item)) {
      $item = $page->Error404();
      $result = $item['content'];
    } else {
      $result = $this->replaceMacros($this->settings['tmplItem'], $item, $this->settings['dateFormatFullText']);
    }
    $page->section[] = $item['caption'];
    $item['access'] = $page->access;
    $item['name'] = $item['id'];
    $item['title'] = $item['caption'];
    $item['hint'] = $item['description'] = $item['keywords'] = '';
    $plugins->clientOnURLSplit($item, $request['path']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $page;

    $articles = $this->renderArticlesBlock();
    $text = str_replace('$(ArticlesBlock)', $articles, $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>