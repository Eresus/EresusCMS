Работа с шаблонами
==================

У плагина может быть две группы шаблонов: для административного и клиентского интерфейсов. Шаблоны
для АИ должны располагаться в папке ``templates/admin`` и могут использоваться непосредственно из неё.
Шаблоны для КИ должны располагаться в папке ``templates/client``, при установке плагина они будут
скопированы в папку CMS ``templates/имя_плагина``, что позволит изменять их через веб-интерфейс.

Метод `Eresus_Plugin::templates() <../../api/classes/Eresus_Plugin.html#method_templates>`_ возвращает
экземпляр класса `Eresus_Plugin_Templates <../../api/classes/Eresus_Plugin_Templates.html>`_,
который предоставляет методы для работы с обеими группами шаблонов.

Пример создания разметки на основе административного шаблона «foo.html» и данных из массива $vars:

.. code-block:: php

    <?php
    $html = $this->templates()->admin('foo.html')->compile($vars);

Пример чтения содержимого клиентского шаблона:

.. code-block:: php

    <?php
    $contents = $this->templates()->clientRead('foo.html');

Пример записи содержимого клиентского шаблона:

.. code-block:: php

    <?php
    $this->templates()->clientWrite('foo.html', $contents);
