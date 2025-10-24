CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- tabela de locais
CREATE TABLE IF NOT EXISTS locals (
    id SERIAL PRIMARY KEY,
    local TEXT NOT NULL,
    status SMALLINT NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT NOW ()
);

CREATE INDEX IF NOT EXISTS idx_locals_status ON locals (status);

CREATE INDEX IF NOT EXISTS idx_locals_local ON locals (local);

-- tabela de leads (registros_visitas)
CREATE TABLE IF NOT EXISTS registros_visitas (
    id BIGSERIAL PRIMARY KEY,
    local_id INTEGER NOT NULL REFERENCES locals (id) ON DELETE CASCADE,
    fullname TEXT NOT NULL,
    email TEXT NOT NULL,
    whatsapp TEXT NOT NULL,
    course TEXT NOT NULL,
    terms SMALLINT NOT NULL DEFAULT 1,
    chosen SMALLINT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT NOW ()
);

-- unicidade para evitar duplicatas
CREATE UNIQUE
INDEX IF NOT EXISTS unique_lead ON registros_visitas (
    local_id,
    lower(fullname),
    lower(email)
);

CREATE
INDEX IF NOT EXISTS idx_email ON registros_visitas (lower(email));

CREATE INDEX IF NOT EXISTS idx_course ON registros_visitas (course);

CREATE
INDEX IF NOT EXISTS idx_localid ON registros_visitas (local_id);