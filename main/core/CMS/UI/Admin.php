<?php
/**
 * ${product.title}
 *
 * Административный интерфейс CMS
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
 *
 * @package Eresus
 *
 * $Id$
 */

/**
 * Административный интерфейс CMS
 *
 * @package EresusCMS
 * @since 2.16
 */
class Eresus_CMS_UI_Admin extends Eresus_CMS_UI
{
	/**
	 * Тема оформления
	 *
	 * @var Eresus_UI_Admin_Theme
	 */
	private $theme;

	/**
	 * @uses Eresus_ACL::getInstance()
	 * @uses Eresus_ACL::isGranted()
	 * @see Eresus_CMS_UI::process()
	 */
	public function process()
	{
		if (!Eresus_ACL::getInstance()->isGranted('EDIT'))
		{
			return $this->auth();
		}
		else
		{
			$req = Eresus_CMS_Request::getInstance();
			if ($req->getBasePath() == '/admin/logout')
			{
				Eresus_Auth::getInstance()->logout();
				Eresus_HTTP_Response::redirect($req->getRootPrefix() . '/admin/');
				//@codeCoverageIgnoreStart
			}
			//@codeCoverageIgnoreEnd

			return $this->main();
		}
/*
		$request = Eresus_CMS_Request::getInstance();

		try
		{
			$this->section = $router->findSection($request);
			$this->module = $this->section->getModule();
			$response = new Eresus_CMS_Response($this->module->clientRenderContent($this->section));
		}
		catch (Eresus_CMS_Exception_Forbidden $e)
		{
			$tmpl = Eresus_Service_Templates::getInstance()->get('errors/403');
			$html = $tmpl ? $tmpl->compile() : 'Access denied';
			$response = new Eresus_CMS_Response($html, Eresus_CMS_Response::FORBIDDEN);
		}
		catch (Eresus_CMS_Exception_NotFound $e)
		{
			$tmpl = Eresus_Service_Templates::getInstance()->get('errors/404');
			$html = $tmpl ? $tmpl->compile() : 'Not Found';
			$response = new Eresus_CMS_Response($html, Eresus_CMS_Response::NOT_FOUND);
		}

		return $response;*/
	}
	//-----------------------------------------------------------------------------

	/**
	 * Основной метод интерфейса
	 *
	 * @return Eresus_CMS_Response
	 *
	 * @since 2.16
	 */
	private function main()
	{
		$this->theme = new Eresus_UI_Admin_Theme();
		Eresus_Template::setGlobalValue('theme', $this->theme);

		$doc = new Eresus_HTML_Document();
		$doc->setTemplate('page.default', 'core');
		Eresus_Template::setGlobalValue('document', $doc);

		$ts = Eresus_Template_Service::getInstance();
		$req = Eresus_CMS_Request::getInstance();

		$controllerName = $req->getParam();
		$controllerClass = 'Eresus_Controller_Admin_' . $controllerName;

		try
		{
			if (!Eresus_Kernel::classExists($controllerClass))
			{
				throw new Eresus_CMS_Exception_NotFound;
			}

			$controller = new $controllerClass;
			$contents = $controller->execute($doc);
		}
		catch (Eresus_CMS_Exception_NotFound $e)
		{
			$tmpl = $ts->get('errors/NotFound', 'core');
			$doc->setVar('content', $tmpl->compile(array('error' => $e)));
		}
		return new Eresus_CMS_Response($doc->compile());
	}
	//-----------------------------------------------------------------------------

	/**
	 * Аутентификация
	 *
	 * @return string  HTML
	 *
	 * @uses Eresus_Model_User::USERNAME_FILTER
	 * @uses Eresus_Auth::getInstance()
	 * @uses Eresus_Auth::SUCCESS
	 * @uses Eresus_HTTP_Response::redirect()
	 */
	private function auth()
	{
		$req = Eresus_CMS_Request::getInstance();

		if ($req->isPOST())
		{
			$username = trim($req->getPost()->get('username', Eresus_Model_User::USERNAME_FILTER));
			$password = trim($req->getPost()->get('password'));
			$state = Eresus_Auth::getInstance()->login($username, $password);
			if ($state == Eresus_Auth::SUCCESS)
			{
				if ($req->getPost()->get('autologin'))
				{
					Eresus_Auth::getInstance()->setCookies();
				}
				Eresus_HTTP_Response::redirect($req->getHeader('Referer'));
				//@codeCoverageIgnoreStart
			}
			//@codeCoverageIgnoreEnd
			$html = $this->getAuthScreen(i18n('Invalid username or password', 'admin.auth'));
		}
		else
		{
			$html = $this->getAuthScreen();
		}
		return new Eresus_CMS_Response($html);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает диалог аутентификации
	 *
	 * @param string $errorMessage  сообщение об ошибке
	 * @return string
	 */
	private function getAuthScreen($errorMessage = '')
	{
		$req = Eresus_CMS_Request::getInstance();

		$data = array(
			'username' => $req->getPost()->get('username', Eresus_Model_User::USERNAME_FILTER),
			'password' => $req->getPost()->get('password'),
			'autologin' => $req->getPost()->get('autologin'),
			'error' => $errorMessage
		);
		$ts = Eresus_Template_Service::getInstance();
		$tmpl = $ts->get('auth', 'core');
		$html = $tmpl->compile($data);
		return $html;
	}
	//-----------------------------------------------------------------------------

}
