CREATE TABLE user_notification (
	id SERIAL,
	user_id INTEGER NOT NULL,
	site_id INTEGER NULL,
	from_extension_id VARCHAR(255) NOT NULL,
	from_user_notification_type VARCHAR(255) NOT NULL,
	is_email boolean DEFAULT '0' NOT NULL,
	data_json TEXT NOT NULL DEFAULT '{}',
	created_at timestamp without time zone NOT NULL,
	emailed_at timestamp without time zone NULL,
	read_at timestamp without time zone NULL,
	PRIMARY KEY(id)
);
ALTER TABLE user_notification ADD CONSTRAINT user_notification_user_id FOREIGN KEY (user_id) REFERENCES user_account_information(id);
ALTER TABLE user_notification ADD CONSTRAINT user_notification_site_id FOREIGN KEY (site_id) REFERENCES site_information(id);

CREATE TABLE user_notification_preference (
	user_id INTEGER NOT NULL,
	extension_id VARCHAR(255) NOT NULL,
	user_notification_preference_type VARCHAR(255) NOT NULL,
	is_email boolean DEFAULT '1' NOT NULL,
	PRIMARY KEY(user_id, extension_id, user_notification_preference_type)
);
ALTER TABLE user_notification_preference ADD CONSTRAINT user_notification_preference_user_id FOREIGN KEY (user_id) REFERENCES user_account_information(id);
