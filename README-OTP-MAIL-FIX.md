# Super Admin OTP email — code fix + how to actually get it sending

## What was actually broken (recap from your logs)
Your storage/logs/laravel.log showed Gmail rejecting the SMTP login outright:
  535-5.7.8 Username and Password not accepted
That's not a code bug — it's an invalid/revoked Gmail App Password. No
amount of PHP code fixes a rejected credential. You have to:

1. Google Account -> Security -> 2-Step Verification -> App passwords
   -> generate a NEW 16-character app password.
2. Put it in MAIL_PASSWORD on wherever the app actually runs:
   - Local: your .env file
   - Railway (production): Project -> your service -> Variables tab
     (Railway does NOT read your repo's .env for production)
3. php artisan config:clear (config is cached; editing .env alone often
   isn't enough)

## What I fixed in code (this package)
Two real bugs, independent of the credential issue above:

1. app/Http/Controllers/Api/AuthController.php
   - Both places that email the OTP (`sendResetLink` and
     `sendSuperAdminCode`) called `Mail::to()->send()` directly with no
     try/catch. If SMTP fails, that throws an uncaught exception and the
     user just sees a raw Laravel error page — not "check your email",
     not "something went wrong", just a crash.
   - Pulled both into one `sendSuperAdminOtp()` helper that wraps the send
     in try/catch, logs the real error for you, and returns a friendly
     message to the user ("We could not send your verification code
     right now...") instead of a 500 page. One place to fix/upgrade the
     sending logic from now on instead of two.

2. app/Http/Controllers/SuperAdminController.php
   - `testSmtpConnectionApi()` was MISSING entirely — the route
     (`/super-admin/settings/smtp-test`) already pointed to it, so
     clicking that button in your settings page would 500. Implemented
     it: it actually attempts to send a real test email through your
     current mail config and returns the exact transport error (bad
     credentials, connection timeout, etc.) as JSON — so you (or any
     university's Super Admin) can self-diagnose "why isn't OTP arriving"
     from the dashboard instead of needing server log access.

## Recommended next step: move off personal Gmail SMTP
You're building a platform for MANY universities. Gmail SMTP has hard
limits (500 emails/day, ~100 recipients per message) and Google actively
flags "personal Gmail account sending transactional email on behalf of a
website" as suspicious — you risk the account getting locked, which would
take down OTP delivery for every university on the platform at once.

Use a real transactional email provider instead (all have generous free
tiers and work as a drop-in .env swap, no further code changes needed
since your app already just uses Laravel's Mail facade):

  # Example: Resend
  MAIL_MAILER=smtp
  MAIL_HOST=smtp.resend.com
  MAIL_PORT=587
  MAIL_USERNAME=resend
  MAIL_PASSWORD=your_resend_api_key
  MAIL_ENCRYPTION=tls
  MAIL_FROM_ADDRESS=noreply@yourplatformdomain.com
  MAIL_FROM_NAME="OnlineExam"

Mailgun, Postmark, and AWS SES work the same way — swap MAIL_HOST /
MAIL_USERNAME / MAIL_PASSWORD for the ones they give you. Also note
MAIL_FROM_ADDRESS should be on a domain you control (e.g.
noreply@yourplatform.com) once you set this up, not a personal Gmail —
you'll need to verify that domain with whichever provider you pick.
