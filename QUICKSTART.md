# Quick Start Guide

## Inizio Rapido (3 minuti)

### 1. Installa le dipendenze
```bash
composer install
```

### 2. Inizializza il database
```bash
bash init_db.sh
```

### 3. Avvia il server
```bash
php -S localhost:8000
```

Oppure, usa lo script automatico:
```bash
bash start.sh
```

---

## Test degli Endpoint

### Option 1: Usare curl

```bash
# Tutti gli endpoint
curl http://localhost:8000/

# Endpoint 1: Pezzi forniti
curl http://localhost:8000/1

# Endpoint 10: Pezzi da almeno 2 fornitori
curl http://localhost:8000/10
```

### Option 2: Usare jq per output formattato

```bash
curl http://localhost:8000/1 | jq .
curl http://localhost:8000/10 | jq .
```

### Option 3: Usare Postman

Importare gli URL:
- `http://localhost:8000/1`
- `http://localhost:8000/2`
- ... `http://localhost:8000/10`

---

## Struttura File

| File | Descrizione |
|------|-------------|
| `index.php` | Codice principale API con 10 endpoint |
| `database.sql` | Dump database (Fornitori, Pezzi, Catalogo) |
| `database.db` | File database SQLite (creato dopo init_db.sh) |
| `composer.json` | Dipendenze PHP (Slim Framework) |
| `API_DOCS.md` | Documentazione dettagliata di ogni endpoint |
| `ENDPOINTS.md` | Lista rapida degli endpoint |
| `start.sh` | Script per avviare il server automaticamente |
| `init_db.sh` | Script per creare il database |

---

## I 10 Endpoint

```
/1  → Pezzi forniti da almeno un fornitore
/2  → Fornitori che forniscono ogni pezzo
/3  → Fornitori che forniscono tutti i pezzi rossi
/4  → Pezzi forniti dalla Acme e da nessun altro
/5  → Fornitori che ricaricano sopra la media
/6  → Per ogni pezzo, i fornitori con costo massimo
/7  → Fornitori che forniscono solo pezzi rossi
/8  → Fornitori con pezzi rossi e verdi
/9  → Fornitori con pezzi rossi o verdi
/10 → Pezzi forniti da almeno due fornitori
```

---

## Database d'Esempio

### Fornitori (4)
- Acme
- TechSupply
- Global Parts
- FastDelivery

### Pezzi (5)
- Vite (rosso)
- Bullone (blu)
- Dado (rosso)
- Rondella (verde)
- Chiodo (rosso)

### Catalogo (11 associazioni)
Ogni fornitore fornisce alcuni dei questi pezzi con specifici costi.

---

## Formato Risposte

```json
{
  "query": "Descrizione della query",
  "results": [
    {
      "campo1": "valore1",
      "campo2": "valore2"
    }
  ],
  "count": 1
}
```

---

## Troubleshooting

### Errore: "Database error"
```bash
bash init_db.sh
```

### Errore: "No such file or directory" per database.db
```bash
# Il file non esiste, esegui:
bash init_db.sh
```

### Errore di porta già in uso (Port 8000)
Usa un'altra porta:
```bash
php -S localhost:8001
```

### Per disabilitare Xdebug (warning nei log)
È solo un warning, non impedisce il funzionamento dell'API. È sicuro ignorarlo.

---

## Prossime Step

1. **Modifica i dati**: Edit `database.sql` con i tuoi dati
2. **Aggiungi nuovi endpoint**: Aggiungi più route in `index.php`
3. **Deploy**: Usa Nginx/Apache in produzione
4. **Test**: Implementa test unitari in `tests/`

---

## Link Utili

- [Slim Framework Docs](https://www.slimframework.com)
- [SQLite Documentation](https://www.sqlite.org)
- [PHP PDO](https://www.php.net/manual/en/book.pdo.php)

---

Enjoy! 🚀
