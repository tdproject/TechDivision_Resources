-- phpMyAdmin SQL Dump
-- version 3.2.2-rc1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 14, 2009 at 09:40 AM
-- Server version: 5.1.37
-- PHP Version: 5.3.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Create the database `resources`
--

CREATE DATABASE `resources`;
USE `resources`;

--
-- Database: `resources`
--

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE IF NOT EXISTS `resources` (
  `msg_key` varchar(255) NOT NULL,
  `locale` varchar(5) NOT NULL,
  `val` varchar(255) NOT NULL,
  PRIMARY KEY (`msg_key`,`locale`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`msg_key`, `locale`, `val`) VALUES
('test.key', 'de_DE', 'Testwert'),
('test.key', 'en_US', 'Testvalue');