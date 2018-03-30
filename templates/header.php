
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="http://code.jquery.com/jquery-1.11.0.min.js"></script>
    <title>Sentimap</title>
    
    <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    

</head>
<body>
    <div id="logo">
        <img src="img/logo.png">
    </div>
    
    <div class="menu">
    <a href="index.php"><div class="menu-item first <?php if($page == 'index') {echo "selected"; } ?>">Current data</div></a>
    <a href="historic.php"><div class="menu-item <?php if($page == 'historic') {echo "selected"; } ?>">Historic data</div></a>
    <a href="projectinfo.php"><div class="menu-item last <?php if($page == 'projectinfo') {echo "selected"; } ?>">Project results</div></a>
    </div>