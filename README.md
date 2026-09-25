# DVWA DevSecOps Pipeline

**IE3142 – DevOps Security | Group Assignment**

A containerized, security-hardened deployment of Damn Vulnerable Web App (DVWA), built as part of the IE3142 DevOps Security module.

## Application

- **Name:** DVWA (Damn Vulnerable Web App)
- **Source:** https://github.com/digininja/DVWA
- **Stack:** PHP/Apache (web app) + MariaDB (database)
- ** Containerization:** Docker, via DVWA's official `compose.yml`

## Prerequisites

- Docker (or Podman with Docker CLI compatibility)
- Docker Compose

## Setup & Run

1. Clone this repository:
```bash
   git clone https://github.com/janudaliyanage/dvwa-devsecops-pipeline.git
   cd dvwa-devsecops-pipeline
```

2. Create your `.env` file from the provided example:
```bash
   cp .env.example .env
```
   Then edit `.env` and set your own values for `DB_ROOT_PASSWORD`, `DB_USER`, and `DB_PASSWORD`.

3. Start the application:
```bash
   docker compose up -d
```
   On the first run, this pulls the `dvwa` and `mariadb` images, which may take a few minutes.

4. Open DVWA in your browser: http://localhost:4280

5. Log in with the default credentials:
   - Username: `admin`
   - Password: `password`

6. On first login, go to **Setup / Reset DB** and click **Create / Reset Database** to initialize the database tables.

7. Go to **DVWA Security** and set the security level to **Low** for baseline vulnerability testing.

8. 8. To stop the containers:
```bash
   docker compose down
```

## Secrets Management

No credentials are hardcoded in `compose.yml`. Database credentials are read from environment variables (`${DB_ROOT_PASSWORD}`, `${DB_USER}`, `${DB_PASSWORD}`), sourced from a local `.env` file that is excluded from version control via `.gitignore`. `.env.example` is provided as a template only — it contains no real credentials.

## Repository Structure
.
├── compose.yml # Container orchestration (DVWA + MariaDB)
├── .env.example # Template for required environment variables
├── .gitignore
└── README.md

