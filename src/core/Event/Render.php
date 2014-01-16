<?php
/**
 * Событие отрисовки
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
 * Событие отрисовки
 *
 * @since 3.01
 * @package Eresus
 */
class Eresus_Event_Render extends Eresus_Event
{
    /**
     * Отрисованная строка
     *
     * @var string
     *
     * @since 3.01
     */
    private $text;

    /**
     * @param string $text  отрисованная строка
     *
     * @since 3.01
     */
    public function __construct($text)
    {
        $this->text = $text;
    }

    /**
     * Возвращает отрисованную строку
     *
     * @return string
     *
     * @since 3.01
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Меняет отрисованную строку
     *
     * @param string $text
     *
     * @since 3.01
     */
    public function setText($text)
    {
        $this->text = $text;
    }
}

