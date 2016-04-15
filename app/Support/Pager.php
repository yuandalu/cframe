<?php

namespace App\Support;

use App\Support\Loader;

class Pager
{
    private static $options = array();
    private static $return = array();
    private static $executor = null;

    private function __construct()
    {

    }

    /**
     * 基本参数初始化及检查
     *
     * @param array $options
     */
    private static function init($options)
    {
        if(!isset($options['executor']))
        {
            self::$executor = Loader::loadExecutor();
        }else
        {
            self::$executor = $options['executor'];
        }
        if(!isset($options['html']))
        {
            $options['html'] = false;
        }
        if(empty($options['sql']))
        {
            throw new Exception(__METHOD__ . "|" . __LINE__ . ": miss sql");
        }
        if(empty($options['file_name']))
        {
            $options['file_name'] = $_SERVER['PHP_SELF'];//不适合rewrite
        }
        if($options['per_page'] != -1)
        {
            if(empty($options['per_page']))
            {
                $options['per_page'] = 20;
            }
        }
        if(empty($options['page_param']))
        {
            $options['page_param'] = "cp";
        }
        if(empty($options['seqnum']))
        {
            $options['seqnum'] = 11;
        }

        if (!empty($options['request_param']))
        {
            $options['file_name'] .= "?" . implode("&", $options['request_param']) . "&";
        }else
        {
            $options['file_name'] .= "?";
        }

        $options['sql'] = str_replace(array("\t","\n","\r"),array(" "," "," "),$options['sql']);
        $options['sql'] = preg_replace("/^SELECT/i", "SELECT SQL_CALC_FOUND_ROWS ", $options['sql']);
        $options['curr_page'] = intval( $options['curr_page'] );
        if($options['curr_page'] < 1)
        {
            $options['curr_page'] = 1;
        }
        if($options['per_page']>0)
        {
            $limit = ($options['curr_page'] - 1) * $options['per_page'];
            $limit = $limit < 0 ? 0 : $limit;
            $options['sql'] .= " limit " . $limit . "," . $options['per_page'];
        }
        self::$options = $options;
    }

    /**
     *  实现查询
     */
    public static function render($options)
    {
        try
        {
            self::init($options);
        }catch (Exception $e)
        {
            throw $e;
        }
        self::$return['curr_page'] = self::$options['curr_page'];

        $t = self::getExecutor()->querys( self::$options['sql'] , self::$options['sql_param'] );
        self::$return['records']=$t;

        $c = self::getExecutor()->query("SELECT FOUND_ROWS() as c");
        self::$return['rows'] = $c['c'];

        self::$return['pages'] = ceil(self::$return['rows'] / self::$options['per_page']);
        self::$return['prev'] = (1 == self::$options['curr_page']) ? -1 : self::$options['curr_page'] - 1;
        self::$return['next'] = (self::$return['pages'] == self::$options['curr_page'])? -1 : self::$options['curr_page'] + 1;
        self::$return['linkinfo'] = self::makelink();
        return self::$return;
    }

    /**
     *  连接相关信息
     */
    private static function makelink()
    {
        $linkinfo = array();
        $linkinfo['first'] = self::$options['file_name'] . self::$options['page_param']. "=1";
        $linkinfo['last'] = self::$options['file_name'] . self::$options['page_param']. "=" . self::$return['pages'];

        if(self::$return['prev'] != -1)
        {
            $linkinfo['prev'] = self::$options['file_name'] . self::$options['page_param']. "=" . self::$return['prev'];
        }else {
            $linkinfo['prev'] = self::$options['file_name'] . self::$options['page_param']. "=1";
        }
        if(self::$return['next'] != -1)
        {
            $linkinfo['next'] = self::$options['file_name'] . self::$options['page_param']. "=" . self::$return['next'];
        }else {
            $linkinfo['next'] = self::$options['file_name'] . self::$options['page_param']. "=" . self::$return['pages'];
        }
        $linkinfo['seq'] = array();



        if(self::$return['curr_page'] + floor(self::$options['seqnum'] / 2)>self::$return['pages'])
        {
            $b_pager=self::$return['pages'] - self::$options['seqnum'] + 1;
        }else
        {
            $b_pager =self::$return['curr_page'] - floor(self::$options['seqnum'] / 2);
        }
        $i = $b_pager >= 1 ? $b_pager : 1;

        for($j = 1; $i <= self::$return['pages']; $i++, $j++)
        {
            $linkinfo['seq'][$i] = self::$options['file_name'] . self::$options['page_param'] . "=" . $i;
            if ($j >= self::$options['seqnum'])
            {
                break;
            }
        }
        return $linkinfo;
    }

    public static function pageStr($page=1,$pagesize=20,$rows,$baseurl, $varName = 'p')
    {

        if($page >1)
        {
            $prepage=$page-1;
        }
        else
        {
            $prepage=-1;
        }
        $count=ceil($rows/$pagesize);
        if($count == 1 || $count == 0){
            return '';
        }
        if($page < $count)
        {
            $nextpage=$page+1;
        }
        else
        {
            $nextpage=-1;
        }

        if($prepage==-1)
        {
            $pageStr="<a>上一页</a>";
        }
        else
        {
            $pageStr="<a target='_self' href='".$baseurl."$varName=".$prepage."' >上一页</a>";
        }
        if($count<=8)
            {
                for($i=1;$i<=$count;$i++)
                {
                    if($i==$page)
                    {
                        $pageStr.="<span class='on'>".$i."</span>";
                    }
                    else
                    {
                        $pageStr.="<a target='_self' href='".$baseurl."$varName=".$i."' >".$i."</a>";
                    }

                }
        }
        elseif($page<=4 && $count> 8)
        {

            for($i=1;$i<=6;$i++)
            {
                if($i==$page)
                    {
                        $pageStr.="<span class='on'>".$i."</span>";
                    }
                    else
                    {
                        $pageStr.="<a target='_self' href='".$baseurl."$varName=".$i."' >".$i."</a>";
                    }

            }
            if($page==$count)
            {
                $pageStr.="...<span class='on'>".$count."</span>";
            }
            else
            {
                $pageStr.="...<a target='_self' href='".$baseurl."$varName=".$count."' >".$count."</a>";
            }
        }
        elseif($page>4 && $page <= $count-3 && $count>= 8)
        {
            $max=$page+2;
            if($max >= $count)
            {
                $max=$count-1;
            }
            $pageStr.="<a target='_self' href='".$baseurl."$varName=1' >1</a>...";
            for($i=$max-4;$i<$max;$i++)
            {
                if($i==$page)
                {
                    $pageStr.="<span class='on'>".$i."</span>";
                }
                else
                {
                    $pageStr.="<a target='_self' href='".$baseurl."$varName=".$i."' >".$i."</a>";
                }

            }
            if($page==$count)
            {
                $pageStr.="...<span class='on'>".$count."</span>";
            }
            else
            {
                $pageStr.="...<a target='_self' href='".$baseurl."$varName=".$count."' >".$count."</a>";
            }
        }
        elseif( $count>= 8 && $page <= $count && $page >= $count -2)
        {
            $max=$page+2;
            if($max >= $count)
            {
                $max=$count-1;
            }
            $pageStr.="<a target='_self' href='".$baseurl."$varName=1' >1</a>...";
            for($i=$max-4;$i<=$max;$i++)
            {
                if($i==$page)
                {
                    $pageStr.="<span class='on'>".$i."</span>";
                }
                else
                {
                    $pageStr.="<a target='_self' href='".$baseurl."$varName=".$i."' >".$i."</a>";
                }

            }
            if($page==$count)
            {
                $pageStr.="<span class='on'>".$count."</span>";
            }
            else
            {
                $pageStr.="<a target='_self' href='".$baseurl."$varName=".$count."' >".$count."</a>";
            }
        }
        if($nextpage==-1)
        {
            $pageStr.="<a>下一页</a>";
        }
        else
        {
            $pageStr.="<a target='_self' href='".$baseurl."$varName=".$nextpage."' >下一页</a>";
        }
        //$pageStr.='<form id="gotopage" method="GET" action="">';
        //$pageStr.= '<b><i>跳转到</i>';
        //$pageStr.='<input name="p" type="text" class="text" />';
        //$pageStr.= '<input name="" type="button" class="button" onclick="$(\'#gotopage\').submit();"/>';
        //$pageStr.= '</form>';

        return $pageStr;

    }

    public static function pageStr3($page=1,$pagesize=20,$rows,$baseurl, $varName = 'p')
    {

        if($page >1)
        {
            $prepage=$page-1;
        }
        else
        {
            $prepage=-1;
        }
        $count=ceil($rows/$pagesize);
        if($count == 1 || $count == 0){
            return '';
        }
        if($page < $count)
        {
            $nextpage=$page+1;
        }
        else
        {
            $nextpage=-1;
        }

        if($prepage==-1)
        {
            $pageStr="<a>上一页</a>";
        }
        else
        {
            $pageStr="<a target='_self' href='".$baseurl."$varName=".$prepage."' >上一页</a>";
        }
        if($count<=8)
            {
                for($i=1;$i<=$count;$i++)
                {
                    if($i==$page)
                    {
                        $pageStr.="<span class='on'>".$i."</span>";
                    }
                    else
                    {
                        $pageStr.="<a target='_self' href='".$baseurl."$varName=".$i."' >".$i."</a>";
                    }

                }
        }
        elseif($page<=4 && $count> 8)
        {

            for($i=1;$i<=6;$i++)
            {
                if($i==$page)
                    {
                        $pageStr.="<span class='on'>".$i."</span>";
                    }
                    else
                    {
                        $pageStr.="<a target='_self' href='".$baseurl."$varName=".$i."' >".$i."</a>";
                    }

            }
            if($page==$count)
            {
                $pageStr.="...<span class='on'>".$count."</span>";
            }
            else
            {
                $pageStr.="...<a target='_self' href='".$baseurl."$varName=".$count."' >".$count."</a>";
            }
        }
        elseif($page>4 && $page <= $count-3 && $count>= 8)
        {
            $max=$page+2;
            if($max >= $count)
            {
                $max=$count-1;
            }
            $pageStr.="<a target='_self' href='".$baseurl."$varName=1' >1</a>...";
            for($i=$max-4;$i<$max;$i++)
            {
                if($i==$page)
                {
                    $pageStr.="<span class='on'>".$i."</span>";
                }
                else
                {
                    $pageStr.="<a target='_self' href='".$baseurl."$varName=".$i."' >".$i."</a>";
                }

            }
            if($page==$count)
            {
                $pageStr.="...<span class='on'>".$count."</span>";
            }
            else
            {
                $pageStr.="...<a target='_self' href='".$baseurl."$varName=".$count."' >".$count."</a>";
            }
        }
        elseif( $count>= 8 && $page <= $count && $page >= $count -2)
        {
            $max=$page+2;
            if($max >= $count)
            {
                $max=$count-1;
            }
            $pageStr.="<a target='_self' href='".$baseurl."$varName=1' >1</a>...";
            for($i=$max-4;$i<=$max;$i++)
            {
                if($i==$page)
                {
                    $pageStr.="<span class='on'>".$i."</span>";
                }
                else
                {
                    $pageStr.="<a target='_self' href='".$baseurl."$varName=".$i."' >".$i."</a>";
                }

            }
            if($page==$count)
            {
                $pageStr.="<span class='on'>".$count."</span>";
            }
            else
            {
                $pageStr.="<a target='_self' href='".$baseurl."$varName=".$count."' >".$count."</a>";
            }
        }
        if($nextpage==-1)
        {
            $pageStr.="<a>下一页</a>";
        }
        else
        {
            $pageStr.="<a target='_self' href='".$baseurl."$varName=".$nextpage."' >下一页</a>";
        }
        $pageStr.='<form id="gotopage" method="GET" action="'.$baseurl.'">';
        $pageStr.= '<b><i>跳转到</i>';
        $pageStr.='<input name="p" id="p" type="text" class="text" />';
        $pageStr.= '<input name="" type="button" class="button" onclick="goPage(document.getElementById(\'p\').value,'.$count.',\''.$baseurl.'\')"/';
        $pageStr.= '</form>';
        return $pageStr;

    }

    protected static function getExecutor()
    {
        return self::$executor;
    }
}