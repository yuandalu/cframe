<?php
$timer = new Timer();
$timer->s();
define('BASE_DIR', dirname(dirname(dirname(__FILE__))));

require BASE_DIR.'/bootstrap/autoload.php';

\App\Support\Loader::init();
\App\Support\Loader::regSess('v');

$app = require_once BASE_DIR.'/bootstrap/app.php';

$app->setNameSpace('App\Controllers\Front');
$app->setControllerPath(BASE_DIR.'/app/Controllers/Front');
$app->setViewPath(BASE_DIR.'/resources/views/front/');

/*
 * 自定义路由规则
 *
  $userroute = new QFrameStandRoute(
       'u/:qid',
       array(
              'controller' => 'my',
              'action'     => 'index',
       )
);
Container::find('QFrameRouter')->addRoute('user',$userroute);
 */

$app->run();

echo '<br>';
echo $timer->resourceUsage();

class Timer
{
    private $_start_time = 0;
    private $_stop_time  = 0;
    private $times = array(
      'hour'   => 3600000,
      'minute' => 60000,
      'second' => 1000
    );
    private $startTime;
    private $requestTime;

    public function __construct()
    {
    }

    public function start()
    {
        $this->_start_time = microtime(true);
    }

    public function stop()
    {
        $this->_stop_time = microtime(true);
    }

    public function spent($num = 2)
    {
        return number_format(round(($this->_stop_time - $this->_start_time), $num), $num, '.', '');
    }

    public function s()
    {
        if (isset($_SERVER['REQUEST_TIME_FLOAT'])) {
            $this->requestTime = $_SERVER['REQUEST_TIME_FLOAT'];
        } elseif (isset($_SERVER['REQUEST_TIME'])) {
            $this->requestTime = $_SERVER['REQUEST_TIME'];
        } else {
            $this->requestTime = microtime(true);
        }
        $this->startTime = microtime(true);
    }
    public function e()
    {
        return $this->secondsToTimeString(microtime(true) - $this->startTime);
    }
    public function resourceUsage()
    {
        return sprintf(
            'Time: %s, Memory: %4.2fMb',
            $this->timeSinceStartOfRequest(),
            memory_get_peak_usage(true) / 1048576
        );
    }
    public function secondsToTimeString($time)
    {
        $ms = round($time * 1000);
        foreach ($this->times as $unit => $value) {
            if ($ms >= $value) {
                $time = floor($ms / $value * 100.0) / 100.0;
                return $time . ' ' . ($time == 1 ? $unit : $unit . 's');
            }
        }
        return $ms . ' ms';
    }
    public function timeSinceStartOfRequest()
    {
        return $this->secondsToTimeString(microtime(true) - $this->requestTime);
    }

}