События
=======

Плагины могут выполнять некоторые действия при возникновении определённых событий. Для этого надо:

#. Написать метод-обработчик
#. Зарегистрировать обработчик события

Метод-обработчик
----------------

Метод-обработчик — это открытый метод, получающий в качестве единственного аргумента экземпляр
`Eresus_Event <../../api/classes/Eresus_Event.html>`_ или его потомка.

Пример
^^^^^^

.. code-block:: php

   <?php
   class MyPlugin extends Eresus_Plugin
   {
     /**
      * Обработчик события
      */
     public function myEventHandler(Eresus_Event $event)
     {
       // Необходимые действия
     }

Регистрация обработчика
-----------------------

Регистрация выполняется при помощи метода
`Eresus_Event_Dispatcher::addListener() <../../api/classes/Eresus_Event_Dispatcher.html#method_addListener>`_.
Обычно метод вызывается в конструкторе, но он может также вызываться и в любом другом месте.

Пример
^^^^^^

.. code-block:: php

   <?php
   class MyPlugin extends Eresus_Plugin
   {
     /**
      * Конструктор
      */
     public function __construct()
     {
       parent::__construct();
       Eresus_Kernel::app()->getEventDispatcher()
           ->addListener('event.name', array($this, 'MyEventHandler'));
     }

События CMS
-----------

Со списком событий, генерируемых CMS можно ознакомиться в :doc:`приложении <../appendix/events>`.

Собственные события
-------------------

Разработчики расширений могут генерировать свои собственные события. Имена событий могут быть
любыми, при условии, что они не начинаются с «cms.» или «eresus.».

Пример
^^^^^^

.. code-block:: php

   <?php
   class MyPlugin extends Eresus_Plugin
   {
     private function someMethod()
     {
       $event = new MyPlugin_Event_MyEvent(); // Класс должен быть потомком Eresus_Event
       Eresus_Kernel::app()->getEventDispatcher()->dispatch('myplugin.my_event', $event);
     }
