<?php // 500 error page ?>
<!DOCTYPE HTML>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7; IE=EmulateIE9" />
    <link type="text/css" rel="stylesheet" href="<?php echo $app['Core']['web_root'] ?>/css/Anthem/Core/lib/bootstrap.min.css">
    <link type="text/css" rel="stylesheet" href="<?php echo $app['Core']['web_root'] ?>/css/Anthem/Core/anthem.css">
    <title>
      <?php echo $app['Core']['project'] ?>: Error
    </title>
  </head>
  <body class="noprint">
    <div class="wrap">
      <div class="container-fluid main">
        <div class="page-header" style="margin-bottom: 5px; padding-bottom: 0;">
          <h1>500 <small>Application Error / Ошибка приложения</small></h1>
        </div>
        <div class="error-page">
          <h1 style="font-size: 150px; line-height: 170px;">:-(</h1>
          <h3>
            The application has encountered an internal error.
            <br>
            The error has been logged and will be examined by the administration.
            <br>
            Perhaps reloading this page will help?
            <br>
            &bull;
            <br>
            В приложении произошла внутренняя ошибка.
            <br>
            Информация об ошибке была записана, и администраторы вскоре займутся ей.
            <br>
            Может быть, если перезагрузить эту страницу, ошибка исчезнет?
          </h3>
        </div>
      </div>
    </div>
  </body>
</html>
