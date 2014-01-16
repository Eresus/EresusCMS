<?php
/**
 * Работа с шаблонами Dwoo
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
 * @subpackage Templates
 */

/**
 * Настройки шаблонов
 *
 * @package Eresus
 * @subpackage Templates
 */
class TemplateSettings
{
    /**
     * Global substitution value to be used in all templates
     * @var array
     */
    private static $globalValues = array();

    /**
     * Set global substitution value to be used in all templates
     *
     * @param string $name
     * @param mixed  $value
     */
    public static function setGlobalValue($name, $value)
    {
        self::$globalValues[$name] = $value;
    }

    /**
     * Get global substitution value
     *
     * @param string $name
     * @return null|mixed  Null will be returned if value not set
     */
    public static function getGlobalValue($name)
    {
        return array_key_exists($name, self::$globalValues) ? self::$globalValues[$name] : null;
    }

    /**
     * Remove global substitution value
     *
     * @param string $name
     */
    public static function removeGlobalValue($name)
    {
        if (isset(self::$globalValues[$name]))
        {
            unset(self::$globalValues[$name]);
        }
    }

    /**
     * Get all global substitution values
     *
     * @return array
     */
    public static function getGlobalValues()
    {
        return self::$globalValues;
    }
}


/**
 * Файл шаблона
 *
 * @package Eresus
 * @subpackage Templates
 */
class TemplateFile extends Dwoo_Template_File
{
}

