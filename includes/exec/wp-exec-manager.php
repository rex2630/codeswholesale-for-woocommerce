<?php

class ExecManager
{
    const PHP_PATH = 'php';
//    const PHP_PATH = '/usr/bin/php7.1';

    /**
     * @return string
     */
    public static function getPhpPath(): string
    {        
        if (self::phpTest() === $_SERVER['PATH'] || substr(php_uname(), 0, 7) == "Windows"){
            return self::PHP_PATH;
        }else {
            return PHP_BINDIR . '/php';
        }
    }

    private static function phpTest() {
        exec('php -i', $out);
        $match = array_values(preg_grep("/.SERVER\['PATH'\]/i", $out));
        return substr($match[0], 20);
    }
    
    /**
     * @param string $cmd
     * @param string $path
     * @param bool   $inBackgroud
     * @param string $cmdParameters
     *
     * @return bool
     */
    public static function exec(string $cmd, string $path = '', bool $inBackgroud = true, string $cmdParameters = '')
    {
        $path = '' !== $path ? plugin_dir_path( __FILE__ ) . $path : '';

        $cmd = $cmd . ' ' . $cmdParameters . ' ' . $path;

        if (true === $inBackgroud) {
            self::execInBackground($cmd);
        } else {
            exec($cmd . ' 2>&1', $output);

            return $output;
        }

        return true;
    }

    protected static function execInBackground($cmd) {
        if (substr(php_uname(), 0, 7) == "Windows"){
            pclose(popen("start /B ". $cmd, "r"));
        }
        else {
            exec($cmd . " > /dev/null &");
        }
    }
    
    public static function startImportProducts() 
    {
        self::exec(self::getPhpPath(), 'ExecImpotProducts.php');
    }
    
    public static function startUpdateProductPrice() 
    {
       self::exec(self::getPhpPath(), 'ExecUpdateProductPrice.php');
    }
}