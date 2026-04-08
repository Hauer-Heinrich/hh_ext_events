<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtEvents\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use HauerHeinrich\HhExtEvents\Domain\Model\Event;
use HauerHeinrich\HhExtEvents\Domain\Repository\EventRepository;
use HauerHeinrich\HhExtEvents\Controller\GenericController;
use HauerHeinrich\HhExtEvents\Helper\RequestHelper;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class EventController extends GenericController {

    public function __construct(
        protected RequestHelper $requestHelper,
        protected readonly EventRepository $eventRepository
    ) {}

    public function listAction(): ResponseInterface {
        $events = [];

        $this->eventRepository->setSearchResultFields([
            'uid', 'pid', 'crdate', 'title', 'teaser', 'event_dates', 'locations', 'organizers', 'slug', 'categories', 'teaser_media'
        ]);
        $search = $this->eventRepository->initSearch();


        if(isset($this->settings['selectedItems']) && !empty($this->settings['selectedItems'])) {
            if(is_string($this->settings['selectedItems'])) {
                $selectedItems = \explode(',', $this->settings['selectedItems']);
            } else {
                $selectedItems = [\intval($this->settings['selectedItems'])];
            }
            $search->getByUids($selectedItems);
        } else {
            if(isset($this->settings['startingpoint']) && !empty($this->settings['startingpoint'])) {
                if(\is_int($this->settings['startingpoint'])) {
                    $startingPoints = [$this->settings['startingpoint']];
                } else {
                    $startingPoints = \explode(',', $this->settings['startingpoint']);
                }

                $search->getByPids($startingPoints);
            }

            if(isset($this->settings['categories']) && !empty($this->settings['categories'])) {
                $matchType = 'OR';
                if(isset($this->settings['categoryMatchType']) && !empty($this->settings['categoryMatchType'])) {
                    $matchType = strtoupper($this->settings['categoryMatchType']);
                }

                if(is_string($this->settings['categories'])) {
                    $categoryUids = \explode(',', $this->settings['categories']);
                } else {
                    $categoryUids = [\intval($this->settings['categories'])];
                }
                $search->getByCategories($categoryUids, $matchType);
            }
        }

        $totalItems = $this->eventRepository->searchCount();

        // Pagination
        $pagination = [];
        $itemsPerPage = \intval($this->settings['showItemsPerPage'] ?? 18);
        $offset = 0;
        $showPagination = false;
        if(
            \intval(($this->settings['showPaginationBefore'] ?? 0)) === 1
            || \intval(($this->settings['showPaginationAfter'] ?? 0)) === 1
        ) {
            $showPagination = true;
        }

        if ($showPagination === true) {
            $pagination = $this->buildPagination(totalItems: $totalItems, itemsPerPage: $itemsPerPage);
            $offset = (int)($pagination['offset'] ?? 0);
            $offset = $offset >= 0 ? $offset : 0;
        }

        // TODO: Sorting from BE Plugin value, is used if no user FE sorting is given
        // $customPluginSort = isset($this->settings['selectedItems']) ? \explode(',', $this->settings['selectedItems']) : [];

        // Bei aktiver Pagination Offset+Limit setzen
        if ($showPagination === true) {
            $search->searchWithOffset($offset);
            $search->searchWithLimit($itemsPerPage);
        }

        $events = $search->searchExecute();

        // Mit zugehörigen Daten / Relationen anreichern
        $this->eventRepository->addEventDates($events);
        $this->eventRepository->addEventOrganizers($events);
        $this->eventRepository->addEventLocations($events);
        $this->eventRepository->addFileReferences($events, ['teaser_media']);

        // JSON output - needs TypoScript!
        // Call like https://www.domain.tld/mySiteWithListPlugin?type=813475
        // or if you have site routing in place: https://www.domain.tld/mySiteWithListPlugin/events.json
        if(
            (isset($this->request->getQueryParams()['type']) && \intval($this->request->getQueryParams()['type']) === 813475)
            || ($this->request->getAttribute("routing")->getPageType() !== NULL && intval($this->request->getAttribute("routing")->getPageType()) === 813475)
        ) {
            return $this->jsonResponse(\json_encode([
                'view'   => 'list',
                'events' => $events,  // json_encode ruft jsonSerialize() (Domain/Model) automatisch auf
            ]));
        }

        if(empty($events)) {
            $this->addFlashMessage(
                'Es wurden leider keine Veranstaltungen gefunden.',
                'Info',
                ContextualFeedbackSeverity::INFO
            );
        }

        $this->view->assignMultiple([
            'site' => $this->request->getAttribute('site'),
            'data' => $this->contentData,
            'events' => $events,
        ]);

        return $this->htmlResponse();
    }

    public function detailAction(Event $event): ResponseInterface {
        $schemaJson = [
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->getTitle(),
            'description' => $event->getDescription(),
            'identifier' => $event->getSlug().'-'.$event->getUid(),
        ];

        if(isset($event->getEventAttendanceModeEnum()['value']->name)) {
            $schemaJson['eventAttendanceMode'] = 'https://schema.org/'.ucfirst(strtolower($event->getEventAttendanceModeEnum()['value']->name)).'EventAttendanceMode';
        }

        if($event->getTeaserMedia()->count() > 0) {
            try {
                $relativeUrl = $event->getTeaserMedia()[0]->getOriginalResource()->getPublicUrl();
                $uri = $this->request->getUri();
                $absoluteUrl = $uri->getScheme() . '://' . $uri->getAuthority() . $relativeUrl;

                $schemaJson['image'] = $absoluteUrl;
            } catch (\Throwable $th) {
                //throw $th;
                // TODO: Log error
            }
        }

        if($event->getEventDates()->count() > 0) {
            if($event->getEventDates()->count() === 1) {
                foreach($event->getEventDates() as $date) {
                    if($date->getStartDate() !== null) {
                        $schemaJson['startDate'] = $date->getStartDate()->format('c');
                    }
                    if($date->getEndDate() !== null) {
                        $schemaJson['endDate'] = $date->getEndDate()->format('c');
                    }
                }
            } else {
                $schemaJson['startDate'] = $event->getEventDates()[0]->getStartDate()->format('c'); // for google :(
                $schemaJson['eventSchedule'] = [];

                foreach($event->getEventDates() as $date) {
                    $eventSchedule = [
                        '@type' => 'Schedule',
                    ];
                    if($date->getStartDate() !== null) {
                        $eventSchedule['startDate'] = $date->getStartDate()->format('c');
                    }
                    if($date->getEndDate() !== null) {
                        $eventSchedule['endDate'] = $date->getEndDate()->format('c');
                    }

                    $schemaJson['eventSchedule'][] = $eventSchedule;
                }
            }
        }

        if($event->getLocations()->count() > 0) {
            $location = $event->getLocations()[0];
            $schemaJson['location'] = [
                '@type' => 'Place',
                'name' => $location->getCompany(),
                'address' => [
                    '@type' => 'PostalAddress',
                ],
            ];
            if($location->getAddress()) {
                $schemaJson['location']['address']['streetAddress'] = $location->getAddress();
            }
            if($location->getZip()) {
                $schemaJson['location']['address']['postalCode'] = $location->getZip();
            }
            if($location->getCity()) {
                $schemaJson['location']['address']['addressLocality'] = $location->getCity();
            }
            if($location->getCountry()) {
                $schemaJson['location']['address']['addressCountry'] = $location->getCountry();
            }
            if($location->getWww()) {
                $schemaJson['location']['address']['url'] = $location->getWww();
            }
        }

        if($event->getOrganizers()->count() > 0) {
            $organizer = $event->getOrganizers()[0];
            $schemaJson['organizer'] = [
                '@type' => 'Organization',
                'name' => $organizer->getCompany(),
            ];
            if($organizer->getWww()) {
                $schemaJson['organizer']['url'] = $organizer->getWww();
            }
        }

        $this->view->assignMultiple([
            'site' => $this->request->getAttribute('site'),
            'data' => $this->contentData,
            'event' => $event,
            'schemaJson' => \json_encode($schemaJson) ?? []
        ]);

        return $this->htmlResponse();
    }

    public function calendarAction(?int $year = null, ?int $month = null, ?int $day = null): ResponseInterface {
        $year ??= (int)date('Y');
        $month ??= (int)date('m');
        $day ??= (int)date('d');

        $events = $this->eventRepository->findByDate($year, $month, $day);

        $this->view->assignMultiple([
            'events' => $events,
            'year' => $year,
            'month' => $month,
        ]);

        return $this->htmlResponse();
    }

    private function buildPagination(int $totalItems, int $itemsPerPage = 18): array {
        $currentPage = $this->requestHelper->getRequestParam($this->request, $this->settings, 'currentPage');
        $offset = ($currentPage - 1) * $itemsPerPage;
        $totalPages = ceil($totalItems / $itemsPerPage);
        // wie viele Seiten vor/nach der aktuellen
        $range = 2;
        $start = max(1, $currentPage - $range);
        $end = min($totalPages, $currentPage + $range);
        $allPageNumbers = range($start, $end);

        return [
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'hasNextPage' => $currentPage < $totalPages,
            'hasPreviousPage' => $currentPage > 1,
            'nextPage' => $currentPage + 1,
            'previousPage' => $currentPage - 1,
            'allPageNumbers' => $allPageNumbers,
            'offset' => $offset,
        ];
    }
}
