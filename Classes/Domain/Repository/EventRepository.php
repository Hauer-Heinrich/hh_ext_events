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
use \TYPO3\CMS\Core\Resource\FileReference;
use \TYPO3\CMS\Core\Resource\FileRepository;
use \Doctrine\DBAL\ArrayParameterType;
use \TYPO3\CMS\Extbase\Persistence\Repository;
use \TYPO3\CMS\Extbase\Persistence\QueryInterface;
use \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class EventRepository extends Repository {

    protected const TABLE = 'tx_hhextevents_domain_model_event';
    protected int $sysLanguageUid = 0;
    protected array $searchResultFields = [ 'event.uid', 'event.pid', 'event.title' ];
    private QueryBuilder $queryBuilderSearch;

    public function __construct(
        protected ConnectionPool $connectionPool,
        private readonly FileRepository $fileRepository,
        protected DateRepository $dateRepository,
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

    /* ------------------------------ */
    /* ------ Search section -------- */
    /* ------------------------------ */

    /**
     * setSearchResultFields
     * Sets the database fields for the "select" query! Which fields are returned.
     *
     * @param  array $searchResultFields - automatically prefixed with the alias 'event' for the databasetable 'tx_hhextevents_domain_model_event'
     * @return EventRepository
     */
    public function setSearchResultFields(array $searchResultFields): EventRepository {
        $this->searchResultFields = \array_map(fn($item) => 'event.'.$item, $searchResultFields);

        return $this;
    }

    public function initSearch(): EventRepository {
        $this->queryBuilderSearch = $this->connectionPool
            ->getQueryBuilderForTable(self::TABLE);

        $this->queryBuilderSearch
            ->select(...$this->searchResultFields)
            ->from(self::TABLE, 'event')
            ->where(
                $this->queryBuilderSearch->expr()->eq(
                    'event.sys_language_uid',
                    $this->queryBuilderSearch->createNamedParameter($this->sysLanguageUid, Connection::PARAM_INT)
                ),
            );

        return $this;
    }

    public function searchExecute(): array {
        // $typo3DbQueryParser = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser::class);
        // $queryBuilder = $typo3DbQueryParser->convertQueryToDoctrineQueryBuilder($query);
        // DebuggerUtility::var_dump($this->queryBuilderSearch->getSQL());
        // DebuggerUtility::var_dump($this->queryBuilderSearch->getParameters());

        $this->queryBuilderSearch->groupBy('event.uid');
        return $this->queryBuilderSearch->executeQuery()->fetchAllAssociative();
    }

    public function searchOrderByGivenUids(array $uids): EventRepository {
        if (empty($uids)) {
            return $this;
        }

        $uidList = implode(',', array_map('intval', array_reverse($uids)));

        $concreteQueryBuilder = $this->queryBuilderSearch->getConcreteQueryBuilder();
        $concreteQueryBuilder
            // proper quoting must be done manually on the concrete query builder
            ->orderBy('FIELD(' .$this->queryBuilderSearch->quoteIdentifier('event.uid') . ',' . $uidList . ')', 'DESC')
            ->addOrderBy($this->queryBuilderSearch->quoteIdentifier('event.uid'), 'DESC');

        return $this;
    }

    /**
     * e. g. used for pagination
     */
    public function searchSorting(string $orderByField = 'event.sorting', string $orderingDirection = 'ASC', array $uids = []): EventRepository {
        if($orderByField === 'event.uid' && !empty($uids)) {
            return $this->searchOrderByGivenUids($uids);
        }

        $safeOrderingDirection = ($orderingDirection === 'ASC') ? 'ASC' : 'DESC'; // ->orderBy() not escaping 'direction' param.
        $this->queryBuilderSearch->orderBy($orderByField, $safeOrderingDirection);

        return $this;
    }

    /**
     * e. g. used for pagination
     */
    public function searchWithOffset(int $offset): EventRepository {
        $this->queryBuilderSearch->setFirstResult($offset);

        return $this;
    }

    /**
     * e. g. used for pagination
     */
    public function searchWithLimit(int $limit): EventRepository {
        $this->queryBuilderSearch->setMaxResults($limit);

        return $this;
    }

    /**
     * Counts the total number of results for the current search query.
     * This method should be called BEFORE applying any limit, offset or sorting.
     */
    public function searchCount(): int {
        $countQueryBuilder = clone $this->queryBuilderSearch;

        // Wichtig: Entferne eventuell schon gesetzte Sortierungen, Limits etc.
        // und ersetze das SELECT durch ein COUNT.
        // COUNT(DISTINCT event.uid) ist sicherer als COUNT(*), wenn du JOINs hast,
        // um zu verhindern, dass ein Datensatz mehrfach gezählt wird.

        // Hole den ExpressionBuilder vom QueryBuilder
        // $expressionBuilder = $countQueryBuilder->expr();

        // $countQueryBuilder
        //     ->select('event.uid')
        //     ->resetOrderBy()
        //     ->resetGroupBy()
        //     ->select($expressionBuilder->countDistinct('event.uid')); // <-- KORREKTUR HIER

        $countQueryBuilder
            ->select('event.uid')
            ->resetOrderBy()
            ->resetGroupBy()
            // ->count('DISTINCT uid');
            ->selectLiteral(sprintf('COUNT(DISTINCT %s.%s)', 'event', 'uid'));

        return (int)$countQueryBuilder->executeQuery()->fetchOne();
    }

    public function getByUid(int $uid): EventRepository {
        $this->queryBuilderSearch->andWhere(
            $this->queryBuilderSearch->expr()->eq('event.uid', $this->queryBuilderSearch->createNamedParameter($uid, Connection::PARAM_INT))
        );

        return $this;
    }

    public function getByUids(array $uids): EventRepository {
        $this->queryBuilderSearch->andWhere(
            $this->queryBuilderSearch->expr()->in('event.uid', $this->queryBuilderSearch->createNamedParameter($uids, ArrayParameterType::INTEGER))
        );

        return $this;
    }

    public function getByPids(array $pids): EventRepository {
        $this->queryBuilderSearch->andWhere(
            $this->queryBuilderSearch->expr()->in('event.pid', $this->queryBuilderSearch->createNamedParameter($pids, ArrayParameterType::INTEGER))
        );

        return $this;
    }

    /**
     * Filtert Events nach Kategorien
     *
     * @param array $categoryUids - UIDs der ausgewählten Kategorien
     * @param string $matchType - 'OR' (mindestens eine Kategorie) oder 'AND' (alle Kategorien)
     * @return EventRepository
     */
    public function getByCategories(array $categoryUids, string $matchType = 'OR'): EventRepository {
        if (empty($categoryUids)) {
            return $this;
        }

        // Validiere $matchType
        $matchType = strtoupper($matchType);
        if (!in_array($matchType, ['OR', 'AND'])) {
            $matchType = 'OR';
        }

        // Joins zu Category MM-Tabelle
        $this->queryBuilderSearch
            ->leftJoin(
                'event',
                'sys_category_record_mm',
                'cat_mm',
                $this->queryBuilderSearch->expr()->eq(
                    'event.uid',
                    $this->queryBuilderSearch->quoteIdentifier('cat_mm.uid_foreign')
                )
            )
            ->leftJoin(
                'cat_mm',
                'sys_category',
                'sys_category',
                $this->queryBuilderSearch->expr()->eq(
                    'sys_category.uid',
                    $this->queryBuilderSearch->quoteIdentifier('cat_mm.uid_local')
                )
            )
            ->andWhere(
                $this->queryBuilderSearch->expr()->eq(
                    'cat_mm.tablenames',
                    $this->queryBuilderSearch->createNamedParameter(self::TABLE)
                )
            )
            ->andWhere(
                $this->queryBuilderSearch->expr()->eq(
                    'cat_mm.fieldname',
                    $this->queryBuilderSearch->createNamedParameter('categories')
                )
            )
            ->andWhere(
                $this->queryBuilderSearch->expr()->in(
                    'sys_category.uid',
                    $this->queryBuilderSearch->createNamedParameter($categoryUids, \TYPO3\CMS\Core\Database\Connection::PARAM_INT_ARRAY)
                )
            );

        // AND: Event muss ALLE Kategorien haben
        if ($matchType === 'AND') {
            $this->queryBuilderSearch->having(
                $this->queryBuilderSearch->expr()->gte(
                    'COUNT(DISTINCT sys_category.uid)',
                    count($categoryUids)
                )
            );
        }
        // OR: Event muss MINDESTENS EINE Kategorie haben (Standard, kein zusätzlicher Filter nötig)

        return $this;
    }

    public function addFileReferences(array &$events, array $fields = ['media']): array {
        foreach ($events as $key => $event) {
            foreach ($fields as $field) {
                if (isset($event[$field]) && !empty($event[$field])) {
                    $fileReferences = $this->fileRepository->findByRelation(
                        'tx_hhextevents_domain_model_event',
                        $field,
                        (int)$event['uid']
                    );

                    $events[$key][$field] = $fileReferences;
                }
            }
        }

        return $events;
    }

    public function addEventDates(array &$events): array {
        // Sammle alle Event-UIDs
        $eventUids = array_column($events, 'uid');

        if (empty($eventUids)) {
            return $events;
        }

        // Lade ALLE Dates in EINER Query
        // IN-Abfrage statt einzelne Abfragen
        $allDates = $this->dateRepository->findByParent(
            parentUid: $eventUids,
            parentTable: self::TABLE
        );

        // Gruppiere Dates nach Event-UID
        $datesByEventUid = [];
        foreach ($allDates as $date) {
            if (!isset($datesByEventUid[$date['parentid']])) {
                $datesByEventUid[$date['parentid']] = [];
            }
            $datesByEventUid[$date['parentid']][] = $date;
        }

        // Füge Dates zu Events hinzu
        foreach ($events as $key => $event) {
            if (isset($event['uid']) && isset($datesByEventUid[$event['uid']])) {
                $events[$key]['event_dates'] = $datesByEventUid[$event['uid']];
            } else {
                $events[$key]['event_dates'] = [];
            }
        }

        return $events;
    }

    public function addEventOrganizers(array &$events): array {
        // Sammle Event-UIDs
        $eventUids = array_column($events, 'uid');

        // Lade ALLE MM-Einträge für diese Events in EINER Query
        $queryBuilderMM = $this->connectionPool->getQueryBuilderForTable('tx_hhextevents_event_organizer_mm');
        $mmRecords = $queryBuilderMM
            ->select('uid_local', 'uid_foreign', 'sorting')
            ->from('tx_hhextevents_event_organizer_mm')
            ->where(
                $queryBuilderMM
                    ->expr()->in(
                        'uid_local',
                        $queryBuilderMM
                            ->createNamedParameter($eventUids, Connection::PARAM_INT_ARRAY)
                    )
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        // Sammle alle Location-UIDs
        $mmRecordsUids = array_unique(array_column($mmRecords, 'uid_foreign'));

        // Lade ALLE Locations in EINER Query
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_address');
        $items = $queryBuilder
            ->select('*')
            ->from('tt_address')
            ->where(
                $queryBuilder->expr()->in(
                        'uid',
                        $queryBuilder->createNamedParameter($mmRecordsUids, Connection::PARAM_INT_ARRAY)
                    )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        // Index Locations nach UID
        $itemsById = array_column($items, null, 'uid');

        // Gruppiere MM-Records nach Event-UID
        $itemsByEventUid = [];
        foreach ($mmRecords as $mm) {
            $eventUid = $mm['uid_local'];
            if (!isset($itemsByEventUid[$eventUid])) {
                $itemsByEventUid[$eventUid] = [];
            }

            if (isset($itemsById[$mm['uid_foreign']])) {
                $itemsByEventUid[$eventUid][] = $itemsById[$mm['uid_foreign']];
            }
        }

        // Füge Organizers zu Events hinzu
        foreach ($events as $key => $event) {
            $events[$key]['organizers'] = $itemsByEventUid[$event['uid']] ?? [];
        }

        return $events;
    }

    public function addEventLocations(array &$events): array{
        // Sammle Event-UIDs
        $eventUids = array_column($events, 'uid');

        // Lade ALLE MM-Einträge für diese Events in EINER Query
        $queryBuilderMM = $this->connectionPool->getQueryBuilderForTable('tx_hhextevents_event_location_mm');
        $mmRecords = $queryBuilderMM
            ->select('uid_local', 'uid_foreign', 'sorting')
            ->from('tx_hhextevents_event_location_mm')
            ->where(
                $queryBuilderMM
                    ->expr()->in(
                        'uid_local',
                        $queryBuilderMM
                            ->createNamedParameter($eventUids, Connection::PARAM_INT_ARRAY)
                    )
            )
            ->orderBy('sorting', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative();

        // Sammle alle Location-UIDs
        $mmRecordsUids = array_unique(array_column($mmRecords, 'uid_foreign'));

        // Lade ALLE Locations in EINER Query
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_address');
        $items = $queryBuilder
            ->select('*')
            ->from('tt_address')
            ->where(
                $queryBuilder->expr()->in(
                        'uid',
                        $queryBuilder->createNamedParameter($mmRecordsUids, Connection::PARAM_INT_ARRAY)
                    )
            )
            ->executeQuery()
            ->fetchAllAssociative();

        // Index Locations nach UID
        $itemsById = array_column($items, null, 'uid');

        // Gruppiere MM-Records nach Event-UID
        $itemsByEventUid = [];
        foreach ($mmRecords as $mm) {
            $eventUid = $mm['uid_local'];
            if (!isset($itemsByEventUid[$eventUid])) {
                $itemsByEventUid[$eventUid] = [];
            }

            if (isset($itemsById[$mm['uid_foreign']])) {
                $itemsByEventUid[$eventUid][] = $itemsById[$mm['uid_foreign']];
            }
        }

        // Füge Locations zu Events hinzu
        foreach ($events as $key => $event) {
            $events[$key]['locations'] = $itemsByEventUid[$event['uid']] ?? [];
        }

        return $events;
    }
}
