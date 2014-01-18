Контейнер служб
===============

Контейнер служб предоставляет единую точку доступа к различным компонентам и службам приложения.
Контейнер реализован на основе компонента
`Symfony Dependency Injection <http://symfony.com/doc/current/components/dependency_injection/introduction.html>`_.

Доступ к контейнеру можно получить в основном классе плагина и в его контроллерах, через метод
``get``:

.. code-block:: php

    <?php
    class MyPlugin extends Plugin
    {
        ...
        public function foo()
        {
            ...
            $plugins = $this->get('plugins');


Список служб
------------

Список служб приведён в :doc:`приложении </dev/appendix/container>`.
