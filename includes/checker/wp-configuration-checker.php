<?php

/**
 * Class WP_ConfigurationChecker
 */
class WP_ConfigurationChecker
{
    /**
     * @throws \Exception
     */
    public static function checkPhpVersion()
    {
        $phpVersionOutput = ExecManager::exec(ExecManager::PHP_PATH, '', false, '-v');

        preg_match('(^PHP \d)', $phpVersionOutput[0], $exlodePhpVersion);
        preg_match('(^PHP \d.\d{1,2}.\d{1,2})', $phpVersionOutput[0], $detailsPhpVersion);

        if (is_array($exlodePhpVersion) && count($exlodePhpVersion) > 0) {
            $phpVersion = (int) str_replace('PHP ', '', strtoupper($exlodePhpVersion[0]));
            if ($phpVersion < 7) {
                throw new \Exception(sprintf("Too low php version (%s) in shell. Require php version 7.0 or higher", $detailsPhpVersion[0]));
            }
        } else {
            throw new \Exception("We met issue with your PATH to Php. Contact with your administrator");
        }
    }

    /**
     * @throws \Exception
     */
    public static function checkDbConnection()
    {
        /** @var array $output */
        $output = ExecManager::exec(ExecManager::PHP_PATH, 'db-connection-exec.php', false);

        if (count($output) > 0) {
            throw new \Exception("Something went wrong with your Database connection. Check your wp-config.php and change localhost to 127.0.0.1");
        }
    }
}
