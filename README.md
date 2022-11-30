# wunschliste

This package provides a simple wish list that is independent of any online shop or other service. It is meant to be what you would write on a piece of paper with the additional option to add link to the single entries.


## Installation
There are no special requirements to run the code. You just need a web server with a running PHP instance and the SQLite3 extension. Create an administrator password and enter into the configuration file `config.php`. The password is entered as hash value using sha256. You can use any generator to create the hash value, e.g., this [online tool](https://tools.faullab.com/hashcalculator).
Next, set the URL and the absolute path of the installation in the `config.php`. Finally, upload all files to the webserver.
In the following, it is assumed that the URL of the installation is `https://wish.example.org`.

Before the wish list can be used, the database needs to be created and initialized. This is done by calling `https://wish.example.org/init.php`. After the successful database initialization, make sure that the file `INITIALIZE` in the root folder has been deleted to disable further initialization of the database.


## Usage
The individual wish lists are accessible by calling `https://wish.example.org/index.php/user`, where *user* is the respective username. If enabled, an overview of all existing wish lists is shown on the start page.

Users can edit their list by calling `https://wish.example.org/edit.php` where they have to log in with their username and password. The password is set when a new list is created. Users can create new and delete existing items. The available categories can only be changed by the administrator.


## Administration
The administration page is available by calling `https://wish.example.org/admin.php` and logging in with the administrator password. The menu allows to create and delete lists, respectively users, and to create and delete categories. In addition, all login tokens can be cleared which means that all active users are logged out.


## License
This project is licensed under the [MIT](./LICENSE) license.
