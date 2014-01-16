<?php
/**
 * Событие
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
 * Событие
 *
 * @since 3.01
 * @package Eresus
 */
class Eresus_Event
{
    /**
     * Имя события
     * @var string
     * @since 3.01
     */
    private $name;

    /**
     * Признак того, что распространение события остановлено
     * @var bool
     * @since 3.01
     */
    private $isPropagationStopped = false;

    /**
     * Задаёт имя события
     *
     * @param string $name
     *
     * @since 3.01
     */
    public function setName($name)
    {
        assert('is_string($name)');
        $this->name = $name;
    }

    /**
     * Возвращает имя события
     *
     * @return string
     * @since 3.01
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Останавливает распространение события
     *
     * @since 3.01
     */
    public function stopPropagation()
    {
        $this->isPropagationStopped = true;
    }

    /**
     * Возвращает true, если распространение события остановлено
     *
     * @return bool
     * Since 3.01
     */
    public function isPropagationStopped()
    {
        return $this->isPropagationStopped;
    }
}

