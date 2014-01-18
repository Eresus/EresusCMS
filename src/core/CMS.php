<?php
/**
 * Класс приложения Eresus CMS
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Класс приложения Eresus CMS
 *
 * @property-read string $version  версия CMS
 *
 * @package Eresus
 */
class Eresus_CMS extends Eresus_Application
{
    /**
     * Название CMS
     * @var string
     * @since 3.01
     */
    private $name = 'Eresus';
    /**
     * Версия CMS
     * @var string
     * @since 3.01
     */
    private $version = '${product.version}';

    /**
     * Описание сайта
     * @var Eresus_Site
     * @since 3.01
     */
    private $site;

    /**
     * Контейнер служб
     * @var ContainerBuilder
     * @since 3.02
     * @internal
     */
    public $container; // TODO Сделать приватным после удаления Eresus_Plugin_Registry::getInstance

    /**
     * Объект создаваемой страницы
     * @var WebPage
     * @since 3.00
     */
    protected $page;

    /**
     * Инициализация
     *
     * @since 3.01
     */
    public function __construct()
    {
        parent::__construct();

        $this->container = new ContainerBuilder();
        $this->container
            ->register('container', $this->container);
        $this->container
            ->register('events', 'Symfony\Component\EventDispatcher\EventDispatcher');
        $this->container
            ->register('plugins', 'Eresus_Plugin_Registry')
            ->addArgument(new Reference('container'));
    }

    /**
     * Основной метод приложения
     *
     * @return int  Код завершения для консольных вызовов
     *
     * @see EresusApplication::main()
     */
    public function main()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'starting...');

        /* Общая инициализация */
        $this->checkEnvironment();
        $this->createFileStructure();

        /* Подключение старого ядра */
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'Init legacy kernel');
        include_once 'kernel-legacy.php';

        /**
         * @global Eresus Eresus
         * @todo Обратная совместимость — удалить
         * @deprecated с 3.01 используйте Eresus_Kernel::app()->getLegacyKernel()
         */
        $GLOBALS['Eresus'] = new Eresus($this->container);

        TemplateSettings::setGlobalValue('cms', $this);

        $this->initConf();
        $i18n = I18n::getInstance();
        TemplateSettings::setGlobalValue('i18n', $i18n);
        Eresus_CMS::getLegacyKernel()->init();
        TemplateSettings::setGlobalValue('Eresus', Eresus_CMS::getLegacyKernel());
        $this->runWeb();
        return 0;
    }

    /**
     * Магический метод для обеспечения доступа к свойствам только на чтение
     *
     * @param string $property
     * @return mixed
     * @throws LogicException  если свойства $property нет
     */
    public function __get($property)
    {
        if (property_exists($this, $property))
        {
            return $this->{$property};
        }
        throw new LogicException(sprintf('Trying to access unknown property %s of %s',
            $property, __CLASS__));
    }

    /**
     * Возвращает диспетчер событий CMS
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     *
     * @since 3.01
     * @deprecated с 3.02 используйте службу «events» из контейнера зависимостей
     */
    public function getEventDispatcher()
    {
        return $this->container->get('events');
    }

    /**
     * Выводит сообщение о фатальной ошибке и прекращает работу приложения
     *
     * @param Exception|string $error  исключение или описание ошибки
     * @param bool             $exit   завершить или нет выполнение приложения
     *
     * @return void
     *
     * @since 2.16
     * @deprecated с 3.02, вбрасывайте исключения
     */
    public function fatalError(/** @noinspection PhpUnusedParameterInspection */
        $error = null, $exit = true)
    {
        include dirname(__FILE__) . '/fatal.html.php';
        die;
    }

    /**
     * Возвращает экземпляр класса Eresus
     *
     * Метод нужен до отказа от класса Eresus
     *
     * @return Eresus
     *
     * @since 3.00
     */
    public static function getLegacyKernel()
    {
        return $GLOBALS['Eresus'];
    }

    /**
     * Возвращает экземпляр класса Eresus_Site
     *
     * @return Eresus_Site
     *
     * @since 3.01
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Возвращает экземпляр класса TClientUI или TAdminUI
     *
     * Метод нужен до отказа от переменной $page
     *
     * @return WebPage
     *
     * @since 3.00
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Проверка окружения
     *
     * @return void
     */
    protected function checkEnvironment()
    {
        $errors = array();

        /* Проверяем наличие нужных файлов */
        $required = array('cfg/main.php');
        foreach ($required as $filename)
        {
            if (!file_exists($filename))
            {
                $errors []= array('file' => $filename, 'problem' => 'missing');
            }
        }

        /* Проверяем доступность для записи */
        $writable = array(
            'cfg/settings.php',
            'var',
            'data',
            'templates',
            'style'
        );
        foreach ($writable as $filename)
        {
            if (!is_writable($filename))
            {
                $errors []= array('file' => $filename, 'problem' => 'non-writable');
            }
        }

        if ($errors)
        {
            if (!Eresus_Kernel::isCLI())
            {
                require_once 'errors.html.php';
            }
            else
            {
                die("Errors...\n"); // TODO Доделать
            }
        }
    }

    /**
     * Создание файловой структуры
     *
     * @return void
     */
    protected function createFileStructure()
    {
        $dirs = array(
            '/var/log',
            '/var/cache',
            '/var/cache/templates',
        );

        foreach ($dirs as $dir)
        {
            if (!file_exists($this->getFsRoot() . $dir))
            {
                $umask = umask(0000);
                mkdir($this->getFsRoot() . $dir, 0777);
                umask($umask);
            }
            // TODO Сделать проверку на запись в созданные директории
        }
    }

    /**
     * Выполнение в режиме Web
     */
    protected function runWeb()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        $request = new Eresus_CMS_Request(Eresus_HTTP_Request::createFromGlobals());
        $request->setSiteRoot($this->detectWebRoot());

        // TODO Удалить где-нибудь в 3.03-04
        TemplateSettings::setGlobalValue('siteRoot', $request->getSiteRoot());

        $this->initSite();

        if (substr($request->getPath(), 0, 8) == '/ext-3rd')
        {
            $this->call3rdPartyExtension($request);
        }
        else
        {
            if (substr($request->getPath(), 0, 7) == '/admin/'
                || $request->getPath() == '/admin'
                || $request->getPath() == '/admin.php')
            {
                $controller = new Eresus_Admin_FrontController($this->container, $request);
            }
            else
            {
                $controller = new Eresus_Client_FrontController($this->container, $request);
            }
            $this->page = $controller->getPage();
            /**
             * @global
             * @deprecated с 3.01 используйте Eresus_Kernel::app()->getPage()
             */
            $GLOBALS['page'] = $this->page;
            TemplateSettings::setGlobalValue('page', $this->page);
            $response = $controller->dispatch();
            $response->send();
        }
    }

    /**
     * Определяет и возвращает корневой адрес сайта
     *
     * @return string
     */
    protected function detectWebRoot()
    {
        $webServer = WebServer::getInstance();
        $documentRoot = $webServer->getDocumentRoot();
        $suffix = $this->getFsRoot();
        $suffix = substr($suffix, strlen($documentRoot));
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'detected root: %s', $suffix);
        return $suffix;
    }

    /**
     * Инициализация конфигурации
     */
    protected function initConf()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        /*
         * Переменную $Eresus приходится делать глобальной, чтобы файл конфигурации
         * мог записывать в неё свои значения.
         * TODO Избавиться от глобальной переменной
         */
        /** @noinspection PhpUnusedLocalVariableInspection */
        global $Eresus;

        $filename = $this->getFsRoot() . '/cfg/main.php';
        if (file_exists($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include $filename;
            // TODO: Сделать проверку успешного подключения файла
        }
        else
        {
            $this->fatalError("Main config file '$filename' not found!");
        }
    }

    /**
     * Обрабатывает запрос к стороннему расширению
     *
     * Вызов производится через коннектор этого расширения
     *
     * @param Eresus_CMS_Request $request
     *
     * @return void
     */
    protected function call3rdPartyExtension(Eresus_CMS_Request $request)
    {
        $extension = substr(dirname($request->getPath()), 9);
        if (($p = strpos($extension, '/')) !== false)
        {
            $extension = substr($extension, 0, $p);
        }

        $filename = $this->getFsRoot() . '/ext-3rd/' . $extension . '/eresus-connector.php';
        if ($extension && is_file($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
            $className = $extension . 'Connector';
            /** @var EresusExtensionConnector $connector */
            $connector = new $className;
            $connector->proxy();
        }
        else
        {
            header('404 Not Found', true, 404);
            echo '404 Not Found';
        }
    }

    /**
     * Инициализирует сайт
     *
     * @return void
     *
     * @since 3.01
     */
    private function initSite()
    {
        $this->site = new Eresus_Site($this->getLegacyKernel());
        $this->site->setTitle(siteTitle);
        $this->site->setDescription(siteDescription);
        $this->site->setKeywords(siteKeywords);
        TemplateSettings::setGlobalValue('site', $this->site);
    }
}
