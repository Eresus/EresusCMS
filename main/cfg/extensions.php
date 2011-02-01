<?php
/**
 * ${product.title} ${product.version}
 *
 * ${product.description}
 *
 * Конфигурация расширений
 *
 * Этот файл содержит список кстановленных расширений, а так же их настройки.
 * Расширения размещаются в директории ext-3rd, каждое в отдельной поддиректории.
 * В директории расширения должен находиться файл eresus-conntecor.php, обеспечивающий
 * взаимодействие этого расширения с Eresus.
 *
 * Все расширения разбиты по группам, определяющим область применения расширения. Внутри
 * группы они так же делятся по функциям, которые они расширяют.
 *
 * Внутри расширяемой функции в виде ассоциативного массива перечисляются все установленные
 * расширения. В качестве ключа должно использоваться имя директории, в которой расположено
 * расширение. Формат данных пока не определён, используйте значение null.
 *
 * @copyright 2004, ProCreat Systems, http://procreat.ru/
 * @copyright 2007, Eresus Project, http://eresus.ru/
 * @license ${license.uri} ${license.name}
 * @author Mikhail Krasilnikov <mihalych@vsepofigu.ru>
 *
 * @package CoreExtensionsAPI
 *
 * $Id$
 */

$GLOBALS['Eresus']->conf['extensions'] = array(
	// Расширение возможностей форм ввода
	'forms' => array(
		// Расширение полей типа memo
		'memo_syntax' => array(
			'editarea' => null,
		),
		// Расширение полей типа html
		'html' => array(
			'tinymce' => null,
		),
	),
);
