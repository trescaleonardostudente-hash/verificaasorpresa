# Documentazione API - Verificaa Sorpresa

## Introduzione

API REST per gestire query su Fornitori, Pezzi e Catalogo utilizzando [Slim Framework](https://www.slimframework.com/).

**Base URL**: `http://localhost:8000`

**Formato risposte**: `application/json`

---

## Setup e Avvio

### Prerequisiti
- PHP 7.4+
- Composer
- SQLite3

### Installazione

```bash
# 1. Clonare il repository
git clone <repository>
cd verificaasorpresa

# 2. Installare dipendenze
composer install

# 3. Inizializzare il database
bash init_db.sh

# 4. Avviare il server
php -S localhost:8000
```

L'API sarà disponibile su `http://localhost:8000`

---

## Endpoints

### GET /

**Descrizione**: Endpoint di test - elenca tutti gli endpoint disponibili

**Risposta**:
```json
{
  "status": "API is running",
  "endpoints": [
    "GET /1",
    "GET /2",
    ...
    "GET /10"
  ],
  "description": "Database Queries API - Fornitori, Pezzi, Catalogo"
}
```

---

### GET /1

**Descrizione**: Pezzi forniti da almeno un fornitore

**Query SQL**:
```sql
SELECT DISTINCT p.pnome 
FROM Pezzi p 
WHERE EXISTS (SELECT 1 FROM Catalogo c WHERE c.pid = p.pid)
```

**Esempio di risposta**:
```json
{
  "query": "Pezzi forniti da almeno un fornitore",
  "results": [
    {"pnome": "Vite"},
    {"pnome": "Bullone"},
    {"pnome": "Dado"},
    {"pnome": "Rondella"},
    {"pnome": "Chiodo"}
  ],
  "count": 5
}
```

---

### GET /2

**Descrizione**: Fornitori che forniscono ogni pezzo

**Query SQL**:
```sql
SELECT DISTINCT f.fnome 
FROM Fornitori f 
WHERE NOT EXISTS (
    SELECT p.pid FROM Pezzi p 
    WHERE NOT EXISTS (
        SELECT 1 FROM Catalogo c 
        WHERE c.fid = f.fid AND c.pid = p.pid
    )
)
```

**Esempio di risposta**:
```json
{
  "query": "Fornitori che forniscono ogni pezzo",
  "results": [],
  "count": 0
}
```

---

### GET /3

**Descrizione**: Fornitori che forniscono tutti i pezzi rossi

**Query SQL**:
```sql
SELECT DISTINCT f.fnome 
FROM Fornitori f 
WHERE NOT EXISTS (
    SELECT p.pid FROM Pezzi p 
    WHERE p.colore = 'rosso'
    AND NOT EXISTS (
        SELECT 1 FROM Catalogo c 
        WHERE c.fid = f.fid AND c.pid = p.pid
    )
) 
AND EXISTS (
    SELECT 1 FROM Pezzi p 
    WHERE p.colore = 'rosso' 
    AND EXISTS (
        SELECT 1 FROM Catalogo c 
        WHERE c.fid = f.fid AND c.pid = p.pid
    )
)
```

**Esempio di risposta**:
```json
{
  "query": "Fornitori che forniscono tutti i pezzi rossi",
  "results": [],
  "count": 0
}
```

---

### GET /4

**Descrizione**: Pezzi forniti dalla Acme e da nessun altro

**Query SQL**:
```sql
SELECT p.pnome 
FROM Pezzi p 
WHERE EXISTS (
    SELECT 1 FROM Catalogo c, Fornitori f 
    WHERE c.pid = p.pid AND c.fid = f.fid AND f.fnome = 'Acme'
)
AND NOT EXISTS (
    SELECT 1 FROM Catalogo c, Fornitori f 
    WHERE c.pid = p.pid AND c.fid = f.fid AND f.fnome != 'Acme'
)
```

**Esempio di risposta**:
```json
{
  "query": "Pezzi forniti dalla Acme e da nessun altro",
  "results": [],
  "count": 0
}
```

---

### GET /5

**Descrizione**: Fornitori che ricaricano su alcuni pezzi più del costo medio di quel pezzo

**Query SQL**:
```sql
SELECT DISTINCT f.fid, f.fnome 
FROM Fornitori f, Catalogo c 
WHERE f.fid = c.fid 
AND c.costo > (
    SELECT AVG(costo) FROM Catalogo 
    WHERE pid = c.pid
)
```

**Esempio di risposta**:
```json
{
  "query": "Fornitori che ricaricano sopra la media su alcuni pezzi",
  "results": [
    {"fid": "F001", "fnome": "Acme"},
    {"fid": "F002", "fnome": "TechSupply"},
    {"fid": "F004", "fnome": "FastDelivery"}
  ],
  "count": 3
}
```

---

### GET /6

**Descrizione**: Per ciascun pezzo, fornitori che ricaricano di più su quel pezzo

**Query SQL**:
```sql
SELECT p.pnome, f.fnome, c.costo 
FROM Pezzi p, Fornitori f, Catalogo c 
WHERE p.pid = c.pid 
AND f.fid = c.fid 
AND c.costo = (
    SELECT MAX(costo) FROM Catalogo 
    WHERE pid = p.pid
)
ORDER BY p.pnome
```

**Esempio di risposta**:
```json
{
  "query": "Per ogni pezzo, i fornitori con costo massimo",
  "results": [
    {"pnome": "Bullone", "fnome": "TechSupply", "costo": 2.1},
    {"pnome": "Chiodo", "fnome": "Global Parts", "costo": 0.8},
    {"pnome": "Dado", "fnome": "Acme", "costo": 1.8},
    {"pnome": "Rondella", "fnome": "FastDelivery", "costo": 3.6},
    {"pnome": "Vite", "fnome": "FastDelivery", "costo": 1.7}
  ],
  "count": 5
}
```

---

### GET /7

**Descrizione**: Fornitori che forniscono solo pezzi rossi

**Query SQL**:
```sql
SELECT DISTINCT f.fid, f.fnome 
FROM Fornitori f 
WHERE NOT EXISTS (
    SELECT 1 FROM Catalogo c, Pezzi p 
    WHERE c.fid = f.fid 
    AND c.pid = p.pid 
    AND p.colore != 'rosso'
)
AND EXISTS (
    SELECT 1 FROM Catalogo c, Pezzi p 
    WHERE c.fid = f.fid 
    AND c.pid = p.pid 
    AND p.colore = 'rosso'
)
```

**Esempio di risposta**:
```json
{
  "query": "Fornitori che forniscono solo pezzi rossi",
  "results": [],
  "count": 0
}
```

---

### GET /8

**Descrizione**: Fornitori che forniscono un pezzo rosso e un pezzo verde

**Query SQL**:
```sql
SELECT DISTINCT f.fid, f.fnome 
FROM Fornitori f 
WHERE EXISTS (
    SELECT 1 FROM Catalogo c, Pezzi p 
    WHERE c.fid = f.fid 
    AND c.pid = p.pid 
    AND p.colore = 'rosso'
)
AND EXISTS (
    SELECT 1 FROM Catalogo c, Pezzi p 
    WHERE c.fid = f.fid 
    AND c.pid = p.pid 
    AND p.colore = 'verde'
)
```

**Esempio di risposta**:
```json
{
  "query": "Fornitori che forniscono un pezzo rosso e uno verde",
  "results": [
    {"fid": "F002", "fnome": "TechSupply"},
    {"fid": "F004", "fnome": "FastDelivery"}
  ],
  "count": 2
}
```

---

### GET /9

**Descrizione**: Fornitori che forniscono un pezzo rosso o uno verde

**Query SQL**:
```sql
SELECT DISTINCT f.fid, f.fnome 
FROM Fornitori f 
WHERE EXISTS (
    SELECT 1 FROM Catalogo c, Pezzi p 
    WHERE c.fid = f.fid 
    AND c.pid = p.pid 
    AND (p.colore = 'rosso' OR p.colore = 'verde')
)
```

**Esempio di risposta**:
```json
{
  "query": "Fornitori che forniscono un pezzo rosso o verde",
  "results": [
    {"fid": "F001", "fnome": "Acme"},
    {"fid": "F002", "fnome": "TechSupply"},
    {"fid": "F003", "fnome": "Global Parts"},
    {"fid": "F004", "fnome": "FastDelivery"}
  ],
  "count": 4
}
```

---

### GET /10

**Descrizione**: Pezzi forniti da almeno due fornitori

**Query SQL**:
```sql
SELECT p.pid, p.pnome, COUNT(DISTINCT c.fid) as fornitori_count
FROM Pezzi p, Catalogo c 
WHERE p.pid = c.pid 
GROUP BY p.pid, p.pnome
HAVING COUNT(DISTINCT c.fid) >= 2
ORDER BY fornitori_count DESC
```

**Esempio di risposta**:
```json
{
  "query": "Pezzi forniti da almeno due fornitori",
  "results": [
    {"pid": "P001", "pnome": "Vite", "fornitori_count": 3},
    {"pid": "P002", "pnome": "Bullone", "fornitori_count": 3},
    {"pid": "P003", "pnome": "Dado", "fornitori_count": 2},
    {"pid": "P004", "pnome": "Rondella", "fornitori_count": 2}
  ],
  "count": 4
}
```

---

## Schema del Database

### Tabella: Fornitori
| Colonna | Tipo | Descrizione |
|---------|------|-------------|
| fid | VARCHAR(10) | ID fornitore (chiave primaria) |
| fnome | VARCHAR(100) | Nome fornitore |
| indirizzo | VARCHAR(200) | Indirizzo fornitore |

### Tabella: Pezzi
| Colonna | Tipo | Descrizione |
|---------|------|-------------|
| pid | VARCHAR(10) | ID pezzo (chiave primaria) |
| pnome | VARCHAR(100) | Nome pezzo |
| colore | VARCHAR(50) | Colore pezzo |

### Tabella: Catalogo
| Colonna | Tipo | Descrizione |
|---------|------|-------------|
| fid | VARCHAR(10) | ID fornitore (FK) |
| pid | VARCHAR(10) | ID pezzo (FK) |
| costo | REAL | Costo del pezzo |
| Chiave primaria | | (fid, pid) |

---

## Dati di Test Inclusi

### Fornitori
- **F001**: Acme, Via Roma 1
- **F002**: TechSupply, Via Milano 5
- **F003**: Global Parts, Via Napoli 10
- **F004**: FastDelivery, Via Torino 15

### Pezzi
- **P001**: Vite (rosso)
- **P002**: Bullone (blu)
- **P003**: Dado (rosso)
- **P004**: Rondella (verde)
- **P005**: Chiodo (rosso)

### Catalogo (11 associazioni)
- Acme: P001, P002, P003
- TechSupply: P001, P002, P004
- Global Parts: P002, P005
- FastDelivery: P001, P003, P004

---

## Struttura del Progetto

```
verificaasorpresa/
├── index.php               # File principale dell'applicazione Slim
├── database.sql            # Dump del database
├── database.db            # File del database SQLite (creato dopo init_db.sh)
├── init_db.sh             # Script per inizializzare il database
├── composer.json          # Configurazione dipendenze
├── composer.lock          # Lock file (ignorato)
├── vendor/                # Dipendenze Composer
├── src/
│   └── Database/
│       └── DatabaseConnection.php  # Classe per la connessione al DB
├── tests/
│   └── EndpointsTest.php   # Test unitari
├── run_tests.sh           # Script per eseguire i test
├── README.md              # Documentazione generale
└── API_DOCS.md           # Questa documentazione
```

---

## Errori Comuni

### Errore: "Database error"
**Causa**: Il file `database.db` non è stato creato o è stato eliminato.

**Soluzione**: Eseguire il comando:
```bash
bash init_db.sh
```

### Errore: "Call to undefined method"
**Causa**: Versione errata di Slim Framework.

**Soluzione**: Assicurarsi di avere installato Slim 4.x:
```bash
composer require slim/slim:^4.0 slim/psr7:^1.6
```

---

## Test degli Endpoint

### Con cURL

```bash
# Test endpoint 1
curl http://localhost:8000/1

# Test endpoint 10
curl http://localhost:8000/10

# Con jq per formattare l'output
curl http://localhost:8000/1 | jq .
```

### Con Postman

Importare una nuova collezione e aggiungere le seguenti richieste:
- **GET** `http://localhost:8000/1`
- **GET** `http://localhost:8000/2`
- ... fino a
- **GET** `http://localhost:8000/10`

---

## License

MIT License - Vedi il file LICENSE per dettagli.
