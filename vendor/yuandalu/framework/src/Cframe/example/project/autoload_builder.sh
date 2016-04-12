#!/bin/sh
## build system required files : className => FilePath

PHP=/usr/local/bin/php
FW_HOME=`pwd`
##多个目录，请使用：分隔，例如："FW_HOME/src:FW_HOME/config"
AUTOLOAD_PATH="$FW_HOME/src"

# create project autoload files
# php exe_php scan_filepath dest_auto_load_file cache_key
$PHP $FW_HOME/project/build_includes.php $AUTOLOAD_PATH $FW_HOME/src/www/auto_load.php "fw:$USER:autoload:map"