<?php
namespace CodesWholesaleFramework\Database\Repositories;

use CodesWholesale\ClientBuilder;

class AccessTokenRepository extends Repository
{
    /**
     * @return bool
     */
    public function createTable():bool
    {
        $this->db->addTable($this->getTableName(),[
            'id' => 'INT NOT NULL AUTO_INCREMENT',
            'client_config_id' => 'VARCHAR(50)',
            'user_id' => 'VARCHAR(255)',
            'scope' => 'VARCHAR(20)',
            'token_type' => 'VARCHAR(50)',
            'expires_in' => 'VARCHAR(55)',
            'access_token' => 'VARCHAR(55)',
            'issue_time' => 'VARCHAR(55)',
        ], 'id');

        return true;
    }

    /**
     * deleteToken
     *
     * @return bool
     */
    public function deleteToken()
    {
        $this->db->remove($this->getTableName(),[
            'client_config_id' => ClientBuilder::CONFIGURATION_ID
        ]);

        return true;
    }

    /**
     * @return string
     */
    protected function getName(): string
    {
        return 'access_tokens';
    }
}