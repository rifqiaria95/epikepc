# Certificate Gallery

## Data model

`certificates` table stores company certification records with UUID primary keys, publication status, display order, image paths, and audit fields.

## CMS workflow

1. Create certificate as draft with image + alt text.
2. Publish via explicit action or status change with validation.
3. Reorder via `/internal/certificates/reorder`.
4. Archive or soft delete from CMS.

## Publication rules

Frontend visibility requires:

- `status = PUBLISHED`
- `published_at` null or in the past
- not soft deleted
- optional expiry filter via `CERTIFICATE_SHOW_EXPIRED`

## Homepage query

`CertificateHomepageQuery` loads published certificates in one query with URL presentation.

## Gallery sets

`CertificateSetBuilder` (PHP) and `certificate-gallery.js` (JS) chunk items by viewport:

- xl: 10, lg: 8, md: 6, sm: 3

## Gesture behavior

Vertical drag on certificate cards changes entire set. Wheel scroll is not intercepted.

## Permissions

- `view_certificates`, `create_certificates`, `edit_certificates`, `publish_certificates`, `reorder_certificates`, `delete_certificates`

## Configuration

See `config/certificates.php`.

## Deployment

```bash
php artisan migrate
php artisan db:seed --class=CertificatePermissionSeeder
php artisan db:seed --class=CertificateMenuSeeder
php artisan storage:link
```

## Rollback

```bash
php artisan migrate:rollback --step=1
```

## Known limitations

- Thumbnail generation requires PHP GD extension.
- JS interaction tests rely on manual QA matrix; set builder has PHP unit coverage.
