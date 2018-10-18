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
        $phpVersion = null;
        /** @var array $phpVersionOutputs */
        $phpVersionOutputs = ExecManager::exec(ExecManager::getPhpPath(), '', false, '-v');

        foreach ($phpVersionOutputs as $phpVersionOutput) {
            preg_match('(^PHP \d)', $phpVersionOutput, $exlodePhpVersion);
            preg_match('(^PHP \d.\d{1,2}.\d{1,2})', $phpVersionOutput, $detailsPhpVersion);

            if (is_array($exlodePhpVersion) && count($exlodePhpVersion) > 0) {
                $phpVersion = (int) str_replace('PHP ', '', strtoupper($exlodePhpVersion[0]));
                if ($phpVersion < 7) {
                    throw new \Exception(sprintf("PHP (%s) in shell is no longer compatible. You need an upgrade to PHP 7.0 or higher.", $detailsPhpVersion[0]));
                }
            }
        }

        if (null === $phpVersion) {
            throw new \Exception("PHP has not been declared as global variable. Please contact your server administrator.");
        }
    }

    /**
     * @throws \Exception
     */
    public static function checkDbConnection()
    {
        /** @var array $output */
        $output = ExecManager::exec(ExecManager::getPhpPath(), 'db-connection-exec.php', false);

        if (count($output) > 0) {
            throw new \Exception("Something is wrong with your database connection. Please check your wp-config.php and change localhost to 127.0.0.1. If you are using specific port, please include it in the DB Host eg. 127.0.0.1:8889.");
        }
    }
}
