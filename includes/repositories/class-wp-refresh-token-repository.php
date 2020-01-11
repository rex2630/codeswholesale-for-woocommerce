<?php

/**
 * Class WP_RefreshTokenRepository
 */
class WP_RefreshTokenRepository extends WP_AbstractRepository
{
    /**
     * @return bool
     */
    public function createTable(): bool
    {
        global $wpdb;

        $tableName = $this->getTableName();

        if (!$this->exists()) {
            $charset_collate = $wpdb->get_charset_collate();

            $query = 'CREATE TABLE ' . $tableName . ' (
                id               INT NOT NULL AUTO_INCREMENT,
                client_config_id VARCHAR(50),
                user_id          VARCHAR(255),
                scope            VARCHAR(20),
                refresh_token    VARCHAR(50),
                issue_time       VARCHAR(55),
                PRIMARY KEY (id)
            )';

            $query = sprintf('%s %s;', $query, $charset_collate);

            dbDelta($query);
        }

        return true;
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'refresh_tokens';
    }
}