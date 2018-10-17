<?php

/**
 * Class WP_DbManager
 */
class WP_DbManager implements \CodesWholesaleFramework\Database\Interfaces\DbManagerInterface
{
    /**
     * @var wpdb
     */
    protected $db;

    /**
     * WP_DbManager constructor.
     */
    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->db->prefix;
    }

    /**
     * 
     * @param string $sql
     * @return bool
     */
    public function  sql(string $sql): bool
    {
        $result = $this->db->query($sql);

        return false === $result ? false : true;
    }

    
    /**
     * @param string $table
     * @return bool
     */
    public function exists(string $table): bool
    {
        return $this->db->get_var(sprintf("SHOW TABLES LIKE '%s'", $table)) == $table;
    }

    /**
     * @param string $table
     * @param array $fields
     * @return bool
     */
    public function  insert(string $table, array $fields): bool
    {
        $result = $this->db->insert($table, $fields);

        return false === $result ? false : true;
    }

    /**
     * @param string $table
     * @param array $fields
     * @param array $conditions
     * @return bool
     */
    public function  update(string $table, array $fields, array $conditions): bool
    {
        $result = $this->db->update($table, $fields, $conditions);

        return false === $result ? false : true;
    }

    /**
     * @param string $table
     * @param array $fields
     * @param array $conditions
     * @param string $operator
     * @return array|null|object
     */
    public function get(string $table, array $fields = [], array $conditions, string $operator = 'AND')
    {
        $where = [];

        foreach($conditions as $key => $value) {
            if(is_array($value)) {
                foreach($value as $val) {
                    $where[] = $key . " = '"  . $val  ."'";
                }
            } else {
                $where[] = $key . " = '"  . $value  ."'";
            }
        }

        $select = count($fields) > 0 ?  implode(' , ', $fields) : '*';

        $query =  "SELECT " . $select . " FROM " . $table;

        if( count($where) > 0) {
            $query .= " WHERE " .  implode(' ' . $operator . ' ', $where);
        }

        return $this->db->get_results($this->db->prepare($query, []));
    }

    /**
     * @param string $table
     * @param array $conditions
     * @return bool
     */
    public function remove(string $table, array $conditions): bool
    {
        $this->db->delete($table, $conditions);

        return true;
    }

    /**
     * @param string $table
     * @param array $columns
     * @param string|null $primary
     * @return bool
     */
    public function addTable(string $table, array $columns, string $primary = null): bool
    {
        if (!$this->exists($table)) {
            $cols = [];

            foreach ($columns as $key => $value) {
                $cols[] = $key . ' ' . $value;
            }

            $query = "CREATE TABLE " . $table . " ( ";

            $query .= implode(', ', $cols);

            if($primary) {
                $query .= ", PRIMARY KEY ( " . $primary . " )";
            }

            $query .= ")";


            dbDelta($query);
        }

        return true;
    }

    /**
     * @param string $table
     * @return bool
     */
    public function deleteTable(string $table): bool
    {
        if ($this->exists($table)) {
            $query = "DROP TABLE "  . $table;
            $this->db->query($query);
        }

        return true;
    }
}