<header> 
    <div id="banner">
        <h1>Lobotonomy Corporation</h1>
        <p>Helping budding gardeners grow!</p>
    </div>
    <div id="right-side">
        <?php if(isset($_SESSION['acctype']) && !empty($_SESSION['acctype'])):?>
            <?php if($_SESSION['acctype'] === 'O' || $_SESSION['acctype'] === 'A'): ?>
                <p><a href="manage.php">Manage Users</a></p>
            <?php endif?>
        <?php endif ?>
        <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
            <p><a href="logout.php">Log Out</a></p>
        <?php else: ?>
            <p><a href="login.php">Login</a> | <a href="signup.php">Sign Up</a></p>
        <?php endif ?>
    </div> 
</header>