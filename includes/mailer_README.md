PHPMailer / Mailer helper setup

1) Recommended: install PHPMailer via Composer in project root:

```bash
composer require phpmailer/phpmailer
```

This will create `vendor/` and `vendor/autoload.php` which `includes/mailer.php` will auto-load.

2) Configure SMTP credentials in `includes/mailer.php`:
- set `$smtpUser` to your Gmail address
- set `$smtpPass` to an App Password (if using Gmail with 2FA)
- adjust `$smtpHost`, `$smtpPort`, `$smtpSecure` if needed

3) If you cannot use PHPMailer, PHP `mail()` is used as a fallback but requires proper `php.ini` SMTP settings and is often unreliable on local Windows/XAMPP.

4) Testing email locally:
- Use an SMTP testing tool (MailHog, Mailtrap) and point `smtpHost` and credentials accordingly.

5) Security:
- Do NOT commit real SMTP credentials to source control.
- Prefer environment variables or a config file outside webroot for credentials.

6) Notes about Gmail:
- If your Google account has 2-step verification enabled, create an App Password and use it for `$smtpPass`.
- If you don't use 2-step verification, Google may block sign-in from less secure apps; using App Passwords is recommended.

7) Troubleshooting:
- If emails don't arrive, check SMTP logs, spam folder, and ensure port 587 (TLS) or 465 (SSL) is allowed by your network.

