<?php
/**
 * Контроллер КИ типа раздела «По умолчанию»
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
 * Контроллер КИ типа раздела «По умолчанию»
 *
 * @package Eresus
 */
class Eresus_Client_Controller_Content_Default extends Eresus_Client_Controller_Content_Abstract
{
    /**
     * Возвращает разметку области контента
     *
     * @param Eresus_CMS_Request $request
     * @param TClientUI          $page
     *
     * @return Eresus_HTTP_Response|string
     * @since 3.01
     */
    public function getHtml(Eresus_CMS_Request $request, TClientUI $page)
    {
        $plugin = new ContentPlugin;
        return $plugin->clientRenderContent($request, $page);
    }
}

