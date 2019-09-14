create database if not exists myphptaskmanager;

use myphptaskmanager;

create table if not exists task (
    id INT AUTO_INCREMENT NOT NULL,
    user_name VARCHAR(512) NOT NULL,
    email VARCHAR(512) NOT NULL,
    text varchar(1024) not null,
    status TINYINT(1) NOT NULL,
    primary key (id),
    index user_name_idx (user_name),
    index email_idx (email),
    index status_idx (status)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

create table if not exists image (
    id INT AUTO_INCREMENT NOT NULL,
    width INT NOT NULL,
    height INT NOT NULL,
    extension varchar(4) not null,
    hash varchar(32) NOT NULL,
    primary key (id),
    index extension_idx (extension),
    index hash_idx (hash)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;

create table if not exists task_image (
    id INT AUTO_INCREMENT NOT NULL,
    task_id INT NOT NULL,
    image_id INT NOT NULL,
    primary key (id),
    index task_id_idx (task_id),
    index image_id_idx (image_id),
    index task_id_image_id_idx (task_id, image_id)
) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
