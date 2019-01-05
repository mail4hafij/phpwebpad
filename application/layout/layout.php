<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="description" content="phpwebpad opensource simple php mvc framwork" />
    <meta name="keywords" content="php, mvc, framework, simple, mysql, orm, opensource, model, view, controller" />
    
    <title>phpwebpad - simple and elegant</title>
    
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
      
      <?php echo Controller::renderElement("layout/keywords"); ?>
      
    </div>
  </body>
</html>