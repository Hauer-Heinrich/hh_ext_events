<?php
declare(strict_types=1);

namespace HauerHeinrich\HhExtEvents\Domain\Model;

use TYPO3\CMS\Extbase\Annotation\ORM\Cascade;
use TYPO3\CMS\Extbase\Annotation\ORM\Lazy;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use FriendsOfTYPO3\TtAddress\Domain\Model\Address;
use HauerHeinrich\HhExtEvents\Domain\Model\Date;
use HauerHeinrich\HhExtEvents\Enum\EventStatusEnum;
use HauerHeinrich\HhExtEvents\Enum\EventAttendanceModeEnum;

class Event extends AbstractEntity implements \JsonSerializable {

    protected \DateTime $crdate;
    protected \DateTime $tstamp;

    protected string $title = '';
    protected string $teaser = '';
    protected string $description = '';
    protected int $eventStatus = 1; // default to scheduled
    protected int $eventAttendanceMode = 1; // default to offline

    /**
     * @var ObjectStorage<Date>
     */
    #[Lazy]
    #[Cascade(['value' => 'remove'])]
    protected ObjectStorage $eventDates;

    protected string $slug = '';

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    #[Cascade(['value' => 'remove'])]
    protected ObjectStorage $teaserMedia;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    #[Cascade(['value' => 'remove'])]
    protected ObjectStorage $bannerMedia;

    /**
     * @var ObjectStorage<FileReference>
     */
    #[Lazy]
    #[Cascade(['value' => 'remove'])]
    protected ObjectStorage $media;

    /**
     * @var ObjectStorage<Address>
     */
    #[Lazy]
    protected ObjectStorage $locations;

    /**
     * @var ObjectStorage<Address>
     */
    #[Lazy]
    protected ObjectStorage $organizers;

    /** @var ObjectStorage<Category> */
    #[Lazy]
    protected ObjectStorage $categories;

    public function __construct() {
        $this->eventDates = new ObjectStorage();
        $this->teaserMedia = new ObjectStorage();
        $this->bannerMedia = new ObjectStorage();
        $this->media = new ObjectStorage();
        $this->categories = new ObjectStorage();
        $this->locations = new ObjectStorage();
        $this->organizers = new ObjectStorage();
    }



    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject(): void {
        $this->eventDates ??= new ObjectStorage();
        $this->teaserMedia ??= new ObjectStorage();
        $this->bannerMedia ??= new ObjectStorage();
        $this->media ??= new ObjectStorage();
        $this->categories ??= new ObjectStorage();
        $this->locations ??= new ObjectStorage();
        $this->organizers ??= new ObjectStorage();
    }



    public function getCrdate(): \DateTime {
        return $this->crdate;
    }
    public function setCrdate(\DateTime $crdate): void {
        $this->crdate = $crdate;
    }



    public function getTstamp(): \DateTime {
        return $this->tstamp;
    }
    public function setTstamp(\DateTime $tstamp): void {
        $this->tstamp = $tstamp;
    }



    public function getTitle(): string {
        return $this->title;
    }
    public function setTitle(string $title): void {
        $this->title = $title;
    }



    public function getTeaser(): string {
        return $this->teaser;
    }
    public function setTeaser(string $teaser): void {
        $this->teaser = $teaser;
    }



    public function getDescription(): string {
        return $this->description;
    }
    public function setDescription(string $description): void {
        $this->description = $description;
    }



    public function getEventStatus(): ?int {
        return $this->eventStatus;
    }
    public function setEventStatus(int $eventStatus): void {
        $this->eventStatus = $eventStatus;
    }
    public function getEventStatusEnum(): array {
        $enum = EventStatusEnum::tryFromValue($this->eventStatus);
        $enumLabel = $enum ? $enum->label() : 'Unknown';

        return [
            'value' => $enum,
            'label' => $enumLabel,
        ];
    }



    public function getEventAttendanceMode(): ?int {
        return $this->eventAttendanceMode;
    }
    public function setEventAttendanceMode(int $eventAttendanceMode): void {
        $this->eventAttendanceMode = $eventAttendanceMode;
    }
    public function getEventAttendanceModeEnum(): array {
        $enum = EventAttendanceModeEnum::tryFromValue($this->eventAttendanceMode);
        $enumLabel = $enum ? $enum->label() : 'Unknown';

        return [
            'value' => $enum,
            'label' => $enumLabel,
        ];
    }



    public function getSlug(): string {
        return $this->slug;
    }
    public function setSlug(string $slug): void {
        $this->slug = $slug;
    }



    /**
     * @return ObjectStorage<Date>|null
     */
    public function getEventDates(): ?ObjectStorage {
        return $this->eventDates;
    }
    /**
     * @param ObjectStorage<Date> $eventDates
     */
    public function setEventDates(ObjectStorage $eventDates): void {
        $this->eventDates = $eventDates;
    }
    public function addEventDate(Date $eventDates): void {
        $this->eventDates->attach($eventDates);
    }
    public function removeEventDate(Date $eventDate): void {
        $this->eventDates->detach($eventDate);
    }



    /**
     * @return ObjectStorage<FileReference>|null
     */
    public function getTeaserMedia(): ?ObjectStorage {
        return $this->teaserMedia;
    }
    /**
     * @param ObjectStorage<FileReference> $teaserMedia
     */
    public function setTeaserMedia(ObjectStorage $teaserMedia): void {
        $this->teaserMedia = $teaserMedia;
    }
    public function addTeaserMedia(FileReference $teaserMedia): void {
        $this->teaserMedia->attach($teaserMedia);
    }
    public function removeTeaserMedia(FileReference $teaserMedia): void {
        $this->teaserMedia->detach($teaserMedia);
    }



    /**
     * @return ObjectStorage<FileReference>|null
     */
    public function getBannerMedia(): ?ObjectStorage {
        return $this->bannerMedia;
    }
    /**
     * @param ObjectStorage<FileReference> $bannerMedia
     */
    public function setBannerMedia(ObjectStorage $bannerMedia): void {
        $this->bannerMedia = $bannerMedia;
    }
    public function addBannerMedia(FileReference $bannerMedia): void {
        $this->bannerMedia->attach($bannerMedia);
    }
    public function removeBannerMedia(FileReference $bannerMedia): void {
        $this->bannerMedia->detach($bannerMedia);
    }



    /**
     * @return ObjectStorage<FileReference>|null
     */
    public function getMedia(): ?ObjectStorage {
        return $this->media;
    }
    /**
     * @param ObjectStorage<FileReference> $media
     */
    public function setMedia(ObjectStorage $media): void {
        $this->media = $media;
    }
    public function addMedia(FileReference $media): void {
        $this->media->attach($media);
    }
    public function removeMedia(FileReference $media): void {
        $this->media->detach($media);
    }



    /**
     * @return ObjectStorage<Address>|null
     */
    public function getLocations(): ?ObjectStorage {
        return $this->locations;
    }
    /**
     * @param ObjectStorage<Address> $locations
     */
    public function setLocations($locations): void {
        $this->locations = $locations;
    }
    public function addLocation(Address $location): void {
        $this->getLocations()->attach($location);
    }



    /**
     * @return ObjectStorage<Address>|null
     */
    public function getOrganizers(): ?ObjectStorage {
        return $this->organizers;
    }
    /**
     * @param ObjectStorage<Address> $organizers
     */
    public function setOrganizers($organizers): void {
        $this->organizers = $organizers;
    }
    public function addOrganizer(Address $organizer): void {
        $this->getOrganizers()->attach($organizer);
    }



    /**
     * @return ObjectStorage<Category>|null
     */
    public function getCategories(): ?ObjectStorage {
        return $this->categories;
    }
    public function getFirstCategory(): ?Category {
        $categories = $this->getCategories();
        if (!is_null($categories) && $categories->count() > 0) {
            $categories->rewind();
            return $categories->current();
        }
        return null;
    }
    /**
     * @param ObjectStorage<Category> $categories
     */
    public function setCategories($categories): void {
        $this->categories = $categories;
    }
    public function addCategory(Category $category): void {
        $this->getCategories()->attach($category);
    }


    public function jsonSerialize(): array {
        $statusEnum = EventStatusEnum::tryFromValue($this->eventStatus);
        $attendanceEnum = EventAttendanceModeEnum::tryFromValue($this->eventAttendanceMode);

        return [
            'uid'                 => $this->getUid(),
            'title'               => $this->title,
            'teaser'              => $this->teaser,
            'description'         => $this->description,
            'slug'                => $this->slug,
            'crdate'              => $this->crdate?->format('c'),
            'tstamp'              => $this->tstamp?->format('c'),
            'eventStatus'         => [
                'value' => $statusEnum?->value(),
                'label' => $statusEnum?->label(),
                'name'  => $statusEnum?->name,
            ],
            'eventAttendanceMode' => [
                'value' => $attendanceEnum?->value(),
                'label' => $attendanceEnum?->label(),
                'name'  => $attendanceEnum?->name,
            ],
            'eventDates'          => $this->serializeObjectStorage($this->eventDates),
            'categories'          => $this->serializeCategories(),
            'locations'           => $this->serializeAddresses($this->locations),
            'organizers'          => $this->serializeAddresses($this->organizers),
            'teaserMedia'         => $this->serializeFileReferences($this->teaserMedia),
            'bannerMedia'         => $this->serializeFileReferences($this->bannerMedia),
            'media'               => $this->serializeFileReferences($this->media),
        ];
    }

    // -------------------------------------------------------
    // Private Hilfsmethoden für die Serialisierung
    // -------------------------------------------------------

    /**
     * Serialisiert eine ObjectStorage deren Elemente selbst JsonSerializable sind (z.B. Date).
     */
    private function serializeObjectStorage(?ObjectStorage $storage): array {
        if ($storage === null) {
            return [];
        }
        $result = [];
        foreach ($storage as $item) {
            $result[] = $item instanceof \JsonSerializable
                ? $item->jsonSerialize()
                : (string)$item;
        }
        return $result;
    }

    /**
     * Serialisiert tt_address Address-Objekte (locations / organizers).
     * Da Address ein externes Model ist, mappen wir die Felder manuell.
     */
    private function serializeAddresses(?ObjectStorage $storage): array {
        if ($storage === null) {
            return [];
        }
        $result = [];
        foreach ($storage as $address) {
            $result[] = [
                'uid'     => $address->getUid(),
                'name'    => $address->getCompany(),
                'street'  => $address->getAddress(),
                'zip'     => $address->getZip(),
                'city'    => $address->getCity(),
                'country' => $address->getCountry(),
                'phone'   => $address->getPhone(),
                'email'   => $address->getEmail(),
                'website' => $address->getWww(),
            ];
        }
        return $result;
    }

    /**
     * Serialisiert FileReference-ObjectStorage zu öffentlichen URLs.
     */
    private function serializeFileReferences(?ObjectStorage $storage): array {
        if ($storage === null) {
            return [];
        }
        $result = [];
        foreach ($storage as $fileReference) {
            try {
                $resource = $fileReference->getOriginalResource();
                $result[] = [
                    'uid'         => $resource->getUid(),
                    'url'         => $resource->getPublicUrl(),
                    'title'       => $resource->getTitle(),
                    'alternative' => $resource->getAlternative(),
                    'description' => $resource->getDescription(),
                    'mimeType'    => $resource->getMimeType(),
                    'size'        => $resource->getSize(),
                    'width'       => $resource->getProperty('width'),
                    'height'      => $resource->getProperty('height'),
                ];
            } catch (\Throwable) {
                // Datei nicht erreichbar – überspringen
            }
        }
        return $result;
    }

    /**
     * Serialisiert Kategorien.
     */
    private function serializeCategories(): array {
        if ($this->categories === null) {
            return [];
        }
        $result = [];
        foreach ($this->categories as $category) {
            $result[] = [
                'uid'   => $category->getUid(),
                'title' => $category->getTitle(),
            ];
        }
        return $result;
    }
}
