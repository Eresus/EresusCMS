Встроенные классы объектов
==========================

В CMS есть ряд встроенных классов, объекты которых могут встретиться в шаблонах.

.. attention::
   Обратите внимание! Встроенные классы — не то же самое, что и :doc:`глобальные переменные<globals>`.
   Переменные каких именно классов и под какими именами они будут доступны в конкретном шаблоне,
   зависит от программиста модуля расширения.


Eresus_CMS_Page
---------------

`Документация API <../../api/classes/Eresus_CMS_Page.html>`_

Свойства:

* **title** --- полный заголовок страницы, куда, в зависимости от настроек сайта, могут входить:
  имя сайта, заголовок сайта, заголовок раздела и т. д.
* **description** --- полное описание страницы для мета-тега description. В зависимости от
  настроек сайта, в него могут входить: описание сайта и описание раздела.
* **keywords** --- полный набор ключевых слов страницы для мета-тега keywords. В зависимости от
  настроек сайта, в него могут входить: ключевые слова сайта и ключевые слова раздела.

Пример (предполагается что переменная класса Eresus_CMS_Page в шаблоне называется $page):

.. code-block:: smarty

  <head>
    <title>{$page->title|escape}</title>
    <meta name="description" content="{$page->description|escape}">
    <meta name="keywords" content="{$page->keywords|escape}">
  </head>

