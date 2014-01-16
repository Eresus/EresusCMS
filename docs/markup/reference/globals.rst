Глобальные переменные
=====================

Ряд переменных доступен во всех шаблонах Dwoo.

$cms
----

Экземпляр класса `Eresus_CMS <../../api/classes/Eresus_CMS.html>`_.

$Eresus
-------

Экземпляр класса `Eresus <../../api/classes/Eresus.html>`_.

$page
-----

Экземпляр класса `TClientUI <../../api/classes/TClientUI.html>`_ описывающий отрисовываемую в данный
момент страницу.

Пример использования:

.. code-block:: smarty

   <head>
       ...
       <!-- Вывод заголовка раздела -->
       <title>{$page->title}</title>
       <!-- Вывод ключевых слов раздела -->
       <meta name="keywords" content="{$page->keywords}">
       <!-- Вывод описания сайта -->
       <meta name="description" content="{$page->description}">
   </head>
   <body>
       ...
       <!-- Вывод контента страницы -->
       {$page->content}
       ...
   </body>
   </html>

$site
-----

Экземпляр класса `Eresus_Site <../../api/classes/Eresus_Site.html>`_.
