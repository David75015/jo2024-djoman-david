<?php
session_start();
require_once("../../../database/database.php");

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

// Vérifiez si l'ID du lieu est fourni dans l'URL
if (!isset($_GET['id_lieu'])) {
    $_SESSION['error'] = "ID du lieu manquant.";
    header("Location: manage-places.php");
    exit();
}

$id_lieu = filter_input(INPUT_GET, 'id_lieu', FILTER_VALIDATE_INT);

// Vérifiez si l'ID du lieu est un entier valide
if (!$id_lieu && $id_lieu !== 0) {
    $_SESSION['error'] = "ID du lieu invalide.";
    header("Location: manage-places.php");
    exit();
}

// Récupérer les informations du lieu pour affichage dans le formulaire
try {
    $queryplaces = "SELECT nom_lieu, adresse_lieu, cp_ville, ville_lieu FROM lieu WHERE id_lieu = :idlieu";
    $statementplaces = $connexion->prepare($queryplaces);
    $statementplaces->bindParam(":idlieu", $id_lieu, PDO::PARAM_INT);
    $statementplaces->execute();

    if ($statementplaces->rowCount() > 0) {
        $places = $statementplaces->fetch(PDO::FETCH_ASSOC);
    } else {
        $_SESSION['error'] = "Lieu non trouvé.";
        header("Location: manage-places.php");
        exit();
    }
} catch (PDOException $e) {
    $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
    header("Location: manage-places.php");
    exit();
}

// Vérifiez si le formulaire est soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomlieu = $_POST['nomlieu'];
    $adresselieu = $_POST['adresselieu'];
    $cpville = $_POST['cpville'];
    $villelieu = $_POST['villelieu'];

    // Vérifiez si les champs sont vides
    if (empty($nomlieu) || empty($adresselieu) || empty($cpville) || empty($villelieu)) {
        $_SESSION['error'] = "Tous les champs sont obligatoires.";
        header("Location: modify-places.php?id_lieu=$id_lieu");
        exit();
    }

    try {
        // Requête pour mettre à jour le lieu
        $query = "UPDATE lieu 
                  SET nom_lieu = :nomlieu, 
                      adresse_lieu = :adresselieu, 
                      cp_ville = :cpville, 
                      ville_lieu = :villelieu 
                  WHERE id_lieu = :idlieu";
        $statement = $connexion->prepare($query);
        $statement->bindParam(":nomlieu", $nomlieu, PDO::PARAM_STR);
        $statement->bindParam(":adresselieu", $adresselieu, PDO::PARAM_STR);
        $statement->bindParam(":cpville", $cpville, PDO::PARAM_STR);
        $statement->bindParam(":villelieu", $villelieu, PDO::PARAM_STR);
        $statement->bindParam(":idlieu", $id_lieu, PDO::PARAM_INT);

        // Exécutez la requête
        if ($statement->execute()) {
            $_SESSION['success'] = "Les informations du lieu ont été modifiées avec succès.";
            header("Location: manage-places.php");
            exit();
        } else {
            $_SESSION['error'] = "Erreur lors de la modification du lieu.";
            header("Location: modify-places.php?id_lieu=$id_lieu");
            exit();
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = "Erreur de base de données : " . $e->getMessage();
        header("Location: modify-places.php?id_lieu=$id_lieu");
        exit();
    }
}
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
    <title>Modifier un lieu - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages de gestion des lieus -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="manage-places.php">Gestion des lieux</a></li>
                <li><a href="../../logout.php">Déconnexion</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <h1>Modifier un lieu</h1>
        <?php
        if (isset($_SESSION['error'])) {
            echo '<p style="color: red;">' . $_SESSION['error'] . '</p>';
            unset($_SESSION['error']);
        }
        ?>
        <form action="modify-places.php?id_lieu=<?php echo $id_lieu; ?>" method="post"
            onsubmit="return confirm('Êtes-vous sûr de vouloir modifier ce lieu?')">
            <label for="nomlieu">Nom du lieu :</label>
            <input type="text" name="nomlieu" id="nomlieu" value="<?php echo htmlspecialchars($places['nom_lieu']); ?>" required>

            <label for="adresselieu">Adresse :</label>
            <input type="text" name="adresselieu" id="adresselieu"
                value="<?php echo htmlspecialchars($places['adresse_lieu']); ?>" required>

            <label for="cpville">Code postal :</label>
            <input type="text" name="cpville" id="cpville" value="<?php echo htmlspecialchars($places['cp_ville']); ?>" required>

            <label for="villelieu">Ville :</label>
            <input type="text" name="villelieu" id="villelieu" value="<?php echo htmlspecialchars($places['ville_lieu']); ?>" required>

            <input type="submit" value="Modifier le lieu">
        </form>
        <p class="paragraph-link">
            <a class="link-home" href="manage-places.php">Retour à la gestion des lieux</a>
        </p>
    </main> 
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
</body>

</html>
