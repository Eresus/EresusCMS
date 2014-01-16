<?php
/**
 * Абстрактный фронт-контроллер CMS
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
 * Абстрактный фронт-контроллер CMS
 *
 * @package Eresus
 * @since 3.01
 */
abstract class Eresus_CMS_FrontController
{
    /**
     * Обрабатываемый запрос
     *
     * @var Eresus_CMS_Request
     * @since 3.01
     */
    private $request;

    /**
     * Создаваемая страница
     * @var Eresus_CMS_Page
     * @since 3.01
     */
    private $page;

    /**
     * Конструктор контроллера
     *
     * @param Eresus_CMS_Request $request
     *
     * @since 3.01
     */
    public function __construct(Eresus_CMS_Request $request)
    {
        $this->request = $request;
        $this->page = $this->createPage();
    }

    /**
     * Выполняет действия контроллера и возвращает ответ
     *
     * @return Eresus_HTTP_Response
     * @since 3.01
     */
    abstract public function dispatch();

    /**
     * Возвращает объект Eresus_CMS_Page
     *
     * @return Eresus_CMS_Page
     * @since 3.01
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Возвращает текущий запрос
     *
     * @return Eresus_CMS_Request
     * @since 3.01
     */
    protected function getRequest()
    {
        return $this->request;
    }

    /**
     * Создаёт объект Eresus_CMS_Page
     *
     * @return Eresus_CMS_Page
     * @since 3.01
     */
    abstract protected function createPage();
}

