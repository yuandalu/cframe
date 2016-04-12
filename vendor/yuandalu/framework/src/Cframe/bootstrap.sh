#!/bin/bash

platform=`uname -s`
if [ "$platform" = "Linux" ]
then
    SCRIPT=`readlink -f $0`
    # Absolute path this script is in, thus /home/user/bin
    basedir=`dirname $SCRIPT`
    basename=`basename $SCRIPT`
elif [ "$platform" = "FreeBSD" ]
then
    SCRIPT=`realpath $0`
    # Absolute path this script is in, thus /home/user/bin
    basedir=`dirname $SCRIPT`
    basename=`basename $SCRIPT`
else
    echo "Not support ${platform}"
    exit -1
fi

hostname=`hostname`
datacenter=`echo $hostname | awk -F. '{print $3;}'`

cd $basedir

if test -e "/home/q/php/QFrame"
then
    rm /home/q/php/QFrame
fi

ln -s $basedir /home/q/php/QFrame;

sh project/autoload_builder.sh

if test -e "Loader.php"
then
    content="OK Loader.php generated!\n\n"
else
    content="ERROR Loader.php not genernated!\n\n"
fi

content=$content"$hostname QFrame Installed\n\n"

echo -e $content

