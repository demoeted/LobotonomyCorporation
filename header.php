<header> 
    <h1>Lobotonomy Corporation</h1>
    <p>Helping budding gardeners grow!</p>
    <div id="right-side">
        <?php if(isset($_SESSION['email']) && !empty($_SESSION['email'])): ?>
            <p><a href="logout.php">Log Out</a></p>
        <?php else: ?>
            <p><a href="login.php">Login</a></p>
        <?php endif ?>
    </div> 
</header>