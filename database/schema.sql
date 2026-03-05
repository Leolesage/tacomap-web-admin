CREATE DATABASE IF NOT EXISTS tacomap_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE tacomap_db;

-- Ce web-admin consomme l'API tacomap et n'ecrit pas en base locale.
-- Le schema applicatif se trouve dans le dossier API (database/schema.sql)
