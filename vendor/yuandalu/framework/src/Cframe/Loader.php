<?php
class QFrameLoader
{
    public static function loadClass($classname)
    {
        $classpath = self::getClassPath();
        if (isset($classpath[$classname]))
        {
            include($classpath[$classname]);
        }
    }
    protected static function getClassPath()
    {
        static $classpath=array();
        if (!empty($classpath)) return $classpath;
        if(function_exists("apc_fetch"))
        {
            $classpath = apc_fetch("fw:autoload:application:1353295141");
            if ($classpath) return $classpath;

            $classpath = self::getClassMapDef();
            apc_store("fw:autoload:application:1353295141",$classpath); 
        }
        else if(function_exists("eaccelerator_get"))
        {
            $classpath = eaccelerator_get("fw:autoload:application:1353295141");
            if ($classpath) return $classpath;

            $classpath = self::getClassMapDef();
            eaccelerator_put("fw:autoload:application:1353295141",$classpath); 
        }
        else
        {
            $classpath = self::getClassMapDef();
        }
        return $classpath;
    }
    protected static function getClassMapDef()
    {
        return array(
            			"QFrame" => 			"/home/q/php/QFrame/QFrame.php",
			"QFrameBase" => 			"/home/q/php/QFrame/QFramebase.php",
			"QFrameConfig" => 			"/home/q/php/QFrame/base/QFrameconfig.php",
			"QFrameDomainUser" => 			"/home/q/php/QFrame/base/QFrameduser.php",
			"QFrameHttp" => 			"/home/q/php/QFrame/base/QFramehttp.php",
			"QFrameStandRoute" => 			"/home/q/php/QFrame/base/QFramerouteutils.php",
			"QFrameRouteRegex" => 			"/home/q/php/QFrame/base/QFramerouteutils.php",
			"QFrameUtil" => 			"/home/q/php/QFrame/base/QFrameutils.php",
			"QFrameContainer" => 			"/home/q/php/QFrame/base/QFrameutils.php",
			"QFrameBizResult" => 			"/home/q/php/QFrame/base/QFrameutils.php",
			"QFrameRunException" => 			"/home/q/php/QFrame/base/QFramexception.php",
			"QFrameException" => 			"/home/q/php/QFrame/base/QFramexception.php",
			"QFrameDB" => 			"/home/q/php/QFrame/base/db/QFrameDB.php",
			"QFrameDBPDO" => 			"/home/q/php/QFrame/base/db/QFrameDB.php",
			"QFrameDBStatment" => 			"/home/q/php/QFrame/base/db/QFrameDB.php",
			"QFrameDBException" => 			"/home/q/php/QFrame/base/db/QFrameDB.php",
			"QFrameDBExplainResult" => 			"/home/q/php/QFrame/base/db/QFrameDBExplainResult.php",
			"QFrameLog" => 			"/home/q/php/QFrame/logger/QFrameLog.php",
			"FirePHP" => 			"/home/q/php/QFrame/logger/writers/FirePHP.class.php",
			"QFrameLogWriterDisplay" => 			"/home/q/php/QFrame/logger/writers/QFrameLogWriterDisplay.php",
			"QFrameLogWriterFile" => 			"/home/q/php/QFrame/logger/writers/QFrameLogWriterFile.php",
			"QFrameLogWriterFirephp" => 			"/home/q/php/QFrame/logger/writers/QFrameLogWriterFirephp.php",
			"QFramedbTest" => 			"/home/q/php/QFrame/t/QFramedbTest.php",
			"AllTests" => 			"/home/q/php/QFrame/t/QFramedbTestSuite.php",
			"QFrameAction" => 			"/home/q/php/QFrame/web/QFrameaction.php",
			"QFrameRouter" => 			"/home/q/php/QFrame/web/QFramerouter.php",
			"QFrameRouterDefaultRoute" => 			"/home/q/php/QFrame/web/QFramerouter.php",
			"QFrameView" => 			"/home/q/php/QFrame/web/QFrameview.php",
			"QFrameWeb" => 			"/home/q/php/QFrame/web/QFrameweb.php",

        );
    }
}
//    spl_autoload_register(array("QFrameLoader","autoload"));
?>