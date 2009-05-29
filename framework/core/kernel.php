<?php
/**
 * Eresus Core
 *
 * @version 0.1.0
 *
 * Kernel module
 *
 * @copyright 2007-2009, Eresus Project, http://eresus.ru/
 * @license http://www.gnu.org/licenses/gpl.txt GPL License 3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package Core
 * @subpackage Kernel
 *
 * @author  Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id$
 */

/**
 * Eresus Core version
 */
define('ERESUS_CORE_VERSION', '0.1.0');

/**
 * Emergency memory buffer size in KiB
 */
define('ERESUS_MEMORY_OVERFLOW_BUFFER', 64);

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Logging Functions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


/**
 * Write message to log
 *
 * @param string $sender                    Sender name. Use __METHOD__ or __FUNCTION__
 * @param int    $priority                  Message priority. See LOG_XXX
 * @param string $message                   Message. Can contain substitutions (see sprintf)
 * @param mixed  $args1..$argsN [optional]  Some variables
 */
function elog($sender, $priority, $message)
{
	/*
	 * Because of LOG_XXX constants values order, we use ">" to check if message
	 * priority is lower than current log level
	 */
	$ERESUS_LOG_LEVEL = defined('ERESUS_LOG_LEVEL') ? ERESUS_LOG_LEVEL : LOG_ERR;

	if ($priority > $ERESUS_LOG_LEVEL) return;

	/* Substitute vars if any */
	if (@func_num_args() > 3) {
		$args = array();
		for($i = 3; $i < @func_num_args(); $i++) {
			$var = func_get_arg($i);
			if (is_object($var)) $var = get_class($var);
			$args []= $var;
		}
		$message = vsprintf($message, $args);
	}

	/* Add sender */
	if (empty($sender)) $sender = 'unknown';
	$message = $sender . ': ' . $message;

	/* Add priority info */
	switch ($priority) {
		case LOG_DEBUG:   $priorityName = 'debug';    break;
		case LOG_INFO:    $priorityName = 'info';     break;
		case LOG_NOTICE:  $priorityName = 'notice';   break;
		case LOG_WARNING: $priorityName = 'warning';  break;
		case LOG_ERR:     $priorityName = 'error';    break;
		case LOG_CRIT:    $priorityName = 'critical'; break;
		case LOG_ALERT:   $priorityName = 'ALERT';    break;
		case LOG_EMERG:   $priorityName = 'PANIC';    break;
		default: $priorityName = 'unknown';
	}
	$message = '[' . $priorityName . '] ' . $message;

	# Log message
	if (!error_log($message)) {

		if (!syslog($priority, $message)) {
			fputs(STDERR, "Can not log message!\n");
			exit(-1);
		}

	}

}
//-----------------------------------------------------------------------------


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Exceptions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


/**
 * Eresus exception interface
 *
 * Eresus Core uses extended interface for exceptions, wich provides:
 * - Detailed description for occured exception
 * - Method to get real exception class name (for wrapper exceptions)
 * - Own method to get trace as string (for wrapper exceptions)
 * - Implements PHP 5.3 "getPrevious"-like functional
 *
 * As soon as Eresus exceptions can be derived from a different
 * standard PHP exceptions they must all implement this interface
 * class.
 *
 * @package Core
 * @subpackage Kernel
 */
interface EresusExceptionInterface {

	/**
	 * Full exception description
	 *
	 * @return string
	 */
	public function getDescription();

	/**
	 * Exception class name
	 *
	 * @return string
	 */
	public function getClass();

	/**
	 * Call trace as string
	 *
	 * @return string
	 */
	public function getBacktraceAsString();

	/**
	 * Get previous exception
	 *
	 * @return Exception
	 */
	public function getPreviousException();
}


/**
 * Runtime exception
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusRuntimeException extends RuntimeException implements EresusExceptionInterface {

	/**
	 * Previous exception
	 *
	 * @var Exception
	 */
	protected $previous;

	/**
	 * Full description of an exception
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Creates new exception object
	 *
	 * $message must be a short exception description wich can be safely
	 * showed to user. And $description can contain a full description
	 * wich will be logged.
	 *
	 * @param string    $description [optional]  Extended information
	 * @param string    $message	[optional]     Error message
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct($description = null, $message = null, $previous = null)
	{
		if (is_null($description) || empty($description)) $description = 'Description unavailable';
		if (is_null($message)) $message = get_class($this);

		if (Core::testMode()) $message .= ': ' . $description;

		if (PHP::checkVersion('5.3')) {

			parent::__construct($message, 0, $previous);

		} else {

			parent::__construct($message, 0);
			$this->previous = $previous;

		}

		$this->description = $description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Returns value of the $description property
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return exception class name
	 * @return string
	 */
	public function getClass()
	{
		return get_class($this);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return call trace as string
	 * @return array
	 */
	public function getBacktraceAsString()
	{
		return $this->getTraceAsString();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get previous exception
	 *
	 * @return Exception
	 */
	public function getPreviousException()
	{
		if (PHP::checkVersion('5.3'))
			return parent::getPrevious();

		else
			return $this->previous;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Logic exception
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusLogicException extends LogicException implements EresusExceptionInterface {

	/**
	 * Previous exception
	 *
	 * @var Exception
	 */
	protected $previous;

	/**
	 * Full description of an exception
	 *
	 * @var string
	 */
	protected $description;

	/**
	 * Creates new exception object
	 *
	 * $message must be a short exception description wich can be safely
	 * showed to user. And $description can contain a full description
	 * wich will be logged.
	 *
	 * @param string    $description [optional]  Extended information
	 * @param string    $message	[optional]     Error message
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct($description = null, $message = null, $previous = null)
	{
		if (is_null($description) || empty($description)) $description = 'Description unavailable';
		if (is_null($message)) $message = get_class($this);

		if (Core::testMode()) $message .= ': ' . $description;

		if (PHP::checkVersion('5.3')) {

			parent::__construct($message, 0, $previous);

		} else {

			parent::__construct($message, 0);
			$this->previous = $previous;

		}

		$this->description = $description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Returns value of the $description property
	 *
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return exception class name
	 * @return string
	 */
	public function getClass()
	{
		return get_class($this);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return call trace as string
	 * @return array
	 */
	public function getBacktraceAsString()
	{
		return $this->getTraceAsString();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get previous exception
	 *
	 * @return Exception
	 */
	public function getPreviousException()
	{
		if (PHP::checkVersion('5.3'))
			return parent::getPrevious();

		else
			return $this->previous;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Extends stardard Exception class
 *
 * This is a parent class for all Eresus exceptions
 *
 * @deprecated
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusException extends EresusRuntimeException {

	/**
	 * Creates new exception object and logs info about exception
	 *
	 * $message must be a short exception description wich can be safely
	 * showed to user. And $description can contain a full description
	 * wich will be logged.
	 *
	 * @param string $description [optional]  Extended information
	 * @param string $message	[optional]      Error message
	 * @param int    $code [optional]         Error code
	 */
	function __construct($description = null, $message = null, $code = null)
	{
		/* Legacy argument order support */
		if ((is_null($code) || is_string($code)) && is_numeric($message)) {
			$tmp = $code;
			$code = $message;
			$message = $description;
			$description = $tmp;
			unset($tmp);
		}

		parent::__construct($description, $message);
		$this->code = intval($code);
	}
	//-----------------------------------------------------------------------------
}

/**
 * "Type" exception
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusTypeException extends EresusRuntimeException {

	/**
	 * Creates new exception object
	 *
	 * @param mixed     $var [optional]           Variable with a type problem
	 * @param string    $expectedType [optional]  Expected type
	 * @param string    $description [optional]   Extended information
	 * @param Exception $previous [optional]      Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$var = func_get_arg(0);
			$expectedType = func_num_args() > 1 ? func_get_arg(1) : null;
			$description = func_num_args() > 2 ? func_get_arg(2) : null;
			$previous = func_num_args() > 3 ? func_get_arg(3) : null;

			$actualType = gettype($var);

			if (is_null($expectedType))
				$message = 'Unexpected value type: ' . $actualType;
			else
				$message = 'Expecting ' .	$expectedType . ' but got "' . $actualType .'"';

			if ($description) $message .= ' ' . $description;
			parent::__construct($message, 'Type error', $previous);

		} else parent::__construct('Type error');
	}
	//-----------------------------------------------------------------------------
}

/**
 * "Value" exception
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusValueException extends EresusLogicException {

	/**
	 * Creates new exception object
	 *
	 * @param string    $valueName [optional]    Value name
	 * @param mixed     $value [optional]        Value
	 * @param string    $description [optional]  Extended information
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$valueName = func_get_arg(0);
			$value = func_num_args() > 1 ? func_get_arg(1) : null;
			$description = func_num_args() > 2 ? func_get_arg(2) : null;
			$previous = func_num_args() > 3 ? func_get_arg(3) : null;

			if (is_null($value))
				$message = "Invalid value of \"$valueName\"";
			else
				$message = "\"$valueName\" has invalid value: $value";

			if ($description) $message .= ' ' . $description;
			parent::__construct($message, 'Invalid value', $previous);

		} else parent::__construct('Invalid value');
	}
	//-----------------------------------------------------------------------------
}


/**
 * "Property not exists" exception
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusPropertyNotExistsException extends EresusRuntimeException {

	/**
	 * Creates new exception object
	 *
	 * @param string    $property [optional]     Property name
	 * @param string    $class [optional]        Class name
	 * @param string    $description [optional]  Extended information
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$property = func_get_arg(0);
			$class = func_num_args() > 1 ? func_get_arg(1) : null;
			$description = func_num_args() > 2 ? func_get_arg(2) : null;
			$previous = func_num_args() > 3 ? func_get_arg(3) : null;

			if (is_null($class))
				$message = "Property \"$property\" does not exists";
			else
				$message = "Property \"$property\" does not exists in class \"$class\"";

			if ($description) $message .= ' ' . $description;
			parent::__construct($message, 'Property not exists', $previous);

		} else parent::__construct('Property not exists');
	}
	//-----------------------------------------------------------------------------
}


/**
 * "Method not exists" exception
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusMethodNotExistsException extends EresusRuntimeException {

	/**
	 * Creates new exception object
	 *
	 * @param string    $method [optional]       Method name
	 * @param string    $class [optional]        Class name
	 * @param string    $description [optional]  Extended information
	 * @param Exception $previous [optional]     Previous exception
	 */
	function __construct()
	{
		if (func_num_args() > 0) {

			$method = func_get_arg(0);
			$class = func_num_args() > 1 ? func_get_arg(1) : null;
			$description = func_num_args() > 2 ? func_get_arg(2) : null;
			$previous = func_num_args() > 3 ? func_get_arg(3) : null;

			if (is_null($class))
				$message = "Method \"$method\" does not exists";
			else
				$message = "Method \"$method\" does not exists in class \"$class\"";

			if ($description) $message .= ' ' . $description;
			parent::__construct($message, 'Method not exists', $previous);

		} else parent::__construct('Method not exists');
	}
	//-----------------------------------------------------------------------------
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   PHP Functions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Major and minor version numbers (N.N.x-xx)
 */
define('VERSION_ID', 0x01);

/**
 * Major version number (N.x.x-xx)
 */
define('VERSION_MAJOR', 0x02);

/**
 * Minor version number (x.N.x-xx)
 */
define('VERSION_MINOR', 0x03);

/**
 * Release number (x.x.N-xx)
 */
define('VERSION_RELEASE', 0x04);

/**
 * Extra information (x.x.x-NN)
 */
define('VERSION_EXTRA', 0x05);

/**
 * PHP information
 *
 * Part of functions was taken from a {@link http://limb-project.com/ Limb3 project}
 *
 * @package Core
 * @subpackage Kernel
 */
class PHP {

	/**
	 * Plain PHP version
	 * @var string
	 */
	private static $phpVersion = PHP_VERSION;

	/**
	 * Parsed version cache
	 * @var string
	 */
	private static $version = null;

  /**
   * Substitute PHP version with specified value
   *
   * @param string $version
   */
	public static function setVersion($version)
	{
		self::$phpVersion = is_null($version) ? PHP_VERSION : $version;
		self::$version = null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get PHP version
	 *
	 * @param int $part  Version part to return
	 * @return string
	 *
	 * @see VERSION_XXX constants
	 */
	public static function version($part = null)
	{
		if (is_null($part)) return self::$phpVersion;

		if (is_null(self::$version)) {
			/* Parse PHP version only once */
			preg_match('/^(\d+)\.(\d+)\.(\d+).?(.+)?/', self::$phpVersion, $v);
			self::$version[VERSION_ID]      = $v[1] . '.' . $v[2];
			self::$version[VERSION_MAJOR]   = $v[1];
			self::$version[VERSION_MINOR]   = $v[2];
			self::$version[VERSION_RELEASE] = isset($v[3]) ? $v[3] : 0;
			self::$version[VERSION_EXTRA]   = isset($v[4]) ? $v[4] : 0;
		}
		$result = self::$version[$part];

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if current PHP version is equal or higher then $version
	 *
	 * @param string $version
	 * @return bool
	 */
	static function checkVersion($version)
	{
		return version_compare(self::$phpVersion, $version, '>=');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check for CLI SAPI
	 *
	 * @return bool
	 */
	static function isCLI()
	{
		if (Core::testModeIsSet('PHP::isCLI')) return Core::testModeGet('PHP::isCLI');
		return PHP_SAPI == 'cli';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check for CGI SAPI
	 *
	 * @return bool
	 */
	static function isCGI()
	{
		return strncasecmp(PHP_SAPI, 'CGI', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check for web server SAPI
	 *
	 * @return bool
	 */
	static function isModule()
	{
		return !self::isCGI() && isset($_SERVER['GATEWAY_INTERFACE']);
	}
	//-----------------------------------------------------------------------------

	/**
	 * OS independant PHP extension load.
	 *
	 * @param string $ext  Extension name
	 * @return bool Success or not on the dl() call
	 *
	 * @todo UnitTest
	 */
	static function loadExtension($ext)
	{
		if (extension_loaded($ext)) return true;
		elog(__METHOD__, LOG_DEBUG, '(%s)', $ext);

		$enable_dl = Core::testMode() ? Core::testModeGet('enable_dl') : ini_get('enable_dl');
		if (!$enable_dl) {
			elog(__METHOD__, LOG_NOTICE, 'Dynamic extension loading disabled by PHP settings');
			return false;
		}

		$safe_mode = Core::testMode() ? Core::testModeGet('safe_mode') : ini_get('safe_mode');
		if ($safe_mode) {
			elog(__METHOD__, LOG_NOTICE, 'Dynamic extension loading not allowed in a Safe Mode');
			return false;
		}

		$prefix = System::isWindows() ? 'php_' : '';
		$filename = $prefix . $ext . '.' . PHP_SHLIB_SUFFIX;

		elog(__METHOD__, LOG_DEBUG, 'Trying to load "%s"', $filename);

		@$result = dl($filename);

		elog(__METHOD__, LOG_DEBUG, 'result: %d', $result);

		return Core::testMode() ? $filename : $result;
	}
	//-----------------------------------------------------------------------------
}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   System Functions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * System information
 *
 * Part of functions was taken from a Limb3 project - http://limb-project.com/
 *
 * @package Core
 * @subpackage Kernel
 */
class System {

	/**
	 * Init
	 *
	 * @todo UnitTest
	 */
	public static function init()
	{
		@$timezone = date_default_timezone_get();
		date_default_timezone_set($timezone);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if system is a UNIX-like
	 *
	 * @return bool
	 *
	 * @todo UnitTest for OSes  other then UNIX
	 */
	static function isUNIX()
	{
		return DIRECTORY_SEPARATOR == '/';
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if system is a Microsoft Windows
	 *
	 * @return bool
	 *
	 * @todo UnitTest for OSes  other then UNIX
	 */
	static function isWindows()
	{
		if (Core::testModeGet('System::isWindows')) return true;
		return strncasecmp(PHP_OS, 'WIN', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if system is a MacOS
	 *
	 * @return bool
	 *
	 * @todo UnitTest for OSes  other then UNIX
	 */
	static function isMac()
	{
		return strncasecmp(PHP_OS, 'MAC', 3) == 0;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get system time zone
	 * @return string
	 *
	 * @todo UnitTest
	 */
	public static function getTimezone()
	{
		return date_default_timezone_get();
	}
	//-----------------------------------------------------------------------------
}



/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Filesystem Functions
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */


/**
 * Filesystem abstraction layer
 *
 * This class provides static methods for system independent file operations.
 * Special driver classes are used for particular file systems.
 *
 * The most important goal of FS is uniform file names for UNIX and Windows
 * systems.
 *
 * @package Core
 * @subpackage Kernel
 */
class FS {

	/**
	 * Filesystem driver
	 * @var GenericFS
	 */
	static private $driver;

	/**
	 * Init FS module
	 *
	 * Load FS driver for current system
	 */
	static public function init($driver = null)
	{
		elog(__METHOD__, LOG_DEBUG, '(%s)', $driver);
		self::$driver = null;

		/* User defined driver */
		if ($driver) {

			if ($driver instanceof GenericFS)
				self::$driver = $driver;
			else
				elog(__METHOD__, LOG_ERR, 'Invalid FS driver: '.gettype($driver));

		}

		/* Autodetect */
		if (is_null(self::$driver)) {

			elog(__METHOD__, LOG_DEBUG, 'Autodetecting file system...');

			if (System::isWindows()) {

				self::$driver = new WindowsFS();

			}

		}

		/* Generic driver */
		if (is_null(self::$driver)) self::$driver = new GenericFS();

		elog(__METHOD__, LOG_DEBUG, 'Using FS driver: %s', self::$driver);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get current FS driver
	 *
	 * @return GenericFS|null
	 */
	public static function driver()
	{
		return self::$driver;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Normalize file name
	 *
	 * In terms of FS, normal form of file name is:
	 *   - Unix-like directory separator (/)
	 *   - Absence of substitution symbols ('../', './')
	 *
	 * @param string $filename
	 * @param string $type [optional]  Optional file type, can be:
	 *                                   'file' or NULL - regular file
	 *                                   'dir' or 'directory' - directory
	 * @return string
	 */
	static public function normalize($filename, $type = null)
	{
		return self::$driver->normalize($filename, $type);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert canonical (UNIX) filename to filesystem native form
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see FS::canonicalForm()
	 */
	static public function nativeForm($filename)
	{
		return self::$driver->nativeForm($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert filename from filesystem native form to canonical (UNIX)
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see FS::nativeForm()
	 */
	static public function canonicalForm($filename)
	{
		return self::$driver->canonicalForm($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Checks if file exists in file system
	 *
	 * @param string $filename
	 * @return bool
	 */
	static public function exists($filename)
	{
		return self::$driver->exists($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get normalized directory name of a given filename
	 *
	 * @param string $filename
	 * @return string
	 */
	static public function dirname($filename)
	{
		return self::$driver->dirname($filename);
	}
	//-----------------------------------------------------------------------------

}


/**
 * Generic file system class
 *
 * @package Core
 * @subpackage Kernel
 */
class GenericFS {

	/**
	 * Normalize file name
	 *
	 * Function converts given filename to the normal UNIX form:
	 *
	 * /some/path/filename
	 *
	 * 1. Adds slash at the end of directory name (see $type)
	 *
	 * @param string $filename         File name to normalize
	 * @param string $type [optional]  Optional file type, can be:
	 *                                  'file' or NULL - regular file
	 *                                  'dir' or 'directory' - directory
	 * @return string
	 */
	public function normalize($filename, $type = null)
	{
		switch ($type) {
			case 'dir':
			case 'directory':
				if (substr($filename, -1) != '/') $filename .= '/';
			break;

			case 'file':
			case null:
				# Do nothing
			break;

			default: throw new EresusValueException('$type', $type);
		}

		$filename = $this->expandParentLinks($filename);

		$filename = $this->tidy($filename);

		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Correct some errors
	 *
	 * 1. Replace multiple serial directory separators with one
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function tidy($filename)
	{
		$filename = preg_replace('~/{2,}~', '/', $filename);
		$filename = str_replace('/./', '/', $filename);
		$filename = preg_replace('~^./~', '', $filename);

		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Expand links to parent directory ('..')
	 *
	 * @param string $filename
	 * @return string
	 */
	protected function expandParentLinks($filename)
	{
		if (strpos($filename, '..') === false) return $filename;

		$path = $filename;

		if ($path) {

			$parts = explode('/', $path);

			for ($i = 0; $i < count($parts); $i++) {

				if ($parts[$i] == '..') {
					if ($i > 1) {
						array_splice($parts, $i-1, 2);
						$i -= 2;
					} else {
						array_splice($parts, $i, 1);
						$i -= 1;
					}
				}

			}

			$path = implode('/', $parts);

		}

		$filename = $path;

		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert canonical (UNIX) filename to filesystem native form
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see GenericFS::canonicalForm()
	 */
	public function nativeForm($filename)
	{
		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert filename from filesystem native form to canonical (UNIX)
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see GenericFS::nativeForm()
	 */
	public function canonicalForm($filename)
	{
		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get normalized file directory name
	 * @param string $filename
	 * @return string
	 */
	public function dirname($filename)
	{
		$path = $filename;

		$lastDirSep = strrpos($path, '/');
		if ($lastDirSep !== false) $path = substr($path, 0, $lastDirSep+1);

		if ($path === '') $path = '.';

		$path = $this->normalize($path, 'dir');

		return $path;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Checks if file exists in file system
	 * @param string $filename
	 * @return bool
	 */
	public function exists($filename)
	{
		return file_exists($this->nativeForm($filename));
	}
	//-----------------------------------------------------------------------------
}

/**
 * Microsoft(R) Windows(TM) file system driver class
 *
 * @package Core
 * @subpackage FS
 */
class WindowsFS extends GenericFS {

	/**
	 * Convert canonical (UNIX) filename to Windows native form
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see WindowsFS::canonicalForm()
	 */
	public function nativeForm($filename)
	{
		/* Look for drive letter */
		if (preg_match('~^/[a-z]:/~i', $filename)) {

			$drive = substr($filename, 1, 1);
			$filename = substr($filename, 4);

		} else $drive = false;

		/* Convert slashes */
		$filename = str_replace('/', '\\', $filename);

		/* Prepend drive letter if needed */
		if ($drive) {
			$filename = $drive . ':\\' . $filename;
		}

		return $filename;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Convert filename from Windows native form to canonical (UNIX)
	 *
	 * @param string $filename
	 * @return string
	 *
	 * @see WindowsFS::nativeForm()
	 */
	public function canonicalForm($filename)
	{
		/* Convert slashes */
		$filename = str_replace('\\', '/', $filename);

		/* Prepend drive letter with slash if needed */
		if (substr($filename, 1, 1) == ':')
			$filename = '/' . $filename;

		return $filename;
	}
	//-----------------------------------------------------------------------------

}


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 *
 *   Error & Exception Handling
 *
 *   ...
 *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 * Stardard Exception class wrapper
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusExceptionDecorator extends EresusRuntimeException {

	/**
	 * @var Exception  Parent Exception
	 */
	protected $parent;

	/**
	 * Constructor
	 *
	 * @param Exception $e
	 */
	function __construct($e)
	{
		$this->parent = $e;
		parent::__construct($this->parent->getMessage(), $this->getClass(), $this->parent);
		$this->code = $this->parent->getCode();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return exception class name
	 * @return string
	 */
	function getClass()
	{
		return get_class($this->parent);
	}
	//-----------------------------------------------------------------------------

	/**
	 *
	 * Return call trace as string
	 * @return array
	 */
	function getBacktraceAsString()
	{
		return $this->parent->getTraceAsString();
	}
	//-----------------------------------------------------------------------------
}

/**
 * Error wrapper
 *
 * @package Core
 * @subpackage Kernel
 */
class EresusErrorDecorator extends EresusRuntimeException {

	/**
	 * Call trace
	 * @var array
	 */
	protected $trace;

	/**
	 * @var array  Error context
	 */
	protected $context;

	/**
	 * Constructor
	 *
   * @param string $errstr      Error description
   * @param int    $errno       Error type
   * @param string $errfile     Filename where error occured
   * @param int    $errline     Line where error occured
   * @param array  $errcontext  Context symbol table
	 */
	function __construct($errstr, $errno, $errfile, $errline, $errcontext)
	{
		parent::__construct($errstr, 'Error (see log for more info)');
		$this->file = $errfile;
		$this->line = $errline;
		$this->code = $errno;
		$this->context = $errcontext;
		$this->trace = debug_backtrace();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Return call trace as string
	 * @return array
	 */
	function getBacktraceAsString()
	{
		$trace = '';
		$level = 1;

		foreach ($this->trace as $item) {

			$line = $item['function'];
			if (isset($item['class'])) $line = $item['class'] . $item['type'] . $line;
			if (isset($item['args'])) {
				for($i = 0; $i < count($item['args']); $i++) {
					switch (true) {

						case is_string($item['args'][$i]):
							if (strlen($item['args'][$i]) > 48) $item['args'][$i] = substr($item['args'][$i], 0, 48) . '...';
							$item['args'][$i] = "'" . $item['args'][$i] . "'";
						break;
						case is_array($item['args'][$i]): $item['args'][$i] = 'array(' . count($item['args'][$i]) . ')'; break;
						case is_object($item['args'][$i]): $item['args'][$i] = 'object ' . get_class($item['args'][$i]); break;
					}
				}
				$line .= '(' . implode(', ', $item['args']) . ')';
				if (isset($item['file'])) $line .= ' in '.$item['file'].':'.$item['line'];
			}
			$trace .= '#' . ($level++) . ' ' . $line . "\n";

		}

		return $trace . "\n" . print_r($this->context, true);
	}
	//-----------------------------------------------------------------------------
}


/**
 * Error handler
 *
 * @param int    $errno       Error type
 * @param string $errstr      Error description
 * @param string $errfile     Filename where error occured
 * @param int    $errline     Line where error occured
 * @param array  $errcontext  Context symbol table
 */
function EresusErrorHandler($errno, $errstr, $errfile, $errline, $errcontext)
{
	/* Zero value of 'error_reporting' means that "@" operator was used, if so, exiting */
	if (error_reporting() == 0) return true;

	/*
	 *  Note: Actualy only E_WARNING, E_NOTICE, E_USER_ERROR, E_USER_WARNING,
	 *  E_USER_NOTICE and E_STRICT can be handled by this function
	 */

	/* Convert error code to log level */
	switch ($errno) {
		case E_STRICT:
		case E_NOTICE:
		case E_USER_NOTICE:
			 $level = LOG_NOTICE;
		break;
		case E_WARNING:
		case E_USER_WARNING:
			$level = LOG_WARNING;
		break;
		default: $level = LOG_ERR;
	}

	if ($level < LOG_NOTICE) {

		throw new EresusErrorDecorator($errstr, $errno, $errfile, $errline, $errcontext);

	} else {

		$logMessage = sprintf(
			"%s in %s:%s",
			$errstr,
			$errfile,
			$errline
		);
		elog(__FUNCTION__, $level, $logMessage);

	}

	return true;
}
//-----------------------------------------------------------------------------

/**
 * Exception handler
 *
 * @param Exception $e  Exception object
 *
 */
function EresusExceptionHandler($e)
{
	if (! ($e instanceof EresusExceptionInterface)) $e = new EresusExceptionDecorator($e);
	Core::handleException($e);
}
//-----------------------------------------------------------------------------

/**
 * Fatal error handler
 *
 * Perfomance note: this function disposes at begin and allocates at the end
 * memory buffer for memory overflow error handling. These operations slows down
 * output for 1-2%.
 */
function EresusFatalErrorHandler($output)
{
	# Free emergency buffer
	unset($GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER']);
	if (preg_match('/(parse|fatal) error:.*in .* on line/Ui', $output, $m)) {
		$GLOBALS['ERESUS_CORE_FATAL_ERROR_HANDLER'] = true;
		switch(strtolower($m[1])) {
			case 'fatal': $priority = LOG_CRIT; $message = 'Fatal error (see log for more info)'; break;
			case 'parse': $priority = LOG_EMERG; $message = 'Parse error (see log for more info)'; break;
	}
		elog(__FUNCTION__, $priority, trim($output));
		if (!PHP::isCLI()) header('Content-type: text/plain', true);
		return $message . "\n";
	}
	$GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] = str_repeat('x', ERESUS_MEMORY_OVERFLOW_BUFFER * 1024);
	/* Return 'false' to output buffer */
	return false;
}
//-----------------------------------------------------------------------------


/**
 * Class autoload table
 *
 * @package Core
 * @subpackage Kernel
 *
 * @author mekras
 */
class EresusClassAutoloadTable {

	/**
	 * Filename
	 * @var string
	 */
	protected $filename;

	/**
	 * Table
	 * @var array
	 */
	protected $table;

	/**
	 * Constructor
	 *
	 * @param string $filename
	 */
	public function __construct($filename)
	{
		elog(__METHOD__, LOG_DEBUG, $filename);
		if (substr($filename , -4) != '.php') {
			elog(__METHOD__, LOG_DEBUG, 'Adding ".php" extension');
			$filename .= '.php';
		}
		$this->filename = $filename;
		elog(__METHOD__, LOG_DEBUG, 'Table file: %s', $this->filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Try to load class
	 *
	 * @param string $className
	 * @return bool
	 */
	public function load($className)
	{
		if (!$this->filename) return false;

		if (!$this->table) $this->loadTable();
		if (!$this->table) return false;

		elog(__METHOD__, LOG_DEBUG, 'Searching for %s in %s', $className, $this->filename);

		if (isset($this->table[$className])) {

			$filename = $this->table[$className];
			if (substr($filename, -4) != '.php') $filename .= '.php';
			try {

				Core::safeInclude($filename);

			} catch (EresusRuntimeException $e) {

				throw new EresusRuntimeException('Can not load class "'.$className.'" from "'.$filename.'" (' . $e->getDescription() . ')', null, $e);

			}

		}

		elog(__METHOD__, LOG_DEBUG, '%s loading %s from table %s',class_exists($className, false) ? 'Success' : 'Failed', $className, $this->filename);

		return class_exists($className, false);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Load table from file
	 */
	protected function loadTable()
	{
		elog(__METHOD__, LOG_DEBUG, 'Loading autoload table from %s', $this->filename);

		try {

			$this->table = Core::safeInclude($this->filename, true);

		} catch (EresusRuntimeException $e) {

			elog(__METHOD__, LOG_ERR, 'Can\'t load table from "%s": %s', $this->filename, $e->getDescription());
			$this->filename = false;

		}

		elog(__METHOD__, LOG_DEBUG, $this->table ? 'success' : 'failed');
	}
	//-----------------------------------------------------------------------------

}

/**
 * Eresus class autoloader
 *
 * @package Core
 * @subpackage Kernel
 *
 * @author mekras
 */
class EresusClassAutoloader {

	/**
	 * Tables
	 * @var array
	 */
	private static $tables;

	/**
	 * Add
	 * @param $filename
	 * @return unknown_type
	 */
	static public function add($filename)
	{
		self::$tables []= new EresusClassAutoloadTable($filename);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Load class
	 * @param string $className
	 */
	static public function load($className)
	{
		foreach (self::$tables as $table) if ($table->load($className)) break;
	}
	//-----------------------------------------------------------------------------
}


/**
 * Main Eresus Core class
 *
 * @package Core
 * @subpackage Kernel
 */
class Core {

	/**
	 * Indicates initialization state:
	 *  0 - Not inited
	 *  1 - Init in progress
	 *  2 - Init complete
	 *
	 * @var int  Initialization state
	 */
	static private $initState = 0;

	/**
	 * Test mode switch
	 * @var bool
	 */
	static private $testMode = false;

	/**
	 * Test mode settings
	 * @var array
	 */
	static private $testModeOptions;

	/**
	 * Application
	 * @var EresusApplication
	 */
	static private $app = null;

	/**
	 * __autoload handlers pool
	 * @var array
	 */
	static private $autoloaders = array();

	/**
	 * Switch test mode
	 *
	 * @param bool $state
	 * @return bool  Current state
	 */
	static public function testMode($state = null)
	{
		if (!is_null($state)) self::$testMode = $state;
		return self::$testMode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set test mode option
	 *
	 * @param string $option
	 * @param mixed  $value
	 */
	static public function testModeSet($option, $value)
	{
		self::$testModeOptions[$option] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Check if test mode option is set
	 *
	 * @param string $option
	 * @return bool
	 */
	static public function testModeIsSet($option)
	{
		if (!self::testMode()) return null;
		return isset(self::$testModeOptions[$option]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get test mode option
	 *
	 * @param string $option
	 * @return mixed
	 */
	static public function testModeGet($option)
	{
		return self::testMode() ? ecArrayValue(self::$testModeOptions, $option) : null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Unset test mode option
	 *
	 * @param string $option
	 */
	static public function testModeUnset($option)
	{
		if (isset(self::$testModeOptions[$option]))
			unset(self::$testModeOptions[$option]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Init Eresus Core
	 */
	static public function init()
	{
		# Allow only one call of this method
		if (self::$initState) return;

		# Indicate that init in progress
		self::$initState = 1;

		System::init();
		FS::init();

		elog(__METHOD__, LOG_DEBUG, '()');

		self::initExceptionHandling();
		EresusClassAutoloader::add('core.autoload');
		self::registerAutoloader(array('EresusClassAutoloader', 'load'));

		/**
		 * eZ Components
		 */
		include_once '3rdparty/ezcomponents/Base/src/base.php';
		self::registerAutoloader(array('ezcBase', 'autoload'));

		/*
		 * If Eresus Core was built with a "compile" option
		 */
		if ('1') include_once 'eresus-core.compiled.php';

		elog(__METHOD__, LOG_DEBUG, 'done');
		# Indicate that init complete
		self::$initState = 2;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Class autoloading
	 *
	 * @param string $className
	 *
	 * @internal
	 * @ignore
	 */
	static public function autoload($className)
	{
		elog(__METHOD__, LOG_DEBUG, $className);

		if (!class_exists($className, false)) {
			for ($i = 0; $i < count(self::$autoloaders); $i++) {

				call_user_func(self::$autoloaders[$i], $className);
				if (class_exists($className, false)) break;

			}
		}

		elog(__METHOD__, LOG_DEBUG, class_exists($className, false) ? 'success' : 'failed');

	}
	//-----------------------------------------------------------------------------

	/**
	 * Init exception handling
	 *
	 */
	static private function initExceptionHandling()
	{
		if (self::testMode()) return;
		elog(__METHOD__, LOG_DEBUG, '()');

		# Reserve memory for emergency needs
		$GLOBALS['ERESUS_MEMORY_OVERFLOW_BUFFER'] = str_repeat('x', ERESUS_MEMORY_OVERFLOW_BUFFER * 1024);

		# Override php.ini settings
		ini_set('html_errors', false); # Some cosmetic setup

		set_error_handler('EresusErrorHandler');
		elog(__METHOD__, LOG_DEBUG, 'Error handler installed');

		set_exception_handler('EresusExceptionHandler');
		elog(__METHOD__, LOG_DEBUG, 'Exception handler installed');

		/*
		 * PHP has no standart methods to intersept some error types (e.g. E_PARSE or E_ERROR),
		 * but there is a way to do this - register callback function via ob_start.
		 * (Second arg "1" is a special value for 4096-byte output chunks)
		 */
		if (ob_start('EresusFatalErrorHandler', 1))
			elog(__METHOD__, LOG_DEBUG, 'Fatal error handler installed');
		else
			elog(LOG_NOTICE, __METHOD__, 'Fatal error handler not instaled! Fatal error will be not handled!');

		elog(__METHOD__, LOG_DEBUG, 'done');
	}
	//-----------------------------------------------------------------------------

	/**
	 * Default Eresus Core exception handler.
	 *
	 * If exception was not caught by application it will be handled with this method.
	 *
	 * @param Exception $e
	 */
	static public function handleException($e)
	{
		if ($e instanceof EresusExceptionInterface) {

			$class = $e->getClass();
			$message = $e->getMessage();
			$description = $e->getDescription();
			$trace = $e->getBacktraceAsString();

		} else {

			$class = get_class($e);
			$message = $e->getMessage() === '' ? $class : $e->getMessage();
			$description = 'Description not available';
			$trace = $e->getTraceAsString();

		}

		$logMessage = sprintf(
			"Unhandled %s in %s at %s\nMessage: %s\nDescription: %s\nBacktrace/context:\n%s\n",
			$class,
			$e->getFile(),
			$e->getLine(),
			$e->getMessage(),
			$description,
			$trace
		);
		elog(__METHOD__, LOG_ERR, $logMessage);

		$app = self::app();
		if ($app && method_exists($app, 'handleException')) {

			$app->handleException($e);

		} else {

			if (!PHP::isCLI()) header('Content-type: text/plain', true);
			echo $message;
			if (PHP::isCLI() && !self::testMode()) exit($e->getCode());

		}

	}
	//-----------------------------------------------------------------------------

	/**
	 * Make instance of application and execute it
	 * @param string $class  Application class name
	 * @return int  Exit code
	 */
	static public function exec($class)
	{
		if (!class_exists($class, false)) throw new EresusRuntimeException("Application class '$class' does not exists", 'Invalid application class');
		if (!is_subclass_of($class, 'EresusApplication')) throw new EresusRuntimeException("Application '$class' must be descendant of EresusApplication", 'Invalid application class');

		self::$app = new $class();

		try {

			elog(__METHOD__, LOG_DEBUG, 'executing %s', $class);
			$exitCode = self::$app->main();
			elog(__METHOD__, LOG_DEBUG, '%s done with code: %d', $class, $exitCode);

		} catch (Exception $e) {

			self::handleException($e);
			$exitCode = $e->getCode() ? $e->getCode() : 0xFFFF;

		}
		self::$app = null;
		return $exitCode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Get application object
	 * @return object(EresusApplication)
	 */
	static public function app()
	{
		return self::$app;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Register new autoload handler
	 *
	 * With PHP > 5.1.2 method uses spl_autoload_register function otherwise
	 * internal autoload stack will be used
	 *
	 * @param callback $autoloader
	 */
	static public function registerAutoloader($autoloader)
	{
		if (defined('ERESUS_LOG_LEVEL') && ERESUS_LOG_LEVEL == LOG_DEBUG) {

			switch(true) {
				case is_array($autoloader):
					$callback = is_object(reset($autoloader)) ? 'object('.get_class(current($autoloader)).')' : current($autoloader);
					$callback .= '::'.next($autoloader);
				break;
				default: $callback = $autoloader;
			}
			elog(__METHOD__, LOG_DEBUG, 'registering handler "%s"', $callback);
		}

		if (function_exists('spl_autoload_register')) {

			spl_autoload_register($autoloader);

		} else {

			array_unshift(self::$autoloaders, $autoloader);

		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Perform safe include of a PHP-file
	 *
	 * @param string $filename
	 * @param bool   $force     if "true" then "include" will be used instead of
	 *                          "include_once"
	 *
	 * @return mixed  Result of file inclusion
	 *
	 * @throws EresusRuntimeException
	 */
	static public function safeInclude($filename, $force = false)
	{
		$filename = FS::nativeForm($filename);
		$dirs = explode(PATH_SEPARATOR, get_include_path());

		foreach($dirs as $dir) if (FS::exists($dir . DIRECTORY_SEPARATOR . $filename)) {

			if ($force)
				return include $filename;
			else
				return include_once $filename;
		}

		throw new EresusRuntimeException("File '$filename' not found in '".get_include_path()."'", 'File not found');
	}
	//-----------------------------------------------------------------------------

}


/*****************************************************************************
 *
 *   Functions
 *
 *****************************************************************************/

/**
 * Get element with index $key from array $array
 *
 * If there is no element with such index function will return 'null'.
 *
 * @param array      $array
 * @param string|int $key
 * @return mixed
 */
function ecArrayValue($array, $key)
{
	return isset($array[$key]) ? $array[$key] : null;
}
//-----------------------------------------------------------------------------
