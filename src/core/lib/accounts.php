<?php
/**
 * Работа с учётными записями пользователей
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
 * Работа с учётными записями пользователей
 * @package Eresus
 */
class EresusAccounts
{
    /**
     * Имя таблицы БД
     * @var string
     */
    private $table = 'users';

    /**
     * Кэш
     * @var array
     */
    private $cache = array();

    /**
     * Возвращает список полей
     *
     * @access public
     *
     * @return array Список полей
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
     * Возвращает учётную запись или список записей
     *
     * @access public
     *
     * @param int|array|string $id  ID пользователя, список идентификаторов или SQL-условие
     *
     * @return array
     */
    public function get($id)
    {
        if (is_array($id))
        {
            $what = "FIND_IN_SET(`id`, '".implode(',', $id)."')";
        }
        elseif (is_numeric($id))
        {
            $what = "`id`=$id";
        }
        else
        {
            $what = $id;
        }
        $result = Eresus_CMS::getLegacyKernel()->db->select($this->table, $what);
        if ($result)
        {
            for ($i=0; $i<count($result); $i++)
            {
                $result[$i]['profile'] = decodeOptions($result[$i]['profile']);
            }
        }
        if (is_numeric($id) && $result && count($result))
        {
            $result = $result[0];
        }
        return $result;
    }

    /**
     * @param $name
     * @return array
     */
    public function getByName($name)
    {
        return $this->get("`login` = '$name'");
    }

    /**
     * Добавляет учётную запись
     *
     * @param array $item Учётная запись
     *
     * @return mixed Описание записи или false в случае неудачи
     */
    public function add($item)
    {
        $result = false;
        if (isset($item['id'])) unset($item['id']);
        if (!isset($item['profile'])) $item['profile'] = array();
        $item['profile'] = encodeOptions($item['profile']);
        if (Eresus_CMS::getLegacyKernel()->db->insert($this->table, $item))
            $result = $this->get(Eresus_CMS::getLegacyKernel()->db->getInsertedId());
        return $result;
    }

    /**
     * Изменяет учётную запись
     *
     * @param array $item Учётная запись
     *
     * @return mixed Описание изменённой записи или false в случае неудачи
     */
    public function update($item)
    {
        $item['profile'] = encodeOptions($item['profile']);
        $result = Eresus_CMS::getLegacyKernel()->db->
            updateItem($this->table, $item, "`id`={$item['id']}");
        return $result;
    }

    /**
     * Удаляет учётную запись
     *
     * @access public
     *
     * @param int $id Идентификатор записи
     *
     * @return bool Результат операции
     */
    public function delete($id)
    {
        $result = Eresus_CMS::getLegacyKernel()->db->delete($this->table, "`id`=$id");
        return $result;
    }
}

