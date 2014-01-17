События
=======

Плагины могут выполнять некоторые действия при возникновении определённых событий. Для этого надо:

#. Написать метод-обработчик
#. Зарегистрировать обработчик события

Метод-обработчик
----------------

Метод-обработчик — это открытый метод, получающий в качестве единственного аргумента экземпляр
`Symfony\Component\EventDispatcher\Event` или его потомка.

Пример
^^^^^^

.. code-block:: php

   <?php
   use Symfony\Component\EventDispatcher\Event;

   class MyPlugin extends Eresus_Plugin
   {
     /**
      * Обработчик события
      */
     public function myEventHandler(Event $event)
     {
       // Необходимые действия
     }

Регистрация обработчика
-----------------------

Регистрация выполняется при помощи метода
`Symfony\Component\EventDispatcher\EventDispatcher::addListener`.
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
       $this->get('events')
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
       // Класс MyPlugin_Event_MyEvent должен быть потомком Symfony\Component\EventDispatcher\Event
       $event = new MyPlugin_Event_MyEvent();
       $this->get('events')->dispatch('myplugin.my_event', $event);
     }
