-- DB Name : chat

CREATE TABLE `user` (
  cod int(10) NOT NULL PRIMARY KEY,
  username varchar(30) NOT NULL,
  password varchar(80) NOT NULL,
  img varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;