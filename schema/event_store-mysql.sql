--
-- MySQL schema for event store
-- 
-- @author Sean Crystal <sean.crystal@gmail.com>
-- @copyright 2011 Sean Crystal
-- @license http://www.opensource.org/licenses/BSD-3-Clause
-- @link https://github.com/spiralout/Tracks
--

CREATE TABLE IF NOT EXISTS entity (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  guid char(23) NOT NULL,
  `type` varchar(255) NOT NULL,
  version int(10) unsigned NOT NULL DEFAULT '0',
  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY guid (guid)
) ENGINE=InnoDB;


CREATE TABLE IF NOT EXISTS `event` (
  id int(10) unsigned NOT NULL AUTO_INCREMENT,
  guid char(23) NOT NULL,
  `data` text NOT NULL,
  date_created timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB;
