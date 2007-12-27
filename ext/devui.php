<?php
/**
 * DevUI
 *
 * Eresus 2
 *
 * Developer User Interface - ����������� ������������ 
 *
 * @version 0.01a
 *
 * @copyright   2007, Eresus Group, http://eresus.ru/
 * @license     http://www.gnu.org/licenses/gpl.txt  GPL License 3
 * @maintainer  Mikhail Krasilnikov <mk@procreat.ru>
 * @author      Mikhail Krasilnikov <mk@procreat.ru>
 *
 * ������ ��������� �������� ��������� ����������� ������������. ��
 * ������ �������������� �� �/��� �������������� � ������������ �
 * ��������� ������ 3 ���� (�� ������ ������) � ��������� ����� �������
 * ������ ����������� ������������ �������� GNU, �������������� Free
 * Software Foundation.
 *
 * �� �������������� ��� ��������� � ������� �� ��, ��� ��� ����� ���
 * ��������, ������ �� ������������� �� ��� ������� ��������, � ���
 * ����� �������� ��������� ��������� ��� ������� � ����������� ���
 * ������������� � ���������� �����. ��� ��������� ����� ���������
 * ���������� ������������ �� ����������� ������������ ��������� GNU.
 *
 * �� ������ ���� �������� ����� ����������� ������������ ��������
 * GNU � ���� ����������. ���� �� �� �� ��������, �������� �������� ��
 * <http://www.gnu.org/licenses/>
 */

class DevUI extends Plugin {
	var $version = '0.01a';
  var $kernel = '2.10b2';
  var $title = 'DevUI';
  var $description = 'Developer UI';
  var $type = 'admin';
 /**
  * �����������
  * @return DevUI
  */
  function DevUI()
  {
  	parent::Plugin();
  	$this->listenEvents('adminOnMenuRender');
  }
  //-----------------------------------------------------------------------------
 /**
  * ������ ���������� ��������
  *
  * @return string
  */
  function runScripts()
  {
  	global $page;
  	
  	$result = '';
  	if (arg('run')) {
  		ob_start();
  		include(filesRoot.'distrib/'.arg('run'));
  		$result .= ob_get_clean();
  	} else {
	  	$files = glob(filesRoot.'distrib/*.php');
	  	for($i=0; $i<count($files); $i++) {
	  		$result .= '<a href="'.$page->url(array('mode'=>'run', 'run' => basename($files[$i]))).'">'.basename($files[$i]).'</a><br />';
	  	}
  	}
  	return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * ������������ �������
  * 
  * @return string
  */
  function adminRender()
  {
  	switch(arg('mode')) {
  		case 'run': $result = $this->runScripts(); break;
  		default: $result = '';
  	}
  	return $result;
  }
  //-----------------------------------------------------------------------------
 /**
  * ���������� ������� 'adminOnMenuRender'
  *
  */
  function adminOnMenuRender()
  {
    global $page;
  
    $caption = 'DevUI';
    $page->addMenuItem($caption, array ('access'  => EDITOR, 'link'  => $this->name.'&mode=run', 'caption'  => 'Run scripts', 'hint'  => 'Run standalone scripts within the Eresus'));
  }
  //-----------------------------------------------------------------------------
}
//-----------------------------------------------------------------------------

?>