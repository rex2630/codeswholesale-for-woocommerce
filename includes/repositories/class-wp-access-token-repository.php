<?php

use CodesWholesale\ClientBuilder;

/**
 * Class WP_AccessTokenRepository
 */
class WP_AccessTokenRepository extends WP_AbstractRepository
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
                token_type       VARCHAR(50),
                expires_in       VARCHAR(55),
                access_token     VARCHAR(255),
                issue_time       VARCHAR(55),
                PRIMARY KEY (id)
            )';

            $query = sprintf('%s %s;', $query, $charset_collate);

            dbDelta($query);
        }

        return true;
    }

    /**
     * deleteToken
     */
    public function deleteToken()
    {
        global $wpdb;

        $wpdb->delete($this->getTableName(), [
            'client_config_id' => ClientBuilder::CONFIGURATION_ID
        ]);
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'access_tokens';
    }
}