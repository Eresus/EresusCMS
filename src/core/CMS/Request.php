<?php
/**
 * Запрос к CMS
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
 * Запрос к CMS
 *
 * Это надстройка над {@link Eresus_HTTP_Request}, адаптирующая запрос HTTP с учётом свойств сайта.
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_CMS_Request extends Eresus_HTTP_Request
{
    /**
     * Корневой адрес сайта
     *
     * @var null|string
     * @since 3.01
     */
    private $siteRoot = null;

    /**
     * Исходный запрос HTTP
     *
     * @var null|Eresus_HTTP_Request
     * @since 3.01
     */
    private $httpRequest = null;

    /**
     * Конструктор
     *
     * @param string|Eresus_HTTP_Request $source  запрос в виде объекта или строки
     *
     * @throws Eresus_Exception_InvalidArgumentType
     */
    public function __construct($source = null)
    {
        if ($source instanceof Eresus_HTTP_Request)
        {
            $this->httpRequest = $source;
        }
        else
        {
            $this->httpRequest = new Eresus_HTTP_Request($source);
        }
        parent::__construct($source);
    }

    /**
     * Задаёт корневой адрес сайта
     *
     * Этот адрес будет вырезаться в таких методах как {@link getPath()}, {@link getDirectry()},
     * {@link getFile()}.
     *
     * @param mixed $url  корневой URL или только путь относительно корня домена
     *
     * @since 3.01
     */
    public function setSiteRoot($url)
    {
        $url = strval($url);
        if ('' !== $url)
        {
            $url = rtrim(parse_url($url, PHP_URL_PATH), '/');
            if (substr($url, 0, 1) != '/')
            {
                $url = '/' . $url;
            }
        }
        $this->siteRoot = $url;
    }

    /**
     * Возвращает URL корня сайта
     *
     * Адрес никогда не заканчивается слэшем.
     *
     * @return string
     *
     * @since 3.01
     */
    public function getSiteRoot()
    {
        return $this->getScheme() . '://' . $this->getHost() . $this->siteRoot;
    }

    /**
     * Возвращает схему (протокол)
     *
     * Если схема не указана, считается, что это «http».
     *
     * @return string  «http» или «https»
     *
     * @since 3.01
     */
    public function getScheme()
    {
        $scheme = parent::getScheme();
        if ('' == $scheme)
        {
            $scheme = 'http';
        }
        return $scheme;
    }

    /**
     * Возвращает хост
     *
     * Если хост не задан, считается что это «localhost».
     *
     * @return string
     * @since 3.01
     */
    public function getHost()
    {
        $host = parent::getHost();
        if ('' == $host)
        {
            $host = 'localhost';
        }
        return $host;
    }

    /**
     * Возвращает путь (папку и имя файла) из запроса
     *
     * @return string
     * @since 3.01
     * @deprecated с x.xx используйте {@link getPathInfo()}
     */
    public function getPath()
    {
        $path = $this->getBasePath() . $this->getPathInfo();
        if ('' == $path)
        {
            $path = '/';
        }
        if ($this->siteRoot)
        {
            $path = substr($path, strlen($this->siteRoot));
        }
        return $path;
    }

    /**
     * Возвращает исходный запрос HTTP
     *
     * @return Eresus_HTTP_Request
     * @since 3.01
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * Возвращает запрос (URL) в виде строки
     *
     * @return string
     */
    public function __toString()
    {
        $url = $this->getSiteRoot() . $this->getPath();
        if ($this->getQueryString())
        {
            $url .= '?' . $this->getQueryString();
        }
        return $url;
    }
}

