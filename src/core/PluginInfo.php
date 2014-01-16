<?php
/**
 * ${product.title}
 *
 * Информация о плагине
 *
 * @version ${product.version}
 *
 * @copyright 2011, Eresus Project, http://eresus.ru/
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



/**
 * Информация о плагине
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_PluginInfo
{
	/**
	 * UID плагина
	 *
	 * @var string
	 */
	private $uid;

	/**
	 * Имя
	 *
	 * @var string
	 */
	private $name;

	/**
	 * Версия
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Название
	 *
	 * @var string
	 */
	private $title;

	/**
	 * Описание
	 *
	 * @var string
	 */
	private $description;

	/**
	 * Требуемая версия ядра
	 *
	 * @var array [min, max)]
	 */
	private $requiredKernel = array();

	/**
	 * Требуемые плагины
	 *
	 * @var array [[name, min, max]]
	 */
	private $requiredPlugins = array();

	/**
	 * Создаёт объект из файла
	 *
	 * @param string $filename
	 *
	 * @throws RuntimeException  если файл содержит ошибки
	 *
	 * @return Eresus_PluginInfo
	 *
	 * @since 2.16
	 */
	public static function loadFromFile($filename)
	{
		$xmlFile = substr($filename, 0, -4) . '/plugin.xml';
		if (file_exists($xmlFile))
		{
			return self::loadFromXmlFile($xmlFile);
		}
		else
		{
			return self::loadFromPhpFile($filename);
		}
	}

	/**
	 * Имитация свойств
	 *
	 * @param string $name
	 *
	 * @throws RuntimeException
	 *
	 * @return mixed
	 *
	 * @since 2.16
	 */
	public function __get($name)
	{
		$getter = 'get' . $name;
		if (!method_exists($this, $getter))
		{
			throw new RuntimeException('Access to unknown property ' . $name . ' in class ' .
				get_class($this));
		}
		return $this->$getter();
	}

	/**
	 * Возвращает UID плагина
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getUID()
	{
		return $this->uid ? $this->uid : $this->getName() . '@unknown.eresus.ru';
	}

	/**
	 * Возвращает имя
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * Возвращает название
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * Возвращает версию
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * Возвращает описание
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * Возвращает требуемую версию CMS
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function getRequiredKernel()
	{
		return $this->requiredKernel;
	}

	/**
	 * Возвращает список требуемых плагинов
	 *
	 * @return array
	 *
	 * @since 2.16
	 */
	public function getRequiredPlugins()
	{
		return $this->requiredPlugins;
	}

	/**
	 * Создаёт объект из файла XML
	 *
	 * @param string $filename
	 *
	 * @throws RuntimeException  если XML содержит ошибки
	 *
	 * @return Eresus_PluginInfo
	 *
	 * @since 2.16
	 */
	private static function loadFromXmlFile($filename)
	{
		/* Это временное решение до полного отказа от PHP-файла */
		$info = self::loadFromPhpFile(substr($filename, 0, -11) . '.php');

		libxml_use_internal_errors(true);
		libxml_clear_errors();
		$xml = new SimpleXMLElement($filename, 0, true);
		$errors = libxml_get_errors();
		if (count($errors))
		{
			libxml_clear_errors();
			$msg = array();
			foreach ($errors as $e)
			{
				$msg []= $e->message . '(' . $e->file . ':' . $e->line . ':' . $e->column . ')';
			}
			$msg = implode('; ', $msg);
			throw new RuntimeException($msg);
		}

		$info->uid = strval($xml['uid']);

		if (count($xml->requires) > 0 && count($xml->requires->children()) > 0)
		{
			foreach ($xml->requires as $requires)
			{
				foreach ($requires as $item)
				{
					switch ($item->getName())
					{
						case 'plugin':
							$info->requiredPlugins []= array(
								strval($item['name']),
								strval($item['min']),
								strval($item['max'])
							);
						break;
					}
				}
			}
		}

		return $info;
	}

	/**
	 * Создаёт объект из файла PHP
	 *
	 * @param string $filename
	 *
	 * @return Eresus_PluginInfo
	 *
	 * @since 2.16
	 */
	private static function loadFromPhpFile($filename)
	{
		$source = file_get_contents($filename);
		$tokens = token_get_all($source);
		$skipTokens = array(T_COMMENT, T_DOC_COMMENT);
		$props = array('$version', '$kernel', '$title', '$description');

		$info = new self;
		$state = null;

		foreach ($tokens as $token)
		{
			if (is_array($token))
			{
				list($id, $text) = $token;
				if (in_array($id, $skipTokens))
				{
					continue;
				}
				switch (true)
				{
					case T_CLASS == $id && null === $state:
						$state = 'class_name';
					break;

					case T_STRING == $id && 'class_name' == $state && trim($text) != '':
						$info->name = basename($filename, '.php');
						$state = 'prop_name';
					break;

					case T_VARIABLE == $id && 'prop_name' == $state && in_array($text, $props):
						$property = substr($text, 1);
						$state = 'prop_value';
					break;

					case T_CONSTANT_ENCAPSED_STRING == $id && 'prop_value' == $state:
						if (isset($property))
						{
							$value = substr($text, 1, -1);
							if ('kernel' == $property)
							{
								$value = explode('/', $value);
								$info->requiredKernel = array(
									$value[0],
									isset($value[1]) ? $value[1] : null
								);
							}
							else
							{
								$info->$property = $value;
							}
						}
						$state = 'prop_name';
					break;
				}
			}
		}
		return $info;
	}
}
