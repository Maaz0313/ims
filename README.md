# Inventory Management System (IMS)

A comprehensive inventory management solution built with Laravel, designed to streamline product tracking, order management, and inventory control for businesses of all sizes.

## Live Demo

Try out the live demo at: [https://ims.thehexters.com/login](https://ims.thehexters.com/login)

**Demo Credentials:**

-   **Email:** admin@example.com
-   **Password:** password

## Features

### User Management & Security

-   **Role-Based Access Control (RBAC)**: Granular permissions system with customizable roles
-   **Secure Authentication**: Industry-standard authentication with password hashing
-   **Admin-Authorized Registration**: New user accounts require admin approval
-   **User Profiles**: Personalized user profiles with activity tracking

### Product Management

-   **Comprehensive Product Catalog**: Detailed product information including images, descriptions, and specifications
-   **Category Organization**: Hierarchical category system for easy product classification
-   **Barcode Support**: Generate and scan product barcodes
-   **Image Management**: Support for multiple image formats including WebP
-   **Bulk Operations**: Import/export product data via CSV/Excel

### Inventory Control

-   **Real-Time Stock Tracking**: Accurate inventory levels updated in real-time
-   **Low Stock Alerts**: Automated notifications for inventory replenishment
-   **Batch/Serial Number Tracking**: Track products by batch or serial numbers
-   **Inventory Adjustments**: Record and track inventory adjustments with reasons
-   **Multi-Location Support**: Manage inventory across multiple warehouses or locations

### Supplier Management

-   **Supplier Database**: Comprehensive supplier information and performance metrics
-   **Purchase Orders**: Create, track, and manage purchase orders
-   **Supplier Performance**: Track supplier reliability, pricing, and delivery times

### Order Processing

-   **Sales Order Management**: Create and process customer orders
-   **Order Status Tracking**: Real-time updates on order fulfillment status
-   **Invoice Generation**: Automatic invoice creation from orders
-   **Returns Processing**: Manage product returns and exchanges

### Reporting & Analytics

-   **Inventory Reports**: Stock levels, inventory valuation, and movement reports
-   **Sales Analytics**: Sales performance by product, category, and time period
-   **Purchase Reports**: Purchase history and supplier performance
-   **Financial Summaries**: Cost analysis and profit margins
-   **Custom Report Builder**: Create tailored reports for specific business needs
-   **Export Options**: Export reports to PDF, Excel, or CSV formats

### User Interface

-   **Responsive Design**: Mobile-friendly interface accessible on any device
-   **Dashboard**: Customizable dashboard with key metrics and alerts
-   **Live Search**: Real-time search functionality across the application
-   **Batch Operations**: Perform actions on multiple items simultaneously
-   **Dark/Light Mode**: Visual preference options for user comfort

### Integration & Technical Features

-   **API Access**: RESTful API for third-party integrations
-   **Data Import/Export**: Bulk data operations via CSV/Excel
-   **Audit Logging**: Comprehensive activity tracking for security and accountability
-   **Automated Backups**: Scheduled database backups for data protection

## Project Setup

Follow these steps to set up the Inventory Management System (IMS) project on your local machine:

### Prerequisites

Ensure you have the following installed on your system:

-   PHP >= 8.0
-   Composer
-   Node.js and npm
-   MySQL
-   A web server (e.g., Apache, Nginx, or Laravel's built-in server)

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

-   **Run Tests**
    To run the test suite, use:

    ```bash
    php artisan test
    ```

-   **Clear Cache**
    To clear the application cache, use:
    ```bash
    php artisan cache:clear
    ```

### Troubleshooting

If you encounter any issues, ensure the following:

-   The `.env` file is correctly configured.
-   The database server is running and accessible.
-   All dependencies are installed.

For further assistance, refer to the Laravel documentation or contact the project maintainer.
