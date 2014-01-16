<?php
/**
 * Старое ядро
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
 * Название системы
 * @var string
 * @deprecated с 3.01 используйте {@link Eresus_CMS::$name}
 */
define('CMSNAME', 'Eresus');

/**
 * Версия системы
 *
 * @deprecated с 3.01 используйте {@link Eresus_CMS::$version}
 */
define('CMSVERSION', '${product.version}');

/**
 * Веб-сайт
 *
 * @deprecated с 3.01
 */
define('CMSLINK', 'http://eresus.ru/');

/**
 * Имя ядра
 *
 * @deprecated с 3.01
 */
define('KERNELNAME', 'ERESUS');

/**
 * Дата обновления ядра
 *
 * @deprecated с 3.01
 */
define('KERNELDATE', '${build.date}');

/**
 * Уровень доступа «Главный администратор»
 * @var int
 */
define('ROOT',   1);

/**
 * Уровень доступа «Администратор»
 * @var int
 */
define('ADMIN',  2);

/**
 * Уровень доступа «Редактор»
 * @var int
 */
define('EDITOR', 3);

/**
 * Уровень доступа «Пользователь»
 * @var int
 */
define('USER',   4);

/**
 * Уровень доступа «Гость (не зарегистрирован)»
 * @var int
 */
define('GUEST',  5);



/**
 * Возвращает константу для подстановки в макросы
 *
 * @param array $matches
 *
 * @return mixed
 *
 * @since 2.14
 * @deprecated с 3.01
 */
function __macroConst(array $matches)
{
    return constant($matches[1]);
}

/**
 * Возвращает глобальную переменную для подстановки в макросы
 *
 * @param array $matches
 *
 * @return mixed
 *
 * @since 2.14
 * @deprecated с 3.01
 */
function __macroVar(array $matches)
{
    $result = $GLOBALS[$matches[2]];
    if (!empty($matches[3]))
    {
        @eval('$result = $result'.$matches[3].';');
    }
    return $result;
}

/**
 * Функция выводит сообщение о пользовательской ошибке и прекращает работу скрипта.
 *
 * @param string $msg  Текст сообщения
 *
 * @since 2.00
 * @deprecated с 3.01 используйте исключения
 */
function FatalError($msg)
{
    if (PHP_SAPI == 'cli')
    {
        $result = strip_tags(preg_replace('!<br(\s/)?>!i', "\n", $msg))."\n";
    }
    else
    {
        $result =
            "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n".
            "<html>\n".
            "<head>\n".
            "  <title>".errError."</title>\n".
            "  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=".CHARSET."\">\n".
            "</head>\n\n".
            "<body>\n".
            "  <div align=\"center\" style=\"font-family: Arial, Helvetica, sans-serif;\">\n".
            "    <table cellspacing=\"0\" style=\"border-style: solid; " .
            "border-color: #e88 #800 #800 #e88; min-width: 500px;\">\n".
            "      <tr><td style=\"border-style: solid; border-width: 2px; " .
            "border-color: #800 #e88 #e88 #800; background-color: black; color: yellow; " .
            "font-weight: bold; text-align: center; font-size: 10pt;\">".errError."</td></tr>\n".
            "      <tr><td style=\"border-style: solid; border-width: 2px; " .
            "border-color: #800 #e88 #e88 #800; background-color: #c00; padding: 10; color: white; " .
            "font-weight: bold; font-family: verdana, tahoma, Geneva, sans-serif; font-size: 8pt;\">\n".
            "        <p style=\"text-align: center\">".$msg."</p>\n".
            "        <div align=\"center\"><br /><a href=\"javascript:history.back()\" " .
            "style=\"font-weight: bold; color: black; text-decoration: none; font-size: 10pt; " .
            "height: 20px; background-color: #aaa; border-style: solid; border-width: 1px; " .
            "border-color: #ccc #000 #000 #ccc; padding: 0 2em;\">".strReturn."</a></div>\n".
            "      </td></tr>\n".
            "    </table>\n".
            "  </div>\n".
            "</body>\n".
            "</html>";
    }
    die($result);
}

/**
 * Возвращает разметку сообщения о пользовательской ошибке
 *
 * @param string $text     Текст сообщения
 * @param string $caption  Заголовок окна сообщения
 *
 * @return string  HTML
 *
 * @see InfoBox()
 * @see ErrorMessage()
 * @since 2.00
 * @deprecated с 3.01
 */
function ErrorBox($text, $caption = errError)
{
	$result =
		(empty($caption)?'':"<div class=\"errorBoxCap\">".$caption."</div>\n").
		"<div class=\"errorBox\">\n".
		$text.
		"</div>\n";
	return $result;
}

/**
 * Возвращает разметку информационного сообщения
 *
 * @param string $text
 * @param string $caption
 *
 * @return string  HTML
 *
 * @see ErrorBox()
 * @see InfoMessage()
 * @since 2.00
 * @deprecated с 3.01
 */
function InfoBox($text, $caption=strInformation)
{
	$result =
		(empty($caption)?'':"<div class=\"infoBoxCap\">".$caption."</div>\n").
		"<div class=\"infoBox\">\n".
		$text.
		"</div>\n";
	return $result;
}

/**
 * Добавляет на страницу сообщение об ошибке
 *
 * @param string $message
 *
 * @deprecated с 3.01 используйте Eresus_Kernel::app()->getPage()->addErrorMessage()
 */
function ErrorMessage($message)
{
    Eresus_Kernel::app()->getPage()->addErrorMessage($message);
}

/**
 * Добавляет на страницу информационное сообщение
 *
 * @param string $message
 *
 * @deprecated с 3.01
 */
function InfoMessage($message)
{
	Eresus_CMS::getLegacyKernel()->session['msg']['information'][] = $message;
}

/**
 * Функция проверяет права пользователя на соответствие заданной маске
 *
 * @param int $level
 *
 * @return bool
 */
function UserRights($level)
{
	$user = Eresus_CMS::getLegacyKernel()->user;
	return (
		(
			($user['auth']) &&
			($user['access'] <= $level) &&
			($user['access'] != 0)
		) ||
		($level == GUEST)
	);
}

/**
 * Подключает описание класса
 *
 * @param  string  $className   Имя класса
 *
 * @return  bool  Результат выполнения
 *
 * @deprecated
 */
function useClass($className)
{
	$result = false;
	if (DIRECTORY_SEPARATOR != '/')
	{
		$className = str_replace('/', DIRECTORY_SEPARATOR, $className);
	}
	$filename = realpath(dirname(__FILE__)) . '/classes/' . $className.'.php';
	if (is_file($filename))
	{
		include_once($filename);
		$result = true;
	}
	return $result;
}


/**
 * Функция отсылает письмо по указанному адресу
 *
 * @param string $address
 * @param string $subject
 * @param string $text
 * @param bool   $html
 * @param string $fromName
 * @param string $fromAddr
 * @param string $fromOrg
 * @param string $fromSign
 * @param string $replyTo
 *
 * @return bool
 */
function sendMail($address, $subject, $text, $html=false, $fromName='', $fromAddr='', $fromOrg='',
	$fromSign='', $replyTo='')
{
	if (empty($fromName))
	{
		$fromName = option('mailFromName');
	}
	if (empty($fromAddr))
	{
		$fromAddr = option('mailFromAddr');
	}
	if (empty($fromOrg))
	{
		$fromOrg = option('mailFromOrg');
	}
	if (empty($fromSign))
	{
		$fromSign = option('mailFromSign');
	}
	if (empty($replyTo))
	{
		$replyTo = option('mailReplyTo');
	}
	if (empty($replyTo))
	{
		$replyTo = $fromAddr;
	}

	$charset = CHARSET;

	$sender = strlen($fromName) ? "=?".$charset."?B?".base64_encode($fromName)."?= <$fromAddr>" :
		$fromAddr;
	if (strlen($fromOrg))
	{
		$sender .= ' (=?'.$charset.'?B?'.base64_encode($fromOrg).'?=)';
	}
	if (mb_strpos($sender, '@') === false)
	{
		$sender = 'no-reply@'.preg_replace('/^www\./', '', httpHost);
	}
	$fromSign = "\n-- \n".$fromSign;
	if ($html)
	{
		$fromSign = nl2br($fromSign);
	}
	if (strlen($fromSign))
	{
		$text .= $fromSign;
	}

	$headers =
		"MIME-Version: 1.0\n".
		"From: $sender\n".
		"Subject: $subject\n".
		"Reply-To: $replyTo\n".
		"X-Mailer: PHP/" . phpversion()."\n";

	if ($html)
	{
		$text = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n\n" .
			"<html>\n<head></head>\n<body>\n".$text."\n</body>\n</html>";

		$boundary="=_".md5(uniqid(time()));
		$headers.="Content-Type: multipart/mixed; boundary=$boundary\n";
		$multipart="";
		$multipart.="This is a MIME encoded message.\n\n";

		$multipart.="--$boundary\n";
		$multipart.="Content-Type: text/html; charset=$charset\n";
		$multipart.="Content-Transfer-Encoding: Base64\n\n";
		$multipart.=chunk_split(base64_encode($text))."\n\n";
		$multipart.="--$boundary--\n";
		$text = $multipart;
	}
	else
	{
		$headers .= "Content-type: text/plain; charset=$charset\n";
	}

	$conf = Eresus_CMS::getLegacyKernel()->conf;
	if ($conf['debug']['enable'] && $conf['debug']['mail'] !== true)
	{
		if (is_string($conf['debug']['mail']))
		{
			$hnd = @fopen($conf['debug']['mail'], 'a');
			if ($hnd)
			{
				fputs($hnd, "\n====================\n$headers\nTo: $address\nSubject: $subject\n\n$text\n");
				fclose($hnd);
			}
			return true;
		}
	}
	else
	{
		return (mail($address, $subject, $text, $headers)===0);
	}
	return false;
}

/**
 * Возвращает время с учетом смещения
 *
 * @param string $format
 *
 * @return string
 */
function gettime($format = 'Y-m-d H:i:s')
{
	#$delta = (GMT_ZONE * 3600) - date('Z'); // Смещение на нужный часовой пояс
	$delta = 0;
	return date($format , time() + $delta); // Время, со смещением на наш часовой пояс
}

/**
 * Заменяет спецсимволы HTML на сущности (entities)
 *
 * Поддержка массивов добавлена в 2.11.
 *
 * @param mixed $source  строка или массив строк, в которых надо произвести замену
 *
 * @return mixed  строка или массив строк, в зависимости от типа source
 */
function encodeHTML($source)
{
	$translationTable = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
	switch (true)
	{
		case is_string($source):
			$source = strtr($source, $translationTable);
		break;

		case is_array($source):
			foreach ($source as $key => $value)
			{
				$source[$key] = strtr($value, $translationTable);
			}
		break;
	}
	return $source;
}

/**
 * Декодирует спецсимволы HTML
 *
 * @param string $text
 *
 * @return string
 */
function decodeHTML($text)
{
	$trans_tbl = get_html_translation_table(HTML_SPECIALCHARS, ENT_QUOTES);
	$trans_tbl = array_flip ($trans_tbl);
	$trans_tbl['%28'] = '(';
	$trans_tbl['%29'] = ')';
	$text = strtr ($text, $trans_tbl);
	$text = preg_replace('/ilo-[^\s>]*/i', '', $text);
	return $text;
}

/**
 * Разбивает текст на строки и возвращает массив из них
 *
 * @param string $value
 * @param bool   $assoc
 * @return array
 */
function text2array($value, $assoc = false)
{
	$result = trim($value);
	if (!empty($result))
	{
		$result = str_replace("\r",'',$result);
		$result = explode("\n", $result);
		if ($assoc && count($result))
		{
			$items = array();
			foreach ($result as $item)
			{
				$item = explode('=', $item);
				$key = trim($item[0]);
				if ($key !== '')
				{
					$value = isset($item[1]) ? trim($item[1]) : null;
					$items[$key] = $value;
				}
			}
			$result = $items;
		}
	}
	else
	{
		$result = array();
	}
	return $result;
}

/**
 * Собирает текст из массива
 *
 * @param array $items
 * @param bool  $assoc
 *
 * @return string
 */
function array2text($items, $assoc = false)
{
	$result = '';
	if (count($items))
	{
		if ($assoc)
		{
			foreach ($items as $key => $value)
			{
				$result[] = $key.'='.$value;
			}
		}
		else
		{
			$result = $items;
		}
		$result = implode("\n", $result);
	}
	return $result;
}

/**
 * Собирает настройки из массива в строку
 *
 * @param array $options
 *
 * @return string
 */
function encodeOptions($options)
{
	$result = serialize($options);
	return $result;
}

/**
 * Функция разбивает записанные в строковом виде опции на массив
 *
 * @param string $options
 * @param array  $defaults
 *
 * @return array
 */
function decodeOptions($options, $defaults = array())
{
	if (empty($options))
	{
		$result = $defaults;
	}
	else
	{
		@$result = unserialize($options);
		if (gettype($result) != 'array')
		{
			$result = $defaults;
		}
		else
		{
			if (count($defaults))
			{
				foreach ($defaults as $key => $value)
				{
					if (!array_key_exists($key, $result))
					{
						$result[$key] = $value;
					}
				}
			}
		}
	}
	return $result;
}

/**
 * Заменяет макросы на их значения
 *
 * Функция находит в шаблоне все подстроки вида $(имя_макроса), затем ищет соответствующие ключи
 * или свойства в $source и заменяет подстроки на значения из $source.
 *
 * Если $source не содержит ключа или свойства с требуемым именем, замена не производится.
 *
 * Можно использовать «условные макросы», действующие по принципу тернарного (с 3 операндами)
 * условного оператора языка PHP и записывающиеся в виде:
 *
 * $(имя_макроса ? строка1 : строка 2)
 *
 * Если значение с именем «имя_макроса» в $source не ложно по правилам PHP, то макрос заменяется
 * значением строка 1, в противном случае значением строка 2.
 *
 * @param string        $template  шаблон
 * @param array|object  $source    источник для замены (ассоциативный массив или объект)
 *
 * @return string  шаблон template в котором макросы заменены на значения из аргумента source
 */
function replaceMacros($template, $source)
{
	# Замена условных макросов
	preg_match_all('/\$\(([^\)\?:]+)\?([^:\)]*):([^\)]*)\)/U', $template, $matches, PREG_SET_ORDER);
	if (count($matches))
	{
		foreach ($matches as $macros)
		{
			if (__isset($source, $macros[1]))
			{
				$template = str_replace($macros[0], __property($source, $macros[1])?$macros[2]:$macros[3],
					$template);
			}
		}
	}

	// Замена обычных макросов
	preg_match_all('/\$\(([^(]+)\)/U', $template, $matches);
	if (count($matches[1]))
	{
		foreach ($matches[1] as $macros)
		{
			if (__isset($source, $macros))
			{
				$template = str_replace('$('.$macros.')', __property($source, $macros), $template);
			}
		}
	}
	return $template;
}

/**
 * Возвращает значение аргумента запроса HTTP
 *
 * Проверяет, был ли передан GET или POST аргумент $arg (в {@link Eresus::$request}), и, если он был
 * передан, возвращает его значение. В противном случае возвращается null (до 2.10 функция
 * возвращала false).
 *
 * Необязательный аргумент $filter (добавлен в 2.10) позволяет отфильтровать значение аргумента.
 * В качестве фильтра может быть использовано регулярное выражение (PCRE) или одно из ключевых слов:
 *
 * - int, integer – целое число (используется {@link intval() intval()})
 * -float – вещественное число (используется {@link floatval() floatval()})
 * - word – только буквы и цифры
 * - dbsafe – строка, безопасная для использования в запросах к БД
 *
 * @param string $arg     Имя аргумента
 * @param mixed  $filter  Фильтр, применяемый к значению аргумента
 *
 * @return mixed  значение аргумента или null если такой аргумент не был передан в запросе
 *
 * @since 2.01
 */
function arg($arg, $filter = null)
{
	$request = Eresus_CMS::getLegacyKernel()->request;
	$arg = isset($request['arg'][$arg]) ?
		$request['arg'][$arg] :
		null;

	if ($arg !== false && !is_null($filter))
	{
		switch ($filter)
		{
			case 'dbsafe':
				$arg = Eresus_CMS::getLegacyKernel()->db->escape($arg);
			break;

			case 'int':
			case 'integer':
					$arg = intval($arg);
			break;

			case 'float':
					$arg = floatval($arg);
			break;

			case 'word':
					$arg = preg_replace('/\W/', '', $arg);
			break;

			default:
				$arg = preg_replace($filter, '', $arg);
			break;
		}
	}
	return $arg;
}

/**
 * Функция сохраняет в сессии текущие аргументы
 */
function saveRequest()
{
	Eresus_CMS::getLegacyKernel()->session['request'] = Eresus_CMS::getLegacyKernel()->request;
}

/**
 * Функция сохраняет в сессии текущие аргументы
 */
function restoreRequest()
{
	if (isset(Eresus_CMS::getLegacyKernel()->session['request']))
	{
		Eresus_CMS::getLegacyKernel()->request = Eresus_CMS::getLegacyKernel()->session['request'];
		unset(Eresus_CMS::getLegacyKernel()->session['request']);
	}
}

/**
 * Упорядочивание элементов
 *
 * @param string $table      Таблица
 * @param string $condition  Условие
 * @param string $id         Имя ключевого поля
 *
 * @deprecated
 */
function dbReorderItems($table, $condition='', $id='id')
{
	$items = Eresus_CMS::getLegacyKernel()->db->
		select("`".$table."`", $condition, '`position`', $id);
	for ($i=0; $i<count($items); $i++)
	{
		Eresus_CMS::getLegacyKernel()->db->
			update($table, "`position` = $i", "`".$id."`='".$items[$i][$id]."'");
	}
}

/**
 * Удаляет файл
 *
 * @param string $filename Имя файла
 *
 * @return bool Результат выполнения
 *
 * @since 2.10
 */
function filedelete($filename)
{
	$result = false;
	if (is_file($filename))
	{
		if (is_writeable($filename))
		{
			$result = unlink($filename);
		}
	}
	return $result;
}

/**
 * Перемещает файл, загруженный по HTTP
 *
 * Перемещает загруженный по HTTP файл в указанную директорию, под указанным или автоматически
 * определяемым именем (см. $filename). Для перемещения используется функция
 * {@link move_uploaded_file() move_uploaded_file()}, таким образом на функцию upload
 * распространяются те же исключения open_basedir.
 *
 * После успешного перемещения:
 *
 * 1. Если установлена опция опция filesOwnerSetOnUpload, то функция пытается установить владельца
 *    для этого файла, заданного опцией filesOwnerDefault. Только в версиях до 2.14!
 * 2. Если установлена опция опция filesModeSetOnUpload, то функция пытается установить права для
 *    этого файла, заданного опцией filesModeDefault.
 *
 * Если в $filename указана только директория (значение $filename заканчивается слэшем «/»), то
 * файл будет помещён в указанную директорию, а имя его будет выбрано автоматически, исходя из
 * следующих правил:
 *
 * 1. Использовать имя из $_FILES[$name]['name'].
 * 2. Если включена опция filesTranslitNames, имя будет транслитерировано при помощи функции
 *    {@link translit()}.
 * 3. Если файл с таким именем уже существует и аргумент $overwrite равен false, то перед последней
 *    точкой в имени файла будет подставляться последовательно увеличиваемое число до тех пор, пока
 *    не будет получено уникальное имя файла.
 *
 * Аргумент $overwrite имеет смысл только если $filename содержит имя директории и не содержит имени
 * файла.
 *
 * @param string $name       имя элемента массива $_FILES, содержащего информацию о нужном файле
 * @param string $filename   новое имя файла
 * @param bool   $overwrite  перезаписывать или нет существующий файл
 *
 * @return bool|string
 *
 * @since 2.00
 */
function upload($name, $filename, $overwrite = true)
{
	$result = false;
	if (substr($filename, -1) == '/')
	{
		$filename .= option('filesTranslitNames') ?
			Translit($_FILES[$name]['name']) :
			$_FILES[$name]['name'];
		if (file_exists($filename) &&
			((is_string($overwrite) && $filename != $overwrite ) || (is_bool($overwrite) && !$overwrite))
		)
		{
			$i = strrpos($filename, '.');
			$fname = substr($filename, 0, $i);
			$fext = substr($filename, $i);
			$i = 1;
			while (is_file($fname.$i.$fext))
			{
				$i++;
			}
			$filename = $fname.$i.$fext;
		}
	}
	switch ($_FILES[$name]['error'])
	{
		case UPLOAD_ERR_OK:
			if (is_uploaded_file($_FILES[$name]['tmp_name']))
			{
				$moved = @move_uploaded_file($_FILES[$name]['tmp_name'], $filename);
				if ($moved)
				{
					if (option('filesModeSetOnUpload'))
					{
						$mode = option('filesModeDefault');
						$mode = empty($mode) ? 0666 : octdec($mode);
						@chmod($filename, $mode);
					}
					$result = $filename;
				}
				else
				{
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(errFileMove, $_FILES[$name]['name'], $filename));
				}
			}
		break;
		case UPLOAD_ERR_INI_SIZE:
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errUploadSizeINI, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_FORM_SIZE:
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errUploadSizeFORM, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_PARTIAL:
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                sprintf(errUploadPartial, $_FILES[$name]['name']));
		break;
		case UPLOAD_ERR_NO_FILE:
			if (strlen($_FILES[$name]['name']))
			{
                Eresus_Kernel::app()->getPage()->addErrorMessage(
                    sprintf(errUploadNoFile, $_FILES[$name]['name']));
			}
		break;
	}
	return $result;
}

function HttpAnswer($answer)
{
	Header('Content-type: text/html; charset='.CHARSET);
	echo $answer;
	exit;
}

/**
 * Отправляет браузеру XML
 *
 * @param string $data
 */
function SendXML($data)
{
	Header('Content-Type: text/xml');
	echo '<?xml version="1.0" encoding="'.CHARSET.'"?>'."\n<root>".$data."</root>";
	exit;
}

/**
 * Возвращает значение глобального параметра
 *
 * Предоставляет доступ к параметрам, устанавливаемым в разделе конфигурации сайта:
 *
 * - siteName (string) — Название сайта
 * - siteTitle (string) — Глобальный заголовок сайта
 * - siteTitleReverse (bool) — Выводить ли составляющие заголовка в обратном порядке (от частного к
 *   общему)
 * - siteTitleDivider (string) — Разделитель составляющих заголовка
 * - siteKeywords (string) — Глобальные ключевые слова сайта
 * - siteDescription (string) — Глобальное описание сайта
 * - mailFromAddr (string) — E-mail по умолчанию для поля From отправляемых писем
 * - mailFromName (string) — Имя по умолчанию для поля From отправляемых писем
 * - mailFromOrg (string) — Название организации по умолчанию для поля From отправляемых писем
 * - mailReplyTo (string) — Значение по умолчанию для поля Reply-to отправляемых писем
 * - mailCharset (string) — Кодировка отправляемых писем
 * - mailFromSign (string) — Подпись для отправляемых писем
 * - sendNotifyTo (string) — E-mail-адреса для административных извещений
 * - filesOwnerSetOnUpload (bool) — Менять ли владельца загружаемых файлов?
 * - filesOwnerDefault (string) — Владелец загружаемых файлов
 * - filesModeSetOnUpload (bool) — Менять ли права на загружаемые файлы?
 * - filesModeDefault (string) — Права на загружаемые файлы
 * - filesTranslitNames (bool) — Транслитерировать ли имена загружаемых файлов
 * - contentTypeDefault (string) — Тип контента по умолчанию
 * - pageTemplateDefault (string) — Шаблон страницы по умолчанию
 * - clientPagesAtOnce (int) — Количество отображаемых страниц в переключателях страниц
 *
 * @param $name  имя параметра
 *
 * @return mixed
 */
function option($name)
{
	$result = defined($name) ? constant($name) : '';
	return $result;
}

/**
 * Функция возвращает заполненный тэг <img>
 *
 * function img($imagename, $alt='', $title='', $width=0, $height=0, $style='')
 * function img($imagename, $params=array())
 *
 * @param string $imageName
 *
 * @return string
 */
function img($imageName)
{
	$argc = func_num_args();
	$argv = func_get_args();
	if ($argc > 1)
	{
		if (is_array($argv[1]))
		{
			$p = $argv[1];
		}
		else
		{
			$p['alt'] = $argv[1];
			if ($argc > 2)
			{
				$p['title'] = $argv[2];
			}
			if ($argc > 3)
			{
				$p['width'] = $argv[3];
			}
			if ($argc > 4)
			{
				$p['height'] = $argv[4];
			}
			if ($argc > 5)
			{
				$p['style'] = $argv[5];
			}
		}
	}
	if (!isset($p['alt']))
	{
		$p['alt'] = '';
	}
	if (!isset($p['title']))
	{
		$p['title'] = '';
	}
	if (!isset($p['width']))
	{
		$p['width'] = '';
	}
	if (!isset($p['height']))
	{
		$p['height'] = '';
	}
	if (!isset($p['style']))
	{
		$p['style'] = '';
	}
	if (!isset($p['ext']))
	{
		$p['ext'] = '';
	}
	if (!isset($p['autosize']))
	{
		$p['autosize'] = true;
	}

    $legacyKernel = Eresus_Kernel::app()->getLegacyKernel();
	if (strpos($imageName, $legacyKernel->root) !== false)
	{
		$imageName = str_replace($legacyKernel->root, '', $imageName);
	}
	if (strpos($imageName, $legacyKernel->froot) !== false)
	{
		$imageName = str_replace($legacyKernel->froot, '', $imageName);
	}
	if (strpos($imageName, '://') === false)
	{
		$imageName = $legacyKernel->root . $imageName;
	}
	$local = (strpos($imageName, $legacyKernel->root) === 0);

	if ($p['autosize'] && $local && empty($p['width']) && empty($p['height']))
	{
		$filename = str_replace($legacyKernel->root, $legacyKernel->froot, $imageName);
		if (is_file($filename))
		{
			$info = getimagesize($filename);
		}
	}
	if (isset($info))
	{
		$p['width'] = $info[0];
		$p['height'] = $info[1];
	};

	$result = '<img src="'.$imageName.'" alt="'.$p['alt'].'"'.
		(empty($p['width'])?'':' width="'.$p['width'].'"').
		(empty($p['height'])?'':' height="'.$p['height'].'"').
		(empty($p['title'])?'':' title="'.$p['title'].'"').
		(empty($p['style'])?'':' style="'.$p['style'].'"').
		(empty($p['ext'])?'':' '.$p['ext']).
	' />';
	return $result;
}

function FormatSize($size)
{
	if ($size > 1073741824)
	{
		$size = $size / 1073741824;
		$units = 'Гб';
		$z = 2;
	}
	elseif ($size > 1048576)
	{
		$size = $size / 1048576;
		$units = 'Мб';
		$z = 2;
	}
	elseif ($size > 1024)
	{
		$size = $size / 1024;
		$units = 'Кб';
		$z = 2;
	}
	else
	{
		$units = 'Байт';
		$z = 0;
	}
	return number_format($size, $z, '.', ' ').' '.$units;
}

function Translit($s) #: String
{
	$s = strtr($s, $GLOBALS['translit_table']);
	$s = str_replace(
		array(' ','/','?'),
		array('_','-','7'),
		$s
	);
	$s = preg_replace('/(\s|_)+/', '$1', $s);
	return $s;
}

function __clearargs($args)
{
	if (count($args))
	{
		foreach ($args as $key => $value)
		{
			if (gettype($args[$key]) == 'array')
			{
				$args[$key] = __clearargs($args[$key]);
			}
			else
			{
				if (strpos($key, 'wyswyg_') === 0)
				{
					unset($args[$key]);
					$key = substr($key, 7);
					$value = preg_replace('/(<[^>]+) ilo-[^\s>]*/i', '$1', $value);
					$value = str_replace(array('%28', '%29'), array('(',')'), $value);
					$value = str_replace(Eresus_CMS::getLegacyKernel()->root, '$(httpRoot)', $value);
					preg_match_all('/<img.*?>/', $value, $images, PREG_OFFSET_CAPTURE);
					if (count($images[0]))
					{
						$images = $images[0];
						$delta = 0;
						for ($i = 0; $i < count($images); $i++)
						{
							if (!preg_match('/alt=/i', $images[$i][0]))
							{
								$s = preg_replace('/(\/?>)/', 'alt="" $1', $images[$i][0]);
								$value = substr_replace($value, $s, $images[$i][1]+$delta,
									mb_strlen($images[$i][0]));
								$delta += mb_strlen($s) - mb_strlen($images[$i][0]);
							}
						}
					}
				}
				$args[$key] = $value;
			}
		}
	}
	return $args;
}

/**
 * Определяет установлено ли свойство у элемента
 *
 * @param mixed  $object    Элемент
 * @param string $property  Свойство
 * @return bool Значение
 *
 * @see replaceMacros
 */
function __isset($object, $property)
{
	return
		is_object($object) ? isset($object->$property) : (
			is_array ($object) ? isset($object[$property]) :
			false
		);
}

/**
 * Возвращает свойство элемента
 *
 * @param mixed  $object    Элемент
 * @param string $property  Свойство
 * @return string Значение
 *
 * @see replaceMacros
 */
function __property($object, $property)
{
	return
		is_object($object) ? $object->$property : (
			is_array ($object) ? $object[$property] :
			''
		);
}

/**
 * @deprecated с 3.01 используйте {@link Eresus_Kernel::log()}
 */
function eresus_log()
{
    call_user_func_array(array('Eresus_Kernel', 'log'), func_get_args());
}

/**
 * Создание изображения из файла
 *
 * @param string $filename Имя файла
 * @return mixed Дескриптор или false
 */
function imageCreateFromFile($filename)
{
    $type = getimagesize($filename);
    switch ($type[2])
    {
        case IMAGETYPE_GIF:
            $result = imageCreateFromGIF($filename);
            break;
        case IMAGETYPE_JPEG:
        case IMAGETYPE_JPEG2000:
            $result = imageCreateFromJPEG($filename);
            break;
        case IMAGETYPE_PNG:
            $result = imageCreateFromPNG($filename);
            break;
        default:
            switch (substr($type['mime'], 6))
            {
                case 'gif':
                    $result = imageCreateFromGIF($filename);
                    break;
                case 'jpeg':
                    $result = imageCreateFromJPEG($filename);
                    break;
                case 'png':
                    $result = imageCreateFromPNG($filename);
                    break;
                default:
                    $result = false;
            }
    }
    return $result;
}

/**
 * Сохранение изображения в файл заданного формата
 *
 * @param resource $image     Изображение
 * @param string $filename  Имя файла
 * @param int $format         Формат файла
 * @return mixed Дескриптор или false
 */
function imageSaveToFile($image, $filename, $format)
{
    $result = false;
    switch ($format)
    {
        case IMAGETYPE_GIF:
            $result = imageGIF($image, $filename);
            break;
        case IMAGETYPE_JPEG:
        case IMAGETYPE_JPEG2000:
            $quality = func_num_args() > 3 ? func_get_arg(3) : 80;
            $result = imageJPEG($image, $filename, $quality);
            break;
        case IMAGETYPE_PNG:
            $quality = func_num_args() > 3 ? func_get_arg(3) : 7;
            $filters = func_num_args() > 4 ? func_get_arg(4) : 0;
            $result = imagePNG($image, $filename, $quality, $filters);
            break;
    }
    return $result;
}


/**
 * Создание миниатюры
 *
 * @param string $srcFile  Исходный файл
 * @param string $dstFile  Файл миниатюры
 * @param int    $width    Ширина миниатюры
 * @param int    $height   Высота миниатюры
 * @param mixed  $fill     Заполнение фона
 * @param int    $quality  качество
 *
 * @return bool Результат
 */
function thumbnail($srcFile, $dstFile, $width, $height, $fill = null, $quality = 80)
{
    $result = false;
    $src = imageCreateFromFile($srcFile);
    if ($src)
    {
        $sW = imageSX($src);
        $sH = imageSY($src);
        $resizer = ($sW / $width > $sH / $height) ? ($sW / $width) : ($sH / $height);
        $dW = floor($sW / $resizer);
        $dH = floor($sH / $resizer);
        if (is_null($fill))
        {
            $dst = imageCreateTrueColor($dW, $dH);
            imageCopyResampled($dst, $src, 0, 0, 0, 0, $dW, $dH, $sW, $sH);
        }
        else
        {
            $dst = imageCreateTrueColor($width, $height);
            if ($fill[0] == '#')
            {
                $R = hexdec(substr($fill, 1, 2));
                $G = hexdec(substr($fill, 3, 2));
                $B = hexdec(substr($fill, 5, 2));
            }
            else
            {
                $fill = explode(',', $fill);
                $R = trim($fill[0]);
                $G = trim($fill[1]);
                $B = trim($fill[2]);
            }
            imagefill($dst, 0, 0, imagecolorallocate($dst, $R, $G, $B));
            imageCopyResampled($dst, $src, round(($width - $dW) / 2), round(($height - $dH) / 2), 0, 0, $dW, $dH, $sW, $sH);
        }
        $result = ImageJPEG($dst, $dstFile, $quality);
        ImageDestroy($src);
        ImageDestroy($dst);
    }
    return $result;
}

/**
 * Изменение формата изображения
 *
 * @param string $srcFile  Имя исходного файла
 * @param string $dstFile  Имя файла назначения
 * @param int    $format   Формат файла назначения
 *
 * @return bool  Результат выполнения
 */
function imageConvert($srcFile, $dstFile, $format = IMG_JPG)
{
    $src = imageCreateFromFile($srcFile);
    if ($src)
    {
        $result = imageSaveToFile($src, $dstFile, $format);
    }
    else
    {
        $result = false;
    }
    return $result;
}


/**
 * Основной класс приложения
 *
 * @since 2.10
 * @package Eresus
 */
class Eresus
{
	/**
	 * Конфигурация
	 *
	 * @var array
	 */
	public $conf = array(
		'lang' => 'ru',
		'timezone' => '',
		'db' => array(
			'engine'   => 'mysql',
			'host'     => 'localhost',
			'user'     => '',
			'password' => '',
			'name'     => '',
			'prefix'   => '',
		),
		'session' => array(
			'timeout' => 30,
		),
		'extensions' => array(),
		'backward' => array(
			'TPlugin' => false,
			'TContentPlugin' => false,
			'TListContentPlugin' => false,
		),
		'debug' => array(
			'enable' => false,
			'mail' => true,
		),
	);

	/**
	 * Данные сессии
	 *
	 * Это свойство представляет собой ассоциативный массив, хранящий данные текущей сессии:
	 *
	 * - time (int) — Время последней активности пользователя в этой сессии
	 * - request (array) — Используется функциями {@link saveRequest()} и {@link restoreRequest()}
	 * - msg (array) — Очередь сообщений, используемая функциями {@link ErrorMessage()} и
	 * {@link InfoMessage()}
	 *
	 * @var array
	 * @since 2.10
	 */
	public $session;

	/**
	 * Интерфейс к расширениям системы
	 *
	 * @var EresusExtensions
	 */
	public $extensions;

	/**
	 * Интерфейс к БД
	 * @var MySQL
	 * @since 2.10
	 * @deprecated с 3.00, используйте DB
	 */
	public $db;

	/**
	 * Плагины
	 * @var Eresus_Plugin_Registry
     * @deprecated с 3.01 используйте {@link Eresus_Plugin_Registry::getInstance()}
	 */
	public $plugins;

	/**
	 * Учётная запись пользователя
	 *
	 * @var array
	 * @since 2.10
	 */
	public $user;

	/**
	 * Относительный URL сайта относительно корня сервера
	 *
	 * Если сайт, расположен в корне (т. е. по адресу http://example.org/), то path будет равен «/».
	 *
	 * Если сайт, расположен в поддиректории, например по адресу http://example.org/site/, то path
	 * будет равен «/site/».
	 *
	 * @var string
	 * @since 2.10
	 * @deprecated с 3.00, используйте Eresus::$request
	 */
	public $path;

	/**
	 * Полный корневой URL сайта
	 *
	 * @var string
	 * @since 2.10
	 */
	public $root;

	/**
	 * Полный URL к директории данных
	 *
	 * @var string
	 * @since 2.10
	 */
	public $data;

	/**
	 * Полный URL к директории стилей
	 *
	 * @var string
	 * @since 2.10
	 */
	public $style;

	/**
	 * Корневая директория
	 * @var string
	 * @since 2.10
	 */
	public $froot;

	/**
	 * Директория данных
	 * @var string
	 * @since 2.10
	 */
	public $fdata;

	/**
	 * Директория стилей
	 * @var string
	 * @since 2.10
	 */
	public $fstyle;

	/**
	 * Запрос HTTP
	 *
	 * Это свойство представляет собой ассоциативный массив, хранящий параметры текущего запроса
     * HTTP:
	 *
	 * - <b>method</b> (string) — HTTP-метод — GET или POST
	 * - <b>scheme</b> (string) — HTTP-схема: HTTP или HTTPS
	 * - <b>url</b> (string) — Полный URL запроса, за исключением идентификатора сессии (если он
     *   присутствует в URL)
	 * - <b>link</b> (string) — Заготовка для создания URL с параметрами GET. Всегда заканчивается
     *   либо символом «?», либо «&»
	 * - <b>referer</b> (string) — URL, откуда был совершён переход
	 * - <b>arg</b> (array) — Ассоциативный массив аргументов запроса (GET + POST)
	 * - <b>path</b> (string) — Строка, содержащая путь к текущей виртуальной директории. Например,
     *   для запроса 'http://example.org/virt/dir/exec.php?id=1' path будет
     *   'http://example.org/virt/dir/'
	 * - <b>file</b> (string) — Имя запрошенного файла. Например «exec.php» для вышеприведённого
     *   адреса
	 * - <b>params</b> (array) — Массив, представляющий собой набор всех виртуальных директорий URL,
     *   после корневого URL сайта
	 *
	 * @var array
	 * @since 2.10
	 */
	public $request;

	/**
	 * Интерфейс работы с разделами сайта
	 *
	 * @var Sections
	 * @since 2.10
	 */
	public $sections;

	/**
	 * Инициирует сессии
	 */
	private function init_session()
	{
		session_set_cookie_params(ini_get('session.cookie_lifetime'), $this->path);
		session_name('sid');
		session_start();
		$this->session = &$_SESSION['session'];
		if (!isset($this->session['msg']))
		{
			$this->session['msg'] = array('error' => array(), 'information' => array());
		}
		$this->user = &$_SESSION['user'];

		# Обратная совместимость
		$GLOBALS['session'] = &$_SESSION['session'];
		$GLOBALS['user'] = &$_SESSION['user'];
	}

	/**
	 * Определяет файловые пути
	 *
	 * @return void
	 */
	protected function init_resolve()
	{
		if (is_null($this->froot))
		{
			$this->froot = Eresus_Kernel::app()->getFsRoot() . '/';
		}

		$this->fdata = $this->froot . 'data' . DIRECTORY_SEPARATOR;
		$this->fstyle = $this->froot . 'style' . DIRECTORY_SEPARATOR;

		if (is_null($this->path))
		{
			$s = substr(dirname($_SERVER['SCRIPT_FILENAME']), strlen($_SERVER['DOCUMENT_ROOT']));
			if (strlen($s) == 0 || substr($s, -1) != '/')
			{
				$s .= '/';
			}
			if (substr($s, 0, 1) != '/')
			{
				$s = '/' . $s;
			}
			$this->path = $s;
		}
	}

	/**
	 * Читает настройки
     *
     * @throws Eresus_Exception_ComponentCorrupted
	 */
	private function initSettings()
	{
		$filename = $this->froot . 'cfg/settings.php';
		if (is_file($filename))
		{
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
		}
		else
		{
			throw new Eresus_Exception_ComponentCorrupted(
                sprintf('Settings file "%s" not found!', $filename));
		}
	}

	/**
	 * Первичный разбор запроса
	 */
	private function init_request()
	{
		global $request;

		# Значения по умолчанию
		$request = array(
			'method' => $_SERVER['REQUEST_METHOD'],
			'scheme' => isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) ? 'https' : 'http',
			'host' => strtolower($_SERVER['HTTP_HOST']),
			'port' => '',
			'user' => isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : '',
			'pass' => isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '',
			'path' => '',
			'query' => '',
			'fragment' => '',
			'referer' => isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '',
		);

		$request['url'] = $request['scheme'] . '://' . $request['host'] . $_SERVER['REQUEST_URI'];

		$request = array_merge($request, parse_url($request['url']));
		$request['file'] = substr($request['path'], strrpos($request['path'], '/')+1);
		if ($request['file'])
		{
			$request['path'] = substr($request['path'], 0, -strlen($request['file']));
		}

		# Создаем заготовку URL для GET-запросов с параметрами
		$request['link'] = $request['url'];
		if (substr($request['link'], -1) == '/')
		{
			$request['link'] .= '?';
		}
		elseif (strpos($request['link'], '?') === false)
		{
			$request['link'] .= '?';
		}
		else
		{
			$request['link'] .= '&';
		}

		if (is_null($this->path))
		{
			$s = $this->froot;
			$s = substr($s,
				strlen(realpath($_SERVER['DOCUMENT_ROOT'])) - (Eresus_Kernel::isWindows() ? 2 : 0));
			if (!strlen($s) || substr($s, -1) != '/')
			{
				$s .= '/';
			}
			$this->path = (substr($s, 0, 1) != '/' ? '/' : '').$s;
		}

		/*
		 * Установка свойств объекта $Eresus
		 * Должна выполняться ДО вызова __clearargs
		 */
		$root = $request['scheme'] . '://' . $request['host'] .
			($request['port'] ? ':'.$request['port'] : '');
		$this->root = $root.$this->path;
		$this->data = $this->root.'data/';
		$this->style = $this->root.'style/';


		# Сбор аргументов вызова
		$request['arg'] = __clearargs(array_merge($_GET, $_POST));
		# Разбивка параметров вызова скрипта
		$s = substr($request['path'], strlen($this->path));
		$request['params'] = $s ? explode('/', substr($s, 0, -1)) : array();

		$request['path'] = $root.$request['path'];

		# Обратная совместимость
		# <= 2.9
		$this->request = &$request;
        /**
         * @deprecated с 3.01
         */
		define('httpPath', $this->path);
        /**
         * @deprecated с 3.01
         */
		define('httpHost', $this->request['host']);
        /**
         * @deprecated с 3.01
         */
        define('httpRoot', $this->root);
        /**
         * @deprecated с 3.01
         */
		define('styleRoot', $this->style);
        /**
         * @deprecated с 3.01
         */
		define('dataRoot', $this->data);
        /**
         * @deprecated с 3.01
         */
		define('cookieHost', $this->request['host']);
        /**
         * @deprecated с 3.01
         */
		define('cookiePath', $this->path);
	}

	/**
	 * Инициализация локали
     *
     * @throws Eresus_Exception_ComponentCorrupted
	 */
	private function initLocale()
	{
		global $locale;

		$locale['lang'] = $this->conf['lang'];
		$locale['prefix'] = '';

		# Подключение строковых данных
		$filename = $this->froot.'lang/'.$locale['lang'].'.php';
		if (is_file($filename))
		{
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
		}
		else
		{
            throw new Eresus_Exception_ComponentCorrupted(
                sprintf('Locale file "%s" not found!', $filename));
		}
	}

	/**
	 * Подключение базовых классов
     *
     * @throws Eresus_Exception_ComponentCorrupted
	 */
	private function initClasses()
	{
		$filename = $this->froot.'core/classes.php';
		if (is_file($filename))
		{
            /** @noinspection PhpIncludeInspection */
            include_once $filename;
		}
		else
		{
            throw new Eresus_Exception_ComponentCorrupted(
                sprintf('Classes file "%s" not found!', $filename));
		}
	}

	/**
	 * Инициализация расширений
	 */
	function init_extensions()
	{
		$filename = $this->froot.'cfg/extensions.php';
		if (is_file($filename))
		{
			include_once($filename);
		}

		$this->extensions = new EresusExtensions();
	}

	/**
	 * Подключение к источнику данных
     *
     * @throws Eresus_Exception_ComponentCorrupted
	 */
	private function initDataSource()
	{
        $className = $this->conf['db']['engine'];
		if (class_exists($className))
		{
			$this->db = new $className;
			$this->db->init($this->conf['db']['host'], $this->conf['db']['user'],
				$this->conf['db']['password'], $this->conf['db']['name'],
                $this->conf['db']['prefix']);
		}
		else
		{
            throw new Eresus_Exception_ComponentCorrupted(
                sprintf('Library "%s" not found!', $this->conf['db']['engine']));
		}
	}

	/**
	 * Проверка сессии
	 */
	private function check_session()
	{
		if (isset($this->session['time']))
		{
			if (
				(time() - $this->session['time'] > $this->conf['session']['timeout']*3600) &&
				($this->user['auth'])
			)
			{
				$this->logout(false);
			}
			else
			{
				$this->session['time'] = time();
			}
		}
	}

	/**
	 * Проверка на логин/логаут
	 *
	 */
	function check_loginout()
	{
		switch (arg('action'))
		{
			case 'login':
				$this->login(arg('user'), $this->password_hash(arg('password')), arg('autologin', 'int'));
				HTTP::redirect($this->request['url']);
			break;
			case 'logout':
				$this->logout(true);
				HTTP::redirect($this->root.'admin/');
			break;
		}
	}

	/**
	 * Попытка cookie-логина
	 */
	function check_cookies()
	{
		if (!$this->user['auth'] && isset($_COOKIE['eresus_login']))
		{
			if (!$this->login($_COOKIE['eresus_login'], $_COOKIE['eresus_key'], true, true))
			{
				$this->clear_login_cookies();
			}
		}
	}

	/**
	 * Обновление данных о пользователе
	 */
	function reset_login()
	{
		$this->user['auth'] = isset($this->user['auth']) ? $this->user['auth'] : false;
		if ($this->user['auth'])
		{
			$item = $this->db->selectItem('users', "`id`='".$this->user['id']."'");
			if (!is_null($item))
			{
				# Если такой пользователь есть...
				if ($item['active'])
				{
					# Если учетная запись активна...
					$this->user['name'] = $item['name'];
					$this->user['mail'] = $item['mail'];
					$this->user['access'] = $item['access'];
					$this->user['profile'] = decodeOptions($item['profile']);
				}
				else
				{
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(ERR_ACCOUNT_NOT_ACTIVE, $item['login']));
					$this->logout();
				}
			}
			else
			{
				$this->logout();
			}
		}
		else
		{
			$this->user['access'] = GUEST;
		}
	}

	/**
	 * Инициализация системы
	 */
	public function init()
	{
		if ($this->conf['timezone'])
		{
			date_default_timezone_set($this->conf['timezone']);
		}
		# Определение путей
		$this->init_resolve();
		# Инициализация сессии
		$this->init_session();
		# Изменяем путь поиска подключаемых файлов
		set_include_path(dirname(__FILE__) . '/lib' . PATH_SEPARATOR . get_include_path());
		# Читаем настройки
		$this->initSettings();
		# Первичный разбор запроса
		$this->init_request();
		# Настройка локали
		$this->initLocale();
		# Подключение базовых классов
		$this->initClasses();
		# Инициализация расширений
		$this->init_extensions();
		# Подключение к источнику данных
		$this->initDataSource();
        $this->plugins = Eresus_Plugin_Registry::getInstance();
        $this->plugins->init();
		# Проверка сессии
		$this->check_session();
		# Проверка логина/логаута
		$this->check_loginout();
		# Попытка cookie-логина
		$this->check_cookies();
		# Обновление данных о пользователе
		$this->reset_login();
		$this->sections = new Sections;
		$GLOBALS['KERNEL']['loaded'] = true; # Флаг загрузки ядра
	}

	/**
	 * Хеширует пароль
	 *
	 * @param string $password  Пароль
	 * @return string  Хеш
	 */
	function password_hash($password)
	{
		$result = md5($password);
		if (!$this->conf['backward']['weak_password'])
		{
			$result = md5($result);
		}
		return $result;
	}

	/**
	 * Устанавливает авторизационные кукисы
	 *
	 * @param string $login
	 * @param string $key
	 */
	function set_login_cookies($login, $key)
	{
		setcookie('eresus_login', $login, time()+2592000, $this->path);
		setcookie('eresus_key', $key, time()+2592000, $this->path);
	}

	/**
	 * Удалаяет авторизационные кукисы
	 *
	 */
	function clear_login_cookies()
	{
		setcookie('eresus_login', '', time()-3600, $this->path);
		setcookie('eresus_key', '', time()-3600, $this->path);
	}

	/**
	 * Авторизация пользователя
	 *
	 * @param string $unsafeLogin  Имя пользователя
	 * @param string $key		       Ключ учётной записи
	 * @param bool   $auto		     Сохранить авторизационные данные на компьютере посетителя
	 * @param bool   $cookie       Авторизация при помощи cookie
	 * @return bool Результат
	 */
	public function login($unsafeLogin, $key, $auto = false, $cookie = false)
	{
		$result = false;

		$login = preg_replace('/[^a-z0-9_\-\.\@]/', '', $unsafeLogin);

		if ($login != $unsafeLogin)
		{
            Eresus_Kernel::app()->getPage()->addErrorMessage(ERR_PASSWORD_INVALID);
			return false;
		}

		$item = $this->db->selectItem('users', "`login`='$login'");
		// Если такой пользователь есть...
		if (!is_null($item))
		{
			// Если учетная запись активна...
			if ($item['active'])
			{
				$noBruteForcing = time() - $item['lastLoginTime'] > $item['loginErrors'];
				if ($noBruteForcing || $this->conf['debug']['enable'])
				{
					// Если пароль верен...
					if ($key == $item['hash'])
					{
						if ($auto)
						{
							$this->set_login_cookies($login, $key);
						}
						else
						{
							$this->clear_login_cookies();
						}
						$setVisitTime = (! isset($this->user['id'])) || (! (bool) $this->user['id']);
						$this->user = $item;
						$this->user['profile'] = decodeOptions($this->user['profile']);
						$this->user['auth'] = true; # Устанавливаем флаг авторизации
						// Хэш пароля используется для подтверждения аутентификации
						$this->user['hash'] = $item['hash'];
						if ($setVisitTime)
						{
							$item['lastVisit'] = gettime(); # Записываем время последнего входа
						}
						$item['lastLoginTime'] = time();
						$item['loginErrors'] = 0;
						$this->db->updateItem('users', $item,"`id`='".$item['id']."'");
						$this->session['time'] = time(); # Инициализируем время последней активности сессии.
						$result = true;
					}
					else
					{
						// Если пароль не верен...
						if (!$cookie)
						{
                            Eresus_Kernel::app()->getPage()->addErrorMessage(ERR_PASSWORD_INVALID);
							$item['lastLoginTime'] = time();
							$item['loginErrors']++;
							$this->db->updateItem('users', $item,"`id`='".$item['id']."'");
						}
					}
				}
				else
				{
					// Если авторизация проведена слишком рано
                    Eresus_Kernel::app()->getPage()->addErrorMessage(
                        sprintf(ERR_LOGIN_FAILED_TOO_EARLY, $item['loginErrors']));
					$item['lastLoginTime'] = time();
					$this->db->updateItem('users', $item,"`id`='".$item['id']."'");
				}
			}
			else
			{
                Eresus_Kernel::app()->getPage()->addErrorMessage(
                    sprintf(ERR_ACCOUNT_NOT_ACTIVE, $login));
			}
		}
		else
		{
            Eresus_Kernel::app()->getPage()->addErrorMessage(
                ERR_PASSWORD_INVALID);
		}
		return $result;
	}

	/**
	 * Завершение сеанса работы с системой
	 *
	 * @param bool $clearCookies
	 */
	public function logout($clearCookies=true)
	{
		$this->user['id'] = null;
		$this->user['auth'] = false;
		$this->user['access'] = GUEST;
		if ($clearCookies)
		{
			$this->clear_login_cookies();
		}
	}
}
