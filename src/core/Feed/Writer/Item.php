<?php
/**
 * ${product.title}
 *
 * Элемент ленты RSS или Atom
 *
 * @version ${product.version}
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
 * @copyright 2007, Eresus Project, http://eresus.ru/
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
 * Элемент лент RSS и Atom
 *
 * @author Anis uddin Ahmad <anisniit@gmail.com>, http://www.ajaxray.com/projects/rss
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_Feed_Writer_Item
{
    private $elements = array(); //Collection of feed elements
    private $version;

    /**
     * Constructor
     *
     * @param string $version  (RSS1/RSS2/ATOM) RSS2 is default.
     */
    public function __construct($version = Eresus_Feed_Writer::RSS2)
    {
        $this->version = $version;
    }

    /**
     * Add an element to elements array
     *
     * @param string $elementName  The tag name of an element
     * @param string $content      The content of tag
     * @param array $attributes   Attributes(if any) in 'attrName' => 'attrValue' format
     * @return void
     */
    public function addElement($elementName, $content, $attributes = null)
    {
        $this->elements[$elementName]['name'] = $elementName;
        $this->elements[$elementName]['content'] = $content;
        $this->elements[$elementName]['attributes'] = $attributes;
    }

    /**
     * Set multiple feed elements from an array.
     * Elements which have attributes cannot be added by this method
     *
     * @param    array   array of elements in 'tagName' => 'tagContent' format.
     * @return   void
     */
    public function addElementArray($elementArray)
    {
        if (!is_array($elementArray))
        {
            return;
        }
        foreach ($elementArray as $elementName => $content)
        {
            $this->addElement($elementName, $content);
        }
    }

    /**
     * Return the collection of elements in this feed item
     *
     * @return   array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Set the 'description' element of feed item
     *
     * @param string $description  The content of 'description' element
     * @return   void
     */
    public function setDescription($description)
    {
        $tag = ($this->version == Eresus_Feed_Writer::ATOM) ? 'summary' : 'description';
        $this->addElement($tag, $description);
    }


    /**
     * @desc     Set the 'title' element of feed item
     * @param string $title  The content of 'title' element
     * @return  void
     */
    public function setTitle($title)
    {
        $this->addElement('title', $title);
    }

    /**
     * Set the 'date' element of feed item
     *
     * @param string $date  The content of 'date' element
     * @return void
     */
    public function setDate($date)
    {
        if (!is_numeric($date))
        {
            $date = strtotime($date);
        }

        if ($this->version == Eresus_Feed_Writer::ATOM)
        {
            $tag = 'updated';
            $value = date(DATE_ATOM, $date);
        }
        elseif ($this->version == Eresus_Feed_Writer::RSS2)
        {
            $tag = 'pubDate';
            $value = date(DATE_RSS, $date);
        }
        else
        {
            $tag = 'dc:date';
            $value = date("Y-m-d", $date);
        }

        $this->addElement($tag, $value);
    }

    /**
     * Set the 'link' element of feed item
     *
     * @param string $link  The content of 'link' element
     * @return void
     */
    public function setLink($link)
    {
        if ($this->version == Eresus_Feed_Writer::RSS2 || $this->version == Eresus_Feed_Writer::RSS1)
        {
            $this->addElement('link', $link);
        }
        else
        {
            $this->addElement('link', '', array('href' => $link));
            $this->addElement('id', Eresus_Feed_Writer::uuid($link, 'urn:uuid:'));
        }

    }

    /**
     * Set the 'encloser' element of feed item
     * For RSS 2.0 only
     *
     * @param string $url     The url attribute of encloser tag
     * @param string $length  The length attribute of encloser tag
     * @param string $type    The type attribute of encloser tag
     * @return void
     */
    public function setEncloser($url, $length, $type)
    {
        $attributes = array('url' => $url, 'length' => $length, 'type' => $type);
        $this->addElement('enclosure', '', $attributes);
    }
}

