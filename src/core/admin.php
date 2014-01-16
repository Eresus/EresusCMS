<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * Тема оформления административного интерфейса
 *
 * Экземпляр этого класса доступен через переменную {$theme} в шаблонах и может быть использован
 * для определения путей к файлам темы, вызова помощников (helpers) и других вспомогательных
 * функций.
 *
 * Автор темы может создать потомка этого класса, размещённого в файле theme.php в корне темы.
 * В этом случае его класс будет использован вместо стандартного.
 *
 * @package Eresus
 */
class AdminUITheme
{
    /**
     * Путь к директории тем относительно корня сайта
     *
     * @var string
     */
    protected $prefix = 'admin/themes';

    /**
     * Внутреннее имя темы
     *
     * Должно совпадать с именем директории темы.
     *
     * @var string
     * @see getName
     */
    protected $name = 'default';

    /**
     * Конструктор
     *
     * @param string $name  Внутреннее имя темы (директория внутри themes)
     *
     * @return AdminUITheme
     */
    public function __construct($name = null)
    {
        if ($name)
        {
            $this->name = $name;
        }
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает внутреннее имя темы
     *
     * @return string
     * @see $name
     */
    public function getName()
    {
        return $this->name;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает адрес ресурса относительно корня сайта
     *
     * @param string $path
     *
     * @return string
     */
    public function getResource($path)
    {
        return $this->prefix . '/' . $this->getName() . '/' . $path;
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает адрес картинки относительно корня сайта
     *
     * @param string $image
     *
     * @return string
     */
    public function getImage($image)
    {
        return $this->getResource('img/' . $image);
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает адрес иконки относительно корня сайта
     *
     * @param string $icon             Имя иконки
     * @param string $size [optional]  Размер иконки. По умолчанию 'medium'
     *
     * @return string
     */
    public function getIcon($icon, $size = 'medium')
    {
        return $this->getResource('img/' . $size . '/' . $icon);
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает шаблон
     *
     * @param string $name
     *
     * @return Eresus_Template
     */
    public function getTemplate($name)
    {
        $filename = $this->getResource($name);
        $template = new Eresus_Template($filename);
        return $template;
    }
}


/**
 * @deprecated с 3.01
 */
define('ADMINUI', true);

/**
 * Класс представляет страницу административного интерфейса
 *
 * @package Eresus
 */
class TAdminUI extends Eresus_CMS_Page_Admin
{
    /**
     * Загружаемый модуль
     *
     * @var object
     */
    public $module;

    /**
     * Меню администратора
     *
     * @var array
     */
    public $menu;

    /**
     * Меню расширений
     *
     * @var array
     */
    public $extmenu;

    /**
     * Уровень вложенности
     *
     * @var int
     */
    public $sub;

    /**
     * Заголовки ответа сервера
     *
     * @var array
     */
    public $headers = array();

    /**
     * Для совместимости с TClientUI
     *
     * @var array
     */
    public $options = array();

    /**
     * Тема оформления
     *
     * @var AdminUITheme
     */
    protected $uiTheme;

    /**
     * Конструктор
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        parent::__construct($container);

        $theme = new AdminUITheme();
        $this->setUITheme($theme);
        TemplateSettings::setGlobalValue('theme', $theme);

        $this->title = admControls;
        /* Определяем уровень вложенности */
        do
        {
            $this->sub++;
            $i = strpos(Eresus_CMS::getLegacyKernel()->request['url'],
                str_repeat('sub_', $this->sub) . 'id');
        }
        while ($i !== false);

        $this->sub--;

        /* Создаем меню */
        $this->menu = array(
            array(
                "access"  => EDITOR,
                "caption" => admControls,
                "items" => array (
                    array ("link" => "pages", "caption"  => admStructure, "hint"  => admStructureHint,
                        'access'=>ADMIN),
                    array ("link" => "files", "caption"  => admFileManager, "hint"  => admFileManagerHint,
                        'access'=>EDITOR),
                    array ("link" => "plgmgr", "caption"  => admPlugins, "hint"  => admPluginsHint,
                        'access'=>ADMIN),
                    array ("link" => "themes", "caption"  => admThemes, "hint"  => admThemesHint,
                        'access'=>ADMIN),
                    array ("link" => "users", "caption"  => admUsers, "hint"  => admUsersHint,
                        'access'=>ADMIN),
                    array ("link" => "settings", "caption"  => admConfiguration,
                        "hint"  => admConfigurationHint, 'access'=>ADMIN),
                )
            ),
        );
    }
    //-----------------------------------------------------------------------------

    /**
     * Возвращает объект текущей темы оформления
     * @return AdminUITheme
     */
    public function getUITheme()
    {
        return $this->uiTheme;
    }
    //-----------------------------------------------------------------------------

    /**
     * Устанавливает новую тему оформления
     * @param AdminUITheme $theme
     * @return void
     */
    public function setUITheme(AdminUITheme $theme)
    {
        $this->uiTheme = $theme;
    }
    //-----------------------------------------------------------------------------

    /**
     * Подставляет значения макросов
     *
     * @param string $text
     *
     * @return string
     */
    public function replaceMacros($text)
    {
        $result = str_replace(
            array(
                '$(httpHost)',
                '$(httpPath)',
                '$(httpRoot)',
                '$(styleRoot)',
                '$(dataRoot)',

                '$(siteName)',
                '$(siteTitle)',
                '$(siteKeywords)',
                '$(siteDescription)',
            ),
            array(
                httpHost,
                httpPath,
                Eresus_CMS::getLegacyKernel()->root,
                styleRoot,
                dataRoot,

                siteName,
                siteTitle,
                siteKeywords,
                siteDescription,
            ),
            $text
        );
        $result = preg_replace_callback('/\$\(const:(.*?)\)/i', '__macroConst', $result);
        $result = preg_replace_callback('/\$\(var:(([\w]*)(\[.*?\]){0,1})\)/i', '__macroVar', $result);
        $result = preg_replace('/\$\(\w+(:.*?)*?\)/', '', $result);
        return $result;
    }
    //-----------------------------------------------------------------------------

    public function url($args = null, $clear = false)
    {
        $basics = array('mod','section','id','sort','desc','pg');
        $result = '';
        $arg = array();
        if (count(Eresus_CMS::getLegacyKernel()->request['arg']))
        {
            foreach (Eresus_CMS::getLegacyKernel()->request['arg'] as $key => $value)
            {
                if (in_array($key,$basics)|| strpos($key, 'sub_')===0)
                {
                    $arg[$key] = $value;
                }
            }
        }
        if (count($args))
        {
            foreach ($args as $key => $value)
            {
                $arg[$key] = $value;
            }
        }
        if (count($arg))
        {
            foreach ($arg as $key => $value)
            {
                if (!empty($value))
                {
                    $result .= '&'.$key.'='.$value;
                }
            }
        }
        if (!empty($result))
        {
            $result[0] = '?';
        }
        // См. баг http://bugs.eresus.ru/view.php?id=365
        //$result = str_replace('&', '&amp;', $result);
        $result = Eresus_CMS::getLegacyKernel()->root . 'admin.php' . $result;
        return $result;
    }
    //-----------------------------------------------------------------------------

    /**
     * Добавляет пункт в меню "Расширения"
     *
     * Добавляет пункт в меню «Расширения», создавая, при необходимости, новое меню. Аргумент
     * $section должен содержать заголовок меню к которому надо добавить пункт. Чтобы добавить пункт
     * стандартному меню «Расширения», можно использовать константу admExtensions.
     *
     * В плагинах рекомендуется вызывать этот метод из обработчика события adminOnMenuRender.
     *
     * При выборе пользователем пункта такого меню, будет вызван метод adminRender соответствующего
     * плагина. Если вы в одном плагине устанавливаете несколько пунктов меню, то в adminRender вам
     * надо указывать значение «link» в виде «$this→name . "&доп_аргумент=значение"»
     * (см. примеры ниже).
     *
     * Стандартный способ. Пункт будет добавлен в меню "Расширения". Название пункта будет
     * совпадать с названием плагина. Пункт будет доступен всем.
     * <code>
     * $page->addMenuItem(admExtensions, array('access' => EDITOR, 'link' => $this->name,
     *  'caption' => $this->title, 'hint' => $this->description));
     * </code>
     *
     * Создаёт меню "Справочники" и добавляет в него пункты "Организации" и "Типы товаров". Пункты
     * будут доступны только администраторам.
     * <code>
     * $page->addMenuItem('Справочники', array('access' => ADMIN,
     *  'link' => $this->name . '&ref=orgs', 'caption' => 'Организации'));
     * $page->addMenuItem('Справочники', array('access' => ADMIN,
     *  'link' => $this->name . '&ref=types', 'caption' => 'Организации'));
     * </code>
     *
     * @param string $section  Заголовок меню
     * @param array  $item     Описание добавляемого пункта. Ассоциативный массив:
     *                         - 'access'   - Минимальный уровень доступа, необходимый чтобы видеть
     *                                        этот пункт
     *                         - 'link'     - "адрес", соответствующий этому пункту. В URL будет
     *                                        подставлен в виде "mod=ext-{link}" (без фигурных скобок)
     *                         - 'caption'  - Название пункта меню
     *                         - 'hint'     - Всплывающая подсказка к пункту меню
     *                         - 'disabled' - Если true - пункт будет видимым, но недоступным
     *
     * @return void
     */
    public function addMenuItem($section, $item)
    {
        $item['link'] = 'ext-'.$item['link'];
        $ptr = null;
        for ($i=0; $i<count($this->extmenu); $i++)
        {
            if ($this->extmenu[$i]['caption'] == $section)
            {
                $ptr = &$this->extmenu[$i];
                break;
            }
        }

        if (is_null($ptr))
        {
            $this->extmenu[] = array(
                'access' => $item['access'],
                'caption' => $section,
                'items' => array()
            );
            $ptr = &$this->extmenu[count($this->extmenu)-1];
        }
        $ptr['items'][] = encodeHTML($item);
        if ($ptr['access'] < $item['access'])
        {
            $ptr['access'] = $item['access'];
        }
    }
    //-----------------------------------------------------------------------------


    function box($text, $class, $caption='')
    {
        $result = "<div".(empty($class)?'':' class="'.$class.'"').">\n".(empty($caption)?'':
                '<span class="'.$class.'Caption">'.$caption.'</span><br />').$text."</div>\n";
        return $result;
    }
    //-----------------------------------------------------------------------------

    function window($wnd)
    {
        $result =
            "<table border=\"0\" class=\"admWindow\"".(empty($wnd['width'])?'':' style="width: '.
                $wnd['width'].';"').">\n".
            (empty($wnd['caption'])?'':"<tr><th>".$wnd['caption']."</th></tr>\n").
            "<tr><td".(empty($wnd['style'])?'':' style="'.$wnd['style'].'"').">".$wnd['body'].
            "</td></tr>\n</table>\n";
        return $result;
    }
    //-----------------------------------------------------------------------------

    /**
     * Отрисовывает элемент управления
     *
     * @param string $type    Тип ЭУ (delete,toggle,move,custom...)
     * @param string $href    Ссылка
     * @param string $custom  Индивидуальные настройки
     *
     * @return  string  HTML
     */
    public function control($type, $href, $custom = array())
    {
        $s = '';
        switch ($type)
        {
            case 'add':
                $control = array(
                    'image' => Eresus_CMS::getLegacyKernel()->root .
                    'admin/themes/default/img/medium/item-add.png',
                    'title' => strAdd,
                    'alt' => '+',
                );
                break;
            case 'edit':
                $control = array(
                    'image' => Eresus_CMS::getLegacyKernel()->root .
                    'admin/themes/default/img/medium/item-edit.png',
                    'title' => strEdit,
                    'alt' => '&plusmn;',
                );
                break;
            case 'delete':
                $control = array(
                    'image' => Eresus_CMS::getLegacyKernel()->root .
                    'admin/themes/default/img/medium/item-delete.png',
                    'title' => strDelete,
                    'alt' => 'X',
                    'onclick' => 'return askdel(this)',
                );
                break;
            case 'setup':
                $control = array(
                    'image' => Eresus_CMS::getLegacyKernel()->root .
                    'admin/themes/default/img/medium/item-config.png',
                    'title' => strProperties,
                    'alt' => '*',
                );
                break;
            case 'move':
                $control = array(
                    'image' => Eresus_CMS::getLegacyKernel()->root .
                    'admin/themes/default/img/medium/item-move.png',
                    'title' => strMove,
                    'alt' => '-&gt;',
                );
                break;
            case 'position':
                $control = array(
                    'image' => Eresus_CMS::getLegacyKernel()->root .
                    'admin/themes/default/img/medium/move-up.png',
                    'title' => ADM_UP,
                    'alt' => '&uarr;',
                );
                $s = array_pop($href);
                $href = $href[0];
                break;
            case 'position_down':
                $control = array(
                    'image' => Eresus_CMS::getLegacyKernel()->root .
                    'admin/themes/default/img/medium/move-down.png',
                    'title' => ADM_DOWN,
                    'alt' => '&darr;',
                );
                break;
            default:
                $control = array(
                    'image' => '',
                    'title' => '',
                    'alt' => '',
                );
                break;
        }
        foreach ($custom as $key => $value)
        {
            $control[$key] = $value;
        }
        $result = '<a href="'.$href.'"'.(isset($control['onclick'])?' onclick="'.
                $control['onclick'].'"':'').'><img src="'.$control['image'].'" alt="'.$control['alt'].
            '" title="'.$control['title'].'" /></a>';
        if ($type == 'position')
        {
            $result .= ' '.$this->control('position_down', $s, $custom);
        }
        return $result;
    }
    //------------------------------------------------------------------------------

    /**
     * Отрисовывает кнопки-"вкладки"
     *
     * Отрисовывает вкладки-переключатели на основе параметров, заданных аргументом tabs. Вкладки
     * представляют собой HTML-таблицу с классом admTabs, каждый таб выполнен в виде ячейки (td).
     *
     * Алгоритм построения URL вкладки:
     *
     * 1. Если для вкладки задан параметр url, то используется его значение.
     * 2. Если не задан параметр name, то используется результат выполнения функции url.
     * 3. Если в текущем URL присутствует аргумент name, то из URL удаляется он и всё после него.
     * 4. К URL прибавляется «name=value» (с соответствующим префиксом & или ?)
     *
     * Ассоциативный массив $tabs описывает вкладки. В нём допустимы следующие ключи:
     *
     * - width (string) — Ширина вкладки в единицах CSS. Если параметр не задан, ширина будет
     *   вычисляться браузером на основе содержимого таба.
     * - items (array) Массив вкладок. Каждая вкладка описывается ассоциативным массивом (см. ниже)
     *
     * Формат описания вкладки:
     *
     * - name (string) — Имя вкладки. Используется для построения URL, если не указан параметр url.
     * - value (string) — Значение. Используется для построения URL, если не указан параметр url.
     * - url (string) — Задаёт адрес по которому будет перенаправлен ПА при нажатии на вкладку.
     * - caption (string) — Текст вкладки. Обязательный параметр.
     * - class (string) CSS-класс вкладки.
     *
     * Пример:
     *
     * <code>
     *  $tabs = array(
     *    'width' => '7em',
     *    'items' => array(
     *      array('caption' => admAdd, 'name' => 'action', 'value' => 'add'),
     *    ),
     *  );
     *  $result .= $page->renderTabs($tabs);
     * </code>
     *
     * @param array $tabs  ассоциативный массив, описывающий вкладки
     *
     * @return string  HTML
     */
    function renderTabs($tabs)
    {
        $result = '';
        if (count($tabs))
        {
            $result = '<div class="legacy-tabs ui-helper-clearfix">';
            $width = empty($tabs['width']) ?
                '' :
                ' style="width: ' . $tabs['width'] . '"';
            if (
                isset($tabs['items']) &&
                count($tabs['items'])
            )
            {
                foreach ($tabs['items'] as $item)
                {
                    if (isset($item['url']))
                    {
                        $url = $item['url'];
                    }
                    else
                    {
                        $url = Eresus_CMS::getLegacyKernel()->request['url'];
                        if (isset($item['name']))
                        {
                            if (($p = strpos($url, $item['name'].'=')) !== false)
                            {
                                $url = substr($url, 0, $p-1);
                            }
                            $url .= (strpos($url, '?') !== false ? '&' : '?') . $item['name'].'='.$item['value'];
                        }
                        else
                        {
                            $url = Eresus_Kernel::app()->getPage()->url();
                        }
                    }
                    $url = preg_replace('/&(?!amp;)/', '&amp;', $url);
                    $result .= '<a'.$width.(isset($item['class'])?' class="'.$item['class'].'"':'').
                        ' href="'.$url.'">'.$item['caption'].'</a>';
                }
            }
            $result .= "</div>\n";
        }
        return $result;
    }
    //-----------------------------------------------------------------------------

    function renderPages($itemsCount, $itemsPerPage, $pageCount, $Descending = false, $sub_prefix='')
    {
        $prefix = empty($sub_prefix)?str_repeat('sub_', $this->sub):$sub_prefix;
        if ($itemsCount > $itemsPerPage)
        {
            $result = '<div class="admListPages">'.strPages;
            if ($Descending)
            {
                $forFrom = $pageCount;
                $forTo = 0;
                $forDelta = -1;
            }
            else
            {
                $forFrom = 1;
                $forTo = $pageCount+1;
                $forDelta = 1;
            }
            $pageIndex = arg($prefix.'pg') ? arg($prefix.'pg', 'int') : $forFrom;
            for ($i = $forFrom; $i != $forTo; $i += $forDelta)
            {
                if ($i == $pageIndex)
                {
                    $result .= '<span class="selected">&nbsp;'.$i.'&nbsp;</span>';
                }
                else
                {
                    $result .= '<a href="'.$this->url(array($prefix.'pg' => $i)).'">&nbsp;'.$i.'&nbsp;</a>';
                }
            }
            $result .= "</div>\n";
            return $result;
        }
        else
        {
            return '';
        }
    }
    //-----------------------------------------------------------------------------

    /**
     * Отрисовывает таблицу-список на основе описания $table
     *
     * Таблица заполняется данными из БД или из аргумента $values.
     *
     * Описание таблицы. Может включать в себя следующие элементы:
     *
     * - name (string) — Имя таблицы в БД, из которой следует брать данные.
     * - key (string) — Имя ключевого поля таблицы, хранящего идентификатор записи.
     * - sortMode (string) — Имя столбца, по которому надо сортировать записи.
     * - sortDesc (bool) — true для сортировки по убыванию.
     * - columns (array) — Список отображаемых столбцов в таблице. Каждый элемент в свою очередь
     *   должен описываться массивом, могущим включать в себя элементы: name (string) — имя столбца в
     *   БД; caption (string) — заголовок столбца; align (string) — выравнивание данных в ячейке,
     *   возможные значения: left, right, center, justify; replace (array) — таблицы замены значений.
     *   Массив, в котором ключи соответствуют возможным значениям соответствующей ячейки БД, а
     *   значения – тексту, который должен быть подставлен вместо значений в БД.
     * - controls (array) — Элементы управления для каждой строки таблицы, может включать в себя
     *   элементы: delete — ЭУ «Удалить»; edit — ЭУ «Изменить»; toggle — ЭУ «Активность».
     *
     * Значение $sub_prefix будет добавлено к ссылкам из таблицы. Например без префикса, иконка
     * «Изменить» добавляет к URL »&id=NN», с $sub_prefix равным «sub_» будет добавлять »&sub_id=NN».
     *
     * @param array  $table
     * @param array  $values
     * @param string $sub_prefix
     * @return string
     */
    function renderTable($table, $values=null, $sub_prefix='')
    {
        $result = '';
        $prefix = empty($sub_prefix) ? str_repeat('sub_', $this->sub) : $sub_prefix;
        $itemsPerPage = isset($table['itemsPerPage']) ?
            $table['itemsPerPage'] :
            (isset($this->module->settings['itemsPerPage']) ?
                $this->module->settings['itemsPerPage'] :
                0);
        $pagesDesc = isset($table['sortDesc']) ? $table['sortDesc'] : false;
        if (isset($table['tabs']) && count($table['tabs']))
        {
            $result .= $this->renderTabs($table['tabs']);
        }
        if (isset($table['hint']))
        {
            $result .= '<div class="admListHint">'.$table['hint']."</div>\n";
        }
        $sortMode = arg($prefix.'sort') ?
            arg($prefix.'sort', 'word') :
            (isset($table['sortMode'])?$table['sortMode']:'');
        $sortDesc = arg($prefix.'desc') ?
            arg($prefix.'desc', 'int') :
            (arg($prefix.'sort')?'':(isset($table['sortDesc'])?$table['sortDesc']:false));
        if (is_null($values))
        {
            $count = Eresus_CMS::getLegacyKernel()->db->count($table['name'],
                isset($table['condition'])?$table['condition']:'');
            if ($itemsPerPage)
            {
                $pageCount = ((integer) ($count / $itemsPerPage)+(($count % $itemsPerPage) > 0));
                if ($count > $itemsPerPage)
                {
                    $pages = $this->renderPages($count, $itemsPerPage, $pageCount, $pagesDesc, $sub_prefix);
                }
                else
                {
                    $pages = '';
                }
                $page = arg($prefix.'pg') ? arg($prefix.'pg', 'int') : ($pagesDesc ? $pageCount : 1);
            }
            else
            {
                $pageCount = $count;
                $pages = '';
                $page = 1;
            }
            $items = Eresus_CMS::getLegacyKernel()->db->select(
                $table['name'],
                isset($table['condition'])?$table['condition']:'',
                ($sortDesc ? '-' : '').$sortMode,
                '',
                $itemsPerPage,
                ($pagesDesc?($pageCount-$page)*$itemsPerPage:($page-1)*$itemsPerPage)
            );
        }
        else
        {
            $items = $values;
        }

        if (isset($pages))
        {
            $result .= $pages;
        }
        $result .= "<table class=\"admList\">\n".
            '<tr><th style="width: 100px;">'.admControls.
            (isset($table['controls']['position'])?' <a href="'.
                $this->url(array($prefix.'sort' => 'position', $prefix.'desc' => '0')).'" title="'.
                ADM_SORT_POS.'">'.
                img('admin/themes/default/img/ard.gif', ADM_SORT_POS, ADM_SORT_POS).'</a>':'').
            "</th>";
        if (count($table['columns']))
        {
            foreach ($table['columns'] as $column)
            {
                $result .= '<th '.(isset($column['width'])?' style="width: '.$column['width'].'"':'').'>'.
                    (arg($prefix.'sort') == $column['name'] ? '<span class="admSortBy">'.
                        (isset($column['caption'])?$column['caption']:'&nbsp;').
                        '</span>':(isset($column['caption'])?$column['caption']:'&nbsp;')).
                    (isset($table['name'])?
                        ' <a href="'.$this->url(array($prefix.'sort' => $column['name'], $prefix.'desc' => '')).
                        '" title="'.ADM_SORT_ASC.'">'.
                        img('admin/themes/default/img/ard.gif', ADM_SORT_ASC, ADM_SORT_ASC).'</a> '.
                        '<a href="'.$this->url(array($prefix.'sort' => $column['name'], $prefix.'desc' => '1')).
                        '" title="'.ADM_SORT_DESC.'">'.
                        img('admin/themes/default/img/aru.gif', ADM_SORT_DESC, ADM_SORT_DESC).
                        '</a></th>':'');
            }
        }
        $result .= "</tr>\n";
        $url_delete = $this->url(array($prefix.'delete'=>"%s"));
        $url_edit = $this->url(array($prefix.'id'=>"%s"));
        $url_position = $this->url(array($prefix."%s"=>"%s"));
        $url_toggle = $this->url(array($prefix.'toggle'=>"%s"));
        if (count($items))
        {
            foreach ($items as $item)
            {
                $result .= '<tr><td class="ctrl">';

                /* Удаление */
                if (
                    isset($table['controls']['delete']) &&
                    (
                        empty($table['controls']['delete']) ||
                        $this->module->$table['controls']['delete']($item)
                    )
                )
                {
                    $result .= ' <a href="' . sprintf($url_delete, $item[$table['key']]) . '" title="' .
                        ADM_DELETE . '" onclick="return askdel(this)">' .
                        img('admin/themes/default/img/medium/item-delete.png', ADM_DELETE, ADM_DELETE, 16, 16).
                        '</a>';
                }

                /* Изменение */
                if (
                    isset($table['controls']['edit']) &&
                    (
                        empty($table['controls']['edit']) ||
                        $this->module->$table['controls']['edit']($item)
                    )
                )
                {
                    $result .= ' <a href="' . sprintf($url_edit, $item[$table['key']]) . '" title="' .
                        ADM_EDIT .	'">' .
                        img('admin/themes/default/img/medium/item-edit.png', ADM_EDIT, ADM_EDIT, 16, 16).'</a>';
                }

                /* Вверх/вниз */
                if (
                    isset($table['controls']['position']) &&
                    (
                        empty($table['controls']['position']) ||
                        $this->module->$table['controls']['position']($item)
                    ) &&
                    $sortMode == 'position'
                )
                {
                    $result .= ' <a href="' . sprintf($url_position, 'up', $item[$table['key']]) .
                        '" title="' . ADM_UP . '">' .
                        img('admin/themes/default/img/medium/move-up.png', ADM_UP, ADM_UP).'</a>';
                    $result .= ' <a href="' . sprintf($url_position, 'down', $item[$table['key']]) .
                        '" title="' . ADM_DOWN . '">' .
                        img('admin/themes/default/img/medium/move-down.png', ADM_DOWN, ADM_DOWN).'</a>';
                }

                /* Активность */
                if (
                    isset($table['controls']['toggle']) &&
                    (
                        empty($table['controls']['toggle']) ||
                        $this->module->$table['controls']['toggle']($item)
                    )
                )
                {
                    $result .= ' <a href="' . sprintf($url_toggle, $item[$table['key']]) . '" title="' .
                        ($item['active'] ? ADM_DEACTIVATE : ADM_ACTIVATE) . '">' .
                        img('admin/themes/default/img/medium/item-' . ($item['active'] ? 'active':'inactive').
                            '.png', $item['active']?ADM_DEACTIVATE:ADM_ACTIVATE,
                            $item['active']?ADM_DEACTIVATE:ADM_ACTIVATE).'</a>';
                }

                $result .= '</td>';
                # Обрабатываем ячейки данных
                if (count($table['columns']))
                {
                    foreach ($table['columns'] as $column)
                    {
                        $value = isset($column['value']) ?
                            $column['value'] :
                            (isset($item[$column['name']])?$item[$column['name']]:'');
                        if (isset($column['replace']) && count($column['replace']))
                        {
                            $value = array_key_exists($value, $column['replace']) ?
                                $column['replace'][$value] :
                                $value;
                        }
                        if (isset($column['macros']))
                        {
                            preg_match_all('/\$\((.+)\)/U', $value, $matches);
                            if (count($matches[1]))
                            {
                                foreach ($matches[1] as $macros)
                                {
                                    if (isset($item[$macros]))
                                    {
                                        $value = str_replace('$('.$macros.')', encodeHTML($item[$macros]), $value);
                                    }
                                }
                            }
                        }
                        $value = $this->replaceMacros($value);
                        if (isset($column['striptags']))
                        {
                            $value = strip_tags($value);
                        }
                        if (isset($column['function']))
                        {
                            switch ($column['function'])
                            {
                                case 'isEmpty':
                                    $value = empty($value)?strYes:strNo;
                                    break;
                                case 'isNotEmpty':
                                    $value = empty($value)?strNo:strYes;
                                    break;
                                case 'isNull':
                                    $value = is_null($value)?strYes:strNo;
                                    break;
                                case 'isNotNull':
                                    $value = is_null($value)?strNo:strYes;
                                    break;
                                case 'length':
                                    $value = mb_strlen($value);
                                    break;
                            }
                        }
                        if (isset($column['maxlength']) && (mb_strlen($value) > $column['maxlength']))
                        {
                            $value = mb_substr($value, 0, $column['maxlength']).'...';
                        }
                        $style = '';
                        if (isset($column['align']))
                        {
                            $style .= 'text-align: '.$column['align'].';';
                        }
                        if (isset($column['wrap']) && !$column['wrap'])
                        {
                            $style .=  'white-space: nowrap;';
                        }
                        if (!empty($style))
                        {
                            $style = " style=\"$style\"";
                        }
                        $result .= '<td'.$style.'>'.$value.'</td>';
                    }
                }
                $result .= "</tr>\n";
            }
        }
        $result .= "</table>\n";
        if (isset($pages))
        {
            $result .= $pages;
        }
        return $result;
    }
    //-----------------------------------------------------------------------------

    public function renderForm($form, $values=array())
    {
        $result = '';
        if (isset($form['tabs']))
        {
            $result .= $this->renderTabs($form['tabs']);
        }
        $wnd['caption'] = $form['caption'];
        $wnd['width'] = isset($form['width'])?$form['width']:'';
        $wnd['style'] = 'padding: 0px;';

        $form = new Form($form, $values);
        $wnd['body'] = $form->render();
        $result .= $this->window($wnd);
        return $result;
    }

    /**
     * Отрисовывает область контента страницы
     *
     * @param Eresus_CMS_Request $request
     *
     * @throws Eresus_HTTP_Exception_NotFound
     *
     * @return string
     */
    private function renderContent(Eresus_CMS_Request $request)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        $result = '';
        if (arg('mod'))
        {
            $module = arg('mod', '/[^\w-]/');
            if (substr($module, 0, 4) == 'ext-')
            {
                $name = substr($module, 4);
                /** @var Eresus_Plugin_Registry $plugins */
                $plugins = $this->container->get('plugins');
                $plugin = $plugins->load($name);
                if (false === $plugin)
                {
                    throw new Eresus_HTTP_Exception_NotFound();
                }
                $provider = new Eresus_Admin_ContentProvider_Plugin($plugin);
                $this->module = $plugin;
            }
            else
            {
                $class = 't' . $module;
                if (!class_exists($class))
                {
                    throw new Eresus_HTTP_Exception_NotFound();
                }
                $module = new $class;
                $provider = new Eresus_Admin_ContentProvider_Module($module);
                $this->module = $module;
            }

            $module = $provider->getModule();
            if ($module instanceof ContainerAwareInterface)
            {
                $module->setContainer($this->container);
            }

            try
            {
                $result = $provider->adminRender($request);
            }
            catch (RuntimeException $e)
            {
                if ($e instanceof Eresus_CMS_Exception_NotFound)
                {
                    $result = ErrorBox($e->getMessage() ?: 'Запрошенная страница не найдена.');
                }
                else
                {
                    Eresus_Kernel::logException($e);
                    $result = ErrorBox($e->getMessage());
                }
            }
        }

        if (
            isset(Eresus_CMS::getLegacyKernel()->session['msg']['information']) &&
            count(Eresus_CMS::getLegacyKernel()->session['msg']['information'])
        )
        {
            $messages = '';
            foreach (Eresus_CMS::getLegacyKernel()->session['msg']['information'] as $message)
            {
                $messages .= InfoBox($message);
            }
            $result = $messages . $result;
            Eresus_CMS::getLegacyKernel()->session['msg']['information'] = array();
        }
        if (
            isset(Eresus_CMS::getLegacyKernel()->session['msg']['errors']) &&
            count(Eresus_CMS::getLegacyKernel()->session['msg']['errors']))
        {
            $messages = '';
            foreach (Eresus_CMS::getLegacyKernel()->session['msg']['errors'] as $message)
            {
                $messages .= ErrorBox($message);
            }
            $result = $messages . $result;
            Eresus_CMS::getLegacyKernel()->session['msg']['errors'] = array();
        }
        return $result;
    }

    /**
     * Отрисовывает ветку меню
     *
     * @param $opened
     * @param $owner
     * @param $level
     */
    private function renderPagesMenu(&$opened, $owner = 0, $level = 0)
    {
        $theme = $this->getUITheme();

        $result = '';
        $items = Eresus_CMS::getLegacyKernel()->
            sections->children($owner, Eresus_CMS::getLegacyKernel()->user['access'], SECTIONS_ACTIVE);

        if (count($items))
        {
            foreach ($items as $item)
            {
                if (empty($item['caption']))
                {
                    $item['caption'] = ADM_NA;
                }

                if (
                    isset(Eresus_CMS::getLegacyKernel()->request['arg']['section']) &&
                    $item['id'] == arg('section')
                )
                {
                    $this->title = $item['caption']; # title - массив?
                }

                $sub = $this->renderPagesMenu($opened, $item['id'], $level+1);
                $current = (arg('mod') == 'content') && (arg('section') == $item['id']);

                if ($current)
                {
                    $opened = $level;
                }

                // Альтернативный текст
                $alt = '[&nbsp;]';
                // Подсказка
                $title = '';
                // Классы пункта меню
                $classes = array();

                if ($opened == $level + 1)
                {
                    $display = 'block';
                    $classes []= 'opened';
                    $opened--;
                }
                else
                {
                    $display = 'none';
                }

                if ($sub)
                {
                    $classes []= 'parrent';
                    $alt = '[+]';
                    $title = 'Развернуть';
                }

                if ($current)
                {
                    $classes []= 'current';
                }

                if (!$item['visible'])
                {
                    $classes []= 'invisible';
                }

                $classes = implode(' ', $classes);

                $result .=
                    '<li' . ($classes ? ' class="' . $classes . '"' : '') . '>' .
                    '<img src="' . Eresus_CMS::getLegacyKernel()->root . $theme->getImage('dot.gif') . '" alt="' .
                    $alt . '" title="' . $title . '" /> ' .
                    '<a href="' . Eresus_CMS::getLegacyKernel()->root . 'admin.php?mod=content&amp;section=' .
                    $item['id'] .
                    '" title="ID: '.$item['id'].' ('.$item['name'].')">'.$item['caption']."</a>\n";

                if (!empty($sub))
                {
                    $result .= '<ul style="display: '.$display.';">'.$sub.'</ul>';
                }
            }
        }

        return $result;
    }
    //-----------------------------------------------------------------------------

    /**
     * Отрисовывает меню плагинов и управления
     *
     * @return string  HTML
     */
    private function renderControlMenu()
    {
        $menu = '';
        for ($section = 0; $section < count($this->extmenu); $section++)
        {
            if (UserRights($this->extmenu[$section]['access']))
            {
                $menu .= '<div class="header">' . $this->extmenu[$section]['caption'] .
                    '</div><div class="content">';
                foreach ($this->extmenu[$section]['items'] as $item)
                {
                    if (
                        UserRights(
                            isset($item['access']) ? $item['access'] : $this->extmenu[$section]['access']
                        ) &&
                        (!(isset($item['disabled']) && $item['disabled']))
                    )
                    {
                        if ($item['link'] == arg('mod'))
                        {
                            $this->title = $item['caption'];
                        }
                        $menu .= '<div ' . ($item['link'] == arg('mod') ? 'class="selected"' : '') .
                            "><a href=\"" . Eresus_CMS::getLegacyKernel()->root . "admin.php?mod=" . $item['link'] .
                            "\" title=\"" .	$item['hint'] . "\">" . $item['caption'] . "</a></div>\n";
                    }
                }
                $menu .= "</div>\n";
            }
        }

        for ($section = 0; $section < count($this->menu); $section++)
        {
            if (UserRights($this->menu[$section]['access']))
            {
                $menu .= '<div class="header">' . $this->menu[$section]['caption'] .
                    '</div><div class="content">';
                foreach ($this->menu[$section]['items'] as $item)
                {
                    if (
                        UserRights(
                            isset($item['access']) ? $item['access'] : $this->menu[$section]['access']
                        ) &&
                        (!(isset($item['disabled']) && $item['disabled']))
                    )
                    {
                        if ($item['link'] == arg('mod'))
                        {
                            $this->title = $item['caption'];
                        }
                        $menu .= '<div '.($item['link'] == arg('mod') ?'class="selected"':'') .
                            "><a href=\"" . Eresus_CMS::getLegacyKernel()->root . "admin.php?mod=" . $item['link'] .
                            "\" title=\"" .	$item['hint'] . "\">" . $item['caption'] . "</a></div>\n";
                    }
                }
                $menu .= "</div>\n";
            }
        }

        return $menu;
    }

    /**
     * Отправляет созданную страницу пользователю
     *
     * @param Eresus_CMS_Request $request
     *
     * @return Eresus_HTTP_Response
     */
    public function render(Eresus_CMS_Request $request)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');
        return $this->renderUI($request);
    }

    /**
     * Отрисовка интерфейса
     *
     * @param Eresus_CMS_Request $request
     *
     * @return Eresus_HTTP_Response
     */
    private function renderUI(Eresus_CMS_Request $request)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');
        $response = $this->renderContent($request);

        if (!($response instanceof Eresus_HTTP_Response))
        {
            $data = array();
            $data['page'] = $this;
            $data['content'] = $response;
            $data['siteName'] = option('siteName');
            $data['body'] = $this->renderBodySection();
            $opened = -1;
            $data['sectionMenu'] = $this->renderPagesMenu($opened);
            $data['controlMenu'] = $this->renderControlMenu();
            $data['user'] = Eresus_CMS::getLegacyKernel()->user;

            $tmpl = new Eresus_Template('admin/themes/default/page.default.html');
            $response = new Eresus_HTTP_Response($tmpl->compile($data), 200, $this->headers);
        }

        return $response;
    }
}

