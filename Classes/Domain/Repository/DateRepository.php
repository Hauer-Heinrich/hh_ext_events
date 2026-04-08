<?php
declare(strict_types=1);
namespace HauerHeinrich\HhExtEvents\Domain\Repository;

/***
 *
 * This file is part of the "hh_ext_events" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2026 HH <web@hauer-heinrich.de>, Werbeagentur Hauer Heinrich
 *
 ***/

// use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

use InvalidArgumentException;
use \TYPO3\CMS\Core\Context\Context;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Core\Database\ConnectionPool;
use \TYPO3\CMS\Core\Database\Query\QueryBuilder;
use \TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use \TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use \TYPO3\CMS\Core\Database\Connection;
use \Doctrine\DBAL\ArrayParameterType;
use \TYPO3\CMS\Extbase\Persistence\Repository;
use \TYPO3\CMS\Extbase\Persistence\QueryInterface;
use \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class DateRepository extends Repository {

    protected const TABLE = 'tx_hhextevents_domain_model_date';
    protected int $sysLanguageUid = 0;
    protected array $searchResultFields = [ 'date.uid', 'date.pid', 'date.parentid', 'date.parenttable', 'date.start_date', 'date.end_date' ];
    private QueryBuilder $queryBuilderSearch;

    public function __construct(
        protected ConnectionPool $connectionPool
    ) {
        parent::__construct();
    }

    public function initializeObject() {
        $querySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);
        $querySettings->setRespectStoragePage(FALSE);
        $querySettings->setRespectSysLanguage(TRUE);
        $this->setDefaultQuerySettings($querySettings);

        $this->sysLanguageUid = GeneralUtility::makeInstance(Context::class)->getPropertyFromAspect('language', 'contentId');
    }

    public function findByParent(array $parentUid, string $parentTable): array {
        if (empty($parentUid) || empty($parentTable)) {
            throw new InvalidArgumentException('Parent UID and Parent Table must be provided', 1600000000);
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE);
        $rows = $queryBuilder
            ->select(...$this->searchResultFields)
            ->from(self::TABLE, 'date')
            ->where(
                $queryBuilder->expr()->and(
                    $queryBuilder->expr()->eq('date.parenttable', $queryBuilder->createNamedParameter($parentTable)),
                    $queryBuilder->expr()->in('date.parentid', $queryBuilder->createNamedParameter($parentUid, Connection::PARAM_INT_ARRAY))
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        return $rows;
    }
}
