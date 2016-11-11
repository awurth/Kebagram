# KEBAGRAM

Web application shamefully inspired by Instagram, but this time for KEBABS !

An idea of University of Lorraine / Florian Ferbach

## Installation
### 1. Clone git repository
```
$ git clone git@github.com:TPCISIIE/Kebagram.git
```

### 2. Download vendors
```
$ cd Kebagram
$ composer install
```

### 3. Setup permissions
```
chmod -R 777 cache
```

### 4. Configure database connection
Navigate to the `bootstrap/` folder and copy `db.config.ini.dist` to `db.config.ini`
```
$ cd bootstrap
$ cp db.config.ini.dist db.config.ini
```

Now you can edit db.config.ini and add your database configuration

### 5. Create tables
Execute the following command in a terminal:
```
$ php _installation/database.php
```
or import `_installation/kebagram.sql` in your database manager

## Credits :
- Maxence ANTOINE 
- Xavier CHOPIN
- Alexis WURTH
