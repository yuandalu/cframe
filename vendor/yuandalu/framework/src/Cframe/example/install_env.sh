#!/bin/sh
## create files for env
## @author:chenchao@360.cn
## @wiki:http://add.corp.qihoo.net:8360/display/platform/QFrame

PHP=/usr/local/bin/php
ROOT=`pwd`
$PHP $ROOT/project/env_build.php
