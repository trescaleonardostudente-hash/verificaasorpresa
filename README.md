# API Verificaa Sorpresa - Fornitori, Pezzi, Catalogo

API REST per gestire query sui Fornitori, Pezzi e Catalogo utilizzando Slim Framework.

## Installazione

```bash
# Installa le dipendenze
composer install

# Inizializza il database
bash init_db.sh

# Avvia il server (dalla root del progetto)
php -S localhost:8000
```

## Endpoints

### Endpoint 1: Pezzi forniti da almeno un fornitore
```
GET http://localhost:8000/1
```
Trova i pnome dei pezzi per cui esiste un qualche fornitore.

### Endpoint 2: Fornitori che forniscono ogni pezzo
```
GET http://localhost:8000/2
```
Trova gli fnome dei fornitori che forniscono ogni pezzo.

### Endpoint 3: Fornitori che forniscono tutti i pezzi rossi
```
GET http://localhost:8000/3
```
Trova gli fnome dei fornitori che forniscono tutti i pezzi rossi.

### Endpoint 4: Pezzi forniti soltanto da Acme  
```
GET http://localhost:8000/4
```
Trova i pnome dei pezzi forniti dalla Acme e da nessun altro.

### Endpoint 5: Fornitori che ricaricano sopra la media
```
GET http://localhost:8000/5
```
Trova i fid dei fornitori che ricaricano su alcuni pezzi più del costo medio di quel pezzo.

### Endpoint 6: Fornitori con costo massimo per ogni pezzo
```
GET http://localhost:8000/6
```
Per ciascun pezzo, trova gli fnome dei fornitori che ricaricano di più su quel pezzo.

### Endpoint 7: Fornitori che forniscono solo pezzi rossi
```
GET http://localhost:8000/7
```
Trova i fid dei fornitori che forniscono solo pezzi rossi.

### Endpoint 8: Fornitori con pezzi rossi e verdi
```
GET http://localhost:8000/8
```
Trova i fid dei fornitori che forniscono un pezzo rosso e un pezzo verde.

### Endpoint 9: Fornitori con pezzi rossi o verdi
```
GET http://localhost:8000/9
```
Trova i fid dei fornitori che forniscono un pezzo rosso o uno verde.

### Endpoint 10: Pezzi forniti da almeno due fornitori
```
GET http://localhost:8000/10
```
Trova i pid dei pezzi forniti da almeno due fornitori.

## Risposta JSON

Tutte le risposte sono in formato JSON con la seguente struttura:

```json
{
  "query": "descrizione della query",
  "results": [ /* risultati */ ],
  "count": 5
}
```

## Struttura del Database

### Tabella Fornitori
- `fid` (VARCHAR 10): ID fornitore
- `fnome` (VARCHAR 100): Nome fornitore
- `indirizzo` (VARCHAR 200): Indirizzo

### Tabella Pezzi
- `pid` (VARCHAR 10): ID pezzo
- `pnome` (VARCHAR 100): Nome pezzo
- `colore` (VARCHAR 50): Colore

### Tabella Catalogo
- `fid` (VARCHAR 10): ID fornitore (FK)
- `pid` (VARCHAR 10): ID pezzo (FK)
- `costo` (REAL): Costo

## Dati di Test

Il database include 4 fornitori, 5 pezzi e 11 associazioni nel catalogo per testare tutte le query.