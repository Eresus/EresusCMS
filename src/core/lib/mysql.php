<?php
/**
 * ${product.title} ${product.version}
 *
 * Библиотека для работы с СУБД MySQL
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

/**
 * Работа с СУБД MySQL
 *
 * @package Eresus
 */
class MySQL
{
    /**
     * Дескриптор соединения
     * @var resource
     */
    protected $Connection;

    /**
     * Имя БД
     * @var string
     */
    protected $name;

    /**
     * Префикс таблиц
     * @var string
     * @deprecated
     */
    public $prefix;

    /**
     * Вести лог запросов
     * @var bool
     */
    public $logQueries = false;

    /**
     * Если TRUE (по умолчанию) в случае ошибки скрипт будет прерван и показано сообщение об ошибке
     *
     * @var bool
     */
    public $error_reporting = true;

    /**
     * ???
     * @var ezcDbSchema
     */
    private $dbSchema = null;

    /**
     * Возвращает объект-одиночку схемы БД
     * @return ezcDbSchema
     */
    public function getSchema()
    {
        if (!$this->dbSchema)
        {
            $db = Eresus_DB::getHandler();
            $options = new ezcDbSchemaOptions(array('tableNamePrefix' => $db->options->tableNamePrefix));
            ezcDbSchema::setOptions($options);

            $this->dbSchema = ezcDbSchema::createFromDb($db);
        }

        return $this->dbSchema;
    }

    /**
     * Выполняет запрос к источнику
     *
     * @param string $query  Запрос в формате источника
     * @return mixed  Результат запроса. Тип зависит от источника, запроса и результата
     * @deprecated
     */
    public function query($query)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $db = Eresus_DB::getHandler();
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, $query);
        $db->exec($query);
        return true;
    }

    /**
     * Выполняет запрос к источнику и возвращает ассоциативный массив значений
     *
     * @param  string $query    Запрос в формате источника
     * @return  array|bool  Ответ в виде массива или FALSE в случае ошибки
     * @deprecated
     */
    public function query_array($query)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $db = Eresus_DB::getHandler();
        $stmt = $db->prepare($query);
        if (!$stmt->execute())
        {
            return false;
        }

        $values = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $values;
    }

    /**
     * Создание новой таблицы
     *
     * @param string $name       Имя таблицы
     * @param string $structure  Описание структуры
     * @param string $options    Опции
     *
     * @return bool Результат
     * @deprecated
     */
    public function create($name, $structure, $options = '')
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $db = Eresus_DB::getHandler();
        $name = $db->options->tableNamePrefix . $name;
        $query = "CREATE TABLE `$name` ($structure) $options";
        $result = $this->query($query);

        if ($result)
        {
            $db = Eresus_DB::getHandler();
            $this->dbSchema = ezcDbSchema::createFromDb($db);
        }

        return $result;
    }

    /**
     * Удаление таблицы
     *
     * @param string $name       Имя таблицы
     *
     * @return bool Результат
     * @deprecated
     */
    public function drop($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $db = Eresus_DB::getHandler();
        $name = $db->options->tableNamePrefix . $name;
        $query = "DROP TABLE `$name`";
        $result = $this->query($query);

        if ($result)
        {
            $db = Eresus_DB::getHandler();
            $this->dbSchema = ezcDbSchema::createFromDb($db);
        }

        return $result;
    }

    /**
     * Производит выборку данных из источника
     *
     * @param  string $tables     Список таблиц из которых проводится выборка
     * @param  string $condition  Условие для выборки (WHERE)
     * @param  string $order      Поля для сортировки (ORDER BY)
     * @param  string $fields     Список полей для получения
     * @param  int $limit      Максимльное количество получаемых записей
     * @param  int $offset     Начальное смещение для выборки
     * @param  string $group      Поле для группировки
     * @param  bool $distinct   Вернуть только уникальные записи
     *
     * @return  array|bool  Выбранные элементы в виде массива или FALSE в случае ошибки
     * @deprecated
     */
    public function select($tables, $condition = '', $order = '', $fields = '', $limit = 0,
                           $offset = 0, $group = '', $distinct = false)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $db = Eresus_DB::getHandler();
        $q = $db->createSelectQuery();
        $e = $q->expr;

        if (empty($fields))
        {
            $fields = '*';
        }

        if ($distinct)
        {
            $q->selectDistinct($fields);
        }
        else
        {
            $q->select($fields);
        }

        $tables = explode(',', $tables);
        $q->from($tables);

        if ($condition)
        {
            $q->where($condition);
        }

        if (strlen($order))
        {
            $order = explode(',', $order);
            for ($i = 0; $i < count($order); $i++)
            {
                switch ($order[$i]{0})
                {
                    case '+':
                        $q->orderBy(substr($order[$i], 1));
                        break;

                    case '-':
                        $q->orderBy(substr($order[$i], 1), ezcQuerySelect::DESC);
                        break;

                    default:
                        $q->orderBy($order[$i]);
                        break;
                }
            }
        }

        if ($limit && $offset)
        {
            $q->limit($limit, $offset);
        }
        elseif ($limit)
        {
            $q->limit($limit);
        }

        if ($group)
        {
            $q->groupBy($group);
        }

        $result = $q->fetchAll();

        return $result;
    }

    /**
     * Вставка элемента в БД
     *
     * @param string $table  Таблица, в которую надо вставтиь элемент
     * @param array $item   Ассоциативный массив значений
     *
     * @return mixed  Результат выполнения операции
     * @deprecated
     */
    public function insert($table, $item)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $fields = $this->fields($table);
        if (!$table)
        {
            return false;
        }

        $q = Eresus_DB::getHandler()->createInsertQuery();
        $q->insertInto($table);

        foreach ($fields as $field)
        {
            if (isset($item[$field]))
            {
                $q->set($field, $q->bindValue($item[$field]));
            }
        }

        $q->execute();
        return true;
    }

    /**
     * Выполняет обновление информации в источнике
     *
     * @param string $table      Таблица
     * @param mixed $set        Изменения
     * @param string $condition  Условие
     * @return void
     * @deprecated
     */
    public function update($table, $set, $condition)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $q = Eresus_DB::getHandler()->createUpdateQuery();
        $q->update($table)
            ->where($condition);

        $set = explode(',', $set);
        foreach ($set as $each)
        {
            list($key, $value) = explode('=', $each);
            $key = str_replace('`', '', trim($key));
            $value = preg_replace('/(^\'|\'$)/', '', trim($value));
            $q->set($key, $q->bindValue($value));
        }

        $q->execute();
    }

    /**
     * Выполняет запрос DELETE к базе данных
     *
     * @param string $table      таблица, из которой требуется удалить записи
     * @param string $condition  признаки удаляемых записей
     * @return mixed
     * @deprecated
     */
    public function delete($table, $condition)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $q = Eresus_DB::getHandler()->createDeleteQuery();
        $q->deleteFrom($table)
            ->where($condition);
        $q->execute();
        return null;
    }

    /**
     * Получение списка полей таблицы
     *
     * @param string $table            Имя таблицы
     * @param bool $info [optional]
     * @return array|bool  Список полей, с описанием, если $info = true
     *
     * @deprecated с 2.14
     */
    public function fields($table, $info = false)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $schm = $this->getSchema()->getSchema();
        if ($schm[$table]->fields)
        {
            return array_keys($schm[$table]->fields);
        }
        else
        {
            return false;
        }
    }

    /**
     * Выбрать одну запись из БД
     *
     * @param string $table      Имя таблицы
     * @param string $condition  SQL-условие
     * @param string $fields     Выбираемые поля
     * @return array|bool
     * @deprecated
     */
    public function selectItem($table, $condition, $fields = '')
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $q = Eresus_DB::getHandler()->createSelectQuery();

        if ($fields == '')
        {
            $fields = '*';
        }

        $q->select($fields);
        $q->from($table);
        $q->where($condition);
        $q->limit(1);

        $item = $q->fetch();

        return $item;
    }

    /**
     * Обновляет одну запись
     *
     * @param string $table
     * @param array $item
     * @param string $condition
     * @return bool
     * @deprecated
     */
    public function updateItem($table, $item, $condition)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $fields = $this->fields($table);
        if (!$table)
        {
            return false;
        }

        $q = Eresus_DB::getHandler()->createUpdateQuery();
        $q->update($table)
            ->where($condition);

        foreach ($fields as $field)
        {
            if (isset($item[$field]))
            {
                $q->set($field, $q->bindValue($item[$field]));
            }
        }

        $q->execute();
        return true;
    }

    /**
     * Возвращает количество записей в таблице
     *
     * @param string $table      таблица, для которой требуется посчитать кол-во записей
     * @param string $condition
     * @param string $group
     * @param bool $rows
     * @return int
     * @deprecated
     */
    public function count($table, $condition = false, $group = false, $rows = false)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $q = Eresus_DB::getHandler()->createSelectQuery();
        $e = $q->expr;

        $q->select($q->alias($e->count('*'), 'count'));
        $q->from($table);

        if ($condition)
        {
            $q->where($condition);
        }

        if ($group)
        {
            $q->groupBy($group);
        }

        $result = $q->fetchAll();
        if ($rows)
        {
            return count($result);
        }
        else
        {
            return intval($result[0]['count']);
        }
    }

    /**
     * Возвращает идентификатор последней вставленной записи
     *
     * @return mixed
     * @deprecated
     */
    public function getInsertedID()
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $db = Eresus_DB::getHandler();
        return $db->lastInsertId();
    }

    /**
     *
     * @param string $table
     * @param string $param
     *
     * @return array|string
     * @deprecated
     */
    public function tableStatus($table, $param = '')
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        $result = $this->query_array("SHOW TABLE STATUS LIKE '" . $this->prefix . $table . "'");
        if ($result)
        {
            $result = $result[0];
            if (!empty($param))
            {
                $result = $result[$param];
            }
        }
        return $result;
    }

    /**
     * Экранирует потенциально опасные символы
     *
     * Начиная с 2.13 метод ничего не делает.
     *
     * @param mixed $src  Входные данные
     * @return mixed
     * @deprecated
     */
    public function escape($src)
    {
        Eresus_Kernel::log(__METHOD__, LOG_NOTICE, 'This method is deprecated');
        return $src;
    }
}

