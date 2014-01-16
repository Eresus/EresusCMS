<?php
/**
 * Реестр модулей расширения
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <m.krasilnikov@yandex.ru>
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
 * Реестр модулей расширения
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Plugin_Registry
{
    /**
     * Список всех активированных плагинов
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать private
     */
    public $list = array();

    /**
     * Массив плагинов
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать private
     */
    public $items = array();

    /**
     * Таблица обработчиков событий
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать private
     */
    public $events = array();

    /**
     * Экземпляр-одиночка
     * @var Eresus_Plugin_Registry
     * @since 3.01
     */
    private static $instance = null;

    /**
     * Возвращает экземпляр-одиночку
     *
     * @return Eresus_Plugin_Registry
     */
    public static function getInstance()
    {
        if (null === self::$instance)
        {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * @deprecated с 3.01 используйте {@link getInstance()}
     */
    public function __construct()
    {
        spl_autoload_register(array($this, 'autoload'));

        $this->registerBcEventListeners();
        $items = Eresus_CMS::getLegacyKernel()->db->select('plugins', 'active = 1');
        if ($items)
        {
            foreach ($items as &$item)
            {
                $item['info'] = unserialize($item['info']);
                $this->list[$item['name']] = $item;
            }

            /* Проверяем зависимости */
            do
            {
                $success = true;
                foreach ($this->list as $plugin => $item)
                {
                    if (!($item['info'] instanceof Eresus_PluginInfo))
                    {
                        continue;
                    }
                    foreach ($item['info']->getRequiredPlugins() as $required)
                    {
                        list ($name, $minVer, $maxVer) = $required;
                        if (
                            !isset($this->list[$name]) ||
                            ($minVer && version_compare($this->list[$name]['info']->version, $minVer, '<')) ||
                            ($maxVer && version_compare($this->list[$name]['info']->version, $maxVer, '>'))
                        )
                        {
                            $msg = 'Plugin "%s" requires plugin %s';
                            $requiredPlugin = $name . ' ' . $minVer . '-' . $maxVer;
                            Eresus_Kernel::log(__CLASS__, LOG_ERR, $msg, $plugin, $requiredPlugin);
                            /*$msg = I18n::getInstance()->getText($msg, $this);
                            ErrorMessage(sprintf($msg, $plugin, $requiredPlugin));*/
                            unset($this->list[$plugin]);
                            $success = false;
                        }
                    }
                }
            }
            while (!$success);
        }
    }

    /**
     * Загружает активные плагины
     *
     * @return void
     *
     * @since 2.16
     */
    public function init()
    {
        /* Загружаем плагины */
        foreach ($this->list as $item)
        {
            $this->load($item['name']);
        }
    }

    /**
     * Устанавливает плагин
     *
     * @param string $name  Имя плагина
     *
     * @return void
     */
    public function install($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s")', $name);

        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $filename = $legacyKernel->froot . 'ext/'.$name.'.php';
        if (file_exists($filename))
        {
            $info = Eresus_PluginInfo::loadFromFile($filename);
            /*
             * Подключаем плагин через eval чтобы убедиться в отсутствии фатальных синтаксических
             * ошибок. Хотя и не факт, что это сработает.
             */
            $code = file_get_contents($filename);
            $code = preg_replace('/^\s*<\?php|\?>\s*$/m', '', $code);
            $code = str_replace('__FILE__', "'$filename'", $code);
            ini_set('track_errors', true);
            $valid = eval($code) !== false;
            ini_set('track_errors', false);
            if (!$valid)
            {
                Eresus_Kernel::app()->getPage()->addErrorMessage(
                    sprintf('Plugin "%s" is broken: %s', $name, $php_errormsg));
                return;
            }

            $className = $name;
            if (!class_exists($className, false) && class_exists('T' . $className, false))
            {
                $className = 'T' . $className; // FIXME: Обратная совместимость с версиями до 2.10b2
            }

            if (class_exists($className, false))
            {
                /** @var Eresus_Plugin $plugin */
                $plugin = new $className();
                $this->items[$name] = $plugin;
                $plugin->install();
                $item = $plugin->__item();
                $item['info'] = serialize($info);
                Eresus_CMS::getLegacyKernel()->db->insert('plugins', $item);
            }
            else
            {
                Eresus_Kernel::app()->getPage()->addErrorMessage(
                    sprintf(errClassNotFound, $className));
            }
        }
        else
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"',
                $filename, $name);
            $msg = I18n::getInstance()->getText('Can not find main file "%s" for plugin "%s"', __CLASS__);
            $msg = sprintf($msg, $filename, $name);
            Eresus_Kernel::app()->getPage()->addErrorMessage($msg);
        }
    }

    /**
     * Деинсталлирует плагин
     *
     * @param string $name  Имя плагина
     */
    public function uninstall($name)
    {
        if (!isset($this->items[$name]))
        {
            $this->load($name);
        }
        if (isset($this->items[$name]))
        {
            /** @var Eresus_Plugin $plugin */
            $plugin = $this->items[$name];
            $plugin->uninstall();
        }
        $item = Eresus_CMS::getLegacyKernel()->db->selectItem('plugins', "`name`='".$name."'");
        if (!is_null($item))
        {
            Eresus_CMS::getLegacyKernel()->db->delete('plugins', "`name`='".$name."'");
        }
        //$filename = filesRoot.'ext/'.$name.'.php';
        //if (file_exists($filename)) unlink($filename);
    }

    /**
     * Загружает плагин и возвращает его экземпляр
     *
     * Метод пытается загрузить плагин с именем $name (если он не был загружен ранее). В случае успеха
     * создаётся и возвращается экземпляр основного класса плагина (либо экземпляр, созданный ранее).
     *
     * @param string $name  Имя плагина
     *
     * @return Eresus_Plugin|bool  Экземпляр плагина или false если не удалось загрузить плагин
     *
     * @since 2.10
     */
    public function load($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s")', $name);
        /* Если плагин уже был загружен возвращаем экземпляр из реестра */
        if (isset($this->items[$name]))
        {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" already loaded', $name);
            return $this->items[$name];
        }

        /* Если такой плагин не зарегистрирован, возвращаем FASLE */
        if (!isset($this->list[$name]))
        {
            Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" not registered', $name);
            return false;
        }

        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        // Путь к файлу плагина
        $filename = $legacyKernel->froot . 'ext/' . $name . '.php';

        /* Если такого файла нет, возвращаем false */
        if (!file_exists($filename))
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Can not find main file "%s" for plugin "%s"',
                $filename, $name);
            return false;
        }

        include_once $filename;
        $className = $name;

        /* TODO: Обратная совместимость с версиями до 2.10b2. Отказаться в новых версиях */
        if (!class_exists($className, false) && class_exists('T' . $className))
        {
            $className = 'T' . $className;
        }

        if (!class_exists($className, false))
        {
            Eresus_Kernel::log(__METHOD__, LOG_ERR, 'Main class %s for plugin "%s" not found in "%s"',
                $className, $name, $filename);
            Eresus_Kernel::app()->getPage()->addErrorMessage(sprintf(errClassNotFound, $name));
            return false;
        }

        // Заносим экземпляр в реестр
        $this->items[$name] = new $className();
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Plugin "%s" loaded', $name);

        return $this->items[$name];
    }

    /**
     * Возвращает список активных модулей расширения
     *
     * Возвращает массив, где ключами являются имена модулей, а значениями экземпляры их основных
     * классов.
     *
     * @return Eresus_Plugin[]  массив экземпляров основных классов модулей
     *
     * @since 3.01
     */
    public function getEnabled()
    {
        return $this->items;
    }

    /**
     * Отрисовка контента раздела
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string  Контент
     */
    public function clientRenderContent(Eresus_CMS_Request $request)
    {
        /* @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = '';
        switch ($page->type)
        {
            case 'default':
                $controller = new Eresus_Client_Controller_Content_Default();
                break;
            case 'list':
                $controller = new Eresus_Client_Controller_Content_List();
                break;
            case 'url':
                $controller = new Eresus_Client_Controller_Content_Url();
                break;
            default:
                $plugin = $this->load($page->type);
                if (false === $plugin)
                {
                    $page->addErrorMessage(sprintf(errContentPluginNotFound, $page->type));
                    return '';
                }
                if ($plugin instanceof ContentPlugin || $plugin instanceof TContentPlugin)
                {
                    /** @var ContentPlugin $plugin */
                    $result = $plugin->clientRenderContent($page, $request);
                }
                else
                {
                    Eresus_Kernel::log(__METHOD__, LOG_ERR,
                        sprintf('Class "%s" must be descendant of ContentPlugin or TContentPlugin',
                            get_class($plugin)));
                    $page->addErrorMessage('Internal error');
                }
        }
        if (isset($controller)
            && $controller instanceof Eresus_Client_Controller_Content_Interface)
        {
            $result = $controller->getHtml($request, $page);
        }
        return $result;
    }

    /**
     * @deprecated с 3.01
     */
    public function clientOnStart()
    {
        if (isset($this->events['clientOnStart']))
        {
            foreach ($this->events['clientOnStart'] as $plugin)
            {
                $this->items[$plugin]->clientOnStart();
            }
        }
    }

    /**
     * @param Eresus_Event_UrlSectionFound $event
     *
     * @deprecated с 3.01
     */
    public function clientOnURLSplit($event)
    {
        if (!($event instanceof Eresus_Event_UrlSectionFound))
        {
            return;
        }
        if (isset($this->events['clientOnURLSplit']))
        {
            foreach ($this->events['clientOnURLSplit'] as $plugin)
            {
                $this->items[$plugin]->clientOnURLSplit($event->getSectionInfo(), $event->getUrl());
            }
        }
    }

    /**
     * @param Eresus_Event_Render $event
     * @deprecated с 3.01
     */
    public function clientOnContentRender($event)
    {
        if (!($event instanceof Eresus_Event_Render))
        {
            return;
        }
        if (isset($this->events['clientOnContentRender']))
        {
            foreach ($this->events['clientOnContentRender'] as $plugin)
            {
                $event->setText($this->items[$plugin]->clientOnContentRender($event->getText()));
            }
        }
    }

    /**
     * @param Eresus_Event_Render $event
     *
     * @deprecated с 3.01
     */
    public function clientOnPageRender($event)
    {
        if (!($event instanceof Eresus_Event_Render))
        {
            return;
        }
        if (isset($this->events['clientOnPageRender']))
        {
            foreach ($this->events['clientOnPageRender'] as $plugin)
            {
                $event->setText($this->items[$plugin]->clientOnPageRender($event->getText()));
            }
        }
    }

    /**
     * @param Eresus_Event_Response $event
     * @deprecated с 3.01
     */
    public function clientBeforeSend($event)
    {
        if (!($event instanceof Eresus_Event_Response))
        {
            return;
        }
        if (isset($this->events['clientBeforeSend']))
        {
            foreach ($this->events['clientBeforeSend'] as $plugin)
            {
                $event->getResponse()
                    ->setContent($this->items[$plugin]
                        ->clientBeforeSend($event->getResponse()->getContent()));
            }
        }
    }

    /**
     * @param Eresus_Event $event
     * @deprecated с 3.01
     */
    public function adminOnMenuRender($event)
    {
        if (!($event instanceof Eresus_Event))
        {
            return;
        }
        if (isset($this->events['adminOnMenuRender']))
        {
            foreach ($this->events['adminOnMenuRender'] as $plugin)
            {
                if (method_exists($this->items[$plugin], 'adminOnMenuRender'))
                {
                    $this->items[$plugin]->adminOnMenuRender();
                }
                else
                {
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errMethodNotFound, 'adminOnMenuRender', $plugin));
                }
            }
        }
    }

    /**
     * Автозагрузка классов плагинов
     *
     * @param string $className
     *
     * @return boolean
     *
     * @since 3.00
     */
    public function autoload($className)
    {
        $pluginName = strtolower(substr($className, 0, strpos($className, '_')));

        if ($this->load($pluginName))
        {
            $filename = Eresus_Kernel::app()->getFsRoot() . '/ext/' . $pluginName . '/classes/' .
                str_replace('_', '/', substr($className, strlen($pluginName) + 1)) . '.php';
            if (file_exists($filename))
            {
                /** @noinspection PhpIncludeInspection */
                include $filename;
                return Eresus_Kernel::classExists($className);
            }
        }

        return false;
    }

    /**
     * Регистрирует старые методы обработки событий
     *
     * @since 3.01
     */
    private function registerBcEventListeners()
    {
        $ed = Eresus_Kernel::app()->getEventDispatcher();

        $ed->addListener('cms.admin.start', array($this, 'adminOnMenuRender'));

        $ed->addListener('cms.client.start', array($this, 'clientOnStart'));
        $ed->addListener('cms.client.url_section_found', array($this, 'clientOnURLSplit'));
        $ed->addListener('cms.client.render_content', array($this, 'clientOnContentRender'));
        $ed->addListener('cms.client.render_page', array($this, 'clientOnPageRender'));
        $ed->addListener('cms.client.response', array($this, 'clientBeforeSend'));
    }
}

