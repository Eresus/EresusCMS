Справочник событий
==================

Если в описании события не указано иное, то обработчикам этого события передаётся экземпляр
`Symfony\Component\EventDispatcher\Event`.

cms.admin.start
---------------

Генерируется в АИ сразу после начала его работы.

cms.client.render_content
-------------------------

Генерируется в КИ после отрисовки контента. Обработчику передаётся экземпляр
`Eresus\Events\RenderEvent <../../api/classes/Eresus.Events.RenderEvent.html>`_.

cms.client.render_page
----------------------

Генерируется в КИ после подстановки контента в шаблон страницы. Обработчику передаётся  экземпляр
`Eresus\Events\RenderEvent <../../api/classes/Eresus.Events.RenderEvent.html>`_.

cms.client.response
-------------------

Генерируется в КИ непосредственно перед отправкой страницы браузеру. Обработчику передаётся
экземпляр `Eresus\Events\ResponseEvent <../../api/classes/Eresus.Events.ResponseEvent.html>`_.

cms.client.start
----------------

Генерируется в КИ сразу после начала его работы.

cms.client.url_section_found
----------------------------

Генерируется в КИ при разборе URL запроса.

Событие генерируется для каждой виртуальной директории в адресе, которая соответствует разделу
сайта. Обработчику передаётся экземпляр
`Eresus\Events\UrlSectionFoundEvent <../../api/classes/Eresus.Events.UrlSectionFoundEvent.html>`_.

