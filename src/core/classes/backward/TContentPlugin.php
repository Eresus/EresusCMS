<?php
/**
 * Устаревший базовый класс для плагинов, предоставляющих тип контента
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
 * Устаревший базовый класс для плагинов, предоставляющих тип контента
 *
 * @package Eresus
 * @deprecated с 3.01 используйте ContentPlugin
 */
class TContentPlugin extends Eresus_Plugin
{
    /**
     * Обновляет контент страницы в БД
     *
     * @param  string  $content  Контент
     */
    protected function updateContent($content)
    {
        $item = Eresus_CMS::getLegacyKernel()->db->
            selectItem('pages', "`id`='".Eresus_Kernel::app()->getPage()->id."'");
        $item['content'] = $content;
        Eresus_CMS::getLegacyKernel()->db->
            updateItem('pages', $item, "`id`='".Eresus_Kernel::app()->getPage()->id."'");
    }

    /**
     * Обновляет контент страницы
     */
    public function update()
    {
        $this->updateContent(arg('content', 'dbsafe'));
        HTTP::redirect(arg('submitURL'));
    }

    /**
     * Отрисовка клиентской части
     *
     * @return  string  контент
     */
    public function clientRenderContent()
    {
        /** @var TClientUI $page */
        $page = Eresus_Kernel::app()->getPage();
        return $page->content;
    }

    /**
     * Отрисовка административной части
     *
     * @return  string  Контент
     */
    public function adminRenderContent()
    {
        $item = Eresus_CMS::getLegacyKernel()->db->selectItem('pages', "`id`='".
            Eresus_Kernel::app()->getPage()->id."'");
        $form = array(
            'name' => 'content',
            'caption' => Eresus_Kernel::app()->getPage()->title,
            'width' => '100%',
            'fields' => array (
                array ('type'=>'hidden','name'=>'update'),
                array ('type' => 'memo', 'name' => 'content', 'label' => strEdit, 'height' => '30'),
            ),
            'buttons' => array('apply', 'reset'),
        );

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $result = $page->renderForm($form, $item);
        return $result;
    }
}

