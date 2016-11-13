<!doctype html>
<html>
    <head>
        <title>(Single Sign-On demo)</title>
        <link href="http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container">
            <h1> <small>(Single Sign-On demo)</small></h1>
            <h3>Logged in</h3>

            <pre><?= json_encode($user, JSON_PRETTY_PRINT); ?></pre>
        
             <a id="logout" class="btn btn-default" href="?logout=1">Logout</a>
        </div>
    </body>
</html>

