-- Safe migration to add foto_perfil to donos if missing
ALTER TABLE donos ADD COLUMN foto_perfil VARCHAR(255) NULL;