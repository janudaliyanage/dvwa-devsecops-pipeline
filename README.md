# DVWA DevSecOps Pipeline

**IE3142 – DevOps Security | Group Assignment**

A containerized and security-focused deployment of **Damn Vulnerable Web Application (DVWA)** developed as part of the IE3142 DevOps Security module.

The project demonstrates the identification, testing, remediation, and verification of common web application vulnerabilities, together with security checks integrated into the development and CI/CD process.

---

## Application

- **Application:** DVWA (Damn Vulnerable Web Application)
- **Source:** https://github.com/digininja/DVWA
- **Web Stack:** PHP / Apache
- **Database:** MariaDB
- **Containerization:** Docker / Docker Compose
- **Default URL:** `http://localhost:4280`

---

## Project Objectives

The main objectives of this project are to:

1. Identify common web application vulnerabilities in DVWA.
2. Demonstrate vulnerabilities in a controlled local environment.
3. Implement appropriate security remediation.
4. Perform static and dynamic security testing.
5. Document before-and-after testing results.
6. Integrate security checks into the CI/CD process.
7. Maintain clear evidence of vulnerabilities, remediation, and verification.

---

## Security Vulnerabilities

| Vulnerability | Live Application Code | Documentation & Evidence |
|---|---|---|
| SQL Injection | `vulnerabilities/sqli/source/low.php` | `exploit-and-fix/sql-injection/` |
| Stored Cross-Site Scripting (XSS) | `vulnerabilities/xss_s/source/low.php` | `exploit-and-fix/xss-stored/` |
| Cross-Site Request Forgery (CSRF) | `vulnerabilities/csrf/` | `exploit-and-fix/csrf/` |
| Brute Force | `vulnerabilities/brute/` | `exploit-and-fix/brute-force/` |

The `vulnerabilities/` directory contains the live, running application source that the Docker container serves and that the CI/CD pipeline scans. The `exploit-and-fix/` directory holds a before/after reference copy of each fix plus supporting evidence (screenshots, SAST results, test notes) for the technical report.

---

## DevSecOps Approach

```text
Identify Vulnerability
        ↓
Document Vulnerable Code
        ↓
Security Testing
        ↓
Implement Remediation
        ↓
Static Analysis
        ↓
Dynamic Testing
        ↓
Collect Evidence
        ↓
CI/CD Security Checks
```

This approach integrates security activities throughout the development and testing process rather than treating security as a final-stage activity.

---

## Prerequisites

- Git
- Docker
- Docker Compose

Podman with Docker CLI compatibility can also be used.

---

## Setup & Run

### 1. Clone the Repository

```bash
git clone https://github.com/janudaliyanage/dvwa-devsecops-pipeline.git
cd dvwa-devsecops-pipeline
```

### 2. Configure Environment Variables

```bash
cp .env.example .env
```

Edit `.env` and configure your own values for:

```text
DB_ROOT_PASSWORD
DB_USER
DB_PASSWORD
```

Do not commit the `.env` file to the repository.

### 3. Start the Application

```bash
docker compose up -d
```

On the first run, Docker may need to download the required images, which can take a few minutes.

### 4. Open DVWA

```text
http://localhost:4280
```

### 5. Login

```text
Username: admin
Password: password
```

### 6. Initialize the Database

After logging in:

1. Go to **Setup / Reset DB**.
2. Click **Create / Reset Database**.

### 7. Set the DVWA Security Level

Go to **DVWA Security → Security Level → Low**. The Low security level is used as the baseline for vulnerability testing.

### 8. Stop the Application

```bash
docker compose down
```

---

## Repository Structure

```text
dvwa-devsecops-pipeline/
│
├── .github/
│   └── workflows/
│       └── security.yml              # CI/CD pipeline: SAST, dependency, secrets, image scanning
│
├── vulnerabilities/                   # Live DVWA application source (served by the container)
│   ├── sqli/source/low.php
│   ├── xss_s/source/low.php
│   └── csrf/
│       ├── index.php
│       └── source/low.php
│
├── exploit-and-fix/                   # Before/after reference copies + evidence per vulnerability
│   ├── sql-injection/
│   │   ├── vulnerable/low.php
│   │   └── fixed/low.php
│   ├── xss-stored/
│   │   ├── vulnerable/low.php
│   │   ├── fixed/low.php
│   │   └── evidence/
│   ├── csrf/
│   │   ├── vulnerable/low.php
│   │   ├── fixed/low.php
│   │   └── evidence/
│   ├── brute-force/
│   │   ├── README.md
│   │   └── vulnerable/low.php
│   └── ci-cd-pipeline/
│       ├── Dockerfile
│       └── security.yml
│
├── evidence/
│   └── sast/
│       ├── semgrep-before.json
│       └── semgrep-after.json
│
├── docs/
│   └── architecture-diagram.png
│
├── config/
│   └── config.inc.php
│
├── compose.yml
├── Dockerfile
├── .env.example
├── .gitignore
└── README.md
```

---

## Vulnerability Documentation Structure

Each vulnerability under `exploit-and-fix/` follows a consistent structure:

```text
exploit-and-fix/<vulnerability>/
│
├── README.md          (where present)
├── vulnerable/
│   └── low.php
├── fixed/
│   └── low.php
└── evidence/
    ├── before-test evidence
    ├── after-test evidence
    └── SAST results
```

This separates the original vulnerable implementation from the remediated implementation and the evidence used to verify the change.

---

## SQL Injection

Documented under `exploit-and-fix/sql-injection/`.

The remediation replaces string-concatenated SQL with parameterized/prepared queries, covering both the MySQL and SQLite code paths in `low.php`, so user input is always bound as data and never interpreted as SQL syntax.

---

## Stored Cross-Site Scripting (XSS)

Documented under `exploit-and-fix/xss-stored/`.

The remediation applies HTML encoding to user-controlled input before it is stored:

```php
htmlspecialchars($input, ENT_QUOTES, 'UTF-8')
```

combined with a parameterized insert query. The same payload was tested before and after remediation to verify the fix.

---

## Cross-Site Request Forgery (CSRF)

Documented under `exploit-and-fix/csrf/`.

The remediation adds anti-CSRF token validation (`checkToken()`) before the password-change request is processed, and converts the password-update query to a parameterized statement.

---

## Brute Force

Documented under `exploit-and-fix/brute-force/`.

The vulnerable implementation demonstrates repeated login attempts against the DVWA Low security level with no rate limiting or lockout. The documentation discusses mitigating controls, including rate limiting, account lockout, CAPTCHA, and avoiding credentials in URLs by using POST requests.

---

## Static Application Security Testing (SAST)

The project uses **Semgrep** for static analysis, run both locally and as part of the CI/CD pipeline's `sast` job.

Evidence is maintained as before/after scan results:

```text
evidence/sast/semgrep-before.json
evidence/sast/semgrep-after.json
```

The `exploit-and-fix/` evidence folders are excluded from the pipeline's SAST scan, since they intentionally contain unfixed reference copies for documentation purposes rather than live application code.

---

## Dynamic Security Testing

Dynamic testing is performed against the locally running DVWA application and may include:

- Before-remediation screenshots
- After-remediation screenshots
- Controlled proof-of-concept payloads
- Security tool output

---

## CI/CD Security Pipeline

Security checks run automatically on every push and pull request against `main`, defined in:

```text
.github/workflows/security.yml
```

The pipeline enforces four automated security gates:

| Gate | Tool | What it checks |
|---|---|---|
| SAST | Semgrep (`p/php`) | Injection, XSS, unsafe function-call patterns in application source |
| Dependency scanning | Trivy (filesystem scan) | Known CVEs across repository files |
| Secrets scanning | Gitleaks | Credential-shaped strings in commit history |
| Container image scanning | Trivy (image scan) | OS and library CVEs in the built Docker image |

The `image-scan` gate is configured to genuinely fail the build (`exit-code: 1`) on any CRITICAL-severity finding, satisfying the requirement that at least one gate block the pipeline on a real issue rather than only warn.

---

## Secrets Management

No real credentials are stored in the repository. Database credentials are supplied through environment variables:

```text
DB_ROOT_PASSWORD
DB_USER
DB_PASSWORD
```

The local `.env` file is excluded from version control via `.gitignore`. `.env.example` is provided as a configuration template with no real credentials.

---

## Technology Stack

| Technology | Purpose |
|---|---|
| DVWA | Vulnerable web application used for security testing |
| PHP / Apache | Web application environment |
| MariaDB | Database |
| Docker / Docker Compose | Containerization and orchestration |
| Git / GitHub | Version control and collaboration |
| GitHub Actions | CI/CD automation |
| Semgrep | Static Application Security Testing |
| Trivy | Dependency and container image scanning |
| Gitleaks | Secrets scanning |

---

## Security and Ethical Testing

All vulnerability testing in this project is performed against the project's controlled local DVWA environment, for educational and authorized testing purposes as part of the IE3142 DevOps Security group assignment.

---

## Project Information

**Module:** IE3142 – DevOps Security
**Project:** DVWA DevSecOps Pipeline
**Repository:** `janudaliyanage/dvwa-devsecops-pipeline`
