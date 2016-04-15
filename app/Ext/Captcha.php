<?php

namespace App\Ext;

use Elephant\Base\Config;
use App\Support\Loader;

class Captcha
{
    const FONT_SIZE = '14';

    const BORDER_COLOR  = '239, 239, 239';

    const CAPTCHA_SESSIONNAME = 'security_code';

    private $_chars_number;//显示多少个字符

    private $_string_type; // Numbers (1), Letters (2), Letters & Numbers (3)

    public function __construct($chars_number=4,$string_type=2){
        $this->_chars_number = $chars_number;
        $this->_string_type = $string_type;
    }

    private function generate_string(){
        if($this->_string_type == 1) // letters
        {
            $array = range('A','Z');
        }
        else if($this->_string_type == 2) // numbers
        {
            $array = array('1','2','3','4','5','6','7','8','9','A','B','C','E','F','G','H','J','K','L','M','N','P','Q','R','S','T','U','V','W','X','Y');
        }
        else // letters & numbers
        {
            $x = ceil($this->_chars_number / 2);
            $array_one = array_rand(array_flip(range('A','Z')), $x);
            if($x <= 2) $x = $x - 1;
            $array_two = array_rand(array_flip(range(1,9)), $this->_chars_number - $x);
            $array = array_merge($array_one, $array_two);
        }

        $rand_keys = array_rand($array, $this->_chars_number);
        $string = '';
        foreach($rand_keys as $key)
        {
            $string .= $array[$key];
        }
        return $string;
    }

    /* Show Captcha Image */
    public function show_image($width = 88, $height = 31)
    {

        if(!file_exists(Config::getConfig('CAPTCHA_FONT_FILE'))) exit('字体文件不存在');

        if($this->_chars_number < 3) exit('验证码长度最小为3');

        $string = $this->generate_string();

        $im = ImageCreate($width, $height);

        //设置空白背景
        $bg = ImageColorAllocateAlpha($im, 255, 255, 255, 0); // (PHP 4 >= 4.3.2, PHP 5)
        ImageFill($im, 0, 0, $bg);

        ///随机绘制两条虚线，起干扰作用
        $black = ImageColorAllocate($im, rand(180,240),rand(180,240),rand(180,240));
        $gray = ImageColorAllocate($im, rand(180,240),rand(180,240),rand(180,240));
        $style = array($black, $black, $black, $black, $black, $gray, $gray, $gray, $gray, $gray);
        imagesetstyle($im, $style);
        $y1=rand(0,20);
        $y2=rand(0,20);
        $y3=rand(0,20);
        $y4=rand(0,20);
        imageline($im, 0, $y1, $width, $y3, IMG_COLOR_STYLED);
        imageline($im, 0, $y2, $width, $y4, IMG_COLOR_STYLED);

        //在画布上随机生成大量黑点，起干扰作用
        for($i=0;$i<240;$i++)
        {
            imagesetpixel($im, rand(0,$width), rand(0,$height), $black);
        }

        /* Border Color */
        if(self::BORDER_COLOR)
        {
            list($red, $green, $blue) = explode(',', self::BORDER_COLOR);
            $border = ImageColorAllocate($im, $red, $green, $blue);
            ImageRectangle($im, 0, 0, $width - 1, $height - 1, $border);
        }



        $y = 24;

        for($i = 0; $i < $this->_chars_number; $i++)
        {
            $char = $string[$i];

            $factor = 15;
            $x = ($factor * ($i + 1)) - 6;
            $angle = rand(-15, 15);

            $textcolor = ImageColorAllocate($im, rand(60,120), rand(60,120),rand(60,120));
            imagettftext($im, self::FONT_SIZE, $angle, $x, $y, $textcolor, Config::getConfig('CAPTCHA_FONT_FILE'), $char);
        }

        //存入SESSION
        Loader::loadSess()->set(self::CAPTCHA_SESSIONNAME,$string);

        //输出文件头为图片格式
        header("Content-type: image/png");
        ImagePNG($im);

        exit;
    }

    /* Show Captcha Image */
    public function showHtmlImage($width = 88, $height = 31)
    {

        if(!file_exists(Config::getConfig('CAPTCHA_FONT_FILE'))) exit('字体文件不存在');

        if($this->_chars_number < 3) exit('验证码长度最小为3');

        $string = $this->generate_string();

        $im = ImageCreate($width, $height);

        //设置空白背景
        $bg = ImageColorAllocateAlpha($im, 255, 255, 255, 0); // (PHP 4 >= 4.3.2, PHP 5)
        ImageFill($im, 0, 0, $bg);

        ///随机绘制两条虚线，起干扰作用
        $black = ImageColorAllocate($im, rand(180,240),rand(180,240),rand(180,240));
        $gray = ImageColorAllocate($im, rand(180,240),rand(180,240),rand(180,240));
        $style = array($black, $black, $black, $black, $black, $gray, $gray, $gray, $gray, $gray);
        imagesetstyle($im, $style);
        $y1=rand(0,20);
        $y2=rand(0,20);
        $y3=rand(0,20);
        $y4=rand(0,20);
        imageline($im, 0, $y1, $width, $y3, IMG_COLOR_STYLED);
        imageline($im, 0, $y2, $width, $y4, IMG_COLOR_STYLED);

        //在画布上随机生成大量黑点，起干扰作用
        for($i=0;$i<1000;$i++)
        {
            imagesetpixel($im, rand(0,$width), rand(0,$height), $black);
        }

        /* Border Color */
        if(self::BORDER_COLOR)
        {
            list($red, $green, $blue) = explode(',', self::BORDER_COLOR);
            $border = ImageColorAllocate($im, $red, $green, $blue);
            ImageRectangle($im, 0, 0, $width - 1, $height - 1, $border);
        }



        $y = 24;

        for($i = 0; $i < $this->_chars_number; $i++)
        {
            $char = $string[$i];

            $factor = 15;
            $x = ($factor * ($i + 1)) - 6;
            $angle = rand(-15, 15);

            $textcolor = ImageColorAllocate($im, rand(60,120), rand(60,120),rand(60,120));
            imagettftext($im, self::FONT_SIZE, $angle, $x, $y, $textcolor, Config::getConfig('CAPTCHA_FONT_FILE'), $char);
        }

        //存入SESSION
        Loader::loadSess()->set(self::CAPTCHA_SESSIONNAME,$string);

        //输出文件头为DIV

        for($i=0 ; $i<$height ; $i++)
        {
            for($j=0 ; $j<$width ; $j++)
            {
                $rgb_index = imagecolorat($im, $j, $i);
                $rgb = imagecolorsforindex($im, $rgb_index);
                echo "#".dechex($rgb['red']),dechex($rgb['green']),dechex($rgb['blue']);
                /*
                if(array_sum($rgb)<700)
                {
                    echo "#".dechex($rgb['red']),dechex($rgb['green']),dechex($rgb['blue']);
                }else
                {
                    echo "#".dechex(rand(180,210)).dechex(rand(180,210)).dechex(rand(180,210));
                }
                */
            }
        }


        exit;
    }

    public function verify($code)
    {
        $save_code = strtolower(Loader::loadSess()->get(Captcha::CAPTCHA_SESSIONNAME));
        Loader::loadSess()->destroy(Captcha::CAPTCHA_SESSIONNAME);
        $code = strtolower($code);

        if($code == '')
        {
            return false;
        }

        if($code == $save_code)
        {
            return true;
        }

        return false;
    }
}