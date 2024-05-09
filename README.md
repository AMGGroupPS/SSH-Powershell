# SSH_Powershell

Original Script written by : https://github.com/alissonpelizaro

Converted to English by Matt Lowe <matt.lowe@amg-group.co.uk>

PHP application ready to execute PowerShell commands on a Windows server remotely from a Linux server.

## Requirements

* Windows Server needs to have SSH service enabled
* PHP >= 5.3.3

## Installation

```
composer require amggroup/ssh_powershell
```

## Dependencies

* phpseclib/phpseclib >=  2.0.21

## Execute in CLI

(parameter "-d" enables DEBUG-MODE)

```
php service.php -d
```

## Execute in browser

```php
require __DIR__.'/core.php';

$host = "remote_host";
$user = "user";
$pass = "password";

$ssh = new SSH_Conn($host, $user, $pass, $debug);
$powershell = new PowerShell($ssh);
```

## Command Examples

```php
// Example to get a list of all users
print_r($powershell->getUsers());

// Example to search for a specific user
print_r($powershell->getUser('alisson'));

// Example to search for a user
print_r($powershell->searchUser('HomePhone', '4130305525'));

// Example to reset a user's password
$powershell->resetPassword('alisson', 'newPassword123');

// Example to execute any PowerShell command
$powershell->exec('powershell Set-ADUser -Identity alisson -PasswordNeverExpires $true');
```

## LOG Usage

Since it can be executed in CLI and sometimes unattended, the best way for monitoring is through LOG. The application already saves all commands in the `ssh.log` file. To set an additional log, simply call the following static method:

`Log::create('Log description', {true for error log}, {true to kill the application after registration});`

Examples:

```php
// Records LOG as informational
use amggroup\Log;Log::create('Process executed successfully');

// Records LOG as an error
Log::create('Error executing command', true);

// Records LOG as an error and kills the application
Log::create('Error executing command', true, true);

// Records LOG as informational and kills the application
Log::create('Command executed', false, true);
```

## PowerShell Commands

### getUsers()

Returns an array with all users from the server.

```php
$users = $powershell->getUsers();
```

### searchUser()

Searches for users based on the passed filters.

```php
$users = $powershell->searchUser('HomePhone', '554130304545');
```

### getUser()

Gets data of a specific user according to their CN.

```php
$user = $powershell->getUser('alisson');
```

### exec()

Executes any PowerShell command passed as a parameter.

```php
$command = $powershell->exec('powershell Get-ADuser joao.silva -properties *');
```

### resetPassword()

Changes the access password of a user.

```php
$powershell->resetPassword('alisson', 'new$pass123');
```

### askNewPassword()

Sets request for a new password on next logon (true or false).

```php
$powershell->askNewPassword('alisson', true);
```

### setExpiredPass()

Sets the "Password never expires" configuration of a user (true or false).

```php
$powershell->setExpiredPass('alisson', true);
```
