<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Главный модуль
 *
 * @copyright 2004, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
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
 * @package Core
 *
 * $Id$
 */



/**
 * Интерфейс коннектора файлового менеджера
 *
 * @package CoreExtensionsAPI
 * @since 2.16
 */
interface FileManagerConnectorInterface
{
	/**
	 * Метод должен возвращать разметку для файлового менеджера директории data.
	 *
	 * @return string HTML
	 *
	 * @since 2.16
	 */
	public function getDataBrowser();
	//-----------------------------------------------------------------------------
}



/**
 * Исключение "Страница не найдена"
 *
 * @package Core
 * @since 2.16
 */
class PageNotFoundException extends DomainException {}



/**
 * Класс приложения Eresus CMS
 *
 * @package Core
 */
class Eresus_CMS
{
	/**
	 * Коренвая директория приложения
	 *
	 * Устанавливается в {@link initFS()}. Используйте {@link getFsRoot()} для получения значения.
	 *
	 * @var string
	 * @see getFsRoot(), initFS()
	 */
	private $fsRoot;

	/**
	 * Фронт-контроллер (АИ или КИ)
	 *
	 * @var object
	 */
	private $frontController;

	/**
	 * HTTP-запрос
	 *
	 * @var HttpRequest
	 */
	private $request;

	/**
	 * Адрес сайта
	 *
	 * @var string
	 */
	private $webRoot;

	/**
	 * Возвращает экземпляр-одиночку этого класса
	 *
	 * @return Eresus_CMS
	 *
	 * @since 2.16
	 */
	public static function app()
	{
		return Eresus_Kernel::app();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Основной метод приложения
	 *
	 * @return int  Код завершения для консольных вызовов
	 */
	public function main()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		try
		{
			$this->initFS();
			$this->checkEnviroment();
			$this->createFileStructure();

			Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Init legacy kernel');
			/* Подключение старого ядра */
			include_once 'kernel-legacy.php';
			$GLOBALS['Eresus'] = new Eresus;
			$this->initConf();
			if ($GLOBALS['Eresus']->conf['debug']['enable'])
			{
				include_once 'debug.php';
			}

			$this->initLocale();
			$this->initDB();
			$this->initSession();
			$GLOBALS['Eresus']->init();
			Eresus_Template::setGlobalValue('Eresus', $GLOBALS['Eresus']);

			if (Eresus_Kernel_PHP::isCLI())
			{
				return $this->runCLI();
			}
			else
			{
				$this->runWeb();
				return 0;
			}
		}
		catch (SuccessException $e)
		{
			return 0;
		}
		catch (Exception $e)
		{
			Eresus_Logger::exception($e);
			ob_end_clean();
			$this->fatalError($e, false);
		}

	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневую директорию приложения
	 *
	 * @return string
	 * @see $fsRoot
	 */
	public function getFsRoot()
	{
		return $this->fsRoot;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает корневой адрес сайта (без финального слеша)
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getWebRoot()
	{
		return $this->webRoot;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает путь к директории данных сайта (без финального слеша)
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getDataDir()
	{
		return $this->getFsRoot() . '/data';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает объект текущего фронт-контроллера
	 *
	 * @return object
	 *
	 * @since 2.16
	 */
	public function getFrontController()
	{
		return $this->frontController;
	}
	//-----------------------------------------------------------------------------

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
	public function fatalError($error = null, $exit = true)
	{
		include dirname(__FILE__) . '/fatal.html.php';
		if ($exit)
		{
			throw new ExitException;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка окружения
	 *
	 * @return void
	 */
	private function checkEnviroment()
	{
		$errors = array();

		/* Проверяем наличие нужных файлов */
		$required = array('cfg/main.php');
		foreach ($required as $filename)
		{
			if (!file_exists($filename))
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
			if (!is_writable($filename))
				$errors []= array('file' => $filename, 'problem' => 'non-writable');

		if ($errors)
		{
			if (!Eresus_Kernel_PHP::isCLI())
			{
				require_once 'errors.html.php';
			}
			else
			{
				die("Errors...\n"); // TODO Доделать
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Создание файловой структуры
	 *
	 * @return void
	 */
	private function createFileStructure()
	{
		$dirs = array(
			'/var/log',
			'/var/cache',
			'/var/cache/templates',
		);

		$errors = array();

		foreach ($dirs as $dir)
		{
			if (!file_exists($this->getFsRoot() . $dir))
			{
				$umask = umask(0000);
				mkdir($this->getFsRoot() . $dir, 0777);
				umask($umask);
			}
			// TODO Сделать проверку на запись в созданные директории
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполнение в режиме Web
	 */
	private function runWeb()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		$this->initWeb();

		$output = '';

		switch (true)
		{
			case substr($this->request->getLocal(), 0, 8) == '/ext-3rd':
				$this->call3rdPartyExtension();
			break;

			case substr($this->request->getLocal(), 0, 6) == '/admin':
				$output = $this->runWebAdminUI();
			break;

			default:
				$output = $this->runWebClientUI();
			break;
		}

		echo $output;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация Web
	 */
	private function initWeb()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		Eresus_Config::set('core.template.templateDir', $this->getFsRoot());
		Eresus_Config::set('core.template.compileDir', $this->getFsRoot() . '/var/cache/templates');
		// FIXME Следующая строка нужна только до перехода на UTF-8
		Eresus_Config::set('core.template.charset', 'CP1251');

		$this->request = HTTP::request();
		//$this->response = new HttpResponse();
		$this->detectWebRoot();
		//$this->initRoutes();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запуск КИ
	 * @return string
	 * @deprecated Это временная функция
	 */
	private function runWebClientUI()
	{
		global $page;

		Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

		include_once 'client.php';

		$page = new TClientUI();
		$page->init();
		/*return */$page->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Запуск АИ
	 * @return string
	 * @deprecated Это временная функция
	 */
	private function runWebAdminUI()
	{
		global $page;

		Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'This method is temporary.');

		define('ADMINUI', true);

		$page = new AdminUI();
		$this->frontController = new Eresus_Controller_Admin($page);

		return $this->frontController->render();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Выполнение в режиме CLI
	 */
	private function runCLI()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		$this->initCLI();
		return 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация CLI
	 */
	private function initCLI()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализирует работу с файловой системой
	 *
	 * Устанвливает {@link $fsRoot} при помощи{@link detectFsRoot()}.
	 *
	 * @return void
	 *
	 * @see $fsRoot, detectFsRoot()
	 */
	private function initFS()
	{
		$this->fsRoot = $this->detectFsRoot();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация конфигурации
	 */
	private function initConf()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		global $Eresus; // FIXME: Устаревшая переменная $Eresus

		@include_once $this->getFsRoot() . '/cfg/main.php';

		// TODO: Сделать проверку успешного подключения файла
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация локали
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	private function initLocale()
	{
		$i18n = Eresus_i18n::getInstance();
		if ($locale = Eresus_Config::get('eresus.cms.locale'))
		{
			$i18n->setLocale($locale);
		}
		else
		{
			$i18n->setLocale('ru_RU');
		}
		Eresus_Template::setGlobalValue('i18n', $i18n);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация БД
	 */
	private function initDB()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		/**
		 * Подключение Doctrine
		 */
		include $this->getFsRoot() . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR .
			'Doctrine.php';
		spl_autoload_register(array('Doctrine', 'autoload'));
		spl_autoload_register(array('Doctrine_Core', 'modelsAutoload'));

		$dsn = Eresus_Config::get('eresus.cms.dsn');
		if (!$dsn)
		{
			throw new DomainException('Configuration parameter "eresus.cms.dsn" not set.');
		}

		Doctrine_Manager::connection($dsn)->
			setCharset('cp1251'); // TODO Убрать после перехода на UTF

		$manager = Doctrine_Manager::getInstance();
		$manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
		$manager->setAttribute(Doctrine_Core::ATTR_VALIDATE, Doctrine_Core::VALIDATE_ALL);

		$prefix = Eresus_Config::get('eresus.cms.dsn.prefix');
		if ($prefix)
		{
			$manager->setAttribute(Doctrine_Core::ATTR_TBLNAME_FORMAT, $prefix . '%s');
		}

		Doctrine_Core::loadModels(dirname(__FILE__) . '/Model');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация сессии
	 *
	 * @return void
	 * @uses Eresus_Logger::log()
	 * @uses Eresus_Security_AuthService::getInstance()
	 */
	private function initSession()
	{
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '()');

		//session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		ini_set('session.use_only_cookies', true);
		session_name('sid');
		Eresus_Kernel_PHP::isCLI() || session_start();

		Eresus_Security_AuthService::getInstance()->init();
		// TODO Убрать. Оставлено для обратной совместимости
		$GLOBALS['Eresus']->user = Eresus_Security_AuthService::getInstance()->getUser();
		$_SESSION['activity'] = time();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обрабатывает запрос к стороннему расширению
	 *
	 * Вызов производитеся через коннектор этого расширения
	 *
	 * @return void
	 */
	private function call3rdPartyExtension()
	{
		$extension = substr($this->request->getLocal(), 9);
		$extension = substr($extension, 0, strpos($extension, '/'));

		$filename = $this->getFsRoot().'/ext-3rd/'.$extension.'/eresus-connector.php';
		if ($extension && is_file($filename))
		{
			include_once $filename;
			$className = $extension.'Connector';
			$connector = new $className;
			$connector->proxy();
		}
		else
		{
			header('404 Not Found', true, 404);
			echo '404 Not Found';
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Определение корневого веб-адреса сайта
	 *
	 * Метод определяет корневой адрес сайта и устанавливает соответствующим
	 * образом localRoot объекта Eresus_CMS::$request
	 */
	private function detectWebRoot()
	{
		$webServer = Eresus_WebServer::getInstance();
		$DOCUMENT_ROOT = $webServer->getDocumentRoot();
		$SUFFIX = $this->getFsRoot();
		$SUFFIX = substr($SUFFIX, strlen($DOCUMENT_ROOT));
		$this->request->setLocalRoot($SUFFIX);
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'detected root: %s', $SUFFIX);

		$this->webRoot = $this->request->getScheme() . '://' . $this->request->getHost() .
			$this->request->getLocalRoot();
		Eresus_Template::setGlobalValue('siteRoot', $this->webRoot);

	}
	//-----------------------------------------------------------------------------

	/**
	 * Пытается определить корневую директорию приложения
	 *
	 * В режиме CLI используется $GLOBALS['argv'][0].
	 *
	 * В остальных режимах используется $_SERVER['SCRIPT_FILENAME'].
	 *
	 * @return string
	 * @see $fsRoot, getFsRoot()
	 */
	private function detectFsRoot()
	{
		$path = false;

		switch (true)
		{
			case Eresus_Kernel_PHP::isCLI():
				$path = reset($GLOBALS['argv']);
				Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Using global $argv variable: %s', $path);
			break;

			default:
				$path = $_SERVER['SCRIPT_FILENAME'];
				Eresus_Logger::log(__METHOD__, LOG_DEBUG, 'Using $_SERVER["SCRIPT_FILENAME"]: %s', $path);
			break;
		}

		if (DIRECTORY_SEPARATOR != '/')
		{
			$path = str_replace($path, DIRECTORY_SEPARATOR, '/');
		}
		$path = dirname($path);
		Eresus_Logger::log(__METHOD__, LOG_DEBUG, '"%s"', $path);

		return $path;
	}
	//-----------------------------------------------------------------------------
}
