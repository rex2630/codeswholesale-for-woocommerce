<?php
namespace CodesWholesaleFramework\Database\Models;

class ImportPropertyModel
{
    const
        STATUS_NEW = 'NEW',
        STATUS_IN_PROGRESS = 'IN_PROGRESS',
        STATUS_DONE = 'DONE',
        STATUS_REJECT = 'REJECT'
    ;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $userId;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $inStockDaysAgo;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @var int
     */
    protected $insertCount = 0;

    /**
     * @var int
     */
    protected $updateCount = 0;

    /**
     * @var int
     */
    protected $totalCount = 0;

    /**
     * @var int
     */
    protected $doneCount = 0;

    /**
     * @var string
     */
    protected $status = self::STATUS_NEW;

    /**
     * @var string
     */
    protected $description;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return ImportPropertyModel
     */
    public function setId(int $id): ImportPropertyModel
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     *
     * @return ImportPropertyModel
     */
    public function setStatus(string $status): ImportPropertyModel
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     *
     * @return ImportPropertyModel
     */
    public function setCreatedAt(\DateTime $createdAt): ImportPropertyModel
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return ImportPropertyModel
     */
    public function setAction(string $action): ImportPropertyModel
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return ImportPropertyModel
     */
    public function setType(string $type): ImportPropertyModel
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getInStockDaysAgo()
    {
        return $this->inStockDaysAgo;
    }

    /**
     * @param string|null $inStockDaysAgo
     *
     * @return ImportPropertyModel
     */
    public function setInStockDaysAgo($inStockDaysAgo): ImportPropertyModel
    {
        $this->inStockDaysAgo = $inStockDaysAgo;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasFilters(): bool
    {
        return null !== $this->filters;
    }

    /**
     * @return array|null
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array|null $filters
     *
     * @return ImportPropertyModel
     */
    public function setFilters($filters): ImportPropertyModel
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return ImportPropertyModel
     */
    public function setUserId(int $userId = null): ImportPropertyModel
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * @return int
     */
    public function getInsertCount(): int
    {
        return $this->insertCount;
    }

    /**
     * @param int $insertCount
     *
     * @return ImportPropertyModel
     */
    public function setInsertCount(int $insertCount): ImportPropertyModel
    {
        $this->insertCount = $insertCount;

        return $this;
    }

    /**
     * @return ImportPropertyModel
     */
    public function increaseInsertCount(): ImportPropertyModel
    {
        $this->insertCount = $this->insertCount + 1;

        return $this;
    }

    /**
     * @return int
     */
    public function getUpdateCount(): int
    {
        return $this->updateCount;
    }

    /**
     * @param int $updateCount
     *
     * @return ImportPropertyModel
     */
    public function setUpdateCount(int $updateCount): ImportPropertyModel
    {
        $this->updateCount = $updateCount;

        return $this;
    }

    /**
     * @return ImportPropertyModel
     */
    public function increaseUpdateCount(): ImportPropertyModel
    {
        $this->updateCount = $this->updateCount + 1;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        return $this->totalCount;
    }

    /**
     * @param int $totalCount
     *
     * @return ImportPropertyModel
     */
    public function setTotalCount(int $totalCount): ImportPropertyModel
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return ImportPropertyModel
     */
    public function increaseTotalCount(): ImportPropertyModel
    {
        $this->totalCount = $this->totalCount + 1;

        return $this;
    }

    /**
     * @return int
     */
    public function getDoneCount(): int
    {
        return $this->doneCount;
    }

    /**
     * @param int $doneCount
     *
     * @return ImportPropertyModel
     */
    public function setDoneCount(int $doneCount): ImportPropertyModel
    {
        $this->doneCount = $doneCount;

        return $this;
    }

    /**
     * @return ImportPropertyModel
     */
    public function increaseDoneCount(): ImportPropertyModel
    {
        $this->doneCount = $this->doneCount + 1;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     *
     * @return ImportPropertyModel
     */
    public function setDescription($description): ImportPropertyModel
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return array
     */
    public function serializeFilters() {
        $filters = [];

        if (0 !== count($this->getFilters()['platform'])) {
            $filters['platform'] = $this->getFilters()['platform'];
        }

        if (0 !== count($this->getFilters()['region'])) {
            $filters['region'] = $this->getFilters()['region'];
        }

        if (0 !== count($this->getFilters()['language'])) {
            $filters['language'] = $this->getFilters()['language'];
        }

        if (null != $this->getInStockDaysAgo()) {
            $filters['inStockDaysAgo'] = $this->getInStockDaysAgo();
        }

        return $filters;
    }
}
