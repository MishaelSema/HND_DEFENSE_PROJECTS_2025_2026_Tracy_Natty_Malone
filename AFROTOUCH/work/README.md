# AfroTouch Seller Dashboard - PHP Implementation

This project contains a premium, responsive seller dashboard built with PHP, HTML, CSS, and Chart.js.

## Files
- `index.php`: Redirects to the dashboard.
- `dashboard.php`: Analytics overview with interactive charts.
- `products.php`: Product management grid with filtering.
- `orders.php`: Order management table with details modal.
- `style.css`: Core design system and responsive styles.
- `components/`: Reusable UI components (Sidebar, Header, Layout).

## How to View (WAMP Server)

Since you have WAMP active, follow these steps:

1.  **Locate your WAMP `www` folder**: Usually at `C:\wamp64\www`.
2.  **Move the files**: Copy the `TREACY` folder from your Desktop to `C:\wamp64\www\treacy`.
3.  **Access in Browser**: Open your browser and go to: `http://localhost/treacy/index.php`

### alternative method (PHP Built-in Server)
If you don't want to move files, you can run this command in your terminal inside the project folder:
```powershell
php -S localhost:8000
```
Then visit `http://localhost:8000` in your browser.

## Features
- **Responsive Design**: Adapts to mobile (burger menu) and desktop.
- **Interactive Charts**: Visual insights using Chart.js.
- **Order Details**: Interactive modal for viewing order specifics.
- **Dummy Data**: Pre-populated with realistic AfroTouch marketplace data.
