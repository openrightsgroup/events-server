ALTER TABLE site_history ADD   is_new SMALLINT DEFAULT '0' NOT NULL;

ALTER TABLE event_history ADD   is_new SMALLINT DEFAULT '0' NOT NULL;

ALTER TABLE group_history ADD   is_new SMALLINT DEFAULT '0' NOT NULL;

ALTER TABLE venue_history ADD   is_new SMALLINT DEFAULT '0' NOT NULL;

ALTER TABLE area_history ADD   is_new SMALLINT DEFAULT '0' NOT NULL;
