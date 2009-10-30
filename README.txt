Welcome to Fizzy!
=================

Introduction
------------
Fizzy is a small PHP 5 Content Management System. It is designed to be portable.
It doesn't require MySQL so it is ideal for small shared hosting accounts or
low memory VPS systems. It's flexible MVC architecture allows for quick
development and it storage backend ensures you don't have to worry about SQL
queries and alike.

For more information, see http://project.voidwalkers.nl/projects/show/fizzy


Requirements
------------
To get Fizzy running you need the following:
- mod_rewrite support
- PHP 5
- DOM XML extension
- SimpleXML extension

Instead of the XML extensions you could also use our experimental SQLite backend.
It requires:
- PDO
- PDO SQLite extension


Getting started
---------------
Copy all the files to your webserver and you are good to go. It is recommended to
only place the files in the public folder in a your webroot and the rest one level
deeper. The data folder en all its files should be writeable for the webserver.

Visit http://<yoururl> for the demo site.
Visit http://<yoururl>/admin for the admin interface.

The default user is admin with password admin. Be sure to change that first.


Getting Help
------------
If you need help with Fizzy, you can get help from the following sources:

- Wiki: http://project.voidwalkers.nl/wiki/fizzy


If you discover any bugs, please report them here:

    http://project.voidwalkers.nl/projects/fizzy/issues


License
-------
The files in this archive are released under the new BSD license.
You can find a copy of this license in LICENSE.txt.