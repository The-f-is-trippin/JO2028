<?php
session_start(); // Démarre la session PHP pour stocker des variables de session.

require_once("database.php"); // Inclut le fichier de connexion à la base de données.

// Désactiver l'affichage des erreurs en production
error_reporting(0);
ini_set("display_errors", 0);

// Vérifier si l'utilisateur est déjà connecté
if (isset($_SESSION["id_utilisateur"])) {
    header("location: ../pages/admin/admin.php");
    exit();
}

// Générer un token CSRF si ce n'est pas déjà fait
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Vérifie si le formulaire est soumis en POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifie le token CSRF
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $_SESSION['error'] = "Token CSRF invalide.";
        header("location: ../pages/login.php");
        exit();
    }

    // Nettoyer et valider les entrées
    $login = filter_input(INPUT_POST, "login", FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"] ?? ""; // Ne pas filtrer le mot de passe

    if (empty($login) || empty($password)) {
        $_SESSION['error'] = "Login ou mot de passe manquant.";
        header("location: ../pages/login.php");
        exit();
    }

    try {
        $query = "SELECT id_utilisateur, nom_utilisateur, prenom_utilisateur, login, password FROM UTILISATEUR WHERE login = :login";
        $stmt = $connexion->prepare($query);
        $stmt->bindParam(":login", $login, PDO::PARAM_STR);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row && password_verify($password, $row["password"])) {
            // Régénérer l'ID de session pour prévenir la fixation de session
            session_regenerate_id(true);
            
            $_SESSION["id_utilisateur"] = $row["id_utilisateur"];
            $_SESSION["nom_utilisateur"] = $row["nom_utilisateur"];
            $_SESSION["prenom_utilisateur"] = $row["prenom_utilisateur"];
            $_SESSION["login"] = $row["login"];
            
            header("location: ../pages/admin/admin.php");
            exit();
        } else {
            $_SESSION['error'] = "Login ou mot de passe incorrect.";
            header("location: ../pages/login.php");
            exit();
        }
    } catch (PDOException $e) {
        // Logger l'erreur en production
        error_log("Erreur d'authentification : " . $e->getMessage());
        $_SESSION['error'] = "Une erreur est survenue lors de l'authentification.";
        header("location: ../pages/login.php");
        exit();
    }
} else {
    // Si le formulaire n'est pas soumis en POST, on redirige vers la page de login
    header("location: ../pages/login.php");
    exit(); // Termine le script
}

// Libération de la connexion à la base de données
unset($connexion);
?>