<?php
namespace CodesWholesaleFramework\Database\Repositories;

class RefreshTokenRepository extends Repository {
    /**
     * @return bool
     */
    public function createTable(): bool
    {
        $this->db->addTable($this->getTableName(), [
            'client_config_id' => ' VARCHAR(50)',
            'user_id' => ' VARCHAR(255)',
            'scope' => ' VARCHAR(20)',
            'refresh_token' => ' VARCHAR(50)',
            'issue_time' => ' VARCHAR(55)',
        ]);

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