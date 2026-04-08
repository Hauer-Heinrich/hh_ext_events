<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtEvents\Domain\Model;

use \TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use FriendsOfTYPO3\TtAddress\Domain\Model\Address;

class Date extends AbstractEntity {

    protected ?\DateTime $startDate = null;
    protected ?\DateTime $endDate = null;

    protected ?\DateTime $startEntryTime = null;

    public function __construct() {
    }



    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void {
    }



    public function getStartDate(): ?\DateTime {
        return $this->startDate;
    }
    public function setStartDate(\DateTime $startDate): void {
        $this->startDate = $startDate;
    }



    public function getEndDate(): ?\DateTime {
        return $this->endDate;
    }
    public function setEndDate(\DateTime $endDate): void {
        $this->endDate = $endDate;
    }



    public function getStartEntryTime(): ?\DateTime {
        return $this->startEntryTime;
    }
    public function setStartEntryTime(\DateTime $startEntryTime): void {
        $this->startEntryTime = $startEntryTime;
    }
}
