<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004-2007, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007-2008, Eresus Project, http://eresus.ru/
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
 * Базовый класс для плагинов с контентом в виде списков
 *
 * @package Eresus
 */
class TListContentPlugin extends TContentPlugin
{
    public $table;
    public $pagesCount = 0;

    function install()
    {
        $this->createTable($this->table);
        parent::install();
    }

    function uninstall()
    {
        $this->dropTable($this->table);
        parent::uninstall();
    }

    function createTable($table)
    {
        Eresus_CMS::getLegacyKernel()->db->
            query('CREATE TABLE IF NOT EXISTS `'.Eresus_CMS::getLegacyKernel()->db->prefix.
            $table['name'].'`'.$table['sql']);
    }

    function dropTable($table)
    {
        Eresus_CMS::getLegacyKernel()->db->
            query("DROP TABLE IF EXISTS `".Eresus_CMS::getLegacyKernel()->db->prefix.$table['name']."`;");
    }

    public function toggle($id)
    {
        Eresus_CMS::getLegacyKernel()->db->
            update($this->table['name'], "`active` = NOT `active`", "`".$this->table['key']."`='".$id.
                "'");
        Eresus_CMS::getLegacyKernel()->db->
            selectItem($this->table['name'], "`".$this->table['key']."`='".$id."'");
        HTTP::redirect(str_replace('&amp;', '&', Eresus_Kernel::app()->getPage()->url()));
    }

    function delete($id)
    {
        Eresus_CMS::getLegacyKernel()->db->
            selectItem($this->table['name'], "`".$this->table['key']."`='".$id."'");
        Eresus_CMS::getLegacyKernel()->db->
            delete($this->table['name'], "`".$this->table['key']."`='".$id."'");
        HTTP::redirect(str_replace('&amp;', '&', Eresus_Kernel::app()->getPage()->url()));
    }

    function up($id)
    {
        $sql_prefix = strpos($this->table['sql'], '`section`') ?
            "(`section`=".arg('section', 'int').") " : 'TRUE';
        dbReorderItems($this->table['name'], $sql_prefix);
        # FIXME: Escaping
        $item = Eresus_CMS::getLegacyKernel()->db->
            selectItem($this->table['name'], "`".$this->table['key']."`='".$id."'");
        if ($item['position'] > 0)
        {
            $temp = Eresus_CMS::getLegacyKernel()->db->
                selectItem($this->table['name'],"$sql_prefix AND (`position`='".($item['position']-1)."')");
            $temp['position'] = $item['position'];
            $item['position']--;
            Eresus_CMS::getLegacyKernel()->db->
                updateItem($this->table['name'], $item, "`".$this->table['key']."`='".$item['id']."'");
            Eresus_CMS::getLegacyKernel()->db->
                updateItem($this->table['name'], $temp, "`".$this->table['key']."`='".$temp['id']."'");
        }
        HTTP::redirect(str_replace('&amp;', '&', Eresus_Kernel::app()->getPage()->url()));
    }

    function down($id)
    {
        $sql_prefix = strpos($this->table['sql'], '`section`') ?
            "(`section`=".arg('section', 'int').") " : 'TRUE';
        dbReorderItems($this->table['name'], $sql_prefix);
        $count = Eresus_CMS::getLegacyKernel()->db->count($this->table['name'], $sql_prefix);
        #FIXME: Escaping
        $item = Eresus_CMS::getLegacyKernel()->db->
            selectItem($this->table['name'], "`".$this->table['key']."`='".$id."'");
        if ($item['position'] < $count-1)
        {
            $temp = Eresus_CMS::getLegacyKernel()->db->
                selectItem($this->table['name'],"$sql_prefix AND (`position`='".($item['position']+1)."')");
            $temp['position'] = $item['position'];
            $item['position']++;
            Eresus_CMS::getLegacyKernel()->db->
                updateItem($this->table['name'], $item, "`".$this->table['key']."`='".$item['id']."'");
            Eresus_CMS::getLegacyKernel()->db->
                updateItem($this->table['name'], $temp, "`".$this->table['key']."`='".$temp['id']."'");
        }
        HTTP::redirect(str_replace('&amp;', '&', Eresus_Kernel::app()->getPage()->url()));
    }


    public function adminRenderContent()
    {
        $result = '';
        if (!is_null(arg('id')))
        {
            $item = Eresus_CMS::getLegacyKernel()->db->
                selectItem($this->table['name'], "`".$this->table['key']."` = '".arg('id', 'dbsafe')."'");
            Eresus_Kernel::app()->getPage()->title .= empty($item['caption'])?'':' - '.$item['caption'];
        }
        switch (true)
        {
            case !is_null(arg('update')) && isset($this->table['controls']['edit']):
                if (method_exists($this, 'update'))
                {
                    $result = $this->update();
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errMethodNotFound, 'update', get_class($this)));
                }
                break;
            case !is_null(arg('toggle')) && isset($this->table['controls']['toggle']):
                if (method_exists($this, 'toggle'))
                {
                    $result = $this->toggle(arg('toggle', 'dbsafe'));
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errMethodNotFound, 'toggle', get_class($this)));
                }
                break;
            case !is_null(arg('delete')) && isset($this->table['controls']['delete']):
                if (method_exists($this, 'delete'))
                {
                    $result = $this->delete(arg('delete', 'dbsafe'));
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errMethodNotFound, 'delete', get_class($this)));
                }
                break;
            case !is_null(arg('up')) && isset($this->table['controls']['position']):
                if (method_exists($this, 'up'))
                {
                    $result = $this->table['sortDesc'] ?
                        $this->down(arg('up', 'dbsafe')) :
                        $this->up(arg('up', 'dbsafe'));
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errMethodNotFound, 'up', get_class($this)));
                }
                break;
            case !is_null(arg('down')) && isset($this->table['controls']['position']):
                if (method_exists($this, 'down'))
                {
                    if ($this->table['sortDesc'])
                    {
                        $this->up(arg('down', 'dbsafe'));
                    }
                    else
                    {
                        $this->down(arg('down', 'dbsafe'));
                    }
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errMethodNotFound, 'down', get_class($this)));
                }
                break;
            case !is_null(arg('id')) && isset($this->table['controls']['edit']):
                if (method_exists($this, 'adminEditItem'))
                {
                    $result = $this->adminEditItem();
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errMethodNotFound, 'adminEditItem', get_class($this)));
                }
                break;
            case !is_null(arg('action')):
                switch (arg('action'))
                {
                    case 'create':
                        if (isset($this->table['controls']['edit']))
                        {
                            if (method_exists($this, 'adminAddItem'))
                            {
                                $result = $this->adminAddItem();
                            }
                            else
                            {
                                Eresus_Kernel::app()->getPage()->addErrorMessage(
                                    sprintf(errMethodNotFound, 'adminAddItem', get_class($this)));
                            }
                        }
                        break;
                    case 'insert':
                        if (method_exists($this, 'insert'))
                        {
                            $result = $this->insert();
                        }
                        else
                        {
                            Eresus_Kernel::app()->getPage()->addErrorMessage(
                                sprintf(errMethodNotFound, 'insert', get_class($this)));
                        }
                        break;
                }
                break;
            default:
                if (!is_null(arg('section')))
                {
                    $this->table['condition'] = "`section`='".arg('section', 'int')."'";
                }
                /** @var TAdminUI $page */
                $page = Eresus_Kernel::app()->getPage();
                $result = $page->renderTable($this->table);
        }
        return $result;
    }

    /**
     * Отрисовка клиентской части
     *
     * @throws Eresus_CMS_Exception_NotFound
     *
     * @return string|Eresus_HTTP_Response
     */
    public function clientRenderContent()
    {
        $result = '';
        if (!isset($this->settings['itemsPerPage']))
        {
            $this->settings['itemsPerPage'] = 0;
        }
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        if ($page->topic)
        {
            $result = $this->clientRenderItem();
        }
        else
        {
            $db = Eresus_CMS::getLegacyKernel()->db;
            $this->table['fields'] = $db->fields($this->table['name']);
            $itemsCount = $db->count($this->table['name'], "(`section`='" . $page->id . "')".
                    (in_array('active', $this->table['fields'])?"AND(`active`='1')":''));
            if ($itemsCount)
            {
                $this->pagesCount = $this->settings['itemsPerPage'] ?
                    ((integer) ($itemsCount / $this->settings['itemsPerPage']) +
                        (($itemsCount % $this->settings['itemsPerPage']) > 0)):1;
            }
            if (!$page->subpage)
            {
                $page->subpage = $this->table['sortDesc'] ? $this->pagesCount : 1;
            }
            if ($itemsCount && ($page->subpage > $this->pagesCount))
            {
                throw new Eresus_CMS_Exception_NotFound;
            }
            else
            {
                $result .= $this->clientRenderList();
            }
        }
        return $result;
    }

    /**
     * @param array|null $options
     * @return string
     */
    function clientRenderList($options = null)
    {
        if (is_null($options))
        {
            $options = array();
        }
        $options['pages'] = isset($options['pages']) ? $options['pages'] : true;
        $options['oldordering'] = isset($options['oldordering']) ? $options['oldordering'] : true;

        $result = '';
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $items = Eresus_CMS::getLegacyKernel()->db->select(
            $this->table['name'],
            "(`section`='" . $page->id . "')".
            (strpos($this->table['sql'], '`active`')!==false?"AND(`active`='1')":''),
            ($this->table['sortDesc'] ? '-' : '+').$this->table['sortMode'],
            '',
            $this->settings['itemsPerPage'],
            $this->table['sortDesc'] && $options['oldordering']
                ? (
                ($this->pagesCount - $page->subpage) *
                $this->settings['itemsPerPage'])
                : (($page->subpage-1)*$this->settings['itemsPerPage'])
        );
        if (count($items))
        {
            foreach ($items as $item)
            {
                $result .= $this->clientRenderListItem($item);
            }
        }
        if ($options['pages'])
        {
            $pages = $this->clientRenderPages();
            $result .= $pages;
        }
        return $result;
    }

    /**
     * Возвращает разметку одного объекта в представлении «Список объектов»
     *
     * @param array $item
     * @return string
     */
    protected function clientRenderListItem(array $item)
    {
        $result = $item['caption']."<br />\n";
        return $result;
    }

    /**
     * Возвращает разметку представления «Просмотр объекта»
     *
     * @return string
     */
    protected function clientRenderItem()
    {
        return '';
    }

    /**
     * Возвращает разметку переключателя страниц
     * @return string
     */
    protected function clientRenderPages()
    {
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = $page->pages($this->pagesCount, $this->settings['itemsPerPage'],
            $this->table['sortDesc']);
        return $result;
    }

}
