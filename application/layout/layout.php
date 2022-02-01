<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    
    <?php echo(Controller::renderElement('layout/title')); ?>
    
    <?php
    $load_balancer = new Request("/Layout/loadbalancer");
    Router::render($load_balancer);
    ?>
    
  </head>
  <body>
    <div id="wrap">
      
      <center><div id="bodywrap"><?php echo $__VIEW__; ?></div></center>
      <div class="clb"></div>

      <div id="footwrap">&copy; Free to use phpwebpad</div>
      
    </div>
  </body>
</html>