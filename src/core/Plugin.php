<?php
/**
 * Родительский класс для всех плагинов
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
 * Родительский класс для всех плагинов
 *
 * @package Eresus
 */
abstract class Eresus_Plugin
{
    /**
     * Имя плагина
     *
     * @var string
     * @deprecated с 3.01 используйте {@link getName()}
     * @todo сделать приватным
     */
    public $name = null;

    /**
     * Версия плагина
     *
     * Потомки должны перекрывать это своим значением
     *
     * @var string
     */
    public $version = 'n/a';

    /**
     * Необходимая версия Eresus
     *
     * Потомки могут перекрывать это своим значением
     *
     * @var string
     */
    public $kernel = '3.00';

    /**
     * Название плагина
     *
     * Потомки должны перекрывать это своим значением
     *
     * @var string
     */
    public $title = 'no title';

    /**
     * Описание плагина
     *
     * Потомки должны перекрывать это своим значением
     *
     * @var string
     */
    public $description = '';

    /**
     * Настройки плагина
     *
     * Потомки могут перекрывать это своим значением
     *
     * @var array
     */
    public $settings = array();

    /**
     * Директория данных
     *
     * /data/имя_плагина
     *
     * @var string
     */
    protected $dirData;

    /**
     * URL данных
     *
     * @var string
     */
    protected $urlData;

    /**
     * Директория скриптов
     *
     * /ext/имя_плагина
     *
     * @var string
     */
    protected $dirCode;

    /**
     * URL скриптов
     *
     * @var string
     */
    protected $urlCode;

    /**
     * Директория оформления
     *
     * style/имя_плагина
     *
     * @var string
     */
    protected $dirStyle;

    /**
     * URL оформления
     *
     * @var string
     */
    protected $urlStyle;

    /**
     * Шаблоны плагина
     * @var Eresus_Plugin_Templates
     * @since 3.01
     */
    private $templates = null;

    /**
     * Конструктор
     *
     * Производит чтение настроек плагина и подключение языковых файлов
     *
     * @uses $locale
     * @uses FS::isFile
     * @uses Core::safeInclude
     * @uses Plugin::resetPlugin
     */
    public function __construct()
    {
        global $locale;

        $legacyKernel = Eresus_CMS::getLegacyKernel();
        $plugins = Eresus_Plugin_Registry::getInstance();
        if (array_key_exists($this->getName(), $plugins->list))
        {
            $info = $plugins->list[$this->getName()];
            $this->settings = decodeOptions($info['settings'], $this->settings);
            /*
             * Если установлена версия плагина отличная от установленной ранее, то необходимо
             * произвести обновление информации о плагине в БД
             */
            if ($this->version != $info['version'])
            {
                $this->resetPlugin();
            }
        }
        $this->dirData = $legacyKernel->fdata . $this->getName() . '/';
        $this->urlData = $legacyKernel->data . $this->getName() . '/';
        $this->dirCode = $legacyKernel->froot . 'ext/' . $this->getName() . '/';
        $this->urlCode = $legacyKernel->root . 'ext/' . $this->getName() . '/';
        $this->dirStyle = $legacyKernel->fstyle . $this->getName() . '/';
        $this->urlStyle = $legacyKernel->style . $this->getName() . '/';
        $filename = $legacyKernel->froot . 'lang/' . $this->getName() . '/' . $locale['lang']
            . '.php';
        if (file_exists($filename))
        {
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
        }
    }

    /**
     * Возвращает информацию о плагине
     *
     * @param  array  $item  Предыдущая версия информации (по умолчанию null)
     *
     * @return  array  Массив информации, пригодный для записи в БД
     */
    public function __item($item = null)
    {
        $result['name'] = $this->getName();
        $result['content'] = '0';
        $result['active'] = is_null($item) ? true : $item['active'];
        $result['settings'] = Eresus_CMS::getLegacyKernel()->db->
            escape(is_null($item) ? encodeOptions($this->settings) : $item['settings']);
        $result['title'] = $this->title;
        $result['version'] = $this->version;
        $result['description'] = $this->description;
        return $result;
    }

    /**
     * Перехватчик обращений к несуществующим методам плагинов
     *
     * @param string $method  Имя вызванного метода
     * @param array  $args    Переданные аргументы
     *
     * @throws LogicException
     */
    public function __call($method, $args)
    {
        throw new LogicException("Method \"$method\" does not exists in class \"" . get_class($this)
            . "\"");
    }

    /**
     * Возвращает имя плагина
     *
     * @return string
     * @since 3.01
     */
    public function getName()
    {
        if (null === $this->name)
        {
            $this->name = strtolower(get_class($this));
        }
        return $this->name;
    }

    /**
     * Возвращает файловый путь к папке данных плагина
     *
     * Обратите внимание, что путь НЕ заканчивается слэшем
     *
     * @return string
     * @since 3.01
     */
    public function getDataDir()
    {
        return rtrim($this->dirData, '/');
    }

    /**
     * Возвращает URL директории данных плагина
     *
     * @return string
     *
     * @since 2.15
     */
    public function getDataUrl()
    {
        return $this->urlData;
    }

    /**
     * Возвращает файловый путь к папке файлов плагина
     *
     * Обратите внимание, что путь НЕ заканчивается слэшем
     *
     * @return string
     * @since 3.01
     */
    public function getCodeDir()
    {
        return rtrim($this->dirCode, '/');
    }

    /**
     * Возвращает URL директории файлов плагина
     *
     * @return string
     *
     * @since 2.15
     */
    public function getCodeUrl()
    {
        return $this->urlCode;
    }

    /**
     * Возвращает файловый путь к папке стилей плагина
     *
     * Обратите внимание, что путь НЕ заканчивается слэшем
     *
     * @return string
     * @since 3.01
     */
    public function getStyleDir()
    {
        return rtrim($this->dirStyle, '/');
    }

    /**
     * Возвращает URL директории стилей плагина
     *
     * @return string
     *
     * @since 2.15
     */
    public function getStyleUrl()
    {
        return $this->urlStyle;
    }

    /**
     * Действия, выполняемые при инсталляции плагина
     */
    public function install()
    {
        $this->installTemplates();
    }

    /**
     * Действия, выполняемые при деинсталляции плагина
     */
    public function uninstall()
    {
        $this->cleanupDB();
        $this->uninstallTemplates();
    }

    /**
     * Действия при изменении настроек
     */
    public function onSettingsUpdate()
    {
    }

    /**
     * Сохраняет в БД изменения настроек плагина
     */
    public function updateSettings()
    {
        foreach ($this->settings as $key => &$value)
        {
            if (!is_null(arg($key)))
            {
                $value = arg($key);
            }
        }
        $this->onSettingsUpdate();
        $this->saveSettings();
    }

    /**
     * Производит выборку из таблицы БД
     *
     * @param string	$table				Имя таблицы (пустое значение - таблица по умолчанию)
     * @param string	$condition		Условие выборки
     * @param string	$order				Порядок выборки
     * @param string	$fields				Список полей
     * @param int			$limit				Вернуть не больше полей чем limit
     * @param int			$offset				Смещение выборки
     * @param bool		$distinct			Только уникальные результаты
     *
     * @return array|bool  Выбранные элементы в виде массива или FALSE в случае ошибки
     */
    public function dbSelect($table = '', $condition = '', $order = '', $fields = '', $limit = 0,
                             $offset = 0, $group = '', $distinct = false)
    {
        $result = Eresus_CMS::getLegacyKernel()->db->select($this->__table($table), $condition, $order,
            $fields, $limit, $offset, $group, $distinct);

        return $result;
    }
    //------------------------------------------------------------------------------

    /**
     * Получение записи из БД
     *
     * @param string $table  Имя таблицы
     * @param mixed  $id   	 Идентификатор элемента
     * @param string $key    Имя ключевого поля
     *
     * @return array Элемент
     */
    public function dbItem($table, $id, $key = 'id')
    {
        $result = Eresus_CMS::getLegacyKernel()->db->selectItem($this->__table($table),
            "`$key` = '$id'");

        return $result;
    }
    //------------------------------------------------------------------------------

    /**
     * Вставка в таблицу БД
     *
     * @param string $table          Имя таблицы
     * @param array  $item           Вставляемый элемент
     * @param string $key[optional]  Имя ключевого поля. По умолчанию "id"
     */
    public function dbInsert($table, $item, $key = 'id')
    {
        Eresus_CMS::getLegacyKernel()->db->insert($this->__table($table), $item);
        $result = $this->dbItem($table, Eresus_CMS::getLegacyKernel()->db->getInsertedId(), $key);

        return $result;
    }
    //------------------------------------------------------------------------------

    /**
     * Изменение данных в БД
     *
     * @param string $table      Имя таблицы
     * @param mixed  $data       Изменяемый эелемент / Изменения
     * @param string $condition  Ключевое поле / Условие для замены
     *
     * @return bool Результат
     */
    public function dbUpdate($table, $data, $condition = '')
    {
        if (is_array($data))
        {
            if (empty($condition)) $condition = 'id';
            $result = Eresus_CMS::getLegacyKernel()->db->
                updateItem($this->__table($table), $data, "`$condition` = '{$data[$condition]}'");
        }
        elseif (is_string($data))
        {
            $result = Eresus_CMS::getLegacyKernel()->db->
                update($this->__table($table), $data, $condition);
        }
        else
        {
            $result = false;
        }

        return $result;
    }
    //------------------------------------------------------------------------------

    /**
     * Удаление элемента из БД
     *
     * @param string $table  Имя таблицы
     * @param mixed  $item   Удаляемый элемент / Идентификатор
     * @param string $key    Ключевое поле
     *
     * @return bool Результат
     */
    public function dbDelete($table, $item, $key = 'id')
    {
        $result = Eresus_CMS::getLegacyKernel()->db->
            delete($this->__table($table), "`$key` = '".(is_array($item)? $item[$key] : $item)."'");

        return $result;
    }
    //------------------------------------------------------------------------------

    /**
     * Подсчёт количества записей в БД
     *
     * @param string $table      Имя таблицы
     * @param string $condition  Условие для включения в подсчёт
     *
     * @return int Количество записей, удовлетворяющих условию
     */
    public function dbCount($table, $condition = '')
    {
        $result = Eresus_CMS::getLegacyKernel()->db->count($this->__table($table), $condition);

        return $result;
    }
    //------------------------------------------------------------------------------

    /**
     * Получение информации о таблицах
     *
     * @param string $table  Маска имени таблицы
     * @param string $param  Вернуть только указанный параметр
     *
     * @return mixed
     */
    public function dbTable($table, $param = '')
    {
        $result = Eresus_CMS::getLegacyKernel()->db->tableStatus($this->__table($table), $param);

        return $result;
    }

    /**
     * Возвращает объект для работы с шаблонами плагина
     *
     * @return Eresus_Plugin_Templates|null
     * @since 3.01
     */
    public function templates()
    {
        if (null === $this->templates)
        {
            $this->templates = new Eresus_Plugin_Templates($this);
        }
        return $this->templates;
    }

    /**
     * Чтение настроек плагина из БД
     *
     * @return bool  Результат выполнения
     */
    protected function loadSettings()
    {
        $result = Eresus_CMS::getLegacyKernel()->db
            ->selectItem('plugins', "`name`='" . $this->getName() . "'");
        if ($result)
        {
            $this->settings = decodeOptions($result['settings'], $this->settings);
        }
        return (bool) $result;
    }

    /**
     * Сохранение настроек плагина в БД
     *
     * @return bool  Результат выполнения
     */
    protected function saveSettings()
    {
        $result = Eresus_CMS::getLegacyKernel()->db
            ->selectItem('plugins', "`name`='{$this->getName()}'");
        $result = $this->__item($result);
        $result['settings'] = Eresus_CMS::getLegacyKernel()->db
            ->escape(encodeOptions($this->settings));
        $result = Eresus_CMS::getLegacyKernel()->db->
            updateItem('plugins', $result, "`name`='".$this->getName()."'");

        return $result;
    }

    /**
     * Обновление данных о плагине в БД
     */
    protected function resetPlugin()
    {
        $this->loadSettings();
        $this->saveSettings();
    }

    /**
     * Регистрация обработчиков событий
     *
     * @param string ...  имена событий
     * @deprecated с 3.01 используйте {@link Eresus_Event_Dispatcher::addEventListener()}
     */
    protected function listenEvents()
    {
        $registry = Eresus_Plugin_Registry::getInstance();
        for ($i=0; $i < func_num_args(); $i++)
        {
            $event = func_get_arg($i);
            if (!array_key_exists($event, $registry->events))
            {
                $registry->events[$event] = array();
            }
            $registry->events[$event] []= $this->getName();
        }
    }

    /**
     * Устанавливает шаблоны КИ в общую папку шаблонов
     */
    protected function installTemplates()
    {
        $path = $this->getCodeDir() . '/client/templates';
        if (file_exists($path))
        {
            $ts = Eresus_Template_Service::getInstance();
            $it = new DirectoryIterator($path);
            foreach ($it as $fileInfo)
            {
                /** @var DirectoryIterator $fileInfo */
                if (!$fileInfo->isDot())
                {
                    $ts->install($fileInfo->getPathname(), $this->getName());
                }
            }
        }
    }

    /**
     * Удаляет шаблоны КИ из общей папки шаблонов
     */
    protected function uninstallTemplates()
    {
        $path = $this->getCodeDir() . '/client/templates';
        if (file_exists($path))
        {
            $ts = Eresus_Template_Service::getInstance();
            $ts->remove($this->getName());
        }
    }

    /**
     * Удаляет таблицы БД при удалении плагина
     *
     * @since 3.01
     */
    protected function cleanupDB()
    {
        $eresus = Eresus_CMS::getLegacyKernel();
        $tables = $eresus->db
            ->query_array("SHOW TABLES LIKE '{$eresus->db->prefix}{$this->getName()}_%'");
        $tables = array_merge($tables, $eresus->db->
            query_array("SHOW TABLES LIKE '{$eresus->db->prefix}{$this->getName()}'"));
        for ($i = 0; $i < count($tables); $i++)
        {
            $this->dbDropTable(substr(current($tables[$i]), strlen($this->getName()) + 1));
        }
    }

    /**
     * Замена макросов
     *
     * @param  string  $template  Строка в которой требуется провести замену макросов
     * @param  mixed   $item      Ассоциативный массив со значениями для подстановки вместо макросов
     *
     * @return  string  Обработанная строка
     */
    protected function replaceMacros($template, $item)
    {
        $result = replaceMacros($template, $item);
        return $result;
    }

    /**
     * Создание новой директории
     *
     * @param string $name Имя директории
     * @return bool Результат
     */
    protected function mkdir($name = '')
    {
        $result = true;
        $umask = umask(0000);
        # Проверка и создание корневой директории данных
        if (!is_dir($this->dirData)) $result = mkdir($this->dirData);
        if ($result)
        {
            # Удаляем директории вида "." и "..", а также финальный и лидирующий слэши
            $name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
            if ($name)
            {
                $name = explode('/', $name);
                $root = substr($this->dirData, 0, -1);
                for($i=0; $i<count($name); $i++) if ($name[$i]) {
                    $root .= '/'.$name[$i];
                    if (!is_dir($root)) $result = mkdir($root);
                    if (!$result) break;
                }
            }
        }
        umask($umask);
        return $result;
    }

    /**
     * Удаление директории и файлов
     *
     * @param string $name Имя директории
     * @return bool Результат
     */
    protected function rmdir($name = '')
    {
        $result = true;
        $name = preg_replace(array('!\.{1,2}/!', '!^/!', '!/$!'), '', $name);
        $name = $this->dirData.$name;
        if (is_dir($name))
        {
            $files = glob($name.'/{.*,*}', GLOB_BRACE);
            for ($i = 0; $i < count($files); $i++)
            {
                if (substr($files[$i], -2) == '/.' || substr($files[$i], -3) == '/..') continue;
                if (is_dir($files[$i])) $result = $this->rmdir(substr($files[$i], strlen($this->dirData)));
                elseif (is_file($files[$i])) $result = filedelete($files[$i]);
                if (!$result) break;
            }
            if ($result) $result = rmdir($name);
        }
        return $result;
    }

    /**
     * Возвращает реальное имя таблицы
     *
     * @param string $table  Локальное имя таблицы
     * @return string Реальное имя таблицы
     */
    protected function __table($table)
    {
        return $this->getName() . (empty($table)?'':'_'.$table);
    }

    /**
     * Создание таблицы в БД
     *
     * @param string $SQL Описание таблицы
     * @param string $name Имя таблицы
     *
     * @return bool Результат выполнения
     */
    protected function dbCreateTable($SQL, $name = '')
    {
        $result = Eresus_CMS::getLegacyKernel()->db->create($this->__table($name), $SQL);
        return $result;
    }

    /**
     * Удаление таблицы БД
     *
     * @param string $name Имя таблицы
     *
     * @return bool Результат выполнения
     */
    protected function dbDropTable($name = '')
    {
        $result = Eresus_CMS::getLegacyKernel()->db->drop($this->__table($name));
        return $result;
    }
}

