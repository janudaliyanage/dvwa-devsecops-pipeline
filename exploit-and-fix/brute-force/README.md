## Brute Force Vulnerability – DVWA

## What I tested

I looked at the Brute Force module in DVWA (`/vulnerabilities/brute/`) at
the Low security setting to see how easy it is to guess a working login.
Turns out — very easy. There's nothing in the code stopping repeated
attempts, so a tool can just hammer the login form until it finds a
match.

## The code

Looking at `low.php`, the username and password come straight from the
URL (GET parameters), get hashed with md5, and get dropped into a SQL
query:

```php
$user = $_GET['username'];
$pass = $_GET['password'];
$pass = md5($pass);
$query = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";
```

No delay, no attempt counter, nothing. Also worth noting the credentials
go out as part of the URL, so they'd end up sitting in your browser
history and any server/proxy logs in plain text.

## How I attacked it

Used Hydra with the rockyou.txt wordlist (the one that ships with Kali,
~14.3 million passwords) against the local DVWA instance running on
port 4280:

```bash
hydra -l admin -P /usr/share/wordlists/rockyou.txt 127.0.0.1 -s 4280 http-get-form \
"/vulnerabilities/brute/:username=^USER^&password=^PASS^&Login=Login:H=Cookie: security=low; PHPSESSID=<session>:F=Username and/or password incorrect"
```

The `-l admin` targets the admin account, `-P` points at the wordlist,
and the `F=` bit tells Hydra what a *failed* login looks like so it
knows when it's found one that isn't a failure.

## Result

It found the password (`password`) in about 2 seconds — basically the
first thing it tried, since it's near the top of rockyou.txt. No
lockout, no CAPTCHA, no slowdown, nothing stopping it.

## Why this matters

Anyone who can reach this login page over the network could do the same
thing. If the real password had been something weak but not the literal
top of a wordlist, it still would've just been a matter of time — Low
security here does nothing to make that time longer.

## What would actually fix it

- **Rate limiting** – block or delay repeated attempts from the same
  source. DVWA's Medium level does this with a basic `sleep(2)` on every
  attempt, which is a start but still brute-forceable, just slower.
- **Account lockout** – lock the account for a while after too many
  wrong tries.
- **CAPTCHA** – forces a human to be involved, which stops automated
  tools cold.
- **CSRF tokens** – DVWA's High level adds this, and it actually breaks
  a naive Hydra attack like the one I ran, since the tool doesn't grab
  and resend a fresh token each time.
- **Use POST instead of GET** – keeps credentials out of URLs and logs.
- **Don't confirm which part was wrong** – "username and/or password
  incorrect" is fine (it doesn't tell you which one), but it's worth
  double-checking DVWA doesn't leak more elsewhere.

## Quick comparison

Low was trivial — cracked instantly. Medium slows things down with the
sleep delay but is still crackable given enough time. High stopped my
Hydra attempt outright because of the CSRF token, which would need a
different approach (grabbing a fresh token per request) to get around.
