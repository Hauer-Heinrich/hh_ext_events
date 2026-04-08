CREATE TABLE tx_hhextevents_domain_model_event (
    sorting int(11) DEFAULT '0' NOT NULL,

    title varchar(255) NOT NULL,
    teaser text,
    description text,
    event_attendance_mode int(11) NOT NULL DEFAULT 1,
);

CREATE TABLE tx_hhextevents_domain_model_date (
    sorting int(11) DEFAULT '0' NOT NULL,
);
