FROM ghcr.io/digininja/dvwa:latest

COPY vulnerabilities/csrf/ /var/www/html/vulnerabilities/csrf/
