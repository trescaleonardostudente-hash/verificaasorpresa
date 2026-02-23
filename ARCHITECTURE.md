# Architettura del Progetto

## Diagramma di Flusso

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT (Browser/cURL)                    │
└────────────────────────┬────────────────────────────────────┘
                         │
                    HTTP Request
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                  PHP Development Server                      │
│                   (localhost:8000)                           │
└─────────────────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│              Slim Framework Application                      │
│                  (index.php)                                 │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  Route Matcher ──► Middleware (CORS) ──► Handler Function  │
│                                                              │
│  /1, /2, /3, ..., /10                                       │
│                                                              │
└────────────────┬─────────────────────────────────────────────┘
                 │
                 │ Esegue Query SQL
                 ▼
┌─────────────────────────────────────────────────────────────┐
│              SQLite Database (database.db)                   │
├─────────────────────────────────────────────────────────────┤
│                                                              │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  Fornitori   │  │    Pezzi     │  │  Catalogo    │      │
│  │              │  │              │  │              │      │
│  │ fid (PK)     │  │ pid (PK)     │  │ fid (FK)     │      │
│  │ fnome        │  │ pnome        │  │ pid (FK)     │      │
│  │ indirizzo    │  │ colore       │  │ costo        │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│                                                              │
└────────────────┬─────────────────────────────────────────────┘
                 │
                 │ Risultati (rows)
                 ▼
┌─────────────────────────────────────────────────────────────┐
│           PHP Data Layer (index.php)                         │
│                                                              │
│  - Recupera dati con PDO                                    │
│  - Converte a array associativo                            │
│  - Serializza a JSON                                        │
│                                                              │
└────────────────┬─────────────────────────────────────────────┘
                 │
                 │ HTTP Response
                 ▼
┌─────────────────────────────────────────────────────────────┐
│              JSON Response (application/json)                │
│                                                              │
│  {                                                           │
│    "query": "Descrizione",                                  │
│    "results": [ {...}, {...} ],                             │
│    "count": 5                                                │
│  }                                                           │
└─────────────────────────────────────────────────────────────┘
                         │
                         ▼
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT Response                          │
│          (Browser/cURL riceve JSON formattato)              │
└─────────────────────────────────────────────────────────────┘
```

---

## Struttura Cartelle

```
verificaasorpresa/
│
├── index.php                    # ⭐ File principale - Slim app + tutti gli endpoint
│
├── database.sql                 # ⭐ Dump del database
│
├── database.db                  # ⭐ File SQLite (generato da init_db.sh)
│
├── composer.json                # Configurazione dipendenze
├── composer.lock                # Lock file delle dipendenze
│
├── vendor/                       # Dipendenze (Slim Framework, PSR-7, etc.)
│   ├── slim/
│   ├── psr/
│   └── ... (altre dipendenze)
│
├── src/                         # Codice sorgente
│   └── Database/
│       └── DatabaseConnection.php   # Classe helper per connessione DB
│
├── tests/                       # Test unitari
│   └── EndpointsTest.php
│
├── init_db.sh                   # Script per creare database.db da database.sql
├── start.sh                     # Script per avviare il server
├── run_tests.sh                 # Script per eseguire i test
│
├── README.md                    # Documentazione generale
├── API_DOCS.md                  # Documentazione dettagliata degli endpoint
├── ENDPOINTS.md                 # Lista rapida degli endpoint
├── QUICKSTART.md                # Guida rapida di inizio
├── ARCHITECTURE.md              # Questo file
│
└── .gitignore                   # File da ignorare in git
```

---

## Ciclo di Vita di una Richiesta

### Esempio: GET /1

```
1. Client invia: GET http://localhost:8000/1

2. PHP Server riceve la richiesta

3. Slim Framework:
   - Analizza il percorso (/1)
   - Trova la route corrispondente
   - Applica middleware (CORS)
   - Chiama il handler della route

4. Handler /1:
   - Crea connessione al database (database.db)
   - Esegue la query SQL:
     SELECT DISTINCT p.pnome FROM Pezzi p 
     WHERE EXISTS (SELECT 1 FROM Catalogo c WHERE c.pid = p.pid)
   - Recupera i risultati

5. Formattazione della risposta:
   - Converte i risultati in JSON
   - Aggiunge metadata (query, count)
   - Imposta header Content-Type: application/json

6. Risposta al client:
   {
     "query": "Pezzi forniti da almeno un fornitore",
     "results": [
       {"pnome": "Vite"},
       {"pnome": "Bullone"},
       ...
     ],
     "count": 5
   }

7. Client riceve e visualizza i dati
```

---

## Stack Tecnologico

| Componente | Tecnologia | Versione |
|-----------|-----------|---------|
| Framework Web | Slim Framework | 4.15.1 |
| Database | SQLite | 3.45.3 |
| Linguaggio | PHP | 8.3+ |
| Gestore Dipendenze | Composer | 2.9.2 |
| PSR-7 Implementation | Slim PSR-7 | 1.8.0 |
| Testing | PHPUnit | 9.6.34 |

---

## Query Design Pattern

Tutte le 10 query seguono il pattern SQL di query nidificate (nested queries) con EXISTS/NOT EXISTS:

```sql
-- Pattern base per query nidificate
SELECT campo1, campo2, ...
FROM tabella_principale
WHERE EXISTS (
    SELECT 1 FROM tabella_correlata 
    WHERE condizioni
)
```

Questo pattern è utile per:
- ✓ Verificare l'esistenza di record correlati
- ✓ Implementare logica complessa di business
- ✓ Evitare JOIN multipli
- ✓ Migliorare leggibilità del codice

---

## Flusso di Initializzazione

```
1. Utente esegue: bash init_db.sh

2. Lo script:
   - Verifica se database.db esiste (lo elimina se presente)
   - Esegue sqlite3 < database.sql
   - sqlite3 legge i comandi SQL da database.sql:
     * CREATE TABLE Fornitori (...)
     * CREATE TABLE Pezzi (...)
     * CREATE TABLE Catalogo (...)
     * INSERT INTO Fornitori VALUES (...)
     * INSERT INTO Pezzi VALUES (...)
     * INSERT INTO Catalogo VALUES (...)

3. Risultato:
   - database.db è creato con 3 tabelle
   - 4 fornitori inseriti
   - 5 pezzi inseriti
   - 11 associazioni nel catalogo

4. database.db è pronto per essere usato dall'API
```

---

## Flusso di Deployment

Per usare questo progetto in produzione:

```
1. Usa un application server vero (non il built-in server)
   - Apache con mod_php
   - Nginx + PHP-FPM
   - Docker container

2. Configura un database persistente
   - Backup regolari del database.db
   - Replica per alta disponibilità

3. Aggiungi un cache layer (opzionale)
   - Redis per caching delle risposte
   - Memcached

4. Configura HTTPS
   - SSL/TLS certificate
   - Redirect HTTP → HTTPS

5. Monitoring e Logging
   - Log degli errori
   - Monitoraggio performance delle query

6. CI/CD Pipeline
   - Test automatici
   - Deploy automatico
```

---

## Performance Considerations

### Query Optimization
- Le query usano EXISTS/NOT EXISTS che sono ottimizzate da SQLite
- Per dataset grandi, considerare:
  - Indici sulle chiavi esterne
  - Query plan analysis

### Database Optimization
```sql
-- Aggiungere indici per migliorare perfor performance:
CREATE INDEX idx_catalogo_fid ON Catalogo(fid);
CREATE INDEX idx_catalogo_pid ON Catalogo(pid);
CREATE INDEX idx_pezzi_colore ON Pezzi(colore);
```

### Response Caching
```php
// Aggiungere header di cache (da implementare):
$response = $response
    ->withHeader('Cache-Control', 'max-age=300')
    ->withHeader('ETag', md5(json_encode($results)));
```

---

## Sicurezza

### Implementato
✓ Response Content-Type: application/json
✓ CORS headers
✓ Parametri recuperati tramite prepared statements (via PDO)

### Da implementare in produzione
- Input validation e sanitization
- Rate limiting
- Authentication/Authorization
- HTTPS obbligatoria
- CORS più restrittivo
- WAF (Web Application Firewall)

---

## Troubleshooting

### Query lenta
```bash
# Analizzare il piano di esecuzione
sqlite3 database.db
sqlite> EXPLAIN QUERY PLAN SELECT ...;
```

### Database corrotto
```bash
# Rifare il database
bash init_db.sh
```

### Errori PHP
```bash
# Controllare i log
php -S localhost:8000 2>&1 | tee server.log
```

---

Creato con ❤️ usando Slim Framework e SQLite
