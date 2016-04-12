<?php


include 'QFrameLog.php';
include 'writers/QFrameLogWriterFile.php';
include 'writers/QFrameLogWriterDisplay.php';
include 'writers/QFrameLogWriterFirephp.php';
include 'writers/FirePHP.class.php';


$logPath = dirname(__FILE__);
$log = QFrameLog::getInstance($logPath);


###当你想把日志打印在页面上###
#$log->openOutput(QFrameLog::OUTPUT_MODE_ECHO);
###end#####


###当你想把日志打印在页面上， 以注释形式###
#$log->openOutput(QFrameLog::OUTPUT_MODE_COMMENT);
###end#####

###当你想把日志打印在 firebug的控制台上 ###
#$log->openOutput(QFrameLog::OUTPUT_MODE_FIREPHP);
###end#####


###如果你只想把日志打印在页面上或打印在firebug的控制台上，不想保存文件 ###
#$log->setOutputMode(QFrameLog::OUTPUT_MODE_ECHO );
#$log->setOutput('这个日志不保存文件，只打印在页面或firbug 控制台上');
###end#####


$log->info('这是一条日志信息');
$res = $log->sql('select * from user where qid in (?,?,?)', array(1,2,3));
print_r($res);

/* 错误时
Array
(
    [errno] => 1
    [errmsg] => Permission denied - failed to open file: xxxxxx/sql.log.20100709
)

正确
Array
(
    [errno] => 0
)
*/
