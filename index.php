<?php

require __DIR__ . '/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// Crea l'app Slim
$app = AppFactory::create();

// Middleware per CORS
$app->add(function ($request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type')
        ->withHeader('Content-Type', 'application/json');
});

// Connessione al database
function getDB() {
    try {
        $db = new PDO('sqlite:' . __DIR__ . '/database.db');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        return null;
    }
}

// ENDPOINT 1: Trovare i pnome dei pezzi per cui esiste un qualche fornitore
$app->get('/1', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT DISTINCT p.pnome 
            FROM Pezzi p 
            WHERE EXISTS (SELECT 1 FROM Catalogo c WHERE c.pid = p.pid)";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Pezzi forniti da almeno un fornitore',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 2: Trovare gli fnome dei fornitori che forniscono ogni pezzo
$app->get('/2', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT DISTINCT f.fnome 
            FROM Fornitori f 
            WHERE NOT EXISTS (
                SELECT p.pid FROM Pezzi p 
                WHERE NOT EXISTS (
                    SELECT 1 FROM Catalogo c 
                    WHERE c.fid = f.fid AND c.pid = p.pid
                )
            )";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Fornitori che forniscono ogni pezzo',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 3: Trovare gli fnome dei fornitori che forniscono tutti i pezzi rossi
$app->get('/3', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT DISTINCT f.fnome 
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
            )";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Fornitori che forniscono tutti i pezzi rossi',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 4: Trovare i pnome dei pezzi forniti dalla Acme e da nessun altro
$app->get('/4', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT p.pnome 
            FROM Pezzi p 
            WHERE EXISTS (
                SELECT 1 FROM Catalogo c, Fornitori f 
                WHERE c.pid = p.pid AND c.fid = f.fid AND f.fnome = 'Acme'
            )
            AND NOT EXISTS (
                SELECT 1 FROM Catalogo c, Fornitori f 
                WHERE c.pid = p.pid AND c.fid = f.fid AND f.fnome != 'Acme'
            )";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Pezzi forniti dalla Acme e da nessun altro',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 5: Trovare i fid dei fornitori che ricaricano su alcuni pezzi più del costo medio di quel pezzo
$app->get('/5', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT DISTINCT f.fid, f.fnome 
            FROM Fornitori f, Catalogo c 
            WHERE f.fid = c.fid 
            AND c.costo > (
                SELECT AVG(costo) FROM Catalogo 
                WHERE pid = c.pid
            )";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Fornitori che ricaricano sopra la media su alcuni pezzi',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 6: Per ciascun pezzo, trovare gli fnome dei fornitori che ricaricano di più su quel pezzo
$app->get('/6', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT p.pnome, f.fnome, c.costo 
            FROM Pezzi p, Fornitori f, Catalogo c 
            WHERE p.pid = c.pid 
            AND f.fid = c.fid 
            AND c.costo = (
                SELECT MAX(costo) FROM Catalogo 
                WHERE pid = p.pid
            )
            ORDER BY p.pnome";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Per ogni pezzo, i fornitori con costo massimo',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 7: Trovare i fid dei fornitori che forniscono solo pezzi rossi
$app->get('/7', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT DISTINCT f.fid, f.fnome 
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
            )";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Fornitori che forniscono solo pezzi rossi',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 8: Trovare i fid dei fornitori che forniscono un pezzo rosso e un pezzo verde
$app->get('/8', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT DISTINCT f.fid, f.fnome 
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
            )";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Fornitori che forniscono un pezzo rosso e uno verde',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 9: Trovare i fid dei fornitori che forniscono un pezzo rosso o uno verde
$app->get('/9', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT DISTINCT f.fid, f.fnome 
            FROM Fornitori f 
            WHERE EXISTS (
                SELECT 1 FROM Catalogo c, Pezzi p 
                WHERE c.fid = f.fid 
                AND c.pid = p.pid 
                AND (p.colore = 'rosso' OR p.colore = 'verde')
            )";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Fornitori che forniscono un pezzo rosso o verde',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// ENDPOINT 10: Trovare i pid dei pezzi forniti da almeno due fornitori
$app->get('/10', function (Request $request, Response $response) {
    $db = getDB();
    if (!$db) {
        $response->getBody()->write(json_encode(['error' => 'Database error']));
        return $response->withStatus(500);
    }
    
    $sql = "SELECT p.pid, p.pnome, COUNT(DISTINCT c.fid) as fornitori_count
            FROM Pezzi p, Catalogo c 
            WHERE p.pid = c.pid 
            GROUP BY p.pid, p.pnome
            HAVING COUNT(DISTINCT c.fid) >= 2
            ORDER BY fornitori_count DESC";
    
    $stmt = $db->query($sql);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response->getBody()->write(json_encode([
        'query' => 'Pezzi forniti da almeno due fornitori',
        'results' => $result,
        'count' => count($result)
    ]));
    return $response->withStatus(200);
});

// Route di test
$app->get('/', function (Request $request, Response $response) {
    $endpoints = [];
    for ($i = 1; $i <= 10; $i++) {
        $endpoints[] = "GET /$i";
    }
    
    $response->getBody()->write(json_encode([
        'status' => 'API is running',
        'endpoints' => $endpoints,
        'description' => 'Database Queries API - Fornitori, Pezzi, Catalogo'
    ]));
    return $response->withStatus(200);
});

$app->run();
