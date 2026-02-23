.PHONY: install setup start test clean help

help:
	@echo "Makefile - Comando di aiuto"
	@echo ""
	@echo "make install    - Installa le dipendenze"
	@echo "make setup      - Crea il database"
	@echo "make start      - Avvia il server"
	@echo "make test       - Testa gli endpoint"
	@echo "make clean      - Ferma il server"
	@echo ""

install:
	composer install

setup:
	bash init_db.sh

start:
	php -S localhost:8000

test:
	@echo "Test endpoint /1:"
	@curl -s http://localhost:8000/1 | jq .

clean:
	@killall php 2>/dev/null || true
	@echo "Server fermato"
