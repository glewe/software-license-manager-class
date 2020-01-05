# Software License Manager PHP Class #
This class can be used in a PHP application to contact a WordPress based license server using the free [Software License Manager Plugin](https://wordpress.org/plugins/software-license-manager/).
## Usage ##
- Install WordPress on your license server
- Install the Software License Manager Plugin on that server
- Include my class in your PHP application
- Change the const variables in the class to match your license server settings
- Instantiate the class in your script, e.g. $LIC = new SoftwareLicenseManager();
- Set the license key property, e.g. $LIC->setKey('5766474b540');
- Load the details for that license from the license server, e.g. $LIC->load();
- Use other methods as needed, e.g.:
  - $LIC->activate();
  - $LIC->daysToExpiry();
  - $LIC->deactivate();
  - $LIC->domainRegistered();
  - $LIC->show();
  - $LIC-status();
## $LIC->status() ##
The status() method returns one of these these values:
- active (the license is active and registered for the domain the validation request came from)
- blocked (the license is blocked)
- expired (the license is expired)
- invalid (an empty or invalid license key was submitted)
- pending (the license is valid but not activated yet)
- unregisterd (the license is active but not registered for the domain the validation request came from)
## $LIC->show() ##
The $LIC->show() method assumes that you use Bootstrap 4 in your PHP application.
## Credits ##
Thanks to [Tips and Tricks HQ](https://www.tipsandtricks-hq.com/software-license-manager-plugin-for-wordpress) for Software License Manager Plugin for WordPress

