-- phpMyAdmin SQL Dump
-- version 5.2.1-1.el8
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июн 10 2026 г., 16:31
-- Версия сервера: 8.0.44-cll-lve
-- Версия PHP: 7.2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `user5233_tcson`
--
CREATE DATABASE IF NOT EXISTS `user5233_tcson` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `user5233_tcson`;

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_api_tokens`
--

CREATE TABLE `messenger_api_tokens` (
  `id` int NOT NULL,
  `token_prefix` varchar(32) NOT NULL,
  `token_hash` char(64) NOT NULL,
  `label` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_api_tokens`
--

INSERT INTO `messenger_api_tokens` (`id`, `token_prefix`, `token_hash`, `label`, `is_active`, `created_at`, `expires_at`, `revoked_at`, `last_used_at`) VALUES
(1, 'mtsn_3fd92d298c2', '821da0c47219b8975fc850400c78e02819c863c8f800b2618d29655efafa0ba4', 'cv', 1, '2026-03-30 13:02:58', '2026-04-18 13:02:00', NULL, NULL),
(2, 'mtsn_9942984bbe0', '9e8a4b0ab8ea838bf283e6433c3b0daa42c6e674320576508cce9b9db0708b43', 'Администратор Максим Болонин', 1, '2026-04-06 18:05:41', '2026-12-13 18:05:00', NULL, '2026-06-10 13:30:53');

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_attachments`
--

CREATE TABLE `messenger_attachments` (
  `id` int NOT NULL,
  `attachment_uuid` char(36) NOT NULL,
  `chat_id` int NOT NULL,
  `message_id` int NOT NULL,
  `stored_name` varchar(255) NOT NULL,
  `storage_path` varchar(500) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `extension` varchar(20) NOT NULL,
  `size_bytes` bigint NOT NULL,
  `sha256` char(64) DEFAULT NULL,
  `uploaded_by_side` enum('site','central') NOT NULL,
  `uploaded_by_user_id` int DEFAULT NULL,
  `uploaded_by_user_name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_attachments`
--

INSERT INTO `messenger_attachments` (`id`, `attachment_uuid`, `chat_id`, `message_id`, `stored_name`, `storage_path`, `original_name`, `mime_type`, `extension`, `size_bytes`, `sha256`, `uploaded_by_side`, `uploaded_by_user_id`, `uploaded_by_user_name`, `is_deleted`, `created_at`, `deleted_at`) VALUES
(1, 'a02ea37f-eab7-4379-9154-d832e1be6e91', 1, 1, 'c0e4ce31a6783ccc143039fdef924243.xls', '2026/04/c0e4ce31a6783ccc143039fdef924243.xls', 'ава.xls', 'application/vnd.ms-excel', 'xls', 18944, '8b892fd4bf3331fad98298746077cad56dc4957514e6e0b342e38f8e19611f86', 'site', 5, 'Болонин Максим Петрович', 0, '2026-04-06 17:30:42', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_audit_log`
--

CREATE TABLE `messenger_audit_log` (
  `id` int NOT NULL,
  `actor_side` enum('site','central') NOT NULL,
  `actor_user_id` int DEFAULT NULL,
  `actor_user_name` varchar(255) DEFAULT NULL,
  `auth_mode` enum('session','bearer') NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_uuid` char(36) NOT NULL,
  `action` varchar(64) NOT NULL,
  `request_id` char(36) DEFAULT NULL,
  `result_code` varchar(64) NOT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_audit_log`
--

INSERT INTO `messenger_audit_log` (`id`, `actor_side`, `actor_user_id`, `actor_user_name`, `auth_mode`, `entity_type`, `entity_uuid`, `action`, `request_id`, `result_code`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'site', 5, 'Болонин Максим Петрович', 'session', 'api_token', 'mtsn_3fd92d298c2', 'create', NULL, 'ok', '192.168.65.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-03-30 13:02:58'),
(2, 'site', 5, 'Болонин Максим Петрович', 'session', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'create', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 17:30:42'),
(3, 'site', 5, 'Болонин Максим Петрович', 'session', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 17:30:42'),
(4, 'site', 5, 'Болонин Максим Петрович', 'session', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 17:31:29'),
(5, 'site', 5, 'Болонин Максим Петрович', 'session', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 17:36:03'),
(6, 'site', 5, 'Болонин Максим Петрович', 'session', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 17:37:03'),
(7, 'site', 5, 'Болонин Максим Петрович', 'session', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 17:49:26'),
(8, 'site', 5, 'Болонин Максим Петрович', 'session', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 18:05:10'),
(9, 'site', 5, 'Болонин Максим Петрович', 'session', 'api_token', 'mtsn_9942984bbe0', 'create', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 18:05:41'),
(10, 'central', NULL, 'Администратор Максим Болонин', 'bearer', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 18:32:52'),
(11, 'central', NULL, 'Администратор Максим Болонин', 'bearer', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'status', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 18:32:55'),
(12, 'central', NULL, 'Администратор Максим Болонин', 'bearer', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 18:32:55'),
(13, 'site', 5, 'Болонин Максим Петрович', 'session', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'read', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 18:33:02'),
(14, 'central', NULL, 'Администратор Максим Болонин', 'bearer', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'delete', NULL, 'ok', '34.118.57.84', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/26.3.1 Safari/605.1.15', '2026-04-06 18:33:16');

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_central_client_state`
--

CREATE TABLE `messenger_central_client_state` (
  `state_key` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_chats`
--

CREATE TABLE `messenger_chats` (
  `id` int NOT NULL,
  `chat_uuid` char(36) NOT NULL,
  `chat_no` int NOT NULL,
  `first_message_id` int DEFAULT NULL,
  `status` enum('new','in_progress','done','closed') NOT NULL DEFAULT 'new',
  `created_by_side` enum('site','central') NOT NULL,
  `created_by_user_id` int DEFAULT NULL,
  `created_by_user_name` varchar(255) NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by_side` enum('site','central') DEFAULT NULL,
  `deleted_by_user_id` int DEFAULT NULL,
  `deleted_by_user_name` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `last_activity_at` datetime NOT NULL,
  `closed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_chats`
--

INSERT INTO `messenger_chats` (`id`, `chat_uuid`, `chat_no`, `first_message_id`, `status`, `created_by_side`, `created_by_user_id`, `created_by_user_name`, `is_deleted`, `deleted_at`, `deleted_by_side`, `deleted_by_user_id`, `deleted_by_user_name`, `created_at`, `updated_at`, `last_activity_at`, `closed_at`) VALUES
(1, '268e466e-4bff-431c-b54e-4e24a9e1866d', 1000, 1, 'in_progress', 'site', 5, 'Болонин Максим Петрович', 1, '2026-04-06 18:33:16', 'central', NULL, 'Администратор Максим Болонин', '2026-04-06 17:30:42', '2026-04-06 18:33:16', '2026-04-06 18:32:55', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_chat_participants`
--

CREATE TABLE `messenger_chat_participants` (
  `id` int NOT NULL,
  `chat_id` int NOT NULL,
  `user_id` int NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `joined_at` datetime NOT NULL,
  `left_at` datetime DEFAULT NULL,
  `added_by_side` enum('site','central') NOT NULL,
  `added_by_user_id` int DEFAULT NULL,
  `added_by_user_name` varchar(255) NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_chat_participants`
--

INSERT INTO `messenger_chat_participants` (`id`, `chat_id`, `user_id`, `user_name`, `is_active`, `joined_at`, `left_at`, `added_by_side`, `added_by_user_id`, `added_by_user_name`, `updated_at`) VALUES
(1, 1, 5, 'Болонин Максим Петрович', 1, '2026-04-06 17:30:42', NULL, 'site', 5, 'Болонин Максим Петрович', '2026-04-06 17:30:42');

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_events`
--

CREATE TABLE `messenger_events` (
  `id` int NOT NULL,
  `event_uuid` char(36) NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_uuid` char(36) NOT NULL,
  `action` varchar(64) NOT NULL,
  `payload_json` json NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_events`
--

INSERT INTO `messenger_events` (`id`, `event_uuid`, `entity_type`, `entity_uuid`, `action`, `payload_json`, `created_at`) VALUES
(1, '92113a4f-c332-4e16-ac52-a5684751af69', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'chat_created', '{\"title\": \"mm\", \"status\": \"new\", \"chat_no\": 1000, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"site_code\": \"GOMEL\", \"display_name\": \"GOMEL-1000 mm\", \"last_activity_at\": \"2026-04-06T14:30:42+00:00\"}', '2026-04-06 17:30:42'),
(2, 'cdbcd587-ac57-45ee-a5fc-63bcd8de7fde', 'participant', 'c78fbb7a-2728-4904-b13d-b94bbf894b86', 'participant_added', '{\"user_id\": 5, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"is_active\": true, \"user_name\": \"Болонин Максим Петрович\", \"updated_at\": \"2026-04-06T14:30:42+00:00\", \"last_activity_at\": \"2026-04-06T14:30:42+00:00\"}', '2026-04-06 17:30:42'),
(3, 'b53519b4-5d1e-4df4-826f-2352df40b10a', 'message', '019dc562-42da-48ce-a369-92aabfdeed2c', 'message_created', '{\"body_text\": \"mm\", \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"created_at\": \"2026-04-06T14:30:42+00:00\", \"updated_at\": \"2026-04-06T14:30:42+00:00\", \"author_side\": \"site\", \"message_uuid\": \"019dc562-42da-48ce-a369-92aabfdeed2c\", \"author_user_id\": 5, \"author_user_name\": \"Болонин Максим Петрович\", \"is_first_message\": true, \"last_activity_at\": \"2026-04-06T14:30:42+00:00\"}', '2026-04-06 17:30:42'),
(4, '2b4950a3-f33c-4181-af8b-c11724fda0fa', 'attachment', 'a02ea37f-eab7-4379-9154-d832e1be6e91', 'attachment_added', '{\"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"mime_type\": \"application/vnd.ms-excel\", \"size_bytes\": 18944, \"message_uuid\": \"019dc562-42da-48ce-a369-92aabfdeed2c\", \"original_name\": \"ава.xls\", \"attachment_uuid\": \"a02ea37f-eab7-4379-9154-d832e1be6e91\", \"last_activity_at\": \"2026-04-06T14:30:42+00:00\"}', '2026-04-06 17:30:42'),
(5, 'f26f9a45-93ca-46a2-aada-cc053e3b517c', 'read', '5ce9a625-3afe-4d7f-a22b-f95f26920b62', 'chat_read_changed', '{\"side\": \"site\", \"user_id\": 5, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Болонин Максим Петрович\", \"reader_key\": \"site:5\", \"last_read_at\": \"2026-04-06T14:30:42+00:00\", \"manual_unread\": false, \"last_read_event_id\": 4}', '2026-04-06 17:30:42'),
(6, '48007292-5fca-4cf3-b4e0-e809746b0f67', 'read', '4708799e-e6f2-4b74-ad0a-08056be35dbc', 'chat_read_changed', '{\"side\": \"site\", \"user_id\": 5, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Болонин Максим Петрович\", \"reader_key\": \"site:5\", \"last_read_at\": \"2026-04-06T14:31:29+00:00\", \"manual_unread\": false, \"last_read_event_id\": 4}', '2026-04-06 17:31:29'),
(7, 'f63516d9-6264-4ab2-979e-ab3db7156997', 'read', '155f089d-af72-4760-989b-ba19897baeb9', 'chat_read_changed', '{\"side\": \"site\", \"user_id\": 5, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Болонин Максим Петрович\", \"reader_key\": \"site:5\", \"last_read_at\": \"2026-04-06T14:36:03+00:00\", \"manual_unread\": false, \"last_read_event_id\": 4}', '2026-04-06 17:36:03'),
(8, '2c7dca37-bc73-42f0-a01b-3d6c4fa9f627', 'read', '6f4cdd6a-42e0-4049-be65-e6a93421545d', 'chat_read_changed', '{\"side\": \"site\", \"user_id\": 5, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Болонин Максим Петрович\", \"reader_key\": \"site:5\", \"last_read_at\": \"2026-04-06T14:37:03+00:00\", \"manual_unread\": false, \"last_read_event_id\": 4}', '2026-04-06 17:37:03'),
(9, '5a9c3459-2b9c-4e9d-afa1-c864d1505c93', 'read', '3859e14b-6041-490b-938b-6005a8927cdb', 'chat_read_changed', '{\"side\": \"site\", \"user_id\": 5, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Болонин Максим Петрович\", \"reader_key\": \"site:5\", \"last_read_at\": \"2026-04-06T14:49:26+00:00\", \"manual_unread\": false, \"last_read_event_id\": 4}', '2026-04-06 17:49:26'),
(10, '90ab6c22-33aa-4042-8494-ef1eeeb0064e', 'read', '7b99e9a2-c3e1-4ba4-a514-faa76be29913', 'chat_read_changed', '{\"side\": \"site\", \"user_id\": 5, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Болонин Максим Петрович\", \"reader_key\": \"site:5\", \"last_read_at\": \"2026-04-06T15:05:10+00:00\", \"manual_unread\": false, \"last_read_event_id\": 4}', '2026-04-06 18:05:10'),
(11, 'c4f86fe3-d64b-40ff-bd2b-7b5cbc3ef7d3', 'read', '8d7f8de3-9848-4e12-a718-f8c34a4e3602', 'chat_read_changed', '{\"side\": \"central\", \"user_id\": null, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Администратор Максим Болонин\", \"reader_key\": \"central\", \"last_read_at\": \"2026-04-06T15:32:52+00:00\", \"manual_unread\": false, \"last_read_event_id\": 4}', '2026-04-06 18:32:52'),
(12, '83d843a1-7dbb-44a6-81e1-5fd4076d1ded', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'chat_status_changed', '{\"title\": \"mm\", \"status\": \"in_progress\", \"chat_no\": 1000, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"site_code\": \"GMZD\", \"display_name\": \"GMZD-1000 mm\", \"last_activity_at\": \"2026-04-06T15:32:55+00:00\"}', '2026-04-06 18:32:55'),
(13, 'f9e4bfd4-4252-474a-8888-2743bc41de3d', 'read', '30476175-a55b-46a2-b12a-2171dbec9b11', 'chat_read_changed', '{\"side\": \"central\", \"user_id\": null, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Администратор Максим Болонин\", \"reader_key\": \"central\", \"last_read_at\": \"2026-04-06T15:32:55+00:00\", \"manual_unread\": false, \"last_read_event_id\": 12}', '2026-04-06 18:32:55'),
(14, 'e1863037-cfcf-41b7-b587-0d1e04acbfe0', 'read', '0d3c3fdb-ba5f-4733-8796-eb2296246e05', 'chat_read_changed', '{\"side\": \"site\", \"user_id\": 5, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"user_name\": \"Болонин Максим Петрович\", \"reader_key\": \"site:5\", \"last_read_at\": \"2026-04-06T15:33:02+00:00\", \"manual_unread\": false, \"last_read_event_id\": 12}', '2026-04-06 18:33:02'),
(15, 'b8e39698-cebf-468e-a479-18f0e41cd73f', 'chat', '268e466e-4bff-431c-b54e-4e24a9e1866d', 'chat_deleted', '{\"title\": \"mm\", \"status\": \"in_progress\", \"chat_no\": 1000, \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"site_code\": \"GMZD\", \"deleted_at\": \"2026-04-06T15:33:16+00:00\", \"display_name\": \"GMZD-1000 mm\", \"last_activity_at\": \"2026-04-06T15:32:55+00:00\"}', '2026-04-06 18:33:16');

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_external_requests`
--

CREATE TABLE `messenger_external_requests` (
  `id` int NOT NULL,
  `request_id` char(36) NOT NULL,
  `endpoint_name` varchar(100) NOT NULL,
  `response_json` json NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_external_requests`
--

INSERT INTO `messenger_external_requests` (`id`, `request_id`, `endpoint_name`, `response_json`, `created_at`) VALUES
(1, '90da7392-80da-49f3-9a5c-1d8259c6c4a4', 'read', '{\"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"event_ids\": [11], \"last_read_at\": \"2026-04-06T15:32:52+00:00\", \"manual_unread\": false, \"last_read_event_id\": 4}', '2026-04-06 18:32:52'),
(2, '55e65356-ccbf-4e29-8c63-4fb5a5535432', 'status', '{\"status\": \"in_progress\", \"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"event_ids\": [12]}', '2026-04-06 18:32:55'),
(3, '4113d537-6d2c-4afa-a07d-c16cc810b115', 'read', '{\"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"event_ids\": [13], \"last_read_at\": \"2026-04-06T15:32:55+00:00\", \"manual_unread\": false, \"last_read_event_id\": 12}', '2026-04-06 18:32:55'),
(4, '53f9baa7-ab8b-413b-adcb-eb06e111c231', 'delete', '{\"chat_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\", \"event_ids\": [15], \"deleted_at\": \"2026-04-06T15:33:16+00:00\", \"entity_type\": \"chat\", \"entity_uuid\": \"268e466e-4bff-431c-b54e-4e24a9e1866d\"}', '2026-04-06 18:33:16');

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` int NOT NULL,
  `message_uuid` char(36) NOT NULL,
  `chat_id` int NOT NULL,
  `author_side` enum('site','central') NOT NULL,
  `author_user_id` int DEFAULT NULL,
  `author_user_name` varchar(255) NOT NULL,
  `body_text` mediumtext NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_messages`
--

INSERT INTO `messenger_messages` (`id`, `message_uuid`, `chat_id`, `author_side`, `author_user_id`, `author_user_name`, `body_text`, `is_deleted`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '019dc562-42da-48ce-a369-92aabfdeed2c', 1, 'site', 5, 'Болонин Максим Петрович', 'mm', 0, '2026-04-06 17:30:42', '2026-04-06 17:30:42', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_message_edits`
--

CREATE TABLE `messenger_message_edits` (
  `id` int NOT NULL,
  `message_id` int NOT NULL,
  `version_no` int NOT NULL,
  `previous_body_text` mediumtext NOT NULL,
  `edited_by_side` enum('site','central') NOT NULL,
  `edited_by_user_id` int DEFAULT NULL,
  `edited_by_user_name` varchar(255) NOT NULL,
  `edited_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_reads`
--

CREATE TABLE `messenger_reads` (
  `id` int NOT NULL,
  `chat_id` int NOT NULL,
  `reader_key` varchar(64) NOT NULL,
  `side` enum('site','central') NOT NULL,
  `user_id` int DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `last_read_event_id` int DEFAULT NULL,
  `last_read_at` datetime NOT NULL,
  `manual_unread` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_reads`
--

INSERT INTO `messenger_reads` (`id`, `chat_id`, `reader_key`, `side`, `user_id`, `user_name`, `last_read_event_id`, `last_read_at`, `manual_unread`) VALUES
(1, 1, 'site:5', 'site', 5, 'Болонин Максим Петрович', 12, '2026-04-06 18:33:02', 0),
(2, 1, 'central', 'central', NULL, 'Администратор Максим Болонин', 12, '2026-04-06 18:32:55', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `messenger_settings`
--

CREATE TABLE `messenger_settings` (
  `id` int NOT NULL,
  `site_uuid` char(36) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `site_code` varchar(6) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `messenger_settings`
--

INSERT INTO `messenger_settings` (`id`, `site_uuid`, `site_name`, `site_code`, `created_at`, `updated_at`) VALUES
(1, '3e3bc318-96ae-4bb4-8e46-0eed55b6ca33', 'ТЦСОН Железнодорожного района г. Гомеля', 'GMZD', '2026-03-29 21:33:40', '2026-04-06 18:31:48');

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int NOT NULL,
  `type` enum('Мероприятие','Новость') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `freim` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `video_filename` varchar(255) DEFAULT NULL,
  `date` date NOT NULL,
  `created_by_user_id` int DEFAULT NULL,
  `created_by_login` varchar(255) DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `type`, `title`, `slug`, `description`, `freim`, `video_filename`, `date`, `created_by_user_id`, `created_by_login`, `created_by_name`) VALUES
(263, 'Мероприятие', 'Лучшая первичная организация!', 'luchshaya-pervichnaya-organizatsiya', 'По итогам 2025 года подведены результаты республиканского конкурса на лучшую первичную организацию Республиканского государственно-общественного объединения «Белорусское добровольное пожарное общество». Высокую оценку своей работы получила первичная организация учреждения «Территориальный центр социального обслуживания населения Желензнодорожного района г. Гомеля», занявшая почетное третье место.', '', NULL, '2026-04-01', 5, 'admin_maks', 'Болонин Максим Петрович'),
(264, 'Мероприятие', 'СЕМЬЯ БЕСЦЕННА🎈', 'semya-bestsenna', 'Специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" в рамках областной профилактической акции \"Счастливая семья- счастливое детство!\"👩‍❤️‍👨 для семей, состоящих на учёте в Центре проведено выездное профилактическое мероприятие  \"Семья бесценна\", направленное на профилактику детского травматизма в быту, на создание уважительного и доброжелательного отношения в семьях, на формирование  у родителей чувства ответственности за своих детей.', '', NULL, '2026-04-01', 5, 'admin_maks', 'Болонин Максим Петрович'),
(265, 'Мероприятие', 'СЕМЬЯ - САМОЕ ДОРОГОЕ', 'semya-samoe-dorogoe', 'Специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля\" совместно с ИДН ОВД администрации Железнодорожного района г.Гомеля для учащихся ГУО \"Средняя школа №8 г.Гомеля\", ГУО \"Средняя школа №30 г.Гомеля\" проведено профилактическое мероприятие \"Сохрани самое дорогое\", направленное на:\r\n📌 формирование у учащихся здорового образа жизни;\r\n📌профилактики преступлений и правонарушений, девиантного поведения;\r\n📌 формирование уважение к ключевым семейным ценностям: любовь, доверие, ответственность и взаимопомощь.\r\nВсе участники мероприятия приняли участие в социальном опросе 👩‍❤️‍👨\"СЕМЬЯ- ЭТО....\"', '', NULL, '2026-04-01', 5, 'admin_maks', 'Болонин Максим Петрович'),
(266, 'Новость', 'Профилактическая акция', 'profilakticheskaya-aktsiya', 'С 1 апреля по 1 июня на Гомельщине пройдет межведомственная профилактическая акция «Счастливая семья – счастливое детство!».\r\n\r\nВ рамках акции будут реализованы мероприятия, направленные на своевременное выявление семей, находящихся в трудной жизненной ситуации, профилактику и преодоление кризисных ситуаций.\r\n\r\nБудут проведены также профилактические беседы, круглые столы, выездные заседания комиссии по содействию занятости населения, диалоговые площадки с трудными подростками.', '', NULL, '2026-04-01', 5, 'admin_maks', 'Болонин Максим Петрович'),
(267, 'Новость', 'Республиканский субботник', 'respublikanskiy-subbotnik', 'Республиканский субботник пройдет в Беларуси 18 апреля. Соответствующее постановление подписал премьер-министр Александр Турчин. К работе подключится и трудовой десант Гомельщины. \r\n\r\nДенежные средства, заработанные от проведения весеннего субботника, будут направлены на создание постоянной экспозиции строящегося Национального исторического музея Беларуси и иные сопутствующие этому работы.', '', NULL, '2026-04-01', 5, 'admin_maks', 'Болонин Максим Петрович'),
(268, 'Новость', 'Активизируются и клещи', 'aktiviziruyutsya-i-kleschi', '🕷️Весна — время прогулок на природе. Но вместе с пробуждением природы активизируются и клещи — потенциальные переносчики опасных заболеваний. 🌤️🌳\r\n\r\nЧтобы отдых не обернулся неприятностями, важно заранее позаботиться о защите. \r\n\r\nКлещи обитают в:\r\n▫️ лесах и лесопарковых зонах;\r\n▫️ высокой траве и кустарниках;\r\n▫️ парках и на дачных участках;\r\n▫️ вдоль тропинок и дорожек.\r\n\r\nКак защитить себя во время прогулки:\r\n▪️ носите светлую одежду с длинными рукавами — на ней легче заметить клещей;\r\n▪️ заправьте брюки в носки или сапоги;\r\n▪️ наденьте головной убор. 👖🧢\r\n▪️ обработайте одежду и открытые участки тела средствами от клещей. 🧴\r\n▪️ каждые 1,5–2 часа осматривайте себя и спутников;\r\n▪️ особое внимание уделите шее, подмышкам, паховой области, за ушами и коже под коленями. 🔎\r\n▪️ если берёте с собой собаку или кошку, проверьте их шерсть после прогулки. 🐕\r\n\r\n🌿 Пусть весна принесёт только радость от общения с природой — будьте осторожны и берегите себя!', '', NULL, '2026-04-01', 5, 'admin_maks', 'Болонин Максим Петрович'),
(270, 'Новость', 'День единения двух стран', 'den-edineniya-dvuh-stran', 'Председатель Высшего Государственного совета Союзного государства, Президент Беларуси Александр Лукашенко направил поздравление с Днем единения народов Беларуси и России.\r\nГлава государства отметил, что 2 апреля 1996 года Беларусь и Россия, основываясь на общности судеб, опираясь на волю обоих народов к дальнейшему сближению, подписали Договор об образовании Сообщества Беларуси и России:\r\n«С тех пор прошло тридцать лет. Векторы политики Союзного государства остаются неизменными. Это мир и согласие, единство и прогресс, благополучие и социальная справедливость».\r\nАлександр Лукашенко подчеркнул, что братские народы дорожат традициями доверия, по праву гордятся общей историей, свято чтут память предков и бережно хранят духовно-нравственные ценности:\r\n«Взаимовыгодное партнерство в различных сферах, растущие объемы торговли, реализация двусторонних программ и проектов открывают новые возможности для устойчивого повышения благосостояния наших народов».', '', NULL, '2026-04-02', NULL, 'telegramnews', 'Telegram News Import'),
(272, 'Новость', 'Фото дня 2 апреля', 'foto-dnya-2-aprelya', 'Фото дня 2 апреля', '', NULL, '2026-04-02', NULL, 'telegramnews', 'Telegram News Import'),
(273, 'Мероприятие', 'Быть женщиной - миссия', 'byt-zhenschinoy-missiya', '✅В День единения народов Беларуси и России в ТЦСОН прошел круглый стол на важную и глубокую тему — «Быть женщиной — миссия созидания».\r\n⚡️Мероприятие стало площадкой для открытого диалога о роли женщины в современном обществе, её вкладе в укрепление семейных ценностей, развитие культуры, образования и социальной сферы.\r\nУчастники обсудили:\r\n👍как женская энергия и мудрость помогают созидать гармоничное общество;\r\n👍какие возможности открываются перед женщинами в Беларуси и России сегодня;\r\n👍важность поддержки женских инициатив на национальном и межгосударственном уровнях;\r\n👍способы укрепления сотрудничества между женскими организациями двух стран.\r\n❗️Особую ценность дискуссии придали выступления представителей ПО ОО «Белорусский союз женщин». Гости поделились опытом реализации значимых социальных проектов, рассказали о текущих инициативах и подчеркнули, как важно сохранять и развивать традиции взаимопомощи и солидарности.\r\n#год_белорусской_женщины #год_белорусской_женщины', '', NULL, '2026-04-02', NULL, 'telegramnews', 'Telegram News Import'),
(274, 'Мероприятие', 'Забота и уважение', 'zabota-i-uvazhenie', '👵🏻ЗАБОТА И УВАЖЕНИЕ👴🏻\r\n📌\"Самое важное - ценность уважения и заботы о старшем поколении\"📌\r\nСпециалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" совместно с сотрудниками ОВД Железнодорожного района 🤝 в рамках выездной акции \"Территория заботы и уважения\" посещены пожилые граждане, находящиеся в наиболее уязвимых социальных обстоятельствах, в \"группе риска\" с целью :\r\n📌проведения профилактических бесед о недопущении противоправных действий в сфере семейно-бытовых отношений, по формированию здорового образа жизни;\r\n📌мотивирования к лечению от алкогольной зависимости;\r\n📌 выездных консультаций психолога, направленных на разрешение трудных жизненных ситуаций, информирования о телефонах \"Доверие\" экстренной психологической службы.', '', NULL, '2026-04-02', NULL, 'telegramnews', 'Telegram News Import'),
(275, 'Мероприятие', 'Чистый четверг', 'chistyy-chetverg', '🍃ЧИСТЫЙ ЧЕТВЕРГ\r\nв ТЦСОН 🍃\r\nСделаем наш город чище вместе!❤️', '', NULL, '2026-04-02', NULL, 'telegramnews', 'Telegram News Import'),
(276, 'Мероприятие', 'Особенные люди, особый мир', 'osobennye-lyudi-osobyy-mir', '✅Представители управления социальной защиты администрации Железнодорожного района г.Гомеля совместно с молодыми людьми с инвалидностью отделения социальной реабилитации, абилитации инвалидов ТЦСОН приняли участие в мероприятии, посвящённом Всемирному дню распространения информации о проблеме аутизма.\r\nЦель — привлечь внимание общества к вопросам поддержки людей с расстройствами аутистического спектра, повысить уровень информированности и способствовать формированию инклюзивной среды.\r\nБлагодарим всех участников за активное вовлечение и неравнодушие!', '', NULL, '2026-04-02', NULL, 'telegramnews', 'Telegram News Import'),
(277, 'Мероприятие', 'Весна славянского единства', 'vesna-slavyanskogo-edinstva', '💥Сегодня в отделении дневного пребывания для граждан пожилого возраста ТЦСОН звучала музыка, объединяющая сердца.\r\n✨В рамках  клуба «Вдохновение», в теплой и уютной  обстановке состоялся праздничный концерт «Весна славянского единства», приуроченный ко Дню единения народов Беларуси🇧🇾 и России🇷🇺.\r\nВ зале звучали песни и стихи на русском и белорусском языке о весне, Родине и дружбе.\r\nПраздник напомнил: мы сильнее вместе🇧🇾🇷🇺, а единство наших культур — это наша общая весна.', '', NULL, '2026-04-02', NULL, 'telegramnews', 'Telegram News Import'),
(278, 'Мероприятие', 'Акция «Мы вместе»', 'aktsiya-my-vmeste', '🏞️ В День единения народов Беларуси и России  волонтёры «серебряного» возраста отряда  «Зов Сердца» совместно  со специалистами  отделения  для граждан пожилого возраста ТЦСОН  провели  акцию 🤝 «Мы вместе».\r\n💥Суть акции — напомнить, что у нас общая история, общие ценности и одна большая душа на двоих.\r\nНаши активные «серебряные» добровольцы показали, что дружба между братскими народами начинается с заботы о ближнем и живого общения.\r\n﻿Отряд «Зов Сердца» и специалисты ТЦСОН благодарят всех, кто откликнулся. Ведь когда мы вместе — мы непобедимы! 🇧🇾❤️🇷🇺', '', NULL, '2026-04-02', NULL, 'telegramnews', 'Telegram News Import'),
(279, 'Новость', 'Соцуслуги не выходя из дома', 'sotsuslugi-ne-vyhodya-iz-doma', '💥Сколько соцуслуг Минтруда и соцзащиты можно получить, не выходя из дома?\n\"Сегодня дистанционно можно заказать 41 вид социальных услуг\", - рассказала заместитель министра труда и социальной защиты Юлия Бердникова в проекте БЕЛТА \"Страна говорит\".\n🖥 С середины прошлого года на портале социальной защиты населения работает личный кабинет физического лица.\n➖➖➖➖➖➖\n🖱Онлайн-заказ социальных услуг: оформляйте заявки на необходимые услуги прямо в своем телефоне/компьютере:\n✅Услуги сиделки (помощника по уходу)\n✅Социального работника\n✅Услуги няни\n✅И другие виды социальной поддержки\n‼️Важно: Заказывать услуги можно не только для себя, но и для своих родственников или соседей, нуждающихся в помощи.\n➖➖➖\n✅ Сервисы доступны 24/7 из любого места.\n✅ Интуитивно понятный интерфейс, адаптированный для всех категорий граждан.\n✅ Вход в личный кабинет осуществляется через надежную Межбанковскую систему идентификации (МСИ).\n➡️ Портал социальной защиты\nPlease open Telegram to view this post\nVIEW IN TELEGRAM', '', NULL, '2026-04-03', NULL, 'telegramnews', 'Telegram News Import'),
(280, 'Мероприятие', 'Совет против насилия', 'sovet-protiv-nasiliya', 'В администрации Железнодорожного района г.Гомеля состоялось внеочередное заседание межведомственного совета по оказанию помощи пострадавшим от домашнего насилия. В ходе заседания рассмотрены:\r\n📌 трудные жизненные ситуации граждан, где было необходимо незамедлительное межведомственное взаимодействие всех субъектов профилактики;\r\n📌организация работы субъектов профилактики, направленной на предупреждение противоправных действий в сфере семейно-бытовых отношений.', '', NULL, '2026-04-03', NULL, 'telegramnews', 'Telegram News Import'),
(282, 'Мероприятие', 'Занятия клуба «Надежда»', 'zanyatiya-kluba-nadezhda', 'В отделении дневного пребывания для граждан пожилого возраста ТЦСОН продолжаются активные занятия в рамках клуба «Надежда».\nПосетители с большим энтузиазмом выполняют комплексы оздоровительной гимнастики, которые способствуют улучшению общего самочувствия и повышению качества жизни.\nОсобое внимание уделяется дыхательной гимнастике — важному элементу, который помогает укрепить здоровье, насытить организм кислородом и повысить жизненный тонус.\nРегулярные тренировки дарят посетителям заряд энергии и хорошее настроение.', '', NULL, '2026-04-03', NULL, 'telegramnews', 'Telegram News Import'),
(283, 'Новость', 'Безопасность для детей', 'bezopasnost-dlya-detey', 'Разговор с незнакомцем, \"безобидный\" подарок, предложение подвезти или просьба проводить домой — все это может выглядеть вполне обычно. Но именно в таких ситуациях ребенку важно понимать, как правильно себя вести.\nВ карточках — простые и важные правила безопасности на улице.\nИсточник: Милиция Минска', '', NULL, '2026-04-04', NULL, 'telegramnews', 'Telegram News Import'),
(284, 'Новость', 'Кампания по установке АПИ', 'kampaniya-po-ustanovke-api', '⚡ Маленький прибор спасает тысячи жизней: в стране стартовала масштабная кампания по установке АПИ\nПожар не выбирает время, но чаще всего он приходит внезапно – под покровом ночи, когда люди наиболее уязвимы. Сон притупляет бдительность, а едкий дым коварен: достаточно нескольких вдохов, чтобы человек уже не смог проснуться. Однако есть простое и доступное решение, способное стать персональным «сторожем» в каждом доме – это автономный пожарный извещатель (АПИ).\nСегодня в республике дан старт широкомасштабной информационной кампании, цель которой – напомнить каждому о жизненной необходимости установки АПИ.\nСтатистика – вещь упрямая, и в вопросах пожарной безопасности она говорит в пользу извещателя. АПИ признан одним из самых эффективных средств раннего обнаружения возгорания. С 2002 года, когда в нашей стране началось массовое внедрение этого прибора, были спасены жизни более 2,7 тыс. человек.Только в прошлом году благодаря пронзительному сигналу извещателя спаслись 67 человек, включая 14 детей.', '', NULL, '2026-04-04', NULL, 'telegramnews', 'Telegram News Import'),
(285, 'Новость', 'Соцобслуживание на дому', 'sotsobsluzhivanie-na-domu', '⚡️ Всегда актуально. Социальное обслуживание на дому\n▪️Услуги социального работника\nпредоставляются нетрудоспособным гражданам*\n▪️Услуги сиделки\nпредоставляются нетрудоспособным гражданам, имеющим резко выраженные нарушения (утратившим) способности к самообслуживанию и передвижению*\n▪️Услуги няни\nдля семей, воспитывающих двойню и более детей до исполнения 3 лет; семьям, воспитывающим ребенка-инвалида в возрасте до 18 лет\n▪️Услуга дневного присмотра\nпредоставляется нетрудоспособным гражданам, утративших способность осуществлять контроль поведения, в том числе для граждан с деменцией*\n▪️Долговременный уход\nинвалидам I и II групп и неработающим гражданам в возрасте 65 лет и старше, нуждающихся в одновременном оказании социальных и медицинских услуг, далее\n▪️Дистанционное соцобслуживание\nнетрудоспособным гражданам; инвалидам I, II, III группы; гражданам, находящимся в трудной жизненной ситуации, далее\n✅ Как оформить?\n▪️Обратиться в ТЦСОН\n➖➖➖\n*при наличии мед. показаний и отсутствии мед. противопоказаний', '', NULL, '2026-04-06', NULL, 'telegramnews', 'Telegram News Import'),
(286, 'Новость', 'Правила содержания животных', 'pravila-soderzhaniya-zhivotnyh', '🪟🪟🪟🪟🪟🪟\n❕Законодательство Республики Беларусь четко регламентирует правила содержания домашних животных. В частности, кошки и собаки, проживающие в жилых помещениях, подлежат обязательной регистрации, а за владение собакой необходимо уплачивать налог.\n🗓 С 1 июля 2026 г. вступают в силу новые правила налогообложения владельцев собак: налог придется платить даже за незарегистрированных питомцев. Это значит, что владельцам четвероногих друзей пора ознакомиться с актуальными требованиями законодательства.\n📃 Для повышения уровня соблюдения законодательства уже в марте владельцы жилья начали получать напоминания о необходимости зарегистрировать домашнее животное. Такая информация размещается в бумажных и электронных счетах‑извещениях на оплату жилищно‑коммунальных услуг – и будет появляться там вплоть до 1 июля 2026 г.\n⚡️ Разберемся, где официально зарегистрировать домашнее животное и соблюсти все требования законодательства.\n🪟🪟🪟 Процедура регистрации животного‑компаньона осуществляется через службу «одно окно». Процедура регистрации бесплатная и выполняется в течение одного рабочего дня.\n🌱Подробности на нашем сайте minjust.gov.by\n💪💪💪💪| Сайт | Telegram | Instagram | YouTube\nPlease open Telegram to view this post\nVIEW IN TELEGRAM', '', NULL, '2026-04-07', NULL, 'telegramnews', 'Telegram News Import'),
(287, 'Новость', 'ТикТок звезда из ТЦСОН', 'tiktok-zvezda-iz-ttsson', '💥 Парень из Ивацевичского ТЦСОН собирает тысячи просмотров в TikTok\nВ территориальном центре социального обслуживания населения в Ивацевичах делают звёзд.\n😉 С нуля и без продакшена.\n➡️А ведь если бы несколько лет назад сотрудникам центра сказали, что ролики ТЦСОН будут собирать такое количество просмотров, они бы, наверное, очень удивились.\n\"Сперва выкладывали поделки, которые сделали наши ребята, сами занятия, подготовку к мероприятиям.\nПотом в кадре все чаще стал появляться Алексей. Мы не сразу начали его снимать.\nПоставили пару роликов с фестиваля в TikTok, и люди стали писать, какой молодец, какой умничка. Пошли комментарии, и мы начали с этим работать\", – отмечает заведующая отделением.\nPlease open Telegram to view this post\nVIEW IN TELEGRAM', '', NULL, '2026-04-07', NULL, 'telegramnews', 'Telegram News Import'),
(288, 'Мероприятие', 'Бильярдный час', 'bilyardnyy-chas', 'Бильярдный час в отделении дневного пребывания 🎱\n💥 Для посетителей отделения дневного пребывания граждан пожилого возраста ТЦСОН запущена новая ежемесячная активность: освоение бильярда — увлекательной игры, которая тренирует меткость, логику и поддерживает тонус.\nУчастники с большим удовольствием осваивают азы: учатся правильно держать кий, прицеливаться и наносить точные удары по шарам.\nПрекрасное времяпровождение за зелёным столом. Отдыхаем с пользой и хорошим настроением! 👍\nПрисоединяйтесь !😊\nБлагодарим руководство ООО \"Ленжанторг\" за предоставленную для наших посетителей возможность поактивничать!', '', NULL, '2026-04-07', NULL, 'telegramnews', 'Telegram News Import'),
(289, 'Мероприятие', 'День здоровья в ТЦСОН', 'den-zdorovya-v-ttsson', '«Возраст — не помеха активности»: посетители отделения дневного пребывания отметили Всемирный день здоровья.💪👍🫶\n«Для нас важно показать, что забота о здоровье — это ежедневный и очень радостный труд. Наши посетители с удовольствием осваивают дыхательные практики» — отметили руководители клубов.\nПосетители нашего отделения доказали, что бодрость духа не зависит от возраста.\nСекрет прост: движение каждый день и хорошая компания подарят заряд энергии и напомнят: чтобы быть здоровым, не нужно ждать особого дня — нужно начинать с улыбки прямо сейчас! ❤️', '', NULL, '2026-04-07', NULL, 'telegramnews', 'Telegram News Import'),
(291, 'Мероприятие', 'Семья без опасности', 'semya-bez-opasnosti', '#СемьяБезОпасности #СемьяБезОпасности🙅‍♀️\r\nСпециалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" в рамках областной профилактической акции \"Счастливая семья- счастливое детство\" дан старт пилотному проекту🙅‍♀️ \"СЕМЬЯ БЕЗ ОПАСНОСТИ\"🙅‍♀️.\r\nСпециалистами Центра в вечернее время, в предпраздничные и праздничные дни обследуют семьи, чьи дети признаны находящимися в социально опасном положении и семьи из \"группы риска\" Данный проект направлен на:\r\n📌поддержку безопасного и гармоничного семейного пространства;\r\n📌 преодоление кризисных ситуаций в семьях;\r\n📌предупреждение детского травматизма в быту и профилактику противоправных действий в сфере семейно-бытовых отношений.', '', NULL, '2026-04-08', NULL, 'telegramnews', 'Telegram News Import'),
(292, 'Мероприятие', 'Семья: от Я до МЫ', 'semya-ot-ya-do-my', '#Счастливаясемья #Счастливаясемья- счастливое детство!\n🎈СЕМЬЯ: от \"Я\" до \"МЫ\"🎈\nСпециалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" в рамках областной профилактической акции \"Счастливая семья- счастливое детство!\"👩‍❤️‍👨 для семей из категории лиц из числа детей - сирот и детей, оставшихся без попечения родителей, воспитывающих малолетних детей, проведено мероприятие \"СЕМЬЯ: от \"Я\" до \"МЫ\"\", направленное на создание уважительного и доброжелательного отношения в семьях, на формирование у родителей семейных ценностей и чувства ответственности за своих детей.\nУчастникам мероприятия вручили памятные сувениры, изготовленные молодыми инвалидами, посещающими отделение социальной реабилитации, абилитации инвалидов.👏🏻.', '', NULL, '2026-04-09', NULL, 'telegramnews', 'Telegram News Import'),
(293, 'Мероприятие', 'Чистый четверг в ТЦСОН', 'chistyy-chetverg-v-ttsson', '🍃ЧИСТЫЙ ЧЕТВЕРГ🍃\n✅В рамках традиционной акции сотрудники ТЦСОН вышли на уборку прилегающей территории. Дружно и слаженно очистили детскую площадку, убрали листву и мелкий мусор, навели порядок перед зданиями.\nТеперь территория выглядит опрятно и ухоженно — приятное и безопасное пространство для отдыха посетителей.\n«Чистый четверг» стал доброй привычкой, которая объединяет коллектив и создаёт уют для всех, кто находится в отделении.', '', NULL, '2026-04-09', NULL, 'telegramnews', 'Telegram News Import'),
(294, 'Мероприятие', 'Школа здоровья: авитаминоз', 'shkola-zdorovya-avitaminoz', '💥В отделении дневного пребывания для граждан пожилого возраста ТЦСОН в рамках клуба «Школа здоровья»💪 прошло очередное занятие на тему: «Что такое авитаминоз и как с ним бороться».\nПосетители узнали, почему длительная нехватка витаминов🍊🥝🍎🥦🥕 опасна именно в пожилом возрасте, и получили практические советы по профилактике, укреплению иммунитета и сохранению здорового образа жизни.', '', NULL, '2026-04-09', NULL, 'telegramnews', 'Telegram News Import'),
(295, 'Новость', 'Год Директивы №12', 'god-direktivy-12', '🔥🔥Ровно год назад, 9 апреля 2025 года, Президентом Беларуси был подписан один из важнейших документов страны – Директива № 12 «О реализации основ идеологии белорусского государства»\n📖Это своего рода букварь гражданина страны. Это емкое и сжатое описание жизни белорусского общества и государства в преломлении прошлого, настоящего и будущего. Расставлены акценты в понимании белорусских смыслов и нарративов.\n🗣Документ стал итогом борьбы, которую белорусский народ вел последние годы. Начиная с 2020 года страну терроризировали в СМИ в идеологическом и историческом планах. Народ видел, как Запад пытался развалить белорусскую государственность, стереть белорусские корни и идентификацию любыми возможными способами.\nПОДПИСАТЬСЯ | ПРИСЛАТЬ НОВОСТЬ', '', NULL, '2026-04-09', NULL, 'telegramnews', 'Telegram News Import'),
(296, 'Новость', 'На родной земле', 'na-rodnoy-zemle', '🌿Республиканская инициатива \"На родной земле - Живи Как Хозяин\"🌱\n#НаРоднойЗемлеЖивиКакХозяин #НаРоднойЗемлеЖивиКакХозяин', '', NULL, '2026-04-10', NULL, 'telegramnews', 'Telegram News Import'),
(297, 'Мероприятие', 'Пасхальное творчество', 'pashalnoe-tvorchestvo', 'Творчество не знает границ.\n💒Пасха — праздник, объединяющий сердца❤️ и пробуждающий в душах самые светлые чувства🌱.\nСегодня в отделении социальной реабилитации , абилитации инвалидов учреждения «Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля» посетители приняли участие в межрегиональном дистанционном конкурсе «Пасхальный узор!»\n🥚🖌️🥚.\nВ этом году радость торжества нашла свое отражение в работах ребят.🖼️', '', NULL, '2026-04-10', NULL, 'telegramnews', 'Telegram News Import'),
(298, 'Новость', 'Неделя охраны труда', 'nedelya-ohrany-truda', '⚡️ Во всех районах пройдут мероприятия, популяризирующие соблюдение требований охраны труда, трудовой и производственной дисциплины, в том числе семинары по актуальным вопросам, прямые телефонные линии\r\nОблисполкомом организовано:\r\nпроверка знаний по вопросам охраны труда для руководителей структурных подразделений, руководителей социальных учреждений области;\r\nвыезд областной межведомственной рабочей группы.\r\nЦель – привлечь внимание каждого работника к безопасному труду.', '', NULL, '2026-04-10', NULL, 'telegramnews', 'Telegram News Import'),
(299, 'Мероприятие', 'Память сердца', 'pamyat-serdtsa', 'Накануне Международного дня освобождения узников фашистских концлагерей в отделении дневного пребывания для граждан пожилого возраста ТЦСОН  состоялся круглый стол «Память сердца».\n💥 За одним столом собрались те, кто пережил ужасы войны детьми, и те, кто родился уже в мирное время.\n🎗 Разговор по душам, чай со сладостями и музыка создали тёплую душевную атмосферу. Говорили не о боли, а о силе духа. О том, как люди выживали и оставались людьми.\n🎵 Настоящим подарком для всех стало участие в мероприятии  и выступление представительницы колледжа строителей Татьяны Ладюковой. Знакомые мелодии под аккордеон согрели сердца и объединили всех: и тех, кто помнит войну, и тех, кто знает о ней только из книг.\n🎁 Кульминацией встречи стало вручение памятных сувениров узникам и детям войны — знак благодарности за их жизненный подвиг.\n«Память сердца» — это напоминание: истинная память живёт в человеческой заботе и уважении, это то, что не стирается годами.', '', NULL, '2026-04-10', NULL, 'telegramnews', 'Telegram News Import'),
(300, 'Новость', 'День освобождения узников', 'den-osvobozhdeniya-uznikov', '11 апреля - Международный день освобождения узников фашистских концлагерей.\nЭта дата была установлена в память об интернациональном восстании узников концентрационного лагеря Бухенвальд, которое произошло 11 апреля 1945 года.', '', NULL, '2026-04-11', NULL, 'telegramnews', 'Telegram News Import'),
(301, 'Новость', 'Цифра дня: ущерб нацистов', 'tsifra-dnya-uscherb-natsistov', '⚡️⚡️⚡️Цифра дня: более $6 трлн.\nИменно столько на сегодняшний день составляет ущерб белорусскому народу, причиненный нацистами и их пособниками в годы ВОВ.\nРуководитель следственной группы Генпрокуратуры по расследованию уголовного дела по факту геноцида белорусского народа Валерий Толкачев:\n«Мы раньше, на октябрь 2022 года, говорили, что это примерно $2,5 трлн. Но с учетом того, что мы посчитали количество уничтоженных населенных пунктов, вывезенные частично культурные ценности, то соответственно ущерб в тоннах золота увеличился. Ввиду того, что биржевые котировки золота изменяются, на январь 2026 года сумма составляет более $6 трлн».\nПо данным на 15 марта 2026 года, на территории Беларуси в годы оккупации полностью либо частично сожжено не менее 12 868 населенных пунктов. Это на 3668 больше, чем считалось изначально.\nПОДПИСАТЬСЯ | ПРИСЛАТЬ НОВОСТЬ', '', NULL, '2026-04-11', NULL, 'telegramnews', 'Telegram News Import'),
(304, 'Новость', 'Фото дня 3 апреля', 'foto-dnya-3-aprelya', 'Публикация содержит видео. Оригинал в Telegram: https://t.me/tcsonrw_gomel/8514', '', 'a4bc0cfdb43f83edcfae23dacd562d47.mp4', '2026-04-03', NULL, 'api-service', 'API Service'),
(305, 'Новость', 'Благо дарю', 'blago-daryu', '☀️БЛАГО🌱 ДАРЮ☀️\nСпециалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" и лицами из числа детей - сирот и детей, оставшихся без попечения родителей, учащимися УО \"Гомельский государственный колледж строителей\",\nв рамках благотворительной акции \"БЛАГО🌱 ДАРЮ\" уделили особое внимание пожилому человеку из социально уязвимой категории граждан.\nСегодня Анастасия Антоновна праздновала своё 87-летие!\n🌱 ребята подарили музыкальное поздравление, частичку 🫶🏻тепла, заботы, внимания человеку старшего поколения;\n🌱Анастасия Антоновна пожелала всем душевного семейного тепла, здоровья, солнечного настроения и никогда не сдаваться, как бы сложно не было\n🎁Имениннику сегодняшнего дня самые тёплые поздравления!с Днём Рождения!\nЖелаем здоровья, душевного спокойствия!🎁', '', '553509528a0b43af3238b3f1657b7112.mp4', '2026-04-07', NULL, 'api-service', 'API Service'),
(306, 'Мероприятие', 'Пасхальное чаепитие в ТЦСОН', 'tg-8587-pashalnoe-chaepitie-v-tczson', '💥В отделении для граждан пожилого возраста ТЦСОН прошло праздничное чаепитие, посвящённое Светлому Христову Воскресению.\n🌿 В уютной обстановке за чашкой чая с куличами и крашеными яйцами собравшиеся вспомнили историю и обычаи главного православного праздника, с азартом играли в «битки»  и отвечали на вопросы познавательной викторины.\nВстреча получилась тёплой, светлой и по-настоящему домашней.', '', NULL, '2026-04-14', NULL, 'usernews', 'Telegram Import'),
(307, 'Мероприятие', 'Осторожно! Мошенники', 'tg-8599-ostorozhno-moshenniki', '«Осторожно! Мошенники!»\n💥 Сегодня в отделении дневного пребывания для граждан пожилого возраста ТЦСОН прошла важная встреча, посвященная кибербезопасности.\n‼️ Заведующая отделением не просто рассказала о новых схемах телефонных мошенников, но и вручила каждому посетителю памятки с четкой инструкцией: что делать, если просят назвать код из СМС или перевести деньги на «безопасный счет».\nСамым ценным стал обмен опытом: наши подопечные поделились реальными историями общения с злоумышленниками и пришли к мнению, что бдительность и знание правил обязательно помогут не попасть на их уловки!\n⚠️ Будьте внимательны и предупредите своих родственников: мошенники постоянно меняют тактику, но главное правило неизменно — ПЕРЕЗВОНИТЕ тому, кого вам называют по телефону.', '', NULL, '2026-04-16', NULL, 'usernews', 'Telegram Import'),
(308, 'Мероприятие', 'Выставка о Чернобыле', 'tg-8603-vystavka-o-chernobyle', '💥Сегодня посетители отделения дневного пребывания для граждан пожилого возраста ТЦСОН побывали на выставке «Катастрофа на Чернобыльской АЭС — трагедия, память, преодоление», которая состоялась во Дворце Румянцевых и Паскевичей и приурочена к 40-й годовщине аварии.\nСначала для гостей провели лекцию в зале дворца, а затем они перешли к осмотру экспозиции.\nЧернобыльская катастрофа стала крупнейшей техногенной аварией в истории человечества.\n‼️Важно помнить не только о масштабах трагедии, но и о подвиге ликвидаторов, которые рискуя жизнью и здоровьем, остановили распространение смертоносной радиации.\nПамять о Чернобыле учит нас ответственности перед будущими поколениями и бережному отношению к технологиям, способным как служить людям, так и стать источником общей беды', '', NULL, '2026-04-16', NULL, 'usernews', 'Telegram Import'),
(309, 'Мероприятие', 'Моя семья - самая, самая', 'tg-8610-moya-semya-samaya-samaya', '💥МОЯ СЕМЬЯ-САМАЯ, САМАЯ...💥\nЧто такое семья? Люди, которым ты нужен. В радости и горе, в мелочах или целом, они приходят в нужную минуту и остаются с тобой, несмотря ни на что\nВ рамках областной профилактической акции \"Счастливая семья-счастливое детство\" специалистами учреждения\n\"Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля\" для семей, находящихся в трудной жизненной ситуации проведено тренинговое занятие \"Моя семья- самая, самая.\", направленное на формирование нравственных ценностей в семье, на развитие отношения партнерства, понимания и сотрудничества в семье.', '', NULL, '2026-04-18', NULL, 'usernews', 'Telegram Import'),
(310, 'Мероприятие', 'Забота и уважение', 'tg-8616-zabota-i-uvazhenie', '👵ЗАБОТА И УВАЖЕНИЕ👴🏻\nВ период длительных выходных в рамках дополнительных мер профилактического воздействия специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\"  совместно с сотрудниками ОВД Железнодорожного района проведена выездная  акция для пожилых граждан\n\"группы риска\", находящихся в наиболее уязвимых социальных обстоятельствах.\n📌Проведены профилактические беседы о недопущении противоправных действий в сфере семейно-бытовых отношений, по формированию здорового образа жизни', '', NULL, '2026-04-19', NULL, 'usernews', 'Telegram Import'),
(311, 'Новость', 'Безопасность в мессенджерах', 'tg-8578-bezopasnost-v-messendzherah', '📍Безопасность в мессенджерах: как не стать жертвой мошенников!\nСегодня мы общаемся, делимся фото и даже оплачиваем покупки прямо в мессенджерах.\n❗️Это удобно, но не всегда безопасно! Мошенники могут воспользоваться нашей доверчивостью и невнимательностью.\nОни умеют притворяться друзьями, сотрудниками банков или магазинов, чтобы выманить деньги и личные данные.\nУчитесь распознавать обман, защитите себя и своих близких!', '', NULL, '2026-04-13', NULL, 'usernews', 'Telegram Import'),
(312, 'Мероприятие', 'График работы медучреждений', 'tg-8583-grafik-raboty-meduchrezhdeniy', '⚡⚡⚡ График работы учреждений здравоохранения в выходные и праздничные дни в апреле ‒ мае.\nАмбулатории, поликлиники, а также поликлинические отделения больниц и РНПЦ, организации службы крови, санэпидорганизации, аптеки, организации медтехники и организации особого типа:\n▫️ 20 апреля — по графику работы в субботний день,\n▫️ 21 апреля и 1 мая — по графику работы в праздничный день,\n▫️ 25 апреля — по графику работы в рабочий день,\n▫️ 9 мая — по графику работы в государственный праздник.\nБольницы и РНПЦ:\n▪️ 20 апреля — по графику работы в рабочий день,\n▪️ 21 апреля и 1 мая — по графику работы в праздничный день,\n▪️ 25 апреля — по графику работы в субботний день,\n▪️ 9 мая — по графику работы в государственный праздник.\nВ праздничные и выходные дни сохранится непрерывность оказания медицинской помощи. Служба скорой и неотложной медицинской помощи перейдет на режим повышенной готовности.', '', NULL, '2026-04-14', NULL, 'usernews', 'Telegram Import'),
(313, 'Новость', 'Молодежная ярмарка вакансий', 'tg-8584-molodezhnaya-yarmarka-vakansiy', '✅Сотрудники учреждения ТЦСОН принимали участие в масштабной молодежной ярмарке вакансий.\nШкольники и студенты узнали о возможности вторичной занятости во время каникул, деятельности Центра, его отделений.', '', NULL, '2026-04-14', NULL, 'usernews', 'Telegram Import'),
(314, 'Новость', 'Беседа о мошенничестве для соцработников', 'tg-8588-beseda-o-moshennichestve-dlya-soczrabotnikov', '⚡️С социальными работниками ТЦСОН проведена профилактическая беседа \"Осторожно мошенники\".\n⚡️Сотрудниками ОВД переданы памятки для дальнейшего информирования о способах защиты от мошеннических схем обслуживаемых пожилых граждан и инвалидов, проживающих в районе.', '', NULL, '2026-04-15', NULL, 'usernews', 'Telegram Import'),
(315, 'Новость', 'Проект «Семья без опасности»', 'tg-8589-proekt-semya-bez-opasnosti', 'Специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" в рамках областной профилактической акции \"Счастливая семья- счастливое детство\" дан старт пилотному проекту🙅‍♀️ \"СЕМЬЯ БЕЗ ОПАСНОСТИ\"🙅‍♀️.\nСпециалистами Центра в вечернее время, в предпраздничные и праздничные дни обследуют семьи, чьи дети признаны находящимися в социально опасном положении и семьи из \"группы риска\" Данный проект направлен на:\n📌поддержку безопасного и гармоничного семейного пространства', '', NULL, '2026-04-15', NULL, 'usernews', 'Telegram Import'),
(316, 'Новость', 'Меры безопасности на субботнике', 'tg-8593-mery-bezopasnosti-na-subbotnike', '🧹🍃 Рекомендации «О мерах безопасности при проведении республиканского субботника 18 апреля 2026 года»\n1️⃣ В случае, если во время субботника работник будет трудиться на своем рабочем месте, обеспечение здоровых и безопасных условий труда должно осуществляться в обычном режиме.\n2️⃣ Если работник во время проведения субботника трудится не на своем рабочем месте (привлекается к выполнению разовых работ, не связанных с прямыми обязанностями по профессии рабочего либо должности служащего) должны соблюдаться ➡️ следующие требования.\n❗️Обязательным условием по допуску работников к работе не на своем рабочем месте является проведение с ними целевого инструктажа по охране труда по выполняемому виду работ.', '', NULL, '2026-04-15', NULL, 'usernews', 'Telegram Import'),
(317, 'Новость', 'Изменения в административной ответственности', 'tg-8594-izmeneniya-v-administrativnoy-otvetstvennosti', '📣 Александр Лукашенко подписал Закон «Об изменении кодексов по вопросам административной ответственности».\nЭто первая масштабная корректировка данных кодексов с момента их принятия в 2021 году.\n📑 Предусматриваются меры административного воздействия за ряд новых правонарушений: незаконные операции с цифровыми знаками (токенами), нарушение требований к охвату территорий услугами сотовой связи и их качеству, несоблюдение порядка приобретения служебных легковых автомобилей и т.д.\n📑 Отдельный блок посвящен защите традиционных ценностей. Вводится ответственность за пропаганду гомосексуальных отношений, смены пола, педофилии и бездетности в виде штрафа в размере до 900 рублей, а при совершении в отношении несовершеннолетнего - до 1350 рублей или административного ареста.\n📑 Закон также усиливает профилактику правонарушений среди подростков. Прописана отдельная глава, регулирующая порядок применения мер воспитательного воздействия в отношении несовершеннолетних (исполнители, процедура, сроки).\n📑 Расширены основания для освобождения от ответственности. Например, при добровольном возмещении вреда, причиненного окружающей среде, или исправлении ошибок в статистических документах.\n📑 Сделаны дополнительные шаги в сторону дебюрократизации административного процесса. Теперь жалобу на действие должностных лиц, ведущих процесс, можно будет подать в электронном виде. Некоторые дела можно будет рассматривать без личного присутствия гражданина, когда он письменно ходатайствует об этом.\n📑 Еще около 150 статей кодексов корректируются в целях приведения в соответствие с отраслевым законодательством и совершенствования с учетом практики применения на основе более 500 поступивших предложений, связанных с уточнением подведомственности дел, прав и обязанностей участников процесса, составов правонарушений.', '', NULL, '2026-04-16', NULL, 'usernews', 'Telegram Import'),
(318, 'Новость', 'Рабочий визит в Кличевский район', 'tg-8595-rabochiy-vizit-v-klichevskiy-rayon', '💥 Сегодня Министр посещает с рабочим визитом Кличевский район\nАндрей Лобович начал поездку по Могилёвской области с посещения районного центра социального обслуживания населения.\n🔎 Вместе с председателем комитета по труду, занятости и соцзащите облисполкома Марией Шапневской глава ведомства встретился с коллективом и оценил работу учреждения.\n☢️ Главная тема разговора — \"Чернобыль: от возрождения до устойчивого развития\", вынесенная на апрельский единый день информирования.\nПомимо этого, Министр говорил и о сегодняшнем дне: государство планомерно укрепляет социальные гарантии, делает помощь доступнее и качественнее.\n➖➖➖➖\n🚗 Маршрут продолжается. Впереди:\n📍Отделение круглосуточного пребывания для граждан пожилого возраста и инвалидов в агрогородке Хвойница Кировского района\n📍Предприятие «Универсал Бобруйск» Белорусского общества глухих\n📍Бобруйский социальный пансионат «Каменка» и Дом сопровождаемого проживания', '', NULL, '2026-04-16', NULL, 'usernews', 'Telegram Import'),
(319, 'Новость', 'Мошенники отправили пенсионерку за границу', 'tg-8600-moshenniki-otpravili-pensionerku-za-graniczu', '😱 Пенсионерка из Жлобина по заданию мошенников дважды летала за границу, чтобы передать деньги обманутых минчан', '', '531a5358197714a9f0590b6a9f55d4ec.mp4', '2026-04-16', NULL, 'usernews', 'Telegram Import'),
(320, 'Новость', 'Жительница Речицы перевела мошенникам 40 000 рублей', 'tg-8601-zhitelnicza-rechiczy-perevela-moshennikam-40-000-rubley', '❗️Жительница Речицы перевела мошенникам 40 000 после звонков в Telegram\n👉Неустановленное лицо в ходе общения в мессенджере Telegram с гражданкой, представившись первоначально сотрудником КГБ, затем представителем Национального банка, под предлогом не распространения её персональных данных, а также с целью аннулирования заявок на кредиты, которые якобы на её имя были оформлены мошенниками, ввело последнюю в заблуждение и убедило оформить кредиты в банках.\n❌Оформив кредиты в трех банках, доложив личные денежные средства и средства, занятые у знакомых, жительница Речицы перевела их в сумме 40 000 белорусских рублей на предоставленные мошенниками счета.\n⚡️⚡️Указанная схема совершения хищений денежных средств граждан является распространённой и, несмотря на проводимую информационно-профилактическую работу, не теряет своей актуальности: граждане продолжают становиться жертвами мошенников.\n‼️Если вам поступили звонки по мобильной связи от незнакомых номеров, например, с предложением перезаключить договор на обслуживание и замену счетчиков, на получение дополнительных чипов для открытия двери, перезаключение в онлайн режиме договоров на любые услуги, в том числе и услуги мобильной связи, незамедлительно прекратите разговор. Если есть сомнения по поводу озвученной вам информации, свяжитесь с инстанциями по официальным телефонам, указанным на их сайтах или предоставленным в справочной службе.\n❌Ни в коем случае по просьбе незнакомых лиц не устанавливайте на смартфон никаких приложений, не переходите по ссылкам, не предоставляйте свои личные паспортные данные и любые коды, пришедшие на телефон.\n👮‍♂️Запомните, один из характерных признаков, указывающих на то, что с вами разговаривает мошенник, – он озвучит вам запрет на разглашение содержания разговора третьим лицам под угрозой привлечения к уголовной ответственности.\nНе поддавайтесь на провокации и берегите свои финансы от преступных посягательств!!!\n📰\"Днепровец\"', '', NULL, '2026-04-16', NULL, 'usernews', 'Telegram Import'),
(321, 'Новость', 'Чистый четверг', 'tg-8602-chistyy-chetverg', '🍃«Чистый четверг» 🍃\n💥Сотрудники ТЦСОН навели порядок на территории: собрали ветки и мусор. Теперь чисто, светло и уютно! 🌿🍂', '', NULL, '2026-04-16', NULL, 'usernews', 'Telegram Import'),
(322, 'Новость', 'Чернобыль: от возрождения до устойчивого развития', 'tg-8606-chernobyl-ot-vozrozhdeniya-do-ustoychivogo-razvitiya', '✅Единый день информирования в ТЦСОН на тему: \"ЧЕРНОБЫЛЬ: ОТ ВОЗРОЖДЕНИЯ ДО УСТОЙЧИВОГО РАЗВИТИЯ\"\nДополнительно рассмотрен вопрос \"Об административной ответственности за несоблюдение санитарно-эпидемиологических требований к содержанию и эксплуатации территорий, а также за нанесение несанкционированных надписей\".', '', NULL, '2026-04-16', NULL, 'usernews', 'Telegram Import'),
(323, 'Новость', 'Каждая пятница - родное, своё', 'tg-8608-kazhdaya-pyatnicza-rodnoe-svoe', 'Каждая пятница - родное, своё', '', 'a2ce3eaacc6f9817e20a82376be8d3f6.mp4', '2026-04-17', NULL, 'usernews', 'Telegram Import'),
(324, 'Новость', 'ТЦСОН на республиканском субботнике', 'tg-8609-tczson-na-respublikanskom-subbotnike', '🌱ТЦСОН на республиканском субботнике.\r\n🇧🇾Сделаем нашу страну и наш город еще чище и красивее вместе!', '', 'aef8defdd2081ec7497b265acc1aca5a.mp4', '2026-04-18', NULL, 'usernews', 'Telegram Import'),
(325, 'Новость', 'Безопасность детей у открытых окон', 'tg-8622-bezopasnost-detey-u-otkrytyh-okon', '☀️С приходом весны многие открывают окна, чтобы проветрить помещения и впустить в дом свежий воздух. Однако если в семье есть маленькие дети, это требует особой осторожности: открытое окно может представлять серьёзную опасность‼️\n🔒 Установите блокираторы или фиксаторы на окна — они не позволят окну открыться шире, чем на несколько сантиметров.\n💡Если конструкция окна позволяет, используйте режим проветривания (верхнее проветривание).\n🛋️ Не оставляйте мебель рядом с окнами — столы, стулья, диваны могут стать ступенькой для малыша, чтобы забраться на подоконник.\n👀 Никогда не оставляйте маленьких детей одних в комнате с открытым окном — даже на минуту.\n🦟 Не полагайтесь на москитные сетки — они не защищают от падений! Ребёнок может опереться на неё и выпасть вместе с сеткой.\n📚 Объясните детям правила безопасности.\n🔧 Проверьте надёжность оконной фурнитуры — убедитесь, что ручки, петли и замки исправны.\nВсего несколько простых шагов помогут сделать ваш дом безопасным для ребёнка!', '', NULL, '2026-04-21', NULL, 'usernews', 'Telegram Import'),
(326, 'Мероприятие', 'Заседание совета по помощи пострадавшим', 'tg-8625-zasedanie-soveta-po-pomoschi-postradavshim', 'Сегодня состоялось выездное внеочередное заседание межведомственного совета по оказанию помощи пострадавшим от домашнего насилия Железнодорожного района г.Гомеля в отношении граждан, находящихся в трудной жизненной ситуации.\nПредседателем межведомственного совета Куницкой Н.Г. совместно с субъектами профилактики определены основные направления работы, направленные на оказание помощи гражданам и предупреждение преступлений и правонарушений в сфере семейных отношений и гибели людей от внешних причин.', '', NULL, '2026-04-23', NULL, 'usernews', 'Telegram Import'),
(327, 'Новость', 'С 1 мая повышается БПМ', 'tg-8631-s-1-maya-povyshaetsya-bpm', '⚡️⚡️⚡️С 1 мая повышаются размеры бюджета прожиточного минимума\nНовые размеры БПМ будут действовать по 31 июля 2026 г.\n👛БПМ в среднем на душу населения по сравнению с его значением, установленным с 1 февраля 2026 г., увеличится на 2,5% и составит 509,62 рубля.\n📈 Это второй перерасчет БПМ в 2026 году.', '', NULL, '2026-04-24', NULL, 'usernews', 'Telegram Import'),
(328, 'Мероприятие', 'Чернобыль: 40 лет спустя', 'tg-8632-chernobyl-40-let-spustya', '💥Чернобыль: 40 лет спустя – от возрождения к устойчивому развитию\nВ ТЦСОН прошла тематическая лекция, приуроченная к 40-летию трагедии на Чернобыльской АЭС.\nНаши посетители вспомнили хронику тех дней, почтили память ликвидаторов и узнали, как зона отчуждения за четыре десятилетия превратилась из территории бедствия в уникальный полигон устойчивого развития.\nОсобое внимание уделили современным проектам: новому безопасному конфайнменту («Арке»), солнечной электростанции в Чернобыле и экологическому туризму.\n‼️Главный вывод: даже после тяжелейшей катастрофы человек может учиться безопасности, восстанавливать природу и использовать технологии во благо.☘️\n✨Благодарим всех участников за неравнодушие и память, которую мы обязаны сохранить.', '', NULL, '2026-04-24', NULL, 'usernews', 'Telegram Import'),
(329, 'Новость', 'Опасный тренд с оскорблением педагогов', 'tg-8633-opasnyy-trend-s-oskorbleniem-pedagogov', 'Вычислен инициатор тренда, в рамках которого школьники грубо нарушают законы\nВ соцсетях распространяется опасный тренд под песню из сериала «Очень странные дела». Подростки и дети берут фото учителей, накладывают музыку и сопровождают изображения прямыми оскорблениями.\nКто подстрекатель?\n⏺Инициатором является 53-летняя Наталия Мелешко, гражданка Российской Федерации, включённая в перечень лиц, причастных к террористической деятельности. Бежала в Европу в 2022 году. Никого не напоминает🤔?\n⏺Сейчас она находится в Израиле, расистка на всю голову, открыто финансирует ВСУ.\n⏺Неоднократно выкладывала в TikTok видео, в которых поливала грязью Беларусь, Россию, Палестину и др. При этом оды она поёт исключительно Израилю и Украине.\n⏺Запустила эту грязную \"акцию\": видео, в которых молодёжь высказывает агрессию и жестокость в сторону педагогов.\n❗️Напоминаем! Что может грозить за подобные \"акции\"\n1️⃣В отношении родителей подростков, опубликовавших оскорбительные ролики о педагогах, могут возбудить уголовное дело по статье 130 Уголовного кодекса Республики Беларусь «Разжигание расовой, национальной, религиозной либо иной социальной вражды или розни» (в части возбуждения вражды по признаку социальной принадлежности). Им может грозить ограничение свободы на срок до пяти лет или лишение свободы на срок от двух до двенадцати лет.\n2️⃣Если подросток, не достигший 18 лет, его родителей и законных представителей могут привлечь к административной ответственности по статье 10.3 Кодекса Республики Беларусь об административных правонарушениях «Невыполнение обязанностей по воспитанию детей». Санкция предусматривает наложение штрафа.\n3️⃣За оскорбление (статья 10.2 КоАП Республики Беларусь) предусмотрено наложение штрафа. Оскорбление, распространённое в глобальной компьютерной сети Интернет, влечёт наложение штрафа до двухсот базовых величин, или общественные работы, или административный арест.\n4️⃣Статья 188 Уголовного кодекса Республики Беларусь «Клевета» предусматривает ответственность за распространение заведомо ложных, порочащих другое лицо измышлений. Наказание — общественные работы, или штраф, или арест на срок до трех лет.\n✍️Всем надо осознать: это не игра. Участие в таких \"трендах\" может привести к серьёзным последствиям. Это надо понимать и школьникам, и родителям, и учителям.', '', NULL, '2026-04-24', NULL, 'usernews', 'Telegram Import'),
(330, 'Мероприятие', 'Семинар по постинтернатному сопровождению', 'tg-8634-seminar-po-postinternatnomu-soprovozhdeniyu', '🤝\"Взаимодействие по сопровождению и поддержке лиц из числа детей-сирот и детей, оставшихся без попечения родителей, работающих на предприятиях и организациях Железнодорожного района г.Гомеля\"🤝\nНа базе учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" специалистами Центра для представителей предприятий и организаций Железнодорожного района г.Гомеля проведен обучающий семинар по вопросам:\n✅постинтернатное сопровождение лиц из числа детей-сирот по первому рабочему месту, оказание поддержки по принципу \"равный обучает равного\"', '', NULL, '2026-04-25', NULL, 'usernews', 'Telegram Import');
INSERT INTO `news` (`id`, `type`, `title`, `slug`, `description`, `freim`, `video_filename`, `date`, `created_by_user_id`, `created_by_login`, `created_by_name`) VALUES
(331, 'Мероприятие', 'Проект PRO-СЕМЬЮ: экономическая грамотность', 'tg-8651-proekt-pro-semyu-ekonomicheskaya-gramotnost', '🌟\"PRO-СЕМЬЮ🌟\nСпециалисты учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" при поддержке Железнодорожного районного отделения г.Гомеля Белорусской партии \"Белая Русь\" продолжают 🤝 реализация пилотного проекта👩‍❤️‍👨 \"PRO-СЕМЬЮ\"👩🏻‍🍼 для лиц из числа детей - сирот и детей, оставшихся без попечения родителей, в возрасте от 22 до 23 лет, имеющих на иждивении несовершеннолетних детей. Сегодня проект проходил в два этапа:\n🌟На первом этапе ребята обучались экономической грамотности: в магазине закупали продукты питания на предоставленную им денежную сумму', '', NULL, '2026-04-25', NULL, 'usernews', 'Telegram Import'),
(332, 'Новость', 'Трудовая династия семьи Шиловых', 'tg-8658-trudovaya-dinastiya-semi-shilovyh', 'Один из ярких примеров трудовой династии - семья Шиловой Валентины. Мама и две дочери - работают в системе соцзащиты более 35 лет.\nШилова Валентина начала трудовую деятельность в соцсфере с 1991 года. С 1995 года - инспектор по основной деятельности ТЦСОН Железнодорожного района. За время работы отмечена различными наградами, проработав в системе 29 лет.\nСтаршая дочь - Данилова Оксана, с 2003 года специалист в управлении соцзащиты администрации Железнодорожного района г.Гомеля, с 2013 года - главный специалист отдела по назначению и выплате пенсий и пособий. За добросовестный труд отмечена Почетными грамотами.\nМладшая дочь - Алексеенко Екатерина, с 2019 года социальный работник ТЦСОН Железнодорожного района. За добросовестный труд в 2026 году отмечена Почетной грамотой Гомельского городского Совета депутатов.\nБлагодаря семьям, чьи представители десятилетиями остаются верны выбранной профессии, наше общество получает надежные кадры.', '', NULL, '2026-04-25', NULL, 'usernews', 'Telegram Import'),
(333, 'Новость', 'Всемирный день охраны труда', 'tg-8659-vsemirnyy-den-ohrany-truda', '🌏Во всем мире ежегодно 28 апреля отмечается Всемирный день охраны труда.\n🔎Тема Всемирного дня охраны труда в 2026 году: «Благоприятная психосоциальная рабочая среда: путь к процветанию работников и сильной организации».\nЭта информационно-\nразъяснительная кампания призвана привлечь внимание общественности к проблемам в области охраны труда и к росту числа травм, заболеваний и смертельных случаев, связанных с трудовой деятельностью. Во всех регионах мира правительства, профсоюзные организации, организации работодателей и специалисты-практики в области охраны труда организуют мероприятия к Всемирному дню охраны труда.', '', NULL, '2026-04-27', NULL, 'usernews', 'Telegram Import'),
(334, 'Новость', 'Киберпонедельник: что такое вишинг', 'tg-8660-kiberponedelnik-chto-takoe-vishing', '💥Доброе утро киберпонедельника.\n✅Один из самых распространенных способов обмана людей сейчас это вишинг. Он происходит ежедневно.\n✈️Что такое вишинг?\nВишинг - это вид мошенничества, который осуществляется через телефонные звонки или голосовые сообщения,чтобы заставить человека добровольно передать деньги,коды подтверждения или доступ к своим аккаунтам.\n🎲Современные схемы вишинга не ограничиваются одним звонком.\n📞Телефонный разговор это первый этап, после которого жертву переводят в мессенджер, направляют на поддельный сайт или убеждают выполнить определенные действия в банковских сервисах.\n🥸Аферисты, действующие через вишинг, выдают себя за сотрудников государственных и силовых структур, банков, обслуживающих компаний и убеждают потенциальных жертв передать деньги и данные самостоятельно.\n🔑Использование социальной инженерии помогает мошенникам создавать адаптированные под новостную повестку убедительные сценарии для обмана людей разных возрастов, профессий и уровня образования.\n⭐Чтобы не стать жертвой вишинга, необходимо:\n〰️не доверять звонкам с незнакомых номеров', '', NULL, '2026-04-27', NULL, 'usernews', 'Telegram Import'),
(335, 'Новость', 'Новые размеры социальной помощи', 'tg-8661-novye-razmery-soczialnoy-pomoschi', '🪙Новые размеры c 1 мая — государственная адресная социальная помощь\nТакая помощь включает четыре соцвыплаты\n➡️Ежемесячное социальное пособие для семей\n▪️Предоставляется при условии, если среднедушевой доход по объективным причинам ниже критерия нуждаемости — 509,62 руб. (с 1 мая).\n▪️Многодетным семьям такое пособие назначается, когда среднедушевой доход не более 1,15 величины критерия нуждаемости — 586,1 руб. (с 1 мая).\n➡️Единовременное социальное пособие\n▪️Выплачивается, когда человек оказался в трудной жизненной ситуации, при условии, что среднедушевой доход не более 1,5 величины критерия нуждаемости — 764,43 руб. (с 1 мая).\n➡️Социальное пособие для возмещения затрат на приобретение предметов гигиены\n▪️Предоставляется независимо от величины дохода семьи детям-инвалидам, имеющим IV степень утраты здоровья, и инвалидам I группы.\nна основании индивидуальной программы реабилитации или заключении врачебно-консультационной комиссии о нуждаемости в предметах гигиены при наличии документов, подтверждающих расходы на их приобретение.\n➡️Обеспечение продуктами питания детей первых двух лет жизни\n▪️Предусмотрено для семей, где доход ниже критерия нуждаемости, а также при рождении двойни и более детей — независимо от уровня дохода.\n💡Справочно о средних размерах за 1 квартал 2026 г.:\n✅размер ежемесячного социального пособия составил 147,11 рубля,\n✅единовременного пособия — 309,74 рубля,\n✅пособия на приобретение предметов гигиены — 685,27 рубля,\n✅обеспечение питанием детей до 2 лет — 93,67 рубля.', '', NULL, '2026-04-27', NULL, 'usernews', 'Telegram Import'),
(336, 'Мероприятие', 'Пасхальный визит в пункт временного проживания', 'tg-8662-pashalnyy-vizit-v-punkt-vremennogo-prozhivaniya', '✅С пасхальным приветствием пункт временного проживания граждан Республики Беларусь, иностранных граждан, лиц без гражданства, находящихся в трудной жизненной ситуации ТЦСОН посетил о.Владимир,\nвозглавляющий отдел социального служения и церковной благотворительности Гомельской Епархии с напутствием важности единства и взаимопонимания между людьми.', '', NULL, '2026-04-27', NULL, 'usernews', 'Telegram Import'),
(337, 'Мероприятие', 'Юбилей хора «Виктория»', 'tg-8665-yubiley-hora-viktoriya', 'Во Дворце культуры ОАО «Гомсельмаш» многолюдно.\n🌸Торжественное мероприятие посвященное 22-летию хорового коллектива «Виктория» ТЦСОН, совпало с 70-летним юбилеем его бессменного руководителя Школяр Тамары Михайловны, чья любовь к музыке и неустанная работа на волонтерских началах вдохновляют всех участников хора на новые творческие свершения.\nСо словами благодарности к юбиляру обратилась заместитель директора Снежкова Е.П., подчеркнув её неоценимый вклад в культурную жизнь Центра.\nПраздник украсили выступления гостей, среди которых были ансамбль «Крынiчанька», сестры Шершневы из Городского центра культуры, семья Стаховец с авторской песней и танцевальные коллективы.\nПриглашаем всех желающих проявить себя и открыть в себе новые таланты!', '', NULL, '2026-04-27', NULL, 'usernews', 'Telegram Import'),
(338, 'Мероприятие', 'Профилактическое мероприятие «Дом без пожара»', 'tg-8668-profilakticheskoe-meropriyatie-dom-bez-pozhara', '«Дом без пожара»: в Гомеле пройдёт специальное профилактическое мероприятие\nДостучаться до каждого и объяснить важность соблюдения правил пожарной безопасности – основополагающие задачи планирующегося мероприятия.', '', NULL, '2026-04-27', NULL, 'usernews', 'Telegram Import'),
(339, 'Новость', 'Схема мошенничества «обратный перевод»', 'tg-8669-shema-moshennichestva-obratnyy-perevod', '🌐Мошенники начали использовать схему «обратного перевода» денег.\nАферисты начали применять новую схему обмана. Они переводят деньги на карты жертв, а потом через мессенджеры просят вернуть сумму.\nЭксперты предупреждают: ни в коем случае не возвращайте деньги самостоятельно — сразу обращайтесь в банк.', '', NULL, '2026-04-27', NULL, 'usernews', 'Telegram Import'),
(340, 'Новость', '28 апреля - День охраны труда', 'tg-8670-28-aprelya-den-ohrany-truda', '🛠 28 апреля — Всемирный день охраны труда\nЕжегодно МОТ 28 апреля отмечает Всемирный день охраны труда. Традиционно,к этому дню в Беларуси проходит целый ряд мероприятий, посвященных вопросам охраны труда.\n‼️Мы вновь и вновь напоминаем о том, что обеспечение прав и гарантий работников в области охраны труда, создание условий для достойной трудовой деятельности, приносящей удовлетворение гражданину и пользу обществу – это один из важнейших приоритетов государственной политики.\n📱В 2026 году центральной темой стала благоприятная психосоциальная рабочая среда: путь к процветанию работников и сильной организации.\n➡️Информационные материалы.\n➡️Пресс-конференция \"Охрана труда работников – приоритет государства и общества\".\n➖➖➖➖➖\n🔽Также читайте:\n▪️Производственный травматизм в Беларуси за 20 лет снизился в три раза.\n▪️В Беларуси планируют внести изменения в закон \"Об охране труда\".\n▪️В 2025 году в трех регионах Беларуси достигли нулевого травматизма.', '', NULL, '2026-04-28', NULL, 'usernews', 'Telegram Import'),
(341, 'Новость', 'Перенос рабочего дня в подразделениях УВД', 'tg-8671-perenos-rabochego-dnya-v-podrazdeleniyah-uvd', 'УВД облисполкома сообщает, что в подразделениях органов внутренних дел, осуществляющих прием граждан по вопросам гражданства и миграции, оборота оружия, регистрации транспортных средств и выдачи водительских удостоверений рабочий день с субботы 2 мая перенесен на понедельник 4 мая.\nИзменение графика связано с Праздником труда.\nПравоохранители просят граждан учитывать данную информацию и планировать посещение подразделений заранее.', '', NULL, '2026-04-28', NULL, 'usernews', 'Telegram Import'),
(342, 'Новость', 'Сервис «Проверь.Бел» для проверки аккаунтов', 'tg-8672-servis-prover-bel-dlya-proverki-akkauntov', '🛡Запущен «Проверь.Бел» – сервис для проверки аккаунтов на мошенничество\nВ нашей стране дан старт работе уникального сайта Проверь.бел, содержащего в себе сведения об аккаунтах в социальных сетях и мессенджерах, которые являются мошенническими. Теперь любой пользователь может быстро это проверить. Для этого достаточно ввести имя аккаунта или ссылку на него в специальную форму на сайте.\n🔵Сервис позволяет:\n➖Проверить интернет-ресурсы на признаки мошенничества.\n➖Выявить фейковые аккаунты в популярных социальных сетях и мессенджерах.\n➖Получить информацию о том, попадал ли данный ресурс в поле зрения правоохранительных органов.\n➖Передать информацию об интернет-ресурсе, который может являться мошенническим, но данные о нем отсутствуют.\n📲Как воспользоваться сервисом:\n1️⃣Перейдите на сайт проверь.бел (или praver.by)', '', NULL, '2026-04-28', NULL, 'usernews', 'Telegram Import'),
(343, 'Новость', 'Мошенники создали сайт-клон Нацбанка', 'tg-8673-moshenniki-sozdali-sayt-klon-naczbanka', 'Мошенники создали сайт — клон Национального банка.\nРесурс копирует внешний вид и адрес официального портала, однако содержит недействительные контакты и опасные ссылки.\nГраждан призывают проверять адрес сайта и не переходить по подозрительным ссылкам из сообщений и писем.', '', NULL, '2026-04-28', NULL, 'usernews', 'Telegram Import'),
(344, 'Мероприятие', 'Хор «Виктория» отметил двойной юбилей', 'tg-8674-hor-viktoriya-otmetil-dvoynoy-yubiley', 'В Гомеле хор «Виктория» отметил двойной юбилей в ДК «Гомсельмаш»\n«С песней по жизни» – под таким названием прошёл концерт хора «Виктория», который существует при ТЦСОН Железнодорожного района Гомеля, в Доме культуры «Гомсельмаш». В зале царила атмосфера творчества и тепла, было сказано много ярких и душевных поздравлений.', '', NULL, '2026-04-28', NULL, 'usernews', 'Telegram Import'),
(345, 'Новость', 'Победа в конкурсе «Пасхальная радость»', 'tg-8675-pobeda-v-konkurse-pashalnaya-radost', '✅Отделение социальной реабилитации,абилитации инвалидов ТЦСОН Железнодорожного района г.Гомеля\n💐Екатерина Белодедова награждена дипломом 1 степени🥇 за участие в дистанционном творческом конкурсе «Пасхальная радость» в номинации «Пасхальный сувенир». 🎁\nУчастие в творческих конкурсах для людей с инвалидностью — это мощный инструмент социальной реабилитации, позволяющий выйти за рамки привычного образа жизни, поверить в себя и продемонстрировать свои таланты', '', NULL, '2026-04-28', NULL, 'usernews', 'Telegram Import'),
(346, 'Новость', 'Проект «Семья без опасности» продолжается', 'tg-8678-proekt-semya-bez-opasnosti-prodolzhaetsya', 'Специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" в рамках областной профилактической акции \"Счастливая семья- счастливое детство\" продолжается реализация пилотного проекта🙅‍♀️ \"СЕМЬЯ БЕЗ ОПАСНОСТИ\"🙅‍♀️.\nСпециалисты Центра в вечернее время обследуют семьи, чьи дети признаны находящимися в социально опасном положении и семьи из \"группы риска\" Данный проект направлен на:\n📌поддержку безопасного и гармоничного семейного пространства', '', NULL, '2026-04-29', NULL, 'usernews', 'Telegram Import'),
(347, 'Мероприятие', 'Открытие фонтанов в Гомеле', 'tg-8681-otkrytie-fontanov-v-gomele', '🔥🔥🔥1 мая в Гомеле состоится торжественное открытие фонтанов!\n💥1 мая в 14. 00 будут включены фонтаны в разных районах города.\nЯрким украшением Центрального района станут фонтаны у здания Цирка, в сквере имени Громыко и в сквере имени П.О.Сухого. В Новобелицком районе - возле кинотеатра «Мир», в Железнодорожном — напротив Областной библиотеки. Советский район порадует гостей фонтаном по проспекту Речицкому, 45.\nДля жителей и гостей города подготовлена насыщенная развлекательная программа.\nПриходите всей семьёй, чтобы вместе насладиться этим замечательным событием!', '', NULL, '2026-04-30', NULL, 'usernews', 'Telegram Import'),
(348, 'Новость', 'Места установки датчиков скорости', 'tg-8688-mesta-ustanovki-datchikov-skorosti', '📸 Места установки датчиков контроля скорости и мобильного комплекса «ОРАКУЛ-ИНСАЙТ» 30 апреля в Гомеле\nна 5 км а/д Р-150 «Хутор – Гомель». Скоростное ограничение на данном участке для всех водителей 60 км/ч', '', NULL, '2026-04-30', NULL, 'usernews', 'Telegram Import'),
(349, 'Новость', 'С 1 мая повышается БПМ', 'tg-8689-s-1-maya-povyshaetsya-bpm', '⚡️⚡️⚡️С 1 мая повышаются размеры бюджета прожиточного минимума\nНовые размеры БПМ будут действовать по 31 июля 2026 г.\n👛БПМ в среднем на душу населения по сравнению с его значением, установленным с 1 февраля 2026 г., увеличится на 2,5% и составит 509,62 рубля.\n📈 Это второй перерасчет БПМ в 2026 году.', '', NULL, '2026-04-30', NULL, 'usernews', 'Telegram Import'),
(350, 'Новость', 'Мошенники представляются сотрудниками поликлиник', 'tg-8690-moshenniki-predstavlyayutsya-sotrudnikami-poliklinik', '🚨🚨🚨Внимание! Активизировалась схема мошенничества!\n☎️ Граждане начали получать звонки от мошенников, в том числе с белорусских номеров. Они представляются сотрудниками поликлиник или медцентров и предлагают оформить электронную медицинскую карту, пытаясь выманить ваши персональные данные.\nСпойлер: \"легенда\" может иметь особенности.\n⁉️Как это работает?\n➡️Первый этап: Вам звонит «сотрудник поликлиники», который утверждает, что нужно заменить бумажную медкарту на электронную. Для этого он просит предоставить личные данные: ФИО, номер паспорта и код из SMS.\n➡️Второй этап: Спустя некоторое время вам снова звонят, но уже якобы из правоохранительных органов. Они сообщают, что предыдущий звонок был от мошенников, которые получили доступ к вашему банковскому счету. Далее следуют угрозы и требования «декларировать» наличные средства.\n⚡️ Что делать, если вам позвонили?\n📵Немедленно прервите разговор!\n🤳🏻Проверьте информацию, перезвонив по официальному номеру медучреждения.\n⛔️Не предоставляйте личные данные и пароли.\n👮🏼Если вы уже передали данные или деньги, срочно обращайтесь в милицию.\n🛑 Помните: настоящие сотрудники государственных учреждений никогда не требуют передачи личных данных или срочных действий с деньгами по телефону. Будьте внимательны и берегите свои данные!', '', NULL, '2026-04-30', NULL, 'usernews', 'Telegram Import'),
(351, 'Новость', 'Поздравление с Праздником труда от Президента', 'tg-8692-pozdravlenie-s-prazdnikom-truda-ot-prezidenta', '📣 Первый поздравил соотечественников с Праздником труда.\n✍🏻 «В этот майский день мы торжественно чествуем всех тружеников, которые вкладывают свои знания, профессионализм и талант в укрепление экономики страны, формируя мощный фундамент для счастливого будущего белорусов.\nНаучный и социальный прогресс, стремительное развитие технологий открывают перед миром широкие возможности. Но за каждым достижением стоят конкретные люди, и никакие инновации не способны заменить их руки, опыт и самоотдачу».\nМы сохраним первомайские традиции, заложенные предыдущими поколениями, и передадим их нашей молодежи - честной, трудолюбивой, целеустремленной. Это залог дальнейшего развития и процветания Беларуси.\n🤝 Желаю всем здоровья, мира и добра. Пусть каждый день будет наполнен радостью новых трудовых свершений.', '', NULL, '2026-05-01', NULL, 'usernews', 'Telegram Import'),
(352, 'Новость', 'Поздравление Министра труда с Праздником труда', 'tg-8693-pozdravlenie-ministra-truda-s-prazdnikom-truda', '🕊Поздравление Министра труда и социальной защиты Андрея Лобовича с Праздником труда\nНаша страна всегда опиралась на трудолюбие, талант и профессионализм граждан. Именно благодаря вашему мастерству, ответственности и энергии мы успешно решаем самые амбициозные задачи, сохраняем стабильность в обществе и строим основу для нашего уверенного будущего.\nВ Министерстве труда и социальной защиты первостепенной задачей остаётся создание комфортных и безопасных условий для работы, обеспечение достойной заработной платы и эффективной социальной поддержки.\nКаждый человек труда в нашей стране должен чувствовать защищённость и уверенность в завтрашнем дне, видеть, что его вклад в общее развитие является по-настоящему значимым и ценным.\n🇧🇾 Желаю всем крепкого здоровья, оптимизма, неиссякаемой энергии и новых профессиональных успехов во имя процветания нашей любимой Родины. Пусть мир, добро и взаимное уважение всегда царят в ваших домах и сердцах!\n➖➖➖➖➖➖➖\nС уважением,\nМинистр труда и социальной защиты\nАндрей Лобович', '', NULL, '2026-05-01', NULL, 'usernews', 'Telegram Import'),
(353, 'Новость', 'Поздравление главы администрации с Праздником труда', 'tg-8694-pozdravlenie-glavy-administraczii-s-prazdnikom-truda', '1 мая на протяжении многих лет является одним из любимых и, действительно, всенародных праздников для всех поколений. Это праздник всех, кто ежедневным трудом создает настоящее и строит будущее.\nПервомай – это, прежде всего, праздник, призванный объединять! Он объединяет всех тех, кто стремится достойно трудиться, жить в мире и согласии, сохранять и защищать лучшие традиции.\nНаша страна всегда славилась трудолюбивыми и талантливыми людьми, благодаря которым нам многого удалось достичь. И нам по праву есть чем гордиться: сегодня Республика Беларусь – это независимая, свободная и мирная страна, высшей ценностью которой является человек, его права, свободы и гарантии их реализации.\nВ этот день выражаю слова благодарности ветеранам труда и передовикам производства за многолетнюю и плодотворную работу.\nЖелаю праздничного весеннего настроения, крепкого здоровья, оптимизма, вдохновения и бодрости духа, дальнейших успехов и достижений в труде!\nВ.М.Морозов, глава администрации Железнодорожного района г.Гомеля', '', NULL, '2026-05-01', NULL, 'usernews', 'Telegram Import'),
(354, 'Новость', 'Поздравление с Праздником труда от Надежды Цупы', 'tg-8695-pozdravlenie-s-prazdnikom-truda-ot-nadezhdy-czupy', '🌸 Уважаемые коллеги, поздравляю вас с Праздником труда!\nЭтот день — дань уважения каждому, кто своим трудом создает основу благополучия общества.\nТруд человека всегда был основой единства, гордости и стабильности нашей страны. Каждый из нас является частью большой команды, от усилий которой зависит процветание родных мест. Сегодня мы чествуем ваш профессионализм, мастерство и преданность делу, выражаем благодарность тем, кто ежедневно трудится на благо нашей Республики Беларусь.\nЖелаю крепкого здоровья, уверенности в завтрашнем дне, семейного тепла и новых профессиональных достижений.\n🕊С Праздником!\n➖➖➖➖\nС уважением,\nпредседатель комитета по труду, занятости и социальной защите\nНадежда Цупа', '', NULL, '2026-05-01', NULL, 'usernews', 'Telegram Import'),
(355, 'Мероприятие', 'ТЦСОН на открытии фонтана', 'tg-8696-tczson-na-otkrytii-fontana', '🌸Сотрудники и посетители ТЦСОН на открытии фонтана!', '', NULL, '2026-05-01', NULL, 'usernews', 'Telegram Import'),
(356, 'Мероприятие', 'Защита от иерсиниоза', 'tg-8699-zaschita-ot-iersinioza', '🛡🛡🛡Защита от иерсиниоза: встреча с врачом-эпидемиологом\n👍👍👍В ТЦСОН Железнодорожного района г. Гомеля состоялась профилактическая беседа с врачом-эпидемиологом Гомельского городского ЦГЭ Марией Слуцкой.\n⚡⚡⚡Главная тема встречи - профилактика иерсиниозных инфекций!\n✅ Источник опасности: овощи и фрукты, хранившиеся в подвалах или холодильниках, если они не прошли тщательную обработку.\n✅ Как защититься: мыть руки, обдавать кипятком сырые овощи перед употреблением в салаты.\n✅ Правила хранения: следить за чистотой полок в холодильнике и не допускать появления грызунов в местах хранения продуктов.\nВстреча прошла в теплой атмосфере, посетители отделения социальной реабилитации и абилитации получили полезные рекомендации, которые помогут сохранить здоровье в весенний период.', '', NULL, '2026-05-02', NULL, 'usernews', 'Telegram Import'),
(357, 'Мероприятие', 'Лекция о питании в пожилом возрасте', 'tg-8702-lekcziya-o-pitanii-v-pozhilom-vozraste', '🍏 Старение, болезни и… тарелка здоровой еды.\n💥В отделении дневного пребывания ТЦСОН прошла лекция врача на тему: «Старение и болезнь. Принципы здорового питания в пожилом возрасте».\n‼️Обсудили важные вещи:\n✔️ почему после 60 лет меняются потребности в еде\n✔️ какие продукты помогают сохранить бодрость, а какие ускоряют болезни\n✔️ простые правила, чтобы питаться вкусно, полезно и безопасно\n🖥 Часть слушателей подключились дистанционно.\nСпасибо современным технологиям, что забота о здоровье становится доступнее.\nБлагодарим врача и всех участников за внимание! Будьте здоровы в любом возрасте ❤️', '', NULL, '2026-05-02', NULL, 'usernews', 'Telegram Import'),
(358, 'Мероприятие', 'Тренинг «Диагностическая азбука личности»', 'tg-8703-trening-diagnosticheskaya-azbuka-lichnosti', '💥В отделении дневного пребывания ТЦСОН был проведён психологический тренинг «Диагностическая азбука личности».\n✨ Участницы встречи с помощью цветных карандашей и бумаги смогли лучше понять свой внутренний мир.\n🌞Атмосфера царила тёплая и доверительная.\nКаждая участница смогла получить краткую обратную связь о своём рисунке.\nЗанятие помогло снять напряжение и подарило новые поводы для саморефлексии.🫶\nТакие мероприятия станут доброй традицией, так как  они поддерживают психологическое здоровье, развивают творческие способности и дарят жителям «серебряного» возраста радость общения❤️', '', NULL, '2026-05-02', NULL, 'usernews', 'Telegram Import'),
(359, 'Новость', 'Не дайте мошенникам себя обмануть', 'tg-8704-ne-dayte-moshennikam-sebya-obmanut', 'Не дайте мошенникам себя обмануть!\nЗа минувшие сутки в милицию поступило около 350 сообщений о звонках телефонных мошенников. Благодаря оперативности сотрудников органов внутренних дел и бдительности граждан подавляющее большинство злодеяний предотвращено! Зарегистрировано 37 преступлений, совершенных с использованием информационно-коммуникационных технологий', '', 'f27a2caa861915f03eb2212ba8567545.mp4', '2026-05-02', NULL, 'usernews', 'Telegram Import'),
(360, 'Мероприятие', 'Акция «Территория заботы»', 'tg-8705-akcziya-territoriya-zaboty', '👵 ТЕРРИТОРИЯ ЗАБОТЫ 👴🏻\nВ период длительных выходных для дополнительной профилактики специалистами ТЦСОН совместно с сотрудниками ОВД Железнодорожного района проведена акция для пожилых граждан \"группы риска\".\n📌проведение профилактических бесед в рамках специального профилактического мероприятия🏠 \"Дом без пожара\"', '', NULL, '2026-05-02', NULL, 'usernews', 'Telegram Import'),
(361, 'Новость', 'Внимание, мошенники: блокировка Telegram', 'tg-8712-vnimanie-moshenniki-blokirovka-telegram', 'ВНИМАНИЕ, МОШЕННИКИ!\nБелорусы начали получать СМС-сообщения о том, что их аккаунт в Telegram будет заблокирован.\nК сообщению прикреплена мошенническая ссылка — ни в коем случае не переходите по ней!\nУважаемые читатели, не переходите по таким ссылкам, будьте бдительны и осторожны.\nПроверить подозрительную ссылку можно на сайте Проверь.бел.\nПодробности об этом виде мошенничества здесь.', '', NULL, '2026-05-03', NULL, 'usernews', 'Telegram Import'),
(362, 'Новость', 'Мошенники предлагают оформить медкарты', 'tg-8714-moshenniki-predlagayut-oformit-medkarty', 'Осторожно: мошенники «заводят» вам медкарты.\nВ Беларуси набирает обороты новая схема обмана. Мошенники мимикрируют под заботливых медиков, чтобы опустошить ваши счета.\nКак работает ловушка:\n1. «Звонок из поликлиники»: вам предлагают заменить старую бумажную карту на современную электронную. Для «оформления» просят ФИО, номер паспорта и — самое важное — код из SMS.\n2. «Звонок из милиции»: как только вы дали код, вам перезванивают якобы из МВД. Говорят, что прошлый звонок был от преступников, ваш счет взломан, и теперь нужно срочно «задекларировать» (отдать им) все наличные.\nЗапомните сами и передайте близким:\n❌ Сотрудники поликлиник не запрашивают коды из SMS и паспортные данные по телефону. Правоохранители никогда не требуют переводить деньги на «безопасные счета» или декларировать наличку по телефону.\nЧто делать?\n✅ Просто положите трубку.\n✅ Есть сомнения? Перезвоните в регистратуру своей поликлиники по официальному номеру.\n✅ Если данные уже утекли — немедленно звоните в 102.', '', NULL, '2026-05-03', NULL, 'usernews', 'Telegram Import'),
(363, 'Новость', 'Новые размеры адресной социальной помощи', 'tg-8715-novye-razmery-adresnoy-soczialnoy-pomoschi', '👛 Новые размеры c 1 мая - государственная адресная социальная помощь\n➡️Ежемесячное социальное пособие для семей\n▪️Предоставляется при условии, если среднедушевой доход по объективным причинам ниже критерия нуждаемости — 509,62 руб. (с 1 мая).\n▪️Многодетным семьям такое пособие назначается, когда среднедушевой доход не более 1,15 величины критерия нуждаемости — 586,1 руб. (с 1 мая).\n➡️Единовременное социальное пособие\n▪️Выплачивается, когда человек оказался в трудной жизненной ситуации, при условии, что среднедушевой доход не более 1,5 величины критерия нуждаемости — 764,43 руб. (с 1 мая).\n➡️Социальное пособие для возмещения затрат на приобретение предметов гигиены\n▪️Предоставляется независимо от величины дохода семьи детям-инвалидам, имеющим IV степень утраты здоровья, и инвалидам I группы.\n➡️Обеспечение продуктами питания детей первых двух лет жизни\n▪️Предусмотрено для семей, где доход ниже критерия нуждаемости, а также при рождении двойни и более детей — независимо от уровня дохода.\nПодробнее на сайте', '', NULL, '2026-05-04', NULL, 'usernews', 'Telegram Import'),
(364, 'Новость', 'Поддержка бывшей узницы Валентины Поленок', 'tg-8716-podderzhka-byvshey-uzniczy-valentiny-polenok', 'В Гомеле поддержали бывшую узницу Валентину Поленок\nНакануне Дня Победы ветераны Великой Отечественной войны, а также граждане, пострадавшие её последствий, получают от государства всестороннюю, в том числе материальную поддержку.', '', NULL, '2026-05-04', NULL, 'usernews', 'Telegram Import'),
(365, 'Мероприятие', 'Вручение материальной помощи ко Дню Победы', 'tg-8717-vruchenie-materialnoy-pomoschi-ko-dnyu-pobedy', '⚡️В преддверии Дня Победы представители УСЗ администрации,ТЦСОН, общественных объединений, организаций и РУП \"Белпочта\" вручали единовременную материальную помощь Поленок Валентине Филипповне и Захаренко Виктору Ивановичу, бывшим несовершеннолетним узникам, проживающим на территории района.\n⚡️Выплата материальной помощи ко Дню Победы стартует 4 мая и будет выплачена по 8 мая.', '', NULL, '2026-05-04', NULL, 'usernews', 'Telegram Import'),
(366, 'Мероприятие', 'Профилактическое мероприятие «Дом без пожаров»', 'tg-8721-profilakticheskoe-meropriyatie-dom-bez-pozharov', '🚫🔥 «Дом без пожаров»: учимся предупреждать беду.\nВ отделении дневного пребывания проведено профилактическое мероприятие «Дом без пожаров». Посетителям напомнили главные правила бытовой безопасности, разобрали типичные ошибки и еще раз проговорили алгоритм действий при ЧС.\nБережём себя и близких: если случилась беда — звоните 101 или 112. Бережливость и внимание к деталям спасают жизнь! ❤️', '', NULL, '2026-05-04', NULL, 'usernews', 'Telegram Import'),
(367, 'Мероприятие', 'Активный досуг в ТЦСОН', 'tg-8722-aktivnyy-dosug-v-tczson', 'Активный досуг в ТЦСОН: посетители отделения дневного пребывания для граждан пожилого возраста продолжают осваивать бильярд! 🎱\n✨Занятия развивают меткость, глазомер и логику, а главное — дарят отличное настроение и живое общение.\n💥Успехи игроков растут, с каждым разом партии становятся всё интереснее. Бильярд — отличная тренировка для ума и тела!', '', NULL, '2026-05-04', NULL, 'usernews', 'Telegram Import'),
(368, 'Новость', 'YouTube-канал «вДудь» признан экстремистским', 'tg-8723-youtube-kanal-vdud-priznan-ekstremistskim', 'В Беларуси YouTube-канал \"вДудь\" Юрия Дудя признан экстремистским, сообщает Мининформ страны\nСоответствующее решение 27 апреля принял суд Железнодорожного района Витебска.\nИнформация внесена в Республиканский список экстремистских материалов.\n@belvestnik', '', NULL, '2026-05-04', NULL, 'usernews', 'Telegram Import'),
(369, 'Новость', 'Фейк: гражданам рассылают сообщения от МВД', 'tg-8724-feyk-grazhdanam-rassylayut-soobscheniya-ot-mvd', '‼️ Фейк! Гражданам рассылают «сообщения от МВД».\nОткровенно говоря, это такая проверка на критическое мышление. Сообщение от силовиков, которое вирусно распространяется через мессенджеры, само по себе несуразное. Тем не менее, практика показывает, что кто-то может поверить даже в такую чушь.\n«Просим вас быть в курсе того, что существует группа людей, которая ходит по квартирам, выдавая себя за сотрудников внутренних дел. Они имеют при себе документы и бланки с символикой Министерства внутренних дел и утверждают, что им необходимо проверить наличие действительных удостоверений личности у всех жильцов и снять биометрию в преддверии предстоящей «переписи населения».  Выглядят презентабельно. Могут носить форму МВД. Они высказывают намерение сделать вашу фотографию снять отпечаток пальца «в рамках какой-то схемы». При себе имеют ноутбук, биометрическое устройство и список жильцов. Они показывают этот список и просят предоставить всю эту информацию.\nНапоминаем, что никаких подобных инициатив со стороны Правительства не существует. Никаких биометрических данных просьба не предоставлять! Дверь не открывать! В дом не пускать!! Могут совершать кражи и мошенничества! Не предоставляйте никакой информации без официальных процедур в учреждениях органов власти. Будьте бдительными!».\n⚠️ Следует согласиться с неизвестными авторами этого послания - быть бдительными не повредит. Вот только, разумеется, никаких походов по квартирам нет, да и в МВД об опасности таких «ходоков» не предупреждали. Зачем это делается? Вероятно, для создания в обществе напряженности, а еще - создания негативного имиджа милиции.\nДоверяйте только проверенным источникам информации. Сарафанное радио - не самый надёжный транслятор новостей.\nСообщить о фейке', '', NULL, '2026-05-04', NULL, 'usernews', 'Telegram Import'),
(370, 'Мероприятие', 'Поздравление ветеранов ко Дню Победы', 'tg-8726-pozdravlenie-veteranov-ko-dnyu-pobedy', '🇧🇾В Железнодорожном районе с поздравлениями посещают ветеранов ВОВ администрация района, социальная служба, общественные объединения и организации.\n❤️Сегодня в гостях у партизанки Сергеевой К.И. и блокадницы Андреевой Л.Ф.\nБодрости и крепости нашим победителям!', '', NULL, '2026-05-05', NULL, 'usernews', 'Telegram Import'),
(371, 'Мероприятие', 'Акция «Спасибо за Победу»', 'tg-8732-akcziya-spasibo-za-pobedu', '💐СПАСИБО ЗА ПОБЕДУ!💐\nСпециалистами ТЦСОН в преддверии Дня Победы организована выездная акция.\n⚡️Дети из семей, состоящих на учёте в Центре, написали письма ветеранам Великой Отечественной войны со словами благодарности за мирное небо над головой и пожеланиями крепкого здоровья и силы духа!\nВсе письма будут переданы ветеранам Великой Отечественной войны Железнодорожного района.', '', NULL, '2026-05-05', NULL, 'usernews', 'Telegram Import'),
(372, 'Мероприятие', 'Встреча клуба «Интеллектуалы серебряного возраста»', 'tg-8737-vstrecha-kluba-intellektualy-serebryanogo-vozrasta', '🇧🇾 Связь поколений жива!\nВ преддверии Дня Победы в рамках клуба «Интеллектуалы серебряного возраста»  прошла необычная встреча. Волонтер Белла Мордуховна Лифшиц собрала вместе «серебряных» интеллектуалов и учащихся Гомельского колледжа строителей.\nУчастники диалога вспомнили о жизненном пути великого маршала Рокоссовского , его роли в освобождении нашего города и личном подвиге.\nТакие встречи стирают временные границы и показывают, что интерес к подвигам предков объединяет и старшее поколение, и молодежь.\nПока жива память о героях, будет жива и наша история.\nСпасибо Белле Мордуховне за душевную встречу!', '', NULL, '2026-05-05', NULL, 'usernews', 'Telegram Import'),
(373, 'Мероприятие', 'Поздравление ветеранов ВОВ в Железнодорожном районе', 'tg-8740-pozdravlenie-veteranov-vov-v-zheleznodorozhnom-rayone', '⚡️В Железнодорожном районе не перестают благодарить за мирное небо и продолжают поздравлять наших дорогих ветеранов ВОВ с Днём Победы!\nСегодня в гостях у инвалида ВОВ Пасюги И.А., тружениц тыла Скидан Л.Б. и Ухиной П.Д. администрация района, социальная служба, МЧС, общественные объединения и организации.\nО многом хочется спросить и многому поучиться!', '', '61854801d12ddec294edd524652bbcd1.mp4', '2026-05-06', NULL, 'usernews', 'Telegram Import'),
(374, 'Мероприятие', 'Патриотическая беседа «Женщины Гомельщины»', 'tg-8750-patrioticheskaya-beseda-zhenschiny-gomelschiny', 'О тех, кто дарил жизнь и защищал мир\nВ преддверии 81-й годовщины Великой Победы в отделении социальной реабилитации, абилитации инвалидов ТЦСОН Железнодорожного района г. Гомеля прошла Патриотическая беседа «Женщины Гомельщины в годы войны» стала данью памяти тем, кто наравне с мужчинами приближал май 1945 года.', '', NULL, '2026-05-06', NULL, 'usernews', 'Telegram Import'),
(375, 'Мероприятие', 'ТЦСОН на митинге ко Дню Победы', 'tg-8754-tczson-na-mitinge-ko-dnyu-pobedy', '⚡️ТЦСОН на митинге,посвященном Дню Победы!\n🇧🇾Помним,гордимся!', '', NULL, '2026-05-07', NULL, 'usernews', 'Telegram Import'),
(376, 'Мероприятие', 'Письма ветеранам в акции «Спасибо за Победу»', 'tg-8755-pisma-veteranam-v-akczii-spasibo-za-pobedu', '💐СПАСИБО ЗА ПОБЕДУ!💐\nСпециалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля\" в преддверии Дня Победы в рамках акции \"Спасибо за Победу!\" переданы письма со словами благодарности за мирное небо ветеранам и участникам Великой Отечественной войны Железнодорожного района, написанные детьми из семей, состоящих на учёте в Центре со словами благодарности за мирное небо.\nВетераны и участники Великой Отечественной войны пожелали нашим деткам здоровья, мира в их семьях, счастья и исполнения заветных желаний.', '', NULL, '2026-05-07', NULL, 'usernews', 'Telegram Import'),
(377, 'Новость', 'ФСЗН предупреждает о мошенниках', 'tg-8759-fszn-preduprezhdaet-o-moshennikah', '‼️ВНИМАНИЕ- МОШЕННИКИ!\n🇧🇾 Фонд социальной защиты населения Министерства труда и социальной защиты Республики Беларусь предупреждает о случаях телефонного мошенничества.\nМошенники под видом работников ФСЗН звонят гражданам с мобильных телефонов с целью получения персональных данных.\n📣Официально заявляем:\n➡️ Мы НЕ запрашиваем по телефону информацию, которая может компрометировать вашу финансовую безопасность.\n➡️ Все официальные запросы информации происходят в установленном законодательством порядке, как правило, при личном обращении или через портал электронных услуг.\n➡️ Единственные официальные телефоны для связи с отделениями Фонда — стационарные номера, опубликованные на нашем официальном сайте: ssf.gov.by.', '', NULL, '2026-05-07', NULL, 'usernews', 'Telegram Import'),
(378, 'Мероприятие', 'Поздравление ветерана Н.Н. Калабыниной', 'tg-8760-pozdravlenie-veterana-n-n-kalabyninoy', '⚡️В Железнодорожном районе продолжается поздравление ветеранов ВОВ с Днём Победы!\nАдминистрация района, ОАО \"Гомельстекло\", ГГУ им.Сухого, СШ 42, социальная служба, общественные объединения и организации посетили УВОВ Калабынину Н.Н.\n🇧🇾Любим,гордимся!', '', NULL, '2026-05-07', NULL, 'usernews', 'Telegram Import'),
(379, 'Мероприятие', 'Выставочный проект «Женские лики Победы»', 'tg-8765-vystavochnyy-proekt-zhenskie-liki-pobedy', '🇧🇾 ПРОЕКТ ГОРСОВЕТА В ГОД БЕЛОРУССКОЙ ЖЕНЩИНЫ И В ПРЕДДВЕРИИ ДНЯ ВЕЛИКОЙ ПОБЕДЫ\n«ЖЕНСКИЕ ЛИКИ ПОБЕДЫ»\n🇧🇾 В Год белорусской женщины и в преддверии священного праздника — Дня Победы в Гомеле открылся уникальный выставочный проект «Женский лик Победы».\n🎞 В Музее истории печати и фотографии Гомельщины представили не просто портреты, а глубокое исследование мужества, красоты и силы духа женщин, чьё детство было опалено войной.\n🇧🇾 Проект, инициированный Гомельским городским Советом депутатов, ставит своей целью сохранение исторической памяти через призму личных событий в истории войны.\n🌟💫🌟Главными героинями стали пять жительниц Гомеля — Полина Пьявкина, Анна Банкрашкова, Инайда Савчиц,Валентина Киселева , Лидия Папсуева - бывшие малолетние узницы фашистских концлагерей. Те, кто выжил вопреки всему и пронёс через десятилетия веру в жизнь и доброту, любовь к своей родине!\n🤝🤝 Над созданием образов работала целая команда профессионалов: стилисты, парикмахеры и визажисты. Их задачей было не просто подготовить героинь к съёмке, а подчеркнуть их величие, благородство и неувядающую красоту, которую не смогли сломить никакие военные тяготы.\n🎞 Запечатлела эти образы ведущий фотокорреспондент БЕЛТА Марина Васильева.\n🇧🇾 Елена Алексина председатель Гомельского городского Совета депутатов отметила, что для каждого из нас война — это история, а для участниц-героинь это суровая реальность.\n💬💬 - В год белорусской женщины и в преддверии Дня Победы, именно такая идея о создании проекта была рождена Ириной Коноваловой, депутатом Гомельского городского Совета депутатов. Но а затем эта идея воплотилась в настоящий проект в который были приглашены партнёры и самое главное наши героини, женщины малолетние узники фашистских концлагерей, - отметила Елена Ивановна. Мы с вами 81 год живём в мирной Беларуси и это благодаря подвигу советского народа. Это благодаря бесстрашию офицеров, солдат, партизан, подпольщиков, отрядов народного ополчения, которые героически защищали родную землю.\nФотоработы выполнены с акцентом на детали лиц. Каждая морщинка, каждый взгляд в камеру транслируют сильный характер и волю, ставшие символом героизма всего белорусского народа. Рядом с фотографиями расположены QR-коды, отсканировав которые каждый посетитель сможет узнать историю конкретной участницы, - акцентировала председатель горсовета\n💬💬 - В основе проекта была заложена концепция отобразить живую историю через призму человеческих эмоций. Опытные стилисты, визажисты, фотограф, работали над реализацией проекта, чтобы через искусство передать собирательный женский образ в годы Великой Отечественной войны. Участницы проекта это 5 женщин, которые прошли через войну. Их судьба пропитана их героическими и трагическими страницами истории нашей страны, - отметила Ирина Коновалова, депутат Гомельского городского Совета депутатов.\nСамым трогательным моментом мероприятия стало выступление самих героинь. Полина Никитична Пьявкина, малолетняя узница фашистских концлагерей не скрывала слёз радости:\n💬💬- Огромное спасибо за приглашение и за ту красоту, которую нам подарили. Мне будто продлили жизнь.Любите свою страну, делайте свою работу во имя будущего. Нам не всё равно, что будет после нас, и мы верим, что наша молодёжь не допустит того, что пережили мы.\n🤝📕 🤝После официальной части состоялась церемония вручения благодарностей всем партнёрам и участникам, чей отклик, уважение памяти, гордость подвигом героев- победителей и высокий профессионализм помогли реализовать этот важный социальный проект.\n💬Молодёжь,посетившая выставку, поделилась мнением ➡️', '', NULL, '2026-05-07', NULL, 'usernews', 'Telegram Import'),
(380, 'Мероприятие', 'Чистый четверг в ТЦСОН', 'tg-8768-chistyy-chetverg-v-tczson', '🍃ЧИСТЫЙ ЧЕТВЕРГ\nВ ТЦСОН 🍃\n⚡️Сделаем наш город ещё чище и красивее!', '', NULL, '2026-05-07', NULL, 'usernews', 'Telegram Import'),
(381, 'Мероприятие', 'Поздравление граждан военного детства', 'tg-8772-pozdravlenie-grazhdan-voennogo-detstva', '⚡️Накануне Дня Победы волонтёры «серебряного» возраста отряда «Зов Сердца» ТЦСОН поздравляют на дому граждан, детство которых выпало на годы Великой Отечественной войны.\n🇧🇾 Любим! Помним!', '', NULL, '2026-05-07', NULL, 'usernews', 'Telegram Import'),
(382, 'Мероприятие', 'Концертная программа ко Дню Победы', 'tg-8773-konczertnaya-programma-ko-dnyu-pobedy', '⚡️В преддверии 9 Мая в ТЦСОН состоялась концертная программа «Никто не забыт и ничто не забыто», посвящённая Великой Победе.\nНа мероприятии присутствовали те, чьё детство пришлось на суровые военные годы.\n9 Мая для каждого из нас — день гордости за свой народ, за свою армию.\nВеликие жертвы и великие потери, мужество солдат и пронзительные песни военных лет — вся эта история продолжает жить в наших сердцах.\nНе забыли сказать несколько тёплых слов и постоянному посетителю отделения дневного пребывания Павлушовой Е.П., родившейся 9 мая 1945 года.', '', NULL, '2026-05-07', NULL, 'usernews', 'Telegram Import'),
(383, 'Мероприятие', 'ТЦСОН на митингах ко Дню Победы', 'tg-8775-tczson-na-mitingah-ko-dnyu-pobedy', '⚡️ТЦСОН принимает участие в митингах, посвященных Дню Победы!\n🇧🇾Помним! Гордимся!', '', NULL, '2026-05-08', NULL, 'usernews', 'Telegram Import'),
(384, 'Мероприятие', 'Поздравление ветеранов с Днём Победы', 'tg-8778-pozdravlenie-veteranov-s-dnem-pobedy', '⚡️В Железнодорожном районе продолжают поздравлять наших дорогих ветеранов ВОВ\nс Днём Победы!\nСегодня в гостях у УВОВ Корако А.И., УВОВ Барсук М.А., труженика тыла Белова В.Ф.\nК поздравлениям присоединяются и администрация района, и предприятие, и школа,и общественные объединения и организации, и социальная служба.\n🇧🇾Помним! Гордимся! Радуемся, что вместе ещё можем и спеть!', '', '7f4d24f2689f105171213bda7b77359f.mp4', '2026-05-08', NULL, 'usernews', 'Telegram Import'),
(385, 'Мероприятие', 'Акция «Дом без пожара»', 'tg-8789-akcziya-dom-bez-pozhara', '✅Представители добровольного пожарного формирования второго типа и волонтеры отряда серебряного возраста «Зов Сердца» отделения дневного пребывания ТЦСОН проинформировали жителей района о правилах пожарной безопасности.\n✅В рамках акции «Дом без пожара» распространялись памятки, как уберечь жилье от возгорания.', '', NULL, '2026-05-08', NULL, 'usernews', 'Telegram Import'),
(386, 'Мероприятие', 'Участие в праздничном шествии ко Дню Победы', 'tg-8791-uchastie-v-prazdnichnom-shestvii-ko-dnyu-pobedy', '⚡️В праздничном шествии, посвященном Дню Победы участвуют узники фашистских концлагерей Шапоров Н.М. и Санков Н.П.,проживающие в Железнодорожном районе.\n🇧🇾Помним!Гордимся!', '', NULL, '2026-05-09', NULL, 'usernews', 'Telegram Import'),
(387, 'Новость', 'Они защищали - мы защищаем', 'tg-8794-oni-zaschischali-my-zaschischaem', 'Они защищали – мы защищаем. Они отстояли – мы бережем. Мужество передается не по крови, а по духу. И этот дух живет в каждом из нас.\nНаша Победа – это наша память, символ национальной гордости и великой радости, напоминающей о цене мирной жизни. Мы уважаем подвиг предков, ценим мир и свободу, которые имеем сегодня благодаря их отваге.\n9 Мая – день, который объединяет поколения вокруг памяти о героизме, стойкости и Великой Победе. Это не только история — это урок, который мы должны помнить всегда.', '', '50c1cb1885b2b16cf7346b5da2cd0f6e.mp4', '2026-05-09', NULL, 'usernews', 'Telegram Import'),
(388, 'Мероприятие', 'Праздничное шествие ко Дню Победы', 'tg-8795-prazdnichnoe-shestvie-ko-dnyu-pobedy', '🔥🔥🔥🔥🔥🔥\n💪💪Праздничное шествие по улице Советской в Гомеле!\nВ день 81-й годовщины Великой Победы на улице Советской стартовало торжественное шествие. В едином строю — представители власти, общественных организаций и гомельчане разных поколений.\nВо главе колонны офицеры МЧС несут копию Знамени Победы — того самого штурмового флага 150-й стрелковой дивизии, водружённого над Рейхстагом.\nС праздником, дорогие гомельчане! С Днём Великой Победы! 💪', '', NULL, '2026-05-09', NULL, 'usernews', 'Telegram Import'),
(389, 'Новость', 'Живая память на улице Советской', 'tg-8796-zhivaya-pamyat-na-ulicze-sovetskoy', 'Живая память на улице Советской: Гомель вышел на торжественное шествие\nПод ритм барабанов по главной улице города прошли представители силовых структур, курсанты, кадеты, трудовые коллективы, спортсмены и простые горожане — от ветеранов до самой юной молодёжи.', '', NULL, '2026-05-09', NULL, 'usernews', 'Telegram Import'),
(390, 'Новость', 'Память поколений - наш нравственный ориентир', 'tg-8797-pamyat-pokoleniy-nash-nravstvennyy-orientir', '✨✨✨✨✨ Память поколений — наш главный нравственный ориентир\nСегодня наша страна отмечает 81-ю годовщину Великой Победы. Беларусь помнит, какой ценой достался этот мир: миллионы жизней, сожжённые сёла, опалённое войной детство. Война не смогла сломить наш народ и нашу веру в справедливость.\nДорогие ветераны! 9 Мая — ваш праздник, день славы и подвига, навсегда в наших сердцах. Низкий поклон за мир, чистое небо и право жить. Ваша стойкость — главный пример для всех нас.\n🇧🇾 Забота о вас — наш ежедневный долг. Министерство труда и социальной защиты видит в этом одну из главных своих задач, чтобы вы всегда чувствовали государственную поддержку и внимание.\nМир хрупок — мы помним об этом. И потому сегодня мы склоняем головы перед памятью павших, чтим живых героев и обещаем беречь их наследие каждый день. Вечная слава победителям! Пусть над нашей землёй всегда будет мирное небо.\n💐 ⭐️С Днём Победы!\nС уважением,\nАндрей Лобович\nМинистр труда и социальной защиты', '', NULL, '2026-05-09', NULL, 'usernews', 'Telegram Import'),
(391, 'Новость', 'День Государственного флага, герба и гимна', 'tg-8798-den-gosudarstvennogo-flaga-gerba-i-gimna', '🟥🟩Уважение к государственным символам — наша осознанная гражданская позиция\nСердечно поздравляю вас с Днем Государственного флага, Государственного герба и Государственного гимна Республики Беларусь.\nГлавные символы нашей страны олицетворяют независимость и суверенитет белорусского государства. В них заключена многовековая история народа, его устремленность к созиданию и миру.\nФлаг, герб и гимн наполнены глубочайшим уважением к подвигу предков, отстоявших право жить на родной земле, и верой в силы нынешнего и грядущих поколений.\n‼️Уважение к государственным символам — это осознанная гражданская позиция. Для Министерства труда и социальной защиты этот день символизирует также преемственность и незыблемость нашего главного принципа — заботы о человеке труда, о ветеранах, семьях и детях.\nВ этот праздничный день желаю всем белорусам крепкого здоровья, мира, добра и новых свершений во благо родной страны. Пусть наши государственные символы всегда вызывают у нас чувство гордости и ответственности за судьбу Отечества.\nС уважением,\nМинистр труда и социальной защиты\nАндрей Лобович', '', NULL, '2026-05-10', NULL, 'usernews', 'Telegram Import'),
(392, 'Новость', 'Акция «Дом без насилия»', 'tg-8800-akcziya-dom-bez-nasiliya', 'ДОМ БЕЗ НАСИЛИЯ\nС 11 по 17 мая 2026 года проходит республиканская профилактическая акция «Дом без насилия!».\nЦель акции - вовлечение широких слоев населения в оказание помощи и содействия государственным органам в решении проблем насилия в семье, а также информирование населения о мерах борьбы с насилием.\nВ рамках акции предусмотрены профилактические мероприятия с оказанием психологической и необходимой помощи. За совершение домашнего насилия предусмотрена административная и уголовная ответственность.\nЕсли вы подвергаетесь домашнему насилию, обратитесь в оперативно-дежурную службу ОВД администрации Железнодорожного района г. Гомеля, в учреждение «Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля» или по телефонам «Доверие» 34-97-92, «107».\nНе оставайтесь равнодушными и своевременно информируйте о таких фактах органы внутренних дел.', '', NULL, '2026-05-11', NULL, 'usernews', 'Telegram Import'),
(393, 'Новость', 'Как защититься от укусов мошек', 'tg-8802-kak-zaschititsya-ot-ukusov-moshek', '⚡Мошки в Гомеле: как защититься от укусов и сохранить комфорт.\nС приходом тепла в Гомеле появились мошки, которые могут доставлять дискомфорт и вызывать неприятные последствия после укусов.\n⁉️Как уберечься от укусов?\n✅носите закрытую одежду, особенно в местах скопления мошек (парки, лесные зоны, водоёмы)', '', NULL, '2026-05-11', NULL, 'usernews', 'Telegram Import'),
(394, 'Мероприятие', 'Правила моего дома', 'tg-8803-pravila-moego-doma', '🏠ПРАВИЛА МОЕГО ДОМА🏡\nЗаведующим отделением комплексной поддержки в кризисной ситуации учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля\" в рамках республиканской профилактической акции 🙅‍♀️ \"Дом без насилия\" 🙅‍♀️проведены психолого-профилактические встречи с социальными работниками Центра в рамках и с родителями учащихся ГУО \"Средняя школа № 9 г. Гомеля\" .\nВ рамках встреч обсуждались такие вопросы, как:\n📌сформировать семейные ценности и традиции в воспитании подрастающего поколения', '', NULL, '2026-05-13', NULL, 'usernews', 'Telegram Import');
INSERT INTO `news` (`id`, `type`, `title`, `slug`, `description`, `freim`, `video_filename`, `date`, `created_by_user_id`, `created_by_login`, `created_by_name`) VALUES
(395, 'Мероприятие', 'Секреты семейного счастья', 'tg-8808-sekrety-semeynogo-schastya', '❤️🌸Секреты семейного счастья❤️🌸\nВ преддверии Дня Семьи специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля\" проведена диалоговая площадка 🌹\"Семейные традиции. Роль женщины в семье\" 🌹для учащихся УО \"Гомельский государственный колледж строителей\" с приглашением золотых юбиляров семейной жизни:\n✅семья Стаховец — поделились своим многолетним опытом семейной жизни, секретами, которые помогли сохранить любовь и гармонию на протяжении многих лет.\n📌Людмила Николаевна прочла свои стихи и вместе с Григорием Андреевичем исполнила песни собственного сочинения, демонстрируя гармонию и единство в их семейной жизни.\n📌Супруги поделились опытом воспитания шестерых детей, из которых четверо являются приемными, подчеркнув важность любви, доверия и взаимопонимания в семье.\n📌Участники из числа детей-сирот и детей, оставшихся без попечения родителей, высказали свое мнение о фундаменте их будущих СЕМЕЙ.', '', 'efddbd82a6975c2ba5530ec730238930.mp4', '2026-05-13', NULL, 'usernews', 'Telegram Import'),
(396, 'Мероприятие', 'Мы против домашнего насилия', 'tg-8815-my-protiv-domashnego-nasiliya', '🙋МЫ ПРОТИВ ДОМАШНЕГО НАСИЛИЯ!\nСпециалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" совместно с сотрудниками ОВД администрации Железнодорожного района г.Гомеля в рамках республиканской профилактической акции 🏠\"Дом без насилия\"❗️ проведена встреча с трудовым коллективом КПУП \"Гомельводоканал\", направленная на профилактику и предупреждение семейно-бытовых конфликтов.\nВ ходе диалога субъектами профилактики освещены вопросы по оказанию помощи, пострадавшим от домашнего насилия, разъяснены положения законодательства по предупреждению насилия в семье.', '', NULL, '2026-05-13', NULL, 'usernews', 'Telegram Import'),
(397, 'Новость', 'Памятка для велосипедистов', 'tg-8817-pamyatka-dlya-velosipedistov', 'Памятка для велосипедистов', '', NULL, '2026-05-14', NULL, 'usernews', 'Telegram Import'),
(398, 'Мероприятие', 'Чистый четверг в ТЦСОН', 'tg-8818-chistyy-chetverg-v-tczson', '🍃ЧИСТЫЙ ЧЕТВЕРГ🍃\nв ТЦСОН\n🍃Сделаем наш город чище вместе!', '', NULL, '2026-05-14', NULL, 'usernews', 'Telegram Import'),
(399, 'Новость', 'Господдержка семей с детьми', 'tg-8819-gospodderzhka-semey-s-detmi', 'Полезно знать! Вот 10 самых фактов о господдержке семей с детьми\n1⃣ Семейный капитал за третьего ребенка — 35 505 руб.\nЕсли в семье рождается или усыновляется третий или последующий ребенок, государство зачисляется на специальный счет - \"Семейный капитал\"\nДеньги можно направить в безналичном порядке на жилье, образование, медицину, приобретение средств реабилитации\n2⃣Отпуск по уходу за ребенком — до 3 лет\nБеларусь остается одной из немногих стран, где оплачиваемый отпуск по уходу за ребенком можно использовать до достижения ребенком трех лет.\n3⃣Единовременная выплата при рождении\nС мая 2026:\nза первого ребенка — 5096,20 руб.\nза второго и последующих — 7134,68 руб.\n5⃣Выплата за раннюю постановку на учет по беременности\nЕсли женщина становится на медицинский учет до 12 недель беременности, выплачивается единовременное пособие - 100% БПМ\n5⃣Родитель получает пособие по уходу за ребенком, даже если работает\nПри выходе на неполный рабочий день (0,5 ставки или на работу на дому) до достижения ребенком 3 лет пособие по уходу сохраняется в полном объеме. При полной занятости пособие выплачивается в размере 50%.\nВместе с пособием по уходу за ребенком до 3 лет выплачивается пособие на старших детей (50% БПМ на семью) – все три года.\n6⃣Многодетной семья считается уже с 3 детьми до 18 лет\nПосле рождения третьего ребенка семья официально получает статус многодетной, а вместе с ним — дополнительные жилищные, налоговые и социальные льготы.\n7⃣Льготные жилищные кредиты для многодетных\nМногодетные семьи могут претендовать на господдержку при строительстве или покупке жилья: субсидии, льготное кредитование и помощь с погашением части долга.\n8⃣Отдельные выплаты детям-инвалидам и семьям с особыми обстоятельствами\nповышенный размер пособия по уходу за ребенком-инвалидом до 3 лет - 1286,82 руб.\nпособия на детей старше 3 лет - 356,73 руб. на ребенка-инвалида, 254,81 – на детей без инвалидности\nсоциальная пенсия на ребенка-инвалида (зависит от степени утраты здоровья) - от 407,70 до 560,58 руб.\nсоциальное пособие на приобретение предметов гигиены для детей-инвалидов с 4 степенью утраты здоровья\n9⃣Размер пособий и социальной пенсии регулярно индексируется\nВыплаты привязаны к бюджету прожиточного минимума и пересматриваются несколько раз в год. Например, в 2026 году размеры повышались в феврале и мае. Далее пересмотр выплат будет в августе и ноябре.\n1⃣0⃣Закон о пособиях регулярно обновляется\nСистема поддержки не «заморожена»: в 2025 году были приняты новые изменения -  увеличены минимальные размеры пособия по беременности и родам по временной нетрудоспособности по уходу за детьми и усилена поддержка семей, воспитывающих детей-инвалидов.', '', NULL, '2026-05-14', NULL, 'usernews', 'Telegram Import'),
(400, 'Мероприятие', 'Активное долголетие в ТЦСОН', 'tg-8820-aktivnoe-dolgoletie-v-tczson', '💃🕺Активное долголетие в условиях дневного пребывания\nВ ТЦСОН люди старшего поколения находят себе занятия по душе: и танцуют,и поют,и себе костюмы шьют!\nНа мероприятии ко Дню семьи каждый может показать свои таланты!\n⌛️Не упустите свой шанс! Раскройте свои таланты! Приглашаем, у нас можно научиться разным умениям и навыкам!', '', 'f139f4ad08a764b59a729f4fef38a69f.mp4', '2026-05-14', NULL, 'usernews', 'Telegram Import'),
(401, 'Мероприятие', 'Откажись от насилия', 'tg-8824-otkazhis-ot-nasiliya', '❗️ОТКАЖИСЬ ОТ НАСИЛИЯ🙅\nСегодня на базе ОВД администрации Железнодорожного района г Гомеля в рамках республиканской акции \"Дом без насилия\" проведено общее групповое профилактическое мероприятие для лиц, состоящих на различных видах учёта в РОВД, с целью профилактики тяжких и особо тяжких преступлений против жизни и здоровья граждан:\n✅Присутствующие проинформированы о социально-психологических видах поддержки гражданам, находящимся в трудной жизненной ситуации и имеющих зависимость от ПАВ', '', NULL, '2026-05-14', NULL, 'usernews', 'Telegram Import'),
(402, 'Мероприятие', 'Лекция о хронической сердечной недостаточности', 'tg-8826-lekcziya-o-hronicheskoy-serdechnoy-nedostatochnosti', 'В отделении дневного пребывания для граждан пожилого возраста ТЦСОН проведена лекция\n«Хроническая сердечная недостаточность в пожилом возрасте,\nпринципы лечения и профилактики» врачом терапевтом ГУЗ «ГГКП № 11».\nПосетители отделения узнали о симптомах и способах лечения хронической сердечной недостаточности.\nЛучшим лечением сердечной недостаточности в целом, и хронической, в частности, остается профилактика заболевания. Она включает в себя: здоровый образ жизни, диету, физические упражнения и своевременные визиты к кардиологу для ранней диагностики артериальной гипертонии или атеросклероза.', '', NULL, '2026-05-14', NULL, 'usernews', 'Telegram Import'),
(403, 'Мероприятие', 'Профилактика домашнего насилия на «Гомсельмаше»', 'tg-8827-profilaktika-domashnego-nasiliya-na-gomselmashe', 'Специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" совместно с сотрудниками ОВД администрации Железнодорожного района г.Гомеля в рамках республиканской профилактической акции \"Дом без насилия\" проведена встреча с трудовым коллективом ОАО \"Гомсельмаш\", направленная на профилактику и предупреждение семейно- бытовых конфликтов, освещены вопросы по оказанию помощи, пострадавшим от домашнего насилия.', '', NULL, '2026-05-14', NULL, 'usernews', 'Telegram Import'),
(404, 'Новость', 'Поздравление с Днём семьи', 'tg-8830-pozdravlenie-s-dnem-semi', '👨‍👩‍👧‍👦Семья — это не только основа общества, но и его душа.\nИменно в кругу близких мы учимся любви, заботе и взаимопомощи, формируем жизненные ценности и передаём традиции следующим поколениям.\nВ этом году особое значение праздника связано с Годом белорусской женщины. Это отличный повод отметить неоценимый вклад женщин в укрепление семьи, воспитание детей и развитие общества. Их мудрость, терпение и душевная щедрость — залог благополучия каждой семьи.\n✨ Государство продолжает уделять большое внимание поддержке семей, материнства и детства, создавая условия для гармоничного развития подрастающего поколения и повышения качества жизни.\nЖелаю всем семьям крепкого здоровья, счастья, согласия и благополучия. Пусть в ваших домах всегда царят любовь, взаимопонимание и тепло, а дети растут в атмосфере заботы и уважения.\n🔥С праздником! С Днём семьи!\nС уважением,\nАндрей Лобович\nМинистр труда и социальной защиты', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(405, 'Новость', 'Каждая пятница - родное, своё!', 'tg-8831-kazhdaya-pyatnicza-rodnoe-svoe', 'Каждая пятница - родное, своё!', '', '03e040d7b6cd00324e1b1475b79d9e10.mp4', '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(406, 'Новость', 'Поздравление с Днём семьи от Надежды Цупы', 'tg-8832-pozdravlenie-s-dnem-semi-ot-nadezhdy-czupy', '👨‍👩‍👧‍👦 С Днём семьи!\nВ этот замечательный день, когда мы отмечаем День семьи, хочу выразить слова глубокой благодарности всем, кто создает, поддерживает и бережно хранит самое важное – семейные ценности. Именно они делают наш народ сильнее.\nПоддержка семьи, материнства и детства является приоритетным направлением государственной политики.\nПристальное внимание уделяется укреплению института семьи, социальной защите многодетных семей и созданию условий для полноценного воспитания детей.\nИ сегодня в Республике Беларусь созданы такие условия, чтобы каждая семья чувствовала себя уверенно и счастливо.\nЖелаю вам крепкого здоровья, благополучия и достатка.\nПусть в ваших домах всегда звучит детский смех, царят мир, взаимопонимание и доверие.\n❤️ С праздником!\n______________________\nС уважением,\nпредседатель комитета по труду, занятости и социальной защите Гомельского облисполкома\nНадежда Цупа', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(407, 'Мероприятие', 'Встреча в рамках акции «Дом без насилия»', 'tg-8833-vstrecha-v-ramkah-akczii-dom-bez-nasiliya', 'В рамках республиканской профилактической акции \"Дом без насилия\" специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г Гомеля\" совместно с сотрудниками ОВД администрации Железнодорожного района г. Гомеля проведена информационно - профилактическая встреча с трудовым коллективом ОАО \"Гомельстекло\".\nГражданам разъяснена ответственность за совершение домашнего насилия, услуга \"кризисной комнаты\"и услуга \" социальный патронат\", а также куда можно обратиться, если человек пострадал от домашнего насилия. Всем участникам мероприятия вручены информационные буклеты с номерами телефонов \"горячей линии\"и телефона \"Доверие\" для граждан, находящихся в трудной жизненной ситуации.\n🙅🏻‍♀️Насилие в семье - это не норма!🙅🏻‍♀️', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(408, 'Мероприятие', 'Конкурс «Семейный оберег»', 'tg-8836-konkurs-semeynyy-obereg', '🏡Творим уют и защищаем дом!🏡\nСегодня в отделении социальной реабилитации , абилитации инвалидов учреждения «Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля» посетители приняли участие в межрегиональном дистанционном конкурсе «Семейный оберег»\n🌿🖌️🌿.\n❤️Любите и берегите своих близких! ❤️', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(409, 'Мероприятие', 'Красота в подарок для пожилых людей', 'tg-8840-krasota-v-podarok-dlya-pozhilyh-lyudey', 'Красота в подарок: сотрудники студии Luxury стали волонтёрами для бабушек и дедушек.\n💥 В отделении дневного пребывания ТЦСОН теперь свой гламур. Хозяйка салона Виктория и её мастера бесплатно стригут и красят волосы пожилым людям. Цель — не просто преображение, а возможность порадовать тех, кто особенно нуждается во внимании и заботе.\n«Мы дарим не причёску, а хорошее настроение и повод любить себя в любом возрасте», — говорит хозяйка салона Виктория.\n✨Спасибо команде Luxury за золотые руки и открытое сердце!❤️', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(410, 'Мероприятие', 'Занятие клуба «Школа здоровья»', 'tg-8841-zanyatie-kluba-shkola-zdorovya', 'В отделении дневного пребывания ТЦСОН прошло занятие клуба «Школа здоровья» на тему «Здоровое питание».\n🥗🌱Участники узнали, какие продукты должны быть в рационе, и главное — что здоровое питание это образ жизни, а не диета 📅✨. В завершение все получили памятки с советами: свежие сезонные продукты, готовка на пару, запекание и прогулки 🚶‍♀️❤️.\nЗаботьтесь о себе сегодня — чтобы радоваться жизни завтра! 🌟', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(411, 'Мероприятие', 'Выездное мероприятие «Семья бесценна»', 'tg-8842-vyezdnoe-meropriyatie-semya-besczenna', 'СЕМЬЯ БЕСЦЕННА❤️\nВ День Семьи специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля\" при поддержке ЖРО Белорусское Общество Красного Креста и первичной профсоюзная организация ТЦСОН проведено выездное поздравительное мероприятие 💐\"СЕМЬЯ БЕСЦЕННА\"💐 для семей, которые приобрели статус многодетной семьи и семей, воспитывающих детей-инвалидов.\n🌹Семьям высказаны слова благодарности за преданности семейным ценностям, сохранение и укрепление семейных традиций, за вклад в укрепление института семьи.🌹\n🌷Желаем нашим семьям здоровья, неиссякаемого семейного счастья и благополучия!💥', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(412, 'Новость', 'Тезисы выступления Министра о семье', 'tg-8846-tezisy-vystupleniya-ministra-o-seme', '📄Основные тезисы выступления Министра\n✔ Важность совместных усилий государства, церкви и общества в воспитании молодёжи и сохранении традиций\nКонсолидация усилий государственных органов, духовенства, общественности всегда имела огромное значение в развитии преемственности поколений, формировании у молодёжи духовных ценностей и социально-нравственных ориентиров\n✔ Молодёжь стала более прагматичной, откладывая создание семьи ради карьеры и финансовой стабильности\nВ современном мире вопросы сохранения полноценной традиционной семьи приобретают всё большую актуальность. Это связано, прежде всего, с изменением ценностных установок у молодого поколения в отношении семьи и рождения детей\n✔ Средний возраст вступления в брак и рождения первого ребёнка растёт, что влияет на демографическую ситуацию\nМы наблюдаем устойчивую тенденцию увеличения возраста вступления в брак и рождения детей. Средний возраст парней, впервые вступающих в брак, уже составил 29 лет, девушек — почти 27 лет\n✔Государство активно поддерживает семьи во всех сферах жизнедеятельности:  здравоохранение, образование, социальная защита\nЕжегодно по всем жизненно важным для семей направлениям государством инвестируются значительные ресурсы. Так, в 2025 году только на семейные пособия было направлено 3,7 млрд.руб.\n✔ Целевой идеологический ориентир — на многодетность\nМатериальные стимулы — это не определяющий фактор рождаемости. Главное — это иметь желание создать счастливую семью, родить и воспитать детей\n✔ Важно формировать позитивное восприятие семьи и родительства через СМИ и социальные сети\nНеобходимо устранить имеющиеся стереотипы в отношении многодетной семьи и родительства в целом... Сегодня есть все основания для формирования иного образа — крепкой, благополучной и успешной многодетной семьи\n✔Создание нанимателями комфортных условий для работающих родителей\nРазвиваем корпоративную демографию - быть более социально ответственными перед работниками с семейными обязанностями', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(413, 'Мероприятие', 'Поздравление многодетной мамы с Днём семьи', 'tg-8847-pozdravlenie-mnogodetnoy-mamy-s-dnem-semi', 'Диалог поколений продолжается: мудрость и душевная щедрость «серебряных» волонтёров согревают.\nВолонтёры «серебряного» возраста отряда «Зов Сердца» ТЦСОН поздравили многодетную маму Наталью Николаевну Давыдову с Международным днём семьи.\nСладкие гостинцы🍪 + душевный подарок своими руками (авторская подушка от кружка «Рукодельница»)🎁 = море улыбок и уютная атмосфера.\nСпасибо нашим активным пенсионерам за то, что напоминают: в семье главное — забота и внимание.❤️🥰', '', NULL, '2026-05-15', NULL, 'usernews', 'Telegram Import'),
(414, 'Мероприятие', 'Круглый стол «Моя семья»', 'tg-8848-kruglyy-stol-moya-semya', '💥МОЯ СЕМЬЯ 💥\nВ рамках областной профилактической акции \"Счастливая семья-счастливое детство\" специалистами учреждения \" Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля\" для семей, находящихся в трудной жизненной ситуации\nпроведен круглый стол \" Моя семья\". В мероприятии приняла участие многодетная мама, Демьянцева Марта Васильевна, воспитывающая совместно с супругом троих детей, которая поделилась опытом в воспитании детей и сохранением семейных ценностей, укреплением семейных традиций и укрепления института семьи.\nНикогда не сдавайтесь, как бы не было сложно, выход из трудных ситуаций есть всегда. Надо верить, ведь рядом с вами ваши дети', '', NULL, '2026-05-16', NULL, 'usernews', 'Telegram Import'),
(415, 'Новость', 'Места для купания в Гомеле', 'tg-8857-mesta-dlya-kupaniya-v-gomele', 'Места, разрешенные для купания в городе Гомеле\n🏖️пляж «Новобелицкий» на реке Сож', '', NULL, '2026-05-17', NULL, 'usernews', 'Telegram Import'),
(416, 'Новость', 'Правила безопасности на воде', 'tg-8858-pravila-bezopasnosti-na-vode', '🏖  В жаркий период многие проводят время вблизи водоемов.\n‼️ Отправляясь на отдых к воде, помните о правилах безопасности и расскажите о них своим детям.\n⚠️  СПАСАТЕЛИ НАПОМИНАЮТ:\nВзрослые, не оставляйте без внимания любой факт пребывания несовершеннолетних одних у воды, их шалости и игры в воде.\n‼️МЧС предупреждает: купайтесь правильно!\n❌Запрещено:\n▪️Оставлять детей без присмотра у воды.\n▪️Заплывать за буйки и купаться в запрещенных местах.\n▪️Плавать в состоянии алкогольного опьянения.\n▪️Нырять с дамб, пристаней, лодок.\n▪️Подавать ложные сигналы бедствия.\n▪️Использовать для плавания доски, матрасы, автомобильные камеры.\n▪️Купайтесь только в разрешенных местах!\nБерегите себя и своих близких! Безопасность на воде – ответственность каждого!', '', NULL, '2026-05-17', NULL, 'usernews', 'Telegram Import'),
(417, 'Новость', 'Бот для проверки экстремистских материалов', 'tg-8859-bot-dlya-proverki-ekstremistskih-materialov', '⚡Проверить список экстремистских материалов можно через бот:\n😎@stop_extremismBLR_bot\n❗️ Пересылка и иное системное распространение из источников в списке грозит административной ответственностью до 15 суток с конфискацией устройства!\nСписок экстремистских формирований:\n❗️ Любая отправка информации, интервью, финансирование или иное содействие повлечёт до 7 лет лишения свободы.\n❗️ Кто нарушит закон, будет осужден и попадёт в список экстремистов.\n🆙 Голосуем за канал\n👮 ГУ\"БАЗА\" 👮‍♂️Бот обр. связи', '', 'a354c5da9fbeb9712faf04a8db873da3.mp4', '2026-05-17', NULL, 'usernews', 'Telegram Import'),
(418, 'Новость', 'В Беларуси стартует акция МЧС «Безопасные каникулы»', 'tg-8860-v-belarusi-startuet-akcziya-mchs-bezopasnye-kanikuly', 'С 15 мая по 31 августа во всех регионах страны пройдет республиканская профилактическая акция МЧС «Безопасные каникулы».\nЛето традиционно считается временем отдыха и приключений, однако именно в этот период резко возрастает количество чрезвычайных ситуаций с участием детей. Напомнить взрослым и детям о ценности жизни и необходимости соблюдения, элементарных правил безопасности, предупредить пожары, трагедии на воде и другие несчастные случаи с участием детей – главная цель акции.\nСтатистика, к сожалению, остается тревожной. За 4 месяца 2026 года по причине детской шалости с огнем произошло 17 пожаров. В воде утонули 2 ребенка. Годом ранее, в 2025‑м, огонь унес жизни 3 детей, зарегистрировано 77 пожаров по причине детской шалости. В воде погибли 12 детей. Каждая такая цифра – это чья‑то оборвавшаяся жизнь и трагедия для целой семьи.\nВ этом году старт акции будет дан там, где тема детской безопасности звучит особенно актуально, – на родительских собраниях, а также во время выпускных в школах и детских садах. Именно в период завершения учебного года, когда дети переходят на «летний режим», важно вовремя донести до родителей и ребят простую мысль: многие трагедии можно предотвратить, если заранее поговорить о возможных опасностях, правилах поведения и продумать досуг.\n1 июня в Международный день защиты детей по всей стране пройдут масштабные мероприятия. На открытых площадках в парках развлечений и отдыха, у дворцов культуры, кинотеатров, торгово-развлекательных центров и других популярных семейных локациях работники МЧС организуют яркую интерактивную программу. Гостей ждут викторины и подвижные конкурсы, демонстрация аварийно-спасательной техники и оборудования, показательные выступления подразделений МЧС, концертные номера, работа ростовых кукол и многое другое.\nВ последующие дни к акции присоединятся активисты Белорусской молодежной общественной организации спасателей-пожарных, представители ОСВОД, Белорусского добровольного пожарного общества. Совместно с работниками МЧС они посетят пришкольные и оздоровительные лагеря, детские сады, познакомят ребят с основными правилами безопасности дома и на улице, расскажут, как не стать жертвой сезонных опасностей и что делать в случае возникновения чрезвычайной ситуации.\nДля летних смен подготовлен целый комплекс креативных и обучающих форматов: квизы по пожарной безопасности, мастер‑классы, игры, конкурсы и викторины. Особый акцент – безопасность на воде и формирование базовых навыков плавания. Ребят ждут «День МЧС», «МЧСЛЭНД», игра «Школа юных спасателей», тематические театрализованные представления, онлайн‑квизы, экскурсии в центры безопасности, музеи и пожарные аварийно‑спасательные части.\nТакие мероприятия помогут детям проявить эрудицию и смекалку, раскрыть творческие способности, проверить свои силу и выносливость, а главное – закрепить жизненно важные правила безопасного поведения. Также будут показаны тематические видео- и мультфильмы МЧС.\nОтдельный адресат акции – родители. Безопасность детей начинается дома: с внимательного отношения взрослых, с простых, но обязательных правил, с личного примера. Не оставляйте малышей без присмотра, уберите из свободного доступа источники повышенной опасности, проговаривайте с детьми возможные риски и последствия опасных игр. Помощником в этом может стать youtube‑канал «МЧС детям», где в доступной форме собраны материалы о безопасности для всей семьи.\nПрисоединяйтесь к мероприятиям акции «Безопасные каникулы». Только совместными усилиями взрослых и детей можно сделать лето по‑настоящему безопасным.', '', NULL, '2026-05-18', NULL, 'usernews', 'Telegram Import'),
(419, 'Новость', '«Семья года» — 2026', 'semya-goda-2026', 'Комитет по труду, занятости и социальной защите Гомельского облисполкома информирует о проведении республиканского конкурса «Семья года» — 2026.\n\nКонкурс направлен на укрепление духовно-нравственных основ семьи, повышение престижа семьи и родительства, поддержку многодетности, сохранение и продвижение семейных ценностей и традиций.\n\nК участию приглашаются семьи, воспитывающие троих и более детей и соответствующие условиям конкурса.\n\nПодробнее: [страница конкурса «Семья года» — 2026](https://ktzsz-gomel.gov.by/semya-goda-2026/)', '', NULL, '2026-05-28', NULL, 'usernews', 'Контент-менеджер'),
(420, 'Новость', 'Многодетная семья Угрюмовых', 'tg-8866-mnogodetnaya-semya-ugryumovyh', '✅Многодетная семья Железнодорожного района г.Гомеля Угрюмовых приняла участие в марафоне семейных роликов, с целью продвижения многогранного образа белорусской женщины, привлечения внимания молодежи к ценности семьи.\nВиктория Александровна, мама, воспитывающая троих детей , успешно реализует себя в семье, материнстве и профессиональной деятельности.\nСемья и дети - это счастье и жизненный успех, поддержка в личностном росте и реализации профессиональных стремлений.', '', 'df274c8de8990016eb25ce61b58cfc1d.mp4', '2026-05-19', NULL, 'usernews', 'Telegram Import'),
(421, 'Новость', 'Сервис «Проверь.Бел»', 'tg-8867-servis-prover-bel', '🌐В Беларуси заработал сервис «Проверь.Бел» — защитите себя от онлайн‑мошенников.\nСледственный комитет Беларуси запустил специализированный онлайн‑сервис «Проверь.бел» (prover.by / praver.by) — инструмент для выявления мошеннических аккаунтов в соцсетях и мессенджерах.\nС помощью платформы пользователи могут оперативно проверить, связан ли тот или иной интернет‑ресурс с мошеннической деятельностью. Достаточно ввести в специальную форму на сайте никнейм аккаунта либо ссылку на него — и система выдаст нужную информацию.\nЧто умеет сервис «Проверь.Бел»:\n➡️выявляет фейковые аккаунты в популярных социальных сетях и мессенджерах', '', NULL, '2026-05-19', NULL, 'usernews', 'Telegram Import'),
(422, 'Мероприятие', 'Танцевальный сезон открыт', 'tg-8868-tanczevalnyy-sezon-otkryt', '💃НУ, КАКАЯ Я БАБКА🕺\n⚡️ \"танцевальный сезон открыт\"\n✅На плащадке ТЦСОН стартовал новый сезон танцевально-развлекательной программы \"Уютный вечерок\".\n✅Мероприятие собрало участников отделения дневного пребывания для всех, кто, несмотря на возраст за 60, продолжает вести активный образ жизни.\nПрисоединяйтесь!', '', 'c85ea1f3d30d1e3c7ccf8c2a2ec7a9d7.mp4', '2026-05-19', NULL, 'usernews', 'Telegram Import'),
(423, 'Мероприятие', 'Проект «Семья без опасности»', 'tg-8872-proekt-semya-bez-opasnosti', 'Специалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г.Гомеля\" в рамках областной профилактической акции \"Счастливая семья- счастливое детство\" продолжается реализация пилотного проекта🙅‍♀️ \"СЕМЬЯ БЕЗ ОПАСНОСТИ\"🙅‍♀️.\nСпециалисты Центра в вечернее время обследуют семьи, чьи дети признаны находящимися в социально опасном положении и семьи из \"группы риска\" Данный проект направлен на:\n📌поддержку безопасного и гармоничного семейного пространства', '', NULL, '2026-05-19', NULL, 'usernews', 'Telegram Import'),
(424, 'Мероприятие', 'Мастер-класс «Первая зелень»', 'tg-8876-master-klass-pervaya-zelen', '«Первая зелень».Оттиск.Тарелка с растительным орнаментом.\nВ отдалении социальной реабилитации,абилитации инвалидов ТЦСОН Железнодорожного района г.Гомеля в рамках работы реабилитационной мастерской \"Изнанка\" состоялся мастер- класс по керамике.\n👉Изготовили глиняные тарелки', '', NULL, '2026-05-20', NULL, 'usernews', 'Telegram Import'),
(425, 'Мероприятие', 'Встреча «Живое слово»', 'tg-8880-vstrecha-zhivoe-slovo', '«Живое слово»: библиотека имени Ленина в гостях у пожилых граждан\n💥Сегодня отделение дневного пребывания ТЦСОН посетили сотрудники библиотеки имени\nВ.И. Ленина.\nПосетителям предложили книги 📚 различных жанров, а также провели оживлённое обсуждение произведений. 📙 Участники делились впечатлениями о знакомых строках, вспоминали любимых авторов и персонажей.\n‼️Чтение остаётся важной частью жизни людей старшего поколения: оно помогает сохранять ясность ума, расширяет кругозор и дарит положительные эмоции.', '', NULL, '2026-05-20', NULL, 'usernews', 'Telegram Import'),
(426, 'Мероприятие', 'Поздравление Зинаиды Савенковой', 'tg-8882-pozdravlenie-zinaidy-savenkovoy', '🌸С 98-летием принимает поздравления ветеран ВОВ Савенкова Зинаида Ивановна!\nК поздравлениям присоединяются администрация района, социальная служба, общественные организации и объединения!\n🌸Бодрости духа и позитивного настроения нашей победительнице!', '', NULL, '2026-05-20', NULL, 'usernews', 'Telegram Import'),
(427, 'Мероприятие', 'Единый день информирования', 'tg-8884-edinyy-den-informirovaniya', '✅О трендах и новых направлениях развития туризма в Беларуси в рамках проведения единого дня информирования в ТЦСОН.\n❤️Нам есть чем гордиться: живописная природа, исторические достопримечательности, культурные памятники,неповторимый фольклор и национальная кухня!\n⚡️Председатель профкома заинтересовала коллектив возможностью посещения в 2026 году нескольких уникальных уголков Гомельской области.', '', NULL, '2026-05-21', NULL, 'usernews', 'Telegram Import'),
(428, 'Мероприятие', 'Профилактика киберпреступлений', 'tg-8886-profilaktika-kiberprestupleniy', '⚡️Берегите себя и своих близких!\nПрофилактика  киберпреступлений среди пожилых людей и людей с  инвалидностью- тема профилактической беседы, состоявшейся в отделении социальной реабилитации, абилитации инвалидов ТЦСОН Железнодорожного района г.Гомеля при участии Боровского В.В., следователя Гомельского городского отдела Следственного комитета Республики Беларусь.\nПожилые люди и люди с инвалидностью часто становятся жертвами мошенников — как в реальной жизни, так и в интернете.\nБудьте бдительны!', '', NULL, '2026-05-21', NULL, 'usernews', 'Telegram Import'),
(429, 'Мероприятие', 'Чистый четверг в ТЦСОН', 'tg-8889-chistyy-chetverg-v-tczson', '🍃ЧИСТЫЙ четверг в ТЦСОН🍃\n🌱Сделаем наш город чище вместе!', '', NULL, '2026-05-21', NULL, 'usernews', 'Telegram Import'),
(430, 'Мероприятие', 'Насилие в семье - это не норма', 'tg-8891-nasilie-v-seme-eto-ne-norma', '✅Специалистами ТЦСОН совместно с сотрудниками ОВД администрации Железнодорожного района г. Гомеля проведена информационно - профилактическая встреча с трудовым коллективом КУП \"Спецкоммунтранс\".\nГражданам разъяснена ответственность за совершение домашнего насилия, услуга \"кризисной комнаты\"и услуга \" социальный патронат\", а также куда можно обратиться, если человек пострадал от домашнего насилия. Всем участникам мероприятия вручены информационные буклеты с номерами телефонов \"горячей линии\"и телефона \"Доверие\" для граждан, находящихся в трудной жизненной ситуации.\n❗Помните❗\n🙅🏻‍♀️Насилие в семье - это не норма!', '', NULL, '2026-05-21', NULL, 'usernews', 'Telegram Import'),
(431, 'Новость', 'МВД предупреждает: будьте бдительны', 'tg-8894-mvd-preduprezhdaet-budte-bditelny', 'МВД предупреждает: будьте бдительными!', '', 'b9d1a9250a532487a07b37a3b6bb13bc.mp4', '2026-05-22', NULL, 'usernews', 'Telegram Import'),
(432, 'Мероприятие', 'Акция «Территория заботы и уважения»', 'tg-8895-akcziya-territoriya-zaboty-i-uvazheniya', '👵🏻ЗАБОТА И УВАЖЕНИЕ👴🏻\n📌\"Самое важное - ценность уважения и заботы о старшем поколении\"📌\n✅Специалистами ТЦСОН совместно с сотрудниками РОВД в рамках выездной акции \"Территория заботы и уважения\" посещены пожилые граждане, находящиеся в уязвимых социальных обстоятельствах, в \"группе риска\" с целью :\n📌проведения профилактических бесед о недопущении противоправных действий в сфере семейно-бытовых отношений, по формированию здорового образа жизни', '', NULL, '2026-05-22', NULL, 'usernews', 'Telegram Import'),
(433, 'Новость', 'Неделя цифровой грамотности', 'tg-8898-nedelya-czifrovoy-gramotnosti', '⚡ С 25 мая на территории Гомельской области стартует общереспубликанская профилактическая акция «Неделя цифровой грамотности», которая продлится до 31 мая.\nСотрудники милиции проведут комплекс мероприятий по повышению осведомленности населения о киберпреступлениях и способах защиты от них, формированию навыков безопасного поведения в Сети.\nБудет развернута широкая информационная кампания, включая размещение профилактических листовок, объявление данных по громкой связи в объектах социальной инфраструктуры, общественном транспорте.\nОсобенностью данной акции является проведение встреч с сотрудниками инспекций по налогам и сборам с целью доведения до граждан информации об актуальных схемах мошенничества, связанных с декларированием денежных средств.', '', 'e352ffc62b33f3c9dd3d2bbc6d66e3eb.mp4', '2026-05-25', NULL, 'usernews', 'Telegram Import'),
(434, 'Новость', 'Ясный язык: опыт Беларуси', 'tg-8899-yasnyy-yazyk-opyt-belarusi', '⚡️Беларусь делится опытом по внедрению «Ясного языка»\nДелегация из Казахстана - эксперты, депутаты, члены разных комиссий, которые занимаются вопросами инклюзии, изучают опыт Беларуси по внедрению «Ясного языка»\n‼️Беларусь — первая на постсоветском пространстве, кто закрепил понятие «ясный язык» законодательно!\nЗакон «О правах инвалидов и их социальной интеграции» (вступил в силу с 6 января 2023 г.)\n✨ Что такое «ясный язык»?\nЭто упрощённая подача информации, чтобы её могли понимать люди с трудностями в чтении и восприятии текста.\n⚖️НПА, в которых закреплен «ясный язык»:\n🟢Национальный план по реализации положений Конвенции о правах инвалидов\nРазработка основ ясного языка\n🟢Закон об авторском праве и смежных правах\nЗакреплено право преобразования произведений в специальный формат для доступа к ним незрячих и слабовидящих\n🟢Закон о защите персональных данных\nВозлагается обязанность простым и ясным языком разъяснить права, связанные с обработкой персональных данных\n🟢Постановление Правительства об обеспечении доступной среды для инвалидов\nСодержит требование к изложению ясным языком информации о доступности объектов социальной и производственной инфраструктуры, а также транспортных средств\n🟢Постановление Минтруда и соцзащиты о требованиях к содержанию и качеству социальных услуг\nОбязательное требование для социальных работников при оказании услуг, обучении людей с инвалидностью различным навыкам - излагать информацию понятным, ясным языком\n🟢Рекомендации Минтруда и соцзащиты по организации специализированных рабочих мест и производственной среды для инвалидов\nдля целей данных рекомендаций также используется ясный язык\n🟢Государственные стандарты по ясному языку  (разработаны Государственным комитетом по стандартизации) :  СТБ 2595-2021 «Ясный язык. Основные положения» и СТБ 2631-2023 «Ясный язык. Требования к процессу подготовки информации на ясном языке».\nВ основных положениях закреплена целевая аудитория – это люди с инвалидностью, испытывающие трудности в понимании информации, это люди с интеллектуальными нарушениями, нарушениями речи, слуха, памяти, после травм головного мозга или инсультов, а также пожилые люди, дети, иностранцы\n🩷Сферы применения «ясного языка»\nЗдравоохранение, образование, соцобслуживание, культура, торговля, бытовое обслуживание, транспорт, туризм — везде, где люди решают свои повседневные вопросы.\n📄 На «ясном языке» должны быть:\n🧡Указатели, карты, планы движения\n🧡Приложения для Интернета и телефона\n🧡Информация для просмотра и прослушивания\n🧡Печатные тексты (документы, например, договоры, важная информация)\n🧡Сайты, страницы в Интернете.\n🤝 В создании НПА и иных материалов участвуют эксперты с инвалидностью.', '', NULL, '2026-05-26', NULL, 'usernews', 'Telegram Import'),
(435, 'Новость', 'Дипфейки и мошенничество', 'tg-8902-dipfeyki-i-moshennichestvo', '🚨ВНИМАНИЕ: ВАШИ «БЛИЗКИЕ» МОГУТ БЫТЬ ФЕЙКОМ\n🧔‍♂️Кажется, что ИИ — это крутые нейросети для генерации котиков и рефератов. Но мошенники тоже не дремлют. Более того — они уже на шаг впереди.\n💻Теперь злоумышленники используют дипфейки и голосовые нейросети. Они могут скопировать голос вашего мужа, дочери или друга с точностью до интонаций. За 30 секунд видео из TikTok/Instagram они создадут реалистичную запись, где «вы» просите о помощи.\nКак это работает:\nВам звонит «сын» с незнакомого номера, плачет и просит срочно перевести деньги за ДТП. Голос не отличить. Или приходит видео от «коллеги», где он просит продиктовать код из СМС для «подтверждения сделки».\n⚡Простые правила безопасности (без паники, но с умом):\n1. Код из СМС = деньги. Никогда, никому и ни при каких обстоятельствах. Даже если человек на том конце провода представляется сотрудником банка или полиции.\n2. Сломанная схема «Перезвони сам». Если «родственник» просит помощи по голосовой связи — прервитесь. Положите трубку и перезвоните на его старый номер, который вы знаете по памяти или сохранен в контактах.\n3. Правило двух минут. Не переходите по ссылкам в мессенджерах, даже если прислал лучший друг. Его аккаунт могли взломать, а ссылка ведет на фишинговый сайт.\n4. Включите двухфакторную аутентификацию во всех соцсетях и почтовых ящиках. Это как вторая дверь у сейфа.\n✅Расскажите об этом родителям, бабушкам и дедушкам. Они — главная цель для таких схем. Им сложнее поверить, что «родной голос» в трубке — это подделка.', '', '88c3b7800d7089152c90996c1b9712c2.mp4', '2026-05-26', NULL, 'usernews', 'Telegram Import'),
(436, 'Мероприятие', 'Акция «От Сердца к Сердцу»', 'tg-8903-akcziya-ot-serdcza-k-serdczu', '❤️От Сердца к Сердцу ❤️\nСпециалистами учреждения \"Территориальный центр социального обслуживания населения Железнодорожного района г. Гомеля\" в рамках мероприятий, приуроченных Международному Дню Защиты Детей организована благотворительная акция для семей, воспитывающих детей-инвалидов\n\"От Сердца к Сердцу\".\nСегодня в благотворительной акции принял участие трудовой коллектив ОАО \"Приорбанк\" ЦБУ 400.\nСемьям оказана помощь и поддержка в виде подгузников и средств личной гигиены от ОАО \"Приорбанк\" ЦБУ 400 .\nРодителям высказаны слова признательности и благодарности за их любовь, терпение и ласку, за сохранение гармонии семейных отношений.\nВыражаем огромную благодарность 👏🏻трудовому коллективу ОАО \"Приорбанк\" ЦБУ 400 за оказанную помощь и их ❤️добрые сердца.', '', NULL, '2026-05-27', NULL, 'usernews', 'Telegram Import'),
(437, 'Мероприятие', 'Вечер воспоминаний в клубе «Хозяюшка»', 'tg-8906-vecher-vospominaniy-v-klube-hozyayushka', 'Заглянули в прошлое и улыбнулись: в «Хозяюшке» состоялся ВЕЧЕР ВОСПОМИНАНИЙ\nВ отделении дневного пребывания ТЦСОН участники клуба «Хозяюшка» устроили трогательную встречу.🤝\nУчастники клуба собрались за столом, пили чай,🍪☕🍫🍰 обменивались рассказами о детстве и юности, пели песни 🎶🎤 прошлых лет.\nОсобый интерес вызвал интерактив с плакатом: гости угадывали себя и других на молодых фотографиях, а затем на те же места добавили актуальные снимки. Конкурс доставил всем массу положительных эмоций.😄🔥\n✨Мероприятие получилось невероятно душевным — таким, после которого на душе становится светлее.❤️❤️❤️', '', NULL, '2026-05-27', NULL, 'usernews', 'Telegram Import'),
(438, 'Мероприятие', 'Выставка «Краски жизни»', 'tg-8907-vystavka-kraski-zhizni', '«Краски жизни»: в ТЦСОН открылась необычная выставка картин ☘️\n💥В отделении дневного пребывания ТЦСОН состоялось открытие выставки «Краски жизни», организованной участниками клуба рисования гуашью «Радуга надежды».\n✨Особенностью экспозиции стал акцент не просто на технике исполнения, но и на эмоциональном наполнении работ. Зрители смогли увидеть, какие чувства вызывает каждая картина, и что именно испытывал автор в момент её создания.\nПрисутствующие авторы работ с интересом взглянули на свои картины под новым углом — через призму переданных эмоций.\n🤝Гостями вернисажа стали представители ТЦСОН Центрального района.\nТёплое продолжение выставка получила за чаепитием: художники 🖌🎨 обменялись опытом и договорились о совместном показе работ.👍🫶', '', NULL, '2026-05-27', NULL, 'usernews', 'Telegram Import'),
(439, 'Новость', 'Порядок опеки и попечительства', 'tg-8909-poryadok-opeki-i-popechitelstva', '✅Совершенствуется порядок осуществления функций по опеке и попечительству\nПостановление Правительства от 26 мая 2026 г. № 260 «О вопросах осуществления функций по опеке и попечительству»\n📌В новой редакции излагаются Положение о порядке управления имуществом подопечных и Инструкция о порядке взаимодействия государственных органов и организаций при принятии решений о даче согласия на отчуждение или об отказе в отчуждении жилых помещений\n🤩Положение о порядке управления имуществом подопечных\n1⃣Определяется порядок и срок назначения опекуна над имуществом\n2⃣ Расширяется перечень расходов, которые могут совершать руководители социальных пансионатов за счет средств проживающих в них подопечных\n3⃣Вводится право на возмещение расходов за время пребывания подопечного в «домашнем отпуске» лицу, взявшему на себя обязательство по содержанию подопечного в домашних условиях\n5⃣Упрощается отчетность для опекуна, попечителя\n🤩Инструкция о порядке взаимодействия государственных органов и организаций при принятии решений о даче согласия на отчуждение или об отказе в отчуждении жилых помещений\nРазделены полномочия за подготовку проектов решений местных органов власти о даче согласия на отчуждение или об отказе в отчуждении жилых помещений.\n📌 Вносятся изменения в Положение об органах опеки и попечительства (постановление Правительства от 28 октября 1999 г. № 1676)\n1⃣Распределены функции между ТЦСОН и структурными подразделениями ЖКХ по опеке и попечительству в отношении имущества подопечных\n2⃣Закреплены полномочия местных органов власти по принятию мер по использованию недвижимого имущества\n3⃣Расширены полномочия структурных подразделений местных органов власти в сфере здравоохранения, определены сроки\n📌 Корректируется примерное положение о координационном совете по вопросам опеки и попечительства над совершеннолетними лицами (постановление Правительства от 24 июня 2020 г. № 368) в части целей, задач координационного совета и закрепления периодичности проведения заседаний.\nПодробнее здесь', '', NULL, '2026-05-27', NULL, 'usernews', 'Telegram Import'),
(440, 'Мероприятие', 'Акция «От Сердца к Сердцу»: ГЗЛиН', 'tg-8910-akcziya-ot-serdcza-k-serdczu-gzlin', 'С добром, открытыми ❤️сердцами и верой в каждый новый успех наших детей трудовой коллектив ОАО \"ГЗЛиН\" поддержал благотворительную акцию ❤️\"От Сердца к Сердцу\"❤️, организованную специалистами ТЦСОН для семей , воспитывающих детей с тяжёлой степенью утраты здоровья.\nСплочённый коллектив предприятия ОАО \"ГЗЛиН\" оказал значимую помощь семьям в виде подгузников, средств личной гигиены, гигиенических принадлежностей, поддержал родителей словами благодарности и признательности за тепло и гармонию в их семьях.\nВыражаем огромную благодарность трудовому коллективу👏 ОАО \"ГЗЛин\" за ДОБРО, понимание, отзывчивость, проявленное к нашим детям.\n💥Добрые дела продолжаются...', '', NULL, '2026-05-28', NULL, 'usernews', 'Telegram Import'),
(441, 'Мероприятие', 'Чистый четверг в ТЦСОН', 'tg-8914-chistyy-chetverg-v-tczson', '☘️«Чистый четверг» ☘️— добрая традиция ТЦСОН🌿\nВ ТЦСОН регулярные уборки территории переросли в полезную традицию «Чистый четверг». Сотрудники Центра выходят наводить порядок: собирают мусор, ухаживают за клумбами и облагораживают прилегающую зону.\nВажно не просто содержать территорию в чистоте, но и создавать уютное пространство для прогулок и отдыха.Такая забота о природе и окружающей среде уже стала неотъемлемой частью жизни коллектива.', '', NULL, '2026-05-28', NULL, 'usernews', 'Telegram Import'),
(442, 'Мероприятие', 'Акция «От Сердца к Сердцу»: Гомсельмаш', 'tg-8915-akcziya-ot-serdcza-k-serdczu-gomselmash', 'Благотворительную акцию ❤️\"От Сердца к Сердцу\"❤️, организованную специалистами ТЦСОН поддержал сплочённый трудовой коллектив предприятия ОАО \"Гомсельмаш\".\nСемьям оказана поддержка в виде подгузников, средств личной гигиены, гигиенических принадлежностей, а деток порадовали сладостями для радости.\nВыражаем огромную благодарность трудовому коллективу👏 ОАО \"Гомсельмаш\" за оказанную помощь, их ❤️открытые сердца и веру в каждое достижение этих уникальных людей: детей и их родителей.', '', NULL, '2026-05-28', NULL, 'usernews', 'Telegram Import'),
(443, 'Мероприятие', 'Викторина «Эрудит-пятерка»', 'tg-8920-viktorina-erudit-pyaterka', '✅В рамках Национальной стратегии «Активное долголетие – 2030» в отделении дневного пребывания ТЦСОН совместно с учреждением «ЦСОН Центрального района г. Гомеля» прошла интеллектуальная викторина «Эрудит-пятерка» на тему «Женщины Беларуси», приуроченная к Году белорусской женщины.\nУчастникам были предложены вопросы о знаменитых и успешных соотечественницах, их достижениях.', '', NULL, '2026-05-29', NULL, 'usernews', 'Telegram Import'),
(444, 'Мероприятие', 'Основы информационной безопасности', 'tg-8924-osnovy-informaczionnoy-bezopasnosti', '⚡️Основы информационной безопасности. Профилактика киберпреступности в ТЦСОН ⚡️\nПрисутствующие узнали о самых распространенных схемах телефонного и интернет-мошенничества: как распознать звонки от лжесотрудников банка, не переходить по подозрительным ссылкам и не сообщать личные данные.\nВ завершение участники получили памятки.\nПодобные профилактические беседы помогают пожилым людям не стать жертвой киберпреступников', '', NULL, '2026-05-29', NULL, 'usernews', 'Telegram Import'),
(445, 'Новость', 'Праздник семейного счастья', 'tg-8925-prazdnik-semeynogo-schastya', '👨‍👩‍👧‍👦🫶 Праздник семейного счастья\n🔥Уже завтра в Лельчицах пройдет отборочный тур регионального этапа VII республиканского конкурса \"Семья года\", в котором примут участие 22 семьи Гомельской области.\nКонкурс проводится в целях укрепления духовно-нравственных основ семьи, повышения престижа семьи и родительства, многодетности, сохранения и продвижения в обществе семейных ценностей и традиций.', '', NULL, '2026-05-29', NULL, 'usernews', 'Telegram Import');

-- --------------------------------------------------------

--
-- Структура таблицы `photos`
--

CREATE TABLE `photos` (
  `id` int NOT NULL,
  `news_id` int NOT NULL,
  `sort_order` int DEFAULT NULL,
  `filename` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `photos`
--

INSERT INTO `photos` (`id`, `news_id`, `sort_order`, `filename`) VALUES
(13, 263, 1, '94215c26718f52b987f52b3f7a16b933.jpg'),
(14, 264, 1, 'eeef939e3a12c2545b6057fc480948aa.jpg'),
(15, 264, 2, 'f108098811087769169a8009a1cd2bf6.jpg'),
(16, 264, 3, '2aa4a1289487d23e013b2c8626609656.jpg'),
(17, 265, 1, '208abc509322076e2be616f12e611b0f.jpg'),
(18, 265, 2, 'cf71a05ee43942ff19eaa7b6ecc8a7be.jpg'),
(19, 265, 3, 'eb85da0850b941d423bb30c4953e85ad.jpg'),
(20, 265, 4, '6813108bb6f7170ce482bd92860339bd.jpg'),
(21, 266, 1, '79f81bd97330fb76211dd21116990bbc.jpg'),
(22, 267, 1, 'ada9ce7a378d4fc4187af64e3c0dfc8e.jpg'),
(23, 268, 1, '9952e8e77fa7c415280c5813795a61c3.jpg'),
(24, 270, 1, 'c20af901a77f2fb3266b2dffcbd48ccc.jpg'),
(26, 272, 1, '424711cd3075afe41a1070ebe187fa38.jpg'),
(27, 273, 2, 'eda95ee11b749ef0eee743ce3f133ca9.jpg'),
(28, 273, 3, '0c66d37ef9df4046b141670c454bd0a4.jpg'),
(29, 273, 1, '271f35289fcbb37e4eb6f7d6f973e7b3.jpg'),
(30, 274, 1, '496cc3b1918fb44d58af043b763d5aac.jpg'),
(31, 274, 2, 'b3f0a0e8c82e94c8e1b62b11defceb68.jpg'),
(32, 274, 3, '6e7e4b424de36b46a91d7e1e9902a603.jpg'),
(33, 274, 4, '344590c7e8835fd09e3f7690c8bacb7c.jpg'),
(34, 274, 5, 'f5460046ce2b7bf5cc0ac051a7146693.jpg'),
(35, 275, 1, 'bb8ed04dbbed867e758d6aa41066fff8.jpg'),
(36, 275, 2, '8e2b99b64dc5e34d9e434eed0ea403ff.jpg'),
(37, 275, 3, 'be749d1e64b9791f0edc40a8ed901ca4.jpg'),
(38, 275, 4, '0412564fb5f7f3b385cb6a4e86c54648.jpg'),
(39, 276, 1, '9b8fed4074726b0e285edec065d882f6.jpg'),
(40, 276, 2, '9690f0215ea102d0a687bac5e4ddbbb9.jpg'),
(41, 276, 3, '4c478fbca2db2236570ddfb5e34aae63.jpg'),
(42, 277, 3, 'b1c7771b2a9e64e2c36efe991cb02667.jpg'),
(43, 277, 1, 'eb653e59d287694efd72e4951e6badb6.jpg'),
(44, 277, 2, '2ce9dffc7270d2b225561edaee75fbbc.jpg'),
(45, 278, 1, '0ae19e2d514df1aec5c5ebfff050faee.jpg'),
(46, 279, 1, 'ed4b044b07ff5fbc2c0c1790226f3bdd.jpg'),
(47, 280, 1, '9a17ce6571a2068ad426d8e8f1ec2f09.jpg'),
(48, 280, 2, '531c1c04ee3faa419c7dc7ed49862e2f.jpg'),
(49, 280, 3, '292456dd80e91114c7365fe6c9e5e8c0.jpg'),
(50, 280, 4, '5e78a2b6c85348ba624b77ac39608269.jpg'),
(52, 282, 1, '298aa1af6a099206e670a1c0cbcfa0a2.jpg'),
(53, 283, 1, '3933e7981919a6f696232476b319bb79.jpg'),
(54, 283, 2, '082c7ac799bac8fd9cd9fbd176f61645.jpg'),
(55, 283, 3, '636eda603d9fbdc5dad90a2abcac4b75.jpg'),
(56, 283, 4, '78cfe1e890d46e42f6fa9104ceaba879.jpg'),
(57, 283, 5, '842cccb77f1fea776e1388253a2895b6.jpg'),
(58, 284, 1, '3a2dfba0c8c8b0cfddb61b5dd23a7142.jpg'),
(59, 285, 1, '1e661659150d6e2fc43fcfd09ee934c4.jpg'),
(60, 286, 1, '4b55ec8c39b78e1d2bc4b42ee78cca66.jpg'),
(61, 287, 1, 'cb4248bef73353e1144a2099a76a161a.jpg'),
(62, 288, 1, '513c265b386caca26babe78339fb060d.jpg'),
(63, 288, 2, '3c5b1c9d41d1cc16e4b8d1a35b96335d.jpg'),
(64, 288, 3, '348dd455e405593cd0930296c9cbb7de.jpg'),
(65, 289, 1, 'db6e0179ec42ff7d369c1218eac83587.jpg'),
(66, 289, 2, '7bfd2188ab986ac763aeec0618a52023.jpg'),
(67, 289, 3, '1abbe5baec607fbcc3e48b7cad331709.jpg'),
(73, 291, 1, '3c4aff85b56865e7f754f041b07bb249.jpg'),
(74, 291, 2, 'ab7618e1fe91c8ac561a8de7c8cd05d3.jpg'),
(75, 291, 3, '2e2403ecb986b90cfec3148e6d551d89.jpg'),
(76, 291, 4, 'b979e5f8e022835d0637647a7289014f.jpg'),
(77, 292, 1, '458f16cfc8642d393c20807a7383d715.jpg'),
(78, 292, 2, 'c4e101759c68b71c0c00769e84720cc9.jpg'),
(79, 292, 3, '0ac856e3e186a74b39d14840fa688886.jpg'),
(80, 292, 4, '6db0b32f4b81618691fe8ceded3d8e8f.jpg'),
(81, 292, 5, '08ce2b5c0317812677a345e99b6017e2.jpg'),
(82, 293, 1, '8fe72c59c7b45c4bb360ecf086c2f5c8.jpg'),
(83, 293, 2, '4d691371e2c16aaff282e738f7cdae9b.jpg'),
(84, 293, 3, 'b93738c274366196d5808a94a591bcc1.jpg'),
(85, 294, 1, 'fe25c17bfd8487b33204bc2430c45ae2.jpg'),
(86, 295, 1, 'd5dfe605f3c179a3f0055413f8159958.jpg'),
(87, 297, 1, 'c9875d1c093a1f0e7b74bcfa23ff0a3b.jpg'),
(88, 297, 2, 'c961b8a6ac32fea1b35b796ac0b2e118.jpg'),
(89, 297, 3, '621ab7c60bf06ab251ff052a58767ecf.jpg'),
(90, 298, 1, 'aba15a29f39dcc6076d3603976889876.jpg'),
(91, 299, 1, 'cd66aee8c51e5609de9e1076a3fb5378.jpg'),
(92, 299, 2, 'a6f69d52cb363e8b360a499507005930.jpg'),
(93, 300, 1, '920847330831fbcffd01b9c2d30168e9.jpg'),
(94, 300, 2, '366880180c2e50fca1956d9e564c3087.jpg'),
(95, 301, 1, '3630f8d51316a826fec6a058048826c8.jpg'),
(98, 305, 1, '41fddee25c0c2c9455f344da0ec5f08c.jpg'),
(99, 305, 2, '6b1238a72e088653407ee6004cb8b382.jpg'),
(100, 305, 3, '88049fc53469acde3e198a9f236d9544.jpg'),
(101, 305, 4, '169aa93df75c35daf275851c2b30b1b6.jpg'),
(102, 305, 5, '6306077e3281257e5e6b01a9f9fc2154.jpg'),
(103, 304, 1, 'e8765723f13114c02bfec16281c5e8d8.jpg'),
(104, 304, 2, '8e6e1eea5e2053ded6f380600cdaa607.jpg'),
(105, 304, 3, '1d6d38b4d063e5ed283d05dedb7a8de5.jpg'),
(106, 304, 4, 'e4bd24e94fc45c7859e6065859f9ae09.jpg'),
(107, 304, 5, '806b218c8ed154fef27b308450676030.jpg'),
(108, 305, 6, 'b514db15cbca838e4b87350e033d9696.jpg'),
(109, 305, 7, 'e4c97e898de647f5bda59767142349ed.jpg'),
(110, 305, 8, 'b4d9317383a4a5efb6ccfc447d4ec0c5.jpg'),
(111, 305, 9, 'a4d4c95defb2e46910fcda10e6409196.jpg'),
(112, 305, 10, '3026a4c4103db6779878521043aa5413.jpg'),
(128, 311, 1, 'a14e23b66e335d85bf6993219563444b.jpg'),
(129, 311, 2, '07f4201acd8cb80495ce82b928234597.jpg'),
(130, 311, 3, 'fb9d5029349e6743c2a8995b4d8cb3b1.jpg'),
(131, 311, 4, '7786aff4cb4701ecabed6194f3608365.jpg'),
(132, 311, 5, '4bc740b385a69842c395aa0ff7124977.jpg'),
(133, 312, 1, '77c6179516c9d5c5f7087df3077096e9.jpg'),
(134, 313, 1, 'cae308cceab4923792e17a30d41a745c.jpg'),
(135, 313, 2, 'e3911ab053de3cea93bba758cfc0a91c.jpg'),
(136, 313, 3, '2b91b421cb636a7297241400ae1e8fd6.jpg'),
(138, 314, 1, '8b31c7d5aa16f997a6e758d600a721f7.jpg'),
(139, 315, 1, '577390cbeb17c2c88e0afb055a98bb8e.jpg'),
(140, 315, 2, '0daf30cd620a49d02cbb6738f636fa2e.jpg'),
(141, 315, 3, '3d00cb08e25bc3041ef5ff664aedcf82.jpg'),
(142, 315, 4, 'e08d76f1af6d1b37acd3264522fc9100.jpg'),
(143, 316, 1, 'ddb65d156e6b94645e15ee8a14856be6.jpg'),
(144, 318, 1, '295167a2373b7d1e93a9e196839b1269.jpg'),
(145, 318, 2, 'b36281e6fe978c11a621bbc865cd18be.jpg'),
(146, 318, 3, 'e22a98613e2a4c0191e1e3a03cc4e508.jpg'),
(147, 318, 4, '379cb2404d20ace0c91efc220fbd5a3f.jpg'),
(149, 320, 1, 'a4fd7a80d24d4acce182d6d6a5b9a524.jpg'),
(150, 321, 1, '3098bccae31823927a71aa99f620cd07.jpg'),
(152, 322, 1, '93fa750425b034f20aa417e59c0dc271.jpg'),
(153, 322, 2, 'ef7b7f503f3a458af1ac799c88abe920.jpg'),
(166, 325, 1, '43421029013b6e5e7fb703d6a9afab1d.jpg'),
(167, 306, 1, 'e6eb5457fc9fa310920d62805e35526b.jpg'),
(168, 307, 1, '860b0b1f15d343d10a2d113d04bd4b39.jpg'),
(169, 319, 1, '9b331122832b2f1d7cb9258117a56e12.jpg'),
(170, 319, 2, '132c609bbb29e18fb93788e82f50cb45.jpg'),
(171, 319, 3, 'cc86bb9c247bc6adb9f58fd1c6954bd8.jpg'),
(172, 319, 4, 'd69d66e29a0fc748698f7968e7686c40.jpg'),
(173, 319, 5, '794c9ffdee6ffd60ef02503dfe615291.jpg'),
(174, 308, 1, 'fe3eb024b06aed3e23a0c942d592b406.jpg'),
(175, 323, 1, '09a56b1b7fa51a2fedfc3bdec38e0fd3.jpg'),
(176, 323, 2, '833425f88e6bb96060b7c52509c609a9.jpg'),
(177, 323, 3, '9c2434210128a5d18fbc62d1991657f9.jpg'),
(178, 323, 4, '03382a12c4aa4ea078fc5bf9a48faa3b.jpg'),
(179, 323, 5, '235dafe298d3020353189d2f7f92c415.jpg'),
(180, 309, 1, '1162f8b1f75b74950e351321e92c275c.jpg'),
(181, 309, 2, '1be69739ebac2e740c2eb56740376b59.jpg'),
(182, 309, 3, '97f3aa3fa84a92b5ef813013dfa934d2.jpg'),
(183, 309, 4, '9335c1fb2149cd5e63685b1a6b5504d9.jpg'),
(184, 309, 5, 'a7030138a1ed94526c108548f2c8e9d0.jpg'),
(185, 309, 6, 'e2378e6e8655659dfcbebf1f866c3901.jpg'),
(186, 310, 1, 'c52cf6ae5c7497ceeab4bd3b33905a3b.jpg'),
(187, 310, 2, '3662a75c62b6ff2237d61c3fefac4d0a.jpg'),
(188, 310, 3, 'a061bdee92b3d0ac0354d6224760af6d.jpg'),
(189, 310, 4, '8704d8faf110cf54c90e1a649ea8214c.jpg'),
(190, 310, 5, '83a3e7c8bb8a817b1de3aa10a90137dc.jpg'),
(191, 310, 6, '4912ad2ebf5a42e13cc055b8f45693bf.jpg'),
(192, 326, 1, '4cbf21b41aa4c2c3c16f07221c78c7be.jpg'),
(193, 326, 2, '0a4226e9797377a6e0dc9bb5046cd7ab.jpg'),
(194, 326, 3, '76b4abd67df610e1b614c9bff8813161.jpg'),
(195, 326, 4, '801a1c476a8d38a10009d5466a7953bb.jpg'),
(196, 326, 5, '5feb58d0a0cc01222267d604e822bdc0.jpg'),
(197, 326, 6, 'ed58f11bdaae1671038606cb54fc0d62.jpg'),
(198, 327, 1, '9e4eaa2d1b09f9eb918205d6189e4be2.jpg'),
(199, 328, 1, '1845e9b1c03ee710c2f97b55eb4d651d.jpg'),
(200, 329, 1, '54b06f2f61679873c303df696fb7cd33.jpg'),
(201, 330, 1, 'b1f554a92a27ea05866c5470ee7076c0.jpg'),
(202, 330, 2, 'a19f4820598edfefe7221d35fe8852ea.jpg'),
(203, 330, 3, '8440845d4f8037a394c04d331f43fdf0.jpg'),
(204, 330, 4, '442bb559005fc671c67efe76f2f67046.jpg'),
(205, 330, 5, '158a6af8dcad61d2d68ec8e48322da4f.jpg'),
(206, 330, 6, '633e1ec159785e7cf2d092b285a2b607.jpg'),
(207, 331, 1, 'b1facb81d9786e8586051c5433744fde.jpg'),
(208, 331, 2, 'c31fc94cf727d10991daa0421d577baf.jpg'),
(209, 331, 3, '8769110f5faaceec1ac9f4da6bec4558.jpg'),
(210, 331, 4, '7d5ec27df17bc8cc68dc2a9d5955f029.jpg'),
(211, 331, 5, 'dbad923c2e6ccc15a083f25884bd9f0c.jpg'),
(212, 331, 6, 'dfb40aef7a408f4f72da164b220ef2cc.jpg'),
(213, 331, 7, '2b38635c5b0ebf9430b85f6a233b76c1.jpg'),
(214, 332, 1, '25874878afb04939985d51376bcf5056.jpg'),
(215, 333, 1, 'f04ed9a87d83643761f23600c711668a.jpg'),
(216, 334, 1, '611b9a7401565706e5975e06463391f8.jpg'),
(217, 335, 1, '197064ddf655c7991c0d1293bab698d5.jpg'),
(218, 336, 1, '1751b085b5d8ff757de1613eb66bc754.jpg'),
(219, 336, 2, '9ab7f84082616d6137c05447219caee0.jpg'),
(220, 336, 3, 'bb1354a9e422e44d101055edf5e035b3.jpg'),
(221, 337, 1, 'd9ad86fb4a55c4361dccb5026f13ea3b.jpg'),
(222, 337, 2, '6c66cc77cc35d186ab98c0b55e077c6f.jpg'),
(223, 337, 3, '1a372bae71bcb99fa286946abee390c1.jpg'),
(224, 338, 1, '8b3b6f7e5b43e939f9e85c70c2bad0e0.jpg'),
(225, 339, 1, 'f777919891b393216e9a545b5b6a7940.jpg'),
(226, 340, 1, '43f0fa3304326f64435d5161d9a145fa.jpg'),
(227, 341, 1, '119f7873ff12c8ca9ec601fac84284c6.jpg'),
(228, 342, 1, '14df07f8d7e153465c4b848c06753c5c.jpg'),
(229, 343, 1, '7e95b237e121c38afb51e2623b3c3e30.jpg'),
(230, 344, 1, '6b19279d142870dcec506b4f0f19330e.jpg'),
(231, 345, 1, '304ffc93210403af9d4318fad40ff526.jpg'),
(232, 345, 2, 'eb3f5126117ed5c64d5e2f6f71153dcf.jpg'),
(233, 345, 3, '559e69ae89bc6335b3b0fb429c86017c.jpg'),
(234, 346, 1, '603104ad0d75c2e78564b0c9f62198b8.jpg'),
(235, 346, 2, '2eeea374852b48cd3b24da49e79ef442.jpg'),
(236, 347, 1, 'b0180b0cd16bb109a5c48cf61cd3f92a.jpg'),
(237, 347, 2, '316a710285a8ce523cd59e97fa1878bb.jpg'),
(238, 347, 3, '720c5da0bdf88ad5abedd06e1bff9fff.jpg'),
(239, 347, 4, '39b0f576bf856ddab91c9344bad1c58c.jpg'),
(240, 347, 5, 'f158325589fea9a9128c70ff26757d67.jpg'),
(241, 347, 6, '2b447a59f675984316f1549508c30e47.jpg'),
(242, 347, 7, 'ff5d8afdabeef542479fbe576f2a2a3f.jpg'),
(243, 348, 1, 'cf8f22d8fc1d7bbc66a8fc7fe51e0a01.jpg'),
(244, 349, 1, '0a7a480f397d635a522e2e9c6bfd2c40.jpg'),
(245, 350, 1, 'd6bfde90c0b3da32f3c79b615ad98270.jpg'),
(246, 351, 1, '02aac4cc714d75e5d6dc27b710ff5dc0.jpg'),
(247, 352, 1, '4667d495a5b077393142abc72bded6e1.jpg'),
(248, 353, 1, 'd73308a8fd65de1fc6422c7fa1ca6588.jpg'),
(249, 354, 1, 'adb815b0c757f8a43e797eb4f00320f5.jpg'),
(250, 355, 1, '5dfa590e9d7c25c0bd53f66659ec3f3c.jpg'),
(251, 355, 2, 'b96c3007d38a45e2cfd00e34589550cc.jpg'),
(252, 355, 3, '7aa08f7a4841e516fb6b38f861619e13.jpg'),
(253, 356, 1, '70b4fed904e5fb98b2f7ccfebaa89426.jpg'),
(254, 356, 2, '49d5fd86213cf815bc3b2c8c02dac5ef.jpg'),
(255, 356, 3, 'da3354da03e96afbb2df47187adf5b5e.jpg'),
(256, 357, 1, '0708a0eb6440c09d85f3d0a6a2ace026.jpg'),
(257, 358, 1, 'f3aace3d18f789c6fdc61257f709920a.jpg'),
(258, 359, 1, '4aad7be36c37c8d3c82faa09e501cdff.jpg'),
(259, 359, 2, '89ad666f50b6790d99b788b997cc6d4f.jpg'),
(260, 359, 3, '6a5b1a424073bb7724920f06c9205228.jpg'),
(261, 359, 4, '0506360f566b3ede54a5d6959c2ec184.jpg'),
(262, 359, 5, '3e8e31f2db4514d272338251810a947a.jpg'),
(263, 360, 1, 'a26d67e50a51989d8ad1ef68e4439247.jpg'),
(264, 360, 2, 'ec0b031e249834504cf92f63579b1cba.jpg'),
(265, 360, 3, 'f8a604b313baa717c161963cb359d5da.jpg'),
(266, 360, 4, '01e71dc3c575f133a906810d353d2cbf.jpg'),
(267, 360, 5, '532999532769e0ba7d45717a6f8a9e5e.jpg'),
(268, 360, 6, '5f572d15e2d14ce7a7599747385263a6.jpg'),
(269, 360, 7, '188af9c1f4acb4f11dd4a0db11295765.jpg'),
(270, 361, 1, '4278284d4d721be71f93ccaabbb75e2d.jpg'),
(271, 361, 2, 'a55111e4f1d1184e004c5233fd0e6130.jpg'),
(272, 362, 1, 'd3a632bbd3237d49c2dea1c74c4391ee.jpg'),
(273, 363, 1, 'c245b1828bb2e66db9f95317400ed595.jpg'),
(274, 364, 1, 'a3f9b5ea2b03412b9ee30f761dc93a12.jpg'),
(275, 365, 1, 'a4b1550b7e08a628a9c5a585850cb711.jpg'),
(276, 365, 2, '4695ba8826f9bddb10192c7fb2b6f953.jpg'),
(277, 365, 3, '4200e2998300046a91a978f5fc511761.jpg'),
(278, 365, 4, 'c621adc84104cc0b0bb95795608d062b.jpg'),
(279, 366, 1, '54dc9be3c04a60a7ee0c36baf6416607.jpg'),
(280, 367, 1, 'f0163400760953bbc033d26888ed359e.jpg'),
(281, 368, 1, 'b619ad0d59f087eb12bc227445572e9c.jpg'),
(282, 369, 1, '171c2cc3a68de1a8f3672e1e1b2dc211.jpg'),
(283, 369, 2, 'a873f19a37ef557723331bfda649a041.jpg'),
(284, 370, 1, '314b48a7f2c4f6df45e4a21739d68cbb.jpg'),
(285, 370, 2, '1616dc480ee2a7440f5e17eb4104ee83.jpg'),
(286, 370, 3, '2fb1551602705e8b098867752c454ef1.jpg'),
(287, 370, 4, 'e20d33081012823c427b0c0d9cf11cc0.jpg'),
(288, 370, 5, 'ef2b0697b4a0bd3e6aa1a6ec77ed3d6e.jpg'),
(289, 370, 6, '3fd966e7aabf42dd6112aec285273b33.jpg'),
(290, 371, 1, 'dba24c261bcf501544d3cc1d4ab0051c.jpg'),
(291, 371, 2, 'e19ba474672578c122fc029e818496c3.jpg'),
(292, 371, 3, 'dd9fa342a0aa340942e8728a6b4fed6a.jpg'),
(293, 371, 4, 'ef9a8a5a972bab1c73f0fd0f68d4f393.jpg'),
(294, 371, 5, '082d31435b28ea0d28662adbc5bea11b.jpg'),
(295, 372, 1, '72a1d6ed5e474b2f811116a27c2074c6.jpg'),
(296, 373, 1, '0d87340993763df9ccf4a4337805f2fa.jpg'),
(297, 373, 2, '2e86919ac22a029598fd07eb754da645.jpg'),
(298, 373, 3, 'd6d31bd4ed9a2064ab93dcb286bffe8f.jpg'),
(299, 373, 4, '499b4ef4b356077194ed8ee93cbb2ede.jpg'),
(300, 373, 5, 'c81f54dcc74562aee693b146f0022773.jpg'),
(301, 373, 6, '3f768c5c4d459f1786bcaa6670368f6e.jpg'),
(302, 373, 7, 'a7f4fdb04023d9aeacb5d9f919788520.jpg'),
(303, 373, 8, 'a952dbe5e712b85e5a329a3cb76e55a1.jpg'),
(304, 373, 9, 'b8ff64af2ca561a227e3778b97efcd23.jpg'),
(305, 373, 10, 'c1282d66b29ff66774b1c313c9620dfa.jpg'),
(306, 373, 11, '205aacc46bd3673cafdc961233f5fc1b.jpg'),
(307, 373, 12, 'a90aec4e3a7985fbb1d1f35327edc715.jpg'),
(308, 373, 13, '51ed8449feacaebff80d94383b0f838b.jpg'),
(309, 373, 14, '886de7eea565b329dacb0ad6e87ee7fd.jpg'),
(310, 374, 1, '0ee90e695d0e6abcaae6bf02e1feab75.jpg'),
(311, 374, 2, 'f243ede143a35583919ef0b112ae72af.jpg'),
(312, 374, 3, '97774994ea9289e67d08677b5b6ba3d6.jpg'),
(313, 374, 4, '64e37037bbe76bae658edb42a700d4ab.jpg'),
(314, 375, 1, '406f9af25e951dd7465d1d9a0ae603f0.jpg'),
(315, 376, 1, '13a9ddd470027257df4d1d17b7dcaabe.jpg'),
(316, 376, 2, '09c0923cad0dd89334f406f838f4173d.jpg'),
(317, 376, 3, 'e94a6ae2825c96330a5278d44606895f.jpg'),
(318, 377, 1, 'e77e38f2860e1eac8c28ee89885a602f.jpg'),
(319, 378, 1, 'c265ab4aebe710a0aa6ab3aa4f521471.jpg'),
(320, 378, 2, '91e151c5198e4d7d148095aabcb59c1b.jpg'),
(321, 378, 3, 'a8b9aa372d19d432a6889affdef77b1e.jpg'),
(322, 378, 4, '19e03c787ebb469ca0522ebfbe2d3661.jpg'),
(323, 378, 5, 'fdd07ef581aaa771a0b477bf7fd654af.jpg'),
(324, 379, 1, '35da1f6837f90206c255cad3910d7e74.jpg'),
(325, 379, 2, 'ab3d8782bd8c84ec45471ca9d1d16b7b.jpg'),
(326, 380, 1, '11a7b39d26f7d7a09685c892d5e20b4f.jpg'),
(327, 380, 2, '69f4db376841e192d75e25520b78d942.jpg'),
(328, 380, 3, '5b9932801a225f44c16a89a2b88f7c4f.jpg'),
(329, 380, 4, '9d004ff6ea5a3011efaa11d32a695f9c.jpg'),
(330, 381, 1, '93940585d66bce5638f370489922781c.jpg'),
(331, 382, 1, 'a629555f896b1fcca948a783161c405c.jpg'),
(332, 382, 2, '9c834b0b81dbd356e26ca2a658b52fcf.jpg'),
(333, 383, 1, 'ea1254927521eeb791701cc6439b4c73.jpg'),
(334, 383, 2, 'db28f13814c0f9798da58c257fff4e92.jpg'),
(335, 383, 3, 'fca426848119263ca1f5f049f0be54ff.jpg'),
(336, 384, 1, 'f4bfe18e97098bb6bdf306ec1c570655.jpg'),
(337, 384, 2, '8ee09d8ce4b310cb5990d911dbf3813c.jpg'),
(338, 384, 3, '527d6ded4d298f16d6a35662755cdb9f.jpg'),
(339, 384, 4, 'c36455b05b15e79e44897ef7615c437e.jpg'),
(340, 384, 5, '198ba1403517bc12e81a18cb8f3d93a4.jpg'),
(341, 384, 6, 'f5cf70b94c2659d8a83c4d89bf29f0db.jpg'),
(342, 384, 7, '558e7b7930a1c2c650b85440b541dadd.jpg'),
(343, 384, 8, 'e8e8b5971474a41ab93931a8be0efff7.jpg'),
(344, 384, 9, '784517e3f7318ef4fc9cc3f1d28a98ab.jpg'),
(345, 384, 10, 'bf45d8e432aebf9a151eb109e6095501.jpg'),
(346, 384, 11, '43938857c0036f5edf42186f6a0a4ca0.jpg'),
(347, 385, 1, 'bba05c47d0ab8d66d2c3c788ebc93385.jpg'),
(348, 386, 1, 'dbf6b08eac0b427257f85ee5cfe01d85.jpg'),
(349, 386, 2, '262d5d3a1d4a1a13b614819e6631ecc1.jpg'),
(350, 386, 3, 'a503f4fb4c1497ba1303b55207bac79e.jpg'),
(351, 387, 1, '142cd472f8faa3424a5f6c6d4f147d03.jpg'),
(352, 387, 2, 'b9c721e99bd932a1e6e78c1e3e52eeee.jpg'),
(353, 387, 3, 'ef9a38098827db58bd78eeca0db35801.jpg'),
(354, 387, 4, '34077e0d141ba3b521234e39152bb8fe.jpg'),
(355, 387, 5, '08465f0da1511308a045e9e2141adb51.jpg'),
(356, 389, 1, 'bc06b7785190ac9be02f24d847dcbf2c.jpg'),
(357, 390, 1, '6077dd19fed2a10a2763824f53fc55fd.jpg'),
(358, 391, 1, 'd21ee8d09bae90b1cd8aa46520079729.jpg'),
(359, 392, 1, 'd4f06c4135e856e01d455d9059ef1c9a.jpg'),
(360, 393, 1, 'ff155bf5034f03e36623e61789cd5970.jpg'),
(361, 394, 1, '446fe6cdfa17c687814991664e632dce.jpg'),
(362, 394, 2, 'd879fb741015ff574cd93537f758fd40.jpg'),
(363, 394, 3, '94e11ac874f5b166080578eb0d477769.jpg'),
(364, 394, 4, '3924fb47a10b338d9cba15cb09759064.jpg'),
(365, 395, 1, 'cdbff609ce8375c01f6159ad9466d241.jpg'),
(366, 395, 2, 'd71cfdbd48e5f56f7e0113e365c61d0d.jpg'),
(367, 395, 3, '81390bc42b36db0993794e2f90a785f4.jpg'),
(368, 395, 4, 'fdbed4c33cc69afae3bb6bdb0cf1a8ff.jpg'),
(369, 395, 5, '861a78930465ac77048ab9907cd4af14.jpg'),
(370, 395, 6, '805c3f14d6fc476cbe3e77fee15cc431.jpg'),
(371, 395, 7, 'a3a07c74dfddc2081fdff04ad056bcd5.jpg'),
(372, 395, 8, '2b6113647553e7a3ec0aa0e751af94a2.jpg'),
(373, 395, 9, '2f4a5230ec725ed41127e815d0785ac0.jpg'),
(374, 395, 10, '2b80b9a9cc91ce7feb47b0f7931fcc49.jpg'),
(375, 395, 11, 'a9408de2c0e8eec3d8fdd2fc9bf51aa8.jpg'),
(376, 396, 1, 'a290f91a3aa0ecac0bb85172c4a7260c.jpg'),
(377, 396, 2, '65d138ff413b50adae654d8bd8c76251.jpg'),
(378, 397, 1, 'cf3fa9132555b06a32a3d9a232e1710c.jpg'),
(379, 398, 1, '07732b53ca10028783d351a442d5bcb4.jpg'),
(380, 399, 1, 'f6b40b6b1f721e76ef4f3e0690771462.jpg'),
(381, 400, 1, '2bff47daa91dfae48af67d3810268193.jpg'),
(382, 400, 2, 'eba45e6092aef1e78583a6f4047f75b7.jpg'),
(383, 400, 3, 'c38d6e25c9d578c7e539c32ebfbd11bf.jpg'),
(384, 400, 4, 'd676297d8efb74a779ef215677daba22.jpg'),
(385, 400, 5, '0c44dda263a574cd438c54f3930c2267.jpg'),
(386, 400, 6, '38c842197147effd4dac3ac9558cb10c.jpg'),
(387, 400, 7, 'dbd7a7ac773f3e713ccb63a8258675d5.jpg'),
(388, 400, 8, 'dcd62a8d1e585d8baf5b16a5d44a7345.jpg'),
(389, 400, 9, 'ea363db06b847da4dce92946d070bb55.jpg'),
(390, 400, 10, '527704444f177e57638a40e92aa2344e.jpg'),
(391, 400, 11, '98f4917274a7ffadbe7979213bbcf6de.jpg'),
(392, 400, 12, '16597e8a08e0143e9bf6c85ae91fd58d.jpg'),
(393, 401, 1, 'b1275fc6bec875b7981957923d9a254f.jpg'),
(394, 401, 2, '5ed98de87822138d1fbbaed2c1c77925.jpg'),
(395, 402, 1, '81714b2c388c212b42ece4ef2f8f9ff5.jpg'),
(396, 403, 1, '4ee43c5227b1893cceb97fc733a1f639.jpg'),
(397, 403, 2, '81c1c48e1a753d244103bfb0d76614d4.jpg'),
(398, 403, 3, 'c64367b9168556f292abc92a2b19e78f.jpg'),
(399, 404, 1, '2139d3ea8eee0e01695873ffbe1abef2.jpg'),
(400, 405, 1, '5bcb9ef2f125702aff83c28bdad822de.jpg'),
(401, 405, 2, '6c74268c7dd1baef2a8b8d50d98e02ee.jpg'),
(402, 405, 3, '637790ec97766f7cb46ccc3ae882ecb0.jpg'),
(403, 405, 4, '609fa65875daed88863798788537517d.jpg'),
(404, 405, 5, 'e49c00058e0a79e3329f2045fd460831.jpg'),
(405, 406, 1, '876a33caec5cd389f3850d28dde89d5a.jpg'),
(406, 407, 1, '157aae089b7f457e0bd4948da91a6a5e.jpg'),
(407, 407, 2, 'ee3db3afe43315244cbed3aa2a38f200.jpg'),
(408, 407, 3, '822045a447c12219054f0560dd5810f4.jpg'),
(409, 408, 1, 'dc6591f65d8ff18a7d131dc59a460896.jpg'),
(410, 408, 2, '10bb02a1b5febdd5d0b1c781367037ba.jpg'),
(411, 408, 3, '6d0a7f4806d6c2f5265cf8484f836cdc.jpg'),
(412, 408, 4, '21ce469b441ab20767fc7404965f3d3c.jpg'),
(413, 409, 1, '1bf1627f1787c11b7660c58a0970be4f.jpg'),
(414, 410, 1, '59611121212ed73a9c2b4402ae51208b.jpg'),
(415, 411, 1, '5e4205fadd4fa04875d9b3de89c45743.jpg'),
(416, 411, 2, '5432414a8e52a1d44f7f4236cb13f98f.jpg'),
(417, 411, 3, '4a32fe4beb6670ddb0ce29809986ef66.jpg'),
(418, 411, 4, '1d6d3bc6726ebe6da237a4a61c45d85e.jpg'),
(419, 412, 1, 'a8d82f1c8916ac20ddd4b843c1e84995.jpg'),
(420, 413, 1, 'd7c7fb3c72393dbf6a051aa19bbbcee8.jpg'),
(421, 414, 1, '3eddc1b4bbd71f8584b3d1e54a2afe56.jpg'),
(422, 414, 2, '4317900849c001d4188c09472a6b6b8a.jpg'),
(423, 414, 3, 'e4d368092fc579de6c434ed0238e2c03.jpg'),
(424, 414, 4, '8b8e6a59460c60df49b11edd651e8396.jpg'),
(425, 414, 5, '3ed85d77fa292735aa3997371c19dc39.jpg'),
(426, 414, 6, '7ac8967745725e45b45d97593f92abc2.jpg'),
(427, 414, 7, 'fd99b5cbe15bfb58deb06860bfcca7aa.jpg'),
(428, 414, 8, '88bd3a0455bfff309b6946e772d9345a.jpg'),
(429, 415, 1, '670d40e852d308bd7566336314e2c0c9.jpg'),
(430, 416, 1, '30b560d2deccd3713400f5578fecc6a6.jpg'),
(431, 417, 1, '96d9b1944a09c3f136e8d50bf9c1f5f5.jpg'),
(432, 417, 2, 'dd46493d543b55047638d02c479f8e01.jpg'),
(433, 417, 3, 'a21bace9f8f00736e267faf10d66ff8a.jpg'),
(434, 417, 4, '57749afbbaba58138c986857bde640c8.jpg'),
(435, 417, 5, '36352025e2a8fa9bb80da6b7f1128e83.jpg'),
(437, 418, 1, 'dc7d78c5d8225808a7afa0d08a75e56f.jpg'),
(438, 419, 1, '5855ac12f9fe0abd05e4d6b2bc6c4176.jpg'),
(439, 420, 1, '1a75143b353042b57b45f11648ecaa26.jpg'),
(440, 420, 2, '0f8c87f93cea6fb4024ea56b54b9b5b5.jpg'),
(441, 420, 3, '37bfef6140eb7b088160f2b4cd4c8d1d.jpg'),
(442, 420, 4, 'aebd7a5b9c12efcd0561ad63caebb8fa.jpg'),
(443, 420, 5, '081c5f225ddf4acca20841f234ac1fe8.jpg'),
(444, 421, 1, '2d04f3b6adc41c38762025f1c0104927.jpg'),
(445, 422, 1, '4de03fba97c13cb2e92a4dbdc3f5c6cd.jpg'),
(446, 422, 2, '166f9c3be8e331b8e3858d21b4f1df67.jpg'),
(447, 422, 3, '7cb947cb03255f5559187435ea938cff.jpg'),
(448, 422, 4, 'c56cd760fed92be4f017892f991ea699.jpg'),
(449, 422, 5, 'f6d286a96f79616dafbc6eb410510855.jpg'),
(450, 422, 6, 'ae955f614af6a0961ee59d0f68a94514.jpg'),
(451, 422, 7, '549b9b0929820f47ce5f30b19cf038ee.jpg'),
(452, 422, 8, '6d600f36ead5a4e7ed9c1a36129e2df6.jpg'),
(453, 423, 1, '0813fb90e3d7cf08353ddaf06916ef34.jpg'),
(454, 423, 2, '49c0114c5a79da80dd78165b37727d86.jpg'),
(455, 423, 3, 'dc222d51d58c46bf06403d0141723030.jpg'),
(456, 423, 4, '5d28e41977e822540d0a089347e6f95d.jpg'),
(457, 424, 1, '52fb1b5a6e099053900bb0fd558f2f63.jpg'),
(458, 424, 2, 'e2192d900fd61d4f45afe4b517fb6502.jpg'),
(459, 424, 3, 'a187410a9f524e77664a7d8700c27cf5.jpg'),
(460, 424, 4, '6b9b8c742e05655ce1b2be83de55d52b.jpg'),
(461, 425, 1, 'af8269add6bad32410fd65984fa716c1.jpg'),
(462, 425, 2, '4e4017f0536396b81647424b242e71b3.jpg'),
(463, 426, 1, 'a4a3465bbacf9c7f6fae3f0e598ff1e3.jpg'),
(464, 426, 2, '79f5cd2237e2416c4e86c1ba7d67f962.jpg'),
(465, 427, 1, '047740ee242c0db00896029601ffd3bd.jpg'),
(466, 427, 2, '43735798879ea93b6967b7df6bff2036.jpg'),
(467, 428, 1, '1d163bc0c38ba4ebe5266cefcfd12822.jpg'),
(468, 428, 2, '1c7f927622e08651864777259d56ce95.jpg'),
(469, 428, 3, '1d33e48ce43dacba531273073ef6d6ac.jpg'),
(470, 429, 1, 'c30146f03a09a0eae36935f21408408b.jpg'),
(471, 429, 2, '6ce0fcbaab877bc7ba714557e59f78ab.jpg'),
(472, 430, 1, '205a877eb9caf8ebb6022d463c33abd9.jpg'),
(473, 430, 2, '16fc45bbcfa6e352848abf5a97c1ede4.jpg'),
(474, 430, 3, '8b028803cdf3fdee86a11a4e1d36622a.jpg'),
(475, 431, 1, '222c9353dc4c9d4b572661a3bf27d01a.jpg'),
(476, 431, 2, '88ab7b0ea9fb0e73a1b126cd956afeb2.jpg'),
(477, 431, 3, 'dcfe7ec16ca3e7ee78fd8907ba14810e.jpg'),
(478, 431, 4, '9952c71c9274bf3e7fcada302992c8c5.jpg'),
(479, 431, 5, 'ab49cfe6909ac8f03f1a16097294c4fa.jpg'),
(480, 432, 1, 'f5c50b378c89cab8999ae44407d5762a.jpg'),
(481, 432, 2, '8cefe2f6479c5c844327b54d8971b23e.jpg'),
(482, 432, 3, 'c805ca1bb94d1519e49fa9afd8a1c55a.jpg'),
(483, 433, 1, 'd2d41a129e4e6ed74798cec5f7ccf6eb.jpg'),
(484, 433, 2, '659efc6f02b525fefd9248d15e1014ef.jpg'),
(485, 433, 3, '3415e0480e8961fadf1d41394c3cf6e1.jpg'),
(486, 433, 4, '198943faa546dfe4a76f686a62f050b7.jpg'),
(487, 433, 5, '22d160eba07a1f670ebd59df2a3b3492.jpg'),
(488, 434, 1, '08f23d8927894b3bee17cbebdf34e316.jpg'),
(489, 434, 2, '5d27b3600507ad8ccf7eee3691a3a5c6.jpg'),
(490, 434, 3, 'bad55cdcd8e864cc24014ed3501233cf.jpg'),
(491, 435, 1, '2f32be02bba21adfe5b40bcfc4eb3814.jpg'),
(492, 435, 2, 'e9bcfcdb71601b4e8cc19e66a0d7be51.jpg'),
(493, 435, 3, 'c689301ef3f8b24b260565ac7e437ed7.jpg'),
(494, 435, 4, '1e1c23178906c917a0aee0440d14dc29.jpg'),
(495, 435, 5, 'ece9d6257e3ec50af77f576d0a373d8b.jpg'),
(496, 436, 1, 'f69dcd6dfdd48a1e6be484de0c6ba0c5.jpg'),
(497, 436, 2, '5f328fb0129c9fc29cc2b77629495189.jpg'),
(498, 436, 3, 'aad779922613eeb91697703d9450fec9.jpg'),
(499, 437, 1, '3d79f63aee7ee677a7d7a12cfa79f576.jpg'),
(500, 438, 1, '802f924c28f367382aa3423e6b38a3a6.jpg'),
(501, 438, 2, '74408f14eddc3a948613dfc30e2a9bf2.jpg'),
(502, 439, 1, '7a2ad5d15afecf127307935d74f6c784.jpg'),
(503, 440, 1, 'd12fec2d03c1f0c052ded8acfd5dbbcc.jpg'),
(504, 440, 2, '73787d4d8494f17a9db6a57db6f110f5.jpg'),
(505, 440, 3, 'b61fae6dbe5e037e7552c96fcf0c81b9.jpg'),
(506, 440, 4, 'c9c2acaadfcbc9192f646884a35cef2c.jpg'),
(507, 441, 1, '708d27f05ba6f485d0148762ee738493.jpg'),
(508, 442, 1, 'e9c12e9461791b261f0249e669eadc6a.jpg'),
(509, 442, 2, '74ed92c5e65346472a4b0f1e75fcf755.jpg'),
(510, 442, 3, 'b8397d10ab93b654ab7d6a2844b1da7a.jpg'),
(511, 442, 4, 'f82758932d1cee873cba55e8e627f717.jpg'),
(512, 442, 5, '1b02192518b795cfc63e429cefd88161.jpg'),
(513, 443, 1, '6a4e2c8f9c401093ecffb84626cd26d0.jpg'),
(514, 443, 2, '838270c52d84aea82f58f52698fd80ae.jpg'),
(515, 443, 3, 'bb47cbd86f3d7e55af6597d22ded1fe8.jpg'),
(516, 443, 4, '97c9aaffbc0f424e848832942caf03d1.jpg'),
(517, 444, 1, '1fae3af6e59409cf523944e0df471893.jpg'),
(518, 445, 1, 'f85256a7cce7148506ab1da418e69fbb.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `login` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `blocked` tinyint(1) DEFAULT NULL,
  `permissions` varchar(255) DEFAULT NULL,
  `messenger` tinyint(1) NOT NULL DEFAULT '0',
  `admin` tinyint(1) NOT NULL DEFAULT '0',
  `news` tinyint(1) NOT NULL DEFAULT '0',
  `docs` tinyint(1) NOT NULL DEFAULT '0',
  `people` tinyint(1) NOT NULL DEFAULT '0',
  `tokens` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `password`, `full_name`, `blocked`, `permissions`, `messenger`, `admin`, `news`, `docs`, `people`, `tokens`) VALUES
(2, 'usernews', '$2y$10$GC6LY/sW8GAeqetFxHnMmORh3dGm8M91qm8Bcxlb.VR3jswsbnQ8i', 'Редактор Новостей', 0, 'news;messenger', 1, 0, 1, 0, 0, 0),
(4, 'admin', '$2y$10$gasT8RfJNYgnlXQrlvQq5uEBr5TXPyC/5Kvf64nvprGqt07RzI41O', 'Администратор ТЦСОН', 0, 'admin;news;docs;people;messenger', 1, 1, 1, 1, 1, 0),
(5, 'admin_maks', '$2y$10$NHw5ORf5pkNgIMfmdYXxUOGxjs2.uS79pM3WQCBNnWx5eqtqSw8qC', 'Болонин Максим Петрович', 0, 'admin;news;docs;people;messenger;tokens', 1, 1, 1, 1, 1, 1);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `messenger_api_tokens`
--
ALTER TABLE `messenger_api_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_token_hash` (`token_hash`);

--
-- Индексы таблицы `messenger_attachments`
--
ALTER TABLE `messenger_attachments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_attachment_uuid` (`attachment_uuid`),
  ADD KEY `idx_message_created_at` (`message_id`,`created_at`);

--
-- Индексы таблицы `messenger_audit_log`
--
ALTER TABLE `messenger_audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_entity_created_at` (`entity_type`,`entity_uuid`,`created_at`),
  ADD KEY `idx_actor_created_at` (`actor_side`,`actor_user_id`,`created_at`);

--
-- Индексы таблицы `messenger_central_client_state`
--
ALTER TABLE `messenger_central_client_state`
  ADD PRIMARY KEY (`state_key`);

--
-- Индексы таблицы `messenger_chats`
--
ALTER TABLE `messenger_chats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_chat_uuid` (`chat_uuid`),
  ADD UNIQUE KEY `uniq_chat_no` (`chat_no`),
  ADD KEY `idx_status_last_activity` (`status`,`last_activity_at`),
  ADD KEY `idx_deleted_last_activity` (`is_deleted`,`last_activity_at`),
  ADD KEY `idx_last_activity` (`last_activity_at`);

--
-- Индексы таблицы `messenger_chat_participants`
--
ALTER TABLE `messenger_chat_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_chat_user` (`chat_id`,`user_id`),
  ADD KEY `idx_chat_active` (`chat_id`,`is_active`),
  ADD KEY `idx_user_active_updated` (`user_id`,`is_active`,`updated_at`);

--
-- Индексы таблицы `messenger_events`
--
ALTER TABLE `messenger_events`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_event_uuid` (`event_uuid`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Индексы таблицы `messenger_external_requests`
--
ALTER TABLE `messenger_external_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_request_endpoint` (`request_id`,`endpoint_name`);

--
-- Индексы таблицы `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_message_uuid` (`message_uuid`),
  ADD KEY `idx_chat_created_at` (`chat_id`,`created_at`),
  ADD KEY `idx_chat_updated_at` (`chat_id`,`updated_at`);
ALTER TABLE `messenger_messages` ADD FULLTEXT KEY `ft_body_text` (`body_text`);

--
-- Индексы таблицы `messenger_message_edits`
--
ALTER TABLE `messenger_message_edits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_message_version` (`message_id`,`version_no`);

--
-- Индексы таблицы `messenger_reads`
--
ALTER TABLE `messenger_reads`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_chat_reader` (`chat_id`,`reader_key`),
  ADD KEY `idx_chat_side_read_event` (`chat_id`,`side`,`last_read_event_id`),
  ADD KEY `idx_user_last_read_at` (`user_id`,`last_read_at`);

--
-- Индексы таблицы `messenger_settings`
--
ALTER TABLE `messenger_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_site_uuid` (`site_uuid`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `news_slug_unique` (`slug`);

--
-- Индексы таблицы `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `news_id` (`news_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`login`),
  ADD UNIQUE KEY `unique_full_name` (`full_name`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `messenger_api_tokens`
--
ALTER TABLE `messenger_api_tokens`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `messenger_attachments`
--
ALTER TABLE `messenger_attachments`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `messenger_audit_log`
--
ALTER TABLE `messenger_audit_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `messenger_chats`
--
ALTER TABLE `messenger_chats`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `messenger_chat_participants`
--
ALTER TABLE `messenger_chat_participants`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `messenger_events`
--
ALTER TABLE `messenger_events`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `messenger_external_requests`
--
ALTER TABLE `messenger_external_requests`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `messenger_message_edits`
--
ALTER TABLE `messenger_message_edits`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `messenger_reads`
--
ALTER TABLE `messenger_reads`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `messenger_settings`
--
ALTER TABLE `messenger_settings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=446;

--
-- AUTO_INCREMENT для таблицы `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=519;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `photos`
--
ALTER TABLE `photos`
  ADD CONSTRAINT `photos_ibfk_1` FOREIGN KEY (`news_id`) REFERENCES `news` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
