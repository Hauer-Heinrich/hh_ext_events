<?php
declare(strict_types=1);
namespace HauerHeinrich\HhExtEvents\Helper;

/***
 *
 * This file is part of the "hh_ext_events" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2026 HH <web@hauer-heinrich._de>, Werbeagentur Hauer Heinrich
 *
 ***/

use \TYPO3\CMS\Core\Pagination\SimplePagination;
use \TYPO3\CMS\Extbase\Pagination\QueryResultPaginator;
use \TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

final class PaginationHelper {

    public function getPaginator(array|QueryResultInterface $items, int $currentPage = 1, int $itemsPerPage = 10) : QueryResultPaginator|null {
        return new QueryResultPaginator($items, $currentPage, $itemsPerPage);
    }

    public function getPagination(QueryResultPaginator $paginator) : array {
        $simplePagination = new SimplePagination($paginator);
        $firstPage = $simplePagination->getFirstPageNumber();
        $lastPage = $simplePagination->getLastPageNumber();

        return [
            'lastPageNumber' => $lastPage,
            'firstPageNumber' => $firstPage,
            'nextPageNumber' => $simplePagination->getNextPageNumber(),
            'previousPageNumber' => $simplePagination->getPreviousPageNumber(),
            'startRecordNumber' => $simplePagination->getStartRecordNumber(),
            'endRecordNumber' => $simplePagination->getEndRecordNumber(),
            'currentPageNumber' => $paginator->getCurrentPageNumber(),
            'pages' => range($firstPage, $lastPage)
        ];
    }
}
