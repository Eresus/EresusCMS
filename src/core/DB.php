<?php
/**
 * Работа с базами данных
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
 * @subpackage DB
 */

/**
 * Интерфейс к СУБД
 *
 * @package Eresus
 * @subpackage DB
 */
class Eresus_DB implements ezcBaseConfigurationInitializer
{
    /**
     * Список DSN "ленивых" соединений
     * @var array(mixed => string)
     */
    private static $lazyConnectionDSNs = array();

    /**
     * Создаёт подключение к БД
     *
     * @param string $dsn        DSN
     * @param string|bool $name  опциональное имя соединения
     *
     * @throws Eresus_DB_Exception
     *
     * @return ezcDbHandler
     */
    public static function connect($dsn, $name = false)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s", %s)', $dsn, $name);

        try
        {
            $db = ezcDbFactory::create($dsn);
        }
        catch (Exception $e)
        {
            throw new Eresus_DB_Exception("Can not connect to '$dsn'", 0, $e);
        }

        $db->setAttribute(PDO::ATTR_CASE, PDO::CASE_NATURAL);

        if (substr($dsn, 0, 5) == 'mysql' && preg_match('/charset=(.*?)(&|$)/', $dsn, $m))
        {
            $db->query("SET NAMES {$m[1]}");
        }

        ezcDbInstance::set($db, $name);
        return $db;
    }

    /**
     * Configures lazy connection to DB
     *
     * @param string      $dsn   Connection DSN string
     * @param string|bool $name  Optional connection name
     * @return void
     */
    public static function lazyConnection($dsn, $name = false)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '("%s", %s)', $dsn, $name);
        self::$lazyConnectionDSNs[$name] = $dsn;
    }

    /**
     * Returns connection handler
     *
     * @param string|bool $name  Optional connection name
     * @return ezcDbHandler
     */
    public static function getHandler($name = false)
    {
        return ezcDbInstance::get($name);
    }

    /**
     * Sets connection handler
     *
     * @param ezcDbHandler $handler
     * @param string|bool  $name     Optional connection name
     *
     */
    public static function setHandler($handler, $name = false)
    {
        ezcDbInstance::set($handler, $name);
    }

    /**
     * eZ Components lazy init
     *
     * @param bool|string $name  Connection name
     *
     * @throws Eresus_DB_Exception
     *
     * @return ezcDbHandler
     */
    public static function configureObject($name)
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '(%s)', $name);

        if (!isset(self::$lazyConnectionDSNs[$name]))
        {
            throw new Eresus_DB_Exception('DSN for lazy connection "' . $name . '" not found');
        }

        $dsn = self::$lazyConnectionDSNs[$name];
        $db = self::connect($dsn, $name);

        return $db;
    }

    /**
     * Execute query
     *
     * @param ezcQuery $query
     *
     * @throws Eresus_DB_Exception_QueryFailed
     *
     * @return mixed
     *
     * @deprecated с 3.01 используйте ezcQuery::execute
     */
    public static function execute($query)
    {
        return $query->execute();
    }

    /**
     * Fetch row from DB response
     *
     * @param ezcQuerySelect $query
     *
     * @throws Eresus_DB_Exception_QueryFailed
     *
     * @return array
     *
     * @deprecated с 3.01 используйте ezcQuerySelect::fetch
     */
    public static function fetch(ezcQuerySelect $query)
    {
        return $query->fetch();
    }

    /**
     * Get response rows
     * @param ezcQuerySelect $query
     * @return array
     * @deprecated с 3.01 используйте ezcQuerySelect::fetchAll
     */
    public static function fetchAll(ezcQuerySelect $query)
    {
        return $query->fetchAll();
    }
}


/**
 * Query Insider
 *
 * Internal class for substitution in doBind method to get values
 * set with bindValue or bindParam methods.
 *
 * @package DB
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 * @internal
 * @ignore
 *
 */
class DBQueryInsider extends PDOStatement
{

    /**
     * Values
     * @var array
     */
    protected $values = array();

    /**
     * Bind value
     *
     * @param int      $paramNo
     * @param mixed    $param
     * @param int|null $type
     *
     * @return bool
     */
    public function bindValue($paramNo, $param, $type = null)
    {
        $this->values[$paramNo] = var_export($param, true);
        return true;
    }

    /**
     * Bind param
     *
     * @param int   $paramNo
     * @param mixed $param
     * @param int   $type
     * @param int   $maxLength
     * @param mixed $driverData
     *
     * @return bool
     */
    public function bindParam($paramNo, &$param, $type = null, $maxLength = null, $driverData = null)
    {
        $this->bindValue($paramNo, $param, $type);
        return true;
    }

    /**
     * Substitute values in query
     *
     * @param string $query
     * @return string
     */
    public function subst($query)
    {
        foreach ($this->values as $key => $value)
        {
            $query = preg_replace("/$key(\s|,|$)/", "$value$1", $query);
        }

        return $query;
    }
}

ezcBaseInit::setCallback('ezcInitDatabaseInstance', 'Eresus_DB');
