<?php
/**
 * Управление плагинами
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

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Управление плагинами
 *
 * @package Eresus
 */
class TPlgMgr implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     * @since 3.02
     */
    private $container;

    /**
     * Уровень доступа к модулю
     * @var int
     */
    private $access = ADMIN;

    /**
     * @param ContainerInterface $container
     * @since 3.02
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * Включает или отключает плагин
     *
     * @return void
     */
    private function toggle()
    {
        $q = Eresus_DB::getHandler()->createUpdateQuery();
        $e = $q->expr;
        $q->update('plugins')
            ->set('active', $e->not('active'))
            ->where(
                $e->eq('name', $q->bindValue(arg('toggle')))
            );
        $q->execute();

        HttpResponse::redirect(Eresus_Kernel::app()->getPage()->url());
    }

    private function delete()
    {
        /** @var Eresus_Plugin_Registry $plugins */
        $plugins = $this->container->get('plugins');
        $plugins->load(arg('delete'));
        $plugins->uninstall(arg('delete'));
        HTTP::redirect(Eresus_Kernel::app()->getPage()->url());
    }

    /**
     * Настройки модуля
     *
     * @param string             $pluginName  имя модуля
     * @param Eresus_CMS_Request $request     запрос
     *
     * @return mixed
     *
     * @throws Eresus_CMS_Exception_NotFound
     */
    private function actionSettings($pluginName, Eresus_CMS_Request $request)
    {
        /** @var Eresus_Plugin_Registry $plugins */
        $plugins = $this->container->get('plugins');
        $plugin = $plugins->load($pluginName);
        if (false === $plugin)
        {
            throw new Eresus_CMS_Exception_NotFound;
        }

        $provider = new Eresus_Admin_ContentProvider_Plugin($plugin);
        $controller = $provider->getSettingsController();
        if (false === $controller)
        {
            throw new Eresus_CMS_Exception_NotFound;
        }

        $html = $controller->getHtml($request);

        $request = Eresus_Kernel::app()->getLegacyKernel()->request;
        if ('POST' == $request['method'])
        {
            HTTP::redirect(arg('submitURL'));
        }
        return $html;
    }

    /**
     * Подключает плагины
     *
     * @return void
     * @see add()
     */
    private function insert()
    {
        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        /** @var Eresus_Plugin_Registry $plugins */
        $plugins = $this->container->get('plugins');
        $files = arg('files');
        if ($files && is_array($files))
        {
            foreach ($files as $plugin => $install)
            {
                if ($install)
                {
                    try
                    {
                        $plugins->install($plugin);
                    }
                    catch (DomainException $e)
                    {
                        Eresus_Kernel::app()->getPage()->addErrorMessage($e->getMessage());
                    }
                }
            }

        }
        HttpResponse::redirect('admin.php?mod=plgmgr');
    }

    /**
     * Возвращает диалог добавления плагина
     *
     * @return string  HTML
     */
    private function add()
    {
        $data = array();

        /* Составляем список доступных плагинов */
        $files = glob(Eresus_CMS::getLegacyKernel()->froot . 'ext/*.php');
        if (false === $files)
        {
            $files = array();
        }

        /* Составляем список установленных плагинов */
        $items = Eresus_CMS::getLegacyKernel()->db->select('plugins', '', 'name, version');
        $installed = array();
        foreach ($items as $item)
        {
            $installed []= Eresus_CMS::getLegacyKernel()->froot . 'ext/' . $item['name'] . '.php';
        }
        // Оставляем только неустановленные
        $files = array_diff($files, $installed);

        /*
         * Собираем информацию о неустановленных плагинах
         */
        $data['plugins'] = array();
        if (count($files))
        {
            // Удаляем из версии CMS все буквы, чтобы сравнивать только цифры
            $kernelVersion = preg_replace('/[^\d\.]/', '', Eresus_Kernel::app()->version);

            foreach ($files as $file)
            {
                $errors = array();
                try
                {
                    $info = Eresus_PluginInfo::loadFromFile($file);
                    $required = $info->getRequiredKernel();
                    if (
                        version_compare($kernelVersion, $required[0], '<')/* ||
						version_compare($kernelVersion, $required[1], '>')*/
                    )
                    {
                        $msg =  I18n::getInstance()->getText('admPluginsInvalidVersion', $this);
                        $errors []= sprintf($msg, /*implode(' - ', */$required[0]/*)*/);
                    }
                }
                catch (RuntimeException $e)
                {
                    $errors []= $e->getMessage();
                    $info = new Plugin(); // TODO: Придумать решение без Plugin
                    $info->title = $info->name = basename($file, '.php');
                    $info->version = '';
                }
                $available[$info->name] = $info->version;
                $data['plugins'][$info->title] = array('info' => $info, 'errors' => $errors);
            }
        }

        /** @var Eresus_Plugin_Registry $plugins */
        $plugins = $this->container->get('plugins');

        foreach ($data['plugins'] as &$item)
        {
            if ($item['info'] instanceof Eresus_PluginInfo)
            {
                $required = $item['info']->getRequiredPlugins();
                foreach ($required as $plugin)
                {
                    list ($name, $minVer, $maxVer) = $plugin;
                    if (
                        !isset($plugins->items[$name]) ||
                        ($minVer && version_compare($plugins->items[$name]->version, $minVer, '<')) ||
                        ($maxVer && version_compare($plugins->items[$name]->version, $maxVer, '>'))
                    )
                    {
                        {
                            $msg = I18n::getInstance()->getText('Requires plugin: %s', $this);
                            $item['errors'] []= sprintf($msg, $name . ' ' . $minVer . '-' . $maxVer);
                        }
                    }
                }
            }
        }

        ksort($data['plugins']);

        /* @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $tmpl = $page->getUITheme()->getTemplate('PluginManager/add-dialog.html');
        $html = $tmpl->compile($data);

        return $html;
    }

    /**
     * Отрисовка контента модуля
     *
     * @param Eresus_CMS_Request $request
     *
     * @return string
     */
    public function adminRender(Eresus_CMS_Request $request)
    {
        if (!UserRights($this->access))
        {
            Eresus_Kernel::log(__METHOD__, LOG_WARNING, 'Access denied for user "%s"',
                Eresus_CMS::getLegacyKernel()->user['name']);
            return '';
        }

        Eresus_Kernel::log(__METHOD__, LOG_DEBUG, '()');

        $result = '';
        Eresus_Kernel::app()->getPage()->title = admPlugins;

        switch (true)
        {
            case arg('update') !== null:
                $result = $this->actionSettings(arg('update'), $request);
                break;
            case arg('toggle') !== null:
                $this->toggle();
                break;
            case arg('delete') !== null:
                $this->delete();
                break;
            case arg('id') !== null:
                $result = $this->actionSettings(arg('id'), $request);
                break;
            case arg('action') == 'add':
                $result = $this->add();
                break;
            case arg('action') == 'insert':
                $this->insert();
                break;
            default:
                $table = array (
                    'name' => 'plugins',
                    'key' => 'name',
                    'sortMode' => 'title',
                    'columns' => array(
                        array('name' => 'title', 'caption' => admPlugin, 'width' => '90px', 'wrap'=>false),
                        array('name' => 'description', 'caption' => admDescription),
                        array('name' => 'version', 'caption' => admVersion, 'width'=>'70px','align'=>'center'),
                    ),
                    'controls' => array (
                        'delete' => '',
                        'edit' => 'checkHasSettings',
                        'toggle' => '',
                    ),
                    'tabs' => array(
                        'width'=>'180px',
                        'items'=>array(
                            array('caption'=>admPluginsAdd, 'name'=>'action', 'value'=>'add')
                        )
                    )
                );
                /** @var TAdminUI $page */
                $page = Eresus_Kernel::app()->getPage();
                $result = $page->renderTable($table);
                break;
        }
        return $result;
    }

    /**
     * Проверяет, есть ли у модуля настройки
     * @param array $item
     * @return bool
     */
    public function checkHasSettings($item)
    {
        /** @var Eresus_Plugin_Registry $plugins */
        $plugins = $this->container->get('plugins');
        $plugin = $plugins->load($item['name']);
        if (false === $plugin)
        {
            return false;
        }
        $provider = new Eresus_Admin_ContentProvider_Plugin($plugin);
        return false !== $provider->getSettingsController();
    }
}

