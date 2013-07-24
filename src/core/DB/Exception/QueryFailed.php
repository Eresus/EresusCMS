<?php
/**
 * Исключительная ситуация «Запрос не выполнен»
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
 * Исключительная ситуация «Запрос не выполнен»
 *
 * @package Eresus
 * @subpackage DB
 * @since 3.01
 */
class Eresus_DB_Exception_QueryFailed extends Eresus_DB_Exception
{
    /**
     * Конструктор
     *
     * @todo Заменить фабричным методом
     *
     * @param ezcQuery  $query     неудавшийся запрос
     * @param string    $message   сообщение
     * @param Exception $previous  предыдущее исключение
     */
    public function __construct(ezcQuery $query = null, $message = null, Exception $previous = null)
    {
        $insider = new DBQueryInsider;
        $query->doBind($insider);
        $query = $insider->subst($query);

        if (is_null($message))
        {
            $message = 'Database query failed';
        }

        parent::__construct($message . ': ' . $query, 0, $previous);
    }
}

