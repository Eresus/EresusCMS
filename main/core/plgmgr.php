<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 *
 * @package EresusCMS
 *
 * $Id$
 */

/**
 *
 * @package EresusCMS
 */
class TPlgMgr
{
	/**
	 * ������� ������� � ������
	 * @var int
	 */
	private $access = ADMIN;

	/**
	 * �������� ��� ��������� ������
	 *
	 * @return void
	 */
	private function toggle()
	{
		global $page, $Eresus;

		$q = DB::getHandler()->createUpdateQuery();
		$e = $q->expr;
		$q->update('plugins')
			->set('active', $e->not('active'))
			->where(
				$e->eq('name', $q->bindValue(arg('toggle')))
			);
		DB::execute($q);

		HttpResponse::redirect($page->url());
	}
	//-----------------------------------------------------------------------------

	private function delete()
	{
	global $page, $Eresus;

		$Eresus->plugins->load($Eresus->request['arg']['delete']);
		$Eresus->plugins->uninstall($Eresus->request['arg']['delete']);
		HTTP::redirect($page->url());
	}

	private function edit()
	{
	global $page, $Eresus;

		$Eresus->plugins->load($Eresus->request['arg']['id']);
		if (method_exists($Eresus->plugins->items[$Eresus->request['arg']['id']], 'settings')) {
			$result = $Eresus->plugins->items[arg('id', 'word')]->settings();
		} else {
			$form = array(
				'name' => 'InfoWindow',
				'caption' => $page->title,
				'width' => '300px',
				'fields' => array (
					array('type'=>'text','value'=>'<div align="center"><strong>���� ������ �� ����� ��������</strong></div>'),
				),
				'buttons' => array('cancel'),
			);
			$result = $page->renderForm($form);
		}
		return $result;
	}

	private function update()
	{
	global $page, $Eresus;

		$Eresus->plugins->load($Eresus->request['arg']['update']);
		$Eresus->plugins->items[$Eresus->request['arg']['update']]->updateSettings();
		HTTP::redirect($Eresus->request['arg']['submitURL']);
	}

	/**
	 * ���������� �������
	 *
	 * @return void
	 * @see add()
	 */
	private function insert()
	{
		global $page, $Eresus;

		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$files = arg('files');
		if ($files && is_array($files))
		{
			foreach ($files as $plugin => $install)
			{
				if ($install)
				{
					try
					{
						$Eresus->plugins->install($plugin);
					}
					catch (DomainException $e)
					{
						ErrorMessage($e->getMessage());
					}
				}
			}

		}
		HttpResponse::redirect('admin.php?mod=plgmgr');
	}
	//-----------------------------------------------------------------------------

	/**
	 * ���������� ������ ���������� �������
	 *
	 * @return string  HTML
	 */
	private function add()
	{
		global $page, $Eresus;

		$data = array();

		/* ���������� ������ ��������� �������� */
		$files = glob($Eresus->froot . 'ext/*.php');
		if (false === $files)
		{
			$files = array();
		}

		/* ���������� ������ ��������� � ������������� �������� */
		$items = $Eresus->db->select('plugins', '', 'name, version');
		$available = array();
		$installed = array();
		foreach ($items as $item)
		{
			$available[$item['name']] = $item['version'];
			$installed []= $Eresus->froot . 'ext/' . $item['name'] . '.php';
		}

		// ��������� ������ ���������������
		$files = array_diff($files, $installed);

		/*
		 * �������� ���������� � ��������������� ��������
		 */
		$data['plugins'] = array();
		$features = array();
		if (count($files))
		{
			foreach ($files as $file)
			{
				$errors = array();
				try
				{
					$info = Eresus_PluginInfo::loadFromFile($file);
					// ������� �� ������ ���� ��� �����, ����� ���������� ������ �����
					$kernelVersion = preg_replace('/[^\d\.]/', '', CMSVERSION);
					$required = $info->getRequiredKernel();
					if (
						version_compare($kernelVersion, $required[0], '<')/* ||
						version_compare($kernelVersion, $required[1], '>')*/
					)
					{
						$msg =  I18n::getInstance()->getText('admPluginsInvalidVersion', $this);
						$errors []= sprintf($msg, /*implode(' - ', */$required[0]/*)*/);
					}
					/*}
					else
					{
						$msg =  I18n::getInstance()->getText('Class "%s" not found in plugin file', $this);
						$info['errors'] []= sprintf($msg, $info['name']);
					}*/
				}
				catch (RuntimeException $e)
				{
					$errors []= $e->getMessage();
					$info = new stdClass();
					$info->title = $info->name = basename($file, '.php');
					$info->version = '';
				}
				$available[$info->name] = $info->version;
				$data['plugins'][$info->title] = array('info' => $info, 'errors' => $errors);
			}
		}


		foreach ($data['plugins'] as &$item)
		{
			if ($item['info'] instanceof Eresus_PluginInfo)
			{
				$required = $item['info']->getRequiredPlugins();
				foreach ($required as $plugin)
				{
					list ($name, $minVer, $maxVer) = $plugin;
					if (
						!isset($available[$name]) ||
						($minVer && version_compare($available[$name], $minVer, '<')) ||
						($maxVer && version_compare($available[$name], $maxVer, '>'))
					)
					{
						{
							$msg = I18n::getInstance()->getText('Requires plugin: %s', $this);
							$item['errors'] []= sprintf($msg, $name . ' ' . $minVer . '-' . $maxVer);
						}
					}
				}
			}
		}

		ksort($data['plugins']);

		$tmpl = $page->getUITheme()->getTemplate('PluginManager/add-dialog.html');
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ��������� �������� ������
	 *
	 * @return string
	 */
	public function adminRender()
	{
		global $page, $Eresus;

		if (!UserRights($this->access))
		{
			eresus_log(__METHOD__, LOG_WARNING, 'Access denied for user "%s"', $Eresus->user['name']);
			return '';
		}

		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$result = '';
		$page->title = admPlugins;

		switch (true)
		{
			case arg('update') !== null:
				$this->update();
			break;

			case arg('toggle') !== null:
				$this->toggle();
			break;

			case arg('delete') !== null:
				$this->delete();
			break;

			case arg('id') !== null:
				$result = $this->edit();
			break;

			case arg('action') == 'add':
				$result = $this->add();
			break;

			case arg('action') == 'insert':
				$this->insert();
			break;

			default:
				$table = array (
					'name' => 'plugins',
					'key' => 'name',
					'sortMode' => 'title',
					'columns' => array(
						array('name' => 'title', 'caption' => admPlugin, 'width' => '90px', 'wrap'=>false),
						array('name' => 'description', 'caption' => admDescription),
						array('name' => 'version', 'caption' => admVersion, 'width'=>'70px','align'=>'center'),
					),
					'controls' => array (
						'delete' => '',
						'edit' => '',
						'toggle' => '',
					),
					'tabs' => array(
						'width'=>'180px',
						'items'=>array(
							array('caption'=>admPluginsAdd, 'name'=>'action', 'value'=>'add')
						)
					)
				);
				$result = $page->renderTable($table);
			break;
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}
