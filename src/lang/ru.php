<?php
define('errFileMove', 'Не удается переместить файл "%s" в "%s"');
define('errTemplateNotFound', 'Шаблон "%s" не найден');
define('errUploadSizeINI', 'Размер файла "%s" превышает максимально допустимый размер '.ini_get('upload_max_filesize').'.');
define('errUploadSizeFORM', 'Размер файла "%s" превышает максимально допустимый размер указанный в форме.');
define('errUploadPartial', 'Файл "%s" получен только частично.');
define('errUploadNoFile', 'Файл "%s" не был загружен.');
define('errInvalidPassword', 'Неверное имя пользователя или пароль');
define('errAccountNotActive', errInvalidPassword);
define('errAccountNotExists', errInvalidPassword);
define('errTooEarlyRelogin',"Перед попыткой повторного логина должно пройти не менее %s секунд!");
define('errFormUnknownType', 'Неизвестный тип поля "%s" в форме "%s"');
define('errFormFieldHasNoName', 'Не указано имя для поля типа "%s" в форме "%s"');
define('errFormHasNoName', 'Не указано имя формы');
define('errFormPatternError', 'Введенное значение в поле "%s" не соответствует требуемому формату "%s"');
define('errFormBadConfirm', 'Пароль и подтверждение не совпадают!');
define('errNonexistedDomain', 'Несуществующий домен: "%s"');
define('errContentType', 'Неверный тип контента "%s"');
define('errItemWithSameName', 'Элемент с именем "%s" уже существует.');
define('strOk', 'OK');
define('strApply', 'Применить');
define('strCancel', 'Отменить');
define('strReset', 'Вернуть');
define('strAdd', 'Добавить');
define('strEdit', 'Изменить');
define('strDelete', 'Удалить');
define('strMove', 'Переместить');
define('strReturn', 'Вернуться');
define('strProperties', 'Свойства');
define('strYes', 'Да');
define('strNo', 'Нет');
define('strNotification', "Оповещение");
define('strNotifyTemplate', "%s (%s)<br />Раздел: <a href=\"%s\">%s</a><br /><hr>%s<hr>");
define('strPages', 'Страницы: ');
define('strPrevPage', 'Предыдущая страница');
define('strNextPage', 'Следующая страница');
define('strFirstPage', 'Первая страница');
define('strLastPage', 'Последняя страница');
define('strMainMenu', 'Навигация');
define('strAuthorisation', 'Авторизация');
define('strLogin', 'Логин');
define('strRegistration', 'Регистрация');
define('strRemind', 'Напомнить');
define('strPassword', 'Пароль');
define('strAutoLogin', 'Запомнить');
define('strEnterSite', 'Войти');
define('strLastVisit', 'Последний визит');
define('strURL', 'Адрес');
define('strViewTopic', 'Показать полностью');
define('strInformation', 'Информация');
define('admTDiv', ' - ');
define('admAdd', 'Добавить');
define('admAdded', 'Добавлено');
define('admDelete', 'Удалить');
define('admDeleted', 'Удалено');
define('admEdit', 'Изменить');
define('admActivate', 'Включить');
define('admActivated', 'Активировано');
define('admDeactivate', 'Отключить');
define('admDeactivated', 'Деактивировано');
define('admSortPosition', 'По порядку');
define('admSortAscending', 'По возрастанию');
define('admSortDescending', 'По убыванию');
define('admUp', 'Вверх');
define('admDown', 'Вниз');
define('admUpdated', 'Изменено');
define('admNA', '(не задано)');
define('admPlugin', 'Плагин');
define('admPlugins', 'Модули расширения');
define('admSettings', 'Настройки');
define('admControls', 'Управление');
define('admConfiguration', 'Конфигурация');
define('admStructure', 'Разделы сайта');
define('admUsers', 'Пользователи');
define('admThemes', 'Оформление');
define('admExtensions', 'Расширения');
define('admLanguages', 'Языки');
define('admFileManager', 'Файловый менеджер');
define('admDescription', 'Описание');
define('admVersion', 'Версия');
define('admType', 'Тип');
define('admAccessLevel', 'Уровень доступа');
define('admPosition', 'Позиция');
define('admChanges', 'Изменения');
define('admStructureHint', 'Управление структурой сайта, меню и страницами');
define('admPluginsHint', 'Управление модулями расширения');
define('admUsersHint', 'Управление пользователями');
define('admThemesHint', 'Управление шаблонами страниц и стилями');
define('admConfigurationHint', 'Конфигурация сайта');
define('admLanguagesHint', 'Управление языковыми параметрами');
define('admFileManagerHint', 'Управление файлами');
define('admSettingsMain', 'Основное');
define('admSettingsMail', 'Почта');
define('admSettingsFiles', 'Файлы');
define('admSettingsOther', 'Прочее');
define('admConfigMailSettings', 'Настройки почты');
define('admConfigNotifications', 'Оповещения');
define('admConfigPostInformation', 'Информация о постах');
define('admConfigSecurity', 'Безопасность');
define('admConfigSiteName', 'Название сайта');
define('admConfigSiteTitle', 'Заголовок сайта');
define('admConfigTitleReverse', 'Выводить в обратном порядке');
define('admConfigTitleDivider', 'Разделитель');
define('admConfigSiteKeywords', 'Ключевые слова');
define('admConfigSiteDescription', 'Описание сайта');
define('admConfigMailFromAddr', 'Адрес отправителя');
define('admConfigMailFromName', 'Имя отправителя');
define('admConfigMailFromOrg', 'Организация отправителя');
define('admConfigMailReplyTo', 'Обратный адрес');
define('admConfigMailCharset', 'Кодировка письма');
define('admConfigMailSign', 'Подпись под письмом');
define('admConfigSendNotifyTo', 'Оповещать');
define('admConfigPostsShowInfo', 'Показывать информацию о постах');
define('admConfigPostsDateFormat', 'Формат даты');
define('admConfigPostsShowIP', 'Показывать IP-адреса авторов (администраторам и редакторам)');
define('admConfigPostsResolveIP', 'Определять имена хостов');
define('admConfigAccessPolicy', 'Для неавторизованных');
define('admConfigSiteNameHint', 'Короткое название сайта. Будет доступно через макрос $(siteName)');
define('admConfigSiteTitleHint', 'Заголовок страниц. Будет доступен через макрос $(siteTitle)');
define('admConfigTitleDividerHint', 'Разделитель элементов заголовка сайта');
define('admConfigKeywordsHint', 'Список глобальных ключевых слов. Будет доступен через макрос $(siteKeywords)');
define('admConfigDescriptionHint', 'Описание сайта (META-тэг). Будет доступно через макрос $(siteDescription)');
define('admConfigMailFromAddrHint', 'От: имя <АДРЕС> (оргнизация)');
define('admConfigMailFromNameHint', 'От: ИМЯ <адрес> (оргнизация)');
define('admConfigMailFromOrgHint', 'От: имя <адрес> (ОРГАНИЗАЦИЯ)');
define('admConfigSendNotifyToHint', 'Адреса для рассылки административных сообщений');
define('admConfigFiles', 'Файловые операции');
define('admConfigFilesOwnerSetOnUpload', 'Устанавливать владельца загружаемых файлов (только для суперпользователя)');
define('admConfigFilesOwnerDefault', 'Владелец');
define('admConfigFilesModeSetOnUpload', 'Устанавливать атрибуты на загружаемые файлы');
define('admConfigFilesModeDefault', 'Атрибуты');
define('admConfigTranslitNames', 'Транслитерировать имена загружаемых файлов');
define('admConfigStructure', 'Структура сайта');
define('admConfigDefaultContentType', 'Тип контента по умолчанию');
define('admConfigDefaultPageTamplate', 'Шаблон страницы по умолчанию');
define('admConfigClientPagesAtOnce', 'Показывать');
define('admConfigClientPagesAtOnceComment', 'элементов в переключателе страниц');
define('admThemesTabWidth', '180px');
define('admThemesTemplate', 'Шаблон');
define('admThemesTemplates', 'Шаблоны страниц');
define('admThemesFilenameLabel', 'Имя файла');
define('admThemesDescriptionLabel', 'Описание');
define('admThemesStyles', 'Файлы стилей');
define('admThemesStyleLabel', 'Редактирование файла стилей');
define('admThemesStandard', 'Стандартные шаблоны');
define('admPluginsAdd', 'Добавить плагин');
define('admPluginsTableHint', "Типы: <strong>user</strong> - работает с фронт-эндом, <strong>admin</strong> - работает с бэк-эндом, <strong>content</strong> - плагин контента, <strong>ondemand</strong> - загружается только при необходимости");
define('admPluginsFound', 'Найденные плагины');
define('admPluginsInvalidFile', 'Файл не является модулем расширения');
define('admPluginsAdded', 'Подключен новый плагин');
define('admPluginTopicTable', 'Таблица топиков');
define('admUsersName', 'Имя');
define('admUsersLogin', 'Логин');
define('admUsersAccountState', 'Учетная запись активна');
define('admUsersAccessLevelShort', 'Дост.');
define('admUsersLoginErrors', 'Ошибок входа');
define('admUsersLoginErrorsShort', 'Ошиб.');
define('admUsersMail', 'e-mail');
define('admUsersLastVisit', 'Последний визит');
define('admUsersLastVisitShort', 'Последний визит');
define('admUsersPassword', 'Пароль');
define('admUsersConfirmation', 'Подтверждение');
define('admUsersChangeUser', 'Изменить учетную запись');
define('admUsersChangePassword', 'Изменить пароль');
define('admUsersPasswordChanged', 'Изменен пароль');
define('admUsersNameInvalid', 'Псевдоним пользователя не может быть пустым.');
define('admUsersLoginInvalid', 'Логин не может быть пустым и должен состоять только из букв a-z, цифр и символа подчеркивания.');
define('admUsersLoginExists', 'Пользователь с таким логином уже существует.');
define('admUsersMailInvalid', 'Неверно указан почтовый адрес.');
define('admUsersConfirmInvalid', 'Пароль и подтверждение не совпадают.');
define('admUsersCreate', 'Создать пользователя');
define('admUsersAdded', 'Добавлена учетная запись');
define('admPagesMove', 'Переместить ветку');
define('admPagesRoot', 'КОРЕНЬ');
define('admPagesContentDefault', 'По умолчанию');
define('admPagesContentList', 'Список подразделов');
define('admPagesContentURL', 'URL');
define('admPagesThisURL', 'URL этой страницы');
define('admPagesID', 'ID страницы');
define('admPagesName', 'Имя страницы');
define('admPagesNameInvalid', 'Имя страницы не может быть пустым и может состоять из латинских букв, цифр и символа подчеркивания.');
define('admPagesTitle', 'Заголовок страницы');
define('admPagesTitleInvalid', 'Заголовок страницы не может быть пустым');
define('admPagesCaption', 'Название пункта меню');
define('admPagesCaptionInvalid', 'Пункт меню не может быть пустым');
define('admPagesDescription', 'Описание');
define('admPagesKeywords', 'Ключевые слова');
define('admPagesHint', 'Подсказка');
define('admPagesContentType', 'Тип страницы');
define('admPagesTemplate', 'Шаблон');
define('admPagesActive', 'Активна');
define('admPagesVisible', 'Видимая');
define('admPagesCreated', 'Дата создания');
define('admPagesUpdated', 'Дата обновления');
define('admPagesUpdatedAuto', 'Обновить дату изменения автоматически');
define('admPagesOptions', 'Дополнительные опции');
define('admPagesContent', 'Контент страницы');
define('admTemplList', 'Шаблон элемента списка разделов');
define('admTemplListLabel', 'Шаблон списка разделов. Используйте макрос $(items) для вставки списка. Для изменения оформления элементов списка создайте или измените шаблон <a href="'.httpRoot.'admin.php?mod=themes&section=std">'.admTemplList.'</a>');
define('admTemplListItemLabel', 'Шаблон элемента списка разделов. Макросы <strong>$(title)</strong> - заголовок; <strong>$(caption)</strong> - пункт меню; <strong>$(description)</strong> - описание; <strong>$(hint)</strong> - подсказка; <strong>$(link)</strong> - ссылка.');

$GLOBALS['translit_table'] = array(
	'а'=> 'a', 'б'=> 'b', 'в'=> 'v', 'г'=> 'g', 'д'=> 'd', 'е'=> 'e', 'ё'=> 'yo', 'ж'=> 'zh', 'з'=> 'z', 'и'=> 'i', 'й'=> 'y', 'к'=> 'k', 'л'=> 'l', 'м'=> 'm', 'н'=> 'n', 'о'=> 'o', 'п'=> 'p', 'р'=> 'r', 'с'=> 's', 'т'=> 't', 'у'=> 'u', 'ф'=> 'f', 'х'=> 'h', 'ц'=> 'tc', 'ч'=> 'ch', 'ш'=> 'sh', 'щ'=> 'sch', 'ь'=> '', 'ы'=> 'y', 'ъ'=> '', 'э'=> 'e', 'ю'=> 'yu', 'я'=> 'ya',
	'А'=> 'a', 'Б'=> 'b', 'В'=> 'v', 'Г'=> 'g', 'Д'=> 'd', 'Е'=> 'e', 'Ё'=> 'yo', 'Ж'=> 'zh', 'З'=> 'z', 'И'=> 'i', 'Й'=> 'y', 'К'=> 'k', 'Л'=> 'l', 'М'=> 'm', 'Н'=> 'n', 'О'=> 'o', 'П'=> 'p', 'Р'=> 'r', 'С'=> 's', 'Т'=> 't', 'У'=> 'u', 'Ф'=> 'f', 'Х'=> 'h', 'Ц'=> 'tc', 'Ч'=> 'ch', 'Ш'=> 'sh', 'Щ'=> 'sch', 'Ь'=> '', 'Ы'=> 'y', 'Ъ'=> '', 'Э'=> 'e', 'Ю'=> 'yu', 'Я'=> 'ya'
);