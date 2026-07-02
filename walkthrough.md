# Walkthrough Progetto SQL Injection

Tutti i servizi sono stati configurati e avviati correttamente tramite Docker.

## Servizi Avviati
*   **Database (MariaDB 10.5)**: (Porta esposta rimossa per evitare conflitti, accessibile internamente)
*   **Web App Vulnerabile (Tema Rosso)**: [http://localhost:42080](http://localhost:42080)
*   **Web App Sicura (Tema Blu)**: [https://localhost:42443](https://localhost:42443) (HTTPS)

---

## 🐍 Automazione Visiva della Demo (Consigliato)

Abbiamo preparato uno script Python che utilizza **Selenium** per automatizzare completamente la dimostrazione degli attacchi e delle difese aprendo una finestra del browser. 

**Prerequisiti:**
1. Assicurati che i container siano attivi (`docker compose up --build -d`).
2. **Windows / macOS:** Installa la dipendenza con `pip install selenium`.
   **Linux (Ubuntu/Mint):** Installa la dipendenza con `sudo apt install python3-pip python3-selenium`.

Puoi eseguire la demo con:
```bash
python exploits/demo_browser.py
```

Lo script offre un menu interattivo a 15 Fasi. Eseguendo la **Fase 15 (Modalità Automatica)** o esplorando le fasi manualmente (premendo INVIO ad ogni step), verranno dimostrati in tempo reale:

1. **Attacchi sull'App Vulnerabile**:
   - Bypass dell'Autenticazione senza password (Tautologia Universale).
   - Information Gathering (scoperta delle tabelle e colonne in cieco con `UNION`).
   - Esfiltrazione di massa delle password.
   - Modifica indiscriminata di tutti i voti nel DB (Piggybacked UPDATE).
   - Cancellazione dell'intero database dei voti (Piggybacked DELETE).

2. **Verifica delle Difese sull'App Sicura (Defense in Depth)**:
   - Tentativo di Bypass sul login bloccato con successo.
   - Accesso legittimo come **Alunno Verdi** (utente standard). I tentativi successivi di esfiltrazione e distruzione tramite la barra di ricerca vengono neutralizzati.
   - Accesso legittimo come **Admin Supremo** (utente privilegiato). Viene dimostrato che anche possedendo alti privilegi nell'app, se i *Prepared Statements* sono implementati correttamente, l'SQL Injection è impossibile.

---

## 📋 Sistema di Intrusion Detection (Audit Logging)

L'applicazione sicura include un meccanismo di monitoraggio attivo. Qualsiasi input che contiene pattern pericolosi (`UNION`, `UPDATE`, `OR 1=1`, ecc.) viene intercettato e registrato nel file `audit.log`.

**Come leggerlo:**
Grazie alla configurazione Docker, il file di log viene salvato dinamicamente **direttamente nella cartella del tuo progetto**. Non c'è bisogno di entrare nel container! Ti basta aprire il file `audit.log` presente qui fuori con il tuo editor di codice per vedere l'elenco degli IP e dei payload che hanno tentato di attaccare l'app sicura in tempo reale.

---

## 🛡️ Riassunto Difese Implementate

1. **Livello Applicativo (Prepared Statements - PDO):** Le stringhe iniettate dall'attaccante diventano semplici variabili letterali e non vengono mai eseguite dal motore SQL.
2. **Livello Database (Principio Minimo Privilegio):** L'utente del DB dell'app sicura ha permessi limitati (`SELECT`, `INSERT`, `UPDATE`). I comandi distruttivi come `DELETE` o `DROP` fallirebbero a livello di driver anche in caso di zero-day nell'applicazione.
3. **Livello Forense/Monitoraggio (Audit.log):** Il sistema traccia attivamente e responsabilizza ogni tentativo di attacco rilevato.
