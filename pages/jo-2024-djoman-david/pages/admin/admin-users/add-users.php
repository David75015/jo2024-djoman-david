<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Assurez-vous d'obtenir des données sécurisées et filtrées
    $nomUtilisateur = filter_input(INPUT_POST, 'nomUtilisateur', FILTER_SANITIZE_STRING);
    $prenomUtilisateur = filter_input(INPUT_POST, 'prenomUtilisateur', FILTER_SANITIZE_STRING);
    $login = filter_input(INPUT_POST, 'login', FILTER_SANITIZE_STRING);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Cryptage du mot de passe

    // Vérifiez si les champs sont vides
    if (empty($nomUtilisateur) || empty($prenomUtilisateur) || empty($login) || empty($_POST['password'])) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: add-users.php");
        exit();
    }

    try {
        // Vérifiez si l'utilisateur existe déjà
        $queryCheck = "SELECT id_utilisateur FROM UTILISATEUR WHERE nom_utilisateur = :nomUtilisateur";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomUtilisateur", $nomUtilisateur, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "L'utilisateur existe déjà.";
            header("Location: add-users.php");
            exit();
        } else {
            // Requête pour ajouter un utilisateur
            $query = "INSERT INTO UTILISATEUR (nom_utilisateur, prenom_utilisateur, login, password) 
                      VALUES (:nomUtilisateur, :prenomUtilisateur, :login, :password)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomUtilisateur", $nomUtilisateur, PDO::PARAM_STR);
            $statement->bindParam(":prenomUtilisateur", $prenomUtilisateur, PDO::PARAM_STR);
            $statement->bindParam(":login", $login, PDO::PARAM_STR);
            $statement->bindParam(":password", $password, PDO::PARAM_STR);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "L'utilisateur a été ajouté avec succès.";
                header("Location: manage-users.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout de l'utilisateur.";
                header("Location: add-users.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-users.php");
        exit();
    }
}

// Afficher les erreurs en PHP
error_reporting(E_ALL);
ini_set("display_errors", 1);
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../css/normalize.css">
    <link rel="stylesheet" href="../../../css/styles-computer.css">
    <link rel="stylesheet" href="../../../css/styles-responsive.css">
    <link rel="shortcut icon" href="../../../img/favicon-jo-2024.ico" type="image/x-icon">
    <title>Ajouter un utilisateur - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages de gestion des utilisateurs -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="manage-users.php">Gestion des Utilisateurs</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un Utilisateur</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-users.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter cet utilisateur?')">
            <label for="nomUtilisateur">Nom :</label>
            <input type="text" name="nomUtilisateur" id="nomUtilisateur" required>
            <label for="prenomUtilisateur">Prénom :</label>
            <input type="text" name="prenomUtilisateur" id="prenomUtilisateur" required>
            <label for="login">Identifiant :</label>
            <input type="text" name="login" id="login" required>
            <label for="password">Mot de passe :</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" value="Ajouter l'utilisateur">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-users.php">Retour à la gestion des utilisateurs</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>

</body>

</html>
