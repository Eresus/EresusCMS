<?php
/**
 * Контроллер управления расширениями
 *
 * @version ${product.version}
 * @copyright ${product.copyright}
 * @license ${license.uri} ${license.name}
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
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
 */

namespace Eresus\CmsBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use Eresus\CmsBundle\HTTP\Request;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use Eresus\CmsBundle\Extensions\Registry;
use Eresus\CmsBundle\Extensions\Plugin;

/**
 * Контроллер управления расширениями
 *
 * @since 4.0.0
 */
class AdminPluginsController extends AdminAbstractController
{
    /**
     * Список установленных модулей расширения
     *
     * @return Response
     *
     * @since 4.0.0
     */
    public function indexAction()
    {
        $vars = $this->createTemplateVars();

        /** @var Registry $registry */
        $registry = $this->get('extensions');
        $extensions = $registry->getInstalled();
        usort($extensions,
            function ($a, $b)
            {
                if ($a->title == $b->title)
                {
                    return 0;
                }
                return $a->title > $b->title ? 1 : -1;
            }
        );
        $vars['extensions'] = $extensions;
        return $this->render('CmsBundle:Plugins:index.html.twig', $vars);
    }

    /**
     * Установка нового модуля расширения
     *
     * @param Request $request
     *
     * @return Response
     *
     * @since 4.0.0
     */
    public function installAction(Request $request)
    {
        /** @var Registry $registry */
        $registry = $this->get('extensions');
        if ('POST' === $request->getMethod())
        {
            $install = $request->request->get('install');
            foreach ($install as $namespace)
            {
                $plugin = new Plugin($namespace, $this->container);
                $registry->install($plugin);
            }
            return $this->redirect('admin.plugins');
        }

        $vars = $this->createTemplateVars();

        $installed = $registry->getInstalled();
        $all = $registry->getAll();
        // Плагины, доступные для установки
        $available = array_diff_key($all, $installed);
        usort($available,
            function ($a, $b)
            {
                if ($a->title == $b->title)
                {
                    return 0;
                }
                return $a->title > $b->title ? 1 : -1;
            }
        );
        $vars['available'] = $available;
        return $this->render('CmsBundle:Plugins:install.html.twig', $vars);
    }

    /**
     * Возвращает массив переменных для подстановки в шаблон
     *
     * @return array
     *
     * @since 4.0.0
     */
    private function createTemplateVars()
    {
        return array(
            'mainMenuRoute' => 'admin.settings',
        );
    }
}

