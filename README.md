# Login System

## Overview

This repository contains a PHP-based login system designed to provide secure authentication and user management functionality for web applications. It includes features such as user registration, login, logout, and session management, with planned expansions for password recovery, account activation via email, and administrative controls.

## Features

### Current Features

- User Registration: Allows new users to register by providing their username, email, and password
- User Login: Authenticates users based on their credentials and initiates a session
- Logout: Terminates the user's session and redirects to the login page
- Session Management: Manages user sessions to ensure secure access to restricted areas

### Planned Features

- PHPMailer Integration: For sending account activation emails and password reset links
- Account Activation: Requires new users to activate their accounts via an email link
- Password Recovery: Allows users to reset their password through a secure process
- Profile Management: Enables users to update their email and other account information
- Admin Functionality: Provides administrative controls for managing user accounts
- Dynamic Charts: Visualizes user login data and other relevant statistics using JavaScript charts
- AJAX Integration: Enhances the system with asynchronous data loading and session timeouts for improved user experience

## Installation

Ensure you have PHP and MySQL/MariaDB installed on your server or development machine

Once they are installed, clone this repository to your desired location:

`git clone https://github.com/yourusername/login-system.git`

Navigate to the cloned repository folder and import the user_accounts.sql file into your database to set up the required tables:

```
cd login-system
mysql -u username -p database_name < user_accounts.sql
```

Update the config.php file with your database connection details.

## Usage

To start using the login system, direct your browser to the installation path. Users can register a new account or log in using their existing credentials.

Refer to individual script comments for more detailed instructions on each feature.

## Contributing

Contributions to this repository are welcome! If you have an idea for a new tool or an improvement to an existing tool, feel free to create a pull request or open an issue.

## License

This project is licensed under the [GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.en.html).
