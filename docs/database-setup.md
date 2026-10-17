# Database setup status

## Local development database

Talas is configured to use a separate XAMPP MariaDB 10.4.32 instance through CodeIgniter's `MySQLi` driver. Its data directory is `C:\xampp\mysql\data-talas`, its private config is `C:\xampp\mysql\bin\my-talas.ini`, and it listens only on `127.0.0.1:3307`. Database files and the config are outside the repository and web root. The instance was started as a local process, not installed as a Windows service.

The `talas_local` database uses `utf8mb4`. The application connects with the dedicated `talas_app` account, which has privileges only on `talas_local.*`. Its generated password is stored only in the ignored root `.env`; do not commit, upload, or print that file. The root `.htaccess` denies dotfiles. The `.env` is ignored by Git.

The original `talas_local` database and its `3307` server were used for initial schema work. The shared XAMPP MariaDB data directory later showed a corrupt system table; preserve it and do not use it for new migration verification. The review database described below is the active isolated target for local browser and schema review. The initial default `php spark migrate` attempt failed because it ran the app migration before Shield created `users`; it applied no migrations. Using `--all` then ran migrations in timestamp order and completed successfully. The local `php.ini` does not enable `intl` for CLI by default, so run Spark commands with `-d extension=intl`, for example:

```powershell
C:\xampp\php\php.exe -d extension=intl -d auto_prepend_file=.tools/talas-review-env.php spark migrate:status --all
```

To start the isolated instance after it has stopped:

```powershell
Start-Process -FilePath C:\xampp\mysql\bin\mysqld.exe -ArgumentList '--defaults-file=C:\xampp\mysql\bin\my-talas.ini' -WindowStyle Hidden
```

**Current startup issue (2026-10-16):** A local UI-review startup attempt failed because MariaDB reported `Index for table '.\\mysql\\db' is corrupt` and could not open the privilege tables. The web app consequently returned HTTP 500 while connecting to MariaDB. No repair, table replacement, or reinitialization was attempted. Preserve `data-talas` and review a recovery plan before making changes to it.

**Temporary local review setup (2026-10-16; updated 2026-10-17):** To keep the existing directory untouched, a separate MariaDB 10.4.32 instance is running on `127.0.0.1:3308` with its data directory and connection override under ignored `.tools/`. All current migrations through `2026-10-17-000005_CreateCheckIns` are applied to its separate `talas_review` database; `migrate:status --all` confirmed the check-in migration in batch 4 at 2026-10-17 02:35 UTC. It contains synthetic student data for review. The web server runs at `http://127.0.0.1:8080` using the temporary review router. The repository `.env` and the existing `data-talas` directory were not changed by this setup. Keep the local server and review database running until owner review is complete.

## Run the local web app

Start CodeIgniter's development server directly with XAMPP PHP so the `intl` extension flag is present in the web-server process. Running `spark serve` with `-d extension=intl` starts a child server that does not inherit that flag on this setup.

```powershell
& C:\xampp\php\php.exe -d extension=intl -S 127.0.0.1:8080 -t public vendor\codeigniter4\framework\system\rewrite.php
```

Open `http://127.0.0.1:8080/login` in a browser. The review router connects to the isolated review database on port 3308; do not target the corrupt 3307 instance or the original shared XAMPP database on port 3306 for this app.

The original shared XAMPP data directory remains separate and was not repaired or initialized over. A read-only check reported its `mysql.db` system table corrupt (Aria error 176). Do not run `REPAIR TABLE`, replace system-table files, or initialize over that existing directory as ordinary app setup; recovery could affect other local databases and accounts. Its earlier loopback-only configuration backup is `C:\xampp\mysql\bin\my.ini.talas-backup`.

The PHPUnit connection remains separate and uses in-memory SQLite. Do not point test configuration at production or the hosted database. Hosted database creation and import verification remain pending.

## InfinityFree database

Hosted database creation and phpMyAdmin import remain pending. Create the database through the hosting panel when ready, then configure hosted credentials separately through the documented protected deployment method. Never reuse the local `.env`, local database user, or local password on the host.
