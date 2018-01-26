<?php
require_once(filter_input(INPUT_SERVER, 'DOCUMENT_ROOT') ."/resources/session.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>KEOPS | </title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css" integrity="sha384-rHyoN1iRsVXV4nD0JutlnGaslCJuC7uwjduW9SVrLvRYooPp2bWYgmgJQIXwl/Sp" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/general.css" />
  </head>
  <body>
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">KEOPS</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Tasks</a></li>
          </ul>
          <?php
          echo $SIGNEDIN;
          if ($SIGNEDIN) {
          ?>
          <ul class="nav navbar-nav navbar-right">

            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= $USER->name ?><span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li class="dropdown-header">Account</li>
                <li><a href="">Sign out <?= $USER->name ?></a></li>

                <li role="separator" class="divider"></li>
                <li class="dropdown-header">Management</li>

                <li><a href="/admin/">Admin backend</a></li>

                <li><a href="">Campaign status</a></li>
              </ul>
            </li>
          </ul>
          <?php
          }
          else {
          ?>
          <form class="navbar-form navbar-right" action="users/user_login.php"  method="post" >
            <div class="form-group">
              <input type="text" id="email" name="email" placeholder="Email address" class="form-control">
            </div>
            <div class="form-group">
              <input type="password" id="password" name="password" placeholder="Password" class="form-control">
            </div>
            <button type="submit" class="btn btn-success">Sign in</button>
            <a href="/signup.php" type="button" class="btn btn-primary" method="post">Sign up</a>
          </form>
          <?php
          }
          ?>
        </div><!--/.nav-collapse -->
      </div>
    </nav>
