<?php
/**
 * Абстрактный поставщик контента для АИ
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
 * Абстрактный поставщик контента для АИ
 *
 * @package Eresus
 * @since 3.01
 */
abstract class Eresus_Admin_ContentProvider_Abstract
{
    /**
     * Модуль CMS
     *
     * @var object
     *
     * @since 3.01
     */
    protected $module;

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
        if (!method_exists($this->getModule(), 'adminRender'))
        {
            throw new LogicException(sprintf(_('Метод "%s" не найден в классе "%s".'),
                'adminRender', get_class($this->getModule())));
        }
        try
        {
            $html = $this->getModule()->adminRender($request);
        }
        catch (Eresus_CMS_Exception_NotFound $e)
        {
            throw $e;
        }
        catch (Exception $e)
        {
            throw new RuntimeException(
                sprintf(_('В модуле %s произошла ошибка: %s'), $this->getModuleName(),
                    $e->getMessage()), 0, $e);
        }
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
        if (!method_exists($this->getModule(), 'adminRenderContent'))
        {
            throw new LogicException(sprintf(_('Метод "%s" не найден в классе "%s".'),
                'adminRenderContent`
                ', get_class($this->getModule())));
        }
        try
        {
            $html = $this->getModule()->adminRenderContent($request);
        }
        catch (Exception $e)
        {
            throw new RuntimeException(
                sprintf(_('В модуле %s произошла ошибка: %s'), $this->getModuleName(),
                    $e->getMessage()), 0, $e);
        }
        return $html;
    }

    /**
     * Возвращает модуль-поставщик
     *
     * @return object
     *
     * @since 3.01
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Возвращает имя модуля, пригодное для вывода пользователю
     *
     * @return string
     *
     * @since 3.01
     */
    abstract public function getModuleName();

    /**
     * Возвращает контроллер диалога настроек или false, если настроек у модуля нет
     *
     * @return bool|Eresus_Admin_Controller_Content_Interface
     *
     * @since 3.01
     */
    public function getSettingsController()
    {
        return false;
    }
}

