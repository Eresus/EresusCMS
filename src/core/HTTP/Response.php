<?php
/**
 * Ответ по HTTP
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
 * Ответ по HTTP
 *
 * @package Eresus
 * @subpackage HTTP
 * @since 3.01
 */
class Eresus_HTTP_Response
{
    /**
     * Тело ответа
     * @var mixed
     * @since 3.01
     */
    private $content;

    /**
     * Код состояния
     * @var integer
     * @since 3.01
     */
    private $status;

    /**
     * Текстовое описание кода состояния
     * @var null|string
     * @since 3.01
     */
    protected $statusText = null;

    /**
     * Карта соответствия текстовых сообщений кодам состояния
     *
     * @var array
     * @since 3.01
     */
    private static $statusTextMap = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        105 => 'Name Not Resolved',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found, 302 Moved Temporarily',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URL Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        451 => 'Unavailable For Legal Reasons',
        456 => 'Unrecoverable Error',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    );

    /**
     * Заголовки
     * @var array
     * @since 3.01
     */
    private $headers;

    /**
     * Версия протокола
     * @var string
     * @since 3.01
     */
    private $protocolVersion = '1.1';

    /**
     * Создаёт новый ответ
     *
     * @param string $content  содержимое (тело) ответа
     * @param int    $status   статус HTTP
     * @param array  $headers  дополнительные заголовки
     *
     * @since 3.01
     */
    public function __construct($content = '', $status = 200, array $headers = array())
    {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->headers = $headers;
    }

    /**
     * Отправляет заголовки ответа
     *
     * @return $this
     * @since 3.01
     */
    public function sendHeaders()
    {
        /* Отправляем основной заголовок */
        $statusText = $this->statusText ?: self::getStatusText($this->getStatusCode());
        $header = 'HTTP/'
            . $this->getProtocolVersion() . ' '
            . $this->getStatusCode() . ' '
            . $statusText;
        header($header, true, $this->getStatusCode());

        /* Отправляем дополнительные заголовки */
        foreach ($this->headers as $header)
        {
            header($header);
        }
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
        echo strval($this->content);
        return $this;
    }

    /**
     * Отправляет заголовки и тело ответа
     *
     * @return $this
     * @since 3.01
     */
    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        return $this;
    }

    /**
     * Задаёт версию протокола
     *
     * @param string $version  версия (1.0, 1.1, 2.0…)
     *
     * @throws InvalidArgumentException
     *
     * @return $this
     *
     * @since 3.01
     */
    public function setProtocolVersion($version)
    {
        if (!preg_match('/^\d(\.\d+)+$/', $version))
        {
            throw new InvalidArgumentException('Invalid protocol version: ' . $version);
        }
        $this->protocolVersion = $version;
        return $this;
    }

    /**
     * Возвращает версию протокола
     *
     * @return string
     * @since 3.01
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
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
        $code = intval($code);
        if ($code < 100 || $code > 999)
        {
            throw new InvalidArgumentException('Status code must be from 100 to 999');
        }
        $this->status = $code;
        $this->statusText = $text;
        return $this;
    }

    /**
     * Возвращает код состояния
     *
     * @return int
     * @since 3.01
     */
    public function getStatusCode()
    {
        return $this->status;
    }

    /**
     * Задаёт тело ответа
     *
     * @param mixed $content
     *
     * @return $this
     *
     * @since 3.01
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Возвращает тело ответа
     *
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Возвращает сообщение для указанного кода состояния
     *
     * @param int $status
     *
     * @return string
     *
     * @since 3.01
     */
    public static function getStatusText($status)
    {
        if (array_key_exists($status, self::$statusTextMap))
        {
            return self::$statusTextMap[$status];
        }
        return '';
    }
}

