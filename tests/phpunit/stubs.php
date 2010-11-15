<?php
/**
 * ${product.title}
 *
 * @version ${product.version}
 *
 * PhpUnit Tests
 *
 * @copyright 2010, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
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
 * @package EresusCMS
 * @subpackage Tests
 * @author Mikhail Krasilnikov <mk@eresus.ru>
 *
 * $Id$
 */

if (class_exists('PHP_CodeCoverage_Filter', false))
{
	PHP_CodeCoverage_Filter::getInstance()->addFileToBlacklist(__FILE__);
}
else
{
	PHPUnit_Util_Filter::addFileToFilter(__FILE__);
}

define('errInvalidPassword', 'errInvalidPassword');
define('errAccountNotActive', 'errAccountNotActive');
define('errTooEarlyRelogin', 'errTooEarlyRelogin');

function eresus_log() {}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class FS
{
	public static function canonicalForm($filename)
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


/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class EresusRuntimeException extends Exception
{
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class EresusApplication
{
	public $fsRoot;

	public function getFsRoot()
	{
		return $this->fsRoot;
	}
	//-----------------------------------------------------------------------------
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class HttpRequest
{
	public $localRoot;

	public function setLocalRoot($value)
	{
		$this->localRoot = $value;
	}
	//-----------------------------------------------------------------------------

	public function getLocalRoot()
	{
		return $this->localRoot;
	}
	//-----------------------------------------------------------------------------

	public function getScheme()
	{
		return 'http';
	}
	//-----------------------------------------------------------------------------

	public function getHost()
	{
		return 'example.org';
	}
	//-----------------------------------------------------------------------------
}

/**
 * @package EresusCMS
 * @subpackage Tests
 * @since 2.15
 */
class TemplateSettings
{
	public static function setGlobalValue($a, $b)
	{
		;
	}
	//-----------------------------------------------------------------------------
}
