# phpwebpad

`PHP 8` `MVC` `MySQL ORM` `Zero dependencies`

phpwebpad is a small, dependency-free MVC framework for PHP. It was built for one purpose: to
have everything a typical server-rendered web app needs — routing, controllers, views, a MySQL
ORM, JSON endpoints — without the configuration, abstraction layers, and dependency weight of
larger frameworks. There is no build step, no service container, no annotations to learn. You
can read the entire core (about a dozen small classes) in under an hour and know exactly what
every request does.

It has been running production sites for years (see [phpwebpad.hafij.com](http://phpwebpad.hafij.com))
and is maintained by [Mohammad Hafijur Rahman](https://github.com/mail4hafij).

## Contents

- [Features](#features)
- [Requirements](#requirements)
- [Quick start](#quick-start)
- [Project structure](#project-structure)
- [Routing](#routing)
- [Controllers, views & layouts](#controllers-views--layouts)
- [JSON actions](#json-actions)
- [The ORM](#the-orm)
- [Frontend helper (jswebpad.js)](#frontend-helper-jswebpadjs)
- [Testing](#testing)
- [License](#license)

## Features

### Core / MVC

- Classic MVC with plain PHP views — no template DSL to learn
- Regex-based routing for clean/custom URLs alongside the default `/controller/action/param1/param2` convention
- Per-controller action whitelisting (`actions()` / `jsonActions()`) — nothing is callable from a URL unless you explicitly allow it
- Built-in JSON action support — no separate API framework needed
- Layout + view + reusable "element" partials, each with their own variable scope
- Lightweight in-request action logging
- PSR-4-free autoloading via a single `spl_autoload_register` callback that knows the framework's folder conventions

### ORM (MySQL)

- Tables are created and altered automatically from your model's column definitions — no migration files to write
- Foreign keys are managed in code (not as real DB constraints), so cascade rules live next to your model logic
- Cascade delete and cascade nullify, with an on/off switch per foreign key
- Truncate a table without resetting its auto-increment counter
- Native transaction support (`startTransaction` / `commit` / `rollback`)
- Join-aware loaders so related rows come back as populated objects, not raw arrays

## Requirements

- PHP 8.0+
- MySQL / MariaDB
- Apache with `mod_rewrite` (or an equivalent rewrite-to-`index.php` rule)
- [Composer](https://getcomposer.org/) — only needed for running tests / optional third-party libraries, the framework itself has zero runtime dependencies

## Quick start

1. Clone or download this repo into your web root, e.g. `C:\xampp\htdocs\phpwebpad`
2. Point your Apache `DocumentRoot` (in `httpd.conf` or a vhost) at the project folder, then restart Apache
3. Create a MySQL database named `phpwebpad` — tables are created automatically the first time each model is used, no SQL to run by hand
4. Update the DB credentials used in `application/model/DataContext.php` (or wherever your app builds its `Database` instance) if they differ from the defaults
5. Visit `http://localhost` — you should see the default `ApplicationController::index()` page

## Project structure

```
index.php                    Front controller: bootstraps constants, autoloading, routes the request
.htaccess                    Rewrites every request to index.php
bin/
  core/
    Controller.php           Abstract base every *Controller extends
    Router.php                Matches a Request to a controller + action
    Request.php                Parses the incoming URI
    JSONResponse.php           Tiny wrapper that turns data into a JSON response
  orm/
    Database.php                MySQL connection + query/CRUD/transaction API
    Model.php                     Abstract base every model extends
    TableDefinition.php            Declarative table/column/foreign-key definition
  Helper.php                  Small static helpers (e.g. safe SQL parameter substitution)
lib/                          Standalone utilities: FileUploader, Mail, Resize, RssReader, ...
application/
  controller/                 One *Controller.php per route "controller" segment
  model/                       One model per database table
  view/<controller>/          One view file per action
  layout/                      Layout files views get wrapped in
  element/                     Reusable partials rendered via Controller::renderElement()
web/                           Publicly served assets (css, js, images) + web/js/css bootstrap PHP
test/                         PHPUnit tests
```

## Routing

By default a URL maps to `/ControllerName/actionName/param1/param2/...`. The controller class
must be named `{ControllerName}Controller` and live in `application/controller/`.

For non-default routes (clean URLs, aliases), register a regex route in `index.php` before
`Router::render()` is called:

```php
Router::addRoute('/^\/howitworks$/i', 'Application', 'howitworks');
Router::addRoute('/^\/download$/i',   'Application', 'download');
Router::render($request);
```

A request to `/howitworks` is routed to `ApplicationController::howitworks()` without that
segment ever appearing in the URL.

## Controllers, views & layouts

Every controller extends the abstract `Controller` class and must implement five methods.
`actions()`/`jsonActions()` act as an explicit allow-list — an action that isn't listed there
can't be reached from a URL at all:

```php
class BookController extends ApplicationController {

  public function actions() {
    return array('index', 'view');       // rendered as HTML views
  }

  public function jsonActions() {
    return array('create', 'delete');    // return JSON instead of a view
  }

  public function catchUnAllowedActions($action_name) {
    Controller::redirectAndExit('/');    // called when the action isn't whitelisted
  }

  public function beforRender($action_name, $controller_name) {
    // runs before the action — good place for auth checks
  }

  public function afterRender($action_name, $controller_name) {
    // runs after the action, before the view/layout is rendered
  }

  public function index() {
    $books = DataContext::getDatabase()->loadAll('Book');
    $this->setViewVar('books', $books);
    // renders application/view/book/index.php inside the current layout
  }

  public function view($book_id) {
    $book = DataContext::getDatabase()->loadOnlyById('Book', $book_id);
    $this->setViewVar('book', $book);
  }
}
```

`DataContext::getDatabase()` is this project's own convention (in
`application/model/DataContext.php`) for lazily creating and reusing a single `Database`
connection per request — the framework itself doesn't dictate how you obtain one.

Inside a view file, variables set with `setViewVar()` are available directly (e.g. `$books`).
`setLayout('layout')` wraps the view's output in `application/layout/layout.php`, where it's
available as `$__VIEW__`. Reusable snippets go through:

```php
echo Controller::renderElement('layout/title', array('title' => 'Books'));
```

## JSON actions

Anything listed in `jsonActions()` skips the view/layout step entirely and its return value is
expected to be a `JSONResponse`:

```php
public function create($title, $author) {
  $book = new Book();
  $book->title = $title;
  $book->author = $author;
  DataContext::getDatabase()->store($book);

  $json = new JSONResponse();
  $json->setVar('success', 'Book saved.');
  $json->setVar('url', 'current');   // tell the frontend helper to reload the page
  return $json;
}
```

The bundled frontend helper (see below) knows how to consume this exact response shape —
success/error messages, showing/hiding elements, redirects — from any form or link without you
writing custom AJAX handlers.

## The ORM

A model declares its own table shape by returning a `TableDefinition`. Tables and missing
columns are created automatically the first time the model is instantiated with a `Database` —
there's no separate migration step:

```php
class Book extends Model {
  public function getTableDefinition() {
    $table = new TableDefinition('book');
    $table->addNonNullColumn('title', 'VARCHAR(255)');
    $table->addColumn('author', 'VARCHAR(255)');
    $table->addForeignKey('publisher_id', 'Publisher');  // cascade delete on by default
    $table->addUniqueKey('uq_title', array('title'));
    return $table;
  }
}
```

Register the model once in `DataContext::createAllTables()` so its table gets created/altered
on the next request — passing a `Database` into the constructor is what triggers
`createTable()`:

```php
public static function createAllTables() {
  $database = DataContext::getDatabase();
  $database->setAlterTable(true);

  new Book($database);
  new Publisher($database);
}
```

Common `Database` operations, obtained via `DataContext::getDatabase()`:

```php
$db = DataContext::getDatabase();

$db->store($book);                                    // insert
$db->update($book);                                   // update
$books = $db->loadAll('Book');                         // all rows as Book[]
$book  = $db->loadOnlyById('Book', $id);                // single row by primary key
$count = $db->countAll('Book', Helper::sql('author = %s', $author));

$db->startTransaction();
try {
  $db->deleteAll('Book', Helper::sql('publisher_id = %s', $publisherId));
  $db->commit();
} catch (Exception $e) {
  $db->rollback();
  throw $e;
}

$book->cascadeDelete($db);   // soft-deletes this row and cascades per addForeignKey() rules
```

`Helper::sql(...)` safely substitutes `%s` placeholders, so query conditions never need manual
escaping.

## Frontend helper (jswebpad.js)

The `web/js/` folder ships a small jQuery-based helper,
[jswebpad.js](https://github.com/mail4hafij/jswebpad), that wires up
`<button name="jsonsubmit">` forms and `<a class="get">` links to the JSON action responses
described above — success/error messages, show/hide, redirects — without any per-page
JavaScript. See its own repo for the full API.

## Testing

Tests use [PHPUnit](https://phpunit.readthedocs.io/en/9.5/index.html) and live in `test/`.
Create a `phpwebpad_test` database, then:

```
composer install
./vendor/bin/phpunit --testdox ./test/TimeMachineTest.php
```

## License

Free to use. Contributions and issues welcome at
[github.com/mail4hafij/phpwebpad](https://github.com/mail4hafij/phpwebpad).
# phpwebpad (updated to php version 8)
phpwebpad is a very lightweight MVC driven framework for web development. My idea was to build this framework for my personal use so that I have all the things that I need without the fancy complexities I find in other frameworks. I think, it is worth sharing this project. 

phpwebpad supports all the basic features that other PHP framewoks have - 
  * MVC 
  * Routing actions based on regular expression
  * Actions authorization
  * JSON actions
  * Redirects
  * Action logging

It supports ORM (Object Releational Mapping) with MySql. The ORM has a lot of cool features - 
 * Tables are created and altered on the fly
 * On code foreign key relationships 
 * Cascade delete
 * Truncate tables without loosing auto increament index
 * Database transactions

## How to run locally
 * Change the DocumentRoot in your httpd.conf file to point to ```phpwebpad``` folder
 * Restart your apache server
 * Create a database ```phpwebpad``` in your phpmyadmin
 * Hit localhost in your browser

It is easy to integrate different testing framworks such as <a href="https://phpunit.readthedocs.io/en/9.5/index.html" target="_blank">phpunit</a>.
All the tests are in the 'test' folder. Create a database phpwebpad_test.
Open a terminal in the root project folder and run

```
composer install
./vendor/bin/phpunit --testdox ./test/TimeMachineTest.php
```

It gives all the basic features that you may expect from a server side MVC framework. You can learn it within an hour. Visit <a href="http://phpwebpad.hafij.com" target="_blank">phpwebpad.hafij.com</a>
