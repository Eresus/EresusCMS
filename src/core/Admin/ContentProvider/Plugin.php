<?php
/**
 * Поставщик контента для АИ на основе модуля расширения
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
 * Поставщик контента для АИ на основе модуля расширения
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Admin_ContentProvider_Plugin extends Eresus_Admin_ContentProvider_Abstract
{
    /**
     * Создаёт поставщика на основе переданного модуля расширения
     *
     * @param Eresus_Plugin $plugin
     *
     * @since 3.01
     */
    public function __construct(Eresus_Plugin $plugin)
    {
        $this->module = $plugin;
    }

    /**
     * Отрисовывает интерфейс модуля
     *
     * @param Eresus_CMS_Request $request
     *
     * @throws LogicException
     * @throws RuntimeException
     *
     * @return string  HTML
     *
     * @since 3.01
     */
    public function adminRender(Eresus_CMS_Request $request)
    {
        $this->linkAdminResources();
        $html = parent::adminRender($request);
        return $html;
    }

    /**
     * Отрисовывает область контента раздела
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string  HTML
     *
     * @throws LogicException
     * @throws RuntimeException
     *
     * @since 3.01
     */
    public function adminRenderContent(Eresus_CMS_Request $request)
    {
        $this->linkAdminResources();
        $html = parent::adminRenderContent($request);
        return $html;
    }

    /**
     * Возвращает имя модуля, пригодное для вывода пользователю
     *
     * @return string
     *
     * @since 3.01
     */
    public function getModuleName()
    {
        /** @var Eresus_Plugin $plugin */
        $plugin = $this->module;
        return $plugin->getName();
    }

    /**
     * Возвращает контроллер диалога настроек или false, если настроек у модуля нет
     *
     * @return bool|Eresus_Admin_Controller_Content_Interface
     *
     * @since 3.01
     */
    public function getSettingsController()
    {
        /** @var Eresus_Plugin $plugin */
        $plugin = $this->getModule();
        if (!method_exists($plugin, 'settings'))
        {
            return false;
        }
        $this->linkAdminResources();
        $controller = new Eresus_Plugin_Controller_Admin_LegacySettings($plugin);
        return $controller;
    }

    /**
     * Подключает стили и скрипты АИ
     *
     * @since 3.01
     */
    private function linkAdminResources()
    {
        $page = Eresus_Kernel::app()->getPage();
        /** @var Eresus_Plugin $plugin */
        $plugin = $this->getModule();

        $resource = 'admin/default.css'; // В будущем «default» можно заменить именем темы
        if (file_exists($plugin->getCodeDir() . '/' . $resource))
        {
            $page->linkStyles($plugin->getCodeUrl() . $resource);
        }

        $resource = 'admin/scripts.js';
        if (file_exists($plugin->getCodeDir() . '/' . $resource))
        {
            $page->linkScripts($plugin->getCodeUrl() . $resource);
        }
    }
}

