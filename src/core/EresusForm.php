<?php
/**
 * ${product.title}
 *
 * Веб-форма
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
 */

/**
 * Веб-форма
 *
 * Компонент содержит в себе вспомогательный функционал по работе с
 * формами.
 *
 * Форма описывается в файле шаблона, который сначала обрабатывается
 * стандартным шаблонизатором, а затем специальным парсером расширенных
 * значений, который обрабатывает дополнительные теги и атрибуты.
 *
 * Данные формы отправляются только методом POST.
 *
 * <b>ПРАВИЛА СОСТАВЛЕНИЯ ШАБЛОНОВ</b>
 *
 * 1. Шаблон должен содержать только один корневой элемент
 * 2. Корневым элементом должен быть элемент form
 * 3. В корневом элементе должно быть объявлено пространство имён
 *    "http://eresus.ru/schema/form/"
 * 4. У корневого элемента должен быть установлен id
 * 5. Атрибут method у корневого элемента необязателен и всегда будет "post"
 * 6. Разметка должна использовать XHTML и быть валидной
 *
 * <code>
 *   <form xmlns:fc="http://eresus.ru/schema/form/" id="form_id" ...>
 *     ...
 *   </form>
 * </code>
 *
 * Все расширенные теги и атрибуты принадлежат пространству имён
 * "http://eresus.ru/schema/form/".
 *
 * --------------------------------------------------------------------------
 *
 * <b>РАСШИРЕННЫЕ ТЕГИ</b>
 *
 * <b>wysiwyg</b>
 *
 * Визуальный редактор.
 *
 * <code>
 *   <fc:wysiwyg rows="20" cols="50">
 *
 *   </fc:wysiwyg>
 * </code>
 *
 * <b>tabwidget</b>
 *
 * Виджет "Вкладки".
 *
 * <code>
 *   <fc:tabwidget id="tabs">
 *
 *     <fc:tabcontrol>
 *       <fc:tab name="tab1">Вкладка 1</fc:tab>
 *       <fc:tab name="tab2">Вкладка 2</fc:tab>
 *     </fc:tabcontrol>
 *
 *     <fc:tabs>
 *       <fc:tab name="tab1">...</fc:tab>
 *       <fc:tab name="tab2">...</fc:tab>
 *     </fc:tabs>
 *
 *   </fc:tabwidget>
 * </code>
 *
 * Требования:
 * 1. У тега tabwidget должен быть атрибут "id"
 *
 * Вложенные теги:
 * - tabcontrol
 * - tabs
 *
 * Что делает тег:
 * 1. Создаёт "div" с таким же идентификатором и классом "tab-widget"
 * 2. Вставляет JS-код для инициализации плагина jQuery
 *
 * <b>tabcontrol</b>
 *
 * Переключатель вкладок.
 *
 * <code>
 *   <fc:tabcontrol>
 *     <fc:tab name="tab1">Вкладка 1</fc:tab>
 *     <fc:tab name="tab2">Вкладка 2</fc:tab>
 *   </fc:tabcontrol>
 * </code>
 *
 * Требования:
 * - Может располагаться только внутри tabwidget.
 *
 * Вложенные теги:
 * - tab
 *
 * Что делает тег:
 * - Создаёт ul с переключателями вкладок
 *
 * <b>tabs</b>
 *
 * Область содержимого вкладок.
 *
 * <code>
 *   <fc:tabs>
 *     <fc:tab name="tab1">...</fc:tab>
 *     <fc:tab name="tab2">...</fc:tab>
 *   </fc:tabs>
 * </code>
 *
 * Требования:
 * - Может располагаться только внутри tabwidget.
 *
 * Вложенные теги:
 * - tab
 *
 * Что делает тег:
 * - Создаёт div, содержащие все панели вкладок
 *
 * <b>tab</b>
 *
 * В зависимости от того располагается этот тег внутри tabcontrol или tab
 * является либо кнопкой вкладки либо панелью вкладки.
 *
 * <code>
 *   <fc:tab name="tabName">Содержимое вкладки</fc:tab>
 * </code>
 *
 * Атрибуты:
 * - name - имя вкладки. Должно быть одинаковое у тега tab в tabcontrol и
 * соответствующего ему тега tab в tabs
 *
 * Требования:
 * - Может располагаться только внутри tabcontrol или tabs.
 *
 * Что делает тег:
 * - Создаёт li (в tabcontrol) или div (в tabs)
 * - Для li значение атрибута используется для href
 * - Для div значение атрибута используется для id
 *
 * <b>attach</b>
 *
 * Позволяет присоединить некоторые расширенные атрибуты ко всем элементам,
 * удовлетворяющим jQuery-селектору.
 *
 * <code>
 *   <fc:attach to="input[name=myRadioButton]" required="true" />
 * </code>
 *
 * В приведённом примере пользователь обязательно должен отметить одну из радио-кнопок
 * с именем "myRadioButton".
 *
 * Атрибуты:
 * - to - селектор, поддерживаемый jQuery
 * - required - см. описание атрибута ниже
 *
 * --------------------------------------------------------------------------
 *
 * <b>РАСШИРЕННЫЕ АТРИБУТЫ</b>
 *
 * <b>required</b>
 *
 * Отмечает элемент ввода как обязательный для заполнения:
 *
 * 1. Добавляет в связанный с элементом <label> код '<sup>*</sup>'
 * 2. Добавляет JavaScript-проверку заполненности поля в обработчик submit
 *
 * <i>Возможные значения:</i>
 *
 * - <b>true</b> - Проверка и вывод стандартного сообщения об ошибке
 * - <b>строка</b> - Собственное сообщение об ошибке (пока не реализовано)
 *
 * <i>Применим к:</i>
 * - input (text, password, checkbox, radio)
 * - textarea
 *
 * <i>Условия:</i>
 *
 * - Элемент ввода должен иметь id
 *
 * <code>
 *   <input type="text" name="name" id="someId" fc:required="true" />
 * </code>
 *
 * <b>password</b>
 *
 * Проверяет соответствие подтверждения паролю.
 *
 * <i>Возможные значения:</i>
 *
 * - <b>строка</b> - идентификатор поля пароля
 *
 * <i>Условия:</i>
 *
 * - Применимо только к элементам input[type=password]
 * - Элемент ввода должен иметь id
 * - У поля пароля должен быть атрибут id
 *
 * <code>
 *   <input type="password" name="password" id="passFieldName" fc:required="true" />
 *   <input type="password" name="confirm" id="confirmField" fc:password="passFieldName" />
 * </code>
 *
 * <b>match</b>
 *
 * Проверка значения поля на соответствие регулярному выражению.
 * <b>Внимание!</b> Пустое значение так же считается правильным. Для проверки поля
 * на заполненность можно комбинировать этот атрибут с атрибутом "required".
 *
 * <i>Возможные значения:</i>
 *
 * - Регулярное выражение совместимое с JavaScript и PRCE
 *
 * <i>Условия:</i>
 *
 * - Элемент ввода должен иметь id
 *
 * <code>
 *   <input type="text" name="name" id="someId" fc:match="/^[a-z0-9]+$/i" />
 * </code>
 * В приведённом примере будет выдано сообщение об ошибке если в поле будет
 * введено что-либо кроме латинских букв и цифр.
 *
 * <b>email</b>
 *
 * Проверка того что в поле введён адрес email.
 * <b>Внимание!</b> Пустое значение так же считается правильным. Для проверки поля
 * на заполненность можно комбинировать этот атрибут с атрибутом "required".
 *
 * <i>Возможные значения:</i>
 *
 * - <b>true</b> - Проверка и вывод стандартного сообщения об ошибке
 * - <b>строка</b> - Собственное сообщение об ошибке (пока не реализовано)
 *
 * <i>Условия:</i>
 *
 * - Элемент ввода должен иметь id
 *
 * <code>
 *   <input type="text" name="mail" id="someId" fc:email="true" />
 * </code>
 *
 * --------------------------------------------------------------------------
 *
 * <b>ИСПОЛЬЗОВАНИЕ В JAVASCRIPT</b>
 *
 * Для каждой формы создаётся объект класса PartnerEresusForm имя которого
 * совпадает с идентификатором формы.
 *
 * Этот объект следует использоваться для хранения всей JS-информации формы.
 *
 * <b>СВОЙСТВА</b>
 *
 * <b>tabWidget</b>: jQuery
 *
 * Объект виджета "Вкладки"
 *
 * <b>МЕТОДЫ</b>
 *
 * <b>addFormMessage(msg)</b>
 *
 * Добавление сообщения формы.
 * - <b>msg</b>: string - текст сообщения
 *
 * <b>addTabMessage(msg, tabIndex)</b>
 *
 * Добавление сообщения вкладки.
 * - <b>msg</b>: string - текст сообщения
 * - <b>tabIndex</b>: integer - номер вкладки
 *
 * <b>addValidator(type, [args...])</b>
 *
 * Добавление валидатора к форме.
 * - <b>type</b>: String - Тип валидатора.
 * - <b>[args...]</b>: mixed - Дополнительные данные. См. возможные типы валидаторов.
 *
 * Возможные типы валидаторов:
 * <b>required</b> - обязательное поле. Аргументы:
 * - selector: String - jQuery-селектор проверяемых элементов.
 * - message (optional): String - Сообщение для вывода валидатором
 *
 * <b>password</b> - сравнение с полем пароля. Аргументы:
 * - id: String - идентификатор поля пароля.
 * - message (optional): String - Сообщение для вывода валидатором
 *
 * <b>regexp</b> - Проверка регулярным выражением. Аргументы:
 * - pattern: String - регулярное выражение.
 *
 * <b>custom</b> - Callback-функция проверки. Аргументы:
 * - callback: Callback - Callback-функция.
 *
 * --------------------------------------------------------------------------
 *
 * <b>ИСПОЛЬЗОВАНИЕ В PHP</b>
 *
 * Общий принцип работы с компонентом следующий:
 * 1. В представлении создаётся экземпляр компонента формы
 * 2. В форму подставляются нужные данные (при необходимости)
 * 3. Форма компилируется и выводится в браузер
 * 4. Пользователь заполняет форму и отправляет её на сервер
 * 5. На сервере контроллёр создаёт экземпляр компонента той же формы
 * 6. Считывает из неё данные и обрабатывает их
 *
 * <b>Отображение формы</b>
 *
 * <code>
 *   # Создаём экземпляр формы
 *   $form = new EresusForm('Common/path/form');
 *
 *   # Загружаем данные в форму
 *   $profile = new ProfileModel();
 *   $form->setValue('profile', $profile);
 *
 *   # Компилируем форму
 *   $result = $form->compile();
 * </code>
 *
 * <b>Обработка пользовательского ввода</b>
 *
 * <code>
 * 	# Создаём экземпляр той же формы что и выше
 *  $form = new EresusForm('Partner/path/form');
 *
 *  $profile = new ProfileModel();
 *  # Читаем данные
 *  $profile->orgName = $form->getValue('orgName');
 * </code>
 *
 * @see Eresus_Template
 *
 * @package Eresus
 */
class EresusForm
{

	/**
	 * Пространство имён
	 */
	const NS = 'http://eresus.ru/schema/form/';

	/**
	 * Режим работы: ввод данных
	 * @var string
	 */
	const INPUT = 'input';

	/**
	 * Режим работы: обработка данных
	 * @var string
	 */
	const PROCESS = 'process';

	/**
	 * Список узлов - полей ввода
	 * @var array
	 */
	protected $inputs = array('input', 'textarea');

	/**
	 * Идентификатор формы
	 *
	 * Используется для организации взаимодействия между клиентской
	 * и серверной частями компонента
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Режим формы
	 *
	 * Возможные значения:
	 * - input - ввод данных
	 * - process - обработка данных
	 *
	 * @var string
	 */
	protected $mode = self::INPUT;

	/**
	 * Имя шаблона формы
	 *
	 * @var Eresus_Template
	 */
	protected $template;

	/**
	 * Список значений
	 * @var array
	 */
	protected $values = array();

	/**
	 * Сообщения формы
	 */
	protected $messages = array();

	/**
	 * XML-данные формы
	 *
	 * @var DOMDocument
	 */
	protected $xml;

	/**
	 * JavaScript
	 *
	 * @var array
	 */
	protected $js = array();

	/**
	 * Признак наличия ошибок при заполнении формы
	 *
	 * @var bool
	 *
	 * @see inviladData(), sessionResotore()
	 */
	protected $isInvalid = false;

	/**
	 * Список имён неправильно заполненных полей
	 *
	 * @var array
	 *
	 * @see invalidValue(), inviladData()
	 */
	protected $invalidData = array();

	/**
	 * Автоматическая проверка данных
	 *
	 * Если установлен в false, то JS-проверки валидаторами
	 * проводиться не будут. Так же не будет произведён
	 * автоматический вызов HTTP::goback() после проверки на
	 * сервере.
	 *
	 * @var bool
	 *
	 * @see invalidValue
	 */
	protected $autoValidate = true;

	/**
	 * Конструктор
	 *
	 * @param string $template            Имя файла шаблона
	 */
	function __construct($template)
	{
		$this->template = $template;

		if (self::PROCESS == $this->mode)
		{
			$this->validate();
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Деструктор
	 *
	 * Сохраняет необходимые данные в сессии
	 */
	function __destruct()
	{
		if ($this->mode == self::PROCESS && $this->invalidData())
		{
			$this->sessionStore();
		}
	}
	//-----------------------------------------------------------------------------

	/******************************************
	 *
	 * Режим INPUT
	 *
	 ******************************************/

	/**
	 * Компиляция формы
	 *
	 * @return string HTML
	 */
	public function compile()
	{
		$this->loadXML();
		$this->id = $this->xml->firstChild->nextSibling->getAttribute('id');
		$this->sessionRestore();
		$this->detectAutoValidate();

        /** @var TAdminUI $page */
        $page = Eresus_Kernel::app()->getPage();
        $page->linkScripts(Eresus_Kernel::app()->getLegacyKernel()->root . 'core/EresusForm.js');

		$html = $this->parseExtended();

		return $html;
	}

	/**
	 * Установить значение переменной
	 *
	 * Создаёт внутреннюю переменную с именем $name и значением $value для
	 * использования в шаблоне формы.
	 *
	 * @param string $name   Имя переменной
	 * @param mixed  $value  Значение переменной
	 */
	public function setValue($name, $value)
	{
		$this->values[$name] = $value;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Распаковать данные, полученные из сессии
	 *
	 * @param array $data
	 */
	protected function unpackData($data)
	{
		$this->values = $data['values'];
		$this->isInvalid = $data['isInvalid'];
		$this->messages = $data['messages'];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Восстановление данных из сессии
	 */
	protected function sessionRestore()
	{
		if (isset($_SESSION[get_class($this)][$this->id]))
		{
			$this->unpackData($_SESSION[get_class($this)][$this->id]);
			unset($_SESSION[get_class($this)][$this->id]);
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка расширенного синтаксиса
	 *
	 * @return string
	 */
	protected function parseExtended()
	{

		$this->addFormMessagesNode();

		$this->xml->firstChild->nextSibling->setAttribute('method', 'post');

		$this->processBranch($this->xml->firstChild->nextSibling);

		/*
		 * Обработка тегов:
		 * 1. Удаление расширенных атрибутов
		 * 2. Предотвращение схлопывания пустых тегов
		 */
		$tags = $this->xml->getElementsByTagName('*');
		$collapsable = array('br', 'hr', 'input');

		for ($i = 0; $i < $tags->length; $i++)
		{
			$node = $tags->item($i);

			$isElement = $node->nodeType == XML_ELEMENT_NODE;

			if ($isElement)
			{
				$hasAttributes = $isElement && $node->hasAttributes();
				if ($hasAttributes)
				{
					$attrs = $node->attributes;
					for ($j = 0; $j < $attrs->length; $j++)
					{
						/* @var DOMAttr $attr */
						$attr = $attrs->item($j);
						if ($attr->namespaceURI == self::NS)
						{
							$this->extendedAttr($node, $attr);
							/*
							 * в extendedAttr текущий атрибут удаляется, поэтому следующим шагом рассматривается
							 * элемент с тем же индексом
							 */
							$j--;
						}
					}
				}

				if ($node->textContent === '' && !in_array($node->nodeName, $collapsable))
				{
					$cdata = $this->xml->createCDATASection('');
					$node->appendChild($cdata);
				}
			}
		}

		$id = $this->xml->firstChild->nextSibling->getAttribute('id');
		// инициализация объекта формы
		$scriptContents = "\n\$(document).ready(function () {window.$id = new EresusForm('$id');\n";

		/*
		 * Если в сессии установлен флаг isInvalid, вызываем перепроверку формы
		 */
		if ($this->mode == self::INPUT && $this->isInvalid)
		{
			$this->js []= 'validate(true);';
		}

		// необходимо для отображения сообщений об ошибках, найденных на сервере
		$this->js []= 'showFormMessages();';
		if ($this->js)
		{
			foreach ($this->js as $command)
			{
				$scriptContents .= "$id.$command\n";
			}
		}
		$scriptContents .= "});";
		$script = $this->xml->createElement('script', $scriptContents);
		$script->setAttribute('type', 'text/javascript');
		$this->xml->firstChild->nextSibling->appendChild($script);

		$this->xml->formatOutput = true;
		$html = $this->xml->saveXML($this->xml->firstChild->nextSibling); # This exclude xml declaration
		$html = preg_replace('/\s*xmlns:\w+=("|\').*?("|\')/', '', $html); # Remove ns attrs
		/*$html = preg_replace('/<\?.*\?>/', '', $html);*/
		$html = str_replace('<![CDATA[]]>', '', $html); # Remove empty <![CDATA[]]> sections

		return $html;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавление области сообщений формы
	 */
	protected function addFormMessagesNode()
	{
		$node = $this->xml->createElement('div');
		$node->setAttribute('class', 'form-messages');
		$this->xml->firstChild->nextSibling->
			insertBefore($node, $this->xml->firstChild->nextSibling->firstChild);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка ветки документа
	 *
	 * Метод рекурсивно перебирает все дочерние узлы $branch
	 *
	 * @param DOMNode $branch  Ветка документа
	 */
	protected function processBranch(DOMNode $branch)
	{
		$list = $this->childrenAsArray($branch->childNodes);

		for ($i = 0; $i < count($list); $i++)
		{
			$node = $list[$i];
			if ($node->namespaceURI == self::NS)
			{
				$node = $this->processExtendedNode($node);
			}

			if ($node && in_array($node->nodeName, $this->inputs))
			{
				$node = $this->processInput($node);
			}

			if ($node)
			{
				$this->processBranch($node);
			}
			//$this->processExtendedAttributes($node);
		}

	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка поля ввода
	 * @param DOMNode $node
	 * @return DOMNode
	 */
	protected function processInput($node)
	{
		$name = $node->getAttribute('name');

		/*
		 * Подставляем предопределённые значения
		 */
		if ($name)
		{
			if (isset($this->values[$name]))
			{
				switch ($node->nodeName)
				{
					case 'input':

						switch ($node->getAttribute('type'))
						{
							case 'password':
							break;

							case 'checkbox':
								if ($this->values[$name])
								{
									$node->setAttribute('checked', 'checked');
								}
							break;

							case 'radio':
								if ($node->getAttribute('value') == $this->values[$name])
								{
									$node->setAttribute('checked', 'checked');
								}
							break;

							default:
								$node->setAttribute('value', $this->values[$name]);
						}

					break;

				}

			}

		}

		return $node;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка расширенных тегов
	 *
	 * @param DOMNode $node  Элемент
	 *
	 * @return DOMNode  $node или его замена
	 */
	protected function processExtendedNode(DOMNode $node)
	{
		$handler = 'extendedNode' . $node->localName;

		if (method_exists($this, $handler))
		{
			return $this->$handler($node);
		}
		else
		{
            Eresus_Kernel::log(__METHOD__, LOG_WARNING, 'Unsupported EresusForm tag "%s"', $node->localName);
			return $node;
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка тега "wysiwyg"
	 *
	 * @param DOMElement $node  Элемент
	 *
	 * @return DOMElement
	 */
	protected function extendedNodeWysiwyg(DOMElement $node)
	{
		$parent = $node->parentNode;
		//$id = $node->getAttribute('id');

		/* Создаём замену для тега wysiwyg */
		$textarea = $this->xml->createElement('textarea');
		$this->copyElement($node, $textarea);

		//$tabDiv->setAttribute('id', $id);
		$textarea->setAttribute('class', 'wysiwyg');
		$parent->replaceChild($textarea, $node);

		// инициализация вкладок, проводится перед обработкой ошибок
		//array_unshift($this->js, 'initTabWidget("'.$node->getAttribute('id').'")');

		return $textarea;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка тега "tabwidget"
	 *
	 * @param DOMNode $node  Элемент
	 */
	protected function extendedNodeTabWidget(DOMNode $node)
	{
		$parent = $node->parentNode;
		$id = $node->getAttribute('id');

		/* Создаём замену для тега tabwidget */
		$tabDiv = $this->xml->createElement('div');
		/* Копируем в него содержимое tabwidget */
		$childNodes = $this->childrenAsArray($node->childNodes);
		for ($i = 0; $i < count($childNodes); $i++)
		{
			$child = $childNodes[$i];
			$tabDiv->appendChild($child->cloneNode(true));
		}
		$tabDiv->setAttribute('id', $id);
		$tabDiv->setAttribute('class', 'tab-widget');
		$parent->replaceChild($tabDiv, $node);

		$tabControls = $tabDiv->getElementsByTagNameNS(self::NS, 'tabcontrol');
		for ($i = 0; $i < 1; $i++)
		{
			$this->extendedNodeTabControl($tabControls->item($i), $id);
		}

		$tabs = $tabDiv->getElementsByTagNameNS(self::NS, 'tabs');
		for ($i = 0; $i < 1; $i++)
		{
			$this->extendedNodeTabs($tabs->item($i), $id);
		}

		// инициализация вкладок, проводится перед обработкой ошибок
		array_unshift($this->js, 'initTabWidget("'.$node->getAttribute('id').'")');

		return $tabDiv;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка тега "tabcontrol"
	 *
	 * @param DOMNode $node  Элемент
	 * @param string  $id    Идентификатор виджета вкладок
	 */
	protected function extendedNodeTabControl(DOMNode $node, $id)
	{
		$parent = $node->parentNode;

		/* Создаём замену для тега tabcontrol */
		$newNode = $this->xml->createElement('ul');
		/* Копируем в него содержимое tabcontrol */
		$childNodes = $this->childrenAsArray($node->childNodes);
		for ($i = 0; $i < count($childNodes); $i++)
		{
			$child = $childNodes[$i];
			$newNode->appendChild($child->cloneNode(true));
		}
		$parent->replaceChild($newNode, $node);

		$tabs = $this->childrenAsArray($newNode->getElementsByTagNameNS(self::NS, 'tab'));
		for ($i = 0; $i < count($tabs); $i++)
		{
			$this->extendedNodeTab($tabs[$i], $id);
		}

		return $newNode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка тега "tabs"
	 *
	 * @param DOMNode $node  Элемент
	 * @param string  $id    Идентификатор виджета вкладок
	 */
	protected function extendedNodeTabs(DOMNode $node, $id)
	{
		$parent = $node->parentNode;

		/* Создаём замену для тега tabs */
		$newNode = $this->xml->createElement('div');
		$newNode->setAttribute('class', 'tab-widget-tabs');
		/* Копируем в него содержимое tabs */
		$childNodes = $this->childrenAsArray($node->childNodes);
		for ($i = 0; $i < count($childNodes); $i++)
		{
			$child = $childNodes[$i];
			$newNode->appendChild($child->cloneNode(true));
		}
		$parent->replaceChild($newNode, $node);

		$tabs = $this->childrenAsArray($newNode->getElementsByTagNameNS(self::NS, 'tab'));
		for ($i = 0; $i < count($tabs); $i++)
		{
			$this->extendedNodeTabContent($tabs[$i], $id);
		}

		return $newNode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка тега "tab" в tabcontrol
	 *
	 * @param DOMNode $node  Элемент
	 * @param string  $id    Идентификатор виджета вкладок
	 */
	protected function extendedNodeTab(DOMNode $node, $id)
	{
		$parent = $node->parentNode;

		/* Создаём замену для тега tab */
		$newNode = $this->xml->createElement('li');
		$newNode->setAttribute('id', $id.'-btn-'.$node->getAttribute('name'));
		$a = $this->xml->createElement('a', $node->textContent);
		$a->setAttribute('href', '#'.$id.'-'.$node->getAttribute('name'));
		$newNode->appendChild($a);
		$parent->replaceChild($newNode, $node);

		return $newNode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка тега "tab" в tabs
	 *
	 * @param DOMNode $node  Элемент
	 * @param string  $id    Идентификатор виджета вкладок
	 */
	protected function extendedNodeTabContent(DOMNode $node, $id)
	{
		$parent = $node->parentNode;

		/* Создаём замену для тега tab */
		$newNode = $this->xml->createElement('div');
		$msgNode = $this->xml->createElement('div');
		$msgNode->setAttribute('class', 'tab-messages box error hidden');
		$newNode->appendChild($msgNode);
		/* Копируем в него содержимое tab */
		$childNodes = $this->childrenAsArray($node->childNodes);
		for ($i = 0; $i < count($childNodes); $i++)
		{
			$child = $childNodes[$i];
			$newNode->appendChild($child->cloneNode(true));
		}
		$newNode->setAttribute('id', $id.'-'.$node->getAttribute('name'));
		$parent->replaceChild($newNode, $node);

		return $newNode;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка тега "attach"
	 *
	 * @param DOMNode $node  Элемент
	 */
	protected function extendedNodeAttach(DOMNode $node)
	{
		$parent = $node->parentNode;
		$to = $node->getAttribute('to');

		/* Атрибут required */
		$required = $node->getAttribute('required');
		if ($required)
		{
			$this->js []= "addValidator('required', '$to', '$required');";
		}

		/* Удаляём тег */
		$parent->removeChild($node);

		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка расширенных атрибутов
	 *
	 * @param DOMNode $node  Элемент
	 * @param DOMAttr $attr  Атрибут
	 */
	protected function extendedAttr(DOMNode $node, DOMAttr $attr)
	{
		$handler = 'extendedAttr' . $attr->name;

		if (method_exists($this, $handler))
		{
			$this->$handler($node, $attr);
		}
		else
		{
            Eresus_Kernel::log(__METHOD__, LOG_WARNING, 'Unsupported EresusForm attribute "%s"', $attr->name);
		}

		$node->removeAttributeNode($attr);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка атрибута "required"
	 *
	 * @param DOMNode $node  Элемент
	 * @param DOMAttr $attr  Атрибут
	 */
	protected function extendedAttrRequired(DOMNode $node, DOMAttr $attr)
	{
		$id = $node->getAttribute('id');

		$message = $attr->value;
		$this->js []= "addValidator('required', '#$id', '$message');";

	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка атрибута "password"
	 *
	 * @param DOMNode $node  Элемент
	 * @param DOMAttr $attr  Атрибут
	 */
	protected function extendedAttrPassword(DOMNode $node, DOMAttr $attr)
	{
		$id = $node->getAttribute('id');
		$passwordId = $attr->value;
		$this->js []= "addValidator('password', '#$id', '$passwordId');";
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка атрибута "match"
	 *
	 * @param DOMNode $node  Элемент
	 * @param DOMAttr $attr  Атрибут
	 */
	protected function extendedAttrMatch(DOMNode $node, DOMAttr $attr)
	{
		$id = $node->getAttribute('id');
		$pattern = $attr->value;
		/* Для Dwoo нужно экранировать фигурные скобки. Почему-то они передаются и сюда */
		$pattern = str_replace(array('\{', '\}'), array('{', '}'), $pattern);
		$this->js []= "addValidator('regexp', '#$id', $pattern);";
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка атрибута "email"
	 *
	 * @param DOMNode $node  Элемент
	 * @param DOMAttr $attr  Атрибут
	 */
	protected function extendedAttrEmail(DOMNode $node, DOMAttr $attr)
	{
		$id = $node->getAttribute('id');
		$pattern = $attr->value;
		/* Для Dwoo нужно экранировать фигурные скобки. Почему-то они передаются и сюда */
		$pattern = str_replace(array('\{', '\}'), array('{', '}'), $pattern);
		$this->js []= "addValidator('email', '#$id');";
	}
	//-----------------------------------------------------------------------------

	/******************************************
	 *
	 * Режим PROCESS
	 *
	 ******************************************/

	/**
	 * Проверить данные формы
	 */
	protected function validate()
	{
		$this->loadXML();
		$this->id = $this->xml->firstChild->nextSibling->getAttribute('id');
		$this->detectAutoValidate();

		$this->validateInputs($this->xml->firstChild->nextSibling);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка полей ввода в ветке документа на сервере
	 *
	 * Метод рекурсивно перебирает все дочерние узлы $branch
	 *
	 * @param DOMNode $branch  Ветка документа
	 */
	protected function validateInputs(DOMNode $branch)
	{
		$list = $this->childrenAsArray($branch->childNodes);

		for ($i = 0; $i < count($list); $i++)
		{
			$node = $list[$i];

			if (in_array($node->nodeName, $this->inputs))
			{
				$this->validateInput($node);
			}
			elseif ($node->nodeName == 'fc:attach')
			{
				$this->validateAttach($node);
			}

			$this->validateInputs($node);
		}

	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка поля ввода на сервере
	 * Дает клиенту комманду проверить элементы при загрузке страницы
	 * @param DOMNode $node
	 */
	protected function validateInput($node)
	{
		$hasAttributes = $node->hasAttributes();

		if ($hasAttributes)
		{
			$name = $node->getAttribute('name');

			$attrs = $node->attributes;

			for ($i = 0; $i < $attrs->length; $i++)
			{
				$attr = $attrs->item($i);
				if ($attr->namespaceURI == self::NS)
				{
					switch ($attr->localName)
					{
						case 'required':
							$value = $this->getValue($name);
							if ($value === '' || is_null($value))
							{
								if ($node->nodeName == 'input' && $node->getAttribute('type') == 'radio')
								{
									// для радиокнопок
									// проверить (и пометить, если нужно) это поле
									$this->invalidValue('input[name='.$name.']', 'required');
								}
								else
								{
									$id = $node->getAttribute('id');
									// проверить (и пометить, если нужно) это поле
									$this->invalidValue('#'.$id, 'required');
								}
							}
						break;
					}
				}
			}
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Обработка тега fc:attach
	 *
	 * Дает клиенту комманду проверить элементы при загрузке страницы
	 *
	 * Пример использования тега:
	 * <code>
	 * <fc:attach to="input[name=catalogSections]" required="Вы должны выбрать раздел каталога" />
	 * </code>
	 *
	 * @param DOMNode $node
	 */
	protected function validateAttach($node)
	{
		$to = $node->getAttribute('to');
		$this->invalidValue($to, 'required'); // провеирть (и пометить, если нужно) это поле
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверка поля на правильность заполнения
	 *
	 * @param string $name  Имя поля
	 * @return bool  true - если поле заполнено правильно и false, если нет.
	 */
	public function isValid($name)
	{
		return ! isset($this->invalidData[$name]);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получить значение поля
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function getValue($name)
	{
		if (! isset($this->values[$name]))
		{
			$this->values[$name] = arg($name);
		}

		return $this->values[$name];
	}
	//-----------------------------------------------------------------------------

	/**
	 * Отметить неправильное значение
	 *
	 * Устанавливает флаг invalidData
	 *
	 * @param string $name         Имя значения
	 * @param string $description  Описание ошибки
	 *
	 * @see invalidData
	 */
	public function invalidValue($name, $description)
	{
		$this->invalidData[$name] = true;
		$this->addMessage($name, $description);
	}
	//-----------------------------------------------------------------------------

	/**
	 * Проверить флаг invalidData
	 *
	 * @return bool
	 * @see invalidData
	 */
	public function invalidData()
	{
		return count($this->invalidData) > 0 || $this->isInvalid;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Упаковать данные для сохранения в сессии
	 *
	 * @return array
	 */
	protected function packData()
	{
		$data = array(
			'values' => $this->values,
			'isInvalid' => $this->invalidData(),
			// @deprecated поле 'messages' устарело
			'messages' => $this->messages,
		);
		return $data;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Сохранение данных в сессии
	 */
	protected function sessionStore()
	{
		$_SESSION[get_class($this)][$this->id] = $this->packData();
	}
	//-----------------------------------------------------------------------------

	/**
	 * Добавить сообщение
	 *
	 * @param string $fieldName
	 * @param string $message
	 */
	protected function addMessage($fieldName, $message)
	{
		$this->messages[$fieldName] = $message;
	}
	//-----------------------------------------------------------------------------

	/******************************************
	 *
	 * Вспомогательные методы
	 *
	 ******************************************/

	/**
	 * Компиляция XML
	 */
	protected function loadXML()
	{
		$tmpl = new Eresus_Template($this->template);
		$html = $tmpl->compile($this->values);

		$imp = new DOMImplementation;
		$dtd = $imp->createDocumentType('html', '-//W3C//DTD XHTML 1.0 Strict//EN', null);
		$this->xml = $imp->createDocument(null, 'html', $dtd);

		$html = "<!DOCTYPE root [\n" .
			file_get_contents(dirname(__FILE__) . '/xhtml-lat1.ent') .
			file_get_contents(dirname(__FILE__) . '/xhtml-special.ent') .
			"\n]>\n" . $html;

		$this->xml->loadXML($html);
		$this->xml->encoding = 'utf-8';
		$this->xml->normalize();
	}

	/**
	 * Получить дочерние узлы в виде списка
	 * @param DOMNode $node
	 * @return array
	 */
	protected function childrenAsArray($nodeList)
	{
		$result = array();
		if (! is_object($nodeList))
		{
			return $result;
		}

		if (! ($nodeList instanceof DOMNodeList))
		{
			return $result;
		}

		for ($i = 0; $i < $nodeList->length; $i++)
		{
			$result []= $nodeList->item($i);
		}

		return $result;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Копирует атрибуты элемента и его дочерние узлы
	 *
	 * @param DOMElement $source
	 * @param DOMElement $target
	 * @return void
	 */
	protected function copyElement(DOMElement $source, DOMElement $target)
	{
		if ($source->hasAttributes())
		{
			$attributes = $source->attributes;
			if (!is_null($attributes))
			{
				foreach ($attributes as $attr)
				{
					$target->setAttribute($attr->name, $attr->value);
				}
			}
		}

		foreach ($source->childNodes as $child)
		{
			$target->appendChild($child->cloneNode(true));
		}
	}
	//-----------------------------------------------------------------------------

	/**
	 * Получение значения поля ввода
	 *
	 * @param DOMNode $node
	 * @return mixed
	 */
	protected function valueOf(DOMNode $node)
	{
		switch ($node->nodeName)
		{
			case 'input':

				switch ($node->getAttribute('type'))
				{
					case 'checkbox':
						return $node->getAttribute('checked') ? $node->getAttribute('value') : false;
						break;

					default:
						return $node->getAttribute('value');
				}

			break;
		}
		return null;
	}
	//-----------------------------------------------------------------------------

	/**
	 * Определение режима проверки ошибок на JavaScript
	 *
	 * @return void
	 */
	protected function detectAutoValidate()
	{
		$this->autoValidate = true;

		if ($this->xml->firstChild->nextSibling->hasAttributeNS(self::NS, 'validate'))
		{
			$value = $this->xml->firstChild->nextSibling->getAttributeNS(self::NS, 'validate');

			switch ($value)
			{
				case '':
				case '0':
				case 'false':
					$this->autoValidate = false;
					$this->js []= 'autoValidate = false;';
				break;
			}
		}
	}
	//-----------------------------------------------------------------------------

}
