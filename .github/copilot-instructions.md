# LucasPro QR Drive - AI Coding Instructions

## Project Overview
LucasPro QR Drive is a PHP application that generates QR codes linking to Google Drive folders for photo event management. The workflow: event name → OAuth with Google → Drive folder creation → dynamic QR code generation → notification emails.

## Critical Architecture & Data Flow

### OAuth Flow (Multi-File Pattern)
- **index.php** or **create_event.php**: Entry point, captures event name via form, initiates Google OAuth
- **drive_callback_test.php**: Receives OAuth callback (`code` param), exchanges for token, creates Drive folder, generates QR code
- **notify.php**: Public-facing endpoint, triggered via QR scan, emails notification and redirects to Drive folder

Key pattern: Session storage (`$_SESSION['access_token']`, `$_SESSION['event_name']`) persists across redirects.

### Environment Configuration
- **Local development**: Uses `credentials.json` (ignored in git, must be provided locally)
- **Production (Render)**: Loads `GOOGLE_CREDENTIALS_JSON` env var, writes to temp directory
- See [index.php#L3-L13](index.php#L3-L13) and [drive_callback_test.php#L9-L17](drive_callback_test.php#L9-L17) for pattern

### URL/Domain Detection Pattern
All files detect scheme (HTTP/HTTPS) and host dynamically - required for Render deployment:
```php
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$baseUrl = $scheme . '://' . $_SERVER['HTTP_HOST'];
```

## Dependencies & Integration Points

### Google APIs (Guzzle + Client)
- **guzzlehttp/guzzle**: HTTP transport (7.7)
- **google/apiclient**: OAuth2, Drive API (2.14)
- Scopes: `Google_Service_Drive::DRIVE` only (full folder access)
- Access type: `offline` (for refresh tokens)

### QR Code Generation
- **phpqrcode/qrlib.php**: Embedded QR library, used in [drive_callback_test.php#L56-L60](drive_callback_test.php#L56-L60)
- Pattern: Generate to buffer, encode as base64 data URI (avoids disk writes in production)
- Error level: `QR_ECLEVEL_L` (lowest), size: `6`

### PHP Sessions & Email
- Sessions start early (all files call `session_start()`) before output
- Email via native `mail()` function with proper headers; fails silently in sandboxed environments

## Project Conventions & Patterns

### File Naming & Entry Points
- Single-file endpoints: `create_event.php`, `notify.php`, `drive_callback_test.php` (no routing framework)
- No classes/namespaces; procedural PHP throughout
- Comments in French (developer's language choice)

### Error Handling
- OAuth errors: Check `$token['error']` and `$token['error_description']` after `fetchAccessTokenWithAuthCode()`
- Silent failures: Email sending wrapped in `@mail()` (intentional suppression for web environments)
- Redirect security: Use `header('Location: ...')` immediately before `exit`

### Input Validation
- Form input: Sanitize with `htmlspecialchars()` for output safety (see [notify.php#L3-L4](notify.php#L3-L4))
- GET params: Always pass through `urlencode()` when building URLs

### UI/Styling
- Dark theme with gold accents (`#ffd700`)
- Background slideshow animation cycles through assets/ images
- Styled as single-page forms (see [index.php#L42-L100](index.php#L42-L100))

## Essential Developer Workflows

### Setup
1. Install: `composer install` (requires Guzzle & Google client)
2. Configure: Add `credentials.json` (OAuth service account from Google Cloud Console)
3. Test locally: `php -S localhost:8000`, access via `http://localhost/lucaspro_qr_drive_final/index.php`

### Debugging OAuth Issues
- Check `credentials.json` redirect_uri matches app config
- Verify `GOOGLE_CREDENTIALS_JSON` env var is valid JSON on Render
- Session cookies: Ensure `session_start()` called before any output

### Adding New Events/Features
- New entry point? Call `session_start()` first, detect URL scheme/host, initialize Google client
- Modify Drive folder creation? Edit [drive_callback_test.php#L36-L45](drive_callback_test.php#L36-L45)
- Change QR content? Update `$notifyUrl` construction before QR generation

## Key Files Reference
- [index.php](index.php) - Primary entry or alternative creation workflow
- [create_event.php](create_event.php) - Event creation form + OAuth redirect
- [drive_callback_test.php](drive_callback_test.php) - OAuth callback handler, folder/QR generation
- [notify.php](notify.php) - Public notification/redirect endpoint
- [credentials.json](credentials.json) - OAuth config (git-ignored)

## Common Pitfalls
1. Forgetting `session_start()` before accessing `$_SESSION`
2. Hardcoded `localhost` URIs instead of dynamic URL detection → breaks Render deployment
3. Missing base64 encoding for QR → browser can't display inline data URI
4. Not URL-encoding event names in query strings → special chars corrupt the link
