<?php
/**
 * Сайт
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

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Сайт
 *
 * <b>Внимание!</b> Не создавайте экземпляров этого класса самостоятельно!
 *
 * @since x.xx
 */
class Site
{
    /**
     * Контейнер служб
     *
     * @var ContainerInterface
     *
     * @since x.xx
     */
    private $container;

    /**
     * Имя сайта
     *
     * @var string
     *
     * @since x.xx
     */
    private $name = '';

    /**
     * URL корня сайта
     * @var string
     * @since x.xx
     */
    private $rootUrl = '';

    /**
     * URL папки стиля
     * @var string
     * @since x.xx
     */
    private $stylesUrl = '/styles';

    /**
     * Хост сайта
     * @var string
     * @since x.xx
     */
    private $domain = 'localhost';

    /**
     * Заголовок сайта
     * @var string
     * @since x.xx
     */
    private $title = '';

    /**
     * Описание сайта
     * @var string
     * @since x.xx
     */
    private $description = '';

    /**
     * Ключевые слова сайта
     * @var string
     * @since x.xx
     */
    private $keywords = '';

    /**
     * @var \Eresus_CMS_FrontController
     * @internal
     * TODO Удалить по окончанию рефакторинга
     */
    public $controller;

    /**
     * Создаёт описание сайта
     *
     * @param Kernel  $kernel
     * @param Request $request
     */
    public function __construct(Kernel $kernel, Request $request)
    {
        $this->container = $kernel->getContainer();
        $this->rootUrl = $request->getSchemeAndHttpHost() . $request->getBasePath();
        $this->stylesUrl = $this->rootUrl . '/style';
        $this->domain = $request->getHttpHost();

        include $kernel->getAppDir() . '/cfg/settings.php';
        $this->name = siteName;
        $this->title = siteTitle;
        $this->description = siteDescription;
        $this->keywords = siteKeywords;
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
            case 'webRoot':
                trigger_error('Use of ' . __CLASS__ . '::$webRoot is deprecated',
                    E_USER_DEPRECATED);
                return $this->getRootUrl();
        }
        return null;
    }

    /**
     * Возвращает URL корня сайта
     *
     * @return string
     *
     * @since x.xx
     */
    public function getRootUrl()
    {
        return $this->rootUrl;
    }

    /**
     * Возвращает URL папки стилей
     *
     * @return string
     *
     * @since x.xx
     */
    public function getStylesUrl()
    {
        return $this->stylesUrl;
    }

    /**
     * Возвращает доменное имя
     *
     * @return string
     *
     * @since x.xx
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Возвращает название
     *
     * @return string
     *
     * @since x.xx
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Задаёт название
     *
     * @param string $name
     *
     * @throws \Eresus_Exception_InvalidArgumentType
     * @since x.xx
     */
    public function setName($name)
    {
        if (!is_string($name))
        {
            throw \Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'string', $name);
        }
        $this->name = $name;
    }

    /**
     * Возвращает название
     *
     * @return string
     *
     * @since x.xx
     * @todo Вынести в отдельный класс
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Задаёт заголовок сайта
     *
     * @param string $title  новый заголовок
     *
     * @throws \Eresus_Exception_InvalidArgumentType
     * @since x.xx
     * @todo Вынести в отдельный класс
     */
    public function setTitle($title)
    {
        if (!is_string($title))
        {
            throw \Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'string', $title);
        }
        $this->title = $title;
    }

    /**
     * Возвращает описание
     *
     * @return string
     *
     * @since x.xx
     * @todo Вынести в отдельный класс
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Задаёт описание сайта
     *
     * @param string $description  новое описание
     *
     * @throws \Eresus_Exception_InvalidArgumentType
     *
     * @since x.xx
     * @todo Вынести в отдельный класс
     */
    public function setDescription($description)
    {
        if (!is_string($description))
        {
            throw \Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'string',
                $description);
        }
        $this->description = $description;
    }

    /**
     * Возвращает ключевые слова
     *
     * @return string
     *
     * @since x.xx
     * @todo Вынести в отдельный класс
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Задаёт список ключевых слов
     *
     * @param string $keywords  новый список ключевых слов
     *
     * @throws \Eresus_Exception_InvalidArgumentType
     *
     * @since x.xx
     * @todo Вынести в отдельный класс
     */
    public function setKeywords($keywords)
    {
        if (!is_string($keywords))
        {
            throw \Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'string',
                $keywords);
        }
        $this->keywords = $keywords;
    }

    /**
     * Обрабатывает запрос и возвращает ответ
     *
     * @param Request $request
     *
     * @return Response
     *
     * @since x.xx
     */
    public function handleRequest(Request $request)
    {
        if (substr($request->getPathInfo(), 0, 8) == '/ext-3rd')
        {
            // TODO
            $response = $this->call3rdPartyExtension($request);
        }
        else
        {
            if ($request->getPathInfo() == '/admin/' || $request->getPathInfo() == '/admin.php')
            {
                $controller = new \Eresus_Admin_FrontController($this->container, $this, $request);
            }
            else
            {
                // TODO
                $controller = new \Eresus_Client_FrontController($this->container, $this, $request);
            }
            $this->controller = $controller;
            /** @var Response $response */
            //$response = $controller->process($request);
            $response = $controller->dispatch();
        }
        return $response;
    }

    /**
     * Обрабатывает запрос к стороннему расширению
     *
     * Вызов производится через коннектор этого расширения
     *
     * @param Request $request
     *
     * @return Response
     */
    protected function call3rdPartyExtension(Request $request)
    {
        $extension = substr(dirname($request->getPathInfo()), 9);
        if (($p = strpos($extension, '/')) !== false)
        {
            $extension = substr($extension, 0, $p);
        }

        /** @var Kernel $kernel */
        $kernel = $this->container->get('kernel');
        $filename = $kernel->getAppDir() . '/ext-3rd/' . $extension . '/eresus-connector.php';
        if ($extension && is_file($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
            $className = $extension . 'Connector';
            /** @var \EresusExtensionConnector $connector */
            $connector = new $className;
            $connector->proxy();
            $response = new Response();
        }
        else
        {
            $response = new Response('Not found', 404);
        }
        return $response;
    }
}

