<?php
/**
 * Ответ-перенаправление
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
 * @subpackage HTTP
 */

/**
 * Ответ-перенаправление
 *
 * @package Eresus
 * @subpackage HTTP
 * @since 3.01
 */
class Eresus_HTTP_Redirect extends Eresus_HTTP_Response
{
    /**
     * Создаёт новый ответ-перенаправление
     *
     * @param string $url      адрес для перехода
     * @param int    $status   статус HTTP
     * @param array  $headers  дополнительные заголовки
     *
     * @throws InvalidArgumentException
     *
     * @since 3.01
     */
    public function __construct($url = '', $status = 303, array $headers = array())
    {
        if ($status < 300 || $status > 399)
        {
            throw new InvalidArgumentException('Status code must be one of 3xx');
        }
        parent::__construct($url, $status, $headers);
    }

    /**
     * Задаёт код состояния
     *
     * @param int   $code  код состояния
     * @param mixed $text  опциональное текстовое описание кода
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     * @since 3.01
     */
    public function setStatusCode($code, $text = null)
    {
        if ($this->getProtocolVersion() == '1.0'
            && !in_array($this->getStatusCode(), array(300, 301, 302, 304)))
        {
            $code = 302;
        }
        return parent::setStatusCode($code, $text);
    }

    /**
     * Отправляет заголовки
     *
     * @return $this
     *
     * @since 3.01
     */
    public function sendHeaders()
    {
        parent::sendHeaders();
        $url = strval($this->getContent());
        header("Location: $url");
        return $this;
    }

    /**
     * Отправляет тело ответа
     *
     * @return $this
     * @since 3.01
     */
    public function sendContent()
    {
        $url = htmlspecialchars(strval($this->getContent()));
        $message = $this->statusText ?: self::getStatusText($this->getStatusCode());
        echo <<<PAGE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
	<head>
		<meta http-equiv="Refresh" content="0; url='{$url}'">
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
		<title>{$message}</title>
	</head>
	<body>
		<script>
			function doRedirect()
			{
				location.replace("{$url}");
			}
			setTimeout("doRedirect()", 1000);
		</script>
		<p>Your browser does not support automatic redirection. Please follow <a href="{$url}">this link</a>.</p>
	</body>
</html>
PAGE;
        return $this;
    }
}

