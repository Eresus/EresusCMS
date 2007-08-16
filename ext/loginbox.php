<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# LoginBox (CMS Eresus� Plugin)
# � 2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TLoginBox extends TContentPlugin {
  var
    $name = 'loginbox',
    $title = 'LoginBox',
    $type = 'client,content',
    $version = '1.00b',
    $description = '����� �����������',
    $settings = array(
      'tmplForm' => '',
      'tmplInfo' => '$(userName)',
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TLoginBox()
  # ���������� ����������� ������������ �������
  {
  global $plugins;

    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderContent()
  {
    global $request, $page, $session, $db;

    if ($request['arg']['action']=='remind') {
      $item = $db->selectItem('users', "`mail`='".strtolower($request['arg']['mail'])."'");
      if (is_null($item)) {
        ErrorMessage('������! ������������ � ����� e-mail '.$request['arg']['mail'].' �� �������!');
        goto($request['path']);
        exit;
      }
      $item['active'] = true;
      srand ((double) microtime() * 1000000);
      for($i=0; $i<7; $i++) $pswd .= sprintf("%c",rand(97,122));
      $item['hash'] = md5($pswd);
      $item['lastVisit'] = gettime();
      $db->updateItem('users', $item, "`id`='".$item['id']."'");
      $message = "������������, \$(userName).<br><br>\n��� ����� ������� ������ �� ����� \"\$(siteName)\" ������������ ����� ������.<br>\n��� �����: <strong>\$(userLogin)</strong><br>\n��� ������: <strong>\$(userPassword)</strong>";
      $message = str_replace(
        array(
          '$(userName)',
          '$(userLogin)',
          '$(userPassword)',
        ),
        array(
         $item['name'],
         $item['name'],
         $pswd,
        ),
      $message);
      $message = $page->replaceMacros($message);
      sendNotify("�������������� ������:\n  ���: ".$item['name']."\n  e-mail: ".$item['mail']);
      sendMail($item['mail'], '�������������� ������', $message, true);
      $session['message'] = '����� ������ ��� ������ �� ����� '.$item['mail'];
      goto($request['path']);
      exit;
    } else {
      $form = array (
        'name' => 'remind',
        'caption' => '�������������� ������',
        'width' => '400px',
        'fields' => array (
          array ('type' => 'hidden', 'name' => 'action', 'value' => 'remind'),
          array('type'=>'edit','name'=>'mail','label'=>'e-mail','maxlength'=>32,'width'=>'100%'),
        ),
        'buttons' => array('ok'),
      );
      $result = $page->renderForm($form);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page;

    $form = array(
      'name'=>'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'memo','name'=>'tmplForm','label'=>'������ ����� ����� ������/������','height'=>'10'),
        array('type'=>'text','value' => '������� action ����� ����� ����� ����� ��������. ������������� ������ ��������: <b>action</b> �� ��������� login, <b>user</b> - ��� ������������ � <b>password</b> - ������ ������������.'),
        array('type'=>'memo','name'=>'tmplInfo','label'=>'������ ����� ���������� � ������������','height'=>'10'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $user, $request, $page;

    if ($user['auth']) $result = $this->settings['tmplInfo'];
    else $result = $this->settings['tmplForm'];
    $text = str_replace('$(plgLoginBox)', $result, $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>