<?php
/**
 * Диспетчер событий
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
 * Диспетчер событий
 *
 * @since 3.01
 * @package Eresus
 */
class Eresus_Event_Dispatcher
{
    /**
     * Реестр подписчиков
     * @var array
     * @since 3.01
     */
    private $listeners = array();

    /**
     * Регистрирует подписчика события
     *
     * @param string   $eventName  имя события
     * @param callable $listener   подписчик
     * @param int      $priority   приоритет (чем больше, тем раньше будет вызван этот подписчик)
     *
     * @throws Eresus_Exception_InvalidArgumentType
     *
     * @since 3.01
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $eventName = strval($eventName);
        if (!is_callable($listener))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 2, 'callable',
                $listener);
        }

        if (!array_key_exists($eventName, $this->listeners))
        {
            $this->listeners[$eventName] = array();
        }
        if (!array_key_exists($priority, $this->listeners[$eventName]))
        {
            $this->listeners[$eventName][$priority] = array();
        }
        $this->listeners[$eventName][$priority] []= $listener;
    }

    /**
     * Отправляет событие подписчикам
     *
     * @param string       $eventName  имя события
     * @param Eresus_Event $event      опциональный объект события
     *
     * @since 3.01
     */
    public function dispatch($eventName, Eresus_Event $event = null)
    {
        if (!array_key_exists($eventName, $this->listeners))
        {
            return;
        }

        if (null === $event)
        {
            $event = new Eresus_Event();
        }

        $event->setName($eventName);

        krsort($this->listeners[$eventName]);

        foreach ($this->listeners[$eventName] as $listeners)
        {
            foreach ($listeners as $listener)
            {
                call_user_func($listener, $event);
                if ($event->isPropagationStopped())
                {
                    break 2;
                }
            }
        }
    }
}

