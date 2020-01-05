# Software License Manager PHP Class #
This class can be used in a PHP application to contact a WordPress based license server using the free [Software License Manager Plugin](https://wordpress.org/plugins/software-license-manager/).
## Usage ##
- Install WordPress on your license server
- Install the Software License Manager Plugin on that server
- Configure the plugin, e.g. setting the secret key for validation
- Include my class in your PHP application
- Change the const variables in the class to match your license server settings
- Instantiate the class in your script, e.g. `$LIC = new SoftwareLicenseManager();`
- Set the license key property, e.g. `$LIC->setKey('5766474b540');`
- Load the details for that license from the license server, e.g. `$LIC->load();`
- Use other methods as needed, e.g.:
  - `$LIC->activate();`
  - `$LIC->daysToExpiry();`
  - `$LIC->deactivate();`
  - `$LIC->domainRegistered();`
  - `$LIC->getKey();`
  - `$LIC->load();`
  - `$LIC->setKey('5766474b540');`
  - `$LIC->show();`
  - `$LIC-status();`
## $LIC->activate() ##
This method will sumbit the license key in $this->key to the license server for activation plus domain registration. Example:
```php
$LIC->setKey('5766474b540');
$LIC->activate();
```
## $LIC->daysToExpiry() ##
This method will count and return the number of days from 'today' to the expiry date of the license. Example:
```php
$LIC->setKey('5766474b540');
$LIC->load();
echo $LIC->daysToExpiry();
```
## $LIC->deactivate() ##
This method will sumbit the license key in $this->key to the license server for deactivation. Example:
```php
$LIC->setKey('5766474b540');
$LIC->deactivate();
```
## $LIC->domainRegistered() ##
This method will check whether the current domain is registered for the license. Example:
```php
$LIC->setKey('5766474b540');
$LIC->load();
if ($LIC->domainRegistered()) {
   echo "Domain is registered";
else {   
   echo "Domain is not registered";
}
```
## $LIC->getKey() ##
This method will retrieve the class viarable 'key' that is supposed to hold the license key. Example:
```php
$LIC->setKey('5766474b540');
echo $LIC->getKey();
```
## $LIC->load() ##
This method will load the license details from the license server. Example:
```php
$LIC->setKey('5766474b540');
$LIC->load();
```
## $LIC->setKey() ##
This method will set the class viarable 'key' that is supposed to hold the license key. Example:
```php
$LIC->setKey('5766474b540');
```
## $LIC->show() ##
This method assumes that you use Bootstrap 4 in your PHP application. It will display the license status in a Bootstrap 4 alert box.
If you set the second parameter to 'true', a table with the license details will be shown as well inside that box.
Example:
```php
$LIC->setKey('5766474b540');
$LIC->load();
$LIC->show($LIC->details, true);
```
## $LIC->status() ##
The status() method returns one of these these values:
- _active_ (the license is active and registered for the domain the validation request came from)
- _blocked_ (the license is blocked)
- _expired_ (the license is expired)
- _invalid_ (an empty or invalid license key was submitted)
- _pending_ (the license is valid but not activated yet)
- _unregisterd_ (the license is active but not registered for the domain the validation request came from)
Example:
```php
$LIC->setKey('5766474b540');
$LIC->load();
echo $LIC->status();
```
## Credits ##
Thanks to [Tips and Tricks HQ](https://www.tipsandtricks-hq.com/software-license-manager-plugin-for-wordpress) for the Software License Manager Plugin for WordPress

