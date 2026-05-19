<?php
  $DomainPostFix = 'test';
  if (!empty($_GET['q'])) {
    switch ($_GET['q']) {
      case 'info':
        phpinfo(); 
        exit;
      break;
    }
  }
  function stringToColor($string)
    {
        // random color
        $rgb = substr(dechex(crc32($string)), 0, 6);
        // make it darker
        $darker = 1;
        list($R16, $G16, $B16) = str_split($rgb, 2);
        $R = sprintf('%02X', floor(hexdec($R16) / $darker));
        $G = sprintf('%02X', floor(hexdec($G16) / $darker));
        $B = sprintf('%02X', floor(hexdec($B16) / $darker));
        return '#' . $R . $G . $B;
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Laragon</title>
        <link rel="shortcut icon" type="image/png" href="favicon.png">
        <link href="bootstrap-5.3.1/css/bootstrap.min.css" rel="stylesheet" >
        <link href="bootstrap-5.3.1/custom.css" rel="stylesheet" >
        <script src="bootstrap-5.3.1/js/bootstrap.bundle.min.js" ></script>
        <script src="bootstrap-5.3.1/js.js" type="text/javascript"></script>

    </head>
    <body>
    <nav class="navbar navbar-expand-lg fixed-top bg-body-tertiary">
  <div class="container-fluid">
  <a class="navbar-brand" href="#">
      <img src="bootstrap-5.3.1/favicon.png" alt="Logo" width="30" height="24" class="d-inline-block align-text-top">
      <strong>Laragon</strong>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="http://localhost">Home</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
            Tools
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="http://localhost/phpmyadmin/">PhpMyAdmin</a></li>
            <li><a class="dropdown-item" href="http://localhost/?q=info">PHP Info</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="http://localhost/redis/?overview">Redis WA</a></li>
            <li><a class="dropdown-item" href="http://localhost/memcached/">Memecached WA</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">PHP: <?php print phpversion(); ?></a>
        </li>

      </ul>
      <form class="d-flex" role="search">
        <input class="form-control me-2 search" tabindex="1" type="text" id="search" placeholder="Type To search..." aria-label="Search">
      </form>
    </div>
  </div>
</nav>
        <div class="container">
            <div class="content">
                
                
                <div class="Directories row">
                  
                  <?php 
                  $Directories = glob(dirname(__FILE__). '/*', GLOB_ONLYDIR);
                  $temp = [];
                  foreach ($Directories as $key => $value) {
                    $temp[] = basename($value);
                  }
                  natcasesort($temp);
                  foreach($temp AS $Dir){
                    if(basename($Dir) == 'bootstrap-5.3.1'){
                      continue;
                    }
                    $FirstChar = strtoupper(substr($Dir,0,1));
                    echo '<div class="col-md-4"><a href="https://'.basename($Dir).'.'.$DomainPostFix.'"><span style="background-color:'.stringToColor(strtolower($FirstChar)).'">'.$FirstChar.'</span>'.basename($Dir).'</a></div>';
                  }
                  ?>
                </div>
                <div class="info"><br />
                      <?php print($_SERVER['SERVER_SOFTWARE']); ?><br />
                      PHP version: <?php print phpversion(); ?>   <span><a title="phpinfo()" href="/?q=info">info</a></span><br />
                      Document Root: <?php print ($_SERVER['DOCUMENT_ROOT']); ?><br />

                </div>
              
            </div>

        </div>
    </body>
</html>