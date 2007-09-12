<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������� ���������� ��������� Eresus�
# ������ 2.10
# � 2004-2007, ProCreat Systems
# � 2007, Eresus Group
# http://eresus.ru/
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ���� ������������� ������� ���������� ������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
define('CMSNAME', 'Eresus'); # �������� �������
define('CMSVERSION', '2.10b2'); # ������ �������
define('CMSLINK', 'http://eresus.ru/'); # ���-����

define('KERNELNAME', 'ERESUS'); # ��� ����
define('KERNELDATE', '22.09.11'); # ���� ���������� ����

# ������ �������
define('ROOT',   1); # ������� �������������
define('ADMIN',  2); # �������������
define('EDITOR', 3); # ��������
define('USER',   4); # ������������
define('GUEST',  5); # ����� (�� ���������������)

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ��������� ������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function FatalError($msg)
# ������� ������� ��������� � ���������������� ������ � ���������� ������ �������.
{
  $result =
    "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n".
    "<html>\n".
    "<head>\n".
    "  <title>".errError."</title>\n".
    "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\">\n".
    "</head>\n\n".
    "<body>\n".
    "  <div align=\"center\" style=\"font-family: Arial, Helvetica, sans-serif;\">\n".
    "    <table cellspacing=\"0\" style=\"border-style: solid;  border-color: #e88 #800 #800 #e88; min-width: 500px;\">\n".
    "      <tr><td style=\"border-style: solid; border-width: 2px; border-color: #800 #e88 #e88 #800; background-color: black; color: yellow; font-weight: bold; text-align: center; font-size: 10pt;\">".errError."</td></tr>\n".
    "      <tr><td style=\"border-style: solid; border-width: 2px; border-color: #800 #e88 #e88 #800; background-color: #c00; padding: 10; color: white; font-weight: bold; font-family: verdana, tahoma, Geneva, sans-serif; font-size: 8pt;\">\n".
    "        <p style=\"text-align: center\">".$msg."</p>\n".
    "        <div align=\"center\"><br /><a href=\"javascript:history.back()\" style=\"font-weight: bold; color: black; text-decoration: none; font-size: 10pt; height: 20px; background-color: #aaa; border-style: solid; border-width: 1px; border-color: #ccc #000 #000 #ccc; padding: 0 2em;\">".strReturn."</a></div>\n".
    "      </td></tr>\n".
    "    </table>\n".
    "  </div>\n".
    "</body>\n".
    "</html>";
  die($result);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function ErrorBox($text, $caption=errError)
# ������� ������� ��������� � ���������������� ������, �� �� ���������� ������ �������.
{
  $result =
    (empty($caption)?'':"<div class=\"errorBoxCap\">".$caption."</div>\n").
    "<div class=\"errorBox\">\n".
    $text.
    "</div>\n";
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function InfoBox($text, $caption=strInformation)
# ������� ������� ��������� � ���������������� ������, �� �� ���������� ������ �������.
{
  $result =
    (empty($caption)?'':"<div class=\"infoBoxCap\">".$caption."</div>\n").
    "<div class=\"infoBox\">\n".
    $text.
    "</div>\n";
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function ErrorHandler($errno, $errstr, $errfile, $errline)
{
  global $Eresus;
  
  if (error_reporting()) switch ($errno) {
    case E_NOTICE:
      if ($Eresus->conf['debug']) ErrorMessage('<b>'.$errstr.'</b> ('.$errfile.', '.$errline.')');
    break;
    case E_WARNING:
      if ($Eresus->conf['debug'])
        FatalError('WARNING! <b>'.$errstr.'</b> in <b>'.$errfile.'</b> at <b>'.$errline.'</b><br /><br />'.(function_exists('callStack')?callStack():''));
    break;
    default:
      FatalError('Error <b>'.$errno.'</b>: <b>'.$errstr.'</b> in <b>'.$errfile.'</b> at <b>'.$errline.'</b>');
    break;
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function ErrorMessage($message)
{
  global $session;
  $session['msg']['errors'][] = $message;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function InfoMessage($message)
{
  global $session;
  $session['msg']['information'][] = $message;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function UserRights($level) {
# ������� ��������� ����� ������������ �� ������������ �������� �����
global $user;

  return ((($user['auth']) && ($user['access'] <= $level) && ($user['access'] != 0)) || ($level == GUEST));
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function Login($login, $hash, $autologin = false, $cookieLogin = false)
#  ������� ���������� ������������ �, ���� �����, ��������� � ������� ���������� ��� ����������
{
global $db, $user, $session;

  $result = false;
  $session['errorMessage'] = '';
  $item = $db->selectItem('users', "`login`='$login'");
  if (!is_null($item)) { # ���� ����� ������������ ����...
    if ($item['active']) { # ���� ������� ������ �������...
      if (time() - $item['lastLoginTime'] > $item['loginErrors']) {
        if ($hash == $item['hash']) { # ���� ������ �����...
          if ($autologin) { # ���� ���������� ������������� "��������� �����", �� ��������� ��� � �������
            setcookie('autologin[active]', '1', time()+2592000, cookiePath, cookieHost);
            setcookie('autologin[login]', $login, time()+2592000, cookiePath, cookieHost);
            setcookie('autologin[hash]', $hash, time()+2592000, cookiePath, cookieHost);
          } else { # ...�����, ������� ������
            setcookie('autologin[active]', '', time() - 3600, cookiePath, cookieHost);
            setcookie('autologin[login]', '', time() - 3600, cookiePath, cookieHost);
            setcookie('autologin[hash]', '', time() - 3600, cookiePath, cookieHost);
          }
          $setVisitTime = !isset($user['id']);
          $lastVisit = isset($user['lastVisit'])?$user['lastVisit']:'';
          $user = $item;
          $user['profile'] = decodeOptions($user['profile']);
          $user['auth'] = true; # ������������� ���� �����������
          if ($setVisitTime) $item['lastVisit'] = gettime(); # ���������� ����� ���������� �����
          $item['lastLoginTime'] = time();
          $item['loginErrors'] = 0;
          $db->updateItem('users', $item,"`id`='".$item['id']."'");
          $session['time'] = time(); # �������������� ����� ��������� ���������� ������.
          $session['msg'] = array();
          $result = true;
        } else { # ���� ������ �� �����...
          if (!$cookieLogin) {
            $session['msg']['errors'][] = errInvalidPassword;
            $item['lastLoginTime'] = time();
            $item['loginErrors']++;
            $db->updateItem('users', $item,"`id`='".$item['id']."'");
          }
        }
      } else { # ���� ����������� ��������� ������� ����
        $session['msg']['errors'][] = sprintf(errTooEarlyRelogin, $item['loginErrors']);
        $item['lastLoginTime'] = time();
        $db->updateItem('users', $item,"`id`='".$item['id']."'");
      }
    } else $session['msg']['errors'][] = sprintf(errAccountNotActive, $login);
  } else $session['msg']['errors'][] = errInvalidPassword;
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function Logout($clearCookies=true)
# ������� ��������� ����� ������ � �������� � �������� ������
{
global $user;

  $user['auth'] = false;
  $user['access'] = GUEST;
  if ($clearCookies) {
    setcookie('autologin[active]', '', time() - 3600, cookiePath, cookieHost);
    setcookie('autologin[login]', '', time() - 3600, cookiePath, cookieHost);
    setcookie('autologin[hash]', '', time() - 3600, cookiePath, cookieHost);
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function ResetLogin()
{
global $db, $user, $session;

  $user['auth'] = isset($user['auth'])?$user['auth']:false;
  if ($user['auth']) {
    $item = $db->selectItem('users', "`id`='".$user['id']."'");
    if (!is_null($item)) { # ���� ����� ������������ ����...
      if ($item['active']) { # ���� ������� ������ �������...
        $user['name'] = $item['name'];
        $user['mail'] = $item['mail'];
        $user['access'] = $item['access'];
        $user['profile'] = decodeOptions($item['profile']);
      } else {
        $session['msg']['errors'][] = sprintf(errAccountNotActive, $item['login']);
        Logout();
      }
    } else Logout();
  } else $user['access'] = GUEST;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function resetLastVisitTime($time='', $expand=false)
{
global $db, $user;

  if ($user['auth']) {
    $item = $db->selectItem('users', "`id`='".$user['id']."'");
    if (empty($time)) $item['lastVisit'] = gettime(); else {
      if ($expand) $time = substr($time,0,4).'-'.substr($time,4,2).'-'.substr($time,6,2).' '.substr($time,8,2).':'.substr($time,10,2);
      $item['lastVisit'] = $time;
    }
    $db->updateItem('users', $item,"`id`='".$item['id']."'");
    $user['lastVisit'] = $item['lastVisit'];
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

### Library support ###

/**
* Link libaray (if it wasn't included)
*
* @access  public
*
* @param  string  $libaray   Library name
*
* @return  bool  Operation result
*/
function useLib($library)
{
  $result = false;
  if (DIRECTORY_SEPARATOR != '/') $library = str_replace('/', DIRECTORY_SEPARATOR, $library);
  $filename = DIRECTORY_SEPARATOR.$library.'.php';
  $dirs = explode(PATH_SEPARATOR, get_include_path());
  foreach ($dirs as $path) if (is_file($path.$filename)) {
    include_once($path.$filename);
    $result = true;
    break;
  }
  return $result;
}
//------------------------------------------------------------------------------

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# �������� �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function sendMail($address, $subject, $text, $html=false, $fromName='', $fromAddr='', $fromOrg='', $fromSign='', $replyTo='')
# ������� �������� ������ �� ���������� ������
{
  global $Eresus;

  if (empty($fromName)) $fromName = option('mailFromName');
  if (empty($fromAddr)) $fromAddr = option('mailFromAddr');
  if (empty($fromOrg)) $fromOrg = option('mailFromOrg');
  if (empty($fromSign)) $fromSign = option('mailFromSign');
  if (empty($replyTo)) $replyTo = option('mailReplyTo');
  if (empty($replyTo)) $replyTo = $fromAddr;

  $charset = option('mailCharset');
  if (empty($charset)) $charset = CHARSET;

  $sender = strlen($fromName) ? "=?".$charset."?B?".base64_encode($fromName)."?= <$fromAddr>" : $fromAddr;
  if (strlen($fromOrg)) $sender .= ' (=?'.$charset.'?B?'.base64_encode($fromOrg).'?=)';
  if (strpos($sender, '@') === false) $sender = 'no-reply@'.preg_replace('/^www\./', '', httpHost);
  $fromSign = "\n-- \n".$fromSign;
  if ($html) $fromSign = nl2br($fromSign);
  if (strlen($fromSign)) $text .= $fromSign;

  $headers =
   "MIME-Version: 1.0\n".
   "From: $sender\n".
   "Subject: $subject\n".
   "Reply-To: $replyTo\n".
   "X-Mailer: PHP/" . phpversion()."\n";

  if ($html) {

    $text = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n<html>\n<head></head>\n<body>\n".$text."\n</body>\n</html>";

    $boundary="=_".md5(uniqid(time()));
    $headers.="Content-Type: multipart/mixed; boundary=$boundary\n";
    $multipart="";
    $multipart.="This is a MIME encoded message.\n\n";

    $multipart.="--$boundary\n";
    $multipart.="Content-Type: text/html; charset=$charset\n";
    $multipart.="Content-Transfer-Encoding: Base64\n\n";
    $multipart.=chunk_split(base64_encode($text))."\n\n";
    $multipart.="--$boundary--\n";
    $text = $multipart;
  } else $headers .= "Content-type: text/plain; charset=$charset\n"; 
  
  if ($Eresus->conf['debug']['enable'] && $Eresus->conf['debug']['mail'] !== true) {
    if (is_string($Eresus->conf['debug']['mail'])) {
      $hnd = @fopen($Eresus->conf['debug']['mail'], 'a');
      if ($hnd) {
        fputs($hnd, "\n================================================================================\n$headers\nTo: $address\nSubject: $subject\n\n$text\n");
        fclose($hnd);
      }
      return true;
    }
  } else return (mail($address, $subject, $text, $headers)===0);
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-//
function sendNotify($notify, $params=null)
# �������� ���������������� ��� ������������ ����������� �� �����
# ��������� ���������
#   subject (string) - ��������� ������, �� ��������� �������� �����
#   title (string) - �������� �������
#   url (string) - ����� �������
#   user (string) - ��� ������������
{
  global $user, $page, $request;

  $subject = isset($params['subject'])?$params['subject']:option('siteName');
  $username = isset($params['user'])?$params['user']:(is_null($user)?'Guest':$user['name']);
  $usermail = !is_null($user) && $user['auth'] ? $user['mail'] : option('mailFormAddr');
  if (defined('ADMINUI')) {
    $editors = isset($params['editors'])?$params['editors']:false;
    $title = isset($params['title'])?$params['title']:$page->title;
    $url = isset($params['url'])?$params['url']:(isset($request['arg']['submitURL'])?$request['arg']['submitURL']:$request['referer']);
  } else {
    $editors = isset($params['editors'])?$params['editors']:true;
    $title = isset($params['title'])?$params['title']:$page->title;
    $url = isset($params['url'])?$params['url']:$request['arg']['submitURL'];
  }
  $target = sendNotifyTo;
  $host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
  if ($host != $_SERVER['REMOTE_ADDR']) $host = "$host ({$_SERVER['REMOTE_ADDR']})";
  $notify = sprintf(strNotifyTemplate, $username, $host, $url, $title, $notify);
  sendMail($target, $subject, nl2br($notify), true, $username, $usermail, '', '');
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ����/�����
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function gettime($format = 'Y-m-d H:i:s')
# ���������� ����� � ������ ��������
{
  #$delta = (GMT_ZONE * 3600) - date('Z'); // �������� �� ������ ������� ����
  $delta = 0;
  return date($format , time() + $delta); // �����, �� ��������� �� ��� ������� ����
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function FormatDate($date, $format=DATETIME_NORMAL)
# ������� ��������� �������������� ����
{
  if (empty($date)) $result = DATETIME_UNKNOWN; else {
    $year = substr($date, 0, 4);
    $month = substr($date, 5, 2);
    $day = substr($date, 8, 2);
    $hour = substr($date, 11, 2);
    $min = substr($date, 14, 2);
    $sec = substr($date, 17, 2);
    $result = str_replace(array('h', 'H', 'i', 's', 'd', 'D', 'm', 'M', 'y', 'Y'), array($hour, ($hour[0]=='0'?$hour[1]:$hour), $min, $sec, $day, ($day[0]=='0'?$day[1]:$day), $month, constant('MONTH_'.$month), substr($year, 2, 2), $year), $format);
  }
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������ � �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function encodeHTML($text)
# �������� ����������� HTML
{
  $trans_tbl = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
  return strtr ($text, $trans_tbl);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function decodeHTML($text)
# ���������� ����������� HTML
{
  $trans_tbl = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
  $trans_tbl = array_flip ($trans_tbl);
  $trans_tbl['%28'] = '(';
  $trans_tbl['%29'] = ')';
  $text = strtr ($text, $trans_tbl); 
  $text = preg_replace('/ilo-[^\s>]*/i', '', $text);
  return $text;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function text2array($value, $assoc=false)
# ��������� ����� �� ������ � ���������� �� ������
# ���� $assoc = true, �� ������������ ������������� ������ key=value
{
  $result = trim($value);
  if (!empty($result)) {
    $result = str_replace("\r",'',$result);
    $result = explode("\n", $result);
    if ($assoc && count($result)) {
      foreach($result as $item) {
        $item = explode('=', $item);
        $items[trim($item[0])] = trim($item[1]);
      }
      $result = $items;
    }
  } else $result = array();
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function array2text($value, $assoc=false)
# �������� �� ������� �����
# ���� $assoc = true, �� ������ ��������������� ��� �������������
{
  $result = '';
  if (count($value)) {
    $result = $value;
    if ($assoc && count($result)) {
      foreach($result as $key => $value) $items[] = $key.'='.$value;
      $result = $items;
    }
    $result = implode("\r\n", $result);
  }
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function encodeOptions($options)
# �������� ��������� �� ������� � ������
{
  global $db;

  $result = serialize($options);
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function decodeOptions($options, $defaults = array())
# ������� ��������� ���������� � ��������� ���� ����� �� ������
{
  if (empty($options)) $result = $defaults; else {
    @$result = unserialize($options);
    /*if (!$result && strpos($options, 'Winkhaus')) {
      $GLOBALS['session']['msg']['errors'] = array();
      echo callstack();
      die;
    }*/
    if (gettype($result) != 'array') $result = $defaults; else {
      if (count($defaults)) foreach($defaults as $key => $value) if (!array_key_exists($key, $result)) $result[$key] = $value;
    }
  }
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function GetArgs($item, $checkboxes = array(), $prevent = array())
# ��������� ������ $item ���������������� ���������� �� $request['arg']
{
global $request;
   
  if ($clear = (key($item) == '0')) $item = array_flip($item);
  foreach ($item as $key => $value) {
    if ($clear) unset($item[$key]);
    if (!in_array($key, $prevent)) {
      if (isset($request['arg'][$key])) $item[$key] = $request['arg'][$key];
      if (in_array($key, $checkboxes)&& (!isset($request['arg'][$key]))) $item[$key] = false;
    }
  }
  return $item;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function setArgs($fields, $checkboxes = array(), $prevent = array()) /* OBSOLETE */
{
  return GetArgs($fields);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function arg($arg)
{
  global $request;
  return isset($request['arg'][$arg])?$request['arg'][$arg]:false;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function saveRequest()
# ������� ��������� � ������ ������� ���������
{
  global $request, $session;
  $session['request'] = $request;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function restoreRequest()
# ������� ��������� � ������ ������� ���������
{
  global $request, $session;
  if (isset($session['request'])) {
    $request = $session['request'];
    unset($session['request']);
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function replaceMacros($template, $item)
{
  preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
  if (count($matches[1])) foreach($matches[1] as $macros) switch(gettype($item)) {
    case 'array'  : if (isset($item[$macros])) $template = str_replace('$('.$macros.')', $item[$macros], $template); break;
    case 'object' : if (isset($item->$macros)) $template = str_replace('$('.$macros.')', $item->$macros, $template); break;
  }
  return $template;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������ � ��
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function getOption($name) /* OBSOLETE */ {return option($name);}
function setOption($name, $data) /* OBSOLETE */ {}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function dbReorderItems($table, $condition='', $id='id')
{
  global $db;

  $items = $db->select("`".$table."`", $condition, '`position`');
  for($i=0; $i<count($items); $i++) {
    $items[$i]['position'] = $i;
    $db->updateItem($table, $items[$i], "`".$id."`='".$items[$i][$id]."'");
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function dbShiftItems($table, $condition, $delta)
{
global $db;

  $items = $db->select("`".$table."`", $condition, '`position`');
  for($i=0; $i<count($items); $i++) {
    $items[$i]['position'] += $delta;
    $db->updateItem($table, $items[$i], "`id`='".$items[$i]['id']."'");
  }
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ������ � �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function upload($name, $filename, $overwrite = true)
{
  $result = false;
  if (substr($filename, -1) == '/') {
    $filename .= option('filesTranslitNames')?Translit($_FILES[$name]['name']):$_FILES[$name]['name'];
    if (file_exists($filename) && ((is_string($overwrite) && $filename != $overwrite ) || (is_bool($overwrite) && !$overwrite))) {
      $i = strrpos($filename, '.');
      $fname = substr($filename, 0, $i);
      $fext = substr($filename, $i);
      $i = 1;
      while (is_file($fname.$i.$fext)) $i++;
      $filename = $fname.$i.$fext;
    }
  }
  switch($_FILES[$name]['error']) {
    case UPLOAD_ERR_OK: 
      if (is_uploaded_file($_FILES[$name]['tmp_name'])) {
        $moved = @move_uploaded_file($_FILES[$name]['tmp_name'], $filename);
        if ($moved) {
          if (option('filesOwnerSetOnUpload')) {
            $owner = option('filesOwnerDefault');
            if (empty($owner)) $owner = fileowner(__FILE__);
            @chown($filename, $owner);
          }
          if (option('filesModeSetOnUpload')) {
            $mode = option('filesModeDefault');
            $mode = empty($mode) ? 0666 : octdec($mode);
            @chmod($filename, $mode);
          }
          $result = $filename;
        } else ErrorMessage(sprintf(errFileMove, $_FILES[$name]['name'], $filename));
      }
    break;
    case UPLOAD_ERR_INI_SIZE: ErrorMessage(sprintf(errUploadSizeINI, $_FILES[$name]['name'])); break;
    case UPLOAD_ERR_FORM_SIZE: ErrorMessage(sprintf(errUploadSizeFORM, $_FILES[$name]['name'])); break;
    case UPLOAD_ERR_PARTIAL: ErrorMessage(sprintf(errUploadPartial, $_FILES[$name]['name'])); break;
    case UPLOAD_ERR_NO_FILE: if (strlen($_FILES[$name]['name'])) ErrorMessage(sprintf(errUploadNoFile, $_FILES[$name]['name'])); break;
  }
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function loadTemplate($name)
# ��������� ��������� ������
{
  $filename = filesRoot.'templates/'.$name.(strpos($name, '.tmpl')===false?'.tmpl':'');
  if (file_exists($filename)) {
    $result['html'] = file_get_contents($filename);
    preg_match('/<!--(.*?)-->/', $result['html'], $result['description']);
    $result['description'] = trim($result['description'][1]);
    $result['html'] = trim(substr($result['html'], strpos($result['html'], "\n")));
  } else $result = false;
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function saveTemplate($name, $template)
# ��������� ��������� ������
{
  $file = "<!-- ".$template['description']." -->\r\n\r\n".$template['html'];
  $fp = fopen(filesRoot.'templates/'.$name.(strpos($name, '.tmpl')===false?'.tmpl':''), 'w');
  fwrite($fp, $file);
  fclose($fp);
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ����� �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function goto($url)
# �������������� ������� �� ����� �����
#   $url - ����� �����
{
  $url = str_replace('&amp;','&',$url);
  if(preg_match('/Apache/i', $_SERVER['SERVER_SOFTWARE'])) header("Location: $url");
  else header("Refresh: 0; URL=$url");
  exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function HttpAnswer($answer)
{
  Header('Content-type: text/html; charset='.CHARSET);
  echo $answer;
  exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function SendXML($data)
# ���������� �������� XML
{
  Header('Content-Type: text/xml');
  echo '<?xml version="1.0" encoding="'.CHARSET.'"?>'."\n<root>".$data."</root>";
  exit;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function option($name) 
{ 
  $result = defined($name)?constant($name):'';
  return $result; 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function img($imagename)
# function img($imagename, $alt='', $title='', $width=0, $height=0, $style='')
# function img($imagename, $params=array())
# ������� ���������� ����������� ��� <img>
{ 
  $argc = func_num_args();
  $argv = func_get_args();
  if ($argc > 1) {
    if (is_array($argv[1])) $p = $argv[1]; else {
      $p['alt'] = $argv[1];
      if ($argc > 2) $p['title'] = $argv[2];
      if ($argc > 3) $p['width'] = $argv[3];
      if ($argc > 4) $p['height'] = $argv[4];
      if ($argc > 5) $p['style'] = $argv[5];
    }
  }
  if (!isset($p['alt']))    $p['alt'] = '';
  if (!isset($p['title']))  $p['title'] = '';
  if (!isset($p['width']))  $p['width'] = '';
  if (!isset($p['height'])) $p['height'] = '';
  if (!isset($p['style']))  $p['style'] = '';
  if (!isset($p['ext']))  $p['ext'] = '';
  if (!isset($p['autosize'])) $p['autosize'] = true;

  
  if (strpos($imagename, httpRoot) !== false) $imagename = str_replace(httpRoot, '', $imagename);
  if (strpos($imagename, filesRoot) !== false) $imagename = str_replace(filesRoot, '', $imagename);
  if (strpos($imagename, '://') === false) $imagename = httpRoot.$imagename;
  $local = (strpos($imagename, httpRoot) === 0);

  if ($p['autosize'] && $local && empty($p['width']) && empty($p['height'])) {
    $filename = str_replace(httpRoot, filesRoot, $imagename);
    if (is_file($filename)) $info = getimagesize($filename);
  }
  if (isset($info)) {
    $p['width'] = $info[0];
    $p['height'] = $info[1];
  };

  $result = '<img src="'.$imagename.'" alt="'.$p['alt'].'"'.
    (empty($p['width'])?'':' width="'.$p['width'].'"').
    (empty($p['height'])?'':' height="'.$p['height'].'"').
    (empty($p['title'])?'':' title="'.$p['title'].'"').
    (empty($p['style'])?'':' style="'.$p['style'].'"').
    (empty($p['ext'])?'':' '.$p['ext']).
  ' />';
  return $result;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function FormatSize($size)
{
  if ($size > 1073741824) {$size = $size / 1073741824; $units = '��'; $z = 2;}
  elseif ($size > 1048576) {$size = $size / 1048576; $units = '��'; $z = 2;}
  elseif ($size > 1024) {$size = $size / 1024; $units = '��'; $z = 2;}
  else {$units = '����'; $z = 0;}
  return number_format($size, $z, '.', ' ').' '.$units;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function Translit($s) #: String
{
  $s = strtr($s, $GLOBALS['translit_table']);
  $s = str_replace(
    array(' ','/','?'),
    array('_','-','7'),
    $s
  );
  $s = preg_replace('/(\s|_)+/', '$1', $s);
  return $s;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# ���������� �������
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
function __clearargs($args)
{
  if (count($args)) foreach($args as $key => $value) 
    if (gettype($args[$key]) == 'array') {
      $args[$key] = __clearargs($args[$key]);
    } else {
      if (get_magic_quotes_gpc()) $value = StripSlashes($value);
      if (strpos($key, 'wyswyg_') === 0) {
        unset($args[$key]);
        $key = substr($key, 7);
        $value = preg_replace('/(<[^>]+) ilo-[^\s>]*/i', '$1', $value);
        $value = str_replace(array('%28', '%29'), array('(',')'), $value);
        $value = str_replace(httpRoot, '$(httpRoot)', $value);
        preg_match_all('/<img.*?>/', $value, $images, PREG_OFFSET_CAPTURE);
        if (count($images[0])) {
          $images = $images[0];
          $delta = 0;
          for($i = 0; $i < count($images); $i++) if (!preg_match('/alt=/i', $images[$i][0])) {
            $s = preg_replace('/(\/?>)/', 'alt="" $1', $images[$i][0]);
            $value = substr_replace($value, $s, $images[$i][1]+$delta, strlen($images[$i][0]));
            $delta += strlen($s) - strlen($images[$i][0]);
          }
        }
      }
      $args[$key] = $value;
    }
  return $args;
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

/**
* �������� ����� ����������
*
* @var  function  $oldErrorHandler  ���������� ���������� ������
* @var  array     $conf             ������������
* @var  array     $session          ������ ������
* @var  object    $db               ��������� � ����
* @var  array     $user             ������� ������ ������������
*/
class Eresus {
  var $oldErrorHandler;
  var $conf = array(
    'lang' => 'ru',
    'timezone' => '',
    'db' => array(
      'engine'   => 'mysql',
      'host'     => 'localhost',
      'user'     => '',
      'password' => '',
      'name'     => '',
      'prefix'   => '',
    ),
    'session' => array(
      'timeout' => 30,
    ),
    'debug' => array(
      'enable' => false,
      'mail' => true,
    ),
  );
  var $session;
  var $db;
  var $user;
  
  var $host;
  var $https;
  var $path;
  var $root;
  var $data;
  var $style;
  var $froot;
  var $fdata;

  var $request;
  
  /**
  * �����������
  */
  function Eresus()
  {
    # ������������� ������������ ������
    $this->oldErrorHandler = set_error_handler('ErrorHandler');
  }
  //------------------------------------------------------------------------------
  // ���������� � �������
  //------------------------------------------------------------------------------
  /**
  * ����� �� Limb3 - http://limb-project.com/
  */
  function isWin32()  { return DIRECTORY_SEPARATOR == '\\'; }
  function isUnix()   { return DIRECTORY_SEPARATOR == '/'; }
  function isMac()    { return !strncasecmp(PHP_OS, 'MAC', 3); }
  function isModule() { return !$this->isCgi() && isset($_SERVER['GATEWAY_INTERFACE']); }
  function isCgi()    { return !strncasecmp(PHP_SAPI, 'CGI', 3); }
  function isCli()    { return PHP_SAPI == 'cli'; }
  #-------------------------------------------------------------------------------
  /**
  * ������ � ��������� ���������������� ����
  *
  * @access  private
  */
  function init_config()
  {
    global $Eresus;
    
    $filename = realpath(dirname(__FILE__).'/..').'/cfg/main.inc';
    if (is_file($filename)) include_once($filename);
    else FatalError("Main config file '$filename' not found!");
  }
  #-------------------------------------------------------------------------------
  /**
  * ���������� ������
  *
  * @access  private
  */
  function init_session()
  {
    session_name('sid');
    session_start();
    $this->session = &$_SESSION['session'];
    $this->user = &$_SESSION['user'];

    # �������� �������������
    $GLOBALS['session'] = &$_SESSION['session'];
    $GLOBALS['user'] = &$_SESSION['user'];
  }
  #-------------------------------------------------------------------------------
  /**
  * ���������� ���� � ������
  *
  * @access  private
  */
  function init_resolve()
  {
    if (is_null($this->froot)) $this->froot = realpath(dirname(__FILE__).'/..').'/';
    if ($this->isWin32()) {
      $this->froot = str_replace('\\', '/', substr($this->froot, 2));
    }
    $this->fdata = $this->froot.'data/';
    
    if (is_null($this->host)) $this->host = strtolower($_SERVER['HTTP_HOST']);
    $this->https = isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']);
    
    if (is_null($this->path)) {
      $s = $this->froot;
      $s = substr($s, strlen(realpath($_SERVER['DOCUMENT_ROOT']))-($this->isWin32()?2:0));
      if (!strlen($s) || $s{strlen($s)-1} != '/') $s .= '/';
      $this->path = ($s{0} != '/' ? '/' : '').$s;
    }
    $this->root = ($this->https ? 'https://' : 'http://').$this->host.$this->path;
    $this->data = $this->root.'data/';
    $this->style = $this->root.'style/';
    
    # �������� �������������
    define('httpPath', $this->path);
    define('filesRoot', $this->froot);
    define('httpHost', $this->host);
    define('httpRoot', $this->root);
    define('styleRoot', $this->style);
    define('dataRoot', $this->data);
    define('cookieHost', $this->host);
    define('cookiePath', $this->path);
    define('dataFiles', $this->fdata);
  }
  //------------------------------------------------------------------------------
  /**
  * ������ ���������
  *
  * @access  private
  */
  function init_settings()
  {
    $filename = $this->froot.'cfg/settings.inc';
    if (is_file($filename)) include_once($filename);
    else FatalError("Settings file '$filename' not found!");
  }
  //------------------------------------------------------------------------------
  /**
  * ��������� ������ �������
  *
  * @access  private
  */
  function init_request()
  {
    global $request;
    
    $s = substr($_SERVER['REQUEST_URI'], strlen($this->path));
    # ���� SID ���������� � URL, �������� ���.
    $sid = 'sid='.session_id();
    if ($x = strpos($s, $sid)) {
      $s = substr_replace($s, '', $x, strlen($sid));
      if (($s{$x-1} == '&') || ($x == strlen($s))) $s = substr_replace($s, '', $x-1, 1);
      else $s = substr_replace($s, '', $x, 1);
    }
    $request['method'] = $_SERVER['REQUEST_METHOD'];
    $request['url'] = $this->root.$s;
    # ������� ���������� URL ��� ������
    $request['link'] = $request['url'];
    if ((strpos($request['link'], '?') === false) && (substr($request['link'], -1) != '/')) $request['link'] .= '/';
    $request['referer'] = isset($_SERVER['HTTP_REFERER'])?$_SERVER['HTTP_REFERER']:'';
    # ���� ���������� ������
    $request['arg'] = __clearargs(array_merge($_GET, $_POST));
    unset($request['arg']['sid']);
    # �������� ���������� ������ �������
    if ($s{0} == '/') $s = substr($s, 1);
    if ($s{strlen($s)-1} == '/') $s = substr($s, 0, -1);
    $request['params'] = $s ? explode('/', $s) : array();
    $this->request = $request;
  }
  //------------------------------------------------------------------------------
  /**
  * ������������� ������
  *
  * @access private
  */
  function init_locale()
  {
    global $locale;

    $locale['lang'] = $this->conf['lang'];
    $locale['prefix'] = '';
    
    # ����������� ��������� ������
    $filename = $this->froot.'lang/'.$locale['lang'].'.inc';
    if (is_file($filename)) include_once($filename);
    else FatalError("Locale file '$filename' not found!");
  }
  //------------------------------------------------------------------------------
  /**
  * ����������� ������� �������
  *
  * @access private
  */
  function init_classes()
  {
    # ����������� ��������� ������
    $filename = $this->froot.'core/classes.php';
    if (is_file($filename)) include_once($filename);
    else FatalError("Classes file '$filename' not found!");
  }
  //------------------------------------------------------------------------------
  /**
  * ����������� � ��������� ������
  *
  * @access private
  */
  function init_datasource()
  {
    global $db;
    
    if (useLib($this->conf['db']['engine'])) {
      $this->db = new $this->conf['db']['engine'];
      $this->db->init($this->conf['db']['host'], $this->conf['db']['user'], $this->conf['db']['password'], $this->conf['db']['name'], $this->conf['db']['prefix']);
      $db = $this->db;
    } else FatalError(sprintf(errLibNotFound, $this->conf['db']['engine']));
  }
  //------------------------------------------------------------------------------
  /**
  * �������� ������
  *
  * @access private
  */
  function check_session()
  {
    if (isset($this->session['time'])) {
      if ((time() - $this->session['time'] > $this->conf['session']['timeout']*3600)&&($this->user['auth'])) Logout(false);
      else $this->session['time'] = time();
    }
  }
  //------------------------------------------------------------------------------
  /**
  * ������������� �������
  *
  * @access public
  */
  function init()
  {
    # ���������� ������������� ������������ ������
    set_magic_quotes_runtime(0);
    # ������ ������������
    $this->init_config();
    # � PHP 5.1.0 ������ ���� ����������� ��������� ���� �� ���������
    if (PHP_VERSION >= '5.1.0') date_default_timezone_set($this->conf['timezone']);
    # ������������� ������
    $this->init_session();
    # ����������� �����
    $this->init_resolve();
    # �������� ���� ������ ������������ ������
    set_include_path(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.PATH_SEPARATOR.get_include_path());
    # ���� ���������� ���� �������, ���������� ���������� ����������
    if ($this->conf['debug']) useLib('debug');
    # ������ ���������
    $this->init_settings();
    # ��������� ������ �������
    $this->init_request();
    # ��������� ������
    $this->init_locale();
    # ����������� ������� �������
    $this->init_classes();
    # ����������� � ��������� ������
    $this->init_datasource();
    
    useLib('sections');
    $this->sections = new TSections;
  }
  //------------------------------------------------------------------------------
  /**
  * ����������
  *
  * @access public
  */
  function execute()
  {  
    # �������� ������
    $this->check_session();
  }
  //------------------------------------------------------------------------------
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#


$GLOBALS['Eresus'] = new Eresus;
$GLOBALS['Eresus']->init();
$GLOBALS['Eresus']->execute();

if (isset($request['arg']['action'])) switch ($request['arg']['action']) {
  case 'login': Login($request['arg']['user'], md5($request['arg']['password']), isset($request['arg']['autologin'])?$request['arg']['autologin']:false); break;
  case 'logout': Logout(true); goto(httpRoot); break;
}

# ������� cookie-������
if ((!isset($user['auth']) || !$user['auth']) && isset($_COOKIE['autologin']) && $_COOKIE['autologin']['active']) {
  if (!Login($_COOKIE['autologin']['login'], $_COOKIE['autologin']['hash'], true, true))
    setcookie("autologin[active]", "0", time()+2592000, cookiePath, cookieHost);
}
# ���������� ������ � ������������
ResetLogin();

# �������� ��������
$plugins = new TPlugins;

$KERNEL['loaded'] = true; # ���� �������� ����
?>
