# Login System

## Overview

This repository contains a PHP-based login system designed to provide secure authentication and user management functionality for web applications. It supports user registration, login, session management, account activation, password recovery, and admin functionalities.

## Features

- Register new users with username, email, and password
- Authenticate users and start secure sessions
- Manage sessions to protect restricted areas
- Send activation and password-reset emails (PHPMailer)
- Enable password recovery via secure reset links
- Update user account details and email addresses
- Administer user accounts with an admin panel
- Visualize activity and statistics with JavaScript charts
- Implement async loading and session timeouts for smoother UX

## Planned Features

- Integration with modern authentication protocols (OAuth2, 2FA)
- Enhanced dashboard with analytics and usage metrics
- Dark mode and improved UI styling
- Modular plugin support for easier customization
- RESTful API endpoints for user management
- Docker setup for easy local deployment

## Requirements

- PHP 7.4 or higher
- MySQL or MariaDB
- PHPMailer

## Installation

1. Clone or download the repository.

2. Ensure that PHP and MySQL/MariaDB are installed on your server. You can find setup guides for [PHP](https://www.php.net/manual/en/install.php) and [MySQL](https://dev.mysql.com/doc/refman/8.0/en/installing.html)

3. Download PHPMailer from [PHPMailer](https://github.com/PHPMailer/PHPMailer) and place it in the main directory

4. Import the SQL schema to set up the database tables:

```
mysql -u username -p database_name < user_accounts.sql
```

5. Edit the `config.php` file with the database settings and `activationmail.php` with your PHPMailer settings

6. **(Optional)**: Populate the database with sample data for testing:

```
mysql -u username -p database_name < randomusers.sql
```

## Usage

After setup, open the project in your browser and navigate to the installation path. Users can register, log in, or recover their password using the provided interfaces.

Refer to individual script comments for more detailed instructions on each feature.

## Contributing

Contributions to this repository are welcome! If you have an idea for a new tool or an improvement to an existing tool, feel free to create a pull request or open an issue.

## License

This project is licensed under the [GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.en.html).
