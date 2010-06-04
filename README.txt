Welcome to Fizzy!
=================

Introduction
------------
Fizzy is a PHP 5 Content Management System build with the Zend Framework and 
Doctrine. It is designed to be flexible in development and is aimed at sites that
require a large amout of customization. 

For more information, see http://project.voidwalkers.nl/projects/show/fizzy


Requirements
------------
To get Fizzy running you need the following:
- Apache mod_rewrite support (or equivalent)
- PHP 5.2+

If you want to use the SQLite backend, the following PHP extensions are required:
- PDO
- PDO SQLite extension

For MySQL you need:
- PDO
- PDO MySQL extension


Getting started
---------------
First configure the database settings in the application.ini file located in
the configs folder.

Then copy all the files to your webserver and you are good to go. It is
recommended not to copy Fizzy to your web root. Only the files from the public
folder need to be in your webroot.

The data folder and all its files need to be writeable for the webserver. Also
make sure the public/uploads folder exists and is writable by the webserver if 
you want to upload files.

Visit http://<your-site> for the demo site.
Visit http://<your-site>/admin for the admin interface.

The default user is admin with password admin. Be sure to change that after the
first login.


Getting Help
------------
If you need help with Fizzy, you can get help from the following sources:

- Wiki: http://project.voidwalkers.nl/wiki/fizzy


If you discover any bugs, please report them here:

    http://project.voidwalkers.nl/projects/fizzy/issues


License
-------
Doctrine and TinyMCE are LGPL Licensed.
You can find a copy of this license in LICENSE-LGPL.txt.

Zend Framework, SabreDAV and Fizzy itself are released under the new BSD license.
You can find a copy of this license in LICENSE-BSD.txt.
