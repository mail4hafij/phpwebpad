# phpwebpad
phpwebpad is a very lightweight MVC driven framework. Visit <a href="http://phpwebpad.hafij.com" target="_blank">phpwebpad.hafij.com</a>

File structure:
+ application
   + controller
     - ApplicationController.php
   + model
     - Config.php
     - DataContext.php
     - InfoKeeper.php
   + view
     + application
       - index.php
   - element
   + layout
     - layout.php
+ bin
+ lib
+ web
   + css
   + images
   + js
- index.php


Controller:

    A controller extends the Controller class.
    The default controller name is Application.
    Allowable name e.g., User, User_settings, Usersettings
    Not allowable name e.g., UserSettings, User_Settings, userSettings
    A controller class has 'Controller' suffix at the end of its name. E.g., UserController, User_settingsController
    File name: The class name should be the file name. e.g: UserController.php, User_settingsController.php
    File path: /application/controller/UserController.php or /application/controller/User_settingsController.php, etc.


Action and view:

    The default action name is index.
    File path: e.g. If your action name is login then the view file should be in application/view/{controllername}/login.php.

Layout and element:

    The default layout name is layout.
    The file path for layout: /application/layout/layout.php.
    File path for element: /application/element/{yourpage.php}.

Models:

    A model extends Model class and implement getTableDefination() method.
    Class name and the model name should be the same.
    File path: /application/model/{yourmodel.php}.




