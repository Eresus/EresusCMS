<?php
/**
 * Контроллер диалога настройки (старый вариант)
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
 * Контроллер диалога настройки (старый вариант)
 *
 * Этот класс по сути является фасадом к основному классу модуля расширения, имитирующим наличие
 * отдельного контроллера диалога настроек, а на деле просто вызывающего соответствующие метода
 * основного класса. Это сделано для безболезненного выноса функционала работы с настройками из
 * основного класса в контроллер.
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Plugin_Controller_Admin_LegacySettings extends Eresus_Plugin_Controller_Admin
    implements Eresus_Admin_Controller_Content_Interface
{
    /**
     * Возвращает разметку области контента
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string
     * @since 3.01
     */
    public function getHtml(Eresus_CMS_Request $request)
    {
        if ($request->getMethod() == 'POST')
        {
            return $this->call('updateSettings');
        }
        return $this->call('settings');
    }

    /**
     * Вызывает метод модуля расширения
     *
     * @param string $method  имя метода
     * @param array  $args    аргументы
     *
     * @return mixed
     *
     * @throws Eresus_CMS_Exception_NotFound
     *
     * @since 3.01
     */
    private function call($method, array $args = array())
    {
        if (!method_exists($this->getPlugin(), $method))
        {
            throw new Eresus_CMS_Exception_NotFound;
        }

        return call_user_func_array(array($this->getPlugin(), $method), $args);
    }
}

