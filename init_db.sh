#!/bin/bash

# Script per inizializzare il database SQLite dal dump SQL

DB_FILE="database.db"

# Rimuovi il database precedente se esiste
if [ -f "$DB_FILE" ]; then
    rm "$DB_FILE"
    echo "Database precedente rimosso."
fi

# Crea il nuovo database da database.sql
sqlite3 "$DB_FILE" < database.sql

if [ -f "$DB_FILE" ]; then
    echo "Database creato con successo: $DB_FILE"
else
    echo "Errore nella creazione del database"
    exit 1
fi
