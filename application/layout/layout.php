<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    
    <?php echo(Controller::renderElement('layout/title')); ?>
    
    <?php
    $loadjs = new Request("/layout/loadjs");
    Router::render($loadjs);
    ?>

    <?php
    $loadcss = new Request("/layout/loadcss");
    Router::render($loadcss);
    ?>
    
  </head>
  <body>
    
    <?php 
    if(WebContext::isLocalhost()) {
      echo(Controller::renderElement('layout/log', array('log' => $log))); 
    }
    ?>
    
    <div id="wrap">
      <center><div id="bodywrap"><?php echo $__VIEW__; ?></div></center>
      <div class="clear">&nbsp;</div>
      <div id="footwrap">&copy; Free to use phpwebpad</div>
    </div>
    
    <?php
    $usejs = new Request("/layout/usejs");
    Router::render($usejs);
    ?>
    
  </body>
</html>