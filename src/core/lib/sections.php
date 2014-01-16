<?php
/**
 * Работа с разделами сайта
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
 * @subpackage DomainModel
 */

/**
 * Активные разделы
 * @var int
 */
define('SECTIONS_ACTIVE', 0x0001);

/**
 * Видимые разделы
 * @var int
 */
define('SECTIONS_VISIBLE', 0x0002);

/**
 * Работа с разделами сайта
 *
 * Необязательно создавать экземпляр этого класса самостоятельно, можно использовать экземпляр
 * из {@link Eresus::$sections}:
 *
 * <code>
 * $sections = Eresus_Kernel::app()->getLegacyKernel()->sections;
 * </code>
 *
 * @package Eresus
 * @subpackage DomainModel
 *
 * @since 2.10
 */
class Sections
{
    /**
     * Имя таблицы разделов
     *
     * @var string
     */
    private $table = 'pages';

    /**
     * Индекс разделов
     *
     * @var array
     */
    private $index = array();

    /**
     * Кэш
     *
     * @var array
     */
    private $cache = array();

    /**
     * Создаёт индекс разделов
     *
     * @param bool $force  Игнорировать закэшированные данные
     * @return void
     */
    private function index($force = false)
    {
        if ($force || !$this->index)
        {
            $items = Eresus_CMS::getLegacyKernel()->db->
                select($this->table, '', '`position`', '`id`,`owner`');
            if ($items)
            {
                $this->index = array();
                foreach ($items as $item)
                {
                    $this->index[$item['owner']] []= $item['id'];
                }
            }
        }
    }

    /**
     * Создаёт список ID разделов определённой ветки
     *
     * @param int $owner  ID корневого раздела ветки
     * @return array  Список ID разделов
     */
    private function branchIds($owner)
    {
        $result = array();
        if (isset($this->index[$owner]))
        {
            $result = $this->index[$owner];
            foreach ($result as $section)
            {
                $result = array_merge($result, $this->branchIds($section));
            }
        }
        return $result;
    }

    /**
     * Выбирает разделы определённой ветки
     *
     * @param int $owner   Идентификатор корневого раздела ветки
     * @param int $access  Минимальный уровень доступа
     * @param int $flags   Флаги (см. SECTIONS_XXX)
     *
     * @return array  Описания разделов
     */
    public function branch($owner, $access = GUEST, $flags = 0)
    {
        $result = array();
        // Создаём индекс
        if (!$this->index)
        {
            $this->index();
        }
        // Находим ID разделов ветки.
        $set = $this->branchIds($owner);
        if (count($set))
        {
            $list = array();
            /* Читаем из кэша */
            for ($i=0; $i < count($set); $i++)
            {
                if (isset($this->cache[$set[$i]]))
                {
                    $list[] = $this->cache[$set[$i]];
                    array_splice($set, $i, 1);
                    $i--;
                }
            }
            if (count($set))
            {
                $fieldset = '';//implode(',', array_diff($this->fields(), array('content')));
                /* Читаем из БД */
                $set = implode(',', $set);
                $items = Eresus_CMS::getLegacyKernel()->db->select($this->table,
                    "FIND_IN_SET(`id`, '$set') AND `access` >= $access", 'position', $fieldset);
                for ($i=0; $i<count($items); $i++)
                {
                    $this->cache[$items[$i]['id']] = $items[$i];
                    $list[] = $items[$i];
                }
            }

            if ($flags)
            {
                for ($i=0; $i<count($list); $i++)
                {
                    /* Фильтруем с учётом переданных флагов */
                    $filterByActivePassed = !($flags & SECTIONS_ACTIVE) || $list[$i]['active'];
                    $filterByVisiblePassed = !($flags & SECTIONS_VISIBLE) || $list[$i]['visible'];

                    if ($filterByActivePassed && $filterByVisiblePassed)
                    {
                        $result[] = $list[$i];
                    }
                }
            }
            else
            {
                $result = $list;
            }
        }
        return $result;
    }

    /**
     * Возвращает идентификаторы дочерних разделов указанного
     *
     * @param int $owner   Идентификатор корневого раздела ветки
     * @param int $access  Минимальный уровень доступа
     * @param int $flags   Флаги (см. SECTIONS_XXX)
     *
     * @return array  Идентификаторы разделов
     *
     * @since 2.10
     */
    public function children($owner, $access = GUEST, $flags = 0)
    {
        $items = $this->branch($owner, $access, $flags);
        $result = array();
        for ($i=0; $i<count($items); $i++)
        {
            if ($items[$i]['owner'] == $owner)
            {
                $result[] = $items[$i];
            }
        }
        return $result;
    }

    /**
     * Возвращает идентификаторы всех родительских разделов указанного
     *
     * @param int $id   Идентификатор раздела
     *
     * @return array  Идентификаторы разделов или NULL если раздела $id не существует
     */
    public function parents($id)
    {
        $this->index();
        $result = array();
        while ($id)
        {
            foreach ($this->index as $key => $value)
            {
                if (in_array($id, $value))
                {
                    $result[] = $id = $key;
                    break;
                }
            }
            if (!$result)
            {
                return null;
            }
        }
        $result = array_reverse($result);
        return $result;
    }

    /**
     * Возвращает список полей
     *
     * @return  array  Список полей
     */
    public function fields()
    {
        if (isset($this->cache['fields']))
        {
            $result = $this->cache['fields'];
        }
        else
        {
            $result = Eresus_CMS::getLegacyKernel()->db->fields($this->table);
            $this->cache['fields'] = $result;
        }
        return $result;
    }

    /**
     * Возвращает раздел или список разделов
     *
     * * Если $what — целое число, то будет возвращён раздел с указанным идентификатором.
     * * Если $what — массив, то будут возвращены разделы с идентификаторами из этого массива.
     * * Если $what — строка, то она будет использована как условие WHERE в запросе SQL.
     *
     * Описание раздела возвращается в виде ассоциативного массива, где ключами выступают имена
     * полей таблицы pages БД.
     *
     * @param int|array|string $what  ID раздела / список идентификаторов / условие SQL
     *
     * @return array|bool  описание раздела (разделов) или false, если ничего не найдено
     */
    public function get($what)
    {
        if (is_array($what))
        {
            $where = "FIND_IN_SET(`id`, '".implode(',', $what)."')";
        }
        elseif (is_numeric($what))
        {
            $where = "`id`=$what";
        }
        else
        {
            $where = $what;
        }
        $result = Eresus_CMS::getLegacyKernel()->db->select($this->table, $where);
        if ($result)
        {
            for ($i=0; $i<count($result); $i++)
            {
                $result[$i]['options'] = decodeOptions($result[$i]['options']);
            }
        }
        if (is_numeric($what) && $result && count($result))
        {
            $result = $result[0];
        }

        return $result;
    }

    /**
     * Добавляет раздел
     *
     * @param array $item  Массив свойств раздела
     * @return mixed  Описание нового раздела или FALSE в случае неудачи
     */
    public function add($item)
    {
        if (!$this->index)
        {
            $this->index();
        }

        $result = false;
        /* При добавлении свойство id должно устанавливаться БД */
        if (isset($item['id']))
        {
            unset($item['id']);
        }
        /* Если не указан родитель - добавляем в корень */
        if (!isset($item['owner']))
        {
            $item['owner'] = 0;
        }
        $item['created'] = gettime('Y-m-d H:i:s');
        $item['updated'] = $item['created'];
        $item['options'] = isset($item['options']) ? trim($item['options']) : '';
        $item['options'] = empty($item['options']) ?
            '' : encodeOptions(text2array($item['options'], true));

        if (!isset($item['position']) || !$item['position'])
        {
            $item['position'] = isset($this->index[$item['owner']]) ?
                count($this->index[$item['owner']]) : 0;
        }
        if (Eresus_CMS::getLegacyKernel()->db->insert($this->table, $item))
        {
            $result = $this->get(Eresus_CMS::getLegacyKernel()->db->getInsertedId());
        }
        return $result;
    }

    /**
     * Изменяет раздел
     *
     * @param  array  $item  Раздел
     *
     * @return  mixed  Описание нового раздела или false в случае неудачи
     */
    public function update($item)
    {
        $item['updated'] = gettime('Y-m-d H:i:s');
        $item['options'] = encodeOptions($item['options']);
        $item['title'] = Eresus_CMS::getLegacyKernel()->db->escape($item['title']);
        $item['caption'] = Eresus_CMS::getLegacyKernel()->db->escape($item['caption']);
        $item['description'] = Eresus_CMS::getLegacyKernel()->db->escape($item['description']);
        $item['hint'] = Eresus_CMS::getLegacyKernel()->db->escape($item['hint']);
        $item['keywords'] = Eresus_CMS::getLegacyKernel()->db->escape($item['keywords']);
        $item['content'] = Eresus_CMS::getLegacyKernel()->db->escape($item['content']);
        $item['options'] = Eresus_CMS::getLegacyKernel()->db->escape($item['options']);
        $result = Eresus_CMS::getLegacyKernel()->db->updateItem($this->table, $item, "`id`={$item['id']}");
        return $result;
    }

    /**
     * Удаляет раздел и подразделы
     *
     * @param int $id  Идентификатор раздела
     *
     * @return bool  Результат операции
     */
    public function delete($id)
    {
        $children = $this->children($id);
        /* Удаляем подразделы */
        for ($i = 0; $i < count($children); $i++)
        {
            $this->delete($children[$i]['id']);
        }

        /* Удаляем контент раздела */
        $section = $this->get($id);
        $plugins = Eresus_Plugin_Registry::getInstance();
        $plugin = $plugins->load($section['type']);
        //TODO https://github.com/Eresus/EresusCMS/issues/29
        if (false !== $plugin)
        {
            if (method_exists($plugin, 'onSectionDelete'))
            {
                $plugin->onSectionDelete($id);
            }
        }
        Eresus_CMS::getLegacyKernel()->db->delete($this->table, "`id`=$id");
        return true; // Оставлено для обратной совместимости
    }
}

