#!/bin/sh
## build system required files

PHP=/usr/local/bin/php
FW_HOME=`pwd`
AUTOLOAD_PATH_FW="$FW_HOME"

# create project autoload files
$PHP $FW_HOME/project/build_includes.php $AUTOLOAD_PATH_FW $FW_HOME/Loader.php "fw:autoload:application"

#generate cscope files
#root=$FW_HOME
#cd $root
#find $root/ -type f -name "*.inc" > project/cscope.files
#find $root/ -type f -name "*.php" >> project/cscope.files
#cd project
#cscope -b
