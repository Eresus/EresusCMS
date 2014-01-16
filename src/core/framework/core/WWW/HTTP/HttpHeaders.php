<?php
/**
 * Eresus Core
 *
 * @version 0.1.3
 *
 * HTTP Headers
 *
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 *
 * $Id: HttpHeaders.php 443 2009-12-23 06:28:45Z mk $
 */

/**
 * HTTP Headers
 *
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpHeaders
{

    /**
     * Headers
     * @var array
     */
    protected $headers = array();

    /**
     * Constructor
     */
    function __construct()
    {
    }

    //-----------------------------------------------------------------------------

    /**
     * Add header
     * @param HttpHeader $header
     */
    function add($header)
    {
        $this->headers [] = $header;
    }

    //-----------------------------------------------------------------------------

    /**
     * Get all headers
     * @return HttpHeader[]
     */
    public function getAll()
    {
        return $this->headers;
    }

    //-----------------------------------------------------------------------------

    /**
     * Send headers to UA
     */
    public function send()
    {
        $headers = $this->getAll();

        foreach ($headers as $header)
        {
            $header->send();
        }
    }
    //-----------------------------------------------------------------------------
}

//-----------------------------------------------------------------------------

/**
 * HTTP Header
 *
 * @package HTTP
 *
 * @author Mikhail Krasilnikov <mk@procreat.ru>
 */
class HttpHeader
{

    /**
     * Header name
     * @var string
     */
    protected $name;

    /**
     * Header value
     * @var string
     */
    protected $value;

    /**
     * Constructor
     * @param string $name
     * @param string $value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    //-----------------------------------------------------------------------------

    /**
     * Return header as string
     * @return string
     */
    public function __toString()
    {
        return $this->name . ': ' . $this->value;
    }

    //-----------------------------------------------------------------------------

    /**
     * Send header to UA
     */
    public function send()
    {
        if (!Eresus_Kernel::isCLI())
        {
            header($this);
        }
    }
}

