<?php
/**
 * Ответ HTTP
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
 * Ответ HTTP
 *
 * @package Eresus
 * @subpackage HTTP
 *
 * @since 0.1.3
 * @deprecated с 3.01 используйте {@link Eresus_HTTP_Redirect}
 */
class HttpResponse
{
    /**
     * HTTP headers
     *
     * @var HttpHeaders
     */
    private $headers;

    /**
     * Response body
     *
     * Response body must be a string or object with __toString method defined
     *
     * @var string|object
     */
    private $body;

    /**
     * Перенаправляет на новый адрес
     *
     * @param string $url      адрес для переадресации
     * @param array $params   Associative array of query parameters (not implemented yet!)
     * @param bool $session  Whether to append session information (not implemented yet!)
     * @param int $status   код HTTP
     *
     * @return bool  FALSE or exits on success with the specified redirection status code
     *
     * @author based on function by w999d
     */
    public static function redirect($url = null, $params = null, $session = false, $status = null)
    {
        /* No headers can be sent before redirect */
        if (headers_sent())
        {
            return false;
        }

        $url = HTTP::buildURL($url);
        $httpMessage = HttpMessage::fromEnv(HttpMessage::TYPE_REQUEST);

        /* Choose HTTP status code */
        switch (true)
        {
            case $status !== null:
                $code = $status;
                break;
            case $httpMessage->getHttpVersion() == '1.0':
                $code = 302;
                break;
            default:
                $code = 303;
                break;
        }

        /* Choose HTTP status message */
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

        /* Sending headers */
        header('HTTP/' . $httpMessage->getHttpVersion() . ' ' . $message, true, $code);
        header('Location: ' . $url);

        /* Sending HTML page for agents which does not support Location header */
        header('Content-type: text/html; charset=UTF-8');

        $hUrl = htmlspecialchars($url);
        echo <<<PAGE
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2 Final//EN">
<html>
	<head>
		<meta http-equiv="Refresh" content="0; url='{$hUrl}'">
		<meta http-equiv="Content-type" content="text/html;charset=UTF-8">
		<title>{$message}</title>
	</head>
	<body>
		<script>
			function doRedirect()
			{
				location.replace("{$hUrl}");
			}
			setTimeout("doRedirect()", 1000);
		</script>
		<p>Your browser does not support automatic redirection. Please follow <a href="{$hUrl}">this link</a>.</p>
	</body>
</html>
PAGE;

        // Stopping application
        exit($code);
    }

    /**
     * Constructor
     *
     * @param string|object $body
     */
    function __construct($body = null)
    {
        if (!is_null($body))
        {
            $this->body = $body;
        }
    }

    /**
     * Magic property getter
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        switch ($property)
        {
            case 'headers':
                if (!$this->headers)
                {
                    $this->headers = new HttpHeaders();
                }
                return $this->headers;
                break;
        }
        return null;
    }

    /**
     * Set response body
     * @param string|object $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * Send response
     */
    public function send()
    {
        if ($this->headers)
        {
            $this->headers->send();
        }
        echo $this->body;
    }
}

