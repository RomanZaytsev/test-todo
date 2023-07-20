<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title><?php echo $title ?></title>
    <link rel="stylesheet" href="css/main.css" type="text/css"/>
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css" media="all">
    <link rel="stylesheet" href="css/jquery.bootgrid.css"/>
    <script src="js/jquery-2.2.4.min.js"></script>
    <script src="js/jquery.cookie.js"></script>
    <script src="js/ApiController.js"></script>
    <script src="js/CryptoJS_v3/rollups/sha1.js"></script>
    <script src="js/jquery.tablesorter.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.bootgrid.js"></script>
    <script src="js/MyTools.js"></script>
    <script src="js/controller.js"></script>
    <?php foreach ($jscripts as $item): ?>
        <script src="/js/<?php echo $item; ?>"></script>
    <?php endforeach; ?>
    <script>
        <?php if ($user): ?>
        var userid = '<?php echo $user->id ?>';
        <?php endif; ?>
    </script>
</head>
<body>
<div class="header">
    <div class="container">
        <h1>TODO-PHP</h1>
        <div class="pull-right">
            <?php if (@$user->name): ?>
                <p>
                <h3>Вы авторизованы, <?php echo $user->name ?>!</h3>
                <a href="#logout" onclick="user.authorization.logout({ page_return: '?controller=site&action=index' })">Выйти</a>
                </p>
            <?php else: ?>
                <a class="btn btn-xs btn-primary" href='?controller=user&action=login'>Войти</a>
            <?php endif; ?>
        </div>
        <h5><?php foreach ($breadcrumbs as $key => $item): ?><a
                href='<?php echo $item; ?>'><?php echo $key; ?></a> -> <?php endforeach; ?><?php echo $title; ?></h5>
    </div>
</div>
<div id='content'><?php echo $_content ?></div>

</body>
</html>
