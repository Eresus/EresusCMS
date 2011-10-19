<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mk@procreat.ru>
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
 * Абстрактный элемент документа HTML
 *
 * @package Eresus
 * @since 2.15
 */
class HtmlElement
{
	/**
	 * Имя тега
	 *
	 * @var string
	 */
	private $tagName;

	/**
	 * Атрибуты
	 *
	 * @var array
	 */
	private $attrs = array();

	/**
	 * Содержимое
	 *
	 * @var string
	 */
	private $contents = null;

	/**
	 * Конструктор
	 *
	 * @param string $tagName
	 *
	 * @since 2.15
	 */
	public function __construct($tagName)
	{
		$this->tagName = $tagName;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает значение атрибута
	 *
	 * @param string $name              имя атрибута
	 * @param string $value [optional]  значение атрибута
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	public function setAttribute($name, $value = true)
	{
		$this->attrs[$name] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает значение атрибута
	 *
	 * @param string $name  имя атрибута
	 *
	 * @return mixed
	 *
	 * @since 2.15
	 */
	public function getAttribute($name)
	{
		if (!isset($this->attrs[$name]))
		{
			return null;
		}

		return $this->attrs[$name];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает содержимое
	 *
	 * @param string $contents  содержимое
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	public function setContents($contents)
	{
		$this->contents = $contents;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает разметку элемента
	 *
	 * @return string  разметка HTML
	 *
	 * @since 2.15
	 */
	public function getHTML()
	{
		// Открывающий тег
		$html = '<' . $this->tagName;

		/* Добавляем атрибуты */
		foreach ($this->attrs as $name => $value)
		{
			$html .= ' ' . $name;

			if ($value !== true)
			{
				$html .= '="' . $value . '"';
			}
		}

		$html .= '>';

		/* Если есть содержимое, то добавляем его и закрывающий тег */
		if ($this->contents !== null)
		{
			$html .= $this->contents . '</' . $this->tagName . '>';
		}

		return $html;
	}
	//-----------------------------------------------------------------------------
}



/**
 * Элемент <script>
 *
 * @package Eresus
 * @since 2.15
 */
class HtmlScriptElement extends HtmlElement
{
	/**
	 * Создаёт новый элемент <script>
	 *
	 * @param string $script [optional]  URL или код скрипта.
	 *
	 * @since 2.15
	 */
	public function __construct($script = '')
	{
		parent::__construct('script');

		$this->setAttribute('type', 'text/javascript');

		/*
		 * Считаем URL-ом всё, что:
		 * - либо содержит xxx:// в начале
		 * - либо состоит из минимум двух групп непробельных символов, разделённых точкой или слэшем
		 */
		if ($script !== '' && preg_match('=(^\w{3,8}://|^\S*(\.|/)\S*$)=', $script))
		{
			$this->setAttribute('src', $script);
			$this->setContents('');
		}
		else
		{
			$this->setContents($script);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Устанавливает содержимое
	 *
	 * @param string $contents  содержимое
	 *
	 * @return void
	 *
	 * @since 2.15
	 */
	public function setContents($contents)
	{
		if ($contents)
		{
			$contents = "//<!-- <![CDATA[\n". $contents . "\n//]] -->";
		}
		parent::setContents($contents);
	}
	//-----------------------------------------------------------------------------

}



/**
 * Родительский класс веб-интерфейсов
 *
 * @package Eresus
 */
class WebPage
{
	/**
	 * Идентификатор текущего раздела
	 *
	 * @var int
	 */
	public $id = 0;

	/**
	 * HTTP-заголовки ответа
	 *
	 * @var array
	 */
	public $headers = array();

	/**
	 * Описание секции HEAD
	 *
	 * -	meta-http - мета-теги HTTP-заголовков
	 * -	meta-tags - мета-теги
	 * -	link - подключение внешних ресурсов
	 * -	style - CSS
	 * -	script - Скрипты
	 * -	content - прочее
	 *
	 * @var array
	 */
	protected $head = array (
		'meta-http' => array(),
		'meta-tags' => array(),
		'link' => array(),
		'style' => array(),
		'scripts' => array(),
		'content' => '',
	);

	/**
	 * Наполнение секции <body>
	 *
	 * @var array
	 */
	protected $body = array(
		'scripts' => array(),
	);

	/**
	 * Значения по умолчанию
	 * @var array
	 */
	protected $defaults = array(
		'pageselector' => array(
			'<div class="pages">$(pages)</div>',
			'&nbsp;<a href="$(href)">$(number)</a>&nbsp;',
			'&nbsp;<b>$(number)</b>&nbsp;',
			'<a href="$(href)">&larr;</a>',
			'<a href="$(href)">&rarr;</a>',
		),
	);

	/**
	 * Конструктор
	 *
	 * @return WebPage
	 */
	public function __construct()
	{
	}
	//-----------------------------------------------------------------------------

	/**
	 * Установить мета-тег HTTP-заголовка
	 *
	 * Добавляет или изменяет мета-тег <meta http-equiv="$httpEquiv" content="$content" />
	 *
	 * @param string $httpEquiv  Имя заголовка HTTP
	 * @param string $content  	  Значение заголовка
	 */
	public function setMetaHeader($httpEquiv, $content)
	{
		$this->head['meta-http'][$httpEquiv] = $content;
	}
	//------------------------------------------------------------------------------

	/**
	 * Установка мета-тега
	 *
	 * @param string $name  		 Имя тега
	 * @param string $content  Значение тега
	 */
	public function setMetaTag($name, $content)
	{
		$this->head['meta-tags'][$name] = $content;
	}
	//------------------------------------------------------------------------------

	/**
	 * Подключение CSS-файла
	 *
	 * @param string $url    URL файла
	 * @param string $media  Тип носителя
	 */
	public function linkStyles($url, $media = '')
	{
		/* Проверяем, не добавлен ли уже этот URL  */
		for ($i = 0; $i < count($this->head['link']); $i++)
			if ($this->head['link'][$i]['href'] == $url)
				return;

		$item = array('rel' => 'StyleSheet', 'href' => $url, 'type' => 'text/css');

		if (!empty($media))
			$item['media'] = $media;

		$this->head['link'][] = $item;
	}
	//------------------------------------------------------------------------------

	/**
	 * Встраивание CSS
	 *
	 * @param string $content  Стили CSS
	 * @param string $media 	  Тип носителя
	 */
	public function addStyles($content, $media = '')
	{
		$content = preg_replace(array('/^(\s)+/m', '/^(\S)/m'), array('		', '	\1'), $content);
		$content = rtrim($content);
		$item = array('content' => $content);
		if (!empty($media))
			$item['media'] = $media;
		$this->head['style'][] = $item;
	}
	//------------------------------------------------------------------------------

	/**
	 * Подключение клиентского скрипта
	 *
	 * В качестве дополнительных параметров метод может принимать:
	 *
	 * <b>Типы скриптов</b>
	 * - ecma, text/ecmascript
	 * - javascript, text/javascript
	 * - jscript, text/jscript
	 * - vbscript, text/vbscript
	 *
	 * <b>Параметры загрузки скриптов</b>
	 * - async
	 * - defer
	 *
	 * Если скрипту передан параметр defer, то скрипт будет подключён в конце документа, перед
	 * </body>, в противном случае он будет подключён в <head>.
	 *
	 * @param string $url                     URL скрипта
	 * @param string $ar1...$argN [optional]  Дополнительные параметры
	 */
	public function linkScripts($url)
	{
		foreach ($this->head['scripts'] as $script)
		{
			if ($script->getAttribute('src') == $url)
			{
				return;
			}
		}

		$script = new HtmlScriptElement($url);

		$args = func_get_args();
		// Отбрасываем $url
		array_shift($args);

		foreach ($args as $arg)
		{
			switch (strtolower($arg))
			{
				case 'ecma':
				case 'text/ecmascript':
					$script->setAttribute('type', 'text/ecmascript');
				break;

				case 'javascript':
				case 'text/javascript':
					$script->setAttribute('type', 'text/javascript');
				break;

				case 'jscript':
				case 'text/jscript':
					$script->setAttribute('type', 'text/jscript');
				break;

				case 'vbscript':
				case 'text/vbscript':
					$script->setAttribute('type', 'text/vbscript');
				break;

				case 'async':
				case 'defer':
					$script->setAttribute($arg);
				break;
			}
		}

		if ($script->getAttribute('defer'))
		{
			$this->body['scripts'][] = $script;
		}
		else
		{
			$this->head['scripts'][] = $script;
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Добавление клиентских скриптов
	 *
	 * <b>Типы скриптов</b>
	 * - ecma, text/ecmascript
	 * - javascript, text/javascript
	 * - jscript, text/jscript
	 * - vbscript, text/vbscript
	 *
	 * <b>Параметры загрузки скриптов</b>
	 * - head - вставить в секцию <head> (политика по умолчанию)
	 * - body - вставить в секцию <body>
	 *
	 * @param string $code                    Код скрипта
	 * @param string $ar1...$argN [optional]  Дополнительные параметры
	 */
	public function addScripts($code)
	{
		$script = new HtmlScriptElement($code);

		$args = func_get_args();
		// Отбрасываем $code
		array_shift($args);

		// По умолчанию помещаем скрипты в <head>
		$defer = false;

		foreach ($args as $arg)
		{
			switch (strtolower($arg))
			{
				case 'emca':
				case 'text/emcascript':
					$script->setAttribute('type', 'text/ecmascript');
				break;

				case 'javascript':
				case 'text/javascript':
					$script->setAttribute('type', 'text/javascript');
				break;

				case 'jscript':
				case 'text/jscript':
					$script->setAttribute('type', 'text/jscript');
				break;

				case 'vbscript':
				case 'text/vbscript':
					$script->setAttribute('type', 'text/vbscript');
				break;

				case 'defer':
					$defer = true;
				break;
			}
		}

		if ($defer)
		{
			$this->body['scripts'][] = $script;
		}
		else
		{
			$this->head['scripts'][] = $script;
		}
	}
	//------------------------------------------------------------------------------

	/**
	 * Подключает библиотеку JavaScript
	 *
	 * При множественном вызове метода, библиотека будет подключена только один раз.
	 *
	 * Доступные библиотеки и их аргументы:
	 *
	 * - jquery — jQuery
	 *   - ui — jQuery UI
	 *   - cookie — jQuery.Cookie
	 *
	 * @param string $library     имя библиотеки
	 * @param string $arg1…$argN  дополнительные аргументы
	 *
	 * @return void
	 *
	 * @since 2.16
	 */
	public function linkJsLib($library)
	{
		$args = func_get_args();
		array_shift($args);
		switch ($library)
		{
			case 'jquery':
				$this->linkScripts($GLOBALS['Eresus']->root . 'core/jquery/jquery.min.js', 'async');
				if (in_array('cookie', $args))
				{
					$this->linkScripts($GLOBALS['Eresus']->root . 'core/jquery/jquery.cookie.js');
				}
						if (in_array('ui', $args))
				{
					$this->linkScripts($GLOBALS['Eresus']->root . 'core/jquery/jquery-ui.min.js');
				}
			break;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка секции <head>
	 *
	 * @return string  Отрисованная секция <head>
	 */
	public function renderHeadSection()
	{
		$result = array();
		/* <meta> теги */
		if (count($this->head['meta-http']))
			foreach($this->head['meta-http'] as $key => $value)
				$result[] = '	<meta http-equiv="'.$key.'" content="'.$value.'" />';

		if (count($this->head['meta-tags']))
			foreach($this->head['meta-tags'] as $key => $value)
				$result[] = '	<meta name="'.$key.'" content="'.$value.'" />';

		/* <link> */
		if (count($this->head['link']))
			foreach($this->head['link'] as $value)
				$result[] = '	<link rel="'.$value['rel'].'" href="'.$value['href'].'" type="'.
					$value['type'].'"'.(isset($value['media'])?' media="'.$value['media'].'"':'').' />';

		/*
		 * <script>
		 */
		foreach ($this->head['scripts'] as $script)
		{
			$result[] = $script->getHTML();
		}

		/* <style> */
		if (count($this->head['style']))
			foreach($this->head['style'] as $value)
				$result[] = '	<style type="text/css"'.(isset($value['media'])?' media="'.
					$value['media'].'"':'').'>'."\n".$value['content']."\n  </style>";

		$this->head['content'] = trim($this->head['content']);
		if (!empty($this->head['content']))
			$result[] = $this->head['content'];

		$result = implode("\n" , $result);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Отрисовка секции <body>
	 *
	 * @return string  HTML
	 */
	protected function renderBodySection()
	{
		$result = array();
		/*
		 * <script>
		 */
		foreach ($this->body['scripts'] as $script)
		{
			$result[] = $script->getHTML();
		}

		$result = implode("\n" , $result);
		return $result;
	}
	//------------------------------------------------------------------------------

	/**
	 * Строит URL GET-запроса на основе переданных аргументов
	 *
	 * URL будет состоять из двух частей:
	 * 1. Адрес текущего раздела ($Eresus->request['path'])
	 * 2. key=value аргументы
	 *
	 * Список аргументов составляется объединением списка аргументов текущего запроса
	 * и элементов массива $args. Элементы $args имеют приоритет над аргументами текущего
	 * запроса.
	 *
	 * Если значение аргумента - пустая строка, он будет удалён из запроса.
	 *
	 * @param array $args  Установить аргументы
	 * @return string
	 */
	public function url($args = array())
	{
		global $Eresus;

		/* Объединяем аргументы метода и аргументы текущего запроса */
		$args = array_merge($Eresus->request['arg'], $args);

		/* Превращаем значения-массивы в строки, соединяя элементы запятой */
		foreach ($args as $key => $value)
			if (is_array($value))
				$args[$key] = implode(',', $value);

		$result = array();
		foreach ($args as $key => $value)
			if ($value !== '')
				$result []= "$key=$value";

		$result = implode('&amp;', $result);
		$result = $Eresus->request['path'].'?'.$result;
		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Возвращает клиентский URL страницы с идентификатором $id
	 *
	 * @param int $id  Идентификатор страницы
	 * @return string URL страницы или NULL если раздела $id не существует
	 */
	public function clientURL($id)
	{
		global $Eresus;

		$parents = $Eresus->sections->parents($id);

		if (is_null($parents)) return null;

		array_push($parents, $id);
		$items = $Eresus->sections->get( $parents);

		$list = array();
		for($i = 0; $i < count($items); $i++) $list[array_search($items[$i]['id'], $parents)-1] = $items[$i]['name'];
		$result = $Eresus->root;
		for($i = 0; $i < count($list); $i++) $result .= $list[$i].'/';

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отрисовка переключателя страниц
	 *
	 * @param int     $total      Общее количество страниц
	 * @param int     $current    Номер текущей страницы
	 * @param string  $url        Шаблон адреса для перехода к подстранице.
	 * @param array   $templates  Шаблоны оформления
	 * @return string
	 */
	public function pageSelector($total, $current, $url = null, $templates = null)
	{
		global $Eresus;

		$result = '';
		# Загрузка шаблонов
		if (!is_array($templates))
			$templates = array();
		for ($i=0; $i < 5; $i++)
			if (!isset($templates[$i]))
				$templates[$i] = $this->defaults['pageselector'][$i];

		if (is_null($url))
			$url = $Eresus->request['path'].'p%d/';

		$pages = array(); # Отображаемые страницы
		# Определяем номера первой и последней отображаемых страниц
		$visible = 10;
		if ($total > $visible)
		{
			# Будут показаны НЕ все страницы
			$from = floor($current - $visible / 2); # Начинаем показ с текущей минус половину видимых
			if ($from < 1)
				$from = 1; # Страниц меньше 1-й не существует
			$to = $from + $visible - 1; # мы должны показать $visible страниц
			if ($to > $total)
			{ # Но если это больше чем страниц всего, вносим исправления
				$to = $total;
				$from = $to - $visible + 1;
			}
		}
			else
		{
			# Будут показаны все страницы
			$from = 1;
			$to = $total;
		}
		for($i = $from; $i <= $to; $i++)
		{
			$src['href'] = sprintf($url, $i);
			$src['number'] = $i;
			$pages[] = replaceMacros($templates[$i != $current ? 1 : 2], $src);
		}

		$pages = implode('', $pages);
		if ($from != 1)
			$pages = replaceMacros($templates[3], array('href' => sprintf($url, 1))).$pages;
		if ($to != $total)
			$pages .= replaceMacros($templates[4], array('href' => sprintf($url, $total)));
		$result = replaceMacros($templates[0], array('pages' => $pages));

		return $result;
	}
	//------------------------------------------------------------------------------

}
