<?php
/**
 * Страница, создаваемая CMS
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
 * Страница, создаваемая CMS
 *
 * Как правило, результатом запроса к сайту является страница (документ) HTML, отправляемый в
 * браузеру. Этот класс описывает такую страницу.
 *
 * @property-read string $title        полный заголовок страницы
 * @property-read string $description  полное описание страницы
 * @property-read string $keywords     ключевые слова страницы
 *
 * @package Eresus
 * @since 3.01
 */
abstract class Eresus_CMS_Page
{
    /**
     * HTTP-заголовки ответа
     *
     * @var array
     * @deprecated с 3.01
     * @todo сделать приватным
     */
    public $headers = array();

    /**
     * Описание секции HEAD
     *
     * - meta-http — мета-теги HTTP-заголовков
     * - meta-tags — мета-теги
     * - link — подключение внешних ресурсов
     * - style — CSS
     * - jslibs — библиотеки JavaScript
     * - script — скрипты
     * - content — прочее
     *
     * @var array
     */
    protected $head = array(
        'meta-http' => array(),
        'meta-tags' => array(),
        'link' => array(),
        'style' => array(),
        'jslibs' => array(),
        'scripts' => array(),
        'content' => '',
    );

    /**
     * Наполнение секции <body>
     *
     * @var array
     */
    protected $body = array(
        'scripts' => array(),
    );

    /**
     * Сообщения об ошибках
     * @var array
     * @since 3.01
     */
    private $errorMessages = array();

    /**
     * Магический метод для доступа к свойствам страницы
     *
     * @param string $property  имя свойства
     * @return mixed
     * @since 3.01
     */
    public function __get($property)
    {
        $method = 'get' . $property;
        if (method_exists($this, $method))
        {
            return $this->{$method}();
        }
        return null;
    }

    /**
     * Магический метод записи свойств страницы
     *
     * @param string $property  имя свойства
     * @param mixed  $value
     *
     * @throws LogicException
     *
     * @since 3.01
     */
    public function __set($property, $value)
    {
        $method = 'set' . $property;
        if (method_exists($this, $method))
        {
            $this->{$method}($value);
        }
        else
        {
            throw new LogicException(sprintf(
                'Property "%s" not exists in class "%s"', $property, get_class($this)
            ));
        }
    }

    /**
     * Добавляет на страницу сообщение об ошибке
     *
     * @param string $html  сообщение
     *
     * @see getErrorMessages
     * @see clearErrorMessages
     * @since 3.01
     */
    public function addErrorMessage($html)
    {
        $this->errorMessages []= $html;
    }

    /**
     * Возвращает имеющиеся сообщения об ошибках
     *
     * @return string[]
     *
     * @since 3.01
     * @see addErrorMessage
     * @see clearErrorMessages
     */
    public function getErrorMessages()
    {
        return $this->errorMessages;
    }

    /**
     * Очищает имеющиеся сообщения об ошибках
     *
     * @since 3.01
     * @see addErrorMessage
     * @see getErrorMessages
     */
    public function clearErrorMessages()
    {
        $this->errorMessages = array();
    }

    /**
     * Возвращает полный заголовок страницы
     *
     * Этот метод возвращает полный заголовок страницы, куда, в зависимости от настроек сайта, могут
     * входить: имя сайта, заголовок сайта, заголовок раздела и т. д.
     *
     * @return string
     * @since 3.01
     */
    abstract public function getTitle();

    /**
     * Задаёт заголовок страницы
     *
     * @param string $title
     *
     * @since 3.01
     */
    abstract public function setTitle($title);

    /**
     * Возвращает описание страницы
     *
     * Этот метод возвращает полное описание страницы для мета-тега description. В зависимости от
     * настроек сайта, в него могут входить: описание сайта и описание раздела.
     *
     * @return string
     * @since 3.01
     */
    abstract public function getDescription();

    /**
     * Возвращает ключевые слова страницы
     *
     * Этот метод возвращает полный набор ключевых слов страницы для мета-тега keywords. В
     * зависимости от настроек сайта, в него могут входить: ключевые слова сайта и ключевые слова
     * раздела.
     *
     * @return string
     * @since 3.01
     */
    abstract public function getKeywords();

    /**
     * Добавляет или изменяет мета-тег <meta http-equiv="$httpEquiv" content="$content">
     *
     * @param string $httpEquiv  Имя заголовка HTTP
     * @param string $content  	  Значение заголовка
     *
     * @since 2.10
     */
    public function setMetaHeader($httpEquiv, $content)
    {
        $this->head['meta-http'][$httpEquiv] = $content;
    }

    /**
     * Установка мета-тега
     *
     * @param string $name     Имя тега
     * @param string $content  Значение тега
     *
     * @since 2.10
     */
    public function setMetaTag($name, $content)
    {
        $this->head['meta-tags'][$name] = $content;
    }

    /**
     * Подключение CSS-файла
     *
     * @param string $url    URL файла
     * @param string $media  Тип носителя
     *
     * @since 2.10
     */
    public function linkStyles($url, $media = '')
    {
        /* Проверяем, не добавлен ли уже этот URL  */
        for ($i = 0; $i < count($this->head['link']); $i++)
        {
            if ($this->head['link'][$i]['href'] == $url)
            {
                return;
            }
        }

        $item = array('rel' => 'StyleSheet', 'href' => $url, 'type' => 'text/css');

        if (!empty($media))
        {
            $item['media'] = $media;
        }

        $this->head['link'][] = $item;
    }

    /**
     * Встраивание CSS
     *
     * @param string $content  Стили CSS
     * @param string $media    Тип носителя
     *
     * @since 2.10
     */
    public function addStyles($content, $media = '')
    {
        $content = preg_replace(array('/^(\s)+/m', '/^(\S)/m'), array('		', '	\1'), $content);
        $content = rtrim($content);
        $item = array('content' => $content);
        if (!empty($media))
        {
            $item['media'] = $media;
        }
        $this->head['style'][] = $item;
    }

    /**
     * Подключение клиентского скрипта
     *
     * В качестве дополнительных параметров метод может принимать:
     *
     * <b>Типы скриптов</b>
     * - ecma, text/ecmascript
     * - javascript, text/javascript
     * - jscript, text/jscript
     * - vbscript, text/vbscript
     *
     * <b>Параметры загрузки скриптов</b>
     * - async
     * - defer
     * - top
     *
     * Если скрипту передан параметр defer, то скрипт будет подключён в конце документа, перед
     * </body>, в противном случае он будет подключён в <head>.
     *
     * Если передан аргумент «top», то скрипт будет подключен в самом начале блока скриптов.
     *
     * @param string $url  URL скрипта
     * @param string ...   Дополнительные параметры
     *
     * @since 2.10
     */
    public function linkScripts($url)
    {
        foreach ($this->head['scripts'] as $script)
        {
            /** @var HtmlScriptElement $script */
            if ($script->getAttribute('src') == $url)
            {
                return;
            }
        }
        foreach ($this->head['jslibs'] as $script)
        {
            if ($script->getAttribute('src') == $url)
            {
                return;
            }
        }

        $script = new HtmlScriptElement($url);

        $args = func_get_args();
        // Отбрасываем $url
        array_shift($args);

        $top = false;
        $lib = false;

        foreach ($args as $arg)
        {
            switch (strtolower($arg))
            {
                case 'ecma':
                case 'text/ecmascript':
                    $script->setAttribute('type', 'text/ecmascript');
                    break;
                case 'javascript':
                case 'text/javascript':
                    $script->setAttribute('type', 'text/javascript');
                    break;
                case 'jscript':
                case 'text/jscript':
                    $script->setAttribute('type', 'text/jscript');
                    break;
                case 'vbscript':
                case 'text/vbscript':
                    $script->setAttribute('type', 'text/vbscript');
                    break;
                case 'async':
                case 'defer':
                    $script->setAttribute($arg);
                    break;
                case 'top':
                    $top = true;
                    break;
                case 'lib':
                    $lib = true;
                    break;
            }
        }

        if ($script->getAttribute('defer'))
        {
            $this->body['scripts'][] = $script;
        }
        else
        {
            if ($lib)
            {
                $this->head['jslibs'][] = $script;
            }
            elseif ($top)
            {
                array_unshift($this->head['scripts'], $script);
            }
            else
            {
                $this->head['scripts'][] = $script;
            }
        }
    }

    /**
     * Встраивает в страницу клиентские скрипты
     *
     * <b>Типы скриптов</b>
     * - ecma, text/ecmascript
     * - javascript, text/javascript
     * - jscript, text/jscript
     * - vbscript, text/vbscript
     *
     * <b>Параметры загрузки скриптов</b>
     * - head - вставить в секцию <head> (по умолчанию)
     * - body - вставить в секцию <body>
     *
     * @param string $code  Код скрипта
     * @param string ...    Дополнительные параметры
     *
     * @since 2.10
     */
    public function addScripts($code)
    {
        $script = new HtmlScriptElement($code);

        $args = func_get_args();
        // Отбрасываем $code
        array_shift($args);

        // По умолчанию помещаем скрипты в <head>
        $body = false;

        foreach ($args as $arg)
        {
            switch (strtolower($arg))
            {
                case 'ecma':
                case 'text/ecmascript':
                    $script->setAttribute('type', 'text/ecmascript');
                    break;
                case 'javascript':
                case 'text/javascript':
                    $script->setAttribute('type', 'text/javascript');
                    break;
                case 'jscript':
                case 'text/jscript':
                    $script->setAttribute('type', 'text/jscript');
                    break;
                case 'vbscript':
                case 'text/vbscript':
                    $script->setAttribute('type', 'text/vbscript');
                    break;
                case 'body':
                    $body = true;
                    break;
            }
        }

        if ($body)
        {
            $this->body['scripts'][] = $script;
        }
        else
        {
            $this->head['scripts'][] = $script;
        }
    }

    /**
     * Подключает библиотеку JavaScript
     *
     * При множественном вызове метода, библиотека будет подключена только один раз.
     *
     * Доступные библиотеки:
     *
     * - jquery — {@link http://jquery.com/ jQuery}
     * - modernizr — {@link http://modernizr.com/ Modernizr}
     * - webshim — {@link http://afarkas.github.com/webshim/demos/ Webshim}
     * - webshims — устаревший синоним для webshim
     *
     * Аргументы для библиотеки jquery:
     *
     * - ui — jQuery UI
     * - cookie — jQuery.Cookie
     *
     * @param string $library  имя библиотеки
     * @param ...              дополнительные аргументы
     *
     * @return void
     *
     * @since 2.16
     */
    public function linkJsLib($library)
    {
        $args = func_get_args();
        array_shift($args);
        $root = Eresus_CMS::getLegacyKernel()->root;
        switch ($library)
        {
            case 'jquery':
                $this->linkScripts($root . 'core/jquery/jquery.min.js', 'lib');
                if (in_array('cookie', $args))
                {
                    $this->linkScripts($root . 'core/jquery/jquery.cookie.js', 'lib');
                }
                if (in_array('ui', $args))
                {
                    $this->linkScripts($root . 'core/jquery/jquery-ui.min.js', 'lib');
                }
                break;
            case 'modernizr':
                $this->linkScripts($root . 'core/js/modernizr/modernizr.min.js', 'lib');
                break;
            case 'webshim':
            case 'webshims':
                $this->linkJsLib('jquery');
                $this->linkJsLib('modernizr');
                $this->linkScripts($root . 'core/js/webshim/polyfiller.js', 'lib');
                $this->addScripts('jQuery.webshims.polyfill();');
                break;
        }
    }

    /**
     * Отрисовка секции <head>
     *
     * @return string  Отрисованная секция <head>
     */
    public function renderHeadSection()
    {
        $result = array();
        /* <meta> теги */
        if (count($this->head['meta-http']))
        {
            foreach ($this->head['meta-http'] as $key => $value)
            {
                $result[] = '	<meta http-equiv="' . $key . '" content="' . $value . '" />';
            }
        }

        if (count($this->head['meta-tags']))
        {
            foreach ($this->head['meta-tags'] as $key => $value)
            {
                $result[] = '	<meta name="' . $key . '" content="' . $value . '" />';
            }
        }

        /* <link> */
        if (count($this->head['link']))
        {
            foreach ($this->head['link'] as $value)
            {
                $result[] = '	<link rel="' . $value['rel'] . '" href="' . $value['href'] . '" type="' .
                    $value['type'] . '"' . (isset($value['media']) ? ' media="' . $value['media'] . '"' : '') . ' />';
            }
        }

        /*
         * <script>
         */
        foreach ($this->head['jslibs'] as $script)
        {
            /** @var HtmlScriptElement $script */
            $result[] = $script->getHTML();
        }
        foreach ($this->head['scripts'] as $script)
        {
            /** @var HtmlScriptElement $script */
            $result[] = $script->getHTML();
        }

        /* <style> */
        if (count($this->head['style']))
        {
            foreach ($this->head['style'] as $value)
            {
                $result[] = '	<style type="text/css"' . (isset($value['media']) ? ' media="' .
                        $value['media'] . '"' : '') . '>' . "\n" . $value['content'] . "\n  </style>";
            }
        }

        $this->head['content'] = trim($this->head['content']);
        if (!empty($this->head['content']))
        {
            $result[] = $this->head['content'];
        }

        $result = implode("\n", $result);
        return $result;
    }

    /**
     * Отрисовка секции <body>
     *
     * @return string  HTML
     */
    protected function renderBodySection()
    {
        $result = array();
        /*
         * <script>
         */
        foreach ($this->body['scripts'] as $script)
        {
            /** @var HtmlScriptElement $script */
            $result[] = $script->getHTML();
        }

        $result = implode("\n", $result);
        return $result;
    }
}

