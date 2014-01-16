<?php
/**
 * Исключительная ситуация «Неправильный тип аргумента»
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
 * Исключительная ситуация «Неправильный тиа аргумента»
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Exception_InvalidArgumentType extends InvalidArgumentException
{
    /**
     * Фабрика исключений
     *
     * @param string $method        метод, где произошла ошибка
     * @param int    $argNum        порядковый номер аргумента
     * @param string $expectedType  ожидаемый тип аргумента
     * @param mixed  $actualArg     аргумент, вызвавший ошибку
     *
     * @return Eresus_Exception_InvalidArgumentType
     */
    public static function factory($method, $argNum, $expectedType, $actualArg)
    {
        return new self(sprintf(
            'Argument %d of %s expected to be a %s, %s given',
            $argNum,
            $method,
            $expectedType,
            is_object($actualArg) ? 'instance of ' . get_class($actualArg) : gettype($actualArg)
        ));
    }
}

