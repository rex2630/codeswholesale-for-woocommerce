<?php

class FileManager
{
    /**
     * @return string
     */
    public static function getUploadPath(): string
    {
        return wp_upload_dir()['basedir'];
    }

    /**
     * @return string
     */
    public static function getImportPath(): string
    {
        return self::getUploadPath() . '/cw-import-products/';
    }
    
    /**
     * @return string
     */
    public static function getImportFilePath($id): string
    {
        return self::getImportPath() . $id . '-import.csv';
    }
    
    /**
     * @return string
     */
    public static function setImportFile($csv, $id): string
    {
        file_put_contents(self::getImportFilePath($id), $csv);
        
        return self::getImportPath() . $id . '-import.csv';        
    } 
    
    
    /**
     * createImportFolder
     */
    public function createImportFolder($id)
    {
        $old = umask(0);

        try {

            $path = self::getUploadPath();

            if (!is_readable($path) || !is_writable($path)) {
                throw new \Exception(sprintf('Bad permissions for uploads folder: "%s"', $path));
            }

            $path = self::getImportPath();

            if (!is_dir($path)) {
                mkdir($path, 0777);
            }

            if (file_exists($path . sprintf('%s-import.csv', $id))) {
                unlink($path . sprintf('%s-import.csv', $id));
            }
        } catch (\Exception $e) {
            umask($old);
            throw $e;
        }

        umask($old);
    }
}

