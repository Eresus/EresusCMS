<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005-2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TSiteMap extends TContentPlugin {
  var
    $name = 'sitemap',
    $type = 'client,content,ondemand',
    $title = '����� �����',
    $version = '2.00',
    $description = '����� �������� �����';
  var $settings = array (
    'tmplList' => '<table class="level$(level)">$(items)</table>',
    'tmplItem' => '<tr><td><a href="$(url)" title="$(hint)">$(caption)</a>$(subitems)</td></tr>',
    'showHidden' => false,
    'showPriveleged' => false,
  );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function update()
  {
  global $db, $page, $request;

    $item = $db->selectItem('pages', "`id`='".$request['arg']['update']."'");
    $item['content'] = $request['arg']['content'];
    $db->updateItem('pages', $item, "`id`='".$request['arg']['update']."'");
    goto($page->url());
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page, $db;

    $form = array(
      'name'=>'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'header', 'value'=>'�������'),
        array('type'=>'memo','name'=>'tmplList','label'=>'������ ����� ������ ������ ����', 'height' => '3'),
        array('type'=>'text', 'value' => '�������:<ul><li><b>$(level)</b> - ����� �������� ������</li><li><b>$(items)</b> - ����������</li></ul>'),
        array('type'=>'memo','name'=>'tmplItem','label'=>'������ ����', 'height' => '3'),
        array('type'=>'text', 'value' => '�������:<ul><li><b>��� �������� ��������</b></li><li><b>$(level)</b> - ����� �������� ������</li><li><b>$(url)</b> - ������</li><li><b>$(subitems)</b> - ����� ��� ������� �����������</li></ul>'),
        array('type'=>'header', 'value'=>'�����'),
        array('type'=>'checkbox','name'=>'showHidden','label'=>'���������� ���������'),
        array('type'=>'checkbox','name'=>'showPriveleged','label'=>'���������� ���������� �� ������ �������'),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function brunch($owner = 0, $path = '', $level = 0)
  # ������� ������ ����� ������� �� �������� � id = $owner
  #   $owner - id ��������� ������
  #   $path - ����������� ���� � ���������
  #   $level - ������� �����������
  {
    global $db, $user, $page;

    $result = '';
    if (strpos($path, httpRoot) !== false) $path = substr($path, strlen(httpRoot));
    $items = $db->select('`pages`', "(`owner`='".$owner."') AND (`active`='1')".($this->settings['showPriveleged']?'':" AND (`access`>='".($user['auth']?$user['access']:GUEST)."')").($this->settings['showHidden']?'':" AND (`visible` = '1')"), "`position`");
    if (count($items)) {
      foreach($items as $item) {
        if ($item['type'] == 'url') {
          $item['options'] = decodeOptions($item['options']);
          $item['url'] = $item['content'];
        } else $item['url'] = httpRoot.$path.($item['name']=='main'?'':$item['name'].'/');
        $item['level'] = $level+1;
        $item['selected'] = $item['id'] == $page->id;
        $item['subitems'] = $this->brunch($item['id'], $path.$item['name'].'/', $level+1);
        $result .= $this->replaceMacros($this->settings['tmplItem'], $item);
      }
      $result = array('level'=>($level+1), 'items'=>$result);
      $result = $this->replaceMacros($this->settings['tmplList'], $result);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientRenderContent()
  {
    $result = '<div class="sitemap">'.$this->brunch().'</div>';
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>