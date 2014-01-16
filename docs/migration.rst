Переход на 3.01 с 3.00
======================

Для администраторов
-------------------

* Из основного файла настроек удалён блок управления обратной совместимостью.

Для верстальщиков
-----------------

* В :doc:`шаблонах страниц </markup/page-templates>` теперь можно использовать Dwoo.
* Библиотека `jQuery <http://jquery.com/>`_ обновлена до 1.10.2.
* Библиотека `jQuery UI <http://jqueryui.com/>`_ обновлена до 1.11.2.
* Библиотека `Webshim <https://github.com/aFarkas/webshim/>`_ обновлена до 1.10.10.
* Библиотека Webshim в АИ теперь доступна всегда (т. е. её не надо больше подключать самостоятельно).
* Библиотека `Botobor <https://github.com/mekras/botobor>`_ до обновлена 0.4.0.
* Конструкция «$(const:XXX)» объявлена устаревшей.
* Конструкция «$(var:XXX)» объявлена устаревшей.

Для разработчиков расширений
----------------------------

Удалено
^^^^^^^

* Удалён функционал автозагрузки классов на основе файлов *.autoload.php.
* Удалён класс TPlugin, его свойства и методы перенесены в
  `TContentPlugin <api/classes/TContentPlugin.html>`_.
* Удалены константы filesRoot и dataFiles.
* Удалено свойство Eresus::$host.
* Удалены методы DB: setTestInstance, getInstance, createSelectQuery, createUpdateQuery,
  createInsertQuery, createDeleteQuery.
* Удалён псевдоним ``Accounts`` класса `EresusAccounts <api/classes/EresusAccounts.html>`_.
* Удалены классы PHP и System, используйте одноимённые методы класса
  `Eresus_Kernel <api/classes/Eresus_Kernel.html>`_.
* Удалён класс FS, используйте методы класса `Eresus_FS_Tool <api/classes/Eresus_FS_Tool.html>`_.
* Удалены классы EresusFsFileNotExistsException, EresusFsPathNotExistsException,
  EresusFsRuntimeException, EresusPropertyNotExistsException
* Удалена функция form.
* Удалена функция useLib.
* Удалены функции fileread и filewrite.

Изменено
^^^^^^^^

* Полностью переделана система событий (совместимость со старым механизмом сохранена).
  Подробности в статье :doc:`События <dev/guide/events>`.
* Класс Plugin переименован в `Eresus_Plugin <api/classes/Eresus_Plugin.html>`_. Для обратной
  совместимости имя Plugin оставлено как псевдоним к Eresus_Plugin.
* Класс Plugins переименован в `Eresus_Plugin_Registry <api/classes/Eresus_Plugin_Registry.html>`_.
  Для обратной совместимости имя Plugins оставлено как псевдоним к Eresus_Plugin_Registry.
* Класс Template переименован в `Eresus_Template <api/classes/Eresus_Template.html>`_. Для обратной
  совместимости имя Template оставлено как псевдоним к Eresus_Template.
* Класс HttpRequest переименован в `Eresus_HTTP_Request <api/classes/Eresus_HTTP_Request.html>`_. Для
  обратной совместимости имя HttpRequest оставлено как псевдоним к Eresus_HTTP_Request.
* Библиотека Webshim в АИ теперь доступна всегда (т. е. её не надо больше подключать самостоятельно).
* Свойство ``TClientUI::$template`` сделано приватным. Для чтения его значения используйте
  `TClientUI::getTemplateName <api/classes/TClientUI.html#method_getTemplateName>`_
* Класс `WebPage <api/classes/WebPage.html>`_ унаследован от
  `Eresus_CMS_Page <api/classes/Eresus_CMS_Page.html>`_ и сделан абстрактным.
* Класс `TContentPlugin <api/classes/TContentPlugin.html>`_ унаследован от
  `Eresus_Plugin <api/classes/Eresus_Plugin.html>`_
* Функция ``eresus_log`` объявлена устаревшей, вместо неё следует использовать
  `Eresus_Kernel::log <api/classes/Eresus_Kernel.html#method_log>`_.
* Свойство ``Eresus_Plugin::$name`` объявлено устаревшим, вместо него следует использовать метод
  `Eresus_Plugin::getName <api/classes/Eresus_Plugin.html#method_getName>`_.
* Метод ``WebPage::pageSelector()`` объявлен устаревшим.
* Свойство Eresus::$plugins объявлено устаревшим. Вместо него следует использовать
  `Eresus_Plugin_Registry::getInstance <api/classes/Eresus_Plugin_Registry.html#method_getInstance>`_.
* Функция ErrorMessage объявлена устаревшей, вместо нее следует использовать
  `Eresus_CMS_Page::addErrorMessage <api/classes/Eresus_CMS_Page.html#method_addErrorMessage>`_.
* Следующие классы объявлены устаревшими: `TContentPlugin <api/classes/TContentPlugin.html>`_,
  Core, EresusFsRuntimeException, EresusPropertyNotExistsException, EresusRuntimeException,
  EresusTypeException
* Следующие функции объявлены устаревшими: InfoMessage, ErrorBox, InfoBox, useLib.

.. note::
   Полный список устаревших элементов можно найти `здесь <api/deprecated.html>`_.


Обновлено
^^^^^^^^^

* `jQuery <http://jquery.com/>`_ до 1.10.2
* `jquery-cookie <https://github.com/carhartl/jquery-cookie>`_ до 1.4.0
* `jQuery UI <http://jqueryui.com/>`_ до 1.10.3
* `Webshim <https://github.com/aFarkas/webshim/>`_ до 1.11.2
* `Botobor <https://github.com/mekras/botobor>`_ до 0.4.0

Добавлено
^^^^^^^^^

* :doc:`Новый механизм работы с шаблонами </dev/guide/templates>`
* `Eresus_Content_Helper_Replace <api/classes/Eresus_Content_Helper_Replace.html>`_
* `Eresus_HTTP_Response <api/classes/Eresus_HTTP_Response.html>`_
* `Eresus_HTTP_Redirect <api/classes/Eresus_HTTP_Redirect.html>`_
* `Eresus_Kernel::log <api/classes/Eresus_Kernel.html#method_log>`_
* `Eresus_CMS_Exception <api/classes/Eresus_CMS_Exception.html>`_
* `Eresus_CMS_Exception_NotFound <api/classes/Eresus_CMS_Exception_NotFound.html>`_
* `Eresus_DB_Exception <api/classes/Eresus_DB_Exception.html>`_
* `Eresus_Plugin_Component <api/classes/Eresus_Plugin_Component.html>`_
* `Eresus_Plugin_Controller_Abstract <api/classes/Eresus_Plugin_Controller_Abstract.html>`_
* `Eresus_Plugin_Controller_Admin <api/classes/Eresus_Plugin_Controller_Admin.html>`_
* `Eresus_Plugin_Controller_Admin_Content <api/classes/Eresus_Plugin_Controller_Admin_Content.html>`_
* `Eresus_Plugin_Controller_Client <api/classes/Eresus_Plugin_Controller_Client.html>`_
* `Eresus_Plugin_Controller_Client_Content <api/classes/Eresus_Plugin_Controller_Client_Content.html>`_
* `Eresus_Template_Service <api/classes/Eresus_Template_Service.html>`_
  `Eresus_Client_Controller_Content_Interface <api/classes/Eresus_Client_Controller_Content_Interface.html>`_
  `Eresus_Admin_Controller_Content_Interface <api/classes/Eresus_Admin_Controller_Content_Interface.html>`_
* Метод `Eresus_Plugin::getCodeDir <api/classes/Eresus_Plugin.html#method_getCodeDir>`_
* Метод `Eresus_Plugin::getDataDir <api/classes/Eresus_Plugin.html#method_getDataDir>`_
* Метод `Eresus_Plugin::getStyleDir <api/classes/Eresus_Plugin.html#method_getStyleDir>`_
* Метод `TClientUI::getTemplateName <api/classes/TClientUI.html#method_getTemplateName>`_
* Метод `TClientUI::setTemplate <api/classes/TClientUI.html#method_setTemplate>`_
* Метод `Template::loadFromFile <api/classes/Template.html#method_loadFromFile>`_
* Метод `Template::getSource <api/classes/Template.html#method_getSource>`_
* Метод `Template::setSource <api/classes/Template.html#method_setSource>`_
* Метод `Templates::load <api/classes/Templates.html#method_load>`_
