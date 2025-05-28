# EV Charging Station Management System

A complete web application for managing electric vehicle charging stations built with PHP, MySQL, HTML, CSS, and JavaScript.

## Features

- User registration and authentication
- Booking system for charging stations
- Real-time monitoring of charging station availability
- Administrative dashboard for station management
- User dashboard with charging history and statistics
- Automatic notification system for booking expiration

## Installation

1. Install XAMPP on your system if you haven't already (https://www.apachefriends.org/download.html)
2. Clone or download this repository to your XAMPP htdocs folder (e.g., `C:\xampp\htdocs\ev-charging-system` on Windows or `/Applications/XAMPP/htdocs/ev-charging-system` on macOS)
3. Start the Apache and MySQL services in XAMPP Control Panel
4. Open your browser and navigate to `http://localhost/phpmyadmin`
5. Create a new database named `ev_charging_db`
6. Import the `db/schema.sql` file into the newly created database
7. Navigate to `http://localhost/ev-charging-system` in your browser to access the application

## Admin Access

Default admin credentials:
- Username: admin@example.com
- Password: Admin123!

## File Structure

```
/ev-charging-system
    /assets - Contains CSS, JavaScript, and image files
    /config - Database connection and configuration files
    /includes - Reusable PHP components and functions
    /pages - All application pages
    /db - Database schema and initialization scripts
    index.php - Entry point to the application
```

## License

This project is licensed under the MIT License - see the LICENSE file for details.