<?php
namespace CodesWholesaleFramework\Database\Models;

class PostbackImportModel
{
    const
        STATUS_NEW = 'NEW',
        STATUS_AWAITING = 'AWAITING',
        STATUS_IN_PROGRESS = 'IN_PROGRESS',
        STATUS_DONE = 'FINIHED',
        STATUS_CANCEL = 'CANCEL',
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
     * @var string
     */
    protected $externalId;
    
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
     * @return PostbackImportModel
     */
    public function setId(int $id): PostbackImportModel
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalId()
    {
        return $this->externalId;
    }

    /**
     * @param string $externalId
     *
     * @return PostbackImportModel
     */
    public function setExternalId(string $externalId = null): PostbackImportModel
    {
        $this->externalId = $externalId;

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
     * @return PostbackImportModel
     */
    public function setStatus(string $status): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setCreatedAt(\DateTime $createdAt): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setAction(string $action): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setType(string $type): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setInStockDaysAgo($inStockDaysAgo): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setFilters($filters): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setUserId(int $userId = null): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setInsertCount(int $insertCount): PostbackImportModel
    {
        $this->insertCount = $insertCount;

        return $this;
    }

    /**
     * @return PostbackImportModel
     */
    public function increaseInsertCount(): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setUpdateCount(int $updateCount): PostbackImportModel
    {
        $this->updateCount = $updateCount;

        return $this;
    }

    /**
     * @return PostbackImportModel
     */
    public function increaseUpdateCount(): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setTotalCount(int $totalCount): PostbackImportModel
    {
        $this->totalCount = $totalCount;

        return $this;
    }

    /**
     * @return PostbackImportModel
     */
    public function increaseTotalCount(): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setDoneCount(int $doneCount): PostbackImportModel
    {
        $this->doneCount = $doneCount;

        return $this;
    }

    /**
     * @return PostbackImportModel
     */
    public function increaseDoneCount(): PostbackImportModel
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
     * @return PostbackImportModel
     */
    public function setDescription($description): PostbackImportModel
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
            $filters['platforms'] = $this->getFilters()['platform'];
        }

        if (0 !== count($this->getFilters()['region'])) {
            $filters['regions'] = $this->getFilters()['region'];
        }

        if (0 !== count($this->getFilters()['language'])) {
            $filters['languages'] = $this->getFilters()['language'];
        }

        if (null != $this->getInStockDaysAgo()) {
            $filters['inStockDaysAgo'] = $this->getInStockDaysAgo();
        }

        return $filters;
    }
}
