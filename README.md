# Veloce Studio

A PHP and MySQL website for an automotive customisation studio, with customer registration/login, a browser-based cart, checkout address capture, and an administrator dashboard.

## Local setup

1. Install PHP 8.1+ and MySQL/MariaDB (XAMPP is suitable for local development).
2. Create the database by importing `database/schema.sql`.
3. Configure `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, and `DB_PASSWORD` in your web-server environment if your local defaults differ.
4. Start PHP's development server from this folder: `php -S localhost:8000`.
5. Open `http://localhost:8000/index.html`.

To create an administrator, first generate a password hash using `php make_hash.php`, then insert it into `admin_users` with your preferred email and name.

## Notes

- Do not commit `.frm` or `.ibd` files. They are MySQL storage artifacts, not portable database backups.
- Use `database/schema.sql` to create a fresh, portable database.
- The cart is intentionally stored in the browser's local storage. Customer and administrator passwords are stored as PHP password hashes.
