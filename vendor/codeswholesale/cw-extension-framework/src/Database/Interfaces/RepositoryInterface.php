<?php
namespace CodesWholesaleFramework\Database\Interfaces;

/**
 * Interface Repository
 */
interface RepositoryInterface
{
    const CW_PREFIX = 'codeswholesale_';

    /**
     * @return bool
     */
    public function createTable(): bool;
}
