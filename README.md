# Aland POS

A Kurdish, right-to-left point-of-sale and sales-management app built with PHP, MySQL, and a browser-based interface.

## Requirements and setup

1. Run Apache and MySQL from XAMPP.
2. Place the project in the web root and open it through `http://localhost/aland_pos/`.
3. Ensure PHP's PDO MySQL extension is enabled. The MySQL account configured at the top of `index.php` must be able to create the `aland_pos` database on first run.
4. Sign in with the initial account `admin@aland.io` / `123456`, then change the password from **Settings**.

The application creates its tables automatically and inserts sample products and customers into an empty database. Existing databases are extended with the new purchasing, expense, order-line, and inventory-movement tables on startup.

## Features

- POS checkout with cash or card payments, tax calculation, stock checks, and printable receipts.
- Product and customer lists, with low-stock warnings on the dashboard.
- Supplier directory and multi-product purchase entry. Posting a purchase records its supplier and item lines, increases on-hand quantities, records stock movements, and updates each product's weighted average cost.
- Operating-expense entry with category and date.
- Date-filtered sales, gross-profit, purchase-spend, and expense reports, with CSV export.

Purchase spend is reported separately from operating expenses: inventory cost is already reflected in gross profit when the products are sold, so subtracting purchase spend again would double-count it.

The default account is for initial local setup only. Change its password before using the application with real business data.
