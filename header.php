<header> 
    <h1>Lobotonomy Corporation</h1>
    <p>Helping budding gardeners grow!</p>
    <div id="right-side">
        <?php if(isset($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_USER'])): ?>
            <p><a href="#">Log Out</a></p>
        <?php else: ?>
            <p><a href="login.php">Login</a></p>
        <?php endif ?>
    </div> 
</header>