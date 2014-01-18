Переход на 3.02 с 3.01
======================

Для администраторов
-------------------

Для верстальщиков
-----------------

Для разработчиков расширений
----------------------------

Удалено
^^^^^^^

* Классы HttpHeader, HttpHeaders, HttpMessage, HttpRequest, HttpResponse

Изменено
^^^^^^^^
* Система событий переделана с использованием Symfony. Интерфейс остался полностью совместимым
  (`#41 <https://github.com/Eresus/EresusCMS/issues/401>`).
* Вместо конструкции `Eresus_Kernel::app()->getEventDispatcher()` теперь рекомендуется использовать
  `Контейнер зависимостей </dev/guide/container>`.
* Класс ``HTTP`` объявлен устаревшим.
* В связи с переходом на компонент Symfony HttpFoundation:

  * ``Eresus_HTTP_Request`` унаследован от ``Symfony\Component\HttpFoundation\Request``;
  * изменилось объявление конструктора ``Eresus_HTTP_Request``;
  * передача в качестве первого аргумента в конструктор ``Eresus_HTTP_Request`` чего-либо кроме
    массива объявлена устаревшей;
  * Объект ``Eresus_HTTP_Request`` при конвертацию в строку, теперь ведёт себя иначе, см.
    документацию по ``Symfony\Components\HttpFoundation\Request``.

Обновлено
^^^^^^^^^

Добавлено
^^^^^^^^^

* :doc:`Контейнер служб </dev/guide/container>` (`#17 <https://github.com/Eresus/EresusCMS/issues/17>`)
