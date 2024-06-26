CREATE DATABASE kb_t_db;

USE kb_t_db;

CREATE TABLE users
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    username VARCHAR(30) NOT NULL,
    password VARCHAR(257) NOT NULL,
    PRIMARY KEY(id)
);

CREATE TABLE tasks
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    data VARCHAR(1024) NOT NULL,
    description VARCHAR(200),
    user_id INT NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE keybindings
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    data VARCHAR(1024) NOT NULL,
    description VARCHAR(200),
    user_id INT NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE keybinding_version
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    data VARCHAR(1024) NOT NULL,
    modified DATETIME NOT NULL,
    keybinding_id INT(11) NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (keybinding_id) REFERENCES keybindings(id) ON DELETE CASCADE
);

CREATE TABLE task_version
(
    id INT(11) AUTO_INCREMENT NOT NULL,
    data VARCHAR(1024) NOT NULL,
    modified DATETIME NOT NULL,
    task_id INT(11) NOT NULL,
    PRIMARY KEY(id),
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE
);