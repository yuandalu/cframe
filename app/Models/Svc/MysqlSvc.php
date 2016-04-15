<?php

namespace App\Models\Svc;

use App\Support\Loader;

class MysqlSvc
{
    const CORP = 'f';

    public static function getLock( $index, $timeout = 30 )
    {
        $sql = "select get_lock('".self::makeKey($index)."', ".$timeout.") as status";
        $row = Loader::loadExecutor()->query( $sql );
        if(!$row['status'])
        {
            ErrorSvc::show(ErrorSvc::ERR_MYSQL_GET_LOCK);
            exit;
        }
        return $row['status'];
    }

    public static function releaseLock( $index )
    {
        $sql = "select release_lock('".self::makeKey($index)."') as status";
        $row = Loader::loadExecutor()->query( $sql );
        return $row['status'];
    }

    private static function makeKey( $index )
    {
        return md5( self::CORP.'_'.$index );
    }
}