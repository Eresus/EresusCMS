Переход на 3.01 с 3.00
======================

Удалено
-------

* Псевдоним ``Accounts`` класса ``EresusAccounts``.

Изменено
--------

* Класс Plugin переименован в Eresus_Plugin. Для обратной совместимости имя Plugin оставлено как
псевдоним к Eresus_Plugin.

Eresus_Plugin
^^^^^^^^^^^^^

* Свойство ``name`` объявлено устаревшим, вместо него следует использовать метод
`Eresus_Plugin::getName <../../api/classes/Eresus_Plugin.html#method_getName>`_.

TClientUI
^^^^^^^^^

* Свойство ``template`` сделано приватным. Для чтения его значения используйте
  `TClientUI::getTemplateName <../../api/classes/TClientUI.html#method_getTemplateName>`_

Обновлены
^^^^^^^^^

* `Botobor <https://github.com/mekras/botobor>`_ до 0.4.0

Добавлено
---------

* `Eresus_Template_Service <../../api/classes/Eresus_Template_Service.html`_

TClientUI
^^^^^^^^^

* Метод `getTemplateName <../../api/classes/TClientUI.html#method_getTemplateName>`_
* Метод `setTemplate <../../api/classes/TClientUI.html#method_setTemplate>`_

Template
^^^^^^^^

* Метод `setContents <../../api/classes/Template.html#method_setContents>`_

Templates
^^^^^^^^

* Метод `load <../../api/classes/Templates.html#method_load>`_
