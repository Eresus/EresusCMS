<?php
/**
 * Фронт-контроллер КИ
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
 * Фронт-контроллер КИ
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Client_FrontController extends Eresus_CMS_FrontController
{
    /**
     * Выполняет действия контроллера и возвращает ответ
     *
     * @return Eresus_HTTP_Response
     * @since 3.01
     */
    public function dispatch()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'starting...');
        /** @var TClientUI $page */
        $page = $this->getPage();
        $response = $page->render($this->getRequest());

        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, 'done');
        return $response;
    }

    /**
     * Создаёт объект Eresus_CMS_Page
     *
     * @return Eresus_CMS_Page
     * @since 3.01
     */
    protected function createPage()
    {
        return new TClientUI();
    }
}

