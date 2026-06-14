<div align="center">
  <h1>🔒🛡️ Progetto SQL Injection - Sicurezza Informatica</h1>
  <i>Progetto per il corso di Sicurezza, Dipartimento di Informatica <br> <b>Università degli Studi di Roma "La Sapienza"</b></i>
</div>
<br>
<div align="center">
  <img src="https://sb.nordcdn.com/transform/2924aecf-2757-4cc5-a0a2-c258909ff706/sql-injection-800x450?format=webp&quality=80&io=transform%3Afill%2Cwidth%3A1920" alt="SQL Injection" width="500">
</div>
<br>
<div align="center">
  <img src="https://img.shields.io/badge/php-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white" alt="MariaDB">
  <img src="https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white" alt="Docker">
  <img src="https://img.shields.io/badge/bootstrap-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap">
</div>

---

## 🎯 Obiettivo del Progetto
Questo progetto ha lo scopo di dimostrare vulnerabilità di tipo **In-band SQL Injection** all'interno di un'applicazione web PHP, analizzando come tali falle possano compromettere i tre principi fondamentali della sicurezza (**CIA**: *Confidentiality, Integrity, Availability*). 

Inoltre, il progetto propone e implementa misure di difesa efficaci sia a livello applicativo (Web App) che strutturale (Database).

## 🚀 Architettura e Setup

L'infrastruttura è completamente dockerizzata ed emula tre componenti fondamentali:
1. 🗄️ **Database Server** (MariaDB)
2. 🔴 **Web App Vulnerabile** (su porta `8080`)
3. 🔵 **Web App Sicura** (su porta `8081`)

### 🛠️ Come Avviare l'Ambiente
Assicurati di avere Docker e Docker Compose installati, quindi lancia nel terminale:
```bash
docker compose up --build -d
```
I servizi saranno subito pronti all'uso:
- 🔴 App Vulnerabile: [http://localhost:8080](http://localhost:8080)
- 🔵 App Sicura: [http://localhost:8081](http://localhost:8081)

*(Per resettare i database al loro stato originale: `docker compose down -v && docker compose up --build -d`)*

---

## ⚔️ Attacchi Implementati (Violazione C.I.A.)
L'intera catena di attacco è stata progettata seguendo uno scenario **Black Box**: l'attaccante ha _zero conoscenza_ a-priori (non conosce username validi, né il nome delle tabelle o del database). 

All'interno del file [`exploits/payloads.md`](./exploits/payloads.md) troverai una documentazione completa contenente i payload e la spiegazione per eseguire sequenzialmente i seguenti attacchi sull'app vulnerabile:

- 🔓 **Tautologia Universale / Bypass Autenticazione**: Accesso illegittimo come amministratore senza conoscere alcun username, tramite la manipolazione della logica SQL (Violazione della *Confidentiality*).
- 🕵️ **Information Gathering (Fase di Studio)**: Ricerca a tentativi del numero di colonne ed esplorazione "cieca" dello schema tramite l'uso di `UNION SELECT` sulle viste di `information_schema` per scoprire le tabelle e i campi nascosti.
- 🩸 **Esfiltrazione Dati**: Estrazione mirata di informazioni sensibili (email, password) di tutti gli utenti, una volta scoperta la struttura (Violazione della *Confidentiality*).
- ✏️ **Piggybacked Queries (Modifica di Massa)**: Alterazione non autorizzata e massiva di tutti i voti all'interno del database senza conoscere gli ID specifici degli studenti (Violazione dell'*Integrity*).
- 🗑️ **Piggybacked Queries (Cancellazione di Massa)**: Rimozione malevola dell'intero contenuto della tabella dei voti per causare un disservizio permanente (Violazione dell'*Availability*).

---

## 🛡️ Difese Implementate

Il progetto prevede contromisure applicate su due diversi livelli di astrazione:

### 1. Livello Web Application (PHP/PDO)
L'app sicura adotta **Prepared Statements** con l'ausilio della libreria PDO. Invece di concatenare le stringhe input direttamente nella query, vengono utilizzati dei *placeholder*. In questo modo il driver del database sanitizza le stringhe impedendo che il testo inserito venga mai processato come codice SQL eseguibile.

### 2. Livello Database (Principio del Minimo Privilegio)
A livello architetturale, l'app sicura è disaccoppiata da quella vulnerabile. Utilizza un database parallelo (`sql_injection_secure_db`) interrogato da uno specifico utente configurato tramite lo script [`02_secure_user.sql`](./db/02_secure_user.sql). 
A tale utente (`lab_user_secure`) vengono concessi esclusivamente i permessi di `SELECT`, `INSERT` e `UPDATE`, negando esplicitamente i permessi distruttivi come `DROP` e `DELETE`. Questo assicura che, persino in presenza di vulnerabilità 0-day lato codice, gli attacchi mirati alla cancellazione dei dati falliranno a livello del motore DB.

---

## 📁 Struttura della Repository
```text
├── app/
│   ├── sicura/          # 🔵 Codice sorgente dell'app protetta
│   └── vulnerabile/     # 🔴 Codice sorgente dell'app vulnerabile
├── db/                  # 🗄️ Script SQL per l'inizializzazione del database
├── exploits/            # ⚔️ Payload e istruzioni per condurre gli attacchi
├── docker-compose.yaml  # 🐳 Configurazione dei servizi Docker
└── README.md            # 📖 Questo file
```

---
<div align="center">
  <b>Disclaimer:</b> <i>Questo progetto ha uno scopo puramente educativo. Non utilizzare le tecniche qui descritte su sistemi senza un esplicito consenso.</i>
</div>
