CREATE DATABASE  IF NOT EXISTS `servicocomunidade` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `servicocomunidade`;
-- MySQL dump 10.13  Distrib 8.2.0, for Win64 (x86_64)
--
-- Host: localhost    Database: servicocomunidade
-- ------------------------------------------------------
-- Server version	8.2.0

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
-- Table structure for table `estado`
--

DROP TABLE IF EXISTS `estado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado` (
  `id` int NOT NULL AUTO_INCREMENT,
  `estado` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado`
--

LOCK TABLES `estado` WRITE;
/*!40000 ALTER TABLE `estado` DISABLE KEYS */;
INSERT INTO `estado` VALUES (1,'Aceito'),(2,'Recusado'),(3,'Orçamento em produção'),(4,'Orçamento em análise'),(5,'Aguardando resposta do cliente'),(6,'Finalizado');
/*!40000 ALTER TABLE `estado` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_orcamento`
--

DROP TABLE IF EXISTS `estado_orcamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_orcamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orcamento_id` int NOT NULL,
  `estado_id` int NOT NULL,
  `data` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orcamento_has_estado_estado1_idx` (`estado_id`),
  KEY `fk_orcamento_has_estado_orcamento1_idx` (`orcamento_id`),
  CONSTRAINT `fk_orcamento_has_estado_estado1` FOREIGN KEY (`estado_id`) REFERENCES `estado` (`id`),
  CONSTRAINT `fk_orcamento_has_estado_orcamento1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamento` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_orcamento`
--

LOCK TABLES `estado_orcamento` WRITE;
/*!40000 ALTER TABLE `estado_orcamento` DISABLE KEYS */;
INSERT INTO `estado_orcamento` VALUES (1,1,3,'2024-03-21'),(2,1,4,'2024-03-22'),(3,1,5,'2024-03-22'),(4,1,1,'2024-03-25'),(5,1,6,'2024-05-24'),(6,2,3,'2024-04-20'),(7,3,3,'2024-02-10'),(8,3,4,'2024-02-12'),(9,3,5,'2024-02-14'),(10,3,2,'2024-02-16'),(11,4,3,'2024-01-15'),(12,5,3,'2024-02-10'),(13,5,4,'2024-02-14'),(14,6,3,'2024-04-05'),(15,6,4,'2024-04-08'),(16,6,5,'2024-04-13');
/*!40000 ALTER TABLE `estado_orcamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laboratorio`
--

DROP TABLE IF EXISTS `laboratorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laboratorio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(55) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laboratorio`
--

LOCK TABLES `laboratorio` WRITE;
/*!40000 ALTER TABLE `laboratorio` DISABLE KEYS */;
INSERT INTO `laboratorio` VALUES (1,'Química'),(2,'Engenharia mecânica'),(3,'Informática'),(4,'Agronomia'),(5,'Engenharia elétrica');
/*!40000 ALTER TABLE `laboratorio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1715300520),('m150214_044831_init_user',1715300523);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orcamento`
--

DROP TABLE IF EXISTS `orcamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `orcamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data_entrada` date NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `preco` float(10,2) DEFAULT NULL,
  `data_entrega` date DEFAULT NULL,
  `fatura` mediumblob,
  `utilizador_id` int NOT NULL,
  `laboratorio_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_orcamento_laboratorio1_idx` (`laboratorio_id`),
  KEY `fk_orcamento_utilizador1_idx` (`utilizador_id`),
  CONSTRAINT `fk_orcamento_laboratorio1` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorio` (`id`),
  CONSTRAINT `fk_orcamento_utilizador1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizador` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orcamento`
--

LOCK TABLES `orcamento` WRITE;
/*!40000 ALTER TABLE `orcamento` DISABLE KEYS */;
INSERT INTO `orcamento` VALUES (1,'2024-03-21','Gostaria de solicitar análise da água e análise de alguns materiais na minha fazenda',250.00,'2024-05-24',NULL,3,1),(2,'2024-04-20','Preciso de um software para meu ginásio',500.00,'2024-05-20',NULL,3,3),(3,'2024-02-10','Verificar análise de ph da água da minha casa e composição química',100.00,'2024-02-15',NULL,5,1),(4,'2024-01-15','Preciso de uma solução para a doença na minha plantação de milho',500.00,'2024-02-10',NULL,4,4),(5,'2024-02-10','Quero testar uma nova liga metálica que desenvolvi',444.00,'2024-02-12',NULL,4,2),(6,'2024-04-05','Preciso testar o software das máquinas de minha fábrica',123.00,'2024-05-10',NULL,3,2);
/*!40000 ALTER TABLE `orcamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profile` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `full_name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `timezone` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `profile_user_id` (`user_id`),
  CONSTRAINT `profile_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
INSERT INTO `profile` VALUES (1,1,'2024-05-10 00:22:03',NULL,'the one',NULL);
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `can_admin` smallint NOT NULL DEFAULT '0',
  `can_lab` smallint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
INSERT INTO `role` VALUES (1,'Admin','2024-05-10 00:22:03',NULL,1,0),(2,'User','2024-05-10 00:22:03',NULL,0,0),(3,'Lab',NULL,NULL,0,1);
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servico`
--

DROP TABLE IF EXISTS `servico`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servico` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(45) NOT NULL,
  `descricao` varchar(255) NOT NULL,
  `preco_unitario_custo` float(10,2) NOT NULL,
  `preco_unitario_venda` float(10,2) NOT NULL,
  `laboratorio_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `laboratorio_id` (`laboratorio_id`),
  CONSTRAINT `servico_ibfk_1` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorio` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servico`
--

LOCK TABLES `servico` WRITE;
/*!40000 ALTER TABLE `servico` DISABLE KEYS */;
INSERT INTO `servico` VALUES (1,'Análise de água','Determina a qualidade da água, desde o PH a quantidade de minerais',10.00,15.00,1),(2,'Análise de composição química','Determinação da composição de amostras de materiais diversos, como alimentos, medicamentos, produtos químicos, entre outros.',15.00,30.00,1),(3,'Cromatografia','Separação e identificação de componentes em misturas complexas, como análise de poluentes em água ou identificação de compostos em extratos de plantas.',5.00,20.00,1),(4,'Ensaios de materiais','Testes de tração, compressão, flexão e dureza para determinar as propriedades mecânicas de materiais utilizados em projetos de engenharia.',20.00,25.00,2),(5,'Análise de vibrações','Avaliação de vibrações em máquinas e equipamentos para identificar problemas de desbalanceamento, desalinhamento e desgaste, e propor soluções para melhorar o desempenho e a vida útil.',30.00,42.00,2),(6,'Simulação computacional','Utilização de softwares de elementos finitos para simular o comportamento de componentes e sistemas mecânicos sob diferentes condições de carga e temperatura, auxiliando no projeto e otimização',55.00,100.00,2),(7,'Desenvolvimento de software','Criação de programas e aplicativos sob medida para atender às necessidades específicas de empresas e organizações',100.00,200.00,3),(8,'Testes de software','Verificação do funcionamento de softwares em diferentes cenários e plataformas, garantindo a qualidade e a segurança do produto final',55.00,85.00,3),(9,'Análise de dados','Utilização de ferramentas estatísticas e de aprendizado de máquina para extrair informações relevantes de grandes volumes de dados, auxiliando na tomada de decisões estratégicas',30.00,100.00,3),(10,'Análise de solo','Avaliação da fertilidade do solo, determinando os níveis de nutrientes e a presença de elementos tóxicos, para orientar o uso de fertilizantes e corretivos',22.00,44.00,4),(11,'Identificação de pragas e doenças','Análise de plantas e amostras de solo para identificar a presença de pragas e doenças, e recomendar medidas de controle adequadas',20.00,55.00,4),(12,'Análise de qualidade de sementes','Avaliação da germinação, vigor e pureza de sementes, garantindo a qualidade do material utilizado no plantio',55.00,65.00,4),(13,'Ensaios de circuitos elétricos','Testes de funcionamento de circuitos eletrônicos, medindo tensões, correntes e resistências, para verificar o desempenho e identificar falhas',20.00,69.00,5),(14,'Calibração de instrumentos','Ajuste e verificação da precisão de instrumentos de medição elétrica, como multímetros, osciloscópios e analisadores de espectro',14.00,20.00,5),(15,'Desenvolvimento de protótipos eletrônicos','Criação de placas de circuito impresso e montagem de componentes eletrônicos para testar novas ideias e projetos',20.00,60.00,5);
/*!40000 ALTER TABLE `servico` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `servico_orcamento`
--

DROP TABLE IF EXISTS `servico_orcamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `servico_orcamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `orcamento_id` int NOT NULL,
  `servico_id` int NOT NULL,
  `quantidade` smallint NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_servicos` (`orcamento_id`,`servico_id`),
  KEY `fk_orcamento_has_servico_servico1_idx` (`servico_id`),
  KEY `fk_orcamento_has_servico_orcamento1_idx` (`orcamento_id`),
  CONSTRAINT `fk_orcamento_has_servico_orcamento1` FOREIGN KEY (`orcamento_id`) REFERENCES `orcamento` (`id`),
  CONSTRAINT `fk_orcamento_has_servico_servico1` FOREIGN KEY (`servico_id`) REFERENCES `servico` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `servico_orcamento`
--

LOCK TABLES `servico_orcamento` WRITE;
/*!40000 ALTER TABLE `servico_orcamento` DISABLE KEYS */;
INSERT INTO `servico_orcamento` VALUES (1,1,1,3),(2,1,3,1),(3,2,7,1),(4,3,1,5),(5,3,2,2),(6,3,3,2),(7,4,11,1),(8,5,4,1),(9,5,5,2),(10,6,6,1);
/*!40000 ALTER TABLE `servico_orcamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `status` smallint NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `auth_key` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `access_token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logged_in_ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `logged_in_at` timestamp NULL DEFAULT NULL,
  `created_ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `banned_at` timestamp NULL DEFAULT NULL,
  `banned_reason` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_email` (`email`),
  UNIQUE KEY `user_username` (`username`),
  KEY `user_role_id` (`role_id`),
  CONSTRAINT `user_role_id` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,1,1,'neo@neo.com','neo','$2y$13$dyVw4WkZGkABf2UrGWrhHO4ZmVBv.K4puhOL59Y9jQhIdj63TlV.O','J0PrbhhBiKcDxWFUcxz95IsMSHDqth1w','LnkeNnnbkd3N5WZiYOR_9RE8k33nK1RX','::1','2024-06-09 10:55:24',NULL,'2024-05-10 00:22:03',NULL,NULL,NULL),(2,1,1,NULL,'gabinete1','$2y$13$rpF8x95rBgQOu2xThOqL3uXRZq4u/x8wG44sQv.mBFK.RQdDdrDwm','8ibRkBq864PYYLxIa8oKqr6ZLcQlwyfe','wK9DpRsBpCPjxyBTzkJ1fBU2-pbpaTyq','::1','2024-06-09 10:55:34','::1','2024-06-09 10:55:34','2024-06-09 10:55:34',NULL,NULL),(3,1,1,NULL,'gabinete2','$2y$13$sKOCTOXHfB8sfi6kIpfOOOJ4G9iu65DOpXx3M7prG.1bI2Yi8.tI6','EUDfM9GgDpcA29vyVzp3ZXp0OdI7pul0','pYJgEZvQs48t0qhGlnxQC0mbRuZU4ytX','::1','2024-06-09 10:56:24','::1','2024-06-09 10:56:23','2024-06-09 10:56:24',NULL,NULL),(4,2,1,NULL,'cliente1','$2y$13$oVIKwNgMYRdUXcAeE9QSAeF8sle1QOzKqx6Tu3FWvG.8Sa0x.zB.C','yoHfZqvMSwmWVzlUUnIEfLVz_TfGBOFI','9_hQg4sDkXdPpNS2PoaZwWVgIoppAzXZ','::1','2024-06-09 10:56:49','::1','2024-06-09 10:56:49','2024-06-09 10:56:49',NULL,NULL),(5,2,1,NULL,'cliente2','$2y$13$nDm2axLIotT2zF2iMbxD8ukWYRuzGDFmMekuyTQMCOHOzjFANHygG','M9raWn_b0vYePe-11NNOQWsRKR5yy06E','ewg9qlbeKx8Gyjz_50WS1WOccmAqmmu7','::1','2024-06-09 10:57:06','::1','2024-06-09 10:57:06','2024-06-09 10:57:06',NULL,NULL),(6,2,1,NULL,'cliente3','$2y$13$BVHQtk95uS3X.oq9wo9bte0XSI3JFEaNzyIRlcU3T3Qfxp1Iu2Xz.','qWjZ7kqk4IRnaGTuiG4IcaKwnoOHByX-','eH6IeLTet_2ZmuOSyoHjzxMW-cVGM6FG','::1','2024-06-09 10:57:23','::1','2024-06-09 10:57:22','2024-06-09 10:57:23',NULL,NULL),(7,2,1,NULL,'cliente4','$2y$13$WsGVKVsLJY8XgQiqo7x.XOBsUX4tCxiE1bM4XJURl/7mdV6.eON1e','7Jm5VauRthKLYsvSPOSdYg3ihYF_gzPS','PeMgU-ewAXPGcsRVlCqH4aihNS9_T0Qn','::1','2024-06-09 10:57:35','::1','2024-06-09 10:57:34','2024-06-09 10:57:35',NULL,NULL),(8,3,1,NULL,'lab1','$2y$13$pej9DEubRrZlIzw0uze4TOjPBduqSYXNfpSHvbcox9abQD3AQ0l0S','8wT6YzVsCnSWeg6FF_NsL3uqxY0s9twk','-e4Co38nAcu9b08vtfHVhV64Aqllqzq9','::1','2024-06-09 11:49:48','::1','2024-06-09 10:58:18','2024-06-09 10:58:19',NULL,NULL),(9,3,1,NULL,'lab2','$2y$13$7J.TSV3rOFVIU1H6okLPQel1BIFOysmjRgfz.8Zod6vL2xXuM/l46','o74xwPTcKTfq5cW1qmKutpCG8Bt3_Na6','O4YNJqGq103jkeBH7lmt82L5ByRVw1bn','::1','2024-06-09 12:17:22','::1','2024-06-09 10:58:30','2024-06-09 10:58:31',NULL,NULL),(10,3,1,NULL,'lab3','$2y$13$wPXqO0mKwfTyZThteOzDbek3dE758.QKu2RqbtIlOKGlEYgph6xOi','TcSSxktaRv3fEI4JVuUSn76714X2n0WG','h6HDP6K5oyKQVMgWI56YyjcPdwOiY8ZF','::1','2024-06-09 10:58:43','::1','2024-06-09 10:58:43','2024-06-09 10:58:43',NULL,NULL),(11,3,1,NULL,'lab4','$2y$13$7MsUGiRZnqn3pYEhdY7e9O1Ui7a2KXtDKFY/pElVGnFb07Hjsw7Ne','GJs5ftqMnrCxXxjYWhgTEkH1XvS3XB68','CKzOQcdPDxYVpyrsjFxpvwkg7-PniPEm','::1','2024-06-09 12:15:24','::1','2024-06-09 10:58:54','2024-06-09 10:58:55',NULL,NULL),(12,3,1,NULL,'lab5','$2y$13$4x5XUAKnFlFiypP09EmxSujoz/t4o/h.gyoVlbFfR19Vubr8BQxWy','u4oPUjU8IUWH97DWf-9faFcJYI-kybVO','2pctFSqrg2bm6-p_h3WsvDJaRX_Pd90j','::1','2024-06-09 12:14:48','::1','2024-06-09 10:59:10','2024-06-09 10:59:10',NULL,NULL);
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_auth`
--

DROP TABLE IF EXISTS `user_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_auth` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `provider` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `provider_id` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `provider_attributes` text CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_auth_provider_id` (`provider_id`),
  KEY `user_auth_user_id` (`user_id`),
  CONSTRAINT `user_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_auth`
--

LOCK TABLES `user_auth` WRITE;
/*!40000 ALTER TABLE `user_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_token`
--

DROP TABLE IF EXISTS `user_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_token` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `type` smallint NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `data` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_token_token` (`token`),
  KEY `user_token_user_id` (`user_id`),
  CONSTRAINT `user_token_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_token`
--

LOCK TABLES `user_token` WRITE;
/*!40000 ALTER TABLE `user_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utilizador`
--

DROP TABLE IF EXISTS `utilizador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utilizador` (
  `id` int NOT NULL AUTO_INCREMENT,
  `idLab` int DEFAULT NULL,
  `nome` varchar(55) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(100) DEFAULT NULL,
  `cod_postal` varchar(15) DEFAULT NULL,
  `nif` int DEFAULT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idLab` (`idLab`),
  KEY `fk_utilizador_user1_idx` (`user_id`),
  CONSTRAINT `fk_utilizador_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`),
  CONSTRAINT `utilizador_ibfk_1` FOREIGN KEY (`idLab`) REFERENCES `laboratorio` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utilizador`
--

LOCK TABLES `utilizador` WRITE;
/*!40000 ALTER TABLE `utilizador` DISABLE KEYS */;
INSERT INTO `utilizador` VALUES (1,NULL,'Alice Lima','999 999 999','Rua das floras','1234567',123456789,2),(2,NULL,'Bernardo Ferreira','999 999 863','Rua do sol','1234521',456514325,3),(3,NULL,'Carolina Carvalho','981 929 999','Rua das luzes','6743431',231345553,4),(4,NULL,'Davi Oliveira','999 219 951','Rua das pessoas','6567143',543523144,5),(5,NULL,'Elisa Almeida','944 921 659','Rua das cabras','3214543',646524222,6),(6,NULL,'Fernando Castro','911 943 111','Rua das pedras','4324521',321343211,7),(7,2,'Gabriela Lima','911 922 933','Rua das cinzas','3213121',413243211,8),(8,1,'Heitor Lima','222 111 222','Rua das letras','4326577',874535222,9),(9,3,'Isabela Castro','111 222 333','Rua das floras','7865873',786834533,10),(10,4,'João Carvalho','922 964 444','Rua das floras','7657567',876896756,11),(11,5,'Larissa Silva','929 959 949','Rua das escolas','5687876',967575432,12);
/*!40000 ALTER TABLE `utilizador` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-06-09 23:14:47
