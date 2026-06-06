# StudentPlacementAssistant (DGS Tercih Robotu)

A comprehensive web-based application designed to assist students in Turkey with their **DGS (Dikey Geçiş Sınavı)** university choices. It allows users to browse university data, track quotas and minimum scores, create personalized preference lists, and take notes for specific universities.

## Features

- **DGS Specific Data**: Tailored for DGS students with up-to-date 2024 university scores, departments, and quotas.
- **User Authentication**: Secure user registration, login, and session management.
- **Advanced Filtering**: Filter universities by department categories (e.g., Computer Engineering, Software Engineering) and min/max score ranges.
- **Preference Lists (Tercih Listeleri)**: Create, manage, and arrange custom preference lists. Save them securely to your account.
- **Import & Export**: Export your preference lists as `.txt` files or import them from your device.
- **Note Taking (Notlarım)**: Add private, personalized notes to any university or department to aid in decision-making.
- **Dark Mode Support**: Built-in toggle for light and dark themes for better user experience.
- **Data Import (Admin)**: Easily update the database by importing data from `CSV` files (`universiteler.csv`, `yeni_kontenjanlar.csv`) via the provided import scripts.

## Technologies Used

- **Frontend**: 
  - HTML5 & CSS3
  - **Bootstrap 5** for a fully responsive and modern UI layout.
  - **Bootstrap Icons** for UI iconography.
  - **DataTables** for interactive, searchable, and paginated data grids.
  - **jQuery & Vanilla JavaScript** for asynchronous API calls and DOM manipulation.
- **Backend**:
  - **PHP** for server-side logic and RESTful API endpoints (located in the `api/` directory).
- **Database**:
  - **MySQL** for structured data storage (relational schema).

## Project Structure

- `api/` - Contains PHP endpoints for fetching/saving lists, notes, and universities.
- `partials/` - Reusable UI components like headers and footer scripts.
- `sql.sql` - Database schema and table structures.
- `config.php` - Database connection configuration.
- `*.php` - Main views (index, login, register, note management, list management).
- `*.js` - Client-side scripts handling user interactions.

## Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/semihgny/StudentPlacementAssistant.git
   cd StudentPlacementAssistant
   ```

2. **Set up your local server:**
   Place the project folder into your local web server's root directory (e.g., `htdocs` for XAMPP, `www` for WAMP, or `/var/www/html` for LAMP).

3. **Database Configuration:**
   - Open phpMyAdmin or your preferred MySQL client.
   - Create a new, empty database.
   - Import the provided `sql.sql` file into your newly created database to set up the tables.

4. **Update Credentials:**
   Open `config.php` and update the database connection details with your own credentials:
   ```php
   $servername = "localhost";
   $username = "YOUR_DB_USERNAME"; 
   $password = "YOUR_DB_PASSWORD"; 
   $dbname = "YOUR_DB_NAME"; 
   ```

5. **Populate Data (Optional):**
   Navigate to `import.php` or `import_yeni.php` via your browser to populate your database with the provided CSV files (`universiteler.csv`, `yeni_kontenjanlar.csv`).

6. **Run the Application:**
   Open your browser and navigate to the project directory:
   ```text
   http://localhost/StudentPlacementAssistant
   ```

## License

Distributed under the MIT License. See `LICENSE` for more information.
