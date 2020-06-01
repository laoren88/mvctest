-- Create a new database, run MySQL client:
-- mysql -u root -p <password>
-- create database testing;
-- use testing;
-- source schema.mysql.sql

CREATE TABLE `product` (     
  `id` int(11) PRIMARY KEY AUTO_INCREMENT, 
  `name` text NOT NULL,     
  `description` text NOT NULL,           
  `price` int(11) NOT NULL,        
  `url_image` text NOT NULL,         
  `date_created` DATETIME DEFAULT   CURRENT_TIMESTAMP    
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='utf8_general_ci';