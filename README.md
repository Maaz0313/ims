# Inventory Management System (IMS)

## Project Setup

Follow these steps to set up the Inventory Management System (IMS) project on your local machine:

### Prerequisites

Ensure you have the following installed on your system:
- PHP >= 8.0
- Composer
- Node.js and npm
- MySQL
- A web server (e.g., Apache, Nginx, or Laravel's built-in server)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone <repository-url>
   cd ims
   ```

2. **Install PHP Dependencies**
   Run the following command to install the required PHP packages:
   ```bash
   composer install
   ```

3. **Install JavaScript Dependencies**
   Run the following command to install the required JavaScript packages:
   ```bash
   npm install
   ```

4. **Set Up Environment File**
   Copy the `.env.example` file to `.env` and update the environment variables as needed:
   ```bash
   cp .env.example .env
   ```
   Update the following variables in the `.env` file:
   - `DB_DATABASE`: Set the name of your MySQL database.
   - `DB_USERNAME`: Set your MySQL username.
   - `DB_PASSWORD`: Set your MySQL password.

5. **Generate Application Key**
   Run the following command to generate the application key:
   ```bash
   php artisan key:generate
   ```

6. **Run Migrations**
   Run the following command to create the database tables:
   ```bash
   php artisan migrate
   ```

7. **Seed the Database**
   (Optional) Run the following command to seed the database with sample data:
   ```bash
   php artisan db:seed
   ```

8. **Start the Development Server**
   Run the following command to start the Laravel development server:
   ```bash
   php artisan serve
   ```
   The application will be accessible at `http://localhost:8000`.

### Additional Commands

- **Run Tests**
  To run the test suite, use:
  ```bash
  php artisan test
  ```

- **Clear Cache**
  To clear the application cache, use:
  ```bash
  php artisan cache:clear
  ```

### Troubleshooting

If you encounter any issues, ensure the following:
- The `.env` file is correctly configured.
- The database server is running and accessible.
- All dependencies are installed.

For further assistance, refer to the Laravel documentation or contact the project maintainer.