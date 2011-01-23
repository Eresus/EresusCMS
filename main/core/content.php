<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * ���������� ���������
 *
 * @package EresusCMS
 */
class TContent
{
	/**
	 * �������� �������
	 * @var array
	 */
	private $item;

	/**
	 * ���������� �������� ���������� ���������� ��������� �������� �������
	 *
	 * @return string  HTML
	 * @uses EresusAdminFrontController::setController()
	 */
	public function adminRender()
	{
		if (!UserRights(EDITOR))
		{
			return '';
		}

		$plugins = $GLOBALS['Eresus']->plugins;
		$plugin = null;

		useLib('sections');
		$sections = new Sections();
		$this->item = $sections->get(arg('section', 'int'));

		$GLOBALS['page']->id = $this->item['id'];

		if (!array_key_exists($this->item['type'], $plugins->list))
		{
			switch ($this->item['type'])
			{
				case 'default':
					$html = $this->contentTypeDefault();
				break;

				case 'list':
					$html = $this->contentTypeList();
				break;

				case 'url':
					$html = $this->contentTypeURL();
				break;

				default:
					$html = $GLOBALS['page']->box(sprintf(errContentPluginNotFound, $this->item['type']),
						'errorBox', errError);
				break;
			}
		}
		else
		{
			$plugins->load($this->item['type']);
			$plugin = $plugins->items[$this->item['type']];
			EresusCMS::app()->getFrontController()->setController($plugin);
			$html = $plugin->adminRenderContent();
		}

		$tmpl = new Template('core/templates/ContentEditor/common.html');
		$data = array(
			'editor' => $html,
			'contentURL' => '#',
			'propertiesURL' => 'admin.php?mod=pages&id=' . arg('section', 'int'),
			'plugin' => $plugin,
			'clientURL' => $GLOBALS['page']->clientURL(arg('section', 'int'))
		);
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ��� �������� ���� "�� ���������"
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	private function contentTypeDefault()
	{
		$editor = new ContentPlugin();
		if (arg('update'))
		{
			$editor->update();
		}
		else
		{
			return $editor->adminRenderContent();
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ��� �������� ���� "������ �����������"
	 *
	 * @return string
	 *
	 * @since 2.16
	 * @uses HttpResponse::redirect()
	 */
	private function contentTypeList()
	{
		if (arg('update'))
		{
			$original = $this->item['content'];
			$item['content'] = arg('content', 'dbsafe');
			$GLOBALS['Eresus']->db->updateItem('pages', $this->item, "`id`='".$this->item['id']."'");
			HttpResponse::redirect(arg('submitURL'));
		}
		else
		{
			$form = array(
				'name' => 'editURL',
				'caption' => admEdit,
				'width' => '100%',
				'fields' => array (
					array('type'=>'hidden','name'=>'update', 'value'=>$item['id']),
					array ('type' => 'html', 'name' => 'content', 'label' => admTemplListLabel,
						'height' => '300px', 'value'=>isset($item['content'])?$item['content']:'$(items)'),
				),
				'buttons' => array('apply', 'cancel'),
			);
			return $GLOBALS['page']->renderForm($form);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * �������� ��� �������� ���� "URL"
	 *
	 * @return string
	 *
	 * @since 2.16
	 * @uses HttpResponse::redirect()
	 */
	private function contentTypeURL()
	{
		if (arg('update'))
		{
			$original = $this->item['content'];
			$this->item['content'] = arg('url', 'dbsafe');
			$GLOBALS['Eresus']->db->updateItem('pages', $this->item, "`id`='".$this->item['id']."'");
			HttpResponse::redirect(arg('submitURL'));
		}
		else
		{
			$form = array(
				'name' => 'editURL',
				'caption' => admEdit,
				'width' => '100%',
				'fields' => array (
					array('type'=>'hidden','name'=>'update', 'value' => $this->item['id']),
					array ('type' => 'edit', 'name' => 'url', 'label' => 'URL:', 'width' => '100%',
						'value'=>isset($this->item['content']) ? $this->item['content']:''),
				),
				'buttons' => array('apply', 'cancel'),
			);
			return $GLOBALS['page']->renderForm($form);
		}
	}
	//-----------------------------------------------------------------------------
}