<?php

/**
 * Class WP_ImportPropertyModel
 */
class WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setId(int $id): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setStatus(string $status): WP_ImportPropertyModel
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param DateTime $createdAt
     *
     * @return WP_ImportPropertyModel
     */
    public function setCreatedAt(DateTime $createdAt): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setAction(string $action): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setType(string $type): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setInStockDaysAgo($inStockDaysAgo): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setFilters($filters): WP_ImportPropertyModel
    {
        $this->filters = $filters;

        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     *
     * @return WP_ImportPropertyModel
     */
    public function setUserId(int $userId): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setInsertCount(int $insertCount): WP_ImportPropertyModel
    {
        $this->insertCount = $insertCount;

        return $this;
    }

    /**
     * @return WP_ImportPropertyModel
     */
    public function increaseInsertCount(): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setUpdateCount(int $updateCount): WP_ImportPropertyModel
    {
        $this->updateCount = $updateCount;

        return $this;
    }

    /**
     * @return WP_ImportPropertyModel
     */
    public function increaseUpdateCount(): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setTotalCount(int $totalCount): WP_ImportPropertyModel
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return WP_ImportPropertyModel
     */
    public function increaseTotalCount(): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setDoneCount(int $doneCount): WP_ImportPropertyModel
    {
        $this->doneCount = $doneCount;

        return $this;
    }

    /**
     * @return WP_ImportPropertyModel
     */
    public function increaseDoneCount(): WP_ImportPropertyModel
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
     * @return WP_ImportPropertyModel
     */
    public function setDescription($description): WP_ImportPropertyModel
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string
     */
    private function getUploadPath(): string
    {
        return get_site_url().'/wp-content/uploads';
    }

    /**
     * @return string
     */
    private function getImportPath(): string
    {
        return $this->getUploadPath() . '/cw-import-products/';
    }

    /**
     * @return string
     */
    public function getDetailsPath(): string
    {
        return $this->getImportPath() . $this->getId() . '-import.csv';
    }
    
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