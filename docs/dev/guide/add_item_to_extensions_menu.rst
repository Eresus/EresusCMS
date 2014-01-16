Добавление пункта в меню "Расширения"
=====================================

Для добавления собственного пункта меню "Расширения" в АИ, надо выполнить три действия:

**1. Установить обработчик события cms.admin.start**

.. code-block:: php

   <?php
   /**
    * Конструктор
    */
   public function __construct()
   {
       parent::__construct();
       Eresus_Kernel::app()->getEventDispatcher()
           ->addEventListener('cms.admin.start', array($this, 'addMenuItem'));
   }


**2. Написать обработчик**

.. code-block:: php

   <?php
   /**
    * Добавляет пункт "Мой плагин" меню "Расширения"
    *
    * @return void
    */
   public function addMenuItem()
   {
     Eresus_Kernel::app()->getPage()->addMenuItem(admExtensions, array(
       'access'  => EDITOR,
       'link'  => $this->getName(),
       'caption'  => $this->title,
       'hint'  => $this->description
     ));
   }


**3. Определить метод adminRender**

.. code-block:: php

   <?php
   /**
    * Вывод интерфейса
    *
    * @return string  HTML
    */
   public function adminRender()
   {
     return '...';
   }
