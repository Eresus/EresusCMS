<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TFeedBack extends TListContentPlugin {
  var
    $name = 'feedback',
    $type = 'client,content',
    $title = '�������� �����',
    $version = '0.02',
    $description = '������� �������� ����� ������-�����',
    $settings = array(
      'sendto' => '',
      'itemsPerPage' => 20,
      'message' => '��� ������ ����� ��������� ���������� ��������.',
      'messageSent' => '��� ������ ��������� ���������� ��������. ����� ��������� ����� ����� ����� �����������.',
      'lastQuestions' => true,
      'lastQuestionsCount' => 10,
    ),
    $table = array (
      'name' => 'feedback',
      'key'=> 'id',
      'sortMode' => 'posted',
      'sortDesc' => true,
      'columns' => array(
        array('name' => 'caption', 'caption' => '���������'),
        array('name' => 'posted', 'caption' => '�����'),
      ),
      'controls' => array (
        'delete' => '',
        'edit' => '',
        'toggle' => '',
      ),
      'sql' => "(
        `id` int(10) unsigned NOT NULL auto_increment,
        `section` int(10) unsigned default NULL,
        `active` tinyint(1) unsigned default NULL,
        `posted` datetime default NULL,
        `caption` varchar(255) default NULL,
        `question` text,
        `name` varchar(64) default NULL,
        `mail` varchar(64) default NULL,
        `answer` text,
        PRIMARY KEY  (`id`),
        KEY `section` (`section`),
        KEY `active` (`active`),
        KEY `posted` (`posted`)
      ) TYPE=MyISAM AUTO_INCREMENT=1;",
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TFeedBack()
  # ���������� ����������� ������������ �������
  {
  global $plugins, $page;

    parent::TListContentPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function insert()
  {
  global $db, $page, $user, $request, $session;

    $item = getArgs($db->fields($this->table['name']));
    $item['active'] = false;
    $item['posted'] = date('Y-m-d H:i:s');
    $db->insert($this->table['name'], $item);
    sendMail($this->settings['sendto'], '����� ������', '����� <strong>'.$item['name'].' ('.$item['mail'].')</strong><br>'.$item['question'], true);
    goto($request['referer']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['update']."'");
    foreach ($item as $key => $value) if (isset($request['arg'][$key])) $item[$key] = $request['arg'][$key];
    if (!isset($request['arg']['active'])) $item['active'] = false;
    $db->updateItem($this->table['name'], $item, "`id`='".$request['arg']['update']."'");
    goto($request['arg']['submitURL']);
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminEditItem()
  {
  global $db, $page, $request;

    $item = $db->selectItem($this->table['name'], "`id`='".$request['arg']['id']."'");
    $form = array(
      'name' => 'EditForm',
      'caption' => '������������� ������',
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
        array('type'=>'edit', 'name' => 'caption', 'label' => '������� ������', 'width' => '100%', 'maxlength' => '255'),
        array('type'=>'edit', 'name' => 'name', 'label' => '��� ������', 'width' => '100%', 'maxlength' => '64'),
        array('type'=>'edit', 'name' => 'mail', 'label' => 'E-mail', 'width' => '100%', 'maxlength' => '64'),
        array('type'=>'memo', 'name' => 'question', 'label' => '������', 'height' => '5'),
        array('type'=>'memo', 'name' => 'answer', 'label' => '�����', 'height' => '5'),
        array('type'=>'checkbox', 'name'=>'active', 'label'=>'������������'),
        array('type'=>'divider'),
        array('type'=>'edit', 'name' => 'posted', 'label' => '����/�����', 'width' => '100px', 'maxlength' => '20'),
        array('type'=>'edit', 'name' => 'section', 'label' => '������', 'access'=>ADMIN),
      )
    );
    $result = $page->renderForm($form, $item);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page;

    $form = array(
      'name' => 'settingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '400px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'edit','name'=>'sendto','label'=>'����� ��� ����������','width'=>'200px'),
        array('type'=>'edit','name'=>'message','label'=>'�����','width'=>'100%'),
        array('type'=>'edit','name'=>'messageSent','label'=>'���������','width'=>'100%'),
        array('type'=>'edit','name'=>'itemsPerPage','label'=>'��������� �� ��������','width'=>'50px'),
        array('type'=>'header','name'=>'���� ��������� ��������'),
        array('type'=>'checkbox','name'=>'lastQuestions','label'=>'���������� ����'),
        array('type'=>'edit','name'=>'lastQuestionsCount','label'=>'���������� ��������','width'=>'50px'),
      ),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderListItem($item)
  {
    $result =
      '<h4 class="feedbackCaption"><span onClick="feedbackClick(\'feedback'.$item['id'].'\')">'.StripSlashes($item['caption']).'</span></h4>'.
      '<div class="feedbackBox" id="feedback'.$item['id'].'" style="display: none;">'.
      '<div class="feedbackInfo">'.StripSlashes($item['name']).' ('.FormatDate($item['posted'], DATETIME_NORMAL).')</div>'.
      '<div class="feedbackQuestion">'.StripSlashes($item['question']).'</div>'.
      '<div class="feedbackAnswer">'.StripSlashes($item['answer']).'</div>'.
      '</div>';
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function  clientRenderContent()
  {
  global $db, $page;

    $page->scripts .= "
      function feedbackClick(strName)
      {
        var Box = document.getElementById(strName);
        if (Box.style.display == 'none') Box.style.display = 'block'; else Box.style.display = 'none';
      }
    ";
    $result = '';
    if (arg('action') == 'insert') $this->insert();
    else {
      $form = array (
        'name' => 'QuestionForm',
        'caption' => '������� ���� ������',
        'width' => '400px',
        'fields' => array (
          array ('type' => 'hidden', 'name' => 'action', 'value' => 'insert'),
          array ('type' => 'hidden', 'name' => 'section', 'value' => $page->id),
          array ('type' => 'memo', 'name' => 'question', 'label' => '������', 'height' => '5', 'pattern'=>'/.+/', 'errormsg'=>'������ �� ����� ���� ������.'),
          array ('type' => 'edit', 'name' => 'name', 'label' => '<nobr>���� ���</nobr>', 'width' => '100%', 'maxlength' => '64', 'pattern'=>'/.+/', 'errormsg'=>'��� �� ����� ���� ������.'),
          array ('type' => 'edit', 'name' => 'mail', 'label' => '<nobr>��� e-mail</nobr>', 'width' => '100%', 'maxlength' => '64', 'pattern'=>'/.+/', 'errormsg'=>'E-mail �� ����� ���� ������.'),
          array ('type' => 'text', 'value' => '<div style="text-align: center; padding-top: 5px;">'.$this->settings['message'].'</div>'),
        ),
        'buttons' => array('ok'),
      );
      $result .= parent::clientRenderContent();
      $form = $page->renderForm($form);
      $result = $form.'<hr />'.$result;
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
  global $page, $db;

    $result = '';
    $items = $db->select($this->table['name'], "`active`='1'", 'posted', true, '', $this->settings['lastQuestionsCount']);
    if (count($items)) foreach($items as $item) {
      $result .=
        '<div class="feedbackLast">'.
        '<div class="caption"><b>'.$item['name'].':</b> '.StripSlashes($item['caption']).'</div>'.
        '<div class="answer"><b>�����:</b> '.StripSlashes($item['answer']).'</div>'.
        '</div>';
    }
    $text= str_replace('$(FeedbackLast)', $result, $text);
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>