CREATE TABLE IF NOT EXISTS klasse (
  klassekode CHAR(5) NOT NULL,
  klassenavn VARCHAR(50) NOT NULL,
  studiumkode VARCHAR(50) NOT NULL,
  PRIMARY KEY (klassekode)
);

CREATE TABLE IF NOT EXISTS student (
  brukernavn CHAR(7) NOT NULL,
  fornavn VARCHAR(50) NOT NULL,
  etternavn VARCHAR(50) NOT NULL,
  klassekode CHAR(5) NOT NULL,
  PRIMARY KEY (brukernavn),
  FOREIGN KEY (klassekode) REFERENCES klasse (klassekode)
);

-- Eksempeldata
INSERT INTO klasse (klassekode, klassenavn, studiumkode) VALUES
('IT1', 'IT og ledelse 1. år', 'ITLED'),
('IT2', 'IT og ledelse 2. år', 'ITLED'),
('IT3', 'IT og ledelse 3. år', 'ITLED')
ON DUPLICATE KEY UPDATE klassenavn=VALUES(klassenavn), studiumkode=VALUES(studiumkode);

INSERT INTO student (brukernavn, fornavn, etternavn, klassekode) VALUES
('gb',  'Geir',   'Bjarvin',      'IT1'),
('mrj', 'Marius', 'R. Johannessen','IT1'),
('tb',  'Tove',   'Bøe',          'IT2')
ON DUPLICATE KEY UPDATE fornavn=VALUES(fornavn), etternavn=VALUES(etternavn), klassekode=VALUES(klassekode);
