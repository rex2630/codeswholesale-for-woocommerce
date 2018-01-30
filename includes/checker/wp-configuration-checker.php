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
                throw new \Exception(sprintf("PHP (%s) in shell is no longer compatible. You need an upgrade to PHP 7.0 or higher.", $detailsPhpVersion[0]));
            }
        } else {
            throw new \Exception("PHP has not been declared as global variable. Please contact your server administrator.");
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
            throw new \Exception("Something is wrong with your database connection. Please check your wp-config.php and change localhost to 127.0.0.1.");
        }
    }
}
