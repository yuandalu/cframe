<?php
$timer = new Timer();
$timer->s();

require __DIR__.'/../bootstrap/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

$app->setNameSpace('App\Controllers\Front');
$app->setControllerPath(__DIR__.'/../app/Controllers/Front');
$app->setViewPath(__DIR__.'/../app/views/front/');

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