<?PHP

namespace RatchetChat\Logs;

use Dotenv\Dotenv;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class MyLogger
{
    public static function get($channelName)
    {
        $logger = new Logger($channelName);
        $logger->pushHandler(new StreamHandler(__DIR__ . "/log_" . date("Ymd") . ".log", Logger::DEBUG));
        return $logger;
    }
}