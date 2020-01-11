<?php
namespace CodesWholesaleFramework\Database\Interfaces;

/**
 * Interface DbManagerInterface
 */
interface DbManagerInterface
{
    public function getPrefix();

    public function sql(string $sql): bool;
    
    public function exists(string $table): bool;

    public function insert(string $table, array $fields): bool;

    public function update(string $table, array $fields, array $conditions): bool;

    public function get(string $table, array $fields = [], array $conditions, string $operator = 'AND');

    public function remove(string $table, array $conditions): bool;

    public function addTable(string $table, array $columns, string $primary = null): bool;

    public function deleteTable(string $table): bool;
}