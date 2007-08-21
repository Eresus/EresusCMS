<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005-2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class THtml extends TContentPlugin {
  var $name = 'html';
  var $type = 'client,content,ondemand';
  var $title = 'HTML';
  var $version = '2.03';
  var $description = 'HTML ��������';
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page;

    $item = $db->selectItem('pages', "`id`='".arg('update')."'");
    $item['content'] = arg('content');
    $item['options'] = decodeOptions($item['options']);
    $item['options']['allowGET'] = arg('allowGET');
    $item['options'] = encodeOptions($item['options']);
    $db->updateItem('pages', $item, "`id`='".$item['id']."'");
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function adminRenderContent()
  {
  global $db, $page, $request;

    if (isset($request['arg']['update'])) $this->update($request['arg']['update']);
    else {
      $item = $db->selectItem('pages', "`id`='".$request['arg']['section']."'");
      $item['options'] = decodeOptions($item['options']);
      $url = $page->clientURL($item['id']);
      $form = array(
        'name' => 'contentEditor',
        'caption' => '����� ��������',
        'width' => '100%',
        'fields' => array (
          array ('type' => 'hidden','name' => 'update', 'value'=>$item['id']),
          array ('type' => 'html','name' => 'content','height' => '400px', 'value'=>$item['content']),
          array ('type' => 'text', 'value' => '����� ��������: <a href="'.$url.'" target="_blank">'.$url.'</a>'),
          array ('type' => 'checkbox','name' => 'allowGET', 'label' => '��������� ��������� GET', 'value'=>isset($item['options']['allowGET'])?$item['options']['allowGET']:false),
        ),
        'buttons'=> array('ok', 'reset'),
      );
      $result = $page->renderForm($form);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderContent()
  {
    global $request, $page;
    if (isset($page->topic)) {
      if (!($page->options['allowGET'] && (strpos($page->topic, execScript.'?') === 0))) $page->httpError('404');
    }
    return parent::clientRenderContent();
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>