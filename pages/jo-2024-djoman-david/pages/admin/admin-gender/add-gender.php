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
    $nomgenre = filter_input(INPUT_POST, 'nomgenre', FILTER_SANITIZE_STRING);

    // Vérifiez si le nom du genre est vide
    if (empty($nomgenre)) {
        $_SESSION['error'] = "Le nom du genre ne peut pas être vide.";
        header("Location: add-gender.php");
        exit();
    }

    try {
        // Vérifiez si le genre existe déjà
        $queryCheck = "SELECT id_genre FROM genre WHERE nom_genre = :nomgenre";
        $statementCheck = $connexion->prepare($queryCheck);
        $statementCheck->bindParam(":nomgenre", $nomgenre, PDO::PARAM_STR);
        $statementCheck->execute();

        if ($statementCheck->rowCount() > 0) {
            $_SESSION['error'] = "Le genre existe déjà.";
            header("Location: add-gender.php");
            exit();
        } else {

            // Requête pour ajouter un genre
            $query = "INSERT INTO genre (nom_genre) VALUES (:nomgenre)";
            $statement = $connexion->prepare($query);
            $statement->bindParam(":nomgenre", $nomgenre, PDO::PARAM_STR);

            // Exécutez la requête
            if ($statement->execute()) {
                $_SESSION['success'] = "Le genre a été ajouté avec succès.";
                header("Location: manage-gender.php");
                exit();
            } else {
                $_SESSION['error'] = "Erreur lors de l'ajout du genre.";
                header("Location: add-gender.php");
                exit();
            }
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: add-gender.php");
        exit();
    }
}
// Afficher les erreurs en PHP
// (fonctionne à condition d’avoir activé l’option en local)
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
    <title>Ajouter un genre - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages gender, events, et results -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="manage-gender.php">Gestion gender</a></li>
                <li><a href="manage-places.php">Gestion Lieux</a></li>
                <li><a href="manage-events.php">Gestion Calendrier</a></li>
                <li><a href="manage-countries.php">Gestion Pays</a></li>
                <li><a href="manage-gender.php">Gestion Genres</a></li>
                <li><a href="manage-athletes.php">Gestion Athlètes</a></li>
                <li><a href="manage-results.php">Gestion Résultats</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Ajouter un genre</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="add-gender.php" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir ajouter ce genre?')"">
            <label for=" nomgenre">Nom du genre :</label>
            <input type="text" name="nomgenre" id="nomgenre" required>
            <input type="submit" value="Ajouter le genre">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-gender.php">Retour à la gestion des gender</a>
        </p>
    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>

</body>

</html>