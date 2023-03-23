 <?php
  session_start();
  
  require('authenticate.php');

  if(isset($_SESSION) && !empty($_SERVER['PHP_AUTH_USER'])){
    $_SESSION['username'] = $_SERVER['PHP_AUTH_USER'];
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login!</title>
</head>
<body>
  <?php include('header.php') ?>
  <main>
    <nav>
      <ul id="menu">
        <li><a href="index.php">Home</a></li>
        <?php if(isset($_SERVER['PHP_AUTH_USER'])):?>
          <li><a href="edit.php">New Post</a></li>
        <?php endif ?>
      </ul>
    </nav>
    <h2>Successfully logged in!
    <!--<form method="post" action="login.php">
      <fieldset>
        <legend>Login</legend>
        <label for="username">Username</label>
        <input id="username" name="username" type="text">

        <label for="password">Password</label>
        <input id="password" name="password" type="password">

        <button>Login</button>
      </fieldset>
    </form>-->
  </main>
</body>
</html>