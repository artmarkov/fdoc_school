#!/bin/bash
set -e

POSTGRES_TEST_DB=${POSTGRES_DB}_test

# create test DB
echo "Creating database: ${POSTGRES_DB}_test"
psql --username "$POSTGRES_USER" <<EOSQL
CREATE DATABASE ${POSTGRES_TEST_DB} OWNER ${POSTGRES_USER};
EOSQL

# create extension
echo "Creating extensions in DBs"
psql --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" -c 'CREATE EXTENSION IF NOT EXISTS pg_trgm;CREATE EXTENSION IF NOT EXISTS tablefunc;'
psql --username "$POSTGRES_USER" --dbname "$POSTGRES_TEST_DB" -c 'CREATE EXTENSION IF NOT EXISTS pg_trgm;CREATE EXTENSION IF NOT EXISTS tablefunc;'
