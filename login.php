 <?php
  session_start();

  require('connect.php');
  
  $error = false;

  if(isset($_SESSION) && !empty($_POST['email'])){
    $useremail = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    
    if($useremail && !empty($_POST['password'])){
      $query = "SELECT * FROM user WHERE email = :email LIMIT 1";

      $statement = $db->prepare($query);

      $statement->bindValue(":email", $useremail);

      $statement->execute();

      $row = $statement->fetch();

      if($row){
        if(password_verify($_POST['password'], $row['password'])){
          $_SESSION['email'] = $useremail;
          $_SESSION['loggedIn'] = true;
          header('location: index.php');
          exit();
        }
      } else {
        $error = true;
      }
    } else {
      $error = true;
    }
  } else {
    $error = true;
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
        <?php if(isset($_SESSION['email'])):?>
          <li><a href="edit.php">New Post</a></li>
        <?php endif ?>
      </ul>
    </nav>

    <?php if(isset($_POST['email']) && isset($_POST['password'])):?>
      <?php if($error) :?>
        <h2>Invalid Email/Password</h2>
      <?php endif ?>
    <?php endif ?>
    
    <form method="post" action="login.php">
      <fieldset>
        <legend>Login</legend>
        <label for="email">Email</label>
        <input id="email" name="email" type="text">

        <label for="password">Password</label>
        <input id="password" name="password" type="password">

        <button>Login</button>
      </fieldset>
    </form>
  </main>
</body>
</html>