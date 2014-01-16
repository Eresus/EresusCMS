<?php
/**
 * Тесты класса Eresus_Event_Dispatcher
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
 * @subpackage Tests
 */

require_once __DIR__ . '/../../bootstrap.php';

/**
 * Тесты класса Eresus_Event_Dispatcher
 * @package Eresus
 * @subpackage Tests
 */
class Eresus_Event_DispatcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Eresus_Event_Dispatcher::addListener
     * @covers Eresus_Event_Dispatcher::dispatch
     */
    public function testOverall()
    {
        $dispatcher = new Eresus_Event_Dispatcher();
        $listener = $this->getMock('stdClass', array('foo', 'bar', 'baz'));
        $listener->expects($this->once())->method('foo');
        $listener->expects($this->once())->method('bar')
            ->will($this->returnCallback(
                function (Eresus_Event $event)
                {
                    $event->stopPropagation();
                }
            ));
        $listener->expects($this->never())->method('baz');
        $dispatcher->addListener('my_event', array($listener, 'bar'));
        $dispatcher->addListener('my_event', array($listener, 'foo'), 1);
        $dispatcher->addListener('my_event', array($listener, 'baz'));
        $dispatcher->dispatch('my_event');

        $dispatcher->dispatch('other_event');
    }
}

