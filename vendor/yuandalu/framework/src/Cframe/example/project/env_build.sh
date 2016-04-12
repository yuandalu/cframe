#!/bin/sh
## App Env Init Script

DIRS="logs"
EXECUTES="project/autoload_builder.sh"
SUBSYS="httpd server"
USR="fw"
PROJECT="example"
APACHE=""
NGINX=""
WEBPATH=""

ROOT=`pwd`

echo create application environment for $USR

# link app config file
cd $ROOT/config

for SUBSYS in $SUBSYS
do
    if test -e $SUBSYS\_conf.php
    then 
        rm $SUBSYS\_conf.php
    fi
    if (test -s $SUBSYS/$SUBSYS\_conf.php.$USR)
    then
        ln -s $SUBSYS/$SUBSYS\_conf.php.$USR $SUBSYS\_conf.php
        echo link -s $SUBSYS/$SUBSYS\_conf.php ........... OK
    else
        echo link -s $SUBSYS/$SUBSYS\_conf.php  ........... Fail
    fi 
done

if [ "$APACHE" ] ; then
    WEBPATH="apache2"
    CONF="httpd"
fi
if [ "$NGINX" ] ; then
    WEBPATH="nginx"
    CONF="nginx"
fi

# link http_conf to apache|nginx conf
if [ $WEBPATH ];then
    if test -e httpd\_conf.php
        if test -e /usr/local/$WEBPATH/conf/include/$PROJECT\_$USR.conf
        then
            sudo rm -f /usr/local/$WEBPATH/conf/include/$PROJECT\_$USR.conf
        fi
    then
        sudo ln -sf $ROOT/config/$CONF\_conf.php /usr/local/$WEBPATH/conf/include/$PROJECT\_$USR.conf 
        echo link -s $CONF\_conf.php to $WEBPATH/include/conf  .............OK
    else
        echo link -s $CONF\_conf.php to $WEBPATH/include/conf  .............Fail
    fi
fi

cd $ROOT
for dir in $DIRS
do
    if (test ! -d $dir)
    then
        mkdir -p $dir
        chmod 777 $dir
        echo mkdir $dir ................ OK
    fi
done

for execute in $EXECUTES
do
    sh $execute > /dev/null
    if test $? -eq 0
    then
        echo sh $execute ................ OK
    fi
done

if [ "$APACHE" ];then
    sudo $APACHE stop
    sudo $APACHE start
    echo restart apache ................ OK
fi

if [ "$NGINX" ];then
    sudo $NGINX stop
    sudo $NGINX start
    echo restart nginx ................ OK
fi
