<?
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
# Print Version (CMS Eresus� Plugin)
# � 2006, ProCreat Systems
# Web: http://procreat.ru
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TPrint extends TPlugin {
  var 
    $name = 'print',
    $title = '������ ��� ������',
    $type = 'client',
    $version = '0.03',
    $description = '����������� �������� � ���� ������� ��� ������',
    $settings = array(
      'template' => '',
    );
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function TPrint()
  # ���������� ����������� ������������ �������
  {
  global $plugins, $request;
  
    parent::TPlugin();
    if (isset($request['params']) && count($request['params']) && ($request['params'][count($request['params'])-1] == 'print')) {
      array_pop($request['params']);
      if (empty($this->settings['template'])) $plugins->events['clientOnContentRender'][] = $this->name;
      else $plugins->events['clientOnStart'][] = $this->name;
      $plugins->events['clientOnPageRender'][] = $this->name;
    }
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ���������������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function settings()
  {
  global $page, $db;
  
    $templates = $this->loadTemplates();
    $form = array(
      'name'=>'SettingsForm',
      'caption' => $this->title.' '.$this->version,
      'width' => '500px',
      'fields' => array (
        array ('type' => 'hidden','name'=>'update', 'value'=>$this->name),
        array ('type' => 'select','name' => 'template','label' => admPagesTemplate, 'items' => $templates[0], 'values' => $templates[1]),
      ),
      'buttons' => array('ok', 'apply', 'cancel'),
    );
    $result = $page->renderForm($form, $this->settings);
    return $result;
  } 
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  # ����������� �������
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnContentRender($text)
  {
    global $page;
    
    $page->template = "<html><head><title>$(siteTitle)</title></head><body>$(Content)</body></html>";
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
  function clientOnStart()
  {
    global $page;
    
    $page->template = 'print';
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------#
  function clientOnPageRender($text)
  {
    global $page;
    
    $page->scripts = '';
    return $text;
  }
  #--------------------------------------------------------------------------------------------------------------------------------------------------------------# 
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>