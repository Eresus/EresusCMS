Ответы и перенаправления
========================

Есть два способа, которыми расширение может отправить ответ браузеру:

1. вернуть строку;
2. вернуть объект класса `Eresus_HTTP_Response <../../api/classes/Eresus_HTTP_Response.html>`_.

Оба эти способа работают только в определённых методах расширения, о которых будет рассказано дальше
в руководстве.

Возврат строки
--------------

В этом случае возвращаемое значение будет подставлено в область контента отрисовываемой в данном
запросе страницы.

.. code-block:: php

   <?php
   class MyPlugin extends ContentPlugin
   {
      ...
      public function clientRenderContent()
      {
          return 'Hello, world!';
      }

Eresus_HTTP_Response
--------------------

В этом случае возвращённое значение будет без изменений передано браузеру.

.. code-block:: php

   <?php
   class MyPlugin extends ContentPlugin
   {
      ...
      public function clientRenderContent()
      {
          return new Eresus_HTTP_Response('Hello, world!');
      }


Перенаправления
---------------

Перенаправление --- это частный случай возврата
`Eresus_HTTP_Response <../../api/classes/Eresus_HTTP_Response.html>`_, когда используется его дочерний
класс --- `Eresus_HTTP_Redirect <../../api/classes/Eresus_HTTP_Redirect.html>`_.

.. code-block:: php

   <?php
   class MyPlugin extends ContentPlugin
   {
      ...
      public function clientRenderContent()
      {
          return new Eresus_HTTP_Redirect($_SERVER['HTTP_REFERER']);
      }

