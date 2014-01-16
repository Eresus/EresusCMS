<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
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

/**
 * Управление разделами сайта
 *
 * @package Eresus
 */
class TPages
{
    /**
     * Уровень доступа к этому модулю
     * @var int
     */
    public $access = ADMIN;

    /**
     * ???
     * @var array
     */
    public $cache;

    /**
     * Запись новой страницы в БД
     * @return void
     */
    function insert()
    {
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

        $temp = Eresus_CMS::getLegacyKernel()->sections->get("(`name`='" . $item['name'] .
        "') AND (`owner`='" . $item['owner'] . "')");
        if (count($temp) == 0)
        {
            Eresus_CMS::getLegacyKernel()->sections->add($item);
            dbReorderItems('pages', "`owner`='".arg('owner', 'int')."'");
            HTTP::redirect(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
        }
        else
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errItemWithSameName, $item['name']));
            saveRequest();
            HTTP::redirect(Eresus_CMS::getLegacyKernel()->request['referer']);
        }
    }
    //-----------------------------------------------------------------------------

    /**
     * ???
     * @return void
     */
    function update()
    {
        $old = Eresus_CMS::getLegacyKernel()->sections->get(arg('update', 'int'));
        $item = $old;

        $newName = arg('name', '/[^a-z0-9_]/i');
        if ($newName)
        {
            $item['name'] = $newName;
        }
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

        $temp = Eresus_CMS::getLegacyKernel()->sections->get("(`name`='" . $item['name'] .
        "') AND (`owner`='" . $item['owner'] . "' AND `id` <> " . $item['id'] . ")");
        if (count($temp) > 0)
        {
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errItemWithSameName, $item['name']));
            saveRequest();
            HTTP::redirect(Eresus_CMS::getLegacyKernel()->request['referer']);
        }

        if (arg('created'))
        {
            $item['created'] = arg('created', 'dbsafe');
        }
        $item['updated'] = arg('updated', 'dbsafe');
        if (arg('updatedAuto'))
        {
            $item['updated'] = gettime('Y-m-d H:i:s');
        }

        Eresus_CMS::getLegacyKernel()->sections->update($item);

        HTTP::redirect(arg('submitURL'));
    }
    //-----------------------------------------------------------------------------

    /**
     * ???
     * @param $skip
     * @param $owner
     * @param $level
     * @return string
     */
    private function selectList($skip=0, $owner = 0, $level = 0)
    {
        $items = Eresus_CMS::getLegacyKernel()->sections->
            children($owner, Eresus_CMS::getLegacyKernel()->user['access']);
        $result = array(array(), array());
        foreach ($items as $item)
        {
            if ($item['id'] != $skip)
            {
                $item['caption'] = trim($item['caption']);
                if (empty($item['caption']))
                {
                    $item['caption'] = ADM_NA;
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

    /**
     * Функция перемещает страницу вверх в списке
     * @return void
     */
    private function moveUp()
    {
        $item = Eresus_CMS::getLegacyKernel()->sections->get(arg('id', 'int'));
        dbReorderItems('pages', "`owner`='".$item['owner']."'");
        $item = Eresus_CMS::getLegacyKernel()->sections->get(arg('id', 'int'));
        if ($item['position'] > 0)
        {
            $temp = Eresus_CMS::getLegacyKernel()->sections->get("(`owner`='".$item['owner'].
            "') AND (`position`='".
            ($item['position']-1)."')");
            if (count($temp))
            {
                $temp = $temp[0];
                $item['position']--;
                $temp['position']++;
                Eresus_CMS::getLegacyKernel()->sections->update($item);
                Eresus_CMS::getLegacyKernel()->sections->update($temp);
            }
        }
        HTTP::redirect(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
    }

    /**
     * Функция перемещает страницу вниз в списке
     * @return void
     */
    private function moveDown()
    {
        $item = Eresus_CMS::getLegacyKernel()->sections->get(arg('id', 'int'));
        dbReorderItems('pages', "`owner`='".$item['owner']."'");
        $item = Eresus_CMS::getLegacyKernel()->sections->get(arg('id', 'int'));
        if ($item['position'] <
            count(Eresus_CMS::getLegacyKernel()->sections->children($item['owner'])))
        {
            $temp = Eresus_CMS::getLegacyKernel()->sections->
                get("(`owner`='".$item['owner']."') AND (`position`='".
                ($item['position']+1)."')");
            if ($temp)
            {
                $temp = $temp[0];
                $item['position']++;
                $temp['position']--;
                Eresus_CMS::getLegacyKernel()->sections->update($item);
                Eresus_CMS::getLegacyKernel()->sections->update($temp);
            }
        }
        HTTP::redirect(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
    }

    /**
     * Перемещает страницу из одной ветки в другую
     *
     * @return string
     */
    private function move()
    {
        $item = Eresus_CMS::getLegacyKernel()->sections->get(arg('id', 'int'));
        if (!is_null(arg('to')))
        {
            $item['owner'] = arg('to', 'int');
            $item['position'] = count(Eresus_CMS::getLegacyKernel()->sections->children($item['owner']));

            /* Проверяем, нет ли в разделе назначения раздела с таким же именем */
            $q = Eresus_DB::getHandler()->createSelectQuery();
            $e = $q->expr;
            $q->select($q->alias($e->count('id'), 'count'));
            $q->from('pages');
            $q->where($e->lAnd(
                $e->eq('owner', $q->bindValue($item['owner'], null, PDO::PARAM_INT)),
                $e->eq('name', $q->bindValue($item['name']))
            ));
            $count = $q->fetch();
            if ($count['count'])
            {
                Eresus_Kernel::app()->getPage()->addErrorMessage(
                    'В разделе назначения уже есть раздел с таким же именем!');
                HTTP::goback();
            }

            Eresus_CMS::getLegacyKernel()->sections->update($item);
            HTTP::redirect(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
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
            /** @var TAdminUI $page */
            $page = Eresus_Kernel::app()->getPage();
            $result = $page->renderForm($form);
            return $result;
        }
        return '';
    }

    /**
     * Удаляет страницу
     * @return void
     */
    private function delete()
    {
        $item = Eresus_CMS::getLegacyKernel()->sections->get(arg('id', 'int'));
        Eresus_CMS::getLegacyKernel()->sections->delete(arg('id', 'int'));
        dbReorderItems('pages', "`owner`='".$item['owner']."'");
        HTTP::redirect(Eresus_Kernel::app()->getPage()->url(array('id'=>'')));
    }

    /**
     * Возвращает список типов контента в виде, пригодном для построения выпадающего списка
     *
     * @return array
     */
    private function loadContentTypes()
    {
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
        $plugins = Eresus_Plugin_Registry::getInstance();
        foreach ($plugins->getEnabled() as $plugin)
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

        return $result;
    }

    /**
     * ???
     * @return array
     */
    function loadTemplates()
    {
        $result[0] = array();
        $result[1] = array();
        $templates = Templates::getInstance();
        $list = $templates->enum();
        $result[0]= array_values($list);
        $result[1]= array_keys($list);
        return $result;
    }

    /**
     * Функция выводит форму для добавления новой страницы
     * @return string
     */
    function create()
    {
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

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = $page->renderForm($form, Eresus_CMS::getLegacyKernel()->request['arg']);
        return $result;
    }

    /**
     * Возвращает диалог изменения свойств раздела
     *
     * @param int $id
     * @return string  HTML
     */
    private function edit($id)
    {
        $item = Eresus_CMS::getLegacyKernel()->sections->get($id);
        $content = $this->loadContentTypes();
        $templates = $this->loadTemplates();
        $item['options'] = array2text($item['options'], true);
        $form['caption'] = $item['caption'];
        # Вычисляем адрес страницы
        $urlAbs = Eresus_Kernel::app()->getPage()->clientURL($item['id']);

        $isMainPage = $item['name'] == 'main' && $item['owner'] == 0;

        $form = array(
            'name' => 'PageForm',
            'caption' => $item['caption'].' ('.$item['name'].')',
            'width' => '700px',
            'fields' => array (
                array ('type' => 'hidden','name' => 'update', 'value'=>$item['id']),
                array ('type' => 'edit','name' => 'name','label' => admPagesName,'width' => '150px',
                    'maxlength' => '32', 'pattern'=>'/^[a-z0-9_]+$/i', 'errormsg'=>admPagesNameInvalid,
                    'disabled' => $isMainPage),
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
                array ('type' => 'text',
                    'value'=>admPagesThisURL.': <a href="'.$urlAbs.'">'.$urlAbs.'</a>'),
            ),
            'buttons' => array('ok', 'apply', 'cancel'),
        );

        if ($isMainPage)
        {
            array_unshift($form['fields'],
                array('type' => 'hidden', 'name' => 'name', 'value' => 'main'));
        }

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = $page->renderForm($form, $item);
        return $result;
    }

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
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = array();
        $items = Eresus_CMS::getLegacyKernel()->sections->children($owner,
            Eresus_CMS::getLegacyKernel()->user['auth'] ?
                Eresus_CMS::getLegacyKernel()->user['access'] : GUEST);
        for ($i=0; $i<count($items); $i++)
        {
            $content_type = isset($this->cache['content_types'][$items[$i]['type']]) ?
                $this->cache['content_types'][$items[$i]['type']] :
                '<span class="admError">'.sprintf(errContentType, $items[$i]['type']).'</span>';
            $row = array();
            $row[] = array('text' => $items[$i]['caption'], 'style'=>"padding-left: {$level}em;",
                'href'=>Eresus_CMS::getLegacyKernel()->root.'admin.php?mod=content&amp;section='.
                $items[$i]['id']);
            $row[] = $items[$i]['name'];
            $row[] = array('text' => $content_type, 'align' => 'center');
            $row[] = array('text' => constant('ACCESSLEVEL'.$items[$i]['access']), 'align' => 'center');
            if ($items[$i]['name'] == 'main' && $items[$i]['owner'] == 0)
            {
                $root = Eresus_CMS::getLegacyKernel()->root.'admin.php?mod=pages&amp;';
                $controls =
                    $page->control('setup', $root.'id=%d').' '.
                    $page->control('position',
                        array($root.'action=up&amp;id=%d', $root.'action=down&amp;id=%d')).' '.
                    $page->control('add', $root.'action=create&amp;owner=%d');
            }
            else
            {
                $controls = $this->cache['index_controls'];
            }
            $row[] = sprintf($controls, $items[$i]['id'], $items[$i]['id'], $items[$i]['id'],
                $items[$i]['id'], $items[$i]['id'], $items[$i]['id']);
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
     * @return string
     */
    function sectionIndex()
    {
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $root = Eresus_CMS::getLegacyKernel()->root.'admin.php?mod=pages&amp;';
        $this->cache['index_controls'] =
            $page->control('setup', $root.'id=%d') . ' ' .
            $page->control('position',
                array($root.'action=up&amp;id=%d',$root.'action=down&amp;id=%d')) . ' ' .
            $page->control('add', $root.'action=create&amp;owner=%d') . ' ' .
            $page->control('move', $root.'action=move&amp;id=%d') . ' ' .
            $page->control('delete', $root.'action=delete&amp;id=%d');
        $types = $this->loadContentTypes();
        for ($i=0; $i<count($types[0]); $i++)
        {
            $this->cache['content_types'][$types[1][$i]] = $types[0][$i];
        }
        $table = new AdminList;
        $table->setHead(array('text'=>'Раздел', 'align'=>'left'), 'Имя', 'Тип', 'Доступ', '');
        $table->addRow(array(admPagesRoot, '', '', '',
            array($page->control('add', $root.'action=create&amp;owner=0'), 'align' => 'center')));
        $table->addRows($this->sectionIndexBranch(0, 1));
        $result = $table->render();
        return $result;
    }

    /**
     * ???
     * @return string
     */
    function adminRender()
    {
        if (UserRights($this->access))
        {
            $result = '';
            if (arg('update'))
            {
                $this->update();
            }
            elseif (arg('action'))
            {
                switch (arg('action'))
                {
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
            elseif (isset(Eresus_CMS::getLegacyKernel()->request['arg']['id']))
            {
                $result = $this->edit(arg('id', 'int'));
            }
            else
            {
                $result = $this->sectionIndex();
            }
            return $result;
        }
        else
        {
            return '';
        }
    }
    //-----------------------------------------------------------------------------
}

