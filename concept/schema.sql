DROP TABLE comics;
CREATE TABLE `comics` (
  `id`                 INTEGER DEFAULT NULL PRIMARY KEY AUTOINCREMENT,
  `download_date_time` DATETIME,
  `downloaded_by`      INTEGER,
  `filename`           TEXT,
  `status`             INTEGER,
  `status_message`     TEXT,
  `num`                INTEGER UNIQUE,
  `publish_date`       DATE,
  `link`               TEXT,
  `news`               TEXT,
  `transcript`         TEXT,
  `safe_title`         TEXT,
  `alt`                TEXT,
  `img`                TEXT,
  `title`              TEXT,
  `json`               TEXT
);