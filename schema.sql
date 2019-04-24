CREATE DATABASE yeticave
    DEFAULT CHARACTER SET utf8
    DEFAULT COLLATE utf8_general_ci;
USE yeticave;
CREATE TABLE user
(
    id          INT AUTO_INCREMENT PRIMARY KEY,
    date        TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    email       VARCHAR(255) NOT NULL UNIQUE,
    name        VARCHAR(255) NOT NULL,
    password    VARCHAR(255) NOT NULL,
    avatar_path VARCHAR(500),
    contact     VARCHAR(500) NOT NULL
);

CREATE TABLE category
(
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(255) NOT NULL UNIQUE,
    simbolic_code VARCHAR(500) UNIQUE
);

CREATE TABLE lot
(
    id              INT AUTO_INCREMENT PRIMARY KEY,
    creation_date   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    name            VARCHAR(255)  NOT NULL,
    description     VARCHAR(2000) NOT NULL,
    img_path        VARCHAR(500),
    starting_price  INT           NOT NULL,
    completion_date TIMESTAMP,
    bid_step        INT           NOT NULL,
    category_id     INT           NOT NULL,
    user_id         INT           NOT NULL,
    winner_id       INT,
    FOREIGN KEY (category_id) REFERENCES category (id),
    FOREIGN KEY (user_id) REFERENCES user (id),
    FOREIGN KEY (winner_id) REFERENCES user (id)
);
CREATE INDEX idx_lot_by_category_creation ON lot (category_id, creation_date DESC);
CREATE INDEX idx_lot_winners ON lot (winner_id, completion_date);
CREATE FULLTEXT INDEX idx_search_name_descr ON lot (name, description);

CREATE TABLE bid
(
    id         INT AUTO_INCREMENT PRIMARY KEY,
    date       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    bid_amount INT       NOT NULL,
    user_id    INT       NOT NULL,
    lot_id     INT       NOT NULL,
    FOREIGN KEY (user_id) REFERENCES user (id),
    FOREIGN KEY (lot_id) REFERENCES lot (id)
);
CREATE INDEX idx_one_lot_bid_by_date ON bid (lot_id, date DESC);