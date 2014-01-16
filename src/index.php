<?php
/**
 * ${product.title}
 *
 * Запускающий скрипт для режима Web
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
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

/*
 * Вывод ошибок нужен для правильной работы перехватчика фатальных ошибок
 */
ini_set('display_errors', true);

ini_set('track_errors', true);

/**
 * Подключение автозагрузчика классов
 */
include_once 'core/autoload.php';

if (isset($php_errormsg))
{
    die($php_errormsg);
}
ini_set('track_errors', false);

ini_set('error_log', __DIR__ . '/var/log/eresus.log');

Eresus_Kernel::$logLevel = LOG_DEBUG;
Eresus_Kernel::init();

/*
 * Если есть файл install.php, запускаем установщик, а не CMS
 * Это код для будущих версий
 */
if (is_file('install.php'))
{
    $fileName = 'install.php';
    $appName = 'Installer';
}
else
{
    $fileName = 'core/CMS.php';
    $appName = 'Eresus_CMS';
}


try
{
    /**
     * Подключение главного приложения
     */
    include_once $fileName;
}
catch (Exception $e)
{
    die('Can not include file "' . $fileName . '". Is it exists and accessible?');
}

// Запуск приложения
Eresus_Kernel::exec($appName);

