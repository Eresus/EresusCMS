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
 * @package BusinessLogic
 *
 * $Id$
 */

/**
 * Управление разделами сайта
 *
 * @package BusinessLogic
 */
class Eresus_Controller_Admin_Sections extends Eresus_Controller_Admin_Abstract
{
	/**
	 * Уровень доступа к этому модулу
	 * @var int
	 */
	public $access = ADMIN;

	/**
	 * ???
	 * @var unknown_type
	 */
	public $cache;

	/**
	 * Запись новой страницы в БД
	 * @return unknown_type
	 */
	function insert()
	{
		global $Eresus, $page;

		$item = array();
		$item['owner'] = arg('owner', 'int');
		$item['name'] = arg('name', '/[^a-z0-9_]/i');
		$item['title'] = arg('title', 'dbsafe');
		$item['caption'] = arg('caption', 'dbsafe');
		$item['description'] = arg('description', 'dbsafe');
		$item['hint'] = arg('hint', 'dbsafe');
		$item['keywords'] = arg('keywords', 'dbsafe');
		$item['template'] = arg('template', 'dbsafe');
		$item['type'] = arg('type', 'dbsafe');
		$item['active'] = arg('active', 'int');
		$item['visible'] = arg('visible', 'int');
		$item['access'] = arg('access', 'int');
		$item['position'] = arg('position', 'int');
		$item['options'] = arg('options');
		$item['created'] = $item['updated'] = gettime('Y-m-d H:i:s');

		$temp = $Eresus->sections->get("(`name`='" . $item['name'] . "') AND (`owner`='" .
			$item['owner'] . "')");
		if (count($temp) == 0)
		{
			$item = $Eresus->sections->add($item);
			dbReorderItems('pages', "`owner`='".arg('owner', 'int')."'");
			HttpResponse::redirect($page->url(array('id'=>'')));
		}
		else
		{
			ErrorMessage(sprintf(errItemWithSameName, $item['name']));
			saveRequest();
			HttpResponse::redirect($Eresus->request['referer']);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return unknown_type
	 */
	function update()
	{
		global $Eresus, $page;

		$old = $Eresus->sections->get(arg('update', 'int'));
		$item = $old;

		$item['name'] = arg('name', '/[^a-z0-9_]/i');
		$item['title'] = arg('title', 'dbsafe');
		$item['caption'] = arg('caption', 'dbsafe');
		$item['description'] = arg('description', 'dbsafe');
		$item['hint'] = arg('hint', 'dbsafe');
		$item['keywords'] = arg('keywords', 'dbsafe');
		$item['template'] = arg('template', 'dbsafe');
		$item['type'] = arg('type', 'dbsafe');
		$item['active'] = arg('active', 'int');
		$item['visible'] = arg('visible', 'int');
		$item['access'] = arg('access', 'int');
		$item['position'] = arg('position', 'int');
		$item['options'] = text2array(arg('options'), true);
		if (arg('created'))
		{
			$item['created'] = arg('created', 'dbsafe');
		}
		$item['updated'] = arg('updated', 'dbsafe');
		if (arg('updatedAuto'))
		{
			$item['updated'] = gettime('Y-m-d H:i:s');
		}

		$Eresus->sections->update($item);

		HttpResponse::redirect(arg('submitURL'));
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @param $skip
	 * @param $owner
	 * @param $level
	 * @return unknown_type
	 */
	function selectList($skip=0, $owner = 0, $level = 0)
	{
		global $Eresus;

		$items = $Eresus->sections->children($owner, $Eresus->user['access']);
		$result = array(array(), array());
		foreach ($items as $item)
		{
			if ($item['id'] != $skip)
			{
				$item['caption'] = trim($item['caption']);
				if (empty($item['caption']))
				{
					$item['caption'] = admNA;
				}
				$result[0][] = $item['id'];
				$result[1][] = str_repeat('&nbsp;', $level*2).$item['caption'];
				$children = $this->selectList($skip, $item['id'], $level+1);
				$result[0] = array_merge($result[0], $children[0]);
				$result[1] = array_merge($result[1], $children[1]);
			}
		}
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещает раздел вверх в списке
	 *
	 * @return void
	 *
	 * @uses EresusORM::getTable()
	 * @uses HttpResponse::redirect()
	 */
	private function moveUp()
	{
		$item = EresusORM::getTable('Eresus_Model_Section')->find(arg('id', 'int'));
		if ($item)
		{
			$item->moveUp();
		}
		HttpResponse::redirect($GLOBALS['page']->url(array('id'=>'')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещает раздел вниз в списке
	 *
	 * @return void
	 *
	 * @uses EresusORM::getTable()
	 * @uses HttpResponse::redirect()
	 */
	private function moveDown()
	{
		$item = EresusORM::getTable('Eresus_Model_Section')->find(arg('id', 'int'));
		if ($item)
		{
			$item->moveDown();
		}
		HttpResponse::redirect($GLOBALS['page']->url(array('id'=>'')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перемещает страницу из одной ветки в другую
	 *
	 * @return string
	 */
	function move()
	{
		global $Eresus, $page;

		$item = $Eresus->sections->get(arg('id', 'int'));
		if (!is_null(arg('to')))
		{
			$item['owner'] = arg('to', 'int');
			$item['position'] = count($Eresus->sections->children($item['owner']));

			/* Проверяем, нет ли в разделе назанчения раздела с таким же именем */
			$q = DB::createSelectQuery();
			$e = $q->expr;
			$q->select($q->alias($e->count('id'), 'count'))
				->from('pages')
				->where($e->lAnd(
					$e->eq('owner', $q->bindValue($item['owner'], null, PDO::PARAM_INT)),
					$e->eq('name', $q->bindValue($item['name']))
				));
			$count = DB::fetch($q);
			if ($count['count'])
			{
				ErrorMessage(iconv('utf-8', 'cp1251',
					'В разделе назначения уже есть раздел с таким же именем!'));
				HTTP::goback();
			}

			$Eresus->sections->update($item);
			HttpResponse::redirect($page->url(array('id'=>'')));
		}
		else
		{
			$select = $this->selectList($item['id']);
			array_unshift($select[0], 0);
			array_unshift($select[1], admPagesRoot);
			$form = array(
				'name' => 'MoveForm',
				'caption' => admPagesMove,
				'fields' => array(
					array('type'=>'hidden', 'name'=>'mod', 'value' => 'pages'),
					array('type'=>'hidden', 'name'=>'action', 'value' => 'move'),
					array('type'=>'hidden', 'name'=>'id', 'value' => $item['id']),
					array('type'=>'select', 'label'=>strMove.' "<b>'.$item['caption'].'</b>" в',
						'name'=>'to', 'items'=>$select[1], 'values'=>$select[0], 'value' => $item['owner']),
				),
				'buttons' => array('ok', 'cancel'),
			);
			$result = $page->renderForm($form);
			return $result;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Удаляет ветку разделов
	 *
	 * @return void
	 *
	 * @uses HttpResponse::redirect()
	 */
	private function delete()
	{
		$item = EresusORM::getTable('Eresus_Model_Section')->find(arg('id', 'int'));
		$owner = $item->owner;
		$item->delete();
		HttpResponse::redirect($GLOBALS['page']->url(array('id'=>'')));
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает список типов контента в виде, пригодном для построения выпадающего списка
	 *
	 * @return array
	 */
	private function loadContentTypes()
	{
		global $Eresus;

		$result[0] = array();
		$result[1] = array();

		/*
		 * Стандартные типы контента
		 */
		$result[0] []= admPagesContentDefault;
		$result[1] []= 'default';

		$result[0] []= admPagesContentList;
		$result[1] []= 'list';

		$result[0] []= admPagesContentURL;
		$result[1] []= 'url';

		/*
		 * Типы контентов из плагинов
		 */
		if (count($Eresus->plugins->items))
		{
			foreach ($Eresus->plugins->items as $plugin)
			{
				if (
					$plugin instanceof ContentPlugin ||
					$plugin instanceof TContentPlugin
				)
				{
					$result[0][] = $plugin->title;
					$result[1][] = $plugin->name;
				}
			}
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return unknown_type
	 */
	function loadTemplates()
	{
		$result[0] = array();
		$result[1] = array();
		useLib('templates');
		$templates = new Templates();
		$list = $templates->enum();
		$result[0]= array_values($list);
		$result[1]= array_keys($list);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Функция выводит форму для добавления новой страницы
	 * @return unknown_type
	 */
	function create()
	{
		global $Eresus, $page;

		$content = $this->loadContentTypes();
		$templates = $this->loadTemplates();
		restoreRequest();
		$form = array (
			'name' => 'createPage',
			'caption' => strAdd,
			'width' => '600px',
			'fields' => array (
				array ('type' => 'hidden','name'=>'owner','value'=>arg('owner', 'int')),
				array ('type' => 'hidden','name'=>'action', 'value'=>'insert'),
				array ('type' => 'edit','name' => 'name','label' => admPagesName,'width' => '150px',
					'maxlength' => '32', 'pattern'=>'/^[a-z0-9_]+$/i', 'errormsg'=>admPagesNameInvalid),
				array ('type' => 'edit','name' => 'title','label' => admPagesTitle,'width' => '100%',
					'pattern'=>'/.+/', 'errormsg'=>admPagesTitleInvalid),
				array ('type' => 'edit','name' => 'caption','label' => admPagesCaption,'width' => '100%',
					'maxlength' => '64', 'pattern'=>'/.+/', 'errormsg'=>admPagesCaptionInvalid),
				array ('type' => 'edit','name' => 'hint','label' => admPagesHint,'width' => '100%'),
				array ('type' => 'edit','name' => 'description','label' => admPagesDescription,
					'width' => '100%'),
				array ('type' => 'edit','name' => 'keywords','label' => admPagesKeywords,'width' => '100%'),
				array ('type' => 'select','name' => 'template','label' => admPagesTemplate,
					'items' => $templates[0], 'values' => $templates[1], 'default'=>pageTemplateDefault),
				array ('type' => 'select','name' => 'type','label' => admPagesContentType,
					'items' => $content[0], 'values' => $content[1], 'default'=>contentTypeDefault),
				array ('type' => 'checkbox','name' => 'active','label' => admPagesActive, 'default'=>true),
				array ('type' => 'checkbox','name' => 'visible','label' => admPagesVisible,
					'default'=>true),
				array ('type' => 'select','name' => 'access','label' => admAccessLevel,'access' => ADMIN,
					'values'=>array(ADMIN,EDITOR,USER,GUEST),
					'items' => array (ACCESSLEVEL2,ACCESSLEVEL3,ACCESSLEVEL4,ACCESSLEVEL5),
					'default' => GUEST),
				array ('type' => 'edit','name' => 'position','label' => admPosition,'access' => ADMIN,
					'width' => '4em','maxlength' => '5'),
				array ('type' => 'memo','name' => 'options','label' => admPagesOptions,'height' => '5')
			),
			'buttons' => array('ok', 'cancel'),
		);

		$result = $page->renderForm($form, $Eresus->request['arg']);
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог изменения свойств раздела
	 *
	 * @param int $id
	 * @return string  HTML
	 */
	private function edit($id)
	{
		global $Eresus, $page;

		$item = $Eresus->sections->get($id);
		$content = $this->loadContentTypes();
		$templates = $this->loadTemplates();
		$item['options'] = array2text($item['options'], true);
		$form['caption'] = $item['caption'];

		$form = array(
			'name' => 'PageForm',
			'caption' => $item['caption'].' ('.$item['name'].')',
			'width' => '700px',
			'fields' => array (
				array ('type' => 'hidden','name' => 'update', 'value'=>$item['id']),
				array ('type' => 'edit','name' => 'name','label' => admPagesName,'width' => '150px',
					'maxlength' => '32', 'pattern'=>'/^[a-z0-9_]+$/i', 'errormsg'=>admPagesNameInvalid),
				array ('type' => 'edit','name' => 'title','label' => admPagesTitle,'width' => '100%',
					'pattern'=>'/.+/', 'errormsg'=>admPagesTitleInvalid),
				array ('type' => 'edit','name' => 'caption','label' => admPagesCaption,
					'width' => '100%','maxlength' => '64', 'pattern'=>'/.+/',
					'errormsg'=>admPagesCaptionInvalid),
				array ('type' => 'edit','name' => 'hint','label' => admPagesHint,'width' => '100%'),
				array ('type' => 'edit','name' => 'description','label' => admPagesDescription,
					'width' => '100%'),
				array ('type' => 'edit','name' => 'keywords','label' => admPagesKeywords,'width' => '100%'),
				array ('type' => 'select','name' => 'template','label' => admPagesTemplate,
					'items' => $templates[0], 'values' => $templates[1]),
				array ('type' => 'select','name' => 'type','label' => admPagesContentType,
					'items' => $content[0], 'values' => $content[1]),
				array ('type' => 'checkbox','name' => 'active','label' => admPagesActive),
				array ('type' => 'checkbox','name' => 'visible','label' => admPagesVisible),
				array ('type' => 'select','name' => 'access','label' => admAccessLevel,'access' => ADMIN,
					'values'=>array(ADMIN,EDITOR,USER,GUEST),
					'items' => array (ACCESSLEVEL2,ACCESSLEVEL3,ACCESSLEVEL4,ACCESSLEVEL5)),
				array ('type' => 'edit','name' => 'position','label' => admPosition,'access' => ADMIN,
					'width' => '4em','maxlength' => '5'),
				array ('type' => 'memo','name' => 'options','label' => admPagesOptions,'height' => '5'),
				array ('type' => 'edit','name' => 'created','label' => admPagesCreated,'access' => ADMIN,
					'width' => '10em','maxlength' => '19'),
				array ('type' => 'edit','name' => 'updated','label' => admPagesUpdated,'access' => ADMIN,
					'width' => '10em','maxlength' => '19'),
				array ('type' => 'checkbox','name' => 'updatedAuto','label' => admPagesUpdatedAuto,
					'default' => true),
			),
			'buttons' => array('ok', 'apply', 'cancel'),
		);
		$result = $page->renderForm($form, $item);

		$tmpl = new Template('core/templates/ContentEditor/common.html');

		$plugin = $GLOBALS['Eresus']->plugins->load($item['type']);

		$data = array(
			'editor' => $result,
			'contentURL' => 'admin.php?mod=content&section=' . $item['id'],
			'propertiesURL' => '#',
			'plugin' => $plugin,
			'clientURL' => $GLOBALS['page']->clientURL($item['id'])
		);
		$html = $tmpl->compile($data);

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовывает подраздел индекса
	 *
	 * @param  int  $owner  Родительский раздел
	 * @param  int  $level  Уровень вложенности
	 *
	 * @return  string  Отрисованная часть таблицы
	 */
	function sectionIndexBranch($owner=0, $level=0)
	{
		global $Eresus;

		$result = array();
		$items = $Eresus->sections->children($owner,
			$_SESSION['user'] ? $Eresus->user['access'] : GUEST);
		for ($i=0; $i<count($items); $i++)
		{
			$content_type = isset($this->cache['content_types'][$items[$i]['type']]) ?
				$this->cache['content_types'][$items[$i]['type']] :
				'<span class="admError">'.sprintf(errContentType, $items[$i]['type']).'</span>';
			$row = array();
			$row[] = array('text' => $items[$i]['caption'], 'style'=>"padding-left: {$level}em;",
				'href'=>$Eresus->root.'admin.php?mod=content&amp;section='.$items[$i]['id']);
			$row[] = $items[$i]['name'];
			$row[] = array('text' => $content_type, 'align' => 'center');
			$row[] = array('text' => constant('ACCESSLEVEL'.$items[$i]['access']), 'align' => 'center');
			$row[] = sprintf($this->cache['index_controls'], $items[$i]['id'], $items[$i]['id'],
				$items[$i]['id'], $items[$i]['id'], $items[$i]['id'], $items[$i]['id']);
			$result[] = $row;
			$children = $this->sectionIndexBranch($items[$i]['id'], $level+1);
			if (count($children))
			{
				$result = array_merge($result, $children);
			}
		}
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * ???
	 * @return unknown_type
	 */
	function sectionIndex()
	{
		global $Eresus, $page;

		$root = $Eresus->root.'admin.php?mod=pages&amp;';
		$this->cache['index_controls'] =
			$page->control('setup', $root.'id=%d').' '.
			$page->control('position', array($root.'action=up&amp;id=%d',$root.'action=down&amp;id=%d')).
			' '.
			$page->control('add', $root.'action=create&amp;owner=%d').' '.
			$page->control('move', $root.'action=move&amp;id=%d').' '.
			$page->control('delete', $root.'action=delete&amp;id=%d');
		$types = $this->loadContentTypes();
		for ($i=0; $i<count($types[0]); $i++)
		{
			$this->cache['content_types'][$types[1][$i]] = $types[0][$i];
		}
		useLib('admin/lists');
		$table = new AdminList;
		$table->setHead(array('text'=>iconv('utf-8', 'cp1251', 'Раздел'), 'align'=>'left'),
			iconv('utf-8', 'cp1251', 'Имя'), iconv('utf-8', 'cp1251', 'Тип'),
			iconv('utf-8', 'cp1251', 'Доступ'), '');
		$table->addRow(array(admPagesRoot, '', '', '',
			array($page->control('add', $root.'action=create&amp;owner=0'), 'align' => 'center')));
		$table->addRows($this->sectionIndexBranch(0, 1));
		$result = $table->render();
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * ???
	 * @return unknown_type
	 */
	function adminRender()
	{
		global $Eresus, $page;

		if (UserRights($this->access))
		{
			$result = '';
			if (arg('update'))
			{
				$this->update();
			}
			elseif (arg('action'))
			{
				switch(arg('action')) {
					case 'up':
						$this->moveUp();
					break;
					case 'down':
						$this->moveDown();
					break;
					case 'create':
						$result = $this->create();
					break;
					case 'insert':
						$this->insert();
					break;
					case 'move':
						$result = $this->move();
					break;
					case 'delete':
						$this->delete();
					break;
				}
			}
			elseif (isset($Eresus->request['arg']['id']))
			{
				$result = $this->edit(arg('id', 'int'));
			}
			else
			{
				$result = $this->sectionIndex();
			}
			return $result;
		}
	}
	//-----------------------------------------------------------------------------
}