<?php
/**
 * Страница АИ
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
 * Страница АИ
 *
 * @package Eresus
 * @since 3.01
 * @todo Унаследовать напрямую от Eresus_CMS_page после удаления WebPage
 */
class Eresus_CMS_Page_Admin extends WebPage
{
    /**
     * Заголовок страницы
     *
     * @var string
     */
    private $title = '';

    /**
     * Возвращает полный заголовок страницы
     *
     * Этот метод возвращает полный заголовок страницы, куда, в зависимости от настроек сайта, могут
     * входить: имя сайта, заголовок сайта, заголовок раздела и т. д.
     *
     * @return string
     * @since 3.01
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Задаёт заголовок страницы
     *
     * @param string $title
     *
     * @since 3.01
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Возвращает описание страницы
     *
     * Этот метод возвращает полное описание страницы для мета-тега description. В зависимости от
     * настроек сайта, в него могут входить: описание сайта и описание раздела.
     *
     * @return string
     * @since 3.01
     */
    public function getDescription()
    {
        return '';
    }

    /**
     * Возвращает ключевые слова страницы
     *
     * Этот метод возвращает полный набор ключевых слов страницы для мета-тега keywords. В
     * зависимости от настроек сайта, в него могут входить: ключевые слова сайта и ключевые слова
     * раздела.
     *
     * @return string
     * @since 3.01
     */
    public function getKeywords()
    {
        return '';
    }
}

