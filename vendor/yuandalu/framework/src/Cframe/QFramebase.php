<?php

class QFrameBase
{
    public function __construct()
    {/*{{{*/
    }/*}}}*/
    
    public static function getVersion()
    {/*{{{*/
        return 'QFrame_1.0.3';
    }/*}}}*/

    public static function createWebApp()
    {/*{{{*/
        QFrameUtil::sendSDKMsg(self::getVersion());
        return QFrameContainer::find('QFrameWeb'); 
    }/*}}}*/

}
?>
