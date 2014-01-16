<?php
/**
 * ${product.title}
 *
 * Генератор лент RSS и Atom
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
 * Генератор лент RSS и Atom
 *
 * Создаёт ленты RSS 1.0, RSS2.0 и ATOM
 *
 * @author Anis uddin Ahmad <anisniit@gmail.com>, http://www.ajaxray.com/projects/rss
 * @author Михаил Красильников <mihalych@vsepofigu.ru>
 *
 * @package Eresus
 * @since 2.16
 */
class Eresus_Feed_Writer
{
	// RSS 0.90  Officially obsoleted by 1.0
	const RSS1 = 'RSS 1.0';

	// RSS 0.91, 0.92, 0.93 and 0.94  Officially obsoleted by 2.0
	const RSS2 = 'RSS 2.0';

	// So, define constants for RSS 1.0, RSS 2.0 and ATOM
	const ATOM = 'ATOM';

	/**
	 * Collection of channel elements
	 *
	 * @var array
	 */
	private $channels = array();

	/**
	 * Collection of items as object of FeedItem class.
	 *
	 * @var array
	 */
	private $items = array();

	/**
	 * Store some other version wise data
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * The tag names which have to encoded as CDATA
	 * @var array
	 */
	private $CDATAEncoding = array('description', 'content:encoded', 'summary');

	private $version   = null;

	/**
	 * Constructor
	 *
	 * @param string $version  the version constant (RSS1/RSS2/ATOM).
	 */
	function __construct($version = self::RSS2)
	{
		$this->version = $version;
	}

	/**
	 * Set a channel element
	 *
	 * @param string $elementName  name of the channel tag
	 * @param string $content  content of the channel tag
	 *
	 * @return void
	 */
	public function setChannelElement($elementName, $content)
	{
		$this->channels[$elementName] = $content ;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Set multiple channel elements from an array. Array elements
	 * should be 'channelName' => 'channelContent' format.
	 *
	 * @param    array   array of channels
	 * @return   void
	 */
	public function setChannelElementsFromArray($elementArray)
	{
		if (! is_array($elementArray))
		{
			return;
		}
		foreach ($elementArray as $elementName => $content)
		{
			$this->setChannelElement($elementName, $content);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку RSS/ATOM
	 *
	 * @return string  RSS/ATOM
	 */
	public function generateFeed()
	{
		// TODO Вынести возврат заголовков в отдельный метод
		//header("Content-type: text/xml");

		$feed =
			$this->generateHead() .
			$this->generateChannels() .
			$this->generateItems() .
			$this->generateTale();

		return $feed;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Create a new FeedItem.
	 *
	 * @return Eresus_Feed_Writer_Item
	 */
	public function createNewItem()
	{
		$Item = new Eresus_Feed_Writer_Item($this->version);
		return $Item;
	}

	/**
	 * Add a FeedItem to the main class
	 *
	 * @param Eresus_Feed_Writer_Item $feedItem
	 * @return void
	 */
	public function addItem(Eresus_Feed_Writer_Item $feedItem)
	{
		$this->items []= $feedItem;
	}

	/**
	 * Set the 'title' channel element
	 *
	 * @param string $title  value of 'title' channel tag
	 * @return void
	 */
	public function setTitle($title)
	{
		$this->setChannelElement('title', $title);
	}

	/**
	 * Set the 'description' channel element
	 *
	 * @param  string $description  value of 'description' channel tag
	 * @return   void
	 */
	public function setDescription($description)
	{
		$this->setChannelElement('description', $description);
	}

	/**
	 * Set the 'link' channel element
	 *
	 * @param string $link  value of 'link' channel tag
	 * @return   void
	 */
	public function setLink($link)
	{
		$this->setChannelElement('link', $link);
	}

	/**
	 * Set the 'image' channel element
	 *
	 * @param string  $title
	 * @param string  $link
	 * @param string  $url
	 * @return void
	 */
	public function setImage($title, $link, $url)
	{
		$this->setChannelElement('image', array('title'=>$title, 'link'=>$link, 'url'=>$url));
	}

	/**
	 * Set the 'about' channel element. Only for RSS 1.0
	 *
	 * @param string $url  value of 'about' channel tag
	 * @return void
	 */
	public function setChannelAbout($url)
	{
		$this->data['ChannelAbout'] = $url;
	}

	/**
	 * Generates an UUID
	 *
     * @author     Anis uddin Ahmad <admin@ajaxray.com>
	 * @param string $key
     * @param string $prefix  an optional prefix
     *
	 * @return string  the formatted uuid
	 */
	public static function uuid($key = null, $prefix = '')
	{
		$key = ($key == null)? uniqid(rand()) : $key;
		$chars = md5($key);
		$uuid  = substr($chars,0,8) . '-';
		$uuid .= substr($chars,8,4) . '-';
		$uuid .= substr($chars,12,4) . '-';
		$uuid .= substr($chars,16,4) . '-';
		$uuid .= substr($chars,20,12);

		return $prefix . $uuid;
	}

	/**
	 * Prints the xml and rss namespace
	 *
	 * @return string
	 */
	private function generateHead()
	{
		$out  = '<?xml version="1.0" encoding="utf-8"?>' . "\n";

		if ($this->version == self::RSS2)
		{
			$out .= '<rss version="2.0"
					xmlns:content="http://purl.org/rss/1.0/modules/content/"
					xmlns:wfw="http://wellformedweb.org/CommentAPI/"
				  >' . PHP_EOL;
		}
		elseif ($this->version == self::RSS1)
		{
			$out .= '<rdf:RDF
					 xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
					 xmlns="http://purl.org/rss/1.0/"
					 xmlns:dc="http://purl.org/dc/elements/1.1/"
					>' . PHP_EOL;
		}
		elseif ($this->version == self::ATOM)
		{
			$out .= '<feed xmlns="http://www.w3.org/2005/Atom">' . PHP_EOL;
		}
		return $out;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Closes the open tags at the end of file
	 *
	 * @return string
	 */
	private function generateTale()
	{
		if ($this->version == self::RSS2)
		{
			$out = '</channel>' . PHP_EOL . '</rss>';
		}
		elseif ($this->version == self::RSS1)
		{
			$out = '</rdf:RDF>';
		}
		elseif ($this->version == self::ATOM)
		{
			$out = '</feed>';
		}
		else
		{
			$out = '';
		}
		return $out;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Creates a single node as xml format
	 *
	 * @param string $tagName     name of the tag
	 * @param mixed  $tagContent  tag value as string or array of nested tags in 'tagName' => 'tagValue' format
	 * @param array  $attributes  Attributes (if any) in 'attrName' => 'attrValue' format
	 * @return string  formatted xml tag
	 */
	private function makeNode($tagName, $tagContent, $attributes = null)
	{
		$nodeText = '';
		$attrText = '';

		if (is_array($attributes))
		{
			foreach ($attributes as $key => $value)
			{
				$attrText .= " $key=\"$value\" ";
			}
		}

		if (is_array($tagContent) && $this->version == self::RSS1)
		{
			$attrText = ' rdf:parseType="Resource"';
		}


		$attrText .= (in_array($tagName, $this->CDATAEncoding) && $this->version == self::ATOM)?
			' type="html" ' : '';
		$nodeText .= (in_array($tagName, $this->CDATAEncoding))?
			"<{$tagName}{$attrText}><![CDATA[" : "<{$tagName}{$attrText}>";

		if (is_array($tagContent))
		{
			foreach ($tagContent as $key => $value)
			{
				$nodeText .= $this->makeNode($key, $value);
			}
		}
		else
		{
			$nodeText .= (in_array($tagName, $this->CDATAEncoding)) ?
				$this->sanitizeCDATA($tagContent) : 
				htmlspecialchars($tagContent);
		}

		$nodeText .= (in_array($tagName, $this->CDATAEncoding))? "]]></$tagName>" : "</$tagName>";

		return $nodeText . PHP_EOL;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Print channels
	 *
	 * @return string
	 */
	private function generateChannels()
	{
		$out = '';
		//Start channel tag
		switch ($this->version)
		{
			case self::RSS2:
				$out .= '<channel>' . PHP_EOL;
			break;

			case self::RSS1:
				$out .= (isset($this->data['ChannelAbout']))?
					"<channel rdf:about=\"{$this->data['ChannelAbout']}\">" :
					"<channel rdf:about=\"{$this->channels['link']}\">";
			break;
		}

		//Print Items of channel
		foreach ($this->channels as $key => $value)
		{
			if ($this->version == self::ATOM && $key == 'link')
			{
				// ATOM prints link element as href attribute
				$out .= $this->makeNode($key,'',array('href'=>$value, 'rel'=>'self', 'type'=>'application/atom+xml'));
				//Add the id for ATOM
				$out .= $this->makeNode('id',$this->uuid($value,'urn:uuid:'));
			}
			else
			{
				$out .= $this->makeNode($key, $value);
			}

		}

		//RSS 1.0 have special tag <rdf:Seq> with channel
		if ($this->version == self::RSS1)
		{
			$out .= "<items>" . PHP_EOL . "<rdf:Seq>" . PHP_EOL;
			foreach ($this->items as $item)
			{
				$thisItems = $item->getElements();
				$out .= "<rdf:li resource=\"{$thisItems['link']['content']}\"/>" . PHP_EOL;
			}
			$out .= "</rdf:Seq>" . PHP_EOL . "</items>" . PHP_EOL . "</channel>" . PHP_EOL;
		}
		return $out;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Prints formatted feed items
	 *
	 * @return string
	 */
	private function generateItems()
	{
		$out = '';
		foreach ($this->items as $item)
		{
			$thisItems = $item->getElements();

			//the argument is printed as rdf:about attribute of item in rss 1.0
			$out .= $this->startItem($thisItems['link']['content']);

			foreach ($thisItems as $feedItem )
			{
				$out .= $this->makeNode($feedItem['name'], $feedItem['content'], $feedItem['attributes']);
			}
			$out .= $this->endItem();
		}
		return $out;
	}
	//-----------------------------------------------------------------------------

    /**
     * Make the starting tag of channels
     *
     * @param bool|string $about The value of about tag which is used for only RSS 1.0
     * @throws LogicException
     * @return string
     */
	private function startItem($about = false)
	{
		$out = '';
		if ($this->version == self::RSS2)
		{
			$out .= '<item>' . PHP_EOL;
		}
		elseif ($this->version == self::RSS1)
		{
			if ($about)
			{
				$out .= "<item rdf:about=\"$about\">" . PHP_EOL;
			}
			else
			{
				throw new LogicException('link element is not set .\n' .
					'It\'s required for RSS 1.0 to be used as about attribute of item');
			}
		}
		elseif ($this->version == self::ATOM)
		{
			$out .= "<entry>" . PHP_EOL;
		}
		return $out;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Closes feed item tag
	 *
	 * @return string
	 */
	private function endItem()
	{
		$out = '';
		if ($this->version == self::RSS2 || $this->version == self::RSS1)
		{
			$out .= '</item>' . PHP_EOL;
		}
		elseif ($this->version == self::ATOM)
		{
			$out .= "</entry>" . PHP_EOL;
		}
		return $out;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Sanitizes data which will be later on returned as CDATA in the feed.
	 *
	 * A "]]>" respectively "<![CDATA" in the data would break the CDATA in the
	 * XML, so the brackets are converted to a HTML entity.
	 *
	 * @param string $text  Data to be sanitized
	 *
     * @return string  Sanitized data
	 */
	private function sanitizeCDATA($text)
	{
		$text = str_replace("]]>", "]]&gt;", $text);
		$text = str_replace("<![CDATA[", "&lt;![CDATA[", $text);

		return $text;
	}
}
