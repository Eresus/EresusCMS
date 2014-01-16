Расширения Dwoo
===============

wysiwyg
-------

::

  wysiwyg( $name [, $value = '' [, $height = '200px' ]] )

Вставляет визуальный редактор.

* name --- имя поля
* value --- текст для подстановки в поле
* height --- высота редактора в единицах CSS

Пример:

.. code-block:: smarty

   {wysiwyg('text', $object->text, '300px')}
