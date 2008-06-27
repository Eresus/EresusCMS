<?php
/**
 * ProCreat Murash
 *
 * PHP Project Build Tool
 *
 * @version 1.0
 *
 * @copyright 2008, ProCreat Systems, http://procreat.ru/
 * @license   http://www.gnu.org/licenses/gpl.txt  GPL License 3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author  Mikhail Krasilnikov <mk@procreat.ru>, <mk@dvaslona.ru>
 */

prepare_project();
build_project();
cleanup();

exit($EXIT_STATUS);

/**
 * Prepare project
 */
function prepare_project()
{
	$GLOBALS['EXIT_STATUS'] = 0;
}
//-----------------------------------------------------------------------------
/**
 * Build project
 */
function build_project()
{
	;
}
//-----------------------------------------------------------------------------
/**
 * Attempt cleanup
 */
function cleanup()
{
	;
}
//-----------------------------------------------------------------------------


/*
 * MBF/1.0 Instructions
 *
 */
 */