# hh_ext_events

**hh_ext_events** is a TYPO3 v13 extension for managing and displaying events. It provides a list view, detail view, and (TODO:)calendar view for events, supports JSON API output, generates Schema.org structured data, and integrates with `tt_address` for locations and organizers.

---

## Requirements

- TYPO3 13.4.x (LTS)
- PHP 8.1+
- Optional: `friends-of-typo3/tt_address` (for locations and organizers)

---

## Installation

Install via Composer:

```bash
composer require hauer-heinrich/hh-ext-events
```

Or install manually via the TYPO3 Extension Manager ([extensions.typo3.org](https://extensions.typo3.org/)).

After installation:

1. Include the TypoScript setup (via **Configuration Sets** or manually in your root template).
2. Include the **PageTS** in your site configuration or root page.
3. Create a **storage page (sysfolder)** for your events and configure the storage PID in the plugin settings.

---

## Plugins

The extension provides three content element plugins:

| Plugin | Description |
|---|---|
| **Event List** | Displays a paginated, filterable list of events |
| **Event Detail** | Shows full event detail with Schema.org JSON-LD |
| **Event Calendar** | Displays events for a specific date |

---

## TypoScript Configuration

Include the TypoScript template or use the provided **Configuration Set**. The following constants are available:

```
plugin.tx_hhextevents {
    view {
        templateRootPath = ...
        partialRootPath  = ...
        layoutRootPath   = ...
    }
}
```

#### Sitemap
Example:
If you use the seperate detail-plugin please use "tx_hhextevents_eventdetail" else "tx_hhextevents_eventlist"

```
plugin.tx_seo.config {
    xmlSitemap {
        sitemaps {
            events {
                provider = HauerHeinrich\HhExtEvents\XmlSitemap\RecordsXmlSitemapDataProvider
                config {
                    table = tx_hhextevents_domain_model_event
                    sortField = sorting
                    lastModifiedField = tstamp
                    recursive = 1
                    ### pid of the events (directory)
                    pid = 57
                    url {
                        ### PageID of the detail-pape
                        pageId = 89
                        fieldToParameterMap {
                            uid = tx_hhextevents_eventdetail[event]
                        }
                        additionalGetParameters {
                            tx_hhextevents_eventdetail.controller = Event
                            tx_hhextevents_eventdetail.action = detail
                        }
                        useCacheHash = 1
                    }
                }
            }
        }
    }
}
```

## PageTS Options

### Add a custom event layout

Extend the layout dropdown in the Event List plugin via PageTS:

```
TCEFORM.tt_content.pi_flexform.hhextevents_eventlist.layout.settings\.field\.eventLayout {
    addItems {
        myLayout = My Custom Layout
    }
}
```

---

## Beautify URLs

Don't forget to add a site config for your events.
Example can be found **With site routing (`hh_ext_events/sites/config.yaml`):**
It depends on whether you're displaying the event details using a details plugin or the list plugin; depending on which one you use, you'll need one configuration or the other—see the example.

---

## JSON API

The event list can be output as JSON by using page type `813475`.

**Direct URL parameter:**
```
https://www.domain.tld/?type=813475
```

**With site routing (`config/sites/<site>/config.yaml`):**
```yaml
routeEnhancers:
  PageTypeSuffix:
    type: PageType
    map:
      events.json: 813475
```

Then call:
```
https://www.domain.tld/events.json
```

The JSON response includes all event fields with nested relations (dates, locations, organizers, media, categories). The response sets `X-Robots-Tag: noindex` and disables caching.

---

## Schema.org Structured Data

The **Event Detail** plugin automatically generates a `<script type="application/ld+json">` block with [Schema.org Event](https://schema.org/Event) markup, including:

- `name`, `description`, `identifier`
- `startDate`, `endDate` (or `eventSchedule` for multiple dates)
- `eventAttendanceMode` (OfflineEventAttendanceMode / OnlineEventAttendanceMode / MixedEventAttendanceMode)
- `eventStatus` (EventScheduled / EventCancelled / EventMovedOnline / EventPostponed / EventRescheduled)
- `image` (from teaser media)
- `location` (Place with PostalAddress from tt_address)
- `organizer` (Organization from tt_address)
