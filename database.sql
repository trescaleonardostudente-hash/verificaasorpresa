-- Dump del database per Fornitori, Pezzi e Catalogo

CREATE TABLE IF NOT EXISTS Fornitori (
    fid VARCHAR(10) PRIMARY KEY,
    fnome VARCHAR(100) NOT NULL,
    indirizzo VARCHAR(200)
);

CREATE TABLE IF NOT EXISTS Pezzi (
    pid VARCHAR(10) PRIMARY KEY,
    pnome VARCHAR(100) NOT NULL,
    colore VARCHAR(50)
);

CREATE TABLE IF NOT EXISTS Catalogo (
    fid VARCHAR(10) NOT NULL,
    pid VARCHAR(10) NOT NULL,
    costo REAL NOT NULL,
    PRIMARY KEY (fid, pid),
    FOREIGN KEY (fid) REFERENCES Fornitori(fid),
    FOREIGN KEY (pid) REFERENCES Pezzi(pid)
);

-- Inserimento dati di test
INSERT INTO Fornitori VALUES 
('F001', 'Acme', 'Via Roma 1'),
('F002', 'TechSupply', 'Via Milano 5'),
('F003', 'Global Parts', 'Via Napoli 10'),
('F004', 'FastDelivery', 'Via Torino 15');

INSERT INTO Pezzi VALUES 
('P001', 'Vite', 'rosso'),
('P002', 'Bullone', 'blu'),
('P003', 'Dado', 'rosso'),
('P004', 'Rondella', 'verde'),
('P005', 'Chiodo', 'rosso');

INSERT INTO Catalogo VALUES 
('F001', 'P001', 1.50),
('F001', 'P002', 2.00),
('F001', 'P003', 1.80),
('F002', 'P001', 1.60),
('F002', 'P002', 2.10),
('F002', 'P004', 3.50),
('F003', 'P002', 1.95),
('F003', 'P005', 0.80),
('F004', 'P001', 1.70),
('F004', 'P003', 1.75),
('F004', 'P004', 3.60);
