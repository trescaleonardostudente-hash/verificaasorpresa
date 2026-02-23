# URL Endpoint API

Quando il server è avviato su `locale:8000`, puoi testare gli endpoint:

## Endpoint di Test

```
GET http://localhost:8000/                  Elenco degli endpoint
```

## Query Implementate

```
GET http://localhost:8000/1                 Pezzi forniti da almeno un fornitore
GET http://localhost:8000/2                 Fornitori che forniscono ogni pezzo
GET http://localhost:8000/3                 Fornitori che forniscono tutti i pezzi rossi
GET http://localhost:8000/4                 Pezzi forniti dalla Acme e da nessun altro
GET http://localhost:8000/5                 Fornitori che ricaricano sopra la media
GET http://localhost:8000/6                 Per ogni pezzo, i fornitori con costo massimo
GET http://localhost:8000/7                 Fornitori che forniscono solo pezzi rossi
GET http://localhost:8000/8                 Fornitori con pezzi rossi e verdi
GET http://localhost:8000/9                 Fornitori con pezzi rossi o verdi
GET http://localhost:8000/10                Pezzi forniti da almeno due fornitori
```

## Comandi Utili

### Avviare il server
```bash
php -S localhost:8000
```

### Testare un endpoint con cURL
```bash
curl http://localhost:8000/1 | jq .
```

### Testare con Postman
Importare i seguenti URL come richieste GET:
- http://localhost:8000/1
- http://localhost:8000/2
- ... fino a http://localhost:8000/10

## File Principali

- `index.php` - Codice principale dell'API con tutti gli endpoint
- `database.sql` - Dump del database con le tabelle e i dati di test
- `database.db` - File del database SQLite (creato da init_db.sh)
- `composer.json` - Configurazione delle dipendenze
- `API_DOCS.md` - Documentazione dettagliata di ogni endpoint

## Struttura Risposte JSON

Tutte le risposte seguono questo formato:

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
