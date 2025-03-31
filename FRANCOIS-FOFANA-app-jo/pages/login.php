<?php
session_start();

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION["id_utilisateur"])) {
    header("location: admin/admin.php");
    exit();
}

// Vérifiez si le token CSRF existe, sinon générez-le
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Désactiver l'affichage des erreurs en production
error_reporting(0);
ini_set("display_errors", 0);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles-computer.css">
    <link rel="stylesheet" href="../css/styles-responsive.css">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <title>Connexion - Jeux Olympiques - Los Angeles 2028</title>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages sports, events, et results -->
            <ul class="menu">
                <li><a href="../index.php">Accueil</a></li>
                <li><a href="sports.php">Sports</a></li>
                <li><a href="events.php">Calendrier des évènements</a></li>
                <li><a href="results.php">Résultats</a></li>
                <li><a href="login.php">Accès administrateur</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<div class="error-message">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        ?>
        
        <h1>Connexion</h1>

        <form action="../database/auth.php" method="post" id="loginForm" onsubmit="return validateForm()">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            
            <div class="form-group">
                <label for="login">Login :</label>
                <input type="email" name="login" id="login" required 
                       pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                       title="Veuillez entrer une adresse email valide">
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" name="password" id="password" required 
                       minlength="8"
                       title="Le mot de passe doit contenir au moins 8 caractères">
            </div>
            
            <button type="submit">Se connecter</button>
        </form>
    </main>

    <footer>
        <figure>
            <img src="../img/logo-jo.png" alt="logo Jeux Olympiques - Los Angeles 2028">
        </figure>
    </footer>

    <script>
    function validateForm() {
        const login = document.getElementById('login').value;
        const password = document.getElementById('password').value;
        
        if (!login || !password) {
            alert('Veuillez remplir tous les champs');
            return false;
        }
        
        if (!login.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)) {
            alert('Veuillez entrer une adresse email valide');
            return false;
        }
        
        if (password.length < 8) {
            alert('Le mot de passe doit contenir au moins 8 caractères');
            return false;
        }
        
        return true;
    }
    </script>
</body>

</html>