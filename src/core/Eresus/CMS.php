<?php
/**
 * ${product.title}
 *
 * Главный модуль
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
 */

use Symfony\Component\DependencyInjection\Reference;

/**
 * Класс приложения Eresus CMS
 *
 * @package Eresus
 */
class Eresus_CMS
{
	/**
	 * @var Eresus_WebPage
	 * @since 3.00
	 */
	protected $page;

	/**
	 * @var Eresus
	 * @since 3.01
	 */
	protected static $legacyKernel;

	/**
	 * Основной метод приложения
	 *
	 * @return int  Код завершения для консольных вызовов
	 */
	public function main()
	{
		try
		{
			/* Общая инициализация */
			$this->checkEnvironment();
			$this->createFileStructure();

			$this->initServiceContainer();

			eresus_log(__METHOD__, LOG_DEBUG, 'Init legacy kernel');
			/* Подключение старого ядра */
			include ERESUS_APP_ROOT . '/core/kernel-legacy.php';

			self::$legacyKernel = new Eresus;
			$this->initConf();

			$i18n = Eresus_I18n::getInstance();
			TemplateSettings::setGlobalValue('i18n', $i18n);
			//$this->initDB();
			//$this->initSession();
			Eresus_CMS::getLegacyKernel()->init();
			TemplateSettings::setGlobalValue('Eresus', Eresus_CMS::getLegacyKernel());

			if (Eresus_Kernel::isCLI())
			{
				return $this->runCLI();
			}
			else
			{
				$this->runWeb();
			}
		}
		catch (Exception $e)
		{
			Core::logException($e);
			ob_end_clean();
			$this->fatalError($e, false);
		}
		return 0;
	}

	/**
	 * Выводит сообщение о фатальной ошибке и прекращает работу приложения
	 *
	 * @param Exception|string $error  исключение или описание ошибки
	 * @param bool             $exit   завершить или нет выполнение приложения
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function fatalError(/** @noinspection PhpUnusedParameterInspection */
		$error = null, $exit = true)
	{
		include ERESUS_APP_ROOT . '/core/fatal.html.php';
		die;
	}

	/**
	 * Возвращает корневую папку приложения
	 *
	 * @return string
	 */
	public function getFsRoot()
	{
		return ERESUS_APP_ROOT;
	}

	/**
	 * Возвращает экземпляр класса Eresus
	 *
	 * Метод нужен до отказа от класса Eresus
	 *
	 * @return Eresus
	 *
	 * @since 3.00
	 */
	public static function getLegacyKernel()
	{
		return self::$legacyKernel;
	}

	/**
	 * Возвращает экземпляр класса Eresus_ClientUI или Eresus_AdminUI
	 *
	 * Метод нужен до отказа от переменной $page
	 *
	 * @return Eresus_WebPage
	 *
	 * @since 3.00
	 */
	public function getPage()
	{
		return $this->page;
	}

	/**
	 * Проверка окружения
	 *
	 * @return void
	 */
	protected function checkEnvironment()
	{
		$errors = array();

		/* Проверяем наличие нужных файлов */
		$required = array('cfg/main.php');
		foreach ($required as $filename)
		{
			if (!FS::exists($filename))
			{
				$errors []= array('file' => $filename, 'problem' => 'missing');
			}
		}

		/* Проверяем доступность для записи */
		$writable = array(
			'cfg/settings.php',
			'var',
			'data',
			'templates',
			'style'
		);
		foreach ($writable as $filename)
		{
			if (!FS::isWritable($filename))
			{
				$errors []= array('file' => $filename, 'problem' => 'non-writable');
			}
		}

		if ($errors)
		{
			if (!PHP::isCLI())
			{
				include ERESUS_APP_ROOT . '/core/errors.html.php';
			}
			else
			{
				die("Errors...\n"); // TODO Доделать
			}
		}
	}

	/**
	 * Создание файловой структуры
	 *
	 * @return void
	 */
	protected function createFileStructure()
	{
		$dirs = array(
			'/var/log',
			'/var/cache',
			'/var/cache/templates',
		);

		foreach ($dirs as $dir)
		{
			if (!FS::exists($this->getFsRoot() . $dir))
			{
				$umask = umask(0000);
				mkdir(FS::nativeForm($this->getFsRoot() . $dir), 0777);
				umask($umask);
			}
			// TODO Сделать проверку на запись в созданные директории
		}
	}

	/**
	 * Выполнение в режиме Web
	 */
	protected function runWeb()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$this->initWeb();

		$output = '';
		/** @var Eresus_HTTP_Request $request */
		$request = Eresus_Kernel::get('request');

		switch (true)
		{
			case strpos($request->getLocalUrl(), '/ext-3rd') === 0:
				$this->call3rdPartyExtension();
			break;

			case strpos($request->getLocalUrl(), '/admin') === 0:
				$output = $this->runWebAdminUI();
			break;

			default:
				$output = $this->runWebClientUI();
			break;
		}

		echo $output;
	}

	/**
	 * Инициализация Web
	 */
	protected function initWeb()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		Core::setValue('core.template.templateDir', $this->getFsRoot());
		Core::setValue('core.template.compileDir', $this->getFsRoot() . '/var/cache/templates');

		//$this->response = new HttpResponse();

		/** @var Eresus_HTTP_Request $request */
		$request = Eresus_Kernel::get('request');
		TemplateSettings::setGlobalValue('siteRoot',
			$request->getScheme() . '://' . $request->getHost() . $request->getBasePath());

		/** @var Twig_Environment $twigEnv */
		$twigEnv = Eresus_Kernel::sc()->get('twig.environment');
		$twigEnv->addExtension(new Eresus_Twig_Extension());

		//$this->initRoutes();
	}

	/**
	 * Запуск КИ
	 * @return string
	 * @deprecated Это временная функция
	 */
	protected function runWebClientUI()
	{
		eresus_log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

		$this->page = new Eresus_ClientUI();
		$this->page->setContainer(Eresus_Kernel::sc());
		$this->page->init();
		/*return */$this->page->render();
	}

	/**
	 * Запуск АИ
	 * @return string
	 * @deprecated Это временная функция
	 */
	protected function runWebAdminUI()
	{
		eresus_log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

		$this->page = new Eresus_AdminUI();
		$this->page->setContainer(Eresus_Kernel::sc());
		/*return */$this->page->render();
	}

	/**
	 * Выполнение в режиме CLI
	 *
	 * @return int
	 */
	protected function runCLI()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$this->initCLI();
		return 0;
	}

	/**
	 * Инициализация CLI
	 */
	protected function initCLI()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');
	}

	/**
	 * Инициализация конфигурации
	 */
	protected function initConf()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		$filename = $this->getFsRoot() . '/cfg/main.php';
		if (file_exists($filename))
		{
			/** @noinspection PhpIncludeInspection */
			include $filename;
			// TODO: Сделать проверку успешного подключения файла
		}
		else
		{
			$this->fatalError("Main config file '$filename' not found!");
		}
	}

	/**
	 * Инициализация БД
	 */
	protected function initDB()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');
		/*
		global $Eresus;

		// FIXME Использование устаревших настроек
		$dsn = ($Eresus->conf['db']['engine'] ? $Eresus->conf['db']['engine'] : 'mysql') .
			'://' . $Eresus->conf['db']['user'] .
			':' . $Eresus->conf['db']['password'] .
			'@' . ($Eresus->conf['db']['host'] ? $Eresus->conf['db']['host'] : 'localhost') .
			'/' . $Eresus->conf['db']['name'];

		DBSettings::setDSN($dsn);*/
	}

	/**
	 * Инициализация сессии
	 */
	protected function initSession()
	{
		eresus_log(__METHOD__, LOG_DEBUG, '()');

		/*global $Eresus;

		session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		session_name('sid');
		session_start();

		# Обратная совместимость
		$Eresus->session = &$_SESSION['session'];
		#if (!isset($Eresus->session['msg']))
			$Eresus->session['msg'] = array('error' => array(), 'information' => array());
		#$Eresus->user = &$_SESSION['user'];
		$GLOBALS['session'] = &$_SESSION['session'];
		$GLOBALS['user'] = &$_SESSION['user'];*/
	}

	/**
	 * Обрабатывает запрос к стороннему расширению
	 *
	 * Вызов производится через коннектор этого расширения
	 *
	 * @return void
	 */
	protected function call3rdPartyExtension()
	{
		/** @var Eresus_HTTP_Request $request */
		$request = Eresus_Kernel::get('request');
		$extension = substr($request->getLocalUrl(), 9);
		$extension = substr($extension, 0, strpos($extension, '/'));

		$filename = $this->getFsRoot().'/ext-3rd/'.$extension.'/eresus-connector.php';
		if ($extension && is_file($filename))
		{
			/** @noinspection PhpIncludeInspection */
			include_once $filename;
			$className = $extension.'Connector';
			/** @var Eresus_Extensions_Connector $connector */
			$connector = new $className;
			$connector->proxy();
		}
		else
		{
			header('404 Not Found', true, 404);
			echo '404 Not Found';
		}
	}

	/**
	 * Подготавливает контейнер служб
	 */
	private function initServiceContainer()
	{
		$container = Eresus_Kernel::sc();

		$container->register('twig.loader', 'Twig_Loader_Filesystem')
			->addArgument($this->getFsRoot());
		$container->register('twig.environment', 'Twig_Environment')
			->addArgument(new Reference('twig.loader'))
			->addArgument(array('cache' => $this->getFsRoot() . '/var/cache/twig', 'autoescape' => false));
		$container->register('twig.parser', '\Symfony\Component\Templating\TemplateNameParser');
		$container->register('twig.locator', '\Symfony\Component\Config\FileLocator');
		$container->register('templating', '\Symfony\Bundle\TwigBundle\TwigEngine')
			->addArgument(new Reference('twig.environment'))
			->addArgument(new Reference('twig.parser'))
			->addArgument(new Reference('twig.locator'));

		$container->set('request', Eresus_HTTP_Request::createFromGlobals());
		$container->register('sections', 'Eresus_Sections');
	}
}
