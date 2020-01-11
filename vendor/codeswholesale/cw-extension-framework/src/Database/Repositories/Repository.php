<?php
namespace CodesWholesaleFramework\Database\Repositories;

use CodesWholesaleFramework\Database\Interfaces\DbManagerInterface;
use CodesWholesaleFramework\Database\Interfaces\RepositoryInterface;

/**
 * Class Repository
 */
abstract class Repository implements RepositoryInterface
{

    /**
     * @var DbManagerInterface
     */
    protected $db;

    /**
     * Repository constructor.
     * @param DbManagerInterface $db
     */
    public function __construct(DbManagerInterface $db)
    {
        $this->db = $db;
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
        return sprintf('%s%s', self::getGeneralPrefix(), $this->getName());
    }

    /**
     * @return string
     */
    public function getGeneralPrefix(): string
    {
        return sprintf('%s%s',  $this->db->getPrefix(),self::CW_PREFIX);
    }

    /**
     * @return bool
     */
    public function deleteTable() {
        return $this->db->deleteTable($this->getTableName());
    }
}