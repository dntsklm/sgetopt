<?php
/*
 * This program is for testing sgetopt() and is meant to be used
 * through shell wrapper. However, it may be called directly from the
 * command line. E.g.:
 * php test.php a: -a required
 *
 * When error handling is done by the caller, then call always (∞)
 * returns an array. If error handling is requested, then return of
 * an array happens only on successful (✓) call; on error condition,
 * prints error information (in getopt(3) style) and exits the caller
 * program. If optstring itself is invalid, it is considered to be the
 * error condition too.
 *
 * Copyright © 2014 Donatas Klimašauskas, GPLv3+
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published
 * by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

$optstring = implode(array_splice($argv, 1, 1));
$cnt = count($argv) - 1;
$useropts = '"' . implode('" "', array_slice($argv, 1)) . '"';

require_once('../sgetopt.php');
fwrite(STDERR, "\noptstring: \"$optstring\"\nuseropts ($cnt): $useropts\n");
fwrite(STDERR, "Returns (∞):\n" . print_r(sgetopt($optstring), TRUE));
fwrite(STDERR, "Returns (✓):\n" . print_r(sgetopt($optstring, TRUE), TRUE));
exit;
?>
