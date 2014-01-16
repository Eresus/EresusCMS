<?php
/**
 * ${product.title}
 *
 * Управление шаблонами и стилями
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * Данная программа является свободным программным обеспечением. Вы
 * вправе распространять ее и/или модифицировать в соответствии с
 * условиями версии 3 либо (по вашему выбору) с условиями более поздней
 * версии Стандартной Общественной Лицензии GNU, опубликованной Free
 * Software Foundation.
 *
 * Мы распространяем эту программу в надежде на то, что она будет вам
 * полезной, однако НЕ ПРЕДОСТАВЛЯЕМ НА НЕЕ НИКАКИХ ГАРАНТИЙ, в том
 * числе ГАРАНТИИ ТОВАРНОГО СОСТОЯНИЯ ПРИ ПРОДАЖЕ и ПРИГОДНОСТИ ДЛЯ
 * ИСПОЛЬЗОВАНИЯ В КОНКРЕТНЫХ ЦЕЛЯХ. Для получения более подробной
 * информации ознакомьтесь со Стандартной Общественной Лицензией GNU.
 *
 * Вы должны были получить копию Стандартной Общественной Лицензии
 * GNU с этой программой. Если Вы ее не получили, смотрите документ на
 * <http://www.gnu.org/licenses/>
 *
 * @package Eresus
 */

// TODO: Проверить, нет ли доступа к внешним директориям

/**
 * Управление темами оформления
 *
 * @package Eresus
 */
class TThemes
{
	/**
	 * Минимальный требуемый уровень доступа
	 * @var int
	 */
	public $access = ADMIN;

	/**
	 * Псевдо-вкладки
	 * @var array
	 */
	public $tabs = array(
		'width' => ADM_THEMES_TAB_WIDTH,
		'items' => array(
			array('caption' => ADM_THEMES_TEMPLATES),
			array('caption' => ADM_THEMES_STANDARD),
			array('caption' => ADM_THEMES_STYLES),
		),
	);

	/**
	 * ???
	 * @var array
	 */
	public $stdTemplates = array(
		'SectionListItem' => array('caption' => admTemplList, 'hint' => admTemplListItemLabel),
		'PageSelector' => array('caption' => admTemplPageSelector, 'hint' => admTemplPageSelectorLabel),
		'pagination' => array('caption' => 'Новый переключатель страниц',
			'hint' => '<a href="http://wiki.dwoo.org/">Синтаксис</a>.
			Переменная $pagination содержит массив страниц. У каждой страницы есть свойства: title &mdash;
			номер страницы; url &mdash; адрес страницы; current &mdash; true, это это текущая страница.'),
		'400' => array('caption' => 'HTTP 400 - Bad Request'),
		'401' => array('caption' => 'HTTP 401 - Unauthorized'),
		'402' => array('caption' => 'HTTP 402 - Payment Required'),
		'403' => array('caption' => 'HTTP 403 - Forbidden'),
		'404' => array('caption' => 'HTTP 404 - Not Found'),
		'405' => array('caption' => 'HTTP 405 - Method Not Allowed'),
		'406' => array('caption' => 'HTTP 406 - Not Acceptable'),
		'407' => array('caption' => 'HTTP 407 - Proxy Authentication Required'),
		'408' => array('caption' => 'HTTP 408 - Request Timeout'),
		'409' => array('caption' => 'HTTP 409 - Conflict'),
		'410' => array('caption' => 'HTTP 410 - Gone'),
		'411' => array('caption' => 'HTTP 411 - Length Required'),
		'412' => array('caption' => 'HTTP 412 - Precondition Failed'),
		'413' => array('caption' => 'HTTP 413 - Request Entity Too Large'),
		'414' => array('caption' => 'HTTP 414 - Request-URI Too Long'),
		'415' => array('caption' => 'HTTP 415 - Unsupported Media Type'),
		'416' => array('caption' => 'HTTP 416 - Requested Range Not Satisfiable'),
		'417' => array('caption' => 'HTTP 417 - Expectation Failed'),
	);

	/**
	 * ???
	 * @return void
	 */
	public function sectionTemplatesInsert()
	{
		$filename = arg('name');
		$filter = new Eresus_FS_NameFilter();
		$filename = $filter->filter($filename);

		if ('' === $filename)
		{
			$filename = uniqid();
		}

		if ($filename != arg('name'))
		{
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(ADM_THEMES_FILENAME_FILTERED, $filename));
		}

		$templates = Templates::getInstance();
		$templates->add($filename, '', arg('code'), arg('desc'));
		HTTP::redirect(arg('submitURL'));
	}

	/**
	 * ???
	 * @return void
	 */
	public function sectionTemplatesUpdate()
	{
		$templates = Templates::getInstance();
		$templates->update(arg('name'), '', arg('code'), arg('desc'));
		HTTP::redirect(arg('submitURL'));
	}

	/**
	 * ???
	 * @return void
	 */
	public function sectionTemplatesDelete()
	{
		$templates = Templates::getInstance();
		$templates->delete(arg('delete'));
		HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
	}

	/**
	 * ???
	 * @return string  HTML
	 */
	public function sectionTemplatesAdd()
	{
		$form = array(
			'name' => 'addForm',
			'caption' => Eresus_Kernel::app()->getPage()->title.ADM_T_DIV.ADM_ADD,
			'width' => '100%',
			'fields' => array (
				array('type'=>'hidden','name'=>'action', 'value'=>'insert'),
				array('type'=>'hidden','name'=>'section', 'value'=>arg('section')),
				array('type'=>'edit','name'=>'name','label'=>ADM_THEMES_FILENAME_LABEL, 'width'=>'200px',
					'comment'=>'.html'),
				array('type'=>'edit','name'=>'desc','label'=>ADM_THEMES_DESC_LABEL, 'width'=>'100%'),
				array('type'=>'memo','name'=>'code', 'height'=>'30', 'syntax' => 'html'),
			),
			'buttons' => array('ok','cancel'),
		);
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string  HTML
	 */
	public function sectionTemplatesEdit()
	{
		$templates = Templates::getInstance();
		$item = $templates->get(arg('id'), '', true);
		$form = array(
			'name' => 'editForm',
			'caption' => Eresus_Kernel::app()->getPage()->title.ADM_T_DIV.ADM_EDIT,
			'width' => '100%',
			'fields' => array (
				array('type'=>'hidden','name'=>'action', 'value'=>'update'),
				array('type'=>'hidden','name'=>'section', 'value'=>arg('section')),
				array('type'=>'hidden','name'=>'name'),
				array('type' => 'edit', 'name' => 'filename', 'label' => ADM_THEMES_FILENAME_LABEL,
					'width' => '200px', 'comment' => '.html', 'disabled' => true, 'value' => $item['name']),
				array('type'=>'edit','name'=>'desc','label'=>ADM_THEMES_DESC_LABEL, 'width'=>'100%'),
				array('type'=>'memo','name'=>'code', 'height'=>'30', 'syntax' => 'html'),
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form, $item);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string  HTML
	 */
	public function sectionTemplatesList()
	{
		$table = array(
			'name' => 'templates',
			'key'=> 'filename',
			'sortMode' => 'filename',
			'sortDesc' => false,
			'columns' => array(
				array('name' => 'description', 'caption' => 'Описание'),
				array('name' => 'filename', 'caption' => 'Имя файла'),
			),
			'controls' => array (
				'delete' => '',
				'edit' => '',
			),
			'tabs' => array(
				'width'=>'120px',
				'items'=>array(
					array('caption'=>ADM_ADD, 'name'=>'action', 'value'=>'add'),
				)
			),
		);
		$templates = Templates::getInstance();
		$list = $templates->enum();
		$items = array();
		foreach ($list as $key=>$value)
		{
			$items[] = array('filename' => $key, 'description' => $value);
		}
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderTable($table, $items);
		return $result;
	}

	/**
	 * ???
	 * @return string
	 */
	public function sectionTemplates()
	{
		Eresus_Kernel::app()->getPage()->title .= ADM_T_DIV.ADM_THEMES_TEMPLATES;

		$result = '';
		switch (arg('action'))
		{
			case 'update':
				$this->sectionTemplatesUpdate();
				break;
			case 'insert':
				$this->sectionTemplatesInsert();
				break;
			case 'add':
				$result = $this->sectionTemplatesAdd();
				break;
			default:
				if (arg('delete'))
				{
					$this->sectionTemplatesDelete();
				}
				elseif (arg('id'))
				{
					$result = $this->sectionTemplatesEdit();
				}
				else
				{
					$result = $this->sectionTemplatesList();
				}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return void
	 */
	public function sectionStdInsert()
	{
		$templates = Templates::getInstance();
		$templates->add(arg('name'), 'std', arg('code'), $this->stdTemplates[arg('name')]['caption']);
		HTTP::redirect(arg('submitURL'));
	}

	/**
	 * ???
	 * @return void
	 */
	public function sectionStdUpdate()
	{
		$this->sectionStdInsert();
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return void
	 */
	public function sectionStdDelete()
	{
		$templates = Templates::getInstance();
		$templates->delete(arg('delete'), 'std');
		HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
	}

	/**
	 * Диалог добавления стандартного шаблона
	 *
	 * @return string
	 */
	private function sectionStdAdd()
	{
		/*
		 * Создаём список имеющихся шаблонов чтобы отфильтровать их из списка доступных.
		 */
		$templates = Templates::getInstance();
		$list = array_keys($templates->enum('std'));
		$existed = array();
		foreach ($list as $key)
		{
			$existed []= $key;
		}


		$values = array();
		$items = array();
		$jsArray = "var aTemplates = Array();\n";
		foreach ($this->stdTemplates as $key => $item)
		{
			if (in_array($key, $existed))
			{
				continue;
			}
			if (!isset($hint))
			{
				$hint = isset($item['hint']) ? $item['hint'] : '';
			}
			$values[] = $key;
			$items[] = $item['caption'];
			$jsArray .= "aTemplates['".$key."'] = '".(isset($item['hint'])?$item['hint']:'')."'\n";
		}
		if (!isset($hint))
		{
			$hint = '';
		}

		Eresus_Kernel::app()->getPage()->addScripts($jsArray."
			function onTemplateNameChange()
			{
				document.getElementById('templateHint').innerHTML =
					aTemplates[document.addForm.elements.namedItem('name').value];
			}
		");
		$form = array(
			'name' => 'addForm',
			'caption' => Eresus_Kernel::app()->getPage()->title.ADM_T_DIV.ADM_ADD,
			'width' => '100%',
			'fields' => array (
				array('type'=>'hidden','name'=>'action', 'value'=>'insert'),
				array('type'=>'hidden','name'=>'section', 'value'=>arg('section')),
				array('type'=>'select','name'=>'name','label'=>ADM_THEMES_TEMPLATE, 'values'=>$values,
					'items'=>$items, 'extra' => 'onChange="onTemplateNameChange()"'),
				array('type'=>'text','name'=>'hint', 'value' => $hint, 'extra' => 'id="templateHint"'),
				array('type'=>'memo','name'=>'code', 'height'=>'30', 'syntax' => 'html'),
			),
			'buttons' => array('ok','cancel'),
		);
		/* @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string
	 */
	public function sectionStdEdit()
	{
		$templates = Templates::getInstance();
		$item = $templates->get(arg('id'), 'std', true);
		$form = array(
			'name' => 'editForm',
			'caption' => Eresus_Kernel::app()->getPage()->title . ADM_T_DIV . ADM_EDIT,
			'width' => '100%',
			'fields' => array (
				array('type'=>'hidden','name'=>'action', 'value'=>'update'),
				array('type'=>'hidden','name'=>'section', 'value'=>arg('section')),
				array('type'=>'hidden','name'=>'name'),
				array('type'=>'edit','name'=>'_name','label' => ADM_THEMES_FILENAME_LABEL,
					'width' => '200px', 'comment' => '.tmpl (' .
					$this->stdTemplates[$item['name']]['caption'].')',
					'disabled' => true, 'value'=>$item['name']),
				array('type'=>'text','name'=>'hint', 'value' =>
					isset($this->stdTemplates[$item['name']]['hint']) ?
						$this->stdTemplates[$item['name']]['hint']:'', 'extra' => 'id="templateHint"'),
				array('type'=>'memo','name'=>'code', 'height'=>'30', 'syntax' => 'html'),
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form, $item);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string  HTML
	 */
	public function sectionStdList()
	{
		$table = array(
			'name' => 'templates',
			'key'=> 'filename',
			'sortMode' => 'filename',
			'sortDesc' => false,
			'columns' => array(
				array('name' => 'description', 'caption' => 'Описание'),
				#array('name' => 'filename', 'caption' => 'Имя файла'),
			),
			'controls' => array (
				'delete' => '',
				'edit' => '',
			),
			'tabs' => array(
				'width'=>'120px',
				'items'=>array(
					array('caption'=>ADM_ADD, 'name'=>'action', 'value'=>'add'),
				)
			),
		);
		$templates = Templates::getInstance();
		$list = $templates->enum('std');
		$items = array();
		foreach ($list as $key=>$value)
		{
			$items[] = array('filename' => $key, 'description' => $value);
		}
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderTable($table, $items);
		return $result;
	}

	/**
	 * ???
	 * @return string  HTML
	 */
	public function sectionStd()
	{
		Eresus_Kernel::app()->getPage()->title .= ADM_T_DIV.ADM_THEMES_STANDARD;

		$result = '';
		switch (arg('action'))
		{
			case 'update':
				$this->sectionStdUpdate();
				break;
			case 'insert':
				$this->sectionStdInsert();
				break;
			case 'add':
				$result = $this->sectionStdAdd();
				break;
			default:
				if (arg('delete'))
				{
					$this->sectionStdDelete();
				}
				if (arg('id'))
				{
					$result = $this->sectionStdEdit();
				}
				else
				{
					$result = $this->sectionStdList();
				}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создаёт новый файл стилей
	 *
	 * @return void
	 */
	public function sectionStylesInsert()
	{
		$filename = arg('filename');
		$filter = new Eresus_FS_NameFilter();
		$filename = $filter->filter($filename);

		if ('' === $filename)
		{
			$filename = uniqid();
		}

		if ($filename != arg('filename'))
		{
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(ADM_THEMES_FILENAME_FILTERED, $filename));
		}

		$contents = "/* ".arg('description')." */\r\n\r\n".arg('html');
		file_put_contents(Eresus_CMS::getLegacyKernel()->froot . 'style/' . $filename . '.css',
			$contents);
		HTTP::redirect(arg('submitURL'));
	}

	/**
	 * Обновляет файл стилей
	 */
	public function sectionStylesUpdate()
	{
		$this->sectionStylesInsert();
	}

	/**
	 * ???
	 * @return void
	 */
	public function sectionStylesDelete()
	{
		$filename = Eresus_CMS::getLegacyKernel()->froot . 'style/'.arg('delete');
		if (file_exists($filename))
		{
			unlink($filename);
		}
		HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string  HTML
	 */
	public function sectionStylesAdd()
	{
		$form = array(
			'name' => 'addForm',
			'caption' => Eresus_Kernel::app()->getPage()->title.ADM_T_DIV.ADM_ADD,
			'width' => '100%',
			'fields' => array (
				array('type'=>'hidden','name'=>'action', 'value'=>'insert'),
				array('type'=>'hidden','name'=>'section', 'value'=>arg('section')),
				array('type' => 'edit', 'name' => 'filename', 'label' => ADM_THEMES_FILENAME_LABEL,
					'width'=>'200px', 'comment'=>'.css'),
				array('type'=>'edit','name'=>'description','label'=>ADM_THEMES_DESC_LABEL,
					'width'=>'100%'),
				array('type'=>'memo','name'=>'html', 'height'=>'30', 'syntax' => 'css'),
			),
			'buttons' => array('ok','cancel'),
		);
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string  HTML
	 */
	public function sectionStylesEdit()
	{
		$item['filename'] = arg('id');
		$item['html'] = trim(file_get_contents(Eresus_CMS::getLegacyKernel()->froot . 'style/' .
			$item['filename']));
		preg_match('|/\*(.*?)\*/|', $item['html'], $item['description']);
		$item['description'] = trim($item['description'][1]);
		$item['filename'] = substr($item['filename'], 0, strrpos($item['filename'], '.'));
		$item['html'] = trim(mb_substr($item['html'], mb_strpos($item['html'], "\n")));
		$form = array(
			'name' => 'editForm',
			'caption' => Eresus_Kernel::app()->getPage()->title.ADM_T_DIV.ADM_EDIT,
			'width' => '100%',
			'fields' => array (
				array('type'=>'hidden','name'=>'action', 'value'=>'update'),
				array('type'=>'hidden','name'=>'section', 'value'=>arg('section')),
				array('type'=>'hidden','name'=>'filename'),
				array('type' => 'edit', 'name' => '_filename', 'label' => ADM_THEMES_FILENAME_LABEL,
					'width'=>'200px', 'comment'=>'.css', 'disabled' => true, 'value' => $item['filename']),
				array('type'=>'edit','name'=>'description','label'=>ADM_THEMES_DESC_LABEL,
					'width'=>'100%'),
				array('type'=>'memo','name'=>'html', 'height'=>'30', 'syntax' => 'css'),
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);
		/** @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderForm($form, $item);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string
	 */
	public function sectionStylesList()
	{
		$table = array(
			'name' => 'Styles',
			'key'=> 'filename',
			'sortMode' => 'filename',
			'sortDesc' => false,
			'columns' => array(
				array('name' => 'description', 'caption' => 'Описание'),
				array('name' => 'filename', 'caption' => 'Имя файла'),
			),
			'controls' => array (
				'delete' => '',
				'edit' => '',
			),
			'tabs' => array(
				'width'=>'120px',
				'items'=>array(
					array('caption'=>ADM_ADD, 'name'=>'action', 'value'=>'add'),
				)
			),
		);
		# Загружаем список шаблонов
		$dir = Eresus_CMS::getLegacyKernel()->froot . 'style/';
		$hnd = opendir($dir);
		$items = array();
		while (($filename = readdir($hnd))!==false)
		{
			if (preg_match('/.*\.css$/', $filename))
			{
				$description = file_get_contents($dir.$filename);
				preg_match('|/\*(.*?)\*/|', $description, $description);
				$description = trim($description[1]);
				$items[] = array(
					'filename' => $filename,
					'description' => $description,
				);
			}
		}
		closedir($hnd);
		/* @var TAdminUI $page */
		$page = Eresus_Kernel::app()->getPage();
		$result = $page->renderTable($table, $items);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string
	 */
	public function sectionStyles()
	{
		$result = '';
			Eresus_Kernel::app()->getPage()->title .= ADM_T_DIV.ADM_THEMES_STYLES;
		switch (arg('action'))
		{
			case 'update':
				$this->sectionStylesUpdate();
				break;

			case 'insert':
				$this->sectionStylesInsert();
				break;

			case 'add':
				$result = $this->sectionStylesAdd();
				break;

			default:
				if (arg('delete'))
				{
					$this->sectionStylesDelete();
				}
				elseif (arg('id'))
				{
					$result = $this->sectionStylesEdit();
				}
				else
				{
					$result = $this->sectionStylesList();
				}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return string
	 */
	public function adminRender()
	{
		$result = '';
		if (UserRights($this->access))
		{
			#FIXME: Временное решение #0000163
			$this->tabs['items'][0]['url'] =
				Eresus_Kernel::app()->getPage()->url(array('id' => '', 'section' => 'templates'));
			$this->tabs['items'][1]['url'] =
				Eresus_Kernel::app()->getPage()->url(array('id' => '', 'section' => 'std'));
			$this->tabs['items'][2]['url'] =
				Eresus_Kernel::app()->getPage()->url(array('id' => '', 'section' => 'css'));
			/** @var TAdminUI $page */
			$page = Eresus_Kernel::app()->getPage();
			$result .= $page->renderTabs($this->tabs);
			switch (arg('section'))
			{
				case 'css':
					$result .= $this->sectionStyles();
					break;
				case 'std':
					$result .= $this->sectionStd();
					break;
				case 'themes':
				default:
					$result .= $this->sectionTemplates();
					break;
			}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------
}
