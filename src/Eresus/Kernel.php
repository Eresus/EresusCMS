<?php
/**
 * Ядро
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
 */

namespace Eresus;

use Bedoved;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Yaml\Yaml;
use Eresus_Kernel;
use Eresus_CMS_Request;
use TemplateSettings;
use I18n;
use Eresus_DB;
use ezcDbOptions;

/**
 * Ядро
 *
 * Ядро обеспечивает:
 *
 * 1. начальную инициализацию CMS;
 * 2. создание контейнера зависимостей;
 * 3. создание объекта сайта и передачу ему управления.
 *
 * @since x.xx
 */
class Kernel
{
    /**
     * Версия CMS
     * @since x.xx
     */
    const VERSION = '${product.version}';

    /**
     * Режим отладки
     *
     * @var bool
     *
     * @since x.xx
     */
    private $debug = false;

    /**
     * Обработчик ошибок
     *
     * @var Bedoved
     * @since x.xx
     */
    private $bedoved;

    /**
     * Папка приложения
     *
     * @var string
     *
     * @since x.xx
     */
    private $appDir;

    /**
     * Контейнер служб
     * @var ContainerBuilder
     * @since x.xx
     * @deprecated сделать приватным по окончанию рефакторинга
     */
    public $container;

    /**
     * Описание сайта
     * @var Site
     * @since x.xx
     */
    private $site;

    /**
     * Инициализирует ядро
     *
     * @param string $appDir  папка приложения
     *
     * @since x.xx
     */
    public function __construct($appDir)
    {
        $this->appDir = $appDir;
        //TODO session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
        session_name('sid');
        session_start();
    }

    /**
     * Эмуляция устаревших свойств
     *
     * @param string $property
     *
     * @return mixed
     *
     * @since x.xx
     * @deprecated с x.xx
     */
    public function __get($property)
    {
        switch ($property)
        {
            case 'name':
                trigger_error('Use of ' . __CLASS__ . '::$name is deprecated', E_USER_DEPRECATED);
                return 'Eresus';
            case 'version':
                trigger_error('Use of ' . __CLASS__ . '::$version is deprecated',
                    E_USER_DEPRECATED);
                return self::VERSION;
        }
        return null;
    }

    /**
     * Включает или отключает режим отладки
     *
     * @param bool $state
     *
     * @since x.xx
     */
    public function setDebug($state)
    {
        $this->debug = $state;
    }

    /**
     * Запускает CMS
     *
     * @since x.xx
     */
    public function start()
    {
        $this->initErrorHandling();

        $cmsRequest = $this->initRequest();
        $this->initContainer($cmsRequest);
        $this->initLegacyKernel();
        $this->initEventListeners();
        $i18n = I18n::getInstance();
        TemplateSettings::setGlobalValue('i18n', $i18n);

        $this->run($cmsRequest);
    }

    /**
     * Возвращает контейнер
     *
     * @return ContainerBuilder
     *
     * @since x.xx
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Возвращает папку приложения
     *
     * @return string
     *
     * @since x.xx
     */
    public function getAppDir()
    {
        return $this->appDir;
    }

    /**
     * Возвращает путь к папке кэша
     *
     * @return string
     * @since x.xx
     */
    public function getCacheDir()
    {
        return $this->getAppDir() . '/var/cache';
    }

    /**
     * Возвращает экземпляр класса Eresus
     *
     * Метод нужен до отказа от класса Eresus
     *
     * @return \Eresus
     *
     * @since x.xx
     */
    public static function getLegacyKernel()
    {
        return $GLOBALS['Eresus'];
    }

    /**
     * Завершение обработки запроса
     *
     * @since x.xx
     */
    public function onShutdown()
    {
        /* TODO * @var \Doctrine\ORM\EntityManager $om * /
        $om = $this->container->get('doctrine')->getManager();
        $om->flush();*/
    }

    /**
     * Возвращает диспетчер событий CMS
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     *
     * @deprecated с x.xx используйте службу «events» из контейнера зависимостей
     */
    public function getEventDispatcher()
    {
        return $this->container->get('events');
    }

    /**
     * Возвращает экземпляр класса TClientUI или TAdminUI
     *
     * Метод нужен до отказа от переменной $page
     *
     * @return \WebPage
     *
     * @since 3.00
     * @deprecated с x.xx
     */
    public function getPage()
    {
        return $this->site->controller->getPage();
    }

    /**
     * Get application root directory
     *
     * @return string
     * @deprecated с x.xx используйте {@link getAppDir()}
     */
    public function getFsRoot()
    {
        return $this->getAppDir();
    }

    /**
     * Инициализация обработки ошибок
     *
     * @since x.xx
     */
    private function initErrorHandling()
    {
        $this->bedoved = new Bedoved($this->debug);
        $this->bedoved
            ->enableErrorConversion(/*E_ALL ^ (E_NOTICE | E_USER_NOTICE | E_USER_DEPRECATED)*/)
            ->enableExceptionHandling()
            ->enableFatalErrorHandling();
    }

    /**
     * Создаёт объект обрабатываемого запроса
     *
     * @return Eresus_CMS_Request
     *
     * @since x.xx
     */
    private function initRequest()
    {
        $request = Request::createFromGlobals();
        $cmsRequest = new Eresus_CMS_Request($request);
        return $cmsRequest;
    }

    /**
     * Создаёт контейнер служб
     *
     * @param Request $request
     *
     * @since x.xx
     */
    private function initContainer(Request $request)
    {
        $this->container = new ContainerBuilder();

        $this->container->setParameter('debug', $this->debug);
        $this->container->setParameter('request', $request);
        //$this->container->setParameter('security.session.ttl', 30); // в минутах
        //$this->container->setParameter('admin.theme', 'default');

        $this->container->set('container', $this->container);
        $this->container->set('kernel', $this);

        $this->container
            ->register('events', 'Symfony\Component\EventDispatcher\EventDispatcher');

        /*$this->container
            ->register('doctrine', 'Eresus\ORM\Registry')
            ->addArgument(new Reference('container'));*/

        /*$this->container
            ->register('doctrine.driver_chain',
                'Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain');*/

        /*$this->container
            ->register('security', 'Eresus\Security\SecurityManager')
            ->addArgument(new Reference('container'));*/

        $this->container
            ->register('plugins', 'Eresus_Plugin_Registry')
            ->addArgument(new Reference('container'));

        /*$this->container
            ->register('plugins', 'Eresus\Plugins\PluginManager')
            ->addArgument(new Reference('container'));

        $this->container
            ->register('accounts', 'Eresus\Security\AccountManager')
            ->addArgument(new Reference('container'));

        $this->container
            ->register('sections', 'Eresus\Sections\SectionManager')
            ->addArgument(new Reference('container'));

        $this->container
            ->register('templates.dwoo', 'Dwoo')
            ->setPublic(false)
            ->addArgument($this->getCacheDir() . '/templates');

        $this->container
            ->register('i18n', 'I18n')
            ->setFactoryClass('I18n')
            ->setFactoryMethod('getInstance');

        $this->container
            ->register('templates', 'Eresus\Templating\TemplateManager')
            ->addArgument(new Reference('container'))
            ->addArgument(new Reference('templates.dwoo'))
            ->addMethodCall('setGlobal', array('site', '%site%'))
            ->addMethodCall('setGlobal', array('i18n', new Reference('i18n')))
            ->addMethodCall('setGlobal', array('theme', new Reference('admin.theme')));

        $this->container
            ->register('admin.theme', 'Eresus\Templating\AdminTheme')
            ->addArgument(new Reference('container'))
            ->addArgument('%admin.theme%');*/

        // TODO Удалить после удаления устаревших компонентов
        $GLOBALS['_container'] = $this->container;
    }

    /**
     * Устанавливает обработчики событий
     *
     * @since x.xx
     */
    private function initEventListeners()
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $evd */
        $evd = $this->container->get('events');
        $evd->addListener('cms.shutdown', array($this, 'onShutdown'));
    }

    /**
     * Подключение старого ядра
     *
     * @since x.xx
     */
    private function initLegacyKernel()
    {
        include_once $this->getAppDir() . '/core/kernel-legacy.php';

        $legacyKernel = new \Eresus($this->container);
        /**
         * @global \Eresus Eresus
         * @todo Обратная совместимость — удалить
         * @deprecated с 3.00 используйте Eresus_Kernel::app()->getLegacyKernel()
         */
        $GLOBALS['Eresus'] = $legacyKernel;
        $this->initConf();
        $this->initDB();
        $legacyKernel->init();
        TemplateSettings::setGlobalValue('Eresus', $legacyKernel);
    }

    /**
     * Инициализация конфигурации
     *
     * @throws \RuntimeException
     *
     * @since x.xx
     */
    private function initConf()
    {
        $filename = $this->getAppDir() . '/cfg/global.yml';
        if (!file_exists($filename))
        {
            throw new \RuntimeException('File cfg/global.yml not found!');
        }
        $conf = Yaml::parse($filename);
        if (!array_key_exists('parameters', $conf))
        {
            $conf['parameters'] = array();
        }

        $filename = $this->getAppDir() . '/cfg/local.yml';
        if (!file_exists($filename))
        {
            throw new \RuntimeException('File cfg/local.yml not found!');
        }
        $locals = Yaml::parse($filename);
        if (!array_key_exists('parameters', $locals))
        {
            $locals['parameters'] = array();
        }
        $conf = array_merge($conf, $locals);

        $params = $conf['parameters'];

        if (array_key_exists('debug', $params))
        {
            $this->setDebug($params['debug']);
        }

        /*
         * Драйвер СУБД
         */
        if (!array_key_exists('database_driver', $params) || '~' == $params['database_driver'])
        {
            $params['database_driver'] = 'pdo_sqlite';
        }
        $this->container->setParameter('db.driver', strtolower($params['database_driver']));

        /*
         * Хост сервера БД
         */
        if (!array_key_exists('database_host', $params) || '~' == $params['database_host'])
        {
            if (in_array($this->container->getParameter('db.driver'),
                array('pdo_ibm', 'pdo_informix', 'pdo_mysql', 'pdo_sqlsrv', 'pdo_pgsql')))
            {
                $params['database_host'] = 'localhost';
            }
            else
            {
                $params['database_host'] = '';
            }
        }
        $this->container->setParameter('db.host', $params['database_host']);

        $emptyByDefaultList
            = array('database_port', 'database_user', 'database_password', 'database_prefix');
        foreach ($emptyByDefaultList as $property)
        {
            if (!array_key_exists($property, $params) || '~' == $params[$property])
            {
                $params[$property] = '';
            }
        }

        $this->container->setParameter('db.port', $params['database_port']);
        $this->container->setParameter('db.username', $params['database_user']);
        $this->container->setParameter('db.password', $params['database_password']);
        $this->container->setParameter('db.dbname', $params['database_name']);
        $this->container->setParameter('db.prefix', $params['database_prefix']);


        if (array_key_exists('timezone', $params))
        {
            date_default_timezone_set($params['timezone']);
        }

        if (array_key_exists('locale', $params))
        {
            $this->container->setParameter('locale', $params['locale']);
        }

        if (array_key_exists('session_timeout', $params))
        {
            $this->container->setParameter('security.session.timeout', $params['session_timeout']);
        }
    }

    /**
     * Устанавливает соединение с БД
     *
     * @since x.xx
     */
    private function initDB()
    {
        $driver = substr($this->container->getParameter('db.driver'), strlen('pdo_'));
        $host = $this->container->getParameter('db.host');
        $port = $this->container->getParameter('db.port');
        $username = $this->container->getParameter('db.username');
        $password = $this->container->getParameter('db.password');
        $name = $this->container->getParameter('db.dbname');
        $prefix = $this->container->getParameter('db.prefix');

        $dsn = $driver . '://';
        if ($username)
        {
            $dsn .= $username;
            if ($password)
            {
                $dsn .= ':' . $password;
            }
            $dsn .= '@';
        }

        if ($host)
        {
            $dsn .= $host;
            if ($port)
            {
                $dsn .= ':' . $port;
            }
        }

        if ($name)
        {
            $dsn .= '/' . $name;
        }

        if ('mysql' == $driver)
        {
            $dsn .= '?charset=utf8';
        }

        $db = Eresus_DB::connect($dsn);
        $options = array();
        if ($prefix)
        {
            $options['tableNamePrefix'] = $prefix;
        }
        $options = new ezcDbOptions();
        $db->setOptions($options);
    }

    /**
     * Выполняет приложение
     *
     * @param Request $request
     *
     * @since x.xx
     */
    private function run(Request $request)
    {
        $this->site = new Site($this, $request);
        $this->container->setParameter('site', $this->site);
        TemplateSettings::setGlobalValue('site', $this->site);
        TemplateSettings::setGlobalValue('cms', $this);

        $response = $this->site->handleRequest($request);
        $response->send();

        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $evd */
        $evd = $this->container->get('events');
        $evd->dispatch('cms.shutdown');
    }
}

