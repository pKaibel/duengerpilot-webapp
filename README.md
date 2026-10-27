# Düngerpilot Webapp

Die **Düngerpilot Webapp** ist eine PHP-basierte Webanwendung zur Berechnung und Bewertung von Düngestrategien.  
Sie kombiniert Eingabedaten zu **Kultur**, **Bodenart** und **Düngemitteln** und liefert Ergebnisse inkl. Umweltbewertung.

---

## Funktionen

- Auswahl und Verwaltung von:
  - **Kulturen**
  - **Bodenarten**
  - **Düngemitteln**
- Berechnung von Ergebnissen über die Hauptanwendung
- Anzeige von Resultaten in einer separaten Ergebnisseite
- Berechnung eines **Environmental Scores** (Umwelt-Score)
- SQL-Skripte für das initiale Anlegen der benötigten Tabellen

---

## Projektstruktur

```text
duengerpilot-webapp/
├── app/                         # Anwendungslogik/Module
├── assets/                      # Statische Assets (z. B. CSS, Bilder, Icons)
├── index.php                    # Einstiegspunkt der Webapp
├── results.php                  # Ausgabe/Berechnungsresultate
├── environmental_score.php      # Logik zur Umweltbewertung
├── crops.php                    # Verwaltung/Abfrage von Kulturen
├── fertilizers.php              # Verwaltung/Abfrage von Düngemitteln
├── soil_types.php               # Verwaltung/Abfrage von Bodenarten
├── create_crops_table.sql       # SQL-Skript: Tabelle für Kulturen
├── create_fertilizers_table.sql # SQL-Skript: Tabelle für Düngemittel
├── create_soil_types_table.sql  # SQL-Skript: Tabelle für Bodenarten
├── favicon.ico
└── .gitignore
```

---

## Technologie-Stack

- **PHP** (~87 %)
- **CSS** (~13 %)

---

## Voraussetzungen

- PHP (empfohlen: PHP 8.x)
- Webserver (Apache oder Nginx)  
  _oder_ für lokal schnell: PHP Built-in Server
- Datenbanksystem mit SQL-Unterstützung (für die bereitgestellten Tabellen-Skripte)

---

## Installation & Setup

### 1) Repository klonen

```bash
git clone https://github.com/pKaibel/duengerpilot-webapp.git
cd duengerpilot-webapp
```

### 2) Datenbanktabellen anlegen

Führe die SQL-Dateien in deiner Datenbank aus:

- `create_crops_table.sql`
- `create_fertilizers_table.sql`
- `create_soil_types_table.sql`

Beispiel (MySQL/MariaDB, sinngemäß):

```bash
mysql -u <user> -p <datenbankname> < create_crops_table.sql
mysql -u <user> -p <datenbankname> < create_fertilizers_table.sql
mysql -u <user> -p <datenbankname> < create_soil_types_table.sql
```

### 3) Konfiguration prüfen

Je nach Implementierung in den PHP-Dateien sicherstellen:

- DB-Host
- DB-Name
- DB-User
- DB-Passwort

> Falls die Zugangsdaten direkt im Code stehen, bitte für lokale/Produktivumgebung anpassen.

---

## Lokaler Start

### Option A: PHP Built-in Server

```bash
php -S localhost:8000
```

Dann im Browser öffnen:

- `http://localhost:8000/index.php`

### Option B: Apache/Nginx

- Projektverzeichnis als Webroot einbinden
- `index.php` als Startseite verwenden

---

## Typischer Ablauf in der App

1. Aufruf über `index.php`
2. Auswahl relevanter Eingaben (Kultur, Bodenart, Düngemittel)
3. Berechnung/Verarbeitung
4. Ergebnisdarstellung über `results.php`
5. Umweltbezogene Bewertung über `environmental_score.php`

---

## Entwicklung

### Nützliche Hinweise

- Fachlogik liegt in den PHP-Dateien im Root sowie ggf. im `app/`-Verzeichnis.
- Statische Dateien sind unter `assets/` abgelegt.
- Datenmodell-Änderungen können über zusätzliche SQL-Migrationsskripte versioniert werden.

---

## Lizenz

Aktuell ist keine Lizenzdatei im Repository hinterlegt.  
Falls gewünscht, ergänze eine `LICENSE` (z. B. MIT, Apache-2.0, proprietär).

---

## Repository

GitHub: https://github.com/pKaibel/duengerpilot-webapp
