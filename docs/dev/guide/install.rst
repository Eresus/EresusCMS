Инсталляция
===========

Чтобы выполнить какие-либо действия при инсталляции плагина, необходимо переопределить метод
`Eresus_Plugin::install() <../../api/classes/Eresus_Plugin.html#install>`_.

Необходимо не забывать вызывать родительский метод. При этом обычно родительский install вызывается
до собственных действий:

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
      * Действия при установке плагина
      * @return void
      */
     public function install()
     {
       parent::install();

       // Необходимые действия
     }
     ...
   }


Для создания директорий данных плагина рекомендуется метод
`Eresus_Plugin::mkdir() <../../api/classes/Eresus_Plugin.html#mkdir>`_.

Примеры
-------

Создание в БД таблиц ``myplugin_brands`` и ``myplugin_models``.

.. code-block:: php

   <?php
   public function install()
   {
     parent::install();

     $sql = "
       `id` int(10) unsigned NOT NULL,
       `title` varchar(255) default NULL,
       `position` int(10) unsigned NOT NULL default '0',
       PRIMARY KEY  (`id`),
       KEY `position` (`position`)";

     $this->dbCreateTable($sql, 'brands');

     $sql = "
       `id` int(10) unsigned NOT NULL,
       `brand` int(10) unsigned NOT NULL default '0',
       `title` varchar(255) default NULL,
       `position` int(10) unsigned NOT NULL default '0',
       `about` varchar(255) default NULL,
       PRIMARY KEY  (`id`),
       KEY `brand` (`brand`),
       KEY `position` (`position`)";

     $this->dbCreateTable($sql, 'models');

   }

.. hint::
   Вы можете воспользоваться модулем `ORM <http://docs.eresus.ru/cms-plugins/orm/index>`_, который предоставляет более современные средства работы с БД.
