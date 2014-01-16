<?php
/**
 * Сообщение HTTP
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
 * Сообщение HTTP
 *
 * @package Eresus
 * @subpackage HTTP
 */
class HttpMessage
{
    /**
     * Message has is of no specific type
     * @var int
     */
    const TYPE_NONE = 0;

    /**
     * Message is a request style HTTP message
     * @var int
     */
    const TYPE_REQUEST = 1;

    /**
     * Message is a response style HTTP message
     * @var int
     */
    const TYPE_RESPONSE = 2;

    /**
     * Message type
     * @var int
     */
    private $type;

    /**
     * Protocol version
     * @var string
     */
    private $httpVersion;

    /**
     * Request method
     * @var string
     */
    private $requestMethod;

    /**
     * Создаёт объект HttpMessage из окружения
     *
     * В режиме CLI результат всегда будет null.
     *
     * @param int    $messageType  тип сообщения, см. константы TYPE_xxx
     * @param string $className    класс, расширяющий HttpMessage
     *
     * @return HttpMessage|null  объект HttpMessage или null в случае ошибки
     *
     * @throws RuntimeException если класс $className не существует
     * @throws Eresus_Exception_InvalidArgumentType если $className не является потомком HttpMessage
     */
    public static function fromEnv($messageType, $className = 'HttpMessage')
    {
        if (Eresus_Kernel::isCLI())
        {
            return null;
        }

        /*
         * Create message instance
         */
        if (!class_exists($className, true))
        {
            throw new RuntimeException("Class \"$className\" not exists");
        }

        $message = new $className();

        if (! ($message instanceof HttpMessage))
        {
            throw Eresus_Exception_InvalidArgumentType::factory(__METHOD__, 2,
                'descendant of HttpMessage', $className);
        }

        // Message type
        $message->setType($messageType);

        /*
         * Protocol version
         */
        if (isset($_SERVER['SERVER_PROTOCOL']))
        {
            $dividerPosition = strpos($_SERVER['SERVER_PROTOCOL'], '/');

            if ($dividerPosition)
            {
                $httpVersion = substr($_SERVER['SERVER_PROTOCOL'], $dividerPosition + 1);
            }
            else
            {
                $httpVersion = '1.0';
            }
        }
        else
        {
            $httpVersion = '1.0';
        }

        $message->setHttpVersion($httpVersion);

        return $message;
    }

    /**
     * Set Message Type
     *
     * @param int $type  One of HttpMessage::TYPE_*
     * @return void
     */
    public function setType($type)
    {
        $this->type = $type;
    }
    //-----------------------------------------------------------------------------

    /**
     * Get Message Type
     *
     * @return int One of HttpMessage::TYPE_*
     */
    public function getType()
    {
        return $this->type;
    }
    //-----------------------------------------------------------------------------

    /**
     * Set the HTTP Protocol version of the Message
     *
     * @param string $version
     * @return bool  Returns TRUE on success, or FALSE if supplied version is out of range (1.0/1.1)
     */
    public function setHttpVersion($version)
    {
        // Version validation pattern
        $pattern = '~^1\.[01]$~';

        if (! preg_match($pattern, $version))
            return false;

        $this->httpVersion = $version;
        return true;
    }
    //-----------------------------------------------------------------------------

    /**
     * Get the HTTP Protocol Version of the Message
     *
     * @return string  Returns the HTTP protocol version as string
     */
    public function getHttpVersion()
    {
        return $this->httpVersion;
    }
    //-----------------------------------------------------------------------------

    /**
     * Set the Request Method of the HTTP Message
     *
     * @param string $method  The request method name.
     *                         {@link http://tools.ietf.org/html/rfc2068#section-5.1.1 See RFC2068 section 5.1.1}
     *                         for list of acceptable methods
     * @return bool  TRUE on success, or FALSE if the message is not of type
     *                HttpMessage::TYPE_REQUEST or an invalid request method was supplied
     */
    public function setRequestMethod($method)
    {
        if ($this->getType() !== self::TYPE_REQUEST)
            return false;

        $method = strtoupper($method);
        $REQUEST_METHODS = array('OPTIONS', 'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE');

        if (!in_array($method, $REQUEST_METHODS))
            return false;

        $this->requestMethod = $method;
        return true;
    }
    //-----------------------------------------------------------------------------

    /**
     * Get the Request Method of the Message
     *
     * @return string  Request method name on success, or FALSE if the message is not of type
     *                  HttpMessage::TYPE_REQUEST.
     */
    public function getRequestMethod()
    {
        if ($this->getType() !== self::TYPE_REQUEST)
            return false;

        return $this->requestMethod;
    }
    //-----------------------------------------------------------------------------
}

