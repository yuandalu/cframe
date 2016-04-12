<?php
require_once "env_config.php";

if(empty($curUser))         exit("curUser is empty\n");
if(empty($host))            exit("host is empty\n");
if(empty($project_home))    exit("project home can't be empty\n");
$curPath = getcwd();
if(substr($curPath,-5) == "project")
    echo "Sorry!! I must run in Project Root\n";
echo "Begin create Necessary File for ".$curUser."\n";

$devHost        = $curUser.".".$host;
$confPath       = getcwd()."/config/";
$project_home   = rtrim($project_home,"/")."/";
$buildEnvShell  = $project_home."project/env_build.sh";   

/**
 * 构建 server_conf.php 和 httpd_conf.php
 */

$searchArray= array("__DOCUMENTROOT__","__SERVER_NAME__","__CONFIG_PATH__");
$repArray   = array($project_home."src/www/",$devHost,$confPath);
$apacheConf = str_replace($searchArray,$repArray,getApacheConfTpl());
$nginxConf  = str_replace($searchArray,$repArray,getNginxConfTpl());

$devApacheFilePath    = $confPath."httpd/httpd_conf.php.".$curUser;
$devNginxFilePath     = $confPath."nginx/nginx_conf.php.".$curUser;
$devServerFilePath    = $confPath."server/server_conf.php.".$curUser;

$fileRemainFlag = false;
if(file_exists($devServerFilePath) || file_exists($devNginxFilePath) || file_exists($devApacheFilePath))
{
    $fileRemainFlag = true; 
}
if($fileRemainFlag)
{
    print("conf is already exist! if yes, file will be overwritten anyway!(y/n)\n");
    $fp = fopen('/dev/stdin','r');
    $input = fgets($fp,255);
    fclose($fp);
    $input = trim($input);

    if($input !== 'y') exit;
}
$devServerConf = <<<EOF
<?php
\$ROOT_PATH       =   "$project_home";
\$EXCEPTION       =   true;
?>
EOF;

if(is_writeable($devApacheFilePath) || is_writeable(dirname($devApacheFilePath)) )
{
    file_put_contents($devApacheFilePath,$apacheConf);
    echo "Create Apache Httpd Conf         ......OK\n";
}else
{
    echo "Create Apache Httpd Conf         ......FAIL\n";
}

if(is_writeable($devNginxFilePath) || is_writeable(dirname($devNginxFilePath)) )
{
    file_put_contents($devNginxFilePath,$nginxConf);
    echo "Create nginx Httpd Conf         ......OK\n";
}else
{
    echo "Create nginx Httpd Conf         ......FAIL\n";
}

if(is_writeable($devServerFilePath) || is_writeable(dirname($devServerFilePath)) )
{
    file_put_contents($devServerFilePath,$devServerConf);
    echo "Create Server Conf        ......OK\n";
}else
{
    echo "Create Server Conf        .....FAIL\n";
}

/**
 * 补全上线脚本中对应的变量
 */

$deployFile         = $project_home."/tools/conf.sh";
$deployContent      = file_get_contents($deployFile);
$needCreateDepoly   = strpos($deployContent,'__PROJECT_NAME__');
$deployContent      = str_replace("__PROJECT_NAME__",basename($project_home),$deployContent);
#TODO del
if($needCreateDepoly)
{
    file_put_contents($deployFile,$deployContent);
    echo "create depoly.sh          ......OK\n";
}else
{
    echo "Create depoly.sh          .....FAIL\n";
}

replaceShellVariable($sysEnvVariable , $buildEnvShell);
echo shell_exec("sh $buildEnvShell");

function replaceShellVariable($sysEnv , $buildEnvShell)
{
    $file  = $buildEnvShell;
    if(!file_exists($file) && empty($sysEnv) && !is_writeable($file)) {echo "env file is not exist or not available!";exit;}
    
    $content = file_get_contents($file);
    foreach($sysEnv as $key =>$value)
    {
        $content  = preg_replace("/$key=\"([^\"]*)\"/",$key."="."\"$value\"",$content);
    }
    file_put_contents($file , $content);
    echo "Create ".basename($buildEnvShell)."        ......OK\n";
}

function getApacheConfTpl()
{
    $tpl = <<<EOF
<VirtualHost *:80>
    ServerName __SERVER_NAME__
    DocumentRoot __DOCUMENTROOT__
    RewriteEngine On
    RewriteRule !^/(script|style)?(.*)\.(js|ico|gif|jpg|png|css|php|xml|txt)$ /index.php
    php_value include_path "/home/q/php/:__CONFIG_PATH__:."
</VirtualHost>
EOF;
    return $tpl;
}

function getNginxConfTpl()
{
    $tpl = <<<EOF
server
{
    listen 80;
    server_name __SERVER_NAME__;
    root __DOCUMENTROOT__;
    index index.php;
    
    if (\$request_filename !~* ^/(.*)\.(js|ico|gif|jpg|png|css|php|xml|txt|html|swf|apk|ipa)$)
    {
        rewrite ^/(.*)$ /index.php?$1 last;
    }

    location ~ .*\.(php|php5)?$
    {
       fastcgi_pass 127.0.0.1:9000;
       include fastcgi.conf;
       fastcgi_index index.php;
       fastcgi_param  PROJECT_INCLUDE_PATH  "/home/q/php:__CONFIG_PATH__:.";
    }
}
EOF;
    return $tpl;
}

function getAutoLoadTpl()
{
    $tpl = <<<EOF
#!/bin/sh
## build system required files : className => FilePath

PHP=/usr/local/bin/php
__EXAMPLE_HOME__=`pwd`
##多个目录，请使用：分隔，例如："__EXAMPLE_HOME__/src:__EXAMPLE_HOME__/config"
AUTOLOAD_PATH="\$__EXAMPLE_HOME__/src"

# create project autoload files
# php exe_php scan_filepath dest_auto_load_file cache_key
\$PHP \$__EXAMPLE_HOME__/project/build_includes.php \$AUTOLOAD_PATH \$__EXAMPLE_HOME__/src/www/auto_load.php "__PROJECT__:\$USER:autoload:map"
EOF;
    return $tpl;
}
?>
