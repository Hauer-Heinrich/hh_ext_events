<?php
$ll = 'LLL:EXT:hh_ext_events/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => 'Event',
        'label' => 'title',
        'descriptionColumn' => 'description',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'default_sortby' => 'title',
        'type' => 'record_type',
        'typeicon_column' => 'record_type',
        // 'typeicon_classes' => [
        //     'default' => 'ext-events-type-default',
        //     '1' => 'ext-events-type-internal',
        //     '2' => 'ext-events-type-external',
        // ],
        'useColumnsForDefaultValues' => 'record_type',
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'security' => [
            // 'ignorePageTypeRestriction' => true,
        ],
        'searchFields' => 'title,description',
        'iconfile' => 'EXT:event_calendar/Resources/Public/Icons/event.svg', // 64x64px
    ],

    'palettes' => [
        'visibility' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.visibility',
            'showitem' => 'hidden;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:pages.hidden_toggle_formlabel,',
        ],
        'access' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.access',
            'showitem' => 'starttime;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.starttime_formlabel, endtime;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.endtime_formlabel, extendToSubpages;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.extendToSubpages_formlabel, --linebreak--, fe_group;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.fe_group_formlabel, --linebreak--,editlock',
        ],
        'language' => [
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.palettes.language',
            'showitem' => 'sys_language_uid,l18n_parent',
        ],
    ],

    'types' => [
        '0' => [
            'showitem' => '
                record_type,
                title,
                teaser,
                description,
                event_attendance_mode,
                event_dates,
                event_status,
                locations,
                organizers,
                slug,
                --div--;Files,
                    teaser_media,
                    banner_media,
                    media,
                --div--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,
                    categories,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                    --palette--;;visibility,
                    --palette--;;access,
            '
        ],
        'internal' => [
            'showitem' => '
                record_type,
                title,
                teaser,
                internal_url,
                slug,
                --div--;Files,
                    teaser_media,
                --div--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,
                    categories,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                    --palette--;;visibility,
                    --palette--;;access,
            ',
        ],
        'external' => [
            'showitem' => '
                record_type,
                title,
                teaser,
                external_url,
                slug,
                --div--;Files,
                    teaser_media,
                --div--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.tabs.category,
                    categories,
                --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
                    --palette--;;language,
                --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.access,
                    --palette--;;visibility,
                    --palette--;;access,
            ',
        ],
    ],
    'columns' => [
        'sys_language_uid' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'language',
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_news_domain_model_news',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
            ],
        ],
        'l10n_source' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
                'default' => '',
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
            ],
        ],
        'pid' => [
            'label' => 'pid',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'crdate' => [
            'label' => 'crdate',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'tstamp' => [
            'label' => 'tstamp',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'sorting' => [
            'label' => 'sorting',
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
            'config' => [
                'type' => 'datetime',
            ],
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            'config' => [
                'type' => 'datetime',
            ],
        ],

        'record_type' => [
            'exclude' => false,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.doktype_formlabel',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' =>  [
                    ['label' => $ll . 'tx_hhextevents_domain_model_event.record_type.default', 'value' => '0', ],
                    ['label' => $ll . 'tx_hhextevents_domain_model_event.record_type.internal', 'value' => 'internal', ],
                    ['label' => $ll . 'tx_hhextevents_domain_model_event.record_type.external', 'value' => 'external', ],
                ],
                'default' => '0',
                'fieldWizard' => [
                    'selectIcons' => [
                        'disabled' => false,
                    ],
                ],
                'size' => 1,
                'maxitems' => 1,
            ],
        ],
        'title' => [
            'exclude' => true,
            'label' => 'Title',
            'description' => '',
            'config' => [
                'type' => 'input',
                'required' => true,
                'eval' => 'trim',
            ],
        ],
        'teaser' => [
            'exclude' => true,
            'label' => 'Teaser',
            'description' => '',
            'config' => [
                'type' => 'text',
                'rows' => 1,
                'enableRichtext' => true,
            ],
        ],
        'description' => [
            'exclude' => true,
            'label' => 'Description',
            'description' => '',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true,
            ],
        ],
        'internal_url' => [
            'exclude' => false,
            'label' => $ll . 'tx_hhextevents_domain_model_event.internal_url.label',
            'description' => $ll . 'tx_hhextevents_domain_model_event.internal_url.description',
            'config' => [
                'type' => 'link',
                'required' => true,
                'allowedTypes' => ['page', 'file', 'record'],
            ],
        ],
        'external_url' => [
            'exclude' => false,
            'label' => $ll . 'tx_hhextevents_domain_model_event.external_url.label',
            'description' => $ll . 'tx_hhextevents_domain_model_event.external_url.description',
            'config' => [
                'type' => 'link',
                'required' => true,
                'allowedTypes' => ['url'],
            ],
        ],
        'event_dates' => [
            'exclude' => true,
            'label' => 'Event dates',
            'description' => '',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_hhextevents_domain_model_date',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
                'foreign_sortby' => 'sorting',
                'appearance' => [
                    'collapseAll' => true,
                    'expandSingle' => true,
                ],
            ],
        ],
        'event_status' => [
            'exclude' => true,
            'label' => 'Event status',
            'description' => '',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => \HauerHeinrich\HhExtEvents\UserFunction\FormEngine\TcaEventUtility::class . '->getEventStatusOptions',
            ],
        ],
        'teaser_media' => [
            'label' => 'Teaser Media',
            'config' => [
                'type' => 'file',
                'maxitems' => 1,
                'allowed' => 'common-image-types',
            ],
        ],
        'banner_media' => [
            'label' => 'Banner Media',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
            ],
        ],
        'media' => [
            'label' => 'Media',
            'config' => [
                'type' => 'file',
                'allowed' => 'common-image-types',
            ],
        ],
        'event_attendance_mode' => [
            'exclude' => true,
            'label' => 'Event Attendance Mode',
            'description' => '',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'itemsProcFunc' => \HauerHeinrich\HhExtEvents\UserFunction\FormEngine\TcaEventUtility::class . '->getEventAttendanceModeOptions',
            ],
        ],
        'locations' => [
            'exclude' => true,
            'label' => 'Locations',
            'description' => '',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'multiple' => true,
                'foreign_table' => 'tt_address',
                'foreign_table_where' => 'AND {#tt_address}.{#pid} IN (###PAGE_TSCONFIG_IDLIST###) AND {#tt_address}.{#sys_language_uid} = ###REC_FIELD_sys_language_uid###',
                'MM' => 'tx_hhextevents_event_location_mm',
                'maxitems' => 1,
            ],
        ],
        'organizers' => [
            'exclude' => true,
            'label' => 'Organizers',
            'description' => '',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'multiple' => true,
                'foreign_table' => 'tt_address',
                'foreign_table_where' => 'AND {#tt_address}.{#pid} IN (###PAGE_TSCONFIG_IDLIST###) AND {#tt_address}.{#sys_language_uid} = ###REC_FIELD_sys_language_uid###',
                'MM' => 'tx_hhextevents_event_organizer_mm'
            ],
        ],
        'categories' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.categories',
            'description' => '',
            'config' => [
                'type' => 'category',
            ],
        ],
        'slug' => [
            'exclude' => true,
            'label' => 'URL Path (slug)',
            'description' => '',
            'config' => [
                'type' => 'slug',
                'size' => 50,
                'generatorOptions' => [
                    'fields' => [
                        'title',
                    ],
                    'fieldSeparator' => '/',
                    'prefixParentPageSlug' => true,
                ],
                'fallbackCharacter' => '-',
                'eval' => 'uniqueInSite',
                'default' => '',
            ],
        ],
    ],
];
