-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: localhost    Database: watch_db
-- ------------------------------------------------------
-- Server version	9.0.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `branches`
--

DROP TABLE IF EXISTS `branches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `branches` (
  `branch_id` int NOT NULL AUTO_INCREMENT,
  `branch_name` varchar(100) NOT NULL,
  `location` varchar(150) NOT NULL,
  PRIMARY KEY (`branch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `branches`
--

LOCK TABLES `branches` WRITE;
/*!40000 ALTER TABLE `branches` DISABLE KEYS */;
INSERT INTO `branches` VALUES (1,'Manila Branch','Malate, Manila'),(2,'BGC Branch','Bonifacio Global City, Taguig');
/*!40000 ALTER TABLE `branches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brands`
--

DROP TABLE IF EXISTS `brands`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `brands` (
  `brand_id` int NOT NULL AUTO_INCREMENT,
  `brand_name` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`brand_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brands`
--

LOCK TABLES `brands` WRITE;
/*!40000 ALTER TABLE `brands` DISABLE KEYS */;
INSERT INTO `brands` VALUES (1,'Seiko','Japan'),(2,'Hamilton','Switzerland'),(3,'Tissot','Switzerland'),(4,'Orient Bambino','Japan'),(5,'Certina','Switzerland');
/*!40000 ALTER TABLE `brands` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart` (
  `cart_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int DEFAULT '1',
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cart_id`),
  KEY `fk_cart_user` (`user_id`),
  KEY `fk_cart_product` (`product_id`),
  CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (4,5,1,1,'2025-11-17 14:35:16'),(10,8,23,1,'2025-11-17 20:31:47');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Dress Watch'),(2,'Sport Watch'),(3,'Casual Watch'),(4,'Luxury Watch'),(5,'Diver Watch');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `currencies`
--

DROP TABLE IF EXISTS `currencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `currencies` (
  `currency_id` int NOT NULL AUTO_INCREMENT,
  `currency_code` char(3) NOT NULL,
  `exchange_rate` decimal(10,4) NOT NULL,
  PRIMARY KEY (`currency_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `currencies`
--

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;
INSERT INTO `currencies` VALUES (1,'PHP',1.0000),(2,'USD',0.0180),(3,'EUR',0.0165);
/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `action` varchar(255) DEFAULT NULL,
  `log_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`),
  KEY `fk_logs_user` (`user_id`),
  CONSTRAINT `fk_logs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `logs`
--

LOCK TABLES `logs` WRITE;
/*!40000 ALTER TABLE `logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_item_id`),
  KEY `fk_orderitems_order` (`order_id`),
  KEY `fk_orderitems_product` (`product_id`),
  CONSTRAINT `fk_orderitems_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  CONSTRAINT `fk_orderitems_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
INSERT INTO `order_items` VALUES (1,1,22,1,54000.00),(2,2,23,1,42000.00),(3,2,22,2,54000.00);
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `branch_id` int DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Shipped','Delivered','Cancelled') DEFAULT 'Pending',
  `total_amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`order_id`),
  KEY `fk_orders_user` (`user_id`),
  KEY `fk_orders_branch` (`branch_id`),
  CONSTRAINT `fk_orders_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
INSERT INTO `orders` VALUES (1,8,NULL,'2025-11-17 11:40:11','Shipped',54000.00),(2,8,NULL,'2025-11-17 19:55:11','Pending',150000.00);
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_methods`
--

DROP TABLE IF EXISTS `payment_methods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payment_methods` (
  `payment_method_id` int NOT NULL AUTO_INCREMENT,
  `method_name` enum('Cash','Credit Card','PayPal','GCash') NOT NULL,
  PRIMARY KEY (`payment_method_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_methods`
--

LOCK TABLES `payment_methods` WRITE;
/*!40000 ALTER TABLE `payment_methods` DISABLE KEYS */;
INSERT INTO `payment_methods` VALUES (1,'Cash'),(2,'Credit Card'),(3,'PayPal'),(4,'GCash');
/*!40000 ALTER TABLE `payment_methods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payments` (
  `payment_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(10,2) NOT NULL,
  `payment_method_id` int NOT NULL,
  `currency_id` int DEFAULT NULL,
  `status` enum('Pending','Completed','Failed') DEFAULT 'Pending',
  PRIMARY KEY (`payment_id`),
  KEY `fk_payments_order` (`order_id`),
  KEY `fk_payments_method` (`payment_method_id`),
  KEY `fk_payments_currency` (`currency_id`),
  CONSTRAINT `fk_payments_currency` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`currency_id`),
  CONSTRAINT `fk_payments_method` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`payment_method_id`),
  CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
INSERT INTO `payments` VALUES (1,1,'2025-11-17 11:40:11',54000.00,4,1,'Completed'),(2,2,'2025-11-17 19:55:11',150000.00,2,1,'Pending');
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `brand_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `gender` enum('Men','Women','Unisex') DEFAULT NULL,
  `branch_id` int DEFAULT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `stock` int DEFAULT '0',
  `is_available` enum('Yes','No') DEFAULT 'Yes',
  `image_url` varchar(255) DEFAULT NULL,
  `date_added` datetime DEFAULT CURRENT_TIMESTAMP,
  `dial_color` enum('Black','White','Blue','Silver','Gold','Green','Brown','Gray','Red','Rose Gold') DEFAULT NULL,
  `dial_shape` enum('Round','Square','Rectangle','Oval','Triangular') DEFAULT NULL,
  `dial_type` enum('Analog','Digital','Hybrid') DEFAULT NULL,
  `strap_color` enum('Black','Brown','Silver','Gold','Blue','Gray','Green','Red','White','Rose Gold') DEFAULT NULL,
  `strap_material` enum('Leather','Metal','Rubber','Nylon') DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `fk_products_brand` (`brand_id`),
  KEY `fk_products_category` (`category_id`),
  KEY `fk_products_branch` (`branch_id`),
  CONSTRAINT `fk_products_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`branch_id`),
  CONSTRAINT `fk_products_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`brand_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,1,1,'Men',1,'Seiko Presage Cocktail Time SRPB43','Automatic dress watch inspired by cocktail bar aesthetics',25000.00,16,'Yes','img/products/seiko_presage_cocktail_time_srpb43.png','2025-11-05 09:32:52','Blue','Round','Analog','Black','Leather'),(2,1,1,'Men',1,'Seiko 5 Sports SRPD55','Robust automatic sports watch with 100m water resistance',18000.00,20,'Yes','img/products/seiko_5_sports_srpd55.png','2025-11-05 09:35:55','Black','Round','Analog','Black','Metal'),(3,1,1,'Women',1,'Seiko Lukia SSVW184','Elegant women’s automatic watch with rose-gold accents',23000.00,10,'Yes','img/products/seiko_lukia_ssvw184.jpg','2025-11-05 09:36:47','White','Round','Analog','Rose Gold','Metal'),(4,1,1,'Men',1,'Seiko Prospex SRPC93 \"Save the Ocean\"','Dive watch with textured dial and 200m water resistance',32000.00,12,'Yes','img/products/seiko_prospex_srpc93.jpg','2025-11-05 09:36:58','Blue','Round','Analog','Blue','Rubber'),(5,1,1,'Unisex',1,'Seiko Solar Chronograph SSC209','Solar-powered chronograph with alarm and 100 metre water resistance',22000.00,18,'Yes','img/products/seiko_solar_chronograph_ssc209.jpg','2025-11-05 09:37:05','Silver','Round','Analog','Black','Leather'),(6,2,2,'Men',1,'Hamilton Khaki Field Mechanical H70455533','Manual-wind field watch with military heritage',28000.00,9,'Yes','img/products/hamilton_khaki_field_mechanical_h70455533.jpg','2025-11-16 07:16:19','Black','Round','Analog','Brown','Leather'),(7,2,2,'Men',1,'Hamilton Jazzmaster Thinline H38511733','Slim dress watch with day/date function',32000.00,8,'Yes','img/products/hamilton_jazzmaster_thinline_h38511733.jpg','2025-11-16 07:27:19','Silver','Round','Analog','Black','Leather'),(9,2,2,'Men',1,'Hamilton Khaki Aviation Pilot Day Date H64615535','Pilot style automatic with day & date',30000.00,10,'Yes','img/products/hamilton_khaki_aviation_pilot_h64615535.jpg','2025-11-16 07:28:48','Green','Round','Analog','Brown','Leather'),(10,2,2,'Women',1,'Hamilton American Classic Intra-Matic H38425540','Vintage inspired automatic dress watch for women',34000.00,7,'Yes','img/products/hamilton_intra_matic_h38425540.jpg','2025-11-16 07:28:51','Blue','Round','Analog','Brown','Leather'),(11,2,2,'Unisex',1,'Hamilton Ventura H24411732','Iconic triangular automatic watch inspired by Elvis Presley',35000.00,6,'Yes','img/products/hamilton_ventura_h24411732.jpg','2025-11-16 07:31:11','Black','Triangular','Analog','Black','Leather'),(12,3,3,'Men',1,'Tissot PRX Powermatic 80 T137.407.11.351.00','Automatic sport-dress watch with 80-hour power reserve',49000.00,11,'Yes','img/products/tissot_prx_powermatic80_t13740711351.jpg','2025-11-16 07:39:44','Blue','Round','Analog','Silver','Metal'),(13,3,3,'Men',1,'Tissot Le Locle Powermatic 80 T006.407.11.053.00','Classic dress automatic with transparent caseback',47000.00,9,'Yes','img/products/tissot_le_locle_powermatic80_t00640711053.jpg','2025-11-16 07:39:44','White','Round','Analog','Black','Leather'),(14,3,3,'Women',1,'Tissot Bellissima T126.010.11.133.00','Elegant women’s automatic watch designed to be worn for those magic moments and special occasions',42000.00,5,'Yes','img/products/tissot_bellissima_t09421011111.jpg','2025-11-16 07:39:44','Green','Round','Analog','Silver','Metal'),(15,3,3,'Men',1,'Tissot Seastar 1000 Powermatic 80 T120.410.33.041.00','Diver-style automatic with 300m water resistance',56000.00,8,'Yes','img/products/tissot_seastar1000_powermatic80_t12041033041.jpg','2025-11-16 07:39:44','Blue','Round','Analog','Black','Rubber'),(16,3,3,'Unisex',1,'Tissot Everytime T143.410.11.091.00','Minimalist quartz watch ideal for everyday wear',19000.00,20,'Yes','img/products/tissot_everytime_t14341011091.jpg','2025-11-16 07:41:22','White','Round','Analog','Brown','Leather'),(17,4,4,'Men',1,'Orient Bambino Ver 4 Automatic','Domed crystal dress automatic with classic styling',16000.00,12,'Yes','img/products/orient_bambino_version4_fac0000dw0.jpg','2025-11-16 07:52:44','Brown','Round','Analog','Black','Leather'),(18,4,4,'Men',1,'ORIENT Bambino Small Seconds 38MM RA-AP0101B','Dress automatic with small-seconds subdial',16500.00,10,'Yes','img/products/orient_bambino_small_seconds_raap0002s10a.jpg','2025-11-16 07:52:47','Black','Round','Analog','Black','Leather'),(19,4,4,'Women',1,'Orient Bambino RA-AG0019L10B','Elegant ladies dress automatic with blue dial',17000.00,8,'Yes','img/products/orient_bambino_raag0019l10b.jpg','2025-11-16 07:52:49','Blue','Round','Analog','Silver','Metal'),(20,4,4,'Men',1,'Orient Classic RA-AP01 Bambino 38 Small Seconds','Automatic with gold-tone case and vintage look',17500.00,6,'Yes','img/products/orient_bambino_ra-ap01.jpg','2025-11-16 07:52:52','White','Round','Analog','Brown','Leather'),(21,4,4,'Unisex',1,'Orient Bambino RA-AC0001Y00A','Affordable classic automatic with sunburst dial',15000.00,14,'Yes','img/products/orient_bambino_raac0001y00a.jpg','2025-11-16 07:52:55','Green','Round','Analog','Brown','Leather'),(22,5,5,'Men',1,'Certina DS Action Automatic C032.407.11.051.00','Sport automatic with 200m water resistance and robust build',54000.00,7,'Yes','img/products/certina_ds_action_automatic_c03240711051.jpg','2025-11-16 08:01:37','Black','Round','Analog','Silver','Metal'),(23,5,5,'Men',1,'Certina DS Caimano Powermatic 80 C035.407.36.010.00','Classic automatic with 80-hour reserve and sapphire dress look',42000.00,8,'Yes','img/products/certina_ds_caimano_powermatic80_c03540736010.jpg','2025-11-16 08:01:37','Silver','Round','Analog','Brown','Leather'),(24,5,5,'Women',1,'Certina DS-8 Automatic C033.051.11.011.00','Elegant women’s automatic with diamond markers',46000.00,5,'Yes','img/products/certina_ds8_automatic_c03305111011.jpg','2025-11-16 08:01:37','White','Round','Analog','Silver','Metal'),(25,5,5,'Men',1,'Certina DS Podium GMT C021.429.11.051.00','GMT automatic with sporty styling and ceramic bezel',48000.00,6,'Yes','img/products/certina_ds_podium_gmt_c02142911051.jpg','2025-11-16 08:01:37','Blue','Round','Analog','Silver','Metal'),(26,5,5,'Unisex',1,'Certina DS First Lady C014.210.11.116.00','Stylish automatic for women with mother-of-pearl dial',44000.00,9,'Yes','img/products/certina_ds_first_lady_c01421011116.jpg','2025-11-16 08:01:37','White','Round','Analog','Silver','Metal'),(27,1,1,'Men',2,'Seiko Presage Sharp Edged SPB167','A premium automatic with a crisp textured dial inspired by Japanese Asanoha patterns.',42000.00,10,'Yes','img/products/seiko_presage_spb167.jpg','2025-11-17 21:01:05','White','Round','Analog','Black','Leather'),(28,1,5,'Men',2,'Seiko Prospex Samurai SRPC93K1','Angular case diver with strong wrist presence and 200m WR.',30000.00,12,'Yes','img/products/seiko_prospex_samurai_srpc93.jpg','2025-11-17 21:01:05','Blue','Round','Analog','Blue','Rubber'),(29,1,2,'Unisex',2,'Seiko 5 Sports SRPE51','Modern field-style automatic with simplified dial.',17000.00,20,'Yes','img/products/seiko_5_sports_srpe51.jpg','2025-11-17 21:01:05','Gray','Round','Analog','Silver','Metal'),(30,1,3,'Women',2,'Seiko Essentials SUR877P1','Minimalist quartz watch perfect for elegant daily wear.',14000.00,8,'Yes','img/products/seiko_essentials_sur877.jpg','2025-11-17 21:01:05','Silver','Round','Analog','Silver','Metal'),(31,1,1,'Men',2,'Seiko Presage SRPG09J1','Warm-tone dial with classical dress design and automatic movement.',28000.00,11,'Yes','img/products/seiko_presage_srpg09.jpg','2025-11-17 21:01:05','Brown','Round','Analog','Brown','Leather'),(32,2,2,'Men',2,'Hamilton Khaki Field Auto Chrono H71616535','Military-inspired automatic chronograph with rugged charm.',55000.00,6,'Yes','img/products/hamilton_khaki_auto_chrono.jpg','2025-11-17 21:01:05','Black','Round','Analog','Brown','Leather'),(33,2,1,'Men',2,'Hamilton Jazzmaster Open Heart H32565735','Dress watch showcasing the movement through an open-heart dial.',48000.00,9,'Yes','img/products/hamilton_jazzmaster_openheart.jpg','2025-11-17 21:01:05','Blue','Round','Analog','Silver','Metal'),(34,2,1,'Women',2,'Hamilton American Classic Ardmore Quartz H11221514','Art-deco inspired rectangular ladies watch on leather strap.',25000.00,7,'Yes','img/products/hamilton_ardmore.jpg','2025-11-17 21:01:05','White','Rectangle','Analog','Brown','Leather'),(35,2,2,'Men',2,'Hamilton Khaki Navy Scuba Auto H82335131','Automatic diver with bright blue dial and sporty bezel.',37000.00,10,'Yes','img/products/hamilton_khaki_navy_scuba.jpg','2025-11-17 21:01:05','Blue','Round','Analog','Silver','Metal'),(36,2,3,'Unisex',2,'Hamilton Ventura Quartz H24411232','Modern triangular watch with iconic futuristic styling.',36000.00,5,'Yes','img/products/hamilton_ventura_quartz.jpg','2025-11-17 21:01:05','White','Triangular','Analog','Black','Leather'),(37,3,1,'Men',2,'Tissot Heritage Visodate T019.430.16.051.01','Classic heritage automatic with domed crystal and day-date.',31000.00,14,'Yes','img/products/tissot_visodate.jpg','2025-11-17 21:01:05','Black','Round','Analog','Black','Leather'),(38,3,3,'Men',2,'Tissot Supersport Chrono T125.617.33.051.00','Bold chronograph with sporty black PVD case.',28000.00,8,'Yes','img/products/tissot_supersport.jpg','2025-11-17 21:01:05','Black','Round','Analog','Black','Leather'),(39,3,1,'Women',2,'Tissot Flamingo T094.210.11.121.00','Slim and elegant ladies watch with mother-of-pearl dial.',22000.00,9,'Yes','img/products/tissot_flamingo.jpg','2025-11-17 21:01:05','White','Round','Analog','Silver','Metal'),(40,3,5,'Men',2,'Tissot Seastar 2000 Professional T120.607.17.041.00','ISO-rated diver with ceramic bezel and 600m WR.',60000.00,6,'Yes','img/products/tissot_seastar2000.jpg','2025-11-17 21:01:05','Blue','Round','Analog','Black','Rubber'),(41,3,3,'Unisex',2,'Tissot Classic Dream T129.410.11.051.00','Reliable quartz watch with clean and versatile design.',15000.00,20,'Yes','img/products/tissot_classic_dream.jpg','2025-11-17 21:01:05','Black','Round','Analog','Silver','Metal'),(42,4,4,'Men',2,'Orient Star Classic RE-AU0004B00B','Premium Orient automatic with power reserve indicator.',30000.00,5,'Yes','img/products/orient_star_classic.jpg','2025-11-17 21:01:05','Black','Round','Analog','Black','Leather'),(43,4,3,'Men',2,'Orient Kamasu RA-AA0002L19B','Highly popular diver with sapphire crystal and 200m WR.',17000.00,18,'Yes','img/products/orient_kamasu_blue.jpg','2025-11-17 21:01:05','Blue','Round','Analog','Silver','Metal'),(44,4,4,'Women',2,'Orient Contemporary Ladies RA-NR2002A','Dressy ladies automatic with gold-tone accents.',16000.00,7,'Yes','img/products/orient_contemporary_gold.jpg','2025-11-17 21:01:05','White','Round','Analog','Gold','Metal'),(45,4,3,'Unisex',2,'Orient Sports RA-AC0J02B10B','Sporty automatic with stealth black theme.',15500.00,13,'Yes','img/products/orient_sports_black.jpg','2025-11-17 21:01:05','Black','Round','Analog','Black','Rubber'),(46,5,5,'Men',2,'Certina DS Action Diver Titanium C032.607.44.051.00','Lightweight titanium diver with Powermatic 80 movement.',62000.00,6,'Yes','img/products/certina_ds_action_titanium.jpg','2025-11-17 21:01:05','Black','Round','Analog','Gray','Metal'),(47,5,4,'Men',2,'Certina DS-1 Automatic C029.807.11.051.00','Elegant automatic with vintage-inspired dial.',43000.00,10,'Yes','img/products/certina_ds1_auto.jpg','2025-11-17 21:01:05','Black','Round','Analog','Silver','Metal'),(48,5,3,'Unisex',2,'Certina Urban DS C021.610.11.031.00','Modern quartz watch ideal for everyday city wear.',24000.00,15,'Yes','img/products/certina_urban_ds.jpg','2025-11-17 21:01:05','Silver','Round','Analog','Silver','Metal');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role_name` enum('Customer','Admin','Staff') NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin'),(2,'Staff'),(3,'Customer');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` date DEFAULT (curdate()),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_users_role` (`role_id`),
  CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,1,'Algen','Abagat','algen_abagat@zeit.com','$2y$10$kOFXnhncFD3DcKz.3SyIV.Hd9UbcofzMsPwf1qeDC7TcRSeKiPB8q','09171234567','2025-11-05'),(2,1,'Cody','Casem','cody_casem@zeit.com','$2y$10$tkEhOOaeq7PpWLeQUAN92OoD4fgzoWFRtApfmnU28gvrgyJM3tgkO','09181234567','2025-11-05'),(3,1,'Albrecht','Coliat','albrech_coliat@zeit.com','$2y$10$6nk5snJfFCpvpEF7Oaj.8u2YCzwnWCp4BiXChW/oWZHhhcpaxGEbi','09991234567','2025-11-05'),(4,1,'Ken','Latido','ken_latido@zeit.com','$2y$10$jGSdgzYeS7gA66cUqA3HuetSN6c8u4VFAtStWuIa/9Tb0EoTRC4mS','09998726337','2025-11-05'),(5,2,'Michael','Jackson','michael.jackson@zeit.com','$2y$10$QhiCPxfVo6ANBnYM8QwZHeJ/EhF9MWsURvd.hpTBxIg3nQ40fALhS','09201234567','2025-11-05'),(6,3,'Frank','Sinatra','fsinatra@gmail.com','$2y$10$fXqwx/GVc1qEw9IMMLWVw.MdnASiqRq.8Goa9TuuuThP/0Ij0z/Om','09351234567','2025-11-05'),(7,3,'Roja ','Dove','rojadove@gmail.com','$2y$10$FYe3VmEVRUTq9GtvNT.O6OD419ckSsGy8/GeZ0H/UQIZ/T1og/nlm',NULL,'2025-11-09'),(8,3,'Kanye','West','yeezy@gmail.com','$2y$10$ypxait/PnhKON04U9srRNueEjwggxvdQaxiIbGVrsgjabDY4KGjDG','09165695316','2025-11-10'),(10,3,'Frank','Ocean','blonded@gmail.com','$2y$10$v/Qa/KVQcrr0fidL4haIGeyTu9UwvV0C2cQ8gJKERrdxppe41W6oi','09999999999','2025-11-16');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-17 21:06:24
