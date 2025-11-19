-- Migration: add foto_perfil column to donos
ALTER TABLE donos ADD COLUMN foto_perfil VARCHAR(255) NULL AFTER utilizador_id;