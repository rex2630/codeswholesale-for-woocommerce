<?php

/**
 * Interface WP_Repository
 */
interface WP_Repository
{
    const CW_PREFIX = 'codeswholesale_';

    /**
     * @return bool
     */
    public function createTable(): bool;
}