CREATE TABLE user (
        id int(10) NOT NULL AUTO_INCREMENT,
        username VARCHAR(255) NOT NULL,
        password VARCHAR(255) NOT NULL,
        encryption VARCHAR(25) NULL,
        PRIMARY KEY (id)
        );

INSERT INTO user (username, password, encryption) VALUES
('admin','admin',NULL),
('jeroen','324905aefb58c08771149a326741025c', 'md5');

CREATE TABLE page (
        id int(10) NOT NULL AUTO_INCREMENT,
        title VARCHAR(255) NULL,
        slug VARCHAR(255) NOT NULL,
        body TEXT NULL,
        homepage TINYINT(1),
        PRIMARY KEY (id)
        );
