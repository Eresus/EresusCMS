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

use Symfony\Component\HttpFoundation\Request;

/**
 * Запрос HTTP
 *
 * @package Eresus
 * @subpackage HTTP
 */
class Eresus_HTTP_Request extends Request
{
    /**
     * Конструктор
     *
     * @param array  $query      The GET parameters
     * @param array  $request    The POST parameters
     * @param array  $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array  $cookies    The COOKIE parameters
     * @param array  $files      The FILES parameters
     * @param array  $server     The SERVER parameters
     * @param string $content    The raw body data
     */
    public function __construct($query = array(), array $request = array(),
        array $attributes = array(), array $cookies = array(), array $files = array(),
        array $server = array(), $content = null)
    {
        $errstr = 'Passing $query as %s in method %s is deprecated';
        switch (true)
        {
            case is_string($query):
                trigger_error(sprintf($errstr, 'string', __METHOD__), E_USER_DEPRECATED);
                $parts = parse_url($query);
                $queryString = isset($parts['query']) ? $parts['query'] : '';
                parse_str($queryString, $query);
                $server = array(
                    'HTTP_HOST' => $parts['host'],
                    'REQUEST_URI' => $parts['path'] . ($queryString ? '?' . $queryString : ''),
                    'QUERY_STRING' => $queryString
                );
                parent::__construct($query, array(), array(), array(), array(), $server);
                break;
            case is_null($query):
                trigger_error(sprintf($errstr, 'null', __METHOD__), E_USER_DEPRECATED);
                $parts = parse_url(isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '');
                $queryString = isset($parts['query']) ? $parts['query'] : '';
                parse_str($queryString, $query);
                parent::__construct($query);
                break;
            case $query instanceof Eresus_HTTP_Request:
                /** @var Request $query */
                parent::__construct($query->query->all(), $query->request->all(),
                    $query->attributes->all(), $query->cookies->all(), $query->files->all(),
                    $query->server->all(), $query->content);
                break;
            default:
                parent::__construct($query, $request, $attributes, $cookies, $files, $server,
                    $content);
        }
    }

    /**
     * Задаёт схему (протокол)
     *
     * @since 3.01
     * @deprecated с x.xx метод ничего не делает и генерирует E_USER_DEPRECATED
     */
    public function setScheme()
    {
        trigger_error(__METHOD__ . ' is deprecated', E_USER_DEPRECATED);
    }

    /**
     * Задаёт хост
     *
     * @since 3.01
     * @deprecated с x.xx метод ничего не делает и генерирует E_USER_DEPRECATED
     */
    public function setHost()
    {
        trigger_error(__METHOD__ . ' is deprecated', E_USER_DEPRECATED);
    }

    /**
     * Возвращает путь (папку и имя файла) из запроса
     *
     * @return string
     *
     * @deprecated с x.xx используйте {@link getBasePath()} и {@link getPathInfo()}
     */
    public function getPath()
    {
        trigger_error(__METHOD__ . ' is deprecated', E_USER_DEPRECATED);
        return $this->getBasePath() . $this->getPathInfo();
    }

    /**
     * Задаёт путь (папку и имя файла)
     *
     * @deprecated с x.xx метод ничего не делает и генерирует E_USER_DEPRECATED
     */
    public function setPath()
    {
        trigger_error(__METHOD__ . ' is deprecated', E_USER_DEPRECATED);
    }

    /**
     * Возвращает папку из запроса
     *
     * Возвращаемый путь не заканчивается слэшем.
     *
     * @return string
     *
     * @deprecated с x.xx
     */
    public function getDirectory()
    {
        $path = @$this->getPath();
        return substr($path, -1) == '/'
            ? substr($path, 0, -1)
            : dirname($path);
    }

    /**
     * Возвращает имя файла (без пути) из запроса
     *
     * @return string
     *
     * @deprecated с x.xx
     */
    public function getFile()
    {
        return substr($this->getPathInfo(), -1) == '/'
            ? ''
            : basename($this->getPathInfo());
    }

    /**
     * Задаёт строку аргументов GET
     *
     * @since 3.01
     *
     * @deprecated с x.xx метод ничего не делает и генерирует E_USER_DEPRECATED
     */
    public function setQueryString()
    {
        trigger_error(__METHOD__ . ' is deprecated', E_USER_DEPRECATED);
    }
}

