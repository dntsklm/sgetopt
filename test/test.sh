#!/bin/sh

# Commands with different options which are tested to different
# optstrings and then current output is compared with human verified
# correct output.
#
# To produce output for human verification, specify --print and
# redirect stdout to file named correct.
#
# This program is meant to be run on GNU/Linux OS.
#
# Copyright © 2014 Donatas Klimašauskas, GPLv3+
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published
# by the Free Software Foundation, either version 3 of the License,
# or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program.  If not, see <https://www.gnu.org/licenses/>.

progname=$(basename $0);
catfile=0;

if [ $# -gt 0 ]; then
    if [ "$1" == '--print' ]; then
	catfile=1;
    else
	echo $progname: error: invalid option specified;
	exit 1;
    fi
fi

progtest=test.php;
current=/run/shm/$progname-$$;
correct=correct;
exitcode=0;

run()
{
    php $progtest "$@" 2>>$current;
}

run ;
run a;
run ab;
run ab:;
run ab:c;
run ab:c:;
run ab:c::;

run  -a -b required -c optional -d;
run a -a -b required -c optional -d;
run ab -a -b required -c optional -d;
run ab: -a -b required -c optional -d;
run ab:c -a -b required -c optional -d;
run ab:c: -a -b required -c optional -d;
run ab:c:: -a -b required -c optional -d;
run ab:c::d -a -b required -c optional -d;
run ab:c::d: -a -b required -c optional -d;
run ab:c::d:: -a -b required -c optional -d;

run ab:c::d:: ;
run ab:c::d:: -a;
run ab:c::d:: -a -b;
run ab:c::d:: -a -b required;
run ab:c::d:: -a -b required -c;
run ab:c::d:: -a -b required -c optional;

run ab:c::d:: -d -c optional -b required -a;
run ab:c::d:: -d -c optional -a -b required;

run d::c::b:a -a -b required -c optional -d;
run d::c::b:a -d -c optional -a -b required;

run ab:c::d:::;
run a.b:c::d::;
run ab:c,::d::;
run ab:c::2d::;
run :ab:c::d::;
run -ab:c::d::;
run ab:c:-:d::;
run ab:c::d::-;
run +ab:c::d::;

run :;
run 2:;

run :a;
run a -a;
run a -a 0;
run a: -a;
run a: -a 0;
run a:: -a;
run a:: -a 0;
run a:: -a optional;

run aa;
run a:a;
run aa:;
run a::a;
run aa::;

run a -aa;
run aa -a;
run a:a -a;
run aa: -a;
run a::a -a;
run aa:: -a;

run a:a -a required;
run aa: -a required;
run a::a -a optional;
run aa:: -a optional;

run '' -a required -a REQUIRED;
run a -a required -a REQUIRED;
run a: -a required -a REQUIRED;
run a:: -a optional -a OPTIONAL;

run a -a=2;
run a: -a=2;
run a:: -a=2;

run a -a =2;
run a: -a -2;
run a:: -a +2;

run a: -a -;
run a: -a - b;
run a: -a '- b';

run a: -a --;
run a: -a -- b;
run a: -a '-- b';

run '' -a;
run '' -a 0;
run 'a ';

run a a-;
run a a- -a;
run a -a a-;

run a '';
run a ' ';
run a: '';
run a: ' ';
run a:: '';
run a:: ' ';

run a -a '';
run a -a ' ';
run a: -a '';
run a: -a ' ';
run a:: -a '';
run a:: -a ' ';

run A:: -A;

if [ $catfile -eq 1 ]; then
    cat $current;
else
    diff $current $correct;
    if [ $? -ne 0 ]; then
	exitcode=1;
    else
	echo $progname: ok;
    fi
fi
rm $current;
exit $exitcode;
