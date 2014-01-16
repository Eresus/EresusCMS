<?php
/**
 * Страница АИ или КИ
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
 * Абстрактный элемент документа HTML
 *
 * @package Eresus
 * @since 2.15
 */
class HtmlElement
{
    /**
     * Имя тега
     *
     * @var string
     */
    private $tagName;

    /**
     * Атрибуты
     *
     * @var array
     */
    private $attrs = array();

    /**
     * Содержимое
     *
     * @var string
     */
    private $contents = null;

    /**
     * Конструктор
     *
     * @param string $tagName
     *
     * @since 2.15
     */
    public function __construct($tagName)
    {
        $this->tagName = $tagName;
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает значение атрибута
     *
     * @param string $name   имя атрибута
     * @param mixed  $value  значение атрибута
     *
     * @return void
     *
     * @since 2.15
     */
    public function setAttribute($name, $value = true)
    {
        $this->attrs[$name] = $value;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает значение атрибута
     *
     * @param string $name  имя атрибута
     *
     * @return mixed
     *
     * @since 2.15
     */
    public function getAttribute($name)
    {
        if (!isset($this->attrs[$name]))
        {
            return null;
        }

        return $this->attrs[$name];
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает содержимое
     *
     * @param string $contents  содержимое
     *
     * @return void
     *
     * @since 2.15
     */
    public function setContents($contents)
    {
        $this->contents = $contents;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает разметку элемента
     *
     * @return string  разметка HTML
     *
     * @since 2.15
     */
    public function getHTML()
    {
        // Открывающий тег
        $html = '<' . $this->tagName;

        /* Добавляем атрибуты */
        foreach ($this->attrs as $name => $value)
        {
            $html .= ' ' . $name;

            if ($value !== true)
            {
                $html .= '="' . $value . '"';
            }
        }

        $html .= '>';

        /* Если есть содержимое, то добавляем его и закрывающий тег */
        if ($this->contents !== null)
        {
            $html .= $this->contents . '</' . $this->tagName . '>';
        }

        return $html;
    }
    //-----------------------------------------------------------------------------
}



/**
 * Элемент <script>
 *
 * @package Eresus
 * @since 2.15
 */
class HtmlScriptElement extends HtmlElement
{
    /**
     * Создаёт новый элемент <script>
     *
     * @param string $script [optional]  URL или код скрипта.
     *
     * @since 2.15
     */
    public function __construct($script = '')
    {
        parent::__construct('script');

        $this->setAttribute('type', 'text/javascript');

        /*
         * Считаем URL-ом всё, что:
         * - либо содержит xxx:// в начале
         * - либо состоит из минимум двух групп символов (любые непроблеьные или «;»), разделённых
         *   точкой или слэшем
         */
        if ($script !== '' && preg_match('=(^\w{3,8}://|^[^\s;]+(\.|/)[^\s;]+$)=', $script))
        {
            $this->setAttribute('src', $script);
            $this->setContents('');
        }
        else
        {
            $this->setContents($script);
        }
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает содержимое
     *
     * @param string $contents  содержимое
     *
     * @return void
     *
     * @since 2.15
     */
    public function setContents($contents)
    {
        if ($contents)
        {
            $contents = "//<!-- <![CDATA[\n". $contents . "\n//]] -->";
        }
        parent::setContents($contents);
    }
    //-----------------------------------------------------------------------------

}



/**
 * Родительский класс веб-интерфейсов
 *
 * @package Eresus
 */
abstract class WebPage extends Eresus_CMS_Page
{
    /**
     * Идентификатор текущего раздела
     *
     * @var int
     */
    public $id = 0;

    /**
     * Значения по умолчанию
     * @var array
     */
    protected $defaults = array(
        'pageselector' => array(
            '<div class="pages">$(pages)</div>',
            '&nbsp;<a href="$(href)">$(number)</a>&nbsp;',
            '&nbsp;<b>$(number)</b>&nbsp;',
            '<a href="$(href)">&larr;</a>',
            '<a href="$(href)">&rarr;</a>',
        ),
    );

    /**
     * Конструктор
     *
     * @return WebPage
     */
    public function __construct()
    {
    }

    /**
     * Строит URL GET-запроса на основе переданных аргументов
     *
     * URL будет состоять из двух частей:
     * 1. Адрес текущего раздела ($Eresus->request['path'])
     * 2. key=value аргументы
     *
     * Список аргументов составляется объединением списка аргументов текущего запроса
     * и элементов массива $args. Элементы $args имеют приоритет над аргументами текущего
     * запроса.
     *
     * Если значение аргумента - пустая строка, он будет удалён из запроса.
     *
     * Если значение аргумента – массив, то его элементы будут объединены в строку через запятую.
     *
     * <b>Пример</b>
     *
     * Обрабатывается запрос: http://example.com/page/?name=igor&second_name=orlov&date=18.11.10
     *
     * <code>
     * $args = array(
     *   'second_name' => 'zotov',
     *   'date' => '',
     *   'age' => 31,
     *   'flags' => array('new', 'customer', 'discount'),
     * );
     * return $page->url($args);
     * </code>
     *
     * Этот код:
     *
     * - Оставит ''name'' нетронутым, потому что его нет в $args
     * - Заменит значение ''second_name''
     * - Удалит аргумент ''date''
     * - Добавит числовой аргумент ''age''
     * - Добавит массив ''flags''
     *
     * Получится:
     *
     * http://example.com/page/?name=igor&second_name=zotov&age=31&flags=new,customer,discount
     *
     * @param array $args  Установить аргументы
     *
     * @return string
     *
     * @since 2.10
     */
    public function url($args = array())
    {
        global $Eresus;

        /* Объединяем аргументы метода и аргументы текущего запроса */
        $args = array_merge($Eresus->request['arg'], $args);

        /* Превращаем значения-массивы в строки, соединяя элементы запятой */
        foreach ($args as $key => $value)
        {
            if (is_array($value))
            {
                $args[$key] = implode(',', $value);
            }
        }

        $result = array();
        foreach ($args as $key => $value)
        {
            if ($value !== '')
            {
                $result []= "$key=$value";
            }
        }

        $result = implode('&amp;', $result);
        $result = Eresus_CMS::getLegacyKernel()->request['path'] .'?'.$result;
        return $result;
    }

    /**
     * Возвращает клиентский URL страницы с идентификатором $id
     *
     * @param int $id  Идентификатор страницы
     *
     * @return string URL страницы или NULL если раздела $id не существует
     *
     * @since 2.10
     */
    public function clientURL($id)
    {
        $parents = Eresus_CMS::getLegacyKernel()->sections->parents($id);

        if (is_null($parents))
        {
            return null;
        }

        array_push($parents, $id);
        $items = Eresus_CMS::getLegacyKernel()->sections->get( $parents);

        $list = array();
        for ($i = 0; $i < count($items); $i++)
        {
            $list[array_search($items[$i]['id'], $parents)-1] = $items[$i]['name'];
        }
        $result = Eresus_CMS::getLegacyKernel()->root;
        for ($i = 0; $i < count($list); $i++)
        {
            $result .= $list[$i].'/';
        }

        return $result;
    }

    /**
     * Отрисовка переключателя страниц
     *
     * @param int     $total      Общее количество страниц
     * @param int     $current    Номер текущей страницы
     * @param string  $url        Шаблон адреса для перехода к подстранице
     * @param array   $templates  Шаблоны оформления
     *
     * @return string
     *
     * @since 2.10
     * @deprecated с 3.01 используйте {@link PaginationHelper}
     */
    public function pageSelector($total, $current, $url = null, $templates = null)
    {
        # Загрузка шаблонов
        if (!is_array($templates))
        {
            $templates = array();
        }
        for ($i=0; $i < 5; $i++)
        {
            if (!isset($templates[$i]))
            {
                $templates[$i] = $this->defaults['pageselector'][$i];
            }
        }

        if (is_null($url))
        {
            $url = Eresus_CMS::getLegacyKernel()->request['path'].'p%d/';
        }

        $pages = array(); # Отображаемые страницы
        # Определяем номера первой и последней отображаемых страниц
        $visible = 10;
        if ($total > $visible)
        {
            # Будут показаны НЕ все страницы
            $from = floor($current - $visible / 2); # Начинаем показ с текущей минус половину видимых
            if ($from < 1)
            {
                $from = 1; # Страниц меньше 1-й не существует
            }
            $to = $from + $visible - 1; # мы должны показать $visible страниц
            if ($to > $total)
            {
                # Но если это больше чем страниц всего, вносим исправления
                $to = $total;
                $from = $to - $visible + 1;
            }
        }
        else
        {
            # Будут показаны все страницы
            $from = 1;
            $to = $total;
        }
        for ($i = $from; $i <= $to; $i++)
        {
            $src['href'] = sprintf($url, $i);
            $src['number'] = $i;
            $pages[] = replaceMacros($templates[$i != $current ? 1 : 2], $src);
        }

        $pages = implode('', $pages);
        if ($from != 1)
        {
            $pages = replaceMacros($templates[3], array('href' => sprintf($url, 1))).$pages;
        }
        if ($to != $total)
        {
            $pages .= replaceMacros($templates[4], array('href' => sprintf($url, $total)));
        }
        $result = replaceMacros($templates[0], array('pages' => $pages));

        return $result;
    }
}

