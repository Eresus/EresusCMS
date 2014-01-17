<?php
/**
 * Фронт-контроллер АИ
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
 * Фронт-контроллер АИ
 *
 * @package Eresus
 * @since 3.01
 */
class Eresus_Admin_FrontController extends Eresus_CMS_FrontController
{
    /**
     * Выполняет действия контроллера и возвращает ответ
     *
     * @return Eresus_HTTP_Response
     * @since 3.01
     */
    public function dispatch()
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcher $evd */
        $evd = $this->container->get('events');
        $evd->dispatch('cms.admin.start');

        if (!UserRights(EDITOR))
        {
            $response = $this->authAction();
        }
        else
        {
            /** @var TAdminUI|TClientUI $page */
            $page = $this->getPage();
            $response = $page->render($this->getRequest());
        }
        return $response;
    }

    /**
     * Создаёт объект Eresus_CMS_Page
     *
     * @return Eresus_CMS_Page
     * @since 3.01
     */
    protected function createPage()
    {
        return new TAdminUI($this->container);
    }

    /**
     * Отрисовка страницы аутентификации
     *
     * @return Eresus_HTTP_Response
     *
     * @since 3.01
     */
    private function authAction()
    {
        $data = array('errors' => array(), 'user' => '', 'autologin' => '');

        $legacyKernel = Eresus_CMS::getLegacyKernel();
        $req = $this->getRequest();
        if ($req->getMethod() == 'POST')
        {
            $user = $req->request->filter('user', null, FILTER_REGEXP, '/[^a-z0-9_\-\.\@]/');
            $password = $req->request->get('password');
            $autologin = $req->request->get('autologin');
            if ($legacyKernel->login($user, $legacyKernel->password_hash($password), $autologin))
            {
                return new Eresus_HTTP_Redirect($legacyKernel->root . 'admin.php');
            }
            $data['user'] = $user;
            $data['autologin'] = $autologin;
        }

        $data['errors'] = $this->getPage()->getErrorMessages();
        $this->getPage()->clearErrorMessages();
        if (isset($legacyKernel->session['msg']['errors']) &&
            count($legacyKernel->session['msg']['errors']))
        {
            $data['errors'] = $legacyKernel->session['msg']['errors'];
            $legacyKernel->session['msg']['errors'] = array();
        }

        $tmpl = Eresus_Template::loadFromFile('core/templates/auth.html');
        $html = $tmpl->compile($data);
        $response = new Eresus_HTTP_Response($html);
        return $response;
    }
}

