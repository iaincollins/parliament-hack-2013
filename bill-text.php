<?php
    include_once(dirname(__FILE__).'/lib/stdHeader.php');
    $bill = Bill::getBillById($_REQUEST['id']);
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8"/>
        <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Parliament Hack 2013</title>
        <link rel="shortcut icon" href="/favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="/css/stylesheet.css"/>
        <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.2/css/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootswatch/3.0.1/cosmo/bootstrap.min.css"/>
        <link rel="stylesheet" type="text/css" href="http://assets.annotateit.org/annotator/v1.2.6/annotator.min.css"/>
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
        <!-- <script language="javascript" type="text/javascript" src="/javascript/common.js"></script> -->
        
        <!-- NOTE: annotator doesn't seem to work with jQuery 1.10.2 so using an older version-->
        <!-- <script src="//code.jquery.com/jquery-1.10.2.min.js"></script> -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.2/js/bootstrap.min.js"></script>
        <script src="//assets.annotateit.org/annotator/v1.2.6/annotator-full.min.js"></script>
    </head>
    <body class="bill-text">
        <?= $bill->getBillText(); ?>
        <script>
            jQuery(function($) {
                $('.bill-text').annotator().annotator('setupPlugins');
            });
        </script>
    </body>
</html>