<?php
/**
 * Инструменты HTTP
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
 * Инструменты HTTP
 *
 * @package Eresus
 * @subpackage HTTP
 */
class HTTP
{
    /**
     * Replace every part of the first URL when there's one of the second URL
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_REPLACE = 1;

    /**
     * Join relative paths
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_JOIN_PATH = 2;

    /**
     * Join query strings
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_JOIN_QUERY = 4;

    /**
     * Strip any user authentication information
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_STRIP_USER = 8;

    /**
     * Strip any password authentication information
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_STRIP_PASS = 16;

    /**
     * Strip any authentication information
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_STRIP_AUTH = 32;

    /**
     * Strip explicit port numbers
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_STRIP_PORT = 64;

    /**
     * Strip complete path
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_STRIP_PATH = 128;

    /**
     * Strip query string
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_STRIP_QUERY = 256;

    /**
     * Strip any fragments (#identifier)
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_STRIP_FRAGMENT = 512;

    /**
     * Strip anything but scheme and host
     *
     * @var int
     * @todo Wiki docs
     */
    const URL_STRIP_ALL = 1024;

    /**
     * HTTP request object
     * @var HTTPRequest
     */
    static private $request;

    /**
     * Build an URL
     *
     * The parts of the second URL will be merged into the first according to the flags argument.
     *
     * @param	 mixed $url      (Part(s) of) an URL in form of a string or associative array like
     *                         parse_url() returns
     * @param	 mixed $parts    Same as the first argument
     * @param	 int   $flags    A bitmask of binary or'ed HTTP_URL constants (Optional)HTTP_URL_REPLACE
     *                         is the default
     * @param	 array $new_url  If set, it will be filled with the parts of the composed url like
     *                         parse_url() would return
     *
     * @author tycoonmaster(at)gmail(dot)com
     * @author Mikhail Krasilnikov <mk@procreat.ru>
     *
     * @todo Wiki docs
     */
    public static function buildURL($url, $parts = array(), $flags = self::URL_REPLACE,
                                    &$new_url = false)
    {
        $keys = array('user','pass','port','path','query','fragment');

        /* HTTP::URL_STRIP_ALL becomes all the HTTP::URL_STRIP_Xs */
        if ($flags & self::URL_STRIP_ALL)
        {
            $flags |= self::URL_STRIP_USER;
            $flags |= self::URL_STRIP_PASS;
            $flags |= self::URL_STRIP_PORT;
            $flags |= self::URL_STRIP_PATH;
            $flags |= self::URL_STRIP_QUERY;
            $flags |= self::URL_STRIP_FRAGMENT;
        }
        /* HTTP::URL_STRIP_AUTH becomes HTTP::URL_STRIP_USER and HTTP::URL_STRIP_PASS */
        else if ($flags & self::URL_STRIP_AUTH)
        {
            $flags |= self::URL_STRIP_USER;
            $flags |= self::URL_STRIP_PASS;
        }

        // Parse the original URL
        $parse_url = parse_url($url);

        // Scheme and Host are always replaced
        if (isset($parts['scheme']))
            $parse_url['scheme'] = $parts['scheme'];
        if (isset($parts['host']))
            $parse_url['host'] = $parts['host'];

        // (If applicable) Replace the original URL with it's new parts
        if ($flags & self::URL_REPLACE)
        {
            foreach ($keys as $key)
            {
                if (isset($parts[$key]))
                    $parse_url[$key] = $parts[$key];
            }
        }
        else
        {
            // Join the original URL path with the new path
            if (isset($parts['path']) && ($flags & self::URL_JOIN_PATH))
            {
                if (isset($parse_url['path']))
                    $parse_url['path'] = rtrim(str_replace(basename($parse_url['path']), '',
                        $parse_url['path']), '/') . '/' . ltrim($parts['path'], '/');
                else
                    $parse_url['path'] = $parts['path'];
            }

            // Join the original query string with the new query string
            if (isset($parts['query']) && ($flags & self::URL_JOIN_QUERY))
            {
                if (isset($parse_url['query']))
                    $parse_url['query'] .= '&' . $parts['query'];
                else
                    $parse_url['query'] = $parts['query'];
            }
        }

        // Strips all the applicable sections of the URL
        // Note: Scheme and Host are never stripped
        foreach ($keys as $key)
        {
            if ($flags & (int)constant('HTTP::URL_STRIP_' . strtoupper($key)))
                unset($parse_url[$key]);
        }


        $new_url = $parse_url;

        return
            ((isset($parse_url['scheme'])) ? $parse_url['scheme'] . '://' : '')
            .((isset($parse_url['user'])) ? $parse_url['user'] . ((isset($parse_url['pass'])) ? ':' .
            $parse_url['pass'] : '') .'@' : '')
            .((isset($parse_url['host'])) ? $parse_url['host'] : '')
            .((isset($parse_url['port'])) ? ':' . $parse_url['port'] : '')
            .((isset($parse_url['path'])) ? $parse_url['path'] : '')
            .((isset($parse_url['query'])) ? '?' . $parse_url['query'] : '')
            .((isset($parse_url['fragment'])) ? '#' . $parse_url['fragment'] : '')
            ;
    }

    /**
     * Returns an instance of a Eresus_HTTP_Request class
     *
     * Object instancing only once
     *
     * @return Eresus_HTTP_Request
     */
    static public function request()
    {
        if (!self::$request)
        {
            self::$request = Eresus_HTTP_Request::createFromGlobals();
        }
        return self::$request;
    }

    /**
     * Направляет браузер на новый адрес и прекращает выполнение программы
     *
     * @param string $uri        новый адрес
     * @param bool   $permanent  отправлять заголовок '301 Moved permanently'
     */
    public static function redirect($uri, $permanent = false)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, $uri);

        $header = 'Location: '.$uri;

        if ($permanent)
        {
            header($header, true, 301);
        }
        else
        {
            header($header);
        }

        exit;
    }

    /**
     * Redirect UA to previous URI
     *
     * Method uses $_SERVER['HTTP_REFERER'] to determine previous URI. If this
     * variable not set then method will do nothing. In last case developers can
     * use next scheme:
     *
     * <code>
     *  # ...Some actions...
     *
     * 	HTTP::goback();
     *  HTTP::redirect('some_uri');
     * </code>
     *
     * So if there is nowhere to go back user will be redirected to some fixed URI.
     *
     * @see redirect
     */
    static public function goback()
    {
        if (isset($_SERVER['HTTP_REFERER']))
            self::redirect($_SERVER['HTTP_REFERER']);
    }
    //-----------------------------------------------------------------------------
}

