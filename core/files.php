<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������� ���������� ��������� Eresus�
# ������ 2.00
# � 2004-2006, ProCreat Systems
# http://procreat.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# �������� ��������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
function files_compare($a, $b)
{
  if ($a['filename'] == $b['filename']) return 0;
  return ($a['filename'] < $b['filename']) ? -1 : 1;
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------# 

class TFiles{
  var 
    $access = EDITOR,
    $icons = array(
      array('ext'=>'php|inc|js','icon'=>'script'),
      array('ext'=>'jpg|jpeg','icon'=>'jpeg'),
      array('ext'=>'gif','icon'=>'gif'),
      array('ext'=>'bmp','icon'=>'bmp'),
      array('ext'=>'swf','icon'=>'flash'),
      array('ext'=>'htm|html|shtml','icon'=>'html'),
      array('ext'=>'wav|mid|mp3','icon'=>'audio'),
      array('ext'=>'avi|mov|mpg|mpeg','icon'=>'video'),
      array('ext'=>'txt','icon'=>'text'),
      array('ext'=>'exe','icon'=>'app'),
      array('ext'=>'rar','icon'=>'rar'),
      array('ext'=>'zip','icon'=>'zip'),
      array('ext'=>'doc','icon'=>'word'),
      array('ext'=>'xls','icon'=>'excel'),
      array('ext'=>'pdf','icon'=>'pdf'),
    );
  var $root;
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function url($args = null)
  {
  global $request;

    $basics = array('lf','rf','sp');
    $result = '';
    if (count($request['arg'])) foreach($request['arg'] as $key => $value) if (in_array($key,$basics)) $arg[$key] = $value;
    if (count($args)) foreach($args as $key => $value) $arg[$key] = $value;
    if (count($arg)) foreach($arg as $key => $value) if (!empty($value)) $result .= '&amp;'.$key.'='.$value;
    $result = httpRoot.'admin.php?mod=files'.$result;
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function renderMenu()
  {
    $menu = array (
      array (
        'name' => 'folder',
        'caption' => '�����',
        'action' => "javascript:filesMkDir()",
        'active' => true,
      ),
      array (
        'name' => 'copy',
        'caption' => '����������',
        'action' => 'javascript:filesCopy()',
        'active' => true,
      ),
      array (
        'name' => 'move',
        'caption' => '�����������',
        'action' => 'javascript:filesMove()',
        'active' => UserRights(ADMIN),
      ),
      array (
        'name' => 'delete',
        'caption' => '�������',
        'action' => "javascript:filesDelete()",
        'active' => UserRights(ADMIN),
      ),
    );
  
    $result =
      "<table cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n".
      "<tr>";
    foreach($menu as $item) if ($item['active']) $result .= "<td onMouseOver=\"buttonOver(this)\" onMouseOut=\"buttonOut(this)\" onClick=\"".$item['action']."\">".$item['caption']."</td>\n";
    $result .=
      "</tr>".
      "</table>";
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function buildFileList($path)
  {
    $dir = (substr($path, 0, 1) == '/')?substr($path, 1):$path;
    $hnd=opendir(filesRoot.$this->root.$dir);
    $i = 0;
    while (($name = readdir($hnd))!==false) if ($name != '.') {
      $result[$i]['filename'] = $name;
      $perm = fileperms(filesRoot.$this->root.$dir.'/'.$name);
      $perm = $perm - 32768;
      if ($perm < 0) $perm += 16384;
      $result[$i]['perm'] = '';
      for($j=0; $j<3; $j++) {
        $x = $perm % 8;
        $perm /= 8;        
        $result[$i]['perm'] = (($x % 2 == 1)?'x':'-').$result[$i]['perm'];
        $x = ($x - ($x % 2)) / 2;
        $result[$i]['perm'] = (($x % 2 == 1)?'w':'-').$result[$i]['perm'];
        $x = ($x - ($x % 2)) / 2;
        $result[$i]['perm'] = (($x % 2 == 1)?'r':'-').$result[$i]['perm'];
      }
      $s = getenv('WINDIR');
      if (empty($s)) {
        $result[$i]['owner'] = posix_getpwuid(fileowner(filesRoot.$this->root.$dir.'/'.$name));
        $result[$i]['owner'] = $result[$i]['owner']['name'];
      } else $result[$i]['owner'] = 'unknown';
      switch (filetype(filesRoot.$this->root.$dir.'/'.$name)) {
        case 'dir':
          $result[$i]['icon'] = 'folder'; 
          $result[$i]['size'] = '�����';
          $result[$i]['link'] = ($name == '..')?substr($path, 0, strrpos($path, '/')):$path.'/'.$name;
          $result[$i]['action'] = 'cd';
        break;
        case 'file':
          $result[$i]['link'] = httpRoot.$this->root.$dir.'/'.$name;
          $result[$i]['size'] = number_format(filesize(filesRoot.$this->root.$dir.'/'.$name));
          $result[$i]['action'] = 'new';
          $result[$i]['icon'] = 'file';
          if (count($this->icons)) foreach($this->icons as $item) if (preg_match('/\.('.$item['ext'].')$/i', $name)) {
            $result[$i]['icon'] = $item['icon'];
            break;
          }
        break;
      }
      $result[$i]['date'] = strftime("%y-%m-%d %H:%I:%S", filemtime(filesRoot.$this->root.$dir.'/'.$name));
      $i++; 
    }
    closedir($hnd); 
    if (count($result)) {
      usort ($result, "files_compare");
      if (count($result) > 1) {
        for ($i=1; $i<count($result); $i++) {
          if ($result[$i]['icon'] == 'folder') {
            $k = $i;
            while (($k>0)&&(($result[$k-1]['icon'] != 'folder')||(($result[$k-1]['icon'] == 'folder')&&($result[$k-1]['filename'] > $result[$k]['filename'])))) {
              $tmp = $result[$k];
              $result[$k] = $result[$k-1];
              $result[$k-1] = $tmp;
              $k--;
            }
          }
        }
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function renderFileList($side)
  {
  global $request;
    $path = isset($request['arg'][$side.'f'])?$request['arg'][$side.'f']:'';
    $items = $this->BuildFileList($path);
    $result =
      "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"filesList\" id=\"".$side."Panel\">\n".
      "<tr class=\"filesListPath\"><th colspan=\"5\">.".((empty($path)) ? '/' : $path)."</th></tr>\n".
      "<tr class=\"filesListHdr\"><th>&nbsp;</th><th>��� �����</th><th>������</th><th>�����</th><th>������</th><th>��������</th><th style=\"width: 100%\">&nbsp;</th></tr>\n";
    for ($i = 0; $i < count($items);  $i++) {
      $result .= "<tr onClick=\"rowSelect(this)\" onDblClick=\"";
      switch ($items[$i]['action']) {
        case 'cd': $result .= "javascript:filesCD('".$this->url(array($side.'f'=>$items[$i]['link']))."')"; break;
        case 'new': $result .= "window.open('".$items[$i]['link']."');"; break;
      }
      $result .= "\"><td>".img('core/img/icon_'.$items[$i]['icon'].'.gif')."</td><td>".$items[$i]['filename']."</td><td align=\"right\">".$items[$i]['size']."</td><td>".$items[$i]['date']."</td><td>".$items[$i]['perm']."</td><td>".$items[$i]['owner']."</td><td width=\"100%\">&nbsp;</td></tr>\n";
    }
    $result .= "</table>\n";
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function renderControls()
  {
  global $request;
    $result =
      "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n".
      "<tr><td align=\"center\">��������� ����</td><td><form name=\"upload\" action=\"".$request['url']."\" method=\"post\" enctype=\"multipart/form-data\"><input type=\"file\" name=\"upload\" size=\"50\"> <input type=\"submit\" value=\"���������\"> ������������ ������ �����: ".ini_get('upload_max_filesize')."</form></td></tr>".
      "<tr><td align=\"center\"><a href=\"javascript:Copy('SelFileName');\">����������� ���</a></td><td style=\"width: 100%;\"><input type=\"text\" id=\"SelFileName\" value=\"��� ��������� ��������\" style=\"width: 100%;\"></td></tr>".
      "</table>";
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function renderStatus()
  {
  global $request;
    $result =
      "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\">\n".
      "<tr><td>��������� �����: ".FormatSize(disk_free_space(filesRoot.$this->root))."</td></tr>".
      "</table>";
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function upload()
  {
  global $request;
  
    foreach($_FILES as $name => $file) {
      upload($name, substr(filesRoot.$this->root, 0, strlen(filesRoot.$this->root)-1).$request['arg'][$request['arg']['sp'].'f'].'/'.$file['name']);
    }
    goto($request['referer']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function mkDir()
  {
  global $request;
  
    umask(0000);
    mkdir(substr(filesRoot.$this->root, 0, strlen(filesRoot.$this->root)-1).$request['arg'][$request['arg']['sp'].'f'].'/'.$request['arg']['mkdir'], 0777);
    goto($this->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function rmDir($path)
  {
    if (UserRights(ADMIN)) {
      $hnd=@opendir($path);
      if ($hnd) {
        while (($name = readdir($hnd))!==false) if (($name != '.')&&($name != '..')) {
          switch (filetype($path.'/'.$name)) {
            case 'dir':
              $this->rmDir($path.'/'.$name);
              rmdir($path.'/'.$name);
            break;
            case 'file': unlink($path.'/'.$name); break;
          }
        }
        closedir($hnd);
      }
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function copyFile()
  {
  global $request;
  
    $filename = filesRoot.$this->root.$request['arg'][$request['arg']['sp'].'f'].'/'.$request['arg']['copyfile'];
    $dest = filesRoot.$this->root.$request['arg'][($request['arg']['sp']=='l'?'r':'l').'f'].'/'.$request['arg']['copyfile'];
    if (is_file($filename)) copy($filename, $dest);
    elseif (is_dir($filename)) {
    }
    goto($this->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function moveFile()
  {
  global $request;
  
    if (UserRights(ADMIN)) {
      $filename = filesRoot.$this->root.$request['arg'][$request['arg']['sp'].'f'].'/'.$request['arg']['movefile'];
      $dest = filesRoot.$this->root.$request['arg'][($request['arg']['sp']=='l'?'r':'l').'f'].'/'.$request['arg']['movefile'];
      if (is_file($filename)) {
        copy($filename, $dest);
        unlink($filename);
      } elseif (is_dir($filename)) {
      }
    }
    goto($this->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function deleteFile()
  {
  global $request;
  
    if (UserRights(ADMIN)) {
      $filename = filesRoot.$this->root.$request['arg'][$request['arg']['sp'].'f'].'/'.$request['arg']['delete'];
      if (is_file($filename)) unlink($filename);
      elseif (is_dir($filename)) {
        $this->rmDir($filename);
        rmdir($filename);
      }
    }
    goto($this->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminRender()
  {
  global $request, $page;
    
    $this->root = UserRights(ADMIN)?'':'data/';
    if (count($_FILES)) $this->upload();
    elseif (isset($request['arg']['mkdir'])) $this->mkDir();
    elseif (isset($request['arg']['copyfile'])) $this->copyFile();
    elseif (isset($request['arg']['movefile'])) $this->moveFile();
    elseif (isset($request['arg']['delete'])) $this->deleteFile();
    else {
      $page->head .= "   <script language=javascript src=\"".httpRoot."core/files.js\" type=\"text/javascript\"></script>\n";
      $result = 
        "<table id=\"fileManager\"width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" border=\"0\" class=\"files\">\n".
        '<tr><td colspan="2" class="filesMenu">'.$this->renderMenu()."</td></tr>\n".
        '<tr><td colspan="2" class="filesControls">'.$this->renderControls()."</td></tr>".
        '<tr>'.
        '<td valign="top" width="50%" class="filesPanel">'.$this->renderFileList('l')."</td>\n".
        '<td valign="top" width="50%" class="filesPanel">'.$this->renderFileList('r')."</td>\n".
        "</tr>\n".
        '<tr><td colspan="2" class="filesControls">'.$this->renderStatus()."</td></tr>".
        "</table>".
        "<script language=javascript type=\"text/javascript\"><!--\n".
        " filesInit('".httpRoot.$this->root."', '".(empty($request['arg']['sp'])?'l':$request['arg']['sp'])."');\n".
        "--></script>\n";
      return $result;
    }
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>