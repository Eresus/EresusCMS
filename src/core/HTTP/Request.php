<?php
/**
 * Запрос HTTP
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
 * Запрос HTTP
 *
 * @package Eresus
 * @subpackage HTTP
 */
class Eresus_HTTP_Request
{
    /**
     * Параметры GET
     * @var Eresus_HTTP_Parameters
     * @since 3.01
     */
    public $query;

    /**
     * Параметры POST
     * @var Eresus_HTTP_Parameters
     * @since 3.01
     */
    public $request;

    /**
     * Схема (http, https…)
     * @var string
     * @since 3.01
     */
    private $scheme = null;

    /**
     * Метод
     * @var string
     * @since 3.01
     */
    private $method = 'GET';

    /**
     * Хост
     * @var string
     * @since 3.01
     */
    private $host = null;

    /**
     * Порт
     * @var string
     * @since 3.01
     */
    private $port = null;

    /**
     * Пользователь
     * @var string
     * @since 3.01
     */
    private $user = null;

    /**
     * Пароль
     * @var string
     * @since 3.01
     */
    private $password = null;

    /**
     * Путь
     * @var string
     * @since 3.01
     */
    private $path = null;

    /**
     * Фрагмент
     * @var string
     * @since 3.01
     */
    private $fragment = null;

    /**
     * Конструктор
     *
     * @param string|Eresus_HTTP_Request $source  запрос в виде объекта или строки
     *
     * @throws Eresus_Exception_InvalidArgumentType
     */
    public function __construct($source = null)
    {
        if ($source instanceof self)
        {
            $this->query = clone $source->query;
            $this->request = clone $source->request;
            $this->scheme = $source->scheme;
            $this->method = $source->method;
            $this->host = $source->host;
            $this->port = $source->port;
            $this->user = $source->user;
            $this->password = $source->password;
            $this->path = $source->path;
            $this->fragment = $source->fragment;
        }
        else
        {
            $this->query = new Eresus_HTTP_Parameters();
            $this->request = new Eresus_HTTP_Parameters();
            switch (true)
            {
                case is_string($source):
                    break;
                case is_null($source):
                    $source = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
                    break;
                default:
                    throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1,
                        'Eresus_HTTP_Request, string or null', $source);
            }
            $url = parse_url($source);
            $this->scheme = @$url['scheme'];
            $this->host = @$url['host'];
            $this->port = @$url['port'];
            $this->user = @$url['user'];
            $this->password = @$url['password'];
            $this->path = @$url['path'];
            if (array_key_exists('query', $url))
            {
                $this->setQueryString($url['query']);
            }
            $this->fragment = @$url['fragment'];
        }
    }

    /**
     * Возвращает объект, созданный на основе глобальных переменных PHP
     *
     * @return Eresus_HTTP_Request
     * @since 3.01
     */
    public static function createFromGlobals()
    {
        $request = new self(@$_SERVER['REQUEST_URI']);
        if (array_key_exists('REQUEST_METHOD', $_SERVER))
        {
            $request->setMethod($_SERVER['REQUEST_METHOD']);
        }
        if ($request->getMethod() == 'POST')
        {
            $request->request->replace($_POST);
        }
        return $request;
    }

    /**
     * Возвращает схему (протокол)
     *
     * @return string  «http» или «https»
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Задаёт схему (протокол)
     *
     * @param string  $scheme
     *
     * @since 3.01
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Возвращает метод запроса
     *
     * @return string  GET, POST…
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Задаёт метод запроса
     *
     * @param string $value
     */
    public function setMethod($value)
    {
        $this->method = $value;
    }

    /**
     * Возвращает хост
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Возвращает хост
     *
     * @param string $host
     *
     * @since 3.01
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * Возвращает путь (папку и имя файла) из запроса
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Возвращает папку из запроса
     *
     * Возвращаемый путь не заканчивается слэшем.
     *
     * @return string
     */
    public function getDirectory()
    {
        return substr($this->getPath(), -1) == '/'
            ? substr($this->getPath(), 0, -1)
            : dirname($this->getPath());
    }

    /**
     * Возвращает имя файла (без пути) из запроса
     *
     * @return string
     */
    public function getFile()
    {
        return substr($this->getPath(), -1) == '/'
            ? ''
            : basename($this->getPath());
    }

    /**
     * Возвращает строку аргументов GET (часть URL после знака "?")
     * @return string
     */
    public function getQueryString()
    {
        $parameters = $this->query->all();
        array_walk($parameters,
            function (&$value, $key)
            {
                $value = $key . '=' . $value;
            }
        );
        return implode('&', $parameters);
    }

    /**
     * Задаёт строку аргументов GET
     *
     * @param string $query
     *
     * @since 3.01
     */
    public function setQueryString($query)
    {
        parse_str($query, $parameters);
        $this->query->replace($parameters);
    }

    /**
     * Возвращает запрос (URL) в виде строки
     *
     * @return string
     */
    public function __toString()
    {
        $url = '';
        if ($this->getScheme() != '')
        {
            $url .= $this->getScheme() . ':';
        }
        if ($this->getHost() != '')
        {
            $url .= '//' . $this->getHost();
        }
        $url .= $this->getPath();
        if ($this->getQueryString())
        {
            $url .= '?' . $this->getQueryString();
        }
        return $url;
    }
}

