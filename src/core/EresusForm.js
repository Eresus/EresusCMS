/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Веб-форма
 *
 * @copyright 2004, Михаил Красильников <mihalych@vsepofigu.ru>
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
 * Реестр объектов EresusForm
 *
 * Хранит объекты EresusForm, доступные по id формы
 *
 * @var array
 */
window.EresusFormRegistry = new Array();


/**
 * Конструктор объекта EresusForm
 *
 * @param {String} id  Идентификатор формы
 */
function EresusForm(id)
{

	/*
	 * Все свойства и методы компонента будут встроены в форму.
	 * Для этого, проверяем, если this — не узел DOM, вызываем EresusForm как метод узла с указанным
	 * идентификатором.
	 */
	if (undefined === this.nodeName)
	{
		return EresusForm.call(document.getElementById(id), id);
	}
	else
	{
		// Сохраняем ID
		this.formId = id;
	}

	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Объявление свойств
	 */

	/**
	 * Если установлен в true, то после вызова onSubmit происходит отправка данных
	 *
	 * @var Bool
	 */
	this.updatePage = true;

	/**
	 * Переключатель JS-проверок.
	 *
	 * Если установлен в false, то JS-проверки валидаторами
	 * проводиться не будут.
	 *
	 * @var Bool
	 */
	this.autoValidate = true;

	/**
	 * Валидаторы
	 * @var Array
	 */
	this.validators = new Array();

	/**
	 * Признак наличия вкладок на форме
	 * @var Bool
	 */
	this.hasTabs = false;

	/**
	 * Массив вкладок, для которых уже выведено сообещни об ошибках
	 */
	this.tabsHasMessages = new Array();



	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Инициализация объекта
	 */

	/*
	 * Регистрируем объект
	 */
	EresusFormRegistry[id] = this;


	/* * * * * * * * * * * * * * * * * * * * * * * * * * *
	 * Объявление методов
	 */

	/**
	 * Метод отправляет данные формы черех XmlHttpRequest
	 *
	 * @returns Boolean
	 */
	this.sendData = function ()
	{
		var data = $(":input").serializeArray();
		var uri = this.action;

		if (uri === '') uri = window.location.pathname;

		$.post(uri, data);
		this.updatePage = false;
		return this.check();
	};
	//-----------------------------------------------------------------------------

	/**
	 * Проверяет корректность введенных в форму данных
	 *
	 * @return Boolean
	 */
	this.check = function ()
	{
		/*
		 * Очищаем сообщения об ошибках и снимаем выделение с
		 * неправильно заполненных полей
		 */
		this.clearMessages();
		$('#' + this.formId + ' .data-error').removeClass('data-error');

		return this.autoValidate ? this.validate(true, true) : true;
	};
	//-----------------------------------------------------------------------------

	/**
	 * Обработчик отправки формы
	 *
	 * @return bool
	 */
	this.onsubmit = function ()
	{
		return this.check() && this.updatePage;
	};
	//-----------------------------------------------------------------------------

	/**
	 * Проверка полей ввода
	 *
	 * @param {Boolean} showMessages  Показывать ли сообщения об ошибках. По умолчанию - нет.
	 *
	 * @return Boolean
	 */
	this.validate = function ()
	{
		var isValid = true;
		var showMessages = arguments.length ? arguments[0] : false;

		if (showMessages) {
			this.clearMessages();
		}

		for (var i = 0; i < this.validators.length; i++) {

			if (this.validators[i].identifier) { // выполняем проверку

				if (!this.validators[i].validate(showMessages)) {
					isValid = false;
					if (window.console)
						console.debug("EresusForm.validate: Validation failed for: %s", this.validators[i].identifier);
				}

			} else { // удаляем валидатор
				this.validators.splice(i, 1);
				i--; // чтобы скомпенсировать удаленный элемент
			}
		}

		if (showMessages) {
			this.showFormMessages();
		}

		return isValid;
	};
	//-----------------------------------------------------------------------------

	/**
	 * Добавление валидатора
	 *
	 * @param {String} type              Тип валидатора:
	 *                                    - 'required' - обязательное поле
	 *                                    - 'password' - сравнение с полем пароля
	 *                                    - 'regexp'   - сравнение с регулярным выражением
	 *                                    - 'email'    - адрес e-mail
	 *                                    - 'custom'   - Callback-функция проверки
	 */
	this.addValidator = function (type)
	{
		var validator, i;
		var identifier = type + '|' + String(arguments[1]).substr(0, 50);

		for (i = 0; i < this.validators.length; i++) {
			if (this.validators[i].identifier == identifier) {
				break;
			}
		}

		if (i >= this.validators.length) { // такого валидатора нет в массиве this.validators
			switch (type) {
				case 'required': validator = new RequiredValidator(this, arguments[1], arguments[2]); break;
				case 'password': validator = new PasswordValidator(this, arguments[1], arguments[2]); break;
				case 'regexp': validator = new RegExpValidator(this, arguments[1], arguments[2]); break;
				case 'email': validator = new EmailValidator(this, arguments[1], arguments[2]); break;
				case 'custom': validator = new CustomValidator(this, arguments[1]); break;
				default: return;
			}

			validator.identifier = identifier;

			this.validators.push(validator);
		}
	};
	//-----------------------------------------------------------------------------

	/**
	 * Удаление валидатора
	 *
	 * @param {String} type              Тип валидатора:
	 *                                    - 'required' - обязательное поле
	 *                                    - 'required4input' - обязателен выбор радиокнопки
	 *                                    - 'password' - сравнение с полем пароля
	 *                                    - 'regexp' - сравнение с регулярным выражением
	 *                                    - 'custom' - Callback-функция проверки
	 */
	this.removeValidator = function (type)
	{
		var i;

		var identifier = type + '|' + String(arguments[1]).substr(0, 50);

		for (i = 0; i < this.validators.length; i++) {
			if (this.validators[i].identifier == identifier) {
				break;
			}
		}
		if (i < this.validators.length) { // найден удаляемый валидатор
			this.validators[i].identifier = false;
			this.validators[i].destroyValidator();
		}

	};
	//-----------------------------------------------------------------------------

	/**
	 * Инициализация виджета вкладок
	 *
	 * @param {String} id  Идентификатор виджета
	 */
	this.initTabWidget = function(id)
	{
		this.hasTabs = true;
		this.tabWidget = $("#" + id);
		this.tabWidget.tabs({cookie: { expires: 30 }});
	};
	//-----------------------------------------------------------------------------

	/**
	 * Добавить сообщение формы
	 *
	 * @param {String} msg  Текст сообщения
	 */
	this.addFormMessage = function(msg)
	{
		if (msg !== '') {
			$('#' + this.formId + ' .form-messages').append('<p>' + msg + '</p>');
		}
	};
	//-----------------------------------------------------------------------------

	/**
	 * Вызывается после добавления всех сообщений.
	 * Добавляет сообщения об ошибках на вкладках и обрамляем код сообщений дивом.
	 *
	 * Выводит сообщения об ошибках на вкладках и активирует вкладку.
	 */
	this.showFormMessages = function()
	{
		if (this.hasTabs) {
			var tabMessage = '';
			var selected = true;
			var node = false;
			var name = false;
			var nodes = $('#tabs ul li a');

			for (var i = 0; i < nodes.length; i++)
			{
				node = nodes.eq(i);
				name = node.attr('href').substr(1);

				if (this.tabsHasMessages[name])
				{
					if (selected)
					{
						$('#tabs').tabs('select', name);
						selected = false;
					}
					tabMessage +=	this.tabsHasMessages[name];
				}
			}
			this.addFormMessage(tabMessage);
		}

		/*
		 * После того, как все сообщения формы и вкладок добавлены,
		 * обрамляем сообщение формы дивом. Это нужно для верстки.
		 */
		if ($('#' + this.formId + ' .form-messages').html() !== '') {

			var formMessages = $('#' + this.formId + ' .form-messages').eq(0);
			formMessages.html('<div>' + formMessages.html() + '</div>');
			window.scrollTo(formMessages.position().left, formMessages.position().top);

		}
	};
	//-----------------------------------------------------------------------------

	/**
	 * Добавить сообщение вкладки
	 *
	 * @param {String}  msg      Текст сообщения
	 * @param {String}  tabName  Имя вкладки
	 */
	this.addTabMessage = function(msg, tabName)
	{
		if (msg !== '') {
			if (this.hasTabs) {
				if (!this.tabsHasMessages[tabName]) {
					this.tabsHasMessages[tabName] = '<p>Ошибка ввода на вкладке ' + $('a[href="#' + tabName + '"]').eq(0).text() + '</p>';
				}
				$('#' + tabName + ' .tab-messages').show().append('<div>' + msg + '</div>');
			}
			else this.addFormMessage(msg);
		}
	};
	//-----------------------------------------------------------------------------

	/**
	 * Добавить сообщение вкладки
	 *
	 * @param {String}         msg   Текст сообщения
	 * @param {HtmlElement|String} Node  Узел
	 */
	this.addTabMessageByNode = function(msg, Node)
	{
		var tabName = this.getTabIdByNode(Node);
		this.addTabMessage(msg, tabName);
	};
	//-----------------------------------------------------------------------------

	/**
	 * Очистка всех сообщений
	 */
	this.clearMessages = function()
	{
		$('#' + this.formId + ' .form-messages').text('');
		$('#' + this.formId + ' .tab-messages').text('');
		this.tabsHasMessages = new Array();
	};
	//-----------------------------------------------------------------------------

	/**
	 * Получение имени вкладки по узлу внутри неё
	 *
	 * @param {HtmlElement|String} Node  Узел
	 * @return String
	 */
	this.getTabNameByNode = function(Node)
	{
		return $(Node).closest('.ui-tabs-panel').eq(0).attr('id');
	};
	//-----------------------------------------------------------------------------

	/**
	 * Получение id вкладки по узлу внутри неё
	 *
	 * //TODO эта функция аналогична getTabNameByNode
	 * и отличается только тем, что getTabNameByNode у меня не работала (ghost)
	 *
	 * @param {HtmlElement|String} Node  Узел
	 * @return String
	 */
	this.getTabIdByNode = function(Node)
	{
		var i;
		var parents = $(Node).eq(0).parents('div');
		for (i = 0; i < parents.length; i++) {
			if (parents.eq(i).attr('id') && parents.eq(i).attr('id').indexOf('tabs-') != -1) {
				break;
			}
		}
		if (i < parents.length) {
			return parents.eq(i).attr('id');
		} else {
			return false;
		}
	};
	//-----------------------------------------------------------------------------

	/**
	 * Получение элемента label по связанному с ней полю
	 *
	 * @param {jQuery|Element} Node  Узел
	 * @return jQuery
	 */
	this.getLabelByField = function(Node)
	{
		if ($(Node).attr('type') == 'radio') return this.getLabelByFieldByName(Node);
		return $('label[for=' + $(Node).attr('id') + ']', this).eq(0);
	};
	//-----------------------------------------------------------------------------

	/**
	 * Получение элемента label по связанному с ней по атрибуту name (для радиокнопок)
	 *
	 * @param {jQuery|Element} Node  Узел
	 * @return jQuery
	 */
	this.getLabelByFieldByName = function(Node)
	{
		return $('label[for=' + $(Node).attr('name') + ']', this).eq(0);
	};
	//-----------------------------------------------------------------------------

	return this;
}
//-----------------------------------------------------------------------------


/**
 * Конструктор объекта RequiredValidator
 *
 * @param {EresusForm} form      Компонент формы
 * @param {String}     selector  Селектор элемента
 * @param {String}     message   Сообщение
 *
 * @author mekras
 */
function RequiredValidator(form, selector, message)
{
	/**
	 * Компонент формы
	 * @var EresusForm
	 */
	this.form = form;

	/**
	 * jQuery-селектор
	 * @var String
	 */
	this.selector = selector;

	/**
	 * Текст сообщения
	 * @var String
	 */
	this.message = message;

	/**
	 * Радио-кнопки
	 */
	this.radio = [];

	/*
	 * Добавляем признаки обязательного поля
	 */
	var id;

	if ($(this.selector).attr('type') != 'radio' && $(this.selector).attr('type') != 'checkbox')
		id = $(this.selector).attr('id');
	else
		id = $(this.selector).eq(0).attr('name');

	if (id) {
		this.prevLabelHtml = $('#' + this.form.formId +' label[for=' + id + ']').eq(0).html();
		$('#' + this.form.formId +' label[for=' + id + ']').append('<sup>*</sup>');
	}

	/**
	 * Инициация проверки
	 *
	 * @param {Boolean} showMessages  Флаг, определяющий следует ли выводить сообщения об ошибках
	 * @return bool
	 */
	this.validate = function (showMessages)
	{
		var valid = true;
		var isRadio = null;

		/* В этот список будем собирать радио-кнопки, если встретятся */
		this.radio = [];

		/*
		 * Собираем все элементы, соответствующие селектору
		 */
		var Elements = $(this.selector);

		/* Начинаем обход элементов */
		for (var i = 0; i < Elements.length; i++) {

			/* Запомним, если это радио-кнопка */
			isRadio =
				Elements[i].nodeName.toLowerCase() == 'input' &&
				Elements.eq(i).attr('type').toLowerCase() == 'radio';

			/*
			 * Проверяем элемент
			 *
			 * Внимание! isRadio специально стоит в конце условия, чтобы элемент был проверен
			 * методом validateElement, но если это радио-кнопка, никаких действий не выполнялось.
			 */
			if (!this.validateElement(Elements[i]) && !isRadio) { /* Радио-кнопки обработаем потом */

				valid = false;
				if (showMessages) {
					this.markInvalid(Elements[i]);
				}

			}
		}

		/* Отдельно проверяем радио-кнопки */
		for (var name in this.radio) {

			if (!this.radio[name])	{
				valid = false;
				if (showMessages) {
					this.markInvalid('input[name=' + name + ']');
				}
			}

		}

		if (window.console && !valid)
			console.debug("RequiredValidator[%s]: failed!", this.identifier);

		return valid;
	};

	/**
	 * Проверка одного элемента
	 *
	 * @param {HtmlElement} Node
	 * @return Boolean
	 */
	this.validateElement = function (Node)
	{

		switch (Node.nodeName.toLowerCase()) {

			case 'input':

				switch (Node.getAttribute('type')) {

					case 'checkbox':
						return Node.checked;
					break;

					case 'radio':
						if (this.radio[Node.name] == undefined) this.radio[Node.name] = false;
						if (Node.checked) this.radio[Node.name] = true;
						return false;
					break;

					default:
						return Node.value !== '';
					break;
				}

			break;

			case 'textarea':

				return Node.value !== '';

			break;

			case 'select':

				return Node.value !== '' && Node.value !== '0';

			break;

			default:
			break;

		}

		return false;

	};

	/**
	 * Пометить элемент как неправилный
	 *
	 * @param {HtmlElement|String} Node
	 */
	this.markInvalid = function (Node)
	{
		var message = '';

		/*
		 * Устанавливаем выделение ошибочному полю.
		 * Для радио-кнопок выделяем родителя
		 */
		if ($(Node).attr('type') == 'radio') {

			$(Node).parent().addClass('data-error');

		} else 	$(Node).addClass('data-error');

		if (this.message && this.message != 'true') {

			message = this.message;

		} else {

			var labelText = this.form.getLabelByField($(Node)).text();
			message = 'Не заполнено поле ' + labelText;

		}

		var tabName = this.form.getTabNameByNode(Node);
		this.form.addTabMessage(message, tabName);

	};

	/**
	 * Выполнить действия при удалении валидатора
	 */
	this.destroyValidator = function ()
	{
		var id;
		if ($(this.selector).attr('type') != 'radio' && $(this.selector).attr('type') != 'checkbox')
			id = $(this.selector).attr('id');
		else
			id = $(this.selector).eq(0).attr('name');

		$('#' + this.form.formId +' label[for=' + id + ']').eq(0).html(this.prevLabelHtml);
	};

}
//-----------------------------------------------------------------------------

/**
 * Конструктор объекта PasswordValidator
 *
 * @param {EresusForm} form      Компонент формы
 * @param {String}     id        Идентификатор элемента формы
 * @param {String}     password  Идентификатор элемента с паролем
 *
 * @author mekras
 */
function PasswordValidator(form, id, password)
{
	/**
	 * Компонент формы
	 * @var EresusForm
	 */
	this.form = form;

	/**
	 * Идентификатор ввода подтвержедния
	 */
	this.id = id;

	/**
	 * Идентификатор ввода пароля
	 */
	this.password = password;

	/**
	 * Проверка элемента формы
	 *
	 * @param {Boolean} showMessages Флаг, определяющий следует ли выводить сообщения об ошибках
	 * @return Boolean
	 */
	this.validate = function ()
	{
		var jqNode = $('#' + this.id);
		var domConfirm = jqNode.get(0);
		var domPassword = $('#' + this.password).get(0);
		var showMessages = arguments.length ? arguments[0] : false;

		var valid = domConfirm.value == domPassword.value;

		if (showMessages) {
			if (!valid) {
				jqNode.addClass('data-error');
				this.form.addTabMessage('Пароль и подтверждение не совпадают');
			} else {
				if (jqNode.hasClass('data-error')) {
					jqNode.removeClass('data-error');
				}
			}
		}

		return valid;
	};
}
//-----------------------------------------------------------------------------


/**
 * Конструктор объекта RegExpValidator
 *
 * @param {EresusForm} form      Компонент формы
 * @param {String}     id        Идентификатор элемента
 * @param {String}     pattern   Выражение
 *
 * @author mekras
 */
function RegExpValidator(form, id, pattern)
{
	/**
	 * Компонент формы
	 * @var EresusForm
	 */
	this.form = form;

	/**
	 * Идентификатор
	 * @var String
	 */
	this.id = id;

	/**
	 * Регулярное выражение
	 * @var String
	 */
	this.pattern = pattern;

	/**
	 * Инициация проверки
	 *
	 * @param {Boolean} showMessages Флаг, определяющий следует ли выводить сообщения об ошибках
	 * @return Boolean
	 */
	this.validate = function (showMessages)
	{
		var Element = document.getElementById(this.id);

		var valid = false;

		if ($(Element).val() === '') {

			valid = true;

		} else {

			if ($(Element).val().match(this.pattern) === null) {

				valid = false;
				if (showMessages) {
					this.markInvalid(Element);
				}

			} else {
				valid = true;
			}

		}

		if (window.console && !valid)
			console.debug("RegExpValidator[%s]: failed!", this.identifier);

		return valid;
	};

	/**
	 * Пометить элемент как неправилный
	 *
	 * @param {HtmlElement|String} Node
	 */
	this.markInvalid = function (Node)
	{
		/*
		 * Устанавливаем выделение ошибочному полю.
		 */
		$(Node).addClass('data-error');

		var labelText = this.form.getLabelByField($(Node)).text();
		var message = 'Неправильно заполнено поле ' + labelText;

		var tabName = this.form.getTabNameByNode(Node);
		this.form.addTabMessage(message, tabName);

	};
}
//-----------------------------------------------------------------------------



/**
 * Конструктор объекта EmailValidator
 *
 * @param {EresusForm} form      Компонент формы
 * @param {String}     id        Идентификатор элемента
 * @param {String}     message   Сообщение
 *
 * @author mekras
 */
function EmailValidator(form, id, message)
{
	/**
	 * Компонент формы
	 * @var EresusForm
	 */
	this.form = form;

	/**
	 * Идентификатор
	 * @var String
	 */
	this.id = id;

	/**
	 * Сообщение
	 * @var String
	 */
	this.message = message;

	/**
	 * Шаблон проверки
	 * @var RegExp
	 */
	this.pattern = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

	/**
	 * Инициация проверки
	 *
	 * @param {Boolean} showMessages Флаг, определяющий следует ли выводить сообщения об ошибках
	 * @return Boolean
	 */
	this.validate = function (showMessages)
	{
		var Element = document.getElementById(this.id);

		var valid = false;

		if ($(Element).val() === '') {

			valid = true;

		} else {

			if ($(Element).val().match(this.pattern) === null) {

				valid = false;
				if (showMessages) {
					this.markInvalid(Element);
				}

			} else {
				valid = true;
			}

		}

		if (window.console && !valid)
			console.debug("EmailValidator[%s]: failed!", this.identifier);

		return valid;
	};

	/**
	 * Пометить элемент как неправилный
	 *
	 * @param {HtmlElement|String} Node
	 */
	this.markInvalid = function (Node)
	{
		/*
		 * Устанавливаем выделение ошибочному полю.
		 */
		$(Node).addClass('data-error');

		var labelText = this.form.getLabelByField($(Node)).text();
		var message = 'Неправильно указан адрес e-mail в поле ' + labelText;

		var tabName = this.form.getTabNameByNode(Node);
		this.form.addTabMessage(message, tabName);

	};
}
//-----------------------------------------------------------------------------



/**
 * Конструктор объекта CustomValidator
 *
 * Позволяет установить собственную функцию для проверки правильности
 * заполнения форм. Функция должна возвращать true если форма заполнена
 * правильно и false в противном случае.
 *
 * @param {EresusForm} form      Компонент формы
 * @param {Function}   callback  Callback-функция проверки
 *
 * @author mekras
 */
function CustomValidator(form, callback)
{
	this.form = form;
	this.validate = callback;
}
//-----------------------------------------------------------------------------
