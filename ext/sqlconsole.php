<?php
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# CMS Eresus�
# � 2005, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TSQLConsole extends TPlugin {
  var 
    $name = 'sqlconsole',
    $title = 'SQLConsole',
    $type = 'admin',
    $version = '1.02b',
    $description = 'Web SQL �������';
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TSQLConsole()
  # ���������� ����������� ������������ �������
  {
    global $plugins;
  
    parent::TPlugin();
    $plugins->events['adminOnMenuRender'][] = $this->name;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminRender()
  {
  global $db, $page, $request;
    
    $sql = isset($request['arg']['sql']) ? $request['arg']['sql'] : '';
    if (get_magic_quotes_gpc()) $sql = StripSlashes($sql);
    $form = array (
      'name' => 'SQLConsole',
      'caption' => ' ��������� SQL ������:',
      'width' => '100%',
      'fields' => array (
        array ('type' => 'hidden', 'name' => 'action', 'value' => 'exec'),
        array ('type' => 'memo', 'name' => 'sql', 'label' => '����� �������', 'height' => '10', 'value'=>$sql),
      ),
      'buttons'=>array('ok'),
    );
    $result = $page->renderForm($form);
    if (isset($request['arg']['action']) && $request['arg']['action']=='exec') {
      $wnd['caption'] = '��������� �������';
      $wnd['width'] = '100%';
      $wnd['body'] = '';
      $hnd = $db->query($request['arg']['sql'], false);
      if ($hnd == false) $wnd['body'] .= mysql_error();
      if (gettype($hnd) == 'resource') {
        $wnd['body'] .= '�������� �����: '.mysql_num_rows($hnd)."<br>\n";
        $wnd['body'] .= "<table class=\"sqlconsole\">\n";
        $body = false;
        while ($row = mysql_fetch_assoc($hnd)) {
          if (!$body) {
            $keys = array_keys($row);
            $wnd['body'] .= "<tr>";
            foreach($keys as $key) $wnd['body'] .= "<th>".$key."</th>";
            $wnd['body'] .= "</tr>\n";
            $body = true;
          }
          $wnd['body'] .= "<tr>";
          foreach($row as $value) $wnd['body'] .= "<td>".$value."</td>";
          $wnd['body'] .= "</tr>\n";
        }
        $wnd['body'] .= "</table>\n";
      } elseif (gettype($hnd) == 'boolean') 
        if ($hnd) $wnd['body'] .= "������ �������� �������<br>\n��������� �����: ".mysql_affected_rows($db->Connection);
        else "������ ��������� �� �������";
      $result .= '<br>'.$page->window($wnd);
    }
    return $result;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function adminOnMenuRender()
  {
    global $page;
  
    $page->addMenuItem('����������', array ("access"  => ROOT, "link"  => "sqlconsole", "caption"  => "������� SQL", "hint"  => "������� SQL"));
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>