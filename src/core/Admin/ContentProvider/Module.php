<?php
/**
 * Поставщик контента для АИ на основе модуля CMS
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
 * Поставщик контента для АИ на основе модуля CMS
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Admin_ContentProvider_Module extends Eresus_Admin_ContentProvider_Abstract
{
    /**
     * Создаёт поставщика на основе переданного модуля CMS
     *
     * @param object $module
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @since 3.01
     */
    public function __construct($module)
    {
        if (!is_object($module))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 1, 'object', $module);
        }
        $this->module = $module;
    }

    /**
     * Возвращает имя модуля, пригодное для вывода пользователю
     *
     * @return string
     *
     * @since 3.01
     */
    public function getModuleName()
    {
        return get_class($this->module);
    }
}

