Деинсталляция
=============

Чтобы выполнить какие-либо действия при деинсталляции плагина, необходимо переопределить метод
`Eresus_Plugin::uninstall() <../../api/classes/Eresus_Plugin.html#uninstall>`_.

Не забывайте вызывать родительский метод. Обычно родительский uninstall вызывается после собственных
действий:

.. code-block:: php

   <?php
   /**
    * Тестовый плагин
    * @package Demo
    */
   class MyPlugin extends Eresus_Plugin
   {
     ...
     /**
      * Действия при удалении плагина
      * @return void
      */
     public function uninstall()
     {
       // Необходимые действия
       parent::uninstall();
     }
     ...
   }


.. important::
   Все таблицы в БД с именами вида ``ИмяПлагина`` и ``ИмяПлагина_*`` будут удалены автоматически.
   Такое поведение реализовано в системе начиная с версии 2.10 и распространяется только на потомков
   `Eresus_Plugin <../../api/classes/Eresus_Plugin.html>`_.

Для удаления директорий данных плагина рекомендуется метод
`Eresus_Plugin::rmdir() <../../api/classes/Eresus_Plugin.html#rmdir>`_.
