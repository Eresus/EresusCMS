<?php
/**
 * Контроллер АИ типа раздела «URL»
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

use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Контроллер АИ типа раздела «URL»
 *
 * @package Eresus
 */
class Eresus_Admin_Controller_Content_Url implements Eresus_Admin_Controller_Content_Interface
{
    /**
     * Возвращает разметку области контента
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string|Eresus_HTTP_Response
     * @since 3.01
     */
    public function getHtml(Eresus_CMS_Request $request)
    {
        $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
        $sections = $legacyKernel->sections;
        $args = $request->getMethod() == 'GET' ? $request->query : $request->request;
        $item = $sections->get($args->getInt('section'));
        $response = $args->has('update') // TODO Использовать getMethod вметос 'update'
            ? $this->updateAction($item)
            : $this->formAction($item, $args);
        return $response;
    }

    /**
     * Возвращает разметку диалога
     *
     * @param array $item
     *
     * @return string
     * @since 3.01
     */
    private function formAction(array $item)
    {
        $form = array(
            'name' => 'editURL',
            'caption' => ADM_EDIT,
            'width' => '100%',
            'fields' => array(
                array('type' => 'hidden', 'name' => 'update', 'value' => $item['id']),
                array('type' => 'edit', 'name' => 'url', 'label' => 'URL:', 'width' => '100%',
                    'value' => isset($item['content']) ? $item['content'] : ''),
            ),
            'buttons' => array('apply', 'cancel'),
        );
        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $html = $page->renderForm($form);
        return $html;
    }

    /**
     * Сохраняет изменения
     *
     * @param array        $item
     * @param ParameterBag $args
     *
     * @return Eresus_HTTP_Redirect
     */
    private function updateAction(array $item, ParameterBag $args)
    {
        $item['content'] = $args->get('url');
        Eresus_Kernel::app()->getLegacyKernel()->sections->update($item);
        return new Eresus_HTTP_Redirect($args->get('submitURL'));
    }
}

