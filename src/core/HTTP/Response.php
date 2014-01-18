<?php
/**
 * Ответ по HTTP
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
 * @subpackage HTTP
 */

use Symfony\Component\HttpFoundation\Response;

/**
 * Ответ по HTTP
 *
 * @package Eresus
 * @subpackage HTTP
 * @since 3.01
 */
class Eresus_HTTP_Response extends Response
{
    /**
     * Возвращает сообщение для указанного кода состояния
     *
     * @param int $status
     *
     * @return string
     *
     * @since 3.01
     */
    public static function getStatusText($status)
    {
        if (array_key_exists($status, self::$statusTexts))
        {
            return self::$statusTexts[$status];
        }
        return '';
    }
}

