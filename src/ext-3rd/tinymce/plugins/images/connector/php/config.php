<?php

//Корневая директория сайта
define('DIR_ROOT', Eresus_CMS::getLegacyKernel()->froot);
//Директория с изображениями (относительно корневой)
define('DIR_IMAGES', '/data');
//Директория с файлами (относительно корневой)
define('DIR_FILES', '/data');


//Высота и ширина картинки до которой будет сжато исходное изображение и создана ссылка на полную версию
define('WIDTH_TO_LINK', 300);
define('HEIGHT_TO_LINK', 300);

//Атрибуты которые будут присвоены ссылке (для скриптов типа lightbox)
define('CLASS_LINK', 'lightview');
define('REL_LINK', 'lightbox');

