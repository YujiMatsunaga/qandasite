<?php
namespace Novus;

require_once __DIR__ . '/../config/def.php';

use DateTime;

class Log
{
    public static function error($message)
    {
        $output = sprintf(
            "[%s] ERROR %s",
            (new DateTime())->format('Y-m-d H:i:s'),
            $message
        );

        $path = __DIR__ . '/../logs/';
        $file = (new DateTime())->format('Y-m-d') . '.log';

        error_log($output . PHP_EOL, 3, $path . $file);
    }

    public static function info($message)
    {
        $output = sprintf(
            "[%s] INFO %s",
            (new DateTime())->format('Y-m-d H:i:s'),
            $message
        );

        $path = __DIR__ . '/../logs/';
        $file = (new DateTime())->format('Y-m-d') . '.log';

        error_log($output . PHP_EOL, 3, $path . $file);
    }

    public static function sql($query, array $binds)
    {
        $sql = $query;
        foreach ($binds as $key => $value) {
            $sql = str_replace($key, $value, $sql);
        }

        self::info($sql);
    }
}
