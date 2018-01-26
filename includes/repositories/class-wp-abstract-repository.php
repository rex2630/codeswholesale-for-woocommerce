<?php

/**
 * Class WP_AbstractRepository
 */
abstract class WP_AbstractRepository implements WP_Repository
{
    /**
     * @var wpdb
     */
    protected $db;

    /**
     * WP_AbstractRepository constructor.
     */
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @return string
     */
    abstract protected function getName(): string;

    /**
     * @return string
     */
    public function getTableName(): string
    {
        global $wpdb;

        return sprintf('%s%s', self::getGeneralPrefix(), $this->getName());
    }

    /**
     * @return bool
     */
    public function exists()
    {
        global $wpdb;

        $tableName = $this->getTableName();

        return $wpdb->get_var(sprintf("SHOW TABLES LIKE '%s'", $tableName)) == $tableName;
    }

    /**
     * @return string
     */
    public static function getGeneralPrefix(): string
    {
        global $wpdb;

        return sprintf('%s%s', $wpdb->prefix,self::CW_PREFIX);
    }
}