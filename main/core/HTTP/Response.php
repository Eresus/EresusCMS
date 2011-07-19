<?php
/**
 * ${product.title}
 *
 * Ответ HTTP
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
 * Ответ HTTP
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_HTTP_Response
{
	/**
	 * Коды ответа
	 *
	 * @var int
	 */
	const ST_CONTINUE = 100;
	const ST_SWITCHING_PROTOCOLS = 101;
	const ST_PROCESSING = 102;

	const ST_OK = 200;
	const ST_CREATED = 201;
	const ST_ACCEPTED = 202;

	const ST_MULTIPLE_CHOICES = 300;
	const ST_MOVED_PERMANENTLY = 301;
	const ST_FOUND = 302;
	const ST_SEE_OTHER = 303;
	const ST_NOT_MODIFIED = 304;

	const ST_BAD_REQUEST = 400;
	const ST_FORBIDDEN = 403;
	const ST_NOT_FOUND = 404;

	const ST_INTERNAL_SERVER_ERROR = 500;

	/**
	 * Текстовые сообщения о состоянии
	 *
	 * @var array
	 * @since 2.16
	 */
	private static $statusMessages = array(
		100 => 'Continue',
		101 => 'Switching Protocols',
		102 => 'Processing',

		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modified',

		400 => 'Bad Request',
		403 => 'Forbidden',
		404 => 'Not Found',
		413 => 'Request Entity Too Large',

		500 => 'Internal Server Error',
	);

	/**
	 * Код ответа HTTP
	 *
	 * @var int
	 * @since 2.16
	 */
	private $status;

	/**
	 * Заголовки HTTP
	 *
	 * @var array
	 */
	private $headers;

	/**
	 * Тело ответа
	 *
	 * Тело ответа должно быть строкой или объектом с методом {@link __toString()}.
	 *
	 * @var string|object
	 */
	private $body;

	/**
	 * Конструктор
	 *
	 * @param string|object $body
	 *
	 * @return Eresus_HTTP_Response
	 */
	public function __construct($body = null)
	{
		if (!is_null($body))
		{
			$this->body = $body;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Перенаправляет агент пользователя на заданный URI
	 *
	 * @param string $uri      адрес назначения
	 * @param array  $params   ассоциативный массив параметров (ещё не реализовано!)
	 * @param bool   $session  присоединять ли информацию о сессии (ещё не реализовано!)
	 * @param int    $status   код ответа
	 *
	 * @throws Eresus_ExitException  в случае успешной переадресации
	 *
	 * @return bool  false в случае ошибки
	 *
	 * @author основано на коде от w999d
	 * @uses Eresus_Kernel::isCLI()
	 * @uses Eresus_URI::buildURL()
	 * @uses Eresus_HTTP_Request::fromEnv()
	 * @uses Eresus_HTTP_Request::getHttpVersion()
	 * @uses Eresus_ExitException
	 */
	public static function redirect($uri = null, $params = null, $session = false, $status = null)
	{
		/* Перед редиректом не должно быть отправленных заголовков */
		if (headers_sent() && (!Eresus_Kernel::isCLI()))
		{
			return false;
		}

		$uri = Eresus_URI::buildURL($uri);
		$req = Eresus_HTTP_Request::fromEnv();

		/* Выбираем код ответа */
		switch (true)
		{
			case $status !== null:
				$code = $status;
			break;

			case $req->getHttpVersion() == '1.0':
				$code = 302;
			break;

			default:
				$code = 303;
			break;
		}

		/* Выбираем текст статусного сообщения */
		switch ($code)
		{
			case 302:
				$message = '302 Found';
			break;

			case 303:
				$message = '303 See Other';
			break;

			default:
				$message = '';
			break;
		}

		/* Отправляем заголовки */
		header('HTTP/' . $req->getHttpVersion() . ' ' . $message, true, $code);
		header('Location: ' . $uri);

		/* Отправляем HTML для клиентов, неподдерживающих Location */
		header('Content-type: text/html; charset=UTF-8');

		$hUri = htmlspecialchars($uri);
		echo <<<PAGE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
	<head>
		<meta http-equiv="Refresh" content="0; url='{$hUri}'">
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
		<title>{$message}</title>
	</head>
	<body>
		<script>
			function doRedirect()
			{
				location.replace("{$hUri}");
			}
			setTimeout("doRedirect()", 1000);
		</script>
		<p>
			Your browser does not support automatic redirection.
			Please follow <a href="{$hUri}">this link</a>.
		</p>
	</body>
</html>
PAGE;

		throw new Eresus_ExitException;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает тело ответа
	 *
	 * @param string|object $body
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	public function setBody($body)
	{
		$this->body = $body;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отправляет ответ
	 *
	 * @return void
	 */
	public function send()
	{
		/* Если статус не указан, позволим веб-серверу выбрать его самостоятельно */
		if ($this->status)
		{
			header(self::getStatusMessage($this->status), true, $this->status);
		}

		if ($this->headers)
		{
			foreach ($this->headers as $name => $value)
			{
				header($name . ': ' . $value);
			}
		}

		echo $this->body;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает код ответа
	 *
	 * @param int $status
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function setStatus($status)
	{
		$this->status = $status;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает текстовое представление числового кода ответа
	 *
	 * @param int $status
	 *
	 * @return string
	 *
	 * @since 2.16
	 */
	public static function getStatusMessage($status)
	{
		if (isset(self::$statusMessages[$status]))
		{
			return self::$statusMessages[$status];
		}
		else
		{
			return '';
		}
	}
	//-----------------------------------------------------------------------------
}

