<?
//DEBUG MODE
ini_set('display_errors', 1);

require_once('kernel.php');

require_once('classes.inc');
if (defined('ADMINUI')) require_once('admtemplates.inc');

require_once(classesPath.$GLOBALS['classes']['tclass']);
if (defined('ADMINUI')) require_once(classesPath.$GLOBALS['classes']['tclass_admin']);

function isclass($class) {
	if (isset($GLOBALS['classes'][$class])) return true;
	return false;
}

function &loadclass($class, $admneeded= false)
{
	$class= strtolower($class);
	if (!isset($GLOBALS['classes'][$class])) FatalError("����� ������� $class �� ������ � classes.inc!");

	if (is_object($GLOBALS['classes'][$class])) return $GLOBALS['classes'][$class];

	if ($admneeded) require_once(classesPath.$GLOBALS['classes']['tclass_admin']);
	require_once(classesPath.$GLOBALS['classes'][$class]);
	$GLOBALS['classes'][$class]=& new $class();

	return $GLOBALS['classes'][$class];
}

#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
class TObjects {
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	# ���������� �������
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function TObjects()
	{
		//���������� ���� ������
		if (defined('ADMINUI')) $GLOBALS['page']->headlinks.= '<link rel="StyleSheet" href="'.httpRoot.'/ext/objects/style/admin.css" type="text/css" />';
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	//������� ������� � ���� ������ ��� ���� �������, ����������� � classes.inc
	/* TODO: ������� ����� install ������������� ������ ������, ������ ������� �������, � �� ��� �� classes.inc! */
	function install() {
		foreach ($GLOBALS['classes'] as $class=>$path) if (strpos($class, '_admin') !== false && $class != 'tclass_admin') {
			$adm=& loadclass($class);
			$adm->install();
		}
	}

	function uninstall() {}

	function createPluginItem($item = null)
	{
  		$result['name'] = $this->name;
  		$result['type'] = $this->type;
  		$result['active'] = true;
  		$result['position'] = is_null($item)?$GLOBALS['db']->count('plugins'):$item['position'];
  		$result['settings'] = is_null($item)?'':$item['settings'];
  		$result['title'] = $this->title;
  		$result['version'] = $this->version;
  		$result['description'] = $this->description;
  		return $result;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	# ���������������� �������
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function adminRender()
	{
		if (!isset($_GET['class']) || !isclass($_GET['class'])) return;

		$class= $_GET['class'];
		$class_admin= $class.'_admin';
		$ctl=& loadclass($class) or FatalError("����� ������� $class �� ������!");
		$adm=& loadclass($class_admin) or FatalError("����� ������� $class_admin �� ������!");

		//REMOVE IT
		//$adm->createsqltable();

		if (isset($_GET['id'])) {
			$values= $ctl->restore((int)$_GET['id']);
			if (empty($values)) {
				ErrorMessage("� ������� �������� ������ $class �� ������ ������ � ��������������� {$_GET['id']}");
				if (isset($_GET['ajax'])) XMLAjaxResponse();
				return;
			}
		}
		else {
			list($ownerid, $ownerclass)= split_mixed_id(isset($_GET['owner'])?$_GET['owner']:'root_1');
			$values= array('name'=>(isset($_GET['name'])?$_GET['name']:null), 'ownerid'=>$ownerid, 'ownerclass'=>$ownerclass);
			//���� ����� �������� �������� ������, ������� ��������� ������
			if (!$ctl->simple()) {
				$newvalues= $adm->defaults();
				$values['id']= $adm->create($values, $newvalues, null, 1);

				//���� ������� ��������� ������ �� ������� �� �����-�� ��������, ������������
				if ($values['id'] === false) $adm->goback($values);

				$adm->goself($values);
			}
		}

		if (isset ($_GET['id']) && isset($_GET['action__'])) switch ($_GET['action__']) {
			case 'up':
				$adm->up($values);
				if (isset($_GET['ajax'])) XMLAjaxResponse();
				$adm->goback($values);
				break;
			case 'down':
				$adm->down($values);
				if (isset($_GET['ajax'])) XMLAjaxResponse();
				$adm->goback($values);
				break;
			case 'delete':
				$adm->delete($values);
				if (isset($_GET['ajax'])) XMLAjaxResponse();
				$adm->goback($values);
				break;
		}
		if (isset ($_GET['toggle'])) {
			$adm->toggle($values, $_GET['toggle']);
			if (isset($_GET['ajax']))
				XMLAjaxResponse('toggle', $_GET['toggle'].'_'.$class.'_'.$values['id'], $values[$_GET['toggle']]);
			$adm->goback($values);
		}

		if (isset($_POST['action__'])) switch ($_POST['action__']) {
			case 'OK':
				if (isset($values['id']) && !$values['tmp']) $adm->update($values, $GLOBALS['request']['arg']);
				else $values['id']= $adm->create($values, $GLOBALS['request']['arg']);

				$adm->goback($values);
				break;
			case 'Apply':
				if (isset($values['id']) && !$values['tmp']) $adm->update($values, $GLOBALS['request']['arg']);
				else $values['id']= $adm->create($values, $GLOBALS['request']['arg']);

				if ($values['id'] === false) unset($values['id']);

				$adm->goself($values);
				break;
		}

		std_ajax_scripts();
		$result= $adm->render($values, (isset($_GET['action__'])?$_GET['action__']:null));

		return $result;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
	function adminRenderContent()
	{
		$ctl=& loadclass($this->name);
		$values= $ctl->restore(null, null, (int)$_GET['section'], 'root', null, 'id');
		if (empty($values))
			goto(httpRoot.'admin.php?mod=ext-'.$this->name.'&class='.$this->name.'&name='.$this->name.'&owner=root_'.$_GET['section']);
		else
			goto(httpRoot.'admin.php?mod=ext-'.$this->name.'&class='.$this->name.'&id='.$values['id']);
	}
}
#-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-#
?>