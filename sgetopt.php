<?php
/*
 * Simple alternative for PHP native getopt() CLI short option parser
 *
 * SYNOPSIS
 *	require_once('sgetopt.php');
 *	array sgetopt ( string $optstring [, bool $handleerror = FALSE ] );
 *
 * For further description, please refer to the README.
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

define('_SGETOPT_OPTSTRING', '/^[^a-z]|[^a-z:]|:{3,}/i');
define('_SGETOPT_OPTION', '/^-[a-z]/i');
define('_SGETOPT_INVALID', 0);
define('_SGETOPT_MISSING', 1);
define('_SGETOPT_EXTRANEOUS', 2);

function _sgetopt_parse($optstring, &$optsprog)
{
	if (!$optstring || preg_match(_SGETOPT_OPTSTRING, $optstring))
		return FALSE;
	$tmp = str_split($optstring);
	$cnt = count($tmp);
	$optsprog = [];
	for ($i = 0; $i < $cnt; $i++)
		if ($tmp[$i] !== ':') {
			$arg = $tmp[$i];
			$optsprog[$arg] = 0;
		} else {
			$optsprog[$arg]++;
		}
	return TRUE;
}

function _sgetopt_out(&$option, $type)
{
	global $argv;

	$progname = basename($argv[0]);
	$fmt = "%s: %s -- '%s'\nTry `%s -h' for more information.\n";
	switch ($type) {
	case _SGETOPT_INVALID:
		$msg = 'invalid option';
		break;
	case _SGETOPT_MISSING:
		$msg = 'option requires an argument';
		break;
	case _SGETOPT_EXTRANEOUS:
		$msg = 'option requires no argument';
		break;
	}
	fprintf(STDERR, $fmt, $progname, $msg, $option, $progname);
	exit(1);
}

function sgetopt($optstring, $handleerror = FALSE)
{
	global $argv;

	if (!_sgetopt_parse($optstring, $optsprog))
		_sgetopt_out($optstring, _SGETOPT_INVALID);
	$tmp = array_slice($argv, 1);
	$cnt = count($tmp);
	$optsuser = [];
	if (!$cnt)
		return $optsuser;
	if (!preg_match(_SGETOPT_OPTION, $tmp[0]))
		_sgetopt_out(implode(' ', $tmp), _SGETOPT_INVALID);
	for ($i = 0; $i < $cnt; $i++)
		if (preg_match(_SGETOPT_OPTION, $tmp[$i])) {
			$arg = substr($tmp[$i], 1);
			$optsuser[$arg] = '';
		} else {
			$optsuser[$arg] .= $tmp[$i];
		}
	if ($handleerror)
		foreach (array_keys($optsuser) as $option) {
			if (!array_key_exists($option, $optsprog))
				_sgetopt_out($option, _SGETOPT_INVALID);
			$len = strlen($optsuser[$option]);
			if ($optsprog[$option] === _SGETOPT_MISSING && !$len)
				_sgetopt_out($option, _SGETOPT_MISSING);
			if (!$optsprog[$option] && $len)
				_sgetopt_out($option, _SGETOPT_EXTRANEOUS);
		}
	return $optsuser;
}
?>
