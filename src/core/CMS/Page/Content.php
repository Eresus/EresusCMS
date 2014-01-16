<?php
/**
 * Контент страницы
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
 * Контент страницы
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_CMS_Page_Content
{
    /**
     * Страница
     * @var WebPage
     * @since 3.01
     */
    private $page;

    /**
     * Контент
     * @var string
     * @since 3.01
     */
    private $content;

    /**
     * Создаёт новый контент страницы
     *
     * @param WebPage $page
     * @param string $content
     *
     * @since 3.01
     */
    public function __construct(WebPage $page, $content = '')
    {
        $this->page = $page;
        $this->setContent($content);
    }

    /**
     * Возвращает контент
     *
     * @return string
     * @since 3.01
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Задаёт контент
     *
     * @param string $content
     * @since 3.01
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Отрисовывает контент
     *
     * Контент обрабатывается как шаблон, в него подставляются глобальные переменные, и проводится
     * обработка модулями расширений в соответствии с зарегистрированными обработчиками событий.
     *
     * @return string
     * @since 3.01
     */
    public function render()
    {
        $tmpl = new Eresus_Template();
        $tmpl->setSource($this->content);

        $event = new Eresus_Event_Render($tmpl->compile());
        Eresus_Kernel::app()->getEventDispatcher()
            ->dispatch('cms.client.render_content', $event);
        return $event->getText();
    }
}

