<?php
define('styleRoot_', httpRoot.'ext/objects/style/');
define('classesPath', filesRoot.'ext/objects/classes/');
define('classesRoot', httpRoot.'ext/objects/classes/');

/**
 * ���������� id � ����� ��������� ������������� ��������
 *
 * @param unknown_type $values
 */
function root_owner($id, $class)
{
	$ctl=& loadclass($class);
	$values= $ctl->restore($id, null, null, null, null, "`ownerid`, `ownerclass`");
	if (empty($values) || $values['ownerclass'] == 'root') return array($id, $class);
	return root_owner($values['ownerid'], $values['ownerclass']);
}

/**
 * ���������� ������ ������� �������� ��������� ������������� ��������.
 * ������ �������� ������� �������� ��������� (�������� ������� �����������) ���
 * ���� ��������
 *
 * @param unknown_type $id
 * @param unknown_type $class
 */
function settings($id, $class)
{
	$ctl=& loadclass($class);
	$values= $ctl->restore($id, null, null, null, null, "`ownerid`, `ownerclass`");
	if (empty($values)) return null;
	if ($values['ownerclass'] == 'root') return $ctl->restore($id);
	return settings($values['ownerid'], $values['ownerclass']);
}

/**
 * ���������� ������ �� �������� ���� ������������ ���������
 *
 * @param unknown_type $id
 * @param unknown_type $class
 * @return unknown
 */
function owners($id, $class)
{
	$ctl=& loadclass($class);
	$values= $ctl->restore($id);
	if (empty($values) || $values['ownerclass'] == 'root') return array($values);
	return array_unshift(owners($values['ownerid'], $values['ownerclass']), $values);
}


/**
 * ������� ���������� ����, ��� �������������� ������� �� ������ id ��� ������ � ��������� � �����
 *
 * @param ���_������� $name
 * @param �����_������� $class
 * @param id_��������� $ownerid
 * @param �����_��������� $ownerclass
 * @return ������
 */
function objecturl($id, $class, $name=null, $ownerid=null, $ownerclass=null, $savehistory= true, $url= null)
{
	if (!isset($url)) $url= $GLOBALS['request']['url'];

	if (strpos($url, '?') === false) $url.= 'exec.php?cs=true';

	if ($savehistory) {
		//�������� ��� ajax ���������
		$url= preg_replace('/&ajax[^&]+/i', '', $url);

		$url=str_replace('&', '&^', $url);
	}
	else {
		if (defined('ADMINUI')) $url= httpRoot.'admin.php?mod=ext-objects';
		else $url= httpRoot.'?cs=true';
	}

	if (isset($id))
		return $url."&class=$class&id=$id";
	else
		return $url."&class=$class&name=$name&owner=$ownerclass"."_"."$ownerid";
}

function backurl()
{
	$url= preg_replace('/&[^&\^]+/i', '', $GLOBALS['request']['url']);
	return str_replace('&^', '&', $url);
}

/**
 * ������� �������� � ������� url ��������� �� $add, ��� ��������� �� � ��������
 * ��������� � ������� ����������. (� �������� ����� ������������ ����)
 * �������� ���� � ������� url ���� &desc=true, � � ������� $params ���� 'desc'=>'false',
 * �� &desc=true ��������� �� &desc=false
 * ���� � ������� url ���� &somevar=someval, � � ������� $params ���� 'somevar'=>'',
 * �� &somevar=somval ����� ������� �������� �� url
 * �������������, ���� � $params �������� �������� id, �� �� url ����� �������� ��������� name � owner
 *
 * @param unknown_type $params
 * @return unknown
 */
function selfurl($add=array(), $url= null)
{
	if (!isset($url)) $url= $GLOBALS['request']['url'];

	//�������� ��� ajax ���������
	$url= preg_replace('/&ajax[^&]+/i', '', $url);

	//���������, ��������� ��� �������� ��������� $add �� $url.
	if (!empty($add)) foreach ($add as $name=>$value) {
		$url= preg_replace("/&$name=([^&]+)/i", '', $url);
		if ($value !== '') $url.= "&$name=$value";
		if ($name == 'id') {
			$url= preg_replace("/&name=([^&]+)/i", '', $url);
			$url= preg_replace("/&owner=([^&]+)/i", '', $url);
		}
	}
	return $url;
}

/**
 * ������� �������� ������� $mixedid ���� "$class_$id" �� $class � $id :)
 *
 * @param unknown_type $mixedid
 * @return unknown
 */
function split_mixed_id($mixedid)
{
	$i= strrpos($mixedid, '_');
	if ($i === false) return array(null, null);

	$id= (int)substr($mixedid,$i+1);
	$name= substr($mixedid, 0, $i);
	return array($id, $name);
}


/**
 * ������� ���������� �������� ���������� $name ������ $class
 *
 * @param ����� $class
 * @param ��� $name
 * @return ��������
 */
function describe($class, $name)
{
	$adm=& loadclass($class.'_admin');
	return $adm->describe($name);
}


function rm_($template, $values, $class=null, $replace_basics= true)
{
	if ($replace_basics) {
		$values['httpRoot']= httpRoot;
		$values['styleRoot']= styleRoot;
		$values['dataRoot']= dataRoot;
		$values['dataFiles']= dataFiles;
	}
	if (isset($class)) $values['class']= $class;

	$varnames= array_keys($values);	$varvalues= array_values($values);
	array_walk($varnames, create_function('&$v,$k', '$v="$($v)";'));

	return str_replace($varnames, $varvalues, $template);
}

/**
 * ������� ������ �������� $values � ������� $template
 *
 * @param ������ $template
 * @param ��������_�������� $values
 * @param ���_������ $class
 * @param ��������_�������� $replace_basics
 * @return unknown
 */
function rm($template, $values, $class=null, $replace_basics= true)
{
	if (isset($class)) $values['class']= $class;

	$varnames= array(); $varvalues= array();

	preg_match_all('|\$\(([^\)]+)\)|', $template, $matches, PREG_SET_ORDER);
	foreach ($matches as $match) if (isset($values[$match[1]])) {
		$varnames[$match[1]]= $match[0];
		$varvalues[$match[1]]= $values[$match[1]];
	}

	if ($replace_basics) {
		$varnames[]= '$(httpRoot)'; $varvalues[]= httpRoot;
		$varnames[]= '$(styleRoot)'; $varvalues[]= styleRoot;
		$varnames[]= '$(styleRoot_)'; $varvalues[]= styleRoot_;
		$varnames[]= '$(dataRoot)'; $varvalues[]= dataRoot;
		$varnames[]= '$(dataFiles)'; $varvalues[]= dataFiles;
	}

	return str_replace($varnames, $varvalues, $template);
}

/**
 * ���� $vartype - ���� �� ������� �����, �� ������� ���������� � $vartype ��������������� ���
 * ������ mysql. � $varvalue ����������� ��������������� �������������� (�������� ������������),
 * ����� �������� ����� ���� ��������� � ���� ������ � ���������� true;
 * ���� $vartype �� �������, �� ���������� false;
 *
 * @param ���_�������� $vartype
 * @param ��������_�������� $varvalue
 * @return boolean
 */
function sqlize(&$vartype, &$varvalue)
{
	switch ($vartype) {
		case 'ID': $vartype= 'INT UNSIGNED NOT NULL AUTO_INCREMENT'; $varvalue= (int)$varvalue; break;

		case 'BOOL': case 'BOOLEAN': $vartype= 'TINYINT(1) UNSIGNED DEFAULT 0'; $varvalue= ((int)$varvalue > 0)?1:0; break;

		case 'INT': $vartype= 'INT DEFAULT NULL'; $varvalue= (int)$varvalue; break;
		case 'UNSIGNED INT': case 'INT UNSIGNED': $vartype= 'INT UNSIGNED DEFAULT NULL'; $varvalue= ((int)$varvalue < 0)?0:(int)$varvalue; break;

		case 'TINYINT': $vartype= 'TINYINT DEFAULT NULL'; $varvalue= (int)$varvalue; break;
		case 'UNSIGNED TINYINT': case 'TINYINT UNSIGNED': $vartype= 'TINYINT UNSIGNED DEFAULT NULL'; $varvalue= ((int)$varvalue < 0)?0:(int)$varvalue; break;

		case 'SMALLINT': $vartype= 'SMALLINT DEFAULT NULL'; $varvalue= (int)$varvalue; break;
		case 'UNSIGNED SMALLINT': case 'SMALLINT UNSIGNED': $vartype= 'SMALLINT UNSIGNED DEFAULT NULL'; $varvalue= ((int)$varvalue < 0)?0:(int)$varvalue; break;

		case 'FLOAT': case 'DOUBLE': $vartype= 'DOUBLE DEFAULT NULL'; $varvalue= (float)$varvalue; break;

		case 'PASSWORD': $vartype= 'CHAR(32) DEFAULT NULL'; $varvalue= mysql_real_escape_string(substr($varvalue, 0, 32), $GLOBALS['db']->Connection); break;

		case 'VARCHAR(31)': $vartype= 'VARCHAR(31) DEFAULT NULL'; $varvalue= mysql_real_escape_string(substr($varvalue, 0, 31), $GLOBALS['db']->Connection); break;
		case 'VARCHAR(63)': $vartype= 'VARCHAR(63) DEFAULT NULL'; $varvalue= mysql_real_escape_string(substr($varvalue, 0, 63), $GLOBALS['db']->Connection); break;
		case 'VARCHAR(127)': $vartype= 'VARCHAR(127) DEFAULT NULL'; $varvalue= mysql_real_escape_string(substr($varvalue, 0, 127), $GLOBALS['db']->Connection); break;
		case 'VARCHAR(255)': $vartype= 'VARCHAR(255) DEFAULT NULL'; $varvalue= mysql_real_escape_string(substr($varvalue, 0, 255), $GLOBALS['db']->Connection); break;
		case 'TEXT': case 'HTML': $vartype= 'TEXT'; $varvalue= mysql_real_escape_string($varvalue, $GLOBALS['db']->Connection); break;

		case 'DATE': $vartype= 'DATE DEFAULT NULL'; break;
		case 'DATETIME': $vartype= 'DATETIME DEFAULT NULL'; break;

		case 'ARRAY': $varvalue= mysql_real_escape_string(serialize($varvalue), $GLOBALS['db']->Connection); $vartype= 'TEXT'; break;

		default: return false;
	}
	return true;
}

/**
 * ������� ���������, �������� �� ��� $vartype �������
 *
 * @param ���_�������� $vartype
 * @return bool
 */
function isbasic($vartype)
{
	switch ($vartype) {
		case 'ID':
		case 'BOOL': case 'BOOLEAN':
		case 'INT': case 'UNSIGNED INT': case 'INT UNSIGNED':
		case 'TINYINT': case 'UNSIGNED TINYINT': case 'TINYINT UNSIGNED':
		case 'SMALLINT': case 'UNSIGNED SMALLINT': case 'SMALLINT UNSIGNED':
		case 'FLOAT': case 'DOUBLE':
		case 'PASSWORD': case 'VARCHAR(31)': case 'VARCHAR(63)': case 'VARCHAR(127)': case 'VARCHAR(255)':
		case 'TEXT': case 'HTML':
		case 'DATE': case 'DATETIME':
		case 'ARRAY':
			return true;
	}
	return false;
}

/**
 * ���������� �������� �� ��������� ��� �������� ���� $vartype
 *
 * @param ��� $vartype
 * @return ��������_��_���������
 */
function default_value($vartype)
{
	switch ($vartype) {
		case 'ID':
		case 'BOOL': case 'BOOLEAN':
		case 'INT': case 'UNSIGNED INT': case 'INT UNSIGNED':
		case 'TINYINT': case 'UNSIGNED TINYINT': case 'TINYINT UNSIGNED':
		case 'SMALLINT': case 'UNSIGNED SMALLINT': case 'SMALLINT UNSIGNED':
			return (int)0;

		case 'FLOAT': case 'DOUBLE': return (float)0.0;

		case 'PASSWORD': case 'VARCHAR(31)': case 'VARCHAR(63)': case 'VARCHAR(127)': case 'VARCHAR(255)':
		case 'TEXT': case 'HTML':
			return '';

		case 'DATE': return '0000-00-00';
		case 'DATETIME': return '0000-00-00 00:00:00';
		case 'ARRAY': return array();
	}
}

/**
 * ������� ���������, �������� �� ��� �������� $varname ���������� (�����������)
 *
 * @param ���_�������� $varname
 * @return bool
 */
function isintrinsic($varname)
{
	switch ($varname) {
		case 'id': case 'name': case 'ownerid': case 'ownerclass': case 'position':
		case 'tmp': case 'tmp_created':
			return true;
	}
	return false;
}

/**
 * ������� �������� ����������� �� ���� ������ �������� � ������� ���� $vartype
 *
 * @param ���_�������� $vartype
 * @param ��������_�������� $varvalue
 * @return unknown
 */
function unsqlize($vartype, &$varvalue)
{
	switch ($vartype) {
		case 'ID': case 'BOOL': case 'BOOLEAN':
		case 'INT': case 'UNSIGNED INT': case 'INT UNSIGNED':
		case 'TINYINT': case 'UNSIGNED TINYINT': case 'TINYINT UNSIGNED':
		case 'SMALLINT': case 'UNSIGNED SMALLINT': case 'SMALLINT UNSIGNED':
			if (isset($varvalue)) $varvalue= (int)$varvalue; break;
		case 'FLOAT': case 'DOUBLE': if (isset($varvalue)) $varvalue= (double)$varvalue; break;

		case 'PASSWORD':
		case 'VARCHAR(31)': case 'VARCHAR(63)': case 'VARCHAR(127)': case 'VARCHAR(255)': case 'TEXT': case 'HTML':
			if (get_magic_quotes_runtime()) $varvalue= stripslashes($varvalue); break;

		case 'DATE': case 'DATETIME': break;

		case 'ARRAY': if (isset($varvalue))
			$varvalue= unserialize(get_magic_quotes_runtime()? stripslashes($varvalue): $varvalue); break;

		default: return false;
	}
	return true;
}

function std_ajax_scripts()
{
	$GLOBALS['page']->scripts.= "

// ������� ������������� ��������
var reqs= new Array();
var HttpRequest;

//��������� ������ � �������
function queue_req(url, handler, method, data)
{
	var r= {'url':url, 'handler':handler, 'method':method, 'data':data };
	reqs.push(r);
	next_req();
}

//��������� ��������� ������ �� �������
function next_req()
{
	if ((!HttpRequest || HttpRequest.readyState == 4) && reqs.length) {
		r= reqs.shift();
		make_req(r.url, r.handler, r.method, r.data);
	}
}

//��������� ������������� ajax ������
function make_req(url, handler, method, data)
{
	if (window.XMLHttpRequest) HttpRequest= new XMLHttpRequest();
	else if (window.ActiveXObject) HttpRequest= new ActiveXObject('Microsoft.XMLHTTP');
	else return false;

	HttpRequest.onreadystatechange = handler
	HttpRequest.open(method, url, true);
	HttpRequest.send(data);

	return false;
}

//����������
function ajax_handler()
{

	if (HttpRequest.readyState != 4) return;

	//��������� ����������� ��������
	if (HttpRequest.status == '200') {
		var response;
		//��� ����������
		if (window.ActiveXObject) {
			response= new ActiveXObject('Microsoft.XMLDOM');
			response.loadXML(HttpRequest.responseText);
		}
		else response= HttpRequest.responseXML;

		//������� ��������� �� �������
		var errors= response.getElementsByTagName('error');
		if (errors) for (var i= 0; i < errors.length; i++)
			alert(errors[i].firstChild.nodeValue);

		var action= response.getElementsByTagName('action')[0].firstChild.nodeValue;
		var ajax_id= response.getElementsByTagName('ajax_id')[0].firstChild.nodeValue;
		var html_content= response.getElementsByTagName('html')[0].firstChild.nodeValue;

		if (action == 'redraw') redraw(ajax_id, html_content);
		if (action == 'window') jWindow(html_content);
		if (action == 'toggle') toggle(ajax_id, html_content);
	}

	//��������� �� ��������� ��������� ������
	next_req();
}

// ��������� ����������� ������
function reqb(url, method, data)
{
	if (window.XMLHttpRequest) HttpRequest = new XMLHttpRequest();
	else if (window.ActiveXObject) HttpRequest = new ActiveXObject('Microsoft.XMLHTTP');
	else return false;

	HttpRequest.open(method, url, false);
	HttpRequest.send(data);

	if (HttpRequest.status == 200) return HttpRequest.responseText;
	else return false;
}


//--------- ��������� ��������
function queue_redraw(ajax_id)
{
	var url= window.location;
	url= url+'&ajax_id='+ajax_id+'&ajax=true';
	queue_req(url, ajax_handler, 'GET', null);
}

function redraw(ajax_id, html_content)
{

	var oArea= document.getElementById(ajax_id);
	if (!oArea) return;

	oArea.innerHTML= html_content;
}

function toggle(ajax_id, val)
{
	var oImg= document.getElementById(ajax_id)
	if (!oImg) return false;

	if (val) oImg.src= '".styleRoot_."controls/on.gif';
	else oImg.src= '".styleRoot_."controls/off.gif';

	return false;
}

//---------������
function jWindow(html_content)
{
	var oWnd= document.createElement('div');
	oWnd.innerHTML= html_content;
	oWnd.className= 'jWindow';
	document.body.appendChild(oWnd);
}

";
}

function XMLAjaxResponse($action='nothing', $ajax_id=0, $html_data='')
{
	header('Content-Type: text/xml; charset=windows-1251');

	$errors= '';
	if (!empty($_SESSION['session']['msg']['errors'])) foreach ($_SESSION['session']['msg']['errors'] as $error)
		$errors.= '<error><![CDATA['.$error.']]></error>';
	$_SESSION['session']['msg']['errors']= array();

	die("
		<reply>
			<action>$action</action>
			<ajax_id>$ajax_id</ajax_id>
			<html><![CDATA[$html_data]]></html>
			$errors
		</reply>
	");
}
#--------------------------------------------------------------------------------------------------------------------------------------------------------------#

function ftime()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float)$usec + (float)$sec);
}

?>