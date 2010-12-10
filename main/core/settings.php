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
 * @package EresusCMS
 */
class TSettings
{
	/**
	 * ����������� ��������� ������� �������
	 * @var int
	 */
	private $access = ADMIN;

	/**
	 * ������ ������ ��������� ��� ������ � ����
	 * @param string $name     ��� ���������
	 * @param string $type
	 * @param array  $options
	 * @return string
	 * @uses HTTP::request
	 */
	private function mkstr($name, $type = 'string', $options = array())
	{
		global $Eresus;

		$req = HTTP::request();

		$result = "  define('$name', ";
		$quot = "'";
		$value = isset($_POST[$name]) ? $_POST[$name] : null;

		if (isset($options['nobr']) && $options['nobr'])
		{
			$value = str_replace(array("\n", "\r"), ' ', $value);
		}

		if (isset($options['savebr']) && $options['savebr'])
		{
			$value = addcslashes($value, "\n\r\"");
			$quot = '"';
		}

		switch ($type)
		{
			case 'string':
				$value = str_replace(
					array('\\', $quot),
					array('\\\\', '\\' . $quot),
					$value
				);
				$value = $quot . $value . $quot;
			break;
			case 'bool':
				$value = $value ? 'true' : 'false';
			break;
			case 'int':
					$value = intval($value);
			break;
		}
		$result .= $value . ");\n";
		return $result;
	}
	//--------------------------------------------------------------------

	/**
	 * ���������� ���������
	 * @return void
	 * @uses HTTP::goback
	 */
	private function update()
	{
		EresusLogger::log(__METHOD__, LOG_DEBUG, '()');

		$settings = "<?php\n";

		$settings .= $this->mkstr('siteName', 'string');
		$settings .= $this->mkstr('siteTitle', 'string');
		$settings .= $this->mkstr('siteTitleReverse', 'bool');
		$settings .= $this->mkstr('siteTitleDivider', 'string');
		$settings .= $this->mkstr('siteKeywords', 'string', array('nobr'=>true));
		$settings .= $this->mkstr('siteDescription', 'string', array('nobr'=>true));
		$settings .= $this->mkstr('mailFromAddr', 'string');
		$settings .= $this->mkstr('mailFromName', 'string');
		$settings .= $this->mkstr('mailFromOrg', 'string');
		$settings .= $this->mkstr('mailReplyTo', 'string');
		$settings .= $this->mkstr('mailFromSign', 'string', array('savebr'=>true));
		$settings .= $this->mkstr('filesModeSetOnUpload', 'bool');
		$settings .= $this->mkstr('filesModeDefault', 'string');
		$settings .= $this->mkstr('filesTranslitNames', 'bool');
		$settings .= $this->mkstr('contentTypeDefault', 'string');
		$settings .= $this->mkstr('pageTemplateDefault', 'string');

		file_put_contents(filesRoot.'cfg/settings.php', $settings);
		HTTP::goback();
	}
	//--------------------------------------------------------------------

	/**
	 * ���������� HTML-��� �������
	 * @return string  HTML
	 * @uses EresusForm
	 * @uses Templates
	 */
	private function renderForm()
	{
		global $Eresus, $page;

		$form = new EresusForm('core/templates/SiteSettings/form.html', LOCALE_CHARSET);
		/* �������� */
		$form->setValue('siteName', option('siteName'));
		$form->setValue('siteTitle', option('siteTitle'));
		$form->setValue('siteTitleReverse', option('siteTitleReverse'));
		$form->setValue('siteTitleDivider', option('siteTitleDivider'));
		$form->setValue('siteKeywords', option('siteKeywords'));
		$form->setValue('siteDescription', option('siteDescription'));
		/* ����� */
		$form->setValue('mailFromAddr', option('mailFromAddr'));
		$form->setValue('mailFromName', option('mailFromName'));
		$form->setValue('mailFromOrg', option('mailFromOrg'));
		$form->setValue('mailReplyTo', option('mailReplyTo'));
		$form->setValue('mailFromSign', option('mailFromSign'));
		/* ����� */
		$form->setValue('filesModeSetOnUpload', option('filesModeSetOnUpload'));
		$form->setValue('filesModeDefault', option('filesModeDefault'));
		$form->setValue('filesTranslitNames', option('filesTranslitNames'));

		/* ������� ������ ����� �������� */
		$contentTypes = array(
			array('name' => 'default','caption' => admPagesContentDefault),
			array('name' => 'list','caption' => admPagesContentList),
			array('name' => 'url','caption' => admPagesContentURL)
		);

		foreach ($Eresus->plugins->items as $plugin)
		{
			if ($plugin instanceof ContentPlugin || $plugin instanceof TContentPlugin)
			{
				$contentTypes []= array('name' => $plugin->name, 'caption' => $plugin->title);
			}
		}

		$form->setValue('contentTypes', $contentTypes);
		$form->setValue('contentTypeDefault', option('contentTypeDefault'));

		/* ��������� ������ �������� */
		useLib('templates');
		$templates = new Templates();
		$list = $templates->enum();
		$templates = array();
		foreach ($list as $key => $value)
			$templates []= array('name' => $key, 'caption' => $value);

		$form->setValue('templates', $templates);
		$form->setValue('pageTemplateDefault', option('pageTemplateDefault'));

		$html = $form->compile();
		return $html;
	}
	//--------------------------------------------------------------------

	/**
	 * ��������� ��������
	 * @return string
	 * @uses HTTP::request
	 */
	function adminRender()
	{
		EresusLogger::log(__METHOD__, LOG_DEBUG, '()');
		if (!UserRights($this->access))
			return;

		if (HTTP::request()->getMethod() == 'POST')
			$this->update();

		$html = $this->renderForm();
		return $html;
	}
	#--------------------------------------------------------------------------------------------------------------------------------------------------------------#
}
