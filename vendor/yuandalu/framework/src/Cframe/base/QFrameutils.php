<?php
class QFrameUtil
{
    const UDP_HOST          = '220.181.127.201';
    const UDP_PORT          = 9958;
    const SEND_INTERVAL     = 3600;
    const EA_LAST_TIME_KEY  = "FW:SDK:SEND:TIME";

    static public function sendSDKMsg($version)
    {/*{{{*/
        if(!self::sendRandChance(self::EA_LAST_TIME_KEY.":".$version)) return false;

        $fp = @fsockopen( "udp://".self::UDP_HOST , self::UDP_PORT , $errno );
        if( !$fp ) return false;
        stream_set_timeout( $fp , 0 , 100 );
        stream_set_blocking( $fp , 0 );

        $sysinfo    = posix_uname();
        $msg        = $version." - ".$sysinfo['nodename']." - ".date('Y-m-d H:i:s',time());
        $res        = fwrite( $fp , $msg );
        fclose($fp);
    }/*}}}*/

    static public function sendRandChance($key)
    {/*{{{*/
        $now = microtime(true);

        if(function_exists("eaccelerator_get"))
        {
            $lastInserTime = eaccelerator_get($key);
            if(!$lastInserTime) $lastInserTime = 0;

            if( ($now - $lastInserTime) < self::SEND_INTERVAL ) return false;
            eaccelerator_put($key, $now);
            return true;
        }else if(function_exists("apc_fetch"))
        {
            $lastInserTime = apc_fetch($key);            
            if(!$lastInserTime) $lastInserTime = 0;

            if( ($now - $lastInserTime) < self::SEND_INTERVAL ) return false;
            apc_store($key, $now);
            return true;
        }

        $rand = rand(1,60);
        if((time()%60 == $rand) && rand(0,20) == 3)
        {
            return true;
        }
        return false;
    }/*}}}*/
}

class QFrameContainer
{
    private $_objs = array();

    static public function getInstance()
    {/*{{{*/
        static $container = null;
        if(is_null($container))
        {
            $container = new QFrameContainer();
        }
        return $container;
    }/*}}}*/
    
    static public function find($name)
    {/*{{{*/
        $container = self::getInstance();        
        return $container->get($name);
    } /*}}}*/

    private function get($name)
    {/*{{{*/
        if(!isset($this->_objs[$name]))
        {
            $this->set($name);
        }
        return $this->_objs[$name];
    }/*}}}*/

    private function set($name)
    {/*{{{*/
        $this->_objs[$name] = new $name;
    }/*}}}*/

}

class QFrameBizResult
{
    static public function ensureNull($result,$msg)
    {/*{{{*/
        if(!is_null($result))
            throw new QFrameRunException($msg);
    }/*}}}*/

    static public function ensureNotNull($result,$msg)
    {/*{{{*/
        if(is_null($result))
            throw new QFrameRunException($msg);
    }/*}}}*/

    static public function ensureNotFalse($result,$msg)
    {/*{{{*/
        if(false === $result)
            throw new QFrameRunException($msg);
    }/*}}}*/

    static public function ensureFalse($result,$msg)
    {/*{{{*/
        if(false !== $result)
            throw new QFrameRunException($msg);
    }/*}}}*/

    static public function ensureEmpty($result,$msg)
    {/*{{{*/
        if(true !== empty($result))
           throw new QFrameRunException($msg); 
    }/*}}}*/

    static public function ensureNotEmpty($result,$msg)
    {/*{{{*/
        if(true === empty($result))
            throw new QFrameRunException($msg);
    }/*}}}*/
}
?>
