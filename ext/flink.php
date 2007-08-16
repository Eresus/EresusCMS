<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TFLink extends TPlugin {
  var $name = 'flink';
  var $title = 'FLink';
  var $type = 'client';
  var $version = '1.00a';
  var $description = '������ �� �����';
  var $settings = array(
    'folder' => 'download',
    'template' => '<a href="$(url)">$(caption)</a> ($(size))',
  );
  var $path = array(); # ������ ����
  var $level = -1; # ����������� ��������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function TFlink()
  # ���������� ����������� ������������ �������
  {
  global $plugins;

    parent::TPlugin();
    $plugins->events['clientOnPageRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function settings()
  {
  global $page;

    $form = array(
      'name' => 'Settings',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array('type'=>'hidden','name'=>'update', 'value'=>$this->name),
        array('type'=>'edit','name'=>'folder','label'=>'�������� �����','width'=>'100%'),
        array('type'=>'edit','name'=>'template','label'=>'������','width'=>'100%'),
        array('type'=>'text','value'=>
          "������:\n".
          "<ul>\n".
          "  <li><b>$(url)</b> - ������ �� ����</li>\n".
          "  <li><b>$(caption)</b> - �������� ����� (��. ����)</li>\n".
          "  <li><b>$(filename)</b> - ��� �����</li>\n".
          "  <li><b>$(size)</b> - ������ �����</li>\n".
          "</ul>"
        ),
        array('type'=>'divider'),
        array('type'=>'text','value'=>"�������� ������ $(flink:���_�����:��������_�����) ������� �� ����. ':��������_�����' ����� ���� �������"),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $page;

    $result = $text;
    preg_match_all('/\$\(flink:([^:\)]+)(:([^\)]+))?\)/', $text, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
    if (count($matches)) {
      if (substr($this->settings['folder'], 0, 1) == '/') $this->settings['folder'] = substr($this->settings['folder'], 1);
      if (substr($this->settings['folder'], -1) != '/') $this->settings['folder'] .= '/';
      $delta = 0;
      foreach($matches as $match) {
        $filename = filesRoot.$this->settings['folder'].$match[1][0];
        $info['filename'] = basename($filename);
        $info['size'] = FormatSize(@filesize($filename));
        $info['caption'] = empty($match[3][0])?$info['filename']:$match[3][0];
        $info['url'] = str_replace(filesRoot, httpRoot, $filename);
        $replace = $this->replaceMacros($this->settings['template'], $info);
        $result = substr_replace($result, $replace, $delta+$match[0][1], strlen($match[0][0]));
        $delta += strlen($replace) - strlen($match[0][0]);
      }
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>