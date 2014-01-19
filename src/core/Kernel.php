<?php
/**
 * Ядро
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
 * Ядро CMS
 *
 * @package Eresus
 * @since 3.00
 * @deprecated с x.xx
 */
class Eresus_Kernel
{
    /**
     * Порог важности сообщений для записи в журнал
     * @var int
     * @since 3.01
     */
    static public $logLevel = LOG_ERR;

    /**
     * Записывает сообщение в журнал
     *
     * @param string|array $sender    отправитель (используйте \__METHOD\__ и \__FUNCTION\__)
     * @param int          $priority  уровень важности (используйте константы LOG_xxx)
     * @param string       $message   текст сообщение
     * @param mixed        ...        аргументы для вставки в $message через {@link sprintf}
     *
     * @see $logLevel
     */
    public static function log($sender, $priority, $message)
    {
        //TODO Рассмотреть вынос этого метода из этого класса
        if ($priority > self::$logLevel)
        {
            return;
        }

        if (is_array($sender))
        {
            $sender = implode('/', $sender);
        }
        if (empty($sender))
        {
            $sender = 'unknown';
        }


        /* Если есть аргументы для подстановки — вставляем их */
        if (@func_num_args() > 3)
        {
            $args = array();
            for ($i = 3; $i < @func_num_args(); $i++)
            {
                $var = func_get_arg($i);
                if (is_object($var))
                {
                    $var = get_class($var);
                }
                $args []= $var;
            }
            $message = vsprintf($message, $args);
        }

        $message = $sender . ': ' . $message;

        $priorities = array(
            LOG_DEBUG => 'debug',
            LOG_INFO => 'info',
            LOG_NOTICE => 'notice',
            LOG_WARNING => 'warning',
            LOG_ERR => 'error',
            LOG_CRIT => 'critical',
            LOG_ALERT => 'ALERT',
            LOG_EMERG => 'PANIC'
        );
        $message = '[' . (array_key_exists($priority, $priorities)
            ? $priorities[$priority] : 'unknown') . '] ' . $message;

        if (!error_log($message))
        {
            if (!syslog($priority, $message))
            {
                fputs(STDERR, $message);
            }
        }
    }

    /**
     * Записывает сообщение об исключении в журнал
     *
     * @param Exception $e
     */
    public static function logException($e)
    {
        //TODO Рассмотреть вынос этого метода из этого класса
        $previous = $e->getPrevious();
        $trace = $e->getTraceAsString();

        $logMessage = sprintf(
            "%s in %s at %s\n%s\nBacktrace:\n%s\n",
            get_class($e),
            $e->getFile(),
            $e->getLine(),
            $e->getMessage(),
            $trace
        );

        Eresus_Kernel::log(__METHOD__, LOG_ERR, $logMessage);

        if ($previous)
        {
            self::logException($previous, 'Previous exception:');
        }
    }

    /**
     * Возвращает true если PHP запущен на UNIX-подобной ОС
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isUnixLike()
    {
        return DIRECTORY_SEPARATOR == '/';
    }

    /**
     * Возвращает true если PHP запущен на Microsoft® Windows™
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isWindows()
    {
        return strncasecmp(PHP_OS, 'WIN', 3) == 0;
    }

    /**
     * Возвращает true если PHP запущен на MacOS
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isMac()
    {
        return strncasecmp(PHP_OS, 'MAC', 3) == 0;
    }

    /**
     * Возвращает true, если используется
     * {@link http://php.net/manual/en/features.commandline.php CLI}
     * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI}
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isCLI()
    {
        return PHP_SAPI == 'cli';
    }

    /**
     * Возвращает true, если используется CGI
     * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI}
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isCGI()
    {
        return strncasecmp(PHP_SAPI, 'CGI', 3) == 0;
    }

    /**
     * Возвращает true, если используется
     * {@link http://php.net/manual/en/function.php-sapi-name.php SAPI} модуля веб-сервера
     *
     * @return bool
     *
     * @since 3.00
     */
    public static function isModule()
    {
        return !self::isCGI() && isset($_SERVER['GATEWAY_INTERFACE']);
    }

    /**
     * Проверяет, объявлен ли указанный класс или интерфейс
     *
     * Этот метод не инициирует автозагрузку.
     *
     * @param string $name  имя класса или интерфейса
     * @return bool true если класс или интерфейс $name объявлен
     *
     * @since 3.00
     */
    public static function classExists($name)
    {
        return class_exists($name, false) || interface_exists($name, false);
    }

    /**
     * Возвращает выполняемое приложение или null, если приложение не запущено
     *
     * @return \Eresus\Kernel  выполняемое приложение
     *
     * @since 3.00
     */
    public static function app()
    {
        return $GLOBALS['kernel'];
    }
}

