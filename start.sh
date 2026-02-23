#!/bin/bash

# Setup Rapido - Avvia la API in 3 step

echo "========================================="
echo "Verificaa Sorpresa - Setup Rapido"
echo "========================================="
echo ""

# Step 1: Installa le dipendenze
echo "Step 1: Installazione dipendenze..."
if [ ! -d "vendor" ]; then
    composer install
else
    echo "✓ Dipendenze già installate"
fi

# Step 2: Inizializza il database
echo ""
echo "Step 2: Inizializzazione database..."
bash init_db.sh

# Step 3: Avvia il server
echo ""
echo "========================================="
echo "Step 3: Avvio del server..."
echo "========================================="
echo ""
echo "✓ API disponibile su http://localhost:8000"
echo ""
echo "Endpoint disponibili:"
echo "  - GET http://localhost:8000/"
echo "  - GET http://localhost:8000/1 .. /10"
echo ""
echo "Per visualizzare la documentazione:"
echo "  - Vedi API_DOCS.md"
echo "  - Vedi ENDPOINTS.md"
echo ""
echo "Premi CTRL+C per fermare il server"
echo "========================================="
echo ""

php -S localhost:8000
