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


/* * * * * * * * * * * * * * * * * * * * * * * *
*
*     Классы-предки для создания плагинов
*
* * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Базовый класс для плагинов, предоставляющих тип контента
 *
 * @package Eresus
 */
class ContentPlugin extends Eresus_Plugin
{
    /**
     * Конструктор
     *
     * Устанавливает плагин в качестве плагина контента и читает локальные настройки
     */
    public function __construct()
    {
        parent::__construct();

        /* @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        if ($page instanceof TClientUI)
        {
            $page->plugin = $this->getName();
            if (isset($page->options) && count($page->options))
            {
                foreach ($page->options as $key => $value)
                {
                    $this->settings[$key] = $value;
                }
            }
        }
    }

    /**
     * Возвращает информацию о плагине
     *
     * @param  array  $item  Предыдущая версия информации (по умолчанию null)
     *
     * @return  array  Массив информации, пригодный для записи в БД
     */
    public function __item($item = null)
    {
        $result = parent::__item($item);
        $result['content'] = true;
        return $result;
    }

    /**
     * Действия при удалении раздела данного типа
     * @param int     $id     Идентификатор удаляемого раздела
     * @param string  $table  Имя таблицы
     */
    public function onSectionDelete($id, $table = '')
    {
        if (count($this->dbTable($table)))
        {
            $this->dbDelete($table, $id, 'section');
        }
    }

    /**
     * Обновляет контент страницы в БД
     *
     * @param  string  $content  Контент
     */
    public function updateContent($content)
    {
        $db = Eresus_CMS::getLegacyKernel()->db;
        $item = $db->selectItem('pages', "`id`='".Eresus_Kernel::app()->getPage()->id."'");
        $item['content'] = $content;
        $db->updateItem('pages', $item, "`id`='".Eresus_Kernel::app()->getPage()->id."'");
    }

    /**
     * Обновляет контент страницы
     */
    protected function adminUpdate()
    {
        $this->updateContent(arg('content', 'dbsafe'));
        HTTP::redirect(arg('submitURL'));
    }

    /**
     * Отрисовка клиентской части
     *
     * @return  string  Контент
     *
     * @throws Eresus_CMS_Exception_NotFound
     */
    public function clientRenderContent()
    {
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $request = Eresus_CMS::getLegacyKernel()->request;
        /* Если в URL указано что-либо кроме адреса раздела, отправляет ответ 404 */
        if ($request['file'] || $request['query'] ||
            $page->subpage || $page->topic)
        {
            throw new Eresus_CMS_Exception_NotFound;
        }

        return $page->content;
    }

    /**
     * Отрисовка административной части
     *
     * @return  string  Контент
     */
    public function adminRenderContent()
    {
        if (arg('action') == 'update') $this->adminUpdate();
        $item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='".
            Eresus_Kernel::app()->getPage()->id."'");
        $form = array(
            'name' => 'editForm',
            'caption' => Eresus_Kernel::app()->getPage()->title,
            'width' => '100%',
            'fields' => array (
                array ('type'=>'hidden','name'=>'action', 'value' => 'update'),
                array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
            ),
            'buttons' => array('apply', 'reset'),
        );

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = $page->renderForm($form, $item);
        return $result;
    }
}

/**
 * Базовый класс коннектора сторонних расширений
 *
 * @package Eresus
 */
class EresusExtensionConnector
{
    /**
     * Корневой URL расширения
     *
     * @var string
     */
    protected $root;

    /**
     * Корневой путь расширения
     *
     * @var string
     */
    protected $froot;

    /**
     * Конструктор
     *
     * @return EresusExtensionConnector
     */
    function __construct()
    {
        $name = strtolower(substr(get_class($this), 0, -9));
        $this->root = Eresus_CMS::getLegacyKernel()->root.'ext-3rd/'.$name.'/';
        $this->froot = Eresus_CMS::getLegacyKernel()->froot.'ext-3rd/'.$name.'/';
    }
    //-----------------------------------------------------------------------------

    /**
     * Заменяет глобальные макросы
     *
     * @param string $text
     * @return string
     */
    protected function replaceMacros($text)
    {
        $request = Eresus_CMS::getLegacyKernel()->request;
        $text = str_replace(
            array(
                '$(httpHost)',
                '$(httpPath)',
                '$(httpRoot)',
                '$(styleRoot)',
                '$(dataRoot)',
            ),
            array(
                $request['host'],
                $request['path'],
                Eresus_CMS::getLegacyKernel()->root,
                Eresus_CMS::getLegacyKernel()->style,
                Eresus_CMS::getLegacyKernel()->data
            ),
            $text
        );

        return $text;
    }

    /**
     * Метод вызывается при проксировании прямых запросов к расширению
     *
     */
    function proxy()
    {
        if (!UserRights(EDITOR))
            die;

        $filename = Eresus_CMS::getLegacyKernel()->request['path'] .
            Eresus_CMS::getLegacyKernel()->request['file'];
        $filename = Eresus_CMS::getLegacyKernel()->froot . substr($filename,
                strlen(Eresus_CMS::getLegacyKernel()->root));

        if (is_dir($filename))
        {
            $filename = Eresus_FS_Tool::normalize($filename . '/index.php');
        }

        if (!is_file($filename))
        {
            header('Not found', true, 404);
            die('<h1>Not found.</h1>');
        }

        $ext = strtolower(substr($filename, strrpos($filename, '.') + 1));

        switch (true)
        {
            case in_array($ext, array('png', 'jpg', 'jpeg', 'gif')):
                $info = getimagesize($filename);
                header('Content-type: '.$info['mime']);
                echo file_get_contents($filename);
                break;

            case $ext == 'js':
                header('Content-type: text/javascript');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;

            case $ext == 'css':
                header('Content-type: text/css');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;

            case $ext == 'html':
            case $ext == 'htm':
                header('Content-type: text/html');
                $s = file_get_contents($filename);
                $s = $this->replaceMacros($s);
                echo $s;
                break;

            case $ext == 'php':
                Eresus_CMS::getLegacyKernel()->conf['debug']['enable'] = false;
                restore_error_handler();
                chdir(dirname($filename));
                require $filename;
                break;
        }
    }
    //-----------------------------------------------------------------------------
}



/**
 * Класс для работы с расширениями системы
 *
 * @package Eresus
 */
class EresusExtensions
{
    /**
     * Загруженные расширения
     *
     * @var array
     */
    var $items = array();
    /**
     * Определение имени расширения
     *
     * @param string $class     Класс расширения
     * @param string $function  Расширяемая функция
     * @param string $name      Имя расширения
     *
     * @return mixed  Имя расширения или false если подходящего расширения не найдено
     */
    function get_name($class, $function, $name = null)
    {
        $result = false;
        if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions']))
        {
            if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions'][$class]))
            {
                if (isset(Eresus_CMS::getLegacyKernel()->conf['extensions'][$class][$function]))
                {
                    $items = Eresus_CMS::getLegacyKernel()->conf['extensions'][$class][$function];
                    reset($items);
                    $result = isset($items[$name]) ? $name : key($items);
                }
            }
        }

        return $result;
    }
    //-----------------------------------------------------------------------------
    /**
     * Загрузка расширения
     *
     * @param string $class     Класс расширения
     * @param string $function  Расширяемая функция
     * @param string $name      Имя расширения
     *
     * @return mixed  Экземпляр класса EresusExtensionConnector или false если не удалось загрузить расширение
     */
    function load($class, $function, $name = null)
    {
        $result = false;
        $name = $this->get_name($class, $function, $name);

        if (isset($this->items[$name]))
        {
            $result = $this->items[$name];
        }
        else
        {
            $filename = Eresus_CMS::getLegacyKernel()->froot.'ext-3rd/' . $name .
                '/eresus-connector.php';
            if (is_file($filename))
            {
                include_once $filename;
                $class = $name.'Connector';
                if (class_exists($class))
                {
                    $this->items[$name] = new $class();
                    $result = $this->items[$name];
                }
            }
        }
        return $result;
    }
    //-----------------------------------------------------------------------------
}

