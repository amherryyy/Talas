# Database setup status

## Local development database

Talas is configured to use a separate XAMPP MariaDB 10.4.32 instance through CodeIgniter's `MySQLi` driver. Its data directory is `C:\xampp\mysql\data-talas`, its private config is `C:\xampp\mysql\bin\my-talas.ini`, and it listens only on `127.0.0.1:3307`. Database files and the config are outside the repository and web root. The instance was started as a local process, not installed as a Windows service.

The `talas_local` database uses `utf8mb4`. The application connects with the dedicated `talas_app` account, which has privileges only on `talas_local.*`. Its generated password is stored only in the ignored root `.env`; do not commit, upload, or print that file. The root `.htaccess` denies dotfiles. The `.env` is ignored by Git.

The original `talas_local` database and its `3307` server were used for initial schema work. The shared XAMPP MariaDB data directory later showed a corrupt system table; preserve it and do not use it for new migration verification. The review database described below is the isolated target for local browser and schema review. The initial default `php spark migrate` attempt failed because it ran the app migration before Shield created `users`; it applied no migrations. Using `--all` then ran migrations in timestamp order and completed successfully. The local `php.ini` does not enable `intl` for CLI by default. On this Windows setup, load it by DLL name (`php_intl.dll`), for example:

```powershell
C:\xampp\php\php.exe -d extension=php_intl.dll -d auto_prepend_file=.tools/talas-review-env.php spark migrate:status --all
```

To start the isolated instance after it has stopped:

```powershell
Start-Process -FilePath C:\xampp\mysql\bin\mysqld.exe -ArgumentList '--defaults-file=C:\xampp\mysql\bin\my-talas.ini' -WindowStyle Hidden
```

**Current startup issue (2026-10-16):** A local UI-review startup attempt failed because MariaDB reported `Index for table '.\\mysql\\db' is corrupt` and could not open the privilege tables. The web app consequently returned HTTP 500 while connecting to MariaDB. No repair, table replacement, or reinitialization was attempted. Preserve `data-talas` and review a recovery plan before making changes to it.

**Temporary local review setup (2026-10-16; updated 2026-10-17):** To keep the existing directory untouched, a separate MariaDB 10.4.32 instance uses `127.0.0.1:3308` with its data directory and connection override under ignored `.tools/`. All current migrations through `2026-10-17-000005_CreateCheckIns` were applied to its separate `talas_review` database; `migrate:status --all` confirmed the check-in migration in batch 4 at 2026-10-17 02:35 UTC. It contains synthetic student data for review. The web server uses the temporary review router. The repository `.env` and the existing `data-talas` directory were not changed by this setup.

**Current local review status (2026-10-25):** The isolated review DB on port 3308 is reachable; `migrate:status --all` using `.tools/talas-review-env.php` confirmed all nine expected migrations. An unauthenticated `mysqladmin ping` returned access denied because it used the default Windows account without a password; this was not a connectivity failure. The running MariaDB process owns the review data files, so do not launch a duplicate. Several older PHP servers also occupy the usual 8080 port. The current local visual review used port 18080, `app.baseURL=http://localhost:18080/`, `php_intl.dll`, and a temporary review router with the development toolbar disabled. The corrupt shared XAMPP data directory remains untouched.

## Run the local web app

For the first local admin account, apply all migrations and run `C:\xampp\php\php.exe -d extension=php_intl.dll spark admin:create-local` from the repository root. This command is limited to development mode and a loopback database and refuses to create another admin. It prints a generated password once; keep it private and do not paste it into chat or commit it. Hosted admin provisioning is separate; the owner reports creating a synthetic admin account through phpMyAdmin and confirming its dashboard access.

When using the temporary review database on port 3308, the normal Spark command does not load its connection override. From the repository root, run `C:\xampp\php\php.exe -d extension=php_intl.dll .tools\run-review-admin-bootstrap.php` instead. This local helper loads the same loopback `talas_review` connection used by the browser review server, then invokes the guarded command. It prompts for the username and email and displays the generated password once.

Start CodeIgniter's development server directly with XAMPP PHP so the `intl` extension flag is present in the web-server process. Running `spark serve` starts a child server that does not inherit that flag on this setup. Choose an unused port; 8080 may already have another local PHP server.

```powershell
Set-Item Env:app.baseURL -Value 'http://localhost:18080/'
& C:\xampp\php\php.exe -d extension=php_intl.dll -S 127.0.0.1:18080 -t public .tools/talas-review-router-quiet.php
```

Open `http://localhost:18080/login` in a browser. The local review router loads the isolated review database on port 3308 and disables the development toolbar to avoid its HTML response being parsed as a script. Do not target the corrupt 3307 instance or the original shared XAMPP database on port 3306 for this app.

The original shared XAMPP data directory remains separate and was not repaired or initialized over. A read-only check reported its `mysql.db` system table corrupt (Aria error 176). Do not run `REPAIR TABLE`, replace system-table files, or initialize over that existing directory as ordinary app setup; recovery could affect other local databases and accounts. Its earlier loopback-only configuration backup is `C:\xampp\mysql\bin\my.ini.talas-backup`.

The PHPUnit connection remains separate and uses in-memory SQLite. Do not point test configuration at production or the hosted database. The owner reports the hosted database was created and the schema import succeeded on 2026-10-18; phpMyAdmin showed 15 tables and nine migration rows. On 2026-10-25, hosted registration, login, persistent session, and logout succeeded, confirming application database connectivity. A separate synthetic admin account was promoted through its Shield group row in phpMyAdmin; it reaches the aggregate admin dashboard, while the student account receives Forbidden on `/index.php?/admin`. The owner reports R-04 is all good; historical hosted evidence and the unverified `/.env` response are recorded in [`deployment.md`](deployment.md).

## InfinityFree database

The owner reports creating the hosted database and successfully importing the schema through phpMyAdmin on 2026-10-18. The reviewed schema-only file is [`duno-hosted-initial-schema.sql`](deployment.md#initial-hosted-database-import); it contains the current CodeIgniter, Shield, Settings, and Duno schema plus migration tracking rows, but no user or study data. Import it only once into that new, empty database. The owner confirms the 15-table count and nine-row migration ledger in phpMyAdmin. Hosted app connectivity was later demonstrated by student registration/login/session persistence. Configure host credentials in a separate root `.env` as described in `docs/deployment.md`; never reuse the local `.env`. Never reuse or upload the local `.env`, local database user, or local password.
