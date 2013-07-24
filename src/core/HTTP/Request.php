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
     * Разобранный запрос
     * @var array
     */
    protected $request = array();

    /**
     * Локальный корень адресов
     * @var string
     * @see getLocal()
     */
    protected $localRoot = '';

    /**
     * Конструктор
     *
     * @param string|Eresus_HTTP_Request $source  запрос в виде объекта или строки
     *
     * @throws Eresus_Exception_InvalidArgumentType
     */
    public function __construct($source = null)
    {
        switch (true)
        {
            case is_object($source) && $source instanceof Eresus_HTTP_Request:
                $this->request = $source->toArray();
                break;
            case is_string($source):
                $this->request = @parse_url($source);
                $this->request['local'] = $this->getPath();
                if ($this->getQuery())
                {
                    $this->request['local'] .= '?' . $this->getQuery();
                    parse_str($this->getQuery(), $this->request['args']);
                    if (!get_magic_quotes_gpc())
                    {
                        /* Имитируем поведение parse_str */
                        foreach ($this->request['args'] as $key => $value)
                        {
                            $this->request['args'][$key] = addslashes($value);
                        }
                    }
                    if ($this->request['args'] && get_magic_quotes_gpc())
                    {
                        $this->request['args'] = array_map('stripslashes', $this->request['args']);
                    }
                }
                break;
            case is_null($source):
                if (!Eresus_Kernel::isCLI())
                {
                    if (isset($_SERVER['REQUEST_URI']))
                    {
                        $this->request = @parse_url($_SERVER['REQUEST_URI']);
                    }
                    $this->request['local'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
                    $this->request['args'] = $_POST;
                    foreach ($_GET as $key => $value)
                    {
                        if (!isset($this->request['args'][$key]))
                        {
                            $this->request['args'][$key] = $value;
                        }
                    }

                    if ($this->request['args'] && get_magic_quotes_gpc())
                    {
                        $this->request['args'] = array_map('stripslashes', $this->request['args']);
                    }
                }
                break;
            default:
                throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1,
                    'Eresus_HTTP_Request, string or NULL', $source);
        }
    }

    /**
     * Возвращает разобранные части запроса
     *
     * @return array
     * @internal
     * @ignore
     */
    public function toArray()
    {
        return $this->request;
    }

    /**
     * Возвращает схему (протокол)
     *
     * @return string  «http» или «https»
     */
    public function getScheme()
    {
        if (!isset($this->request['scheme']))
        {
            $this->request['scheme'] = 'http';
        }

        $result = $this->request['scheme'];

        return $result;
    }

    /**
     * Возвращает метод запроса
     *
     * @return string  GET, POST…
     */
    public function getMethod()
    {
        if (!isset($this->request['method']))
        {
            $this->request['method'] = isset($_SERVER['REQUEST_METHOD']) ?
                strtoupper($_SERVER['REQUEST_METHOD']) :
                'GET';
        }

        $result = $this->request['method'];

        return $result;
    }

    /**
     * Задаёт метод запроса
     *
     * @param string $value
     */
    public function setMethod($value)
    {
        $this->request['method'] = $value;
    }

    /**
     * Возвращает хост
     *
     * @return string
     */
    public function getHost()
    {
        if (!isset($this->request['host']))
        {
            $this->request['host'] = isset($_SERVER['HTTP_HOST']) ?
                strtolower($_SERVER['HTTP_HOST']) :
                'localhost';
        }

        $result = $this->request['host'];

        return $result;
    }

    /**
     * Возвращает путь (папку и имя файла) из запроса
     * @return string
     */
    public function getPath()
    {
        if (!isset($this->request['path']))
        {
            $this->request['path'] = '/';
        }

        $result = $this->request['path'];

        return $result;
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
        if (!isset($this->request['directory']))
        {
            $path = $this->getPath();
            $this->request['directory'] = dirname($path);
        }

        $result = $this->request['directory'];

        return $result;
    }

    /**
     * Возвращает имя файла (без пути) из запроса
     *
     * @return string
     */
    public function getFile()
    {
        if (!isset($this->request['file']))
        {
            $this->request['file'] = basename($this->getPath());
        }

        $result = $this->request['file'];

        return $result;
    }

    /**
     * Возвращает аргументы GET (часть URL после знака "?")
     * @return string
     */
    public function getQuery()
    {
        if (!isset($this->request['query']))
        {
            $this->request['query'] = '';
        }

        $result = $this->request['query'];

        return $result;
    }

    /**
     * Возвращает все значения аргументов GET и POST
     * @return array
     */
    public function getArgs()
    {
        $result = $this->request['args'];

        if (get_magic_quotes_gpc())
        {
            $result = array_map('stripslashes', $result);
        }

        return $result;
    }

    /**
     * Возвращает значение аргумента GET или POST
     *
     * @param string $arg     имя аргумента
     * @param mixed  $filter  фильтр
     * @return mixed
     */
    public function arg($arg, $filter = null)
    {
        if (!isset($this->request['args'][$arg]))
        {
            return null;
        }

        $result =  $this->request['args'][$arg];

        switch (true)
        {
            case is_callable($filter, false, $callback):
                if (is_array($filter) && is_object($filter[0]))
                {
                    $result = $filter[0]->$filter[1]($result);
                }
                else
                {
                    $result = $callback($result);
                }
                break;
            case is_string($filter):
                switch ($filter)
                {
                    case 'int':
                    case 'integer':
                        $result = intval(filter_var($result, FILTER_SANITIZE_NUMBER_INT));
                        break;
                    case 'float':
                        $result = floatval(filter_var($result, FILTER_SANITIZE_NUMBER_FLOAT,
                            FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND |
                                FILTER_FLAG_ALLOW_SCIENTIFIC));
                        break;
                    default:
                        $result = preg_replace($filter, '', $result);
                        break;
                }
                break;
        }

        return $result;
    }

    /**
     * Возвращает значение аргумента GET или POST
     *
     * @param string $arg     имя аргумента
     * @param mixed  $filter  фильтр
     * @return mixed
     */
    public function getArg($arg, $filter = null)
    {
        return $this->arg($arg, $filter);
    }

    /**
     * Set value of GET or POST argument
     *
     * @param string $arg
     * @param mixed  $value
     */
    public function setArg($arg, $value)
    {
        $this->request['args'][$arg] = $value;
    }

    /**
     * Get local part of URI
     * @return string
     */
    public function getLocal()
    {
        $result = $this->request['local'];

        if ($this->localRoot && strpos($result, $this->localRoot) === 0)
        {
            $result = substr($result, strlen($this->localRoot));
        }

        if ($result === false)
        {
            return '';
        }
        return $result;
    }

    /**
     * Return full URI
     * @return string
     */
    public function __toString()
    {
        $request = $this->getScheme().'://'.$this->getHost().$this->getPath();
        if ($this->getQuery())
        {
            $request .= '?' . $this->getQuery();
        }
        return $request;
    }

    /**
     * Set local root
     *
     * Local root is a part of URL after host name which will be cutted from result
     * of Eresus_HTTP_Request::getLocal.
     *
     * <code>
     * $req = new Eresus_HTTP_Request('http://example.org/some/path/script?query');
     * echo $req->getLocal(); // '/some/path/script?query'
     * $req->setLocalRoot('/some');
     * echo $req->getLocal(); // '/path/script?query'
     * </code>
     *
     * @param string $root
     * @return void
     *
     * @since 0.1.1
     */
    public function setLocalRoot($root)
    {
        $this->localRoot = $root;
    }

    /**
     * Get local root
     * @return string
     */
    public function getLocalRoot()
    {
        return $this->localRoot;
    }
}

