Final Project: Jesse Vaught and Delbert(Lenny) Harrison
Class:  CS441 Compilers 
Synposis:  A compilation program that allows input of a file in the .zp language and turns it into a p-stack executable file



------------------------------------------------------------------------------------------------------------------------------------------------

#### Files included ####

calc_out.apm - file included as a test for the script to check if it is accepting compiled p code files correctly
codegen.cc - used to generate p code
codegen.h - definition for codegen.cc
compiler
main.cc -ties all files together
Makefile -makes the files after bnfc creates the parser and syntax rules
Makefile.codegen -makes the compilation files
pstack -implementation of pstack
pstcode.cc - pstack code
pstcode.h -p stack code definition
README.txt -this readme
symbtable.cc -symbol table implementation
symbtable.h -symbol table definition
Tests -contains all tests needed 
test-zp2pstack-instructorTests.sh -script for testing instructor tests
test-zp2pstack-myTests.sh -script for testing our tests
zp.cf -grammar rules

***** NOTE *****

We have provided a separate script file that runs a test for all implemented features and shows them being succesfully compiled and executed.
Their isn't much that can be said about testing without presenting each test file within this document as opposed to the actual files. 
The following list contains all the files that are in the myTests directory (Each file has an expected err and expected out).  For each implementation of the file there is an advanced version and a version that generates an error.  There is also one file called "practical.zp" that contains
a practical program that is slightly larger and more involved.

For the following test files, we have advanced files that compile for more implementation and simple files that show the basic underlying idea for the file. A file
labeled 'simple_... ' is obviously the simple implementation while a file marked otherwise is regular, advanced implementation. The practical test file generates
a triangle of *s based on the number of rows. The user inputs a number for the amount of rows, and the code itself has a nested for loop that indexs the triangle
accordingly. We also have error files that will pass compliation where arg-error compiles an argument error, unknown var handles an unknown variable, and unknown
function where a function call does not exist.  

All test files will pass compliation with no erros or no questionable code. 

#### TEST FILE NAMES ####
2power.zp
2power.zp_expected.err
2power.zp_expected.out
arg_error.zp
arg_error.zp_expected.err
arg_error.zp_expected.out
global.zp
global.zp_expected.err
global.zp_expected.out
if_else_mytest.zp
if_else_mytest.zp_expected.err
if_else_mytest.zp_expected.out
if_mytest.zp
if_mytest.zp_expected.err
if_mytest.zp_expected.out
if-then-else.zp
if-then-else.zp_expected.err
if-then-else.zp_expected.out
if_then.zp
if_then.zp_expected.err
if_then.zp_expected.out
practical.zp
practical.zp_expected.err
practical.zp_expected.out
repeat_until.zp
repeat_until.zp_expected.err
repeat_until.zp_expected.out
simple_forloop.zp
simple_forloop.zp_expected.err
simple_forloop.zp_expected.out
simple_global.zp
simple_global.zp_expected.err
simple_global.zp_expected.out
simple_if_else_mytest.zp
simple_if_else_mytest.zp_expected.err
simple_if_else_mytest.zp_expected.out
simple_if_mytest.zp
simple_if_mytest.zp_expected.err
simple_if_mytest.zp_expected.out
simple_if-then-else.zp
simple_if-then-else.zp_expected.err
simple_if-then-else.zp_expected.out
simple_if_then.zp
simple_if_then.zp_expected.err
simple_if_then.zp_expected.out
simple_repeat_until.zp
simple_repeat_until.zp_expected.err
simple_repeat_until.zp_expected.out
unknown_function.zp
unknown_function.zp_expected.err
unknown_function.zp_expected.out
unknown_var.zp
unknown_var.zp_expected.err
unknown_var.zp_expected.out




------------------------------------------------------------------------------------------------------------------------------------------------

#### Testing Process ####

./test-zp2pstack-myTests.sh will run a shell script that is exactly how the instructor tests shell script works.  The only difference is that
it runs the test cases we have provided and not instructor tests.  Interesting test cases are somewhat subjective, and we feel that our 
test cases do exactly what they should, which is test whether or not the implemented feature works given the requirements.  WHEN RUNNING THIS TEST
SCRIPT IT WILL ASK FOR INPUT ON TWO TEST FILES BUT WILL NOT SHOW THE PROMPT MESSAGE, ENTER A NUMBER BETWEEN 1 AND 30 TO CONTINUE WITH THE SCRIPT.
Those two scripts represent the getnum functionality wherein you must enter a number.  A getnum test file is not provided in light of this.

./test-zp2pstack-instructorTests will run just as the myTests script except this directory contains test cases provided by the professor.  

One thing that we couldn't do was traceback every call and its scope, which severely hindered our ability to see exactly what was happening
for each test case.  The inability to debug in such a way degrades the quality of testing in that we may have missed an unintended error along 
the way.  AS IT STANDS each feature works with the test cases we have provided, but would likely break down if given a complex case that
tests bounds and/or scalability. 

In order to run your own test case without the script, the following commands are required:

bnfc -cpp_stl -m zp.cf
make
make -f Makefile.codegen

Which should work and output a compiler called "compiler2017" that can input .zp files and output .apm files using the commands below:

./compiler2017 test.zp test.apm

To run the .apm file simply use the pstack api executable as so:

./pstack/api test

Running that command should give desired output according to the test file that was compiled and executed.


WHEN WILL THE TESTS FAIL?

Some of the implementations have a weird tendency to not work when other functions are declared before main.  It doesn't happen a lot, but
we have observed that if-else statements will run both the if and else blocks if the expression for if was true.  
------------------------------------------------------------------------------------------------------------------------------------------------

#### Implementations directly related to the "Specs" located in the paf-2017-stages.pdf canvas file ####


* getnum() *

Status:  Works in most testable situations


* if-then if-then-else *

Status: Works in most testable situations
NOTE:  Also implemented regular if and if-else statements that do not require the "then" clause as the requirements were very unclear


* simple for loop *

Status: Works in all implementations, except you cant declare the iterator within the function you can only declare it beforehand and then
assign it a value within expression1


* Repeat Until *

Status: Works in all implementations


* Optimization *

Status: Number of swap functions was successfully reduced, but no other optimization was implemented.


* globals *

Status:  Working in most testable situations, still cant declare and assign things in-line so it must be declared then assigned separately


* for loop (scoped version) *

NOT IMPLEMENTED


* check arg count for functions *

Status: Working in most testable situations.  Will throw an error telling you which function was incorrectly called.
NOTE: Did not implement type checking, only count checking.
Test file "arg_error.zp" shows exactly what happens when two arguements are passed into a function that only needs 1 argument


* doubles and type checking *

NOT IMPLEMENTED

#### Extra features not listed above ####

-List form of declarations: int a,b,c,d;
-Added operators ">" and "=="
-Nested For Loops 


#### Known Bugs ####

bug 1: if-else and if-then-else sometimes run both blocks if the expression evaluates to true, couldn't figure out why or how
bug 2: repeat-until sometimes skips an iteration, also couldn't figure out why or how
