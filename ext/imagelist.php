<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TImageList extends TListContentPlugin {
  var 
    $name = 'imagelist',
    $type = 'client,content,ondemand',
    $title = '��������',
    $version = '0.01',
    $description = '������ �����������',
    $settings = array(
      'tmplList' => '',
      'tmplListItem' => '',
      'tmplItem' => '',
      'itemsPerPage' => 20,
      'previewWidth' => 120,
      'previewHeight' => 90,
      'buttonBack' => '[ &laquo; ����� ]',
      'buttonNext' => '[ ������ &raquo; ]',
      'imageWidth' => 800,
      'imageHeight' => 600,
    ),
    $table = array (
      'name' => 'imagelist',
      'key'=> 'id',
      'sortMode' => 'position',
      'sortDesc' => false,
      'columns' => array(
        array('name' => 'caption', 'caption' => '��������', 'maxlength'=>100),
        array('name' => 'posted', 'caption' => '����'),
        array('name' => 'image', 'caption' => '����'),
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
         array('caption'=>'��������', 'name'=>'action', 'value'=>'create'),
         #array('caption'=>'�������� �� �����', 'name'=>'action', 'value'=>'load'),
        ),
      ),
      'sql' => "(
        `id` int(10) unsigned NOT NULL auto_increment,
        `section` int(10) unsigned default NULL,
        `position` int(10) unsigned NOT NULL default '0',
        `active` tinyint(1) unsigned default '0',
        `posted` datetime default NULL,
        `caption` varchar(128) default NULL,
        `image` varchar(128) default NULL,
        `preview` varchar(128) default NULL,
        `source` varchar(255) default NULL,
        PRIMARY KEY  (`id`),
        KEY `section` (`section`),
        KEY `position` (`position`),
        KEY `active` (`active`),
        KEY `posted` (`posted`),
        KEY `source` (`source`)
      ) TYPE=MyISAM COMMENT='ImageList';",
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function install()
  {
    parent::install();
    umask(0000);
    if (!file_exists(filesRoot.'data/'.$this->name)) mkdir(filesRoot.'data/'.$this->name, 0777);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function uninstall()
  {
    parent::uninstall();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function encodeFilename($s)
  {
    
    $s = strtr(strtolower($s), array(
      ' '=>'_',
      '�'=> 'a', '�'=> 'b', '�'=> 'v', '�'=> 'g', '�'=> 'd', '�'=> 'e', '�'=> 'yo', '�'=> 'zh', '�'=> 'z', '�'=> 'i', '�'=> 'y', '�'=> 'k', '�'=> 'l', '�'=> 'm', '�'=> 'n', '�'=> 'o', '�'=> 'p', '�'=> 'r', '�'=> 's', '�'=> 't', '�'=> 'u', '�'=> 'f', '�'=> 'h', '�'=> 'tc', '�'=> 'ch', '�'=> 'sh', '�'=> 'sch', '�'=> '', '�'=> 'y', '�'=> '', '�'=> 'e', '�'=> 'yu', '�'=> 'ya',
      '�'=> 'a', '�'=> 'b', '�'=> 'v', '�'=> 'g', '�'=> 'd', '�'=> 'e', '�'=> 'yo', '�'=> 'zh', '�'=> 'z', '�'=> 'i', '�'=> 'y', '�'=> 'k', '�'=> 'l', '�'=> 'm', '�'=> 'n', '�'=> 'o', '�'=> 'p', '�'=> 'r', '�'=> 's', '�'=> 't', '�'=> 'u', '�'=> 'f', '�'=> 'h', '�'=> 'tc', '�'=> 'ch', '�'=> 'sh', '�'=> 'sch', '�'=> '', '�'=> 'y', '�'=> '', '�'=> 'e', '�'=> 'yu', '�'=> 'ya'
    ));
    $s = preg_replace('/[^\d\w_\-\.]/', '', $s);
    if (empty($s)) $s = 'image';
    if (file_exists(filesRoot.'data/'.$this->name.'/'.$s)) {
      $n = 1;
      while (file_exists(filesRoot.'data/'.$this->name.'/'.substr($s, 0, strrpos($s, '.')).$n.substr($s, strrpos($s, '.')))) $n++;
      $s = substr($s, 0, strrpos($s, '.')).$n.substr($s, strrpos($s, '.'));
    }
    return $s;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function resizeImage($filename, $mode, $newname='')
  {
    $path = filesRoot.'data/'.$this->name.'/';
    if (empty($newname)) $newname = substr($filename, 0, strrpos($filename, '.')).'-thmb.jpg';
    $type = getimagesize($path.$filename);
    switch ($type[2]) {
      case IMG_GIF: $src = imageCreateFromGIF($path.$filename); break;
      case IMG_JPG: 
      case IMG_JPEG: $src = imageCreateFromJPEG($path.$filename); break;
      case IMG_PNG: $src = imageCreateFromPNG($path.$filename); break;
    }
    $sW = imageSX($src);
    $sH = imageSY($src);
    $width = $this->settings[$mode.'Width'];
    $height = $this->settings[$mode.'Height'];
    if ($mode == 'preview' || $sW > $width || $sH > $height) {
      $resizer = ($sW > $sH)?($sW / $width):($sH / $height);
      $dst = imageCreateTrueColor($width, $height);
      imageFill($dst, 0, 0, 0);
      $dW = floor($sW / $resizer);
      $dH = floor($sH / $resizer);
      imageCopyResampled($dst, $src, round(($width-$dW)/2), round(($height-$dH)/2), 0, 0, $dW, $dH, $sW, $sH);
      ImageJPEG($dst, $path.$newname);
    }
    return $newname;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function insert()
  {
    global $db, $request;

    $item = GetArgs($db->fields($this->table['name']));
    $item['image'] = $this->encodeFilename($_FILES['image']['name']);
    $item['position'] = 0;
    $item['active'] = true;
    $item['posted'] = gettime();
    dbShiftItems($this->table['name'], "`section`='".$item['section']."'", +1);
    move_uploaded_file($_FILES['image']['tmp_name'], filesRoot.'data/'.$this->name.'/'.$item['image']);
    $item['preview'] = $this->resizeImage($item['image'], 'preview');
    $this->resizeImage($item['image'], 'image', $item['image']);
    $db->insert($this->table['name'], $item);
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;
  
    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    $image = $item['image'];
    $item = GetArgs($item);
    if (!isset($request['arg']['active'])) $item['active'] = false;
    if (!empty($_FILES['image']['tmp_name'])) {
      $item['image'] = $image;
      if (file_exists(filesRoot.'data/'.$this->name.'/'.$item['image'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['image']);
      if (file_exists(filesRoot.'data/'.$this->name.'/'.$item['preview'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['preview']);
      move_uploaded_file($_FILES['image']['tmp_name'], filesRoot.'data/'.$this->name.'/'.$item['image']);
      $item['preview'] = $this->resizeImage($item['image'], 'preview');
      $this->resizeImage($item['image'], 'image', $item['image']);
    }
    $db->updateItem($this->table['name'], $item, "`id`='".$item['id']."'");
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function updateImage()
  {
  global $db, $request, $page;

    $id = $item['id'];
    $item = setArgs($item);
    $item['id'] = $id;
    if (!empty($_FILES['image']['tmp_name'])) {
      $item['image'] = $image;
      if (file_exists(filesRoot.'data/'.$this->name.'/'.$item['image'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['image']);
      if (file_exists(filesRoot.'data/'.$this->name.'/'.$item['preview'])) unlink(filesRoot.'data/'.$this->name.'/'.$item['preview']);
      move_uploaded_file($_FILES['image']['tmp_name'], filesRoot.'data/'.$this->name.'/'.$item['image']);
      $item['preview'] = $this->createPreview($item['image']);
    }
    $db->updateItem($this->sub_table['name'], $item, "`id`='".$item['id']."'");
    sendNotify(admUpdated.': <a href="'.$page->url(array('sub_update'=>'')).'">'.$album['caption'].'</a><br>'.$item['text'], array('url'=>$page->url(array('sub_id'=>''))));
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function deleteImageEx($id)
  {
    global $db;
    
    $item = $db->selectItem($this->table['name'], "`id`='".$id."'");
    $filename = filesRoot.'data/'.$this->name.'/'.$item['image'];
    if (is_file($filename)) unlink($filename);
    $filename = filesRoot.'data/'.$this->name.'/'.$item['preview'];
    if (is_file($filename)) unlink($filename);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function delete($id)
  {
  global $db, $request;
  
    $this->deleteImageEx($id);
    parent::delete($id);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  /*function loadFolder()
  {
    global $request, $db, $session, $user;

    $owner = $db->selectItem($this->table['name'], "`id`='".$request['arg']['owner']."'");
    $folder = $request['arg']['folder'];
    if (strpos($folder, httpRoot) === 0) $folder = substr($folder, strlen(httpRoot));
    if ($folder[strlen($folder)-1] != '/') $folder .= '/';
    $files = glob(filesRoot.$folder.'*.*');
    foreach($files as $file) {
      $item = $db->selectItem($this->sub_table['name'], "`source`='".$file."'");
      if (is_null($item)) {
        $type = getimagesize($file);
        $type = $type[2];
        switch ($type) {
          case 1: $supported = defined('IMG_GIF'); break;
          case 2: $supported = defined('IMG_JPG'); break;
          case 3: $supported = defined('IMG_PNG'); break;
          default: $supported = false;
        }
        if ($supported) {
          $item['caption'] = substr($file, strrpos($file, '/')+1);
          $item['image'] = $this->encodeFilename($item['caption']);
          $item['caption'] = substr($item['caption'], 0, strrpos($item['caption'], '.'));
          $item['owner'] = $owner['id'];
          $item['position'] = $db->count($this->sub_table['name'],"`owner`='".$item['owner']."'");
          $item['active'] = true;
          copy($file, filesRoot.'data/'.$this->name.'/'.$item['image']);
          $item['preview'] = $this->createPreview($item['image'], $type);
          $item['posted'] = gettime();
          $item['updated'] = gettime();
          $item['source'] = $file;
          $db->insert($this->sub_table['name'], $item);
          $owner['images']++;
          $db->updateItem($this->table['name'], $owner, "`id`='".$owner['id']."'");
        } else $session['message'] .= '������ ����� "'.$file.'" �� ��������������<br>';
      } else $session['message'] .= '���� "'.$file.'" ��� ���� � �������<br>';
    }
    goto($request['arg']['submitURL']);
  }*/
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ���������������� �������
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
        array('type'=>'memo','name'=>'tmplList','label'=>'������ ������','height'=>'5'),
        array('type'=>'memo','name'=>'tmplListItem','label'=>'������ �������� ������','height'=>'5'),
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'�������� �� ��������','width'=>'50px', 'maxlength'=>'2'),
        array('type'=>'header', 'value'=>'���������'),
        array('type'=>'edit','name'=>'previewWidth','label'=>'������','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'edit','name'=>'previewHeight','label'=>'������','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'header', 'value'=>'������ ��������'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'������','height'=>'5'),
        array('type'=>'edit','name'=>'imageWidth','label'=>'������','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'edit','name'=>'imageHeight','label'=>'������','width'=>'50px', 'maxlength'=>'3'),
        array('type'=>'edit','name'=>'buttonBack','label'=>'������ �����','width'=>'200px'),
        array('type'=>'edit','name'=>'buttonNext','label'=>'������ ������','width'=>'200px'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderListItem($item)
  {
    global $request;

    $item['url'] = $request['link'].$item['id'].'/';
    $item['preview'] = img('data/imagelist/'.$item['preview']);
    $item['previewWidth'] = $this->settings['previewWidth'];
    $item['previewHeight'] = $this->settings['previewHeight'];
    $result = $this->replaceMacros($this->settings['tmplListItem'], $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderList()
  {
    $result = str_replace('$(items)', parent::clientRenderList(), $this->settings['tmplList']);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientRenderItem()
  {
    global $db, $page, $request;
    
    $item = $db->selectItem($this->table['name'], "`id`='".$page->topic."'");
    if (is_null($item)) $page->HttpError(404); else {
      $item['image'] = img('data/imagelist/'.$item['image']);
      $page->section[] = StripSlashes($item['caption']);

      $items = $db->select($this->table['name'], "`section`='".$page->id."'",  $this->table['sortMode'], $this->table['sortDesc'], '`id`,`caption`');

      for($i=0; $i < count($items); $i++) if ($items[$i]['id'] == $item['id']) {
        if ($i>0) $prev = $items[$i-1];
        if ($i<count($items)-1) $next = $items[$i+1];
      }
      
      #$plugins->clientOnPathSplit($item, $page->name.'/'.$item['id'].'/');
      #if (preg_match('/p[\d]+/i', $request['params'][0])) $page->subpage = substr(array_shift($request['params']), 1);
      #if (count($request['params']) && ($request['arg']['action'] != 'add_image')) {
      #  $page->content['topic'] = array_shift($request['params']);
      #  $result .= $this->renderImage($imagelist, $page->content['topic']);
      #} else $result .= $this->renderAlbum($imagelist);
      $item['back'] = (isset($prev)?'<a href="'.$request['path'].$prev['id'].'">'.$this->settings['buttonBack'].'</a> ':'');
      $item['next'] = (isset($next)?'<a href="'.$request['path'].$next['id'].'">'.$this->settings['buttonNext'].'</a>':'');
      $result = $this->replaceMacros($this->settings['tmplItem'], $item);

    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminAddItem()
  {
  global $page, $request;

    $form = array(
      'name' => 'AddForm',
      'caption' => '�������� ��������',
      'width' => '95%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
        array ('type' => 'hidden', 'name' => 'section', 'value' => $request['arg']['section']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '��������', 'width' => '100%', 'maxlength'=>'128'),
        array ('type' => 'file', 'name' => 'image', 'label'=>'����', 'width' => '70'),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    $result = $page->renderForm($form);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  /*function loadDialog()
  {
    global $request, $page;
  
    $form = array(
      'name' => 'loadForm',
      'caption' => '�������� ����������',
      'width' => '500',
      'fields' => array (
        array ('type' => 'hidden', 'name'=>'action', 'value'=>'loadFolder'),
        array ('type' => 'hidden', 'name'=>'owner', 'value'=>$request['arg']['id']),
        array ('type' => 'edit', 'name' => 'folder', 'label' => '����������', 'width' => '100%'),
        array ('type' => 'text', 'value' => 
          '������� ���� � ����������, �� ������� �� ������ ��������� ����������.<br>'.
          '��������: /old/imagelist/album1/<br>'.
          '��� �� ����� ��������������� <a href="'.httpRoot.'admin.php?mod=files" target="_blank">�������� ����������</a>. ��� �����<ol>'.
          '<li>�������� <a href="'.httpRoot.'admin.php?mod=files" target="_blank">�������� ��������</a>'.
          '<li>������� � ��� ������ ����������'.
          '<li>�������� ���� ��� �� ��� �����. ��� ������� ������ �������� �� �����'.
          '<li>���� � ��� Internet Explorer, ������� �� "����������� ���", ����� ������ �������� � ���������� ��� �������'.
          '<li>�������� �������� ��������'.
          '<li>�������� ����� �� ������ � ���� ����� � ������� ������ OK'.
          '</ol>'
        ),
      ),
      'buttons' => array('ok', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  } */
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'EditForm',
      'caption' => '�������� ��������',
      'width' => '95%',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array ('type' => 'edit', 'name' => 'caption', 'label' => '���������', 'width' => '100%', 'maxlength' => '64'),
        array ('type' => 'file', 'name' => 'image', 'label'=>'����', 'width' => '70'),
        array ('type' => 'divider'),
        array ('type' => 'checkbox', 'name'=>'active', 'label'=>'�������'),
        array ('type' => 'edit', 'name' => 'section', 'label' => '������', 'access'=>ADMIN),
        array ('type' => 'edit', 'name'=>'posted', 'label'=>'��������'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>