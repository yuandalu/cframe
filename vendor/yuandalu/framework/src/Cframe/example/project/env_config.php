<?php
/***************************
 *
 * php project Env Config
 * @author:chenchao@360.cn
 * @Wiki:http://add.corp.qihoo.net:8360/pages/viewpage.action?pageId=1672879
 */

#  Current User Name , eg: cc,cb
$curUser = "cc";

#  Production Environment Domain ; eg: hao.360.cn. your domain will be $curUser.host
$host = "fw.360.cn";

#  Current Project Root Path
$project_home = getcwd();

/*******************************************************
 *
 * php project Sys variables , general No need to change
 *
 */

$sysEnvVariable = array(
        "DIRS"          => "logs",                                  #  automatically make dirs,Multiple dirs separated by spaces
        "EXECUTES"      => "project/autoload_builder.sh",           #  autoload.sh
        "SUBSYS"        => "httpd server nginx",                    #  automatically create link for conf
        "USR"           => "$curUser",
        "PROJECT"       => basename($project_home),
        #"NGINX"         => "nwebctl",                              #  sudo $NGINX stop|start
        "APACHE"        => "/usr/local/apache2/bin/apachectl -k",  #  sudo $APACHE stop|start
    );
