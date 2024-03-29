<?php
session_start();

// Vérifiez si l'utilisateur est connecté
if (!isset($_SESSION['login'])) {
    header('Location: ../../../index.php');
    exit();
}

$login = $_SESSION['login'];
$nom_utilisateur = $_SESSION['nom_utilisateur'];
$prenom_utilisateur = $_SESSION['prenom_utilisateur'];
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
    <title>Gestion Athlètes - Jeux Olympiques 2024</title>
    <style>
        /* Ajoutez votre style CSS ici */
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .action-buttons button {
            background-color: #1b1b1b;
            color: #d7c378;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .action-buttons button:hover {
            background-color: #d7c378;
            color: #1b1b1b;
        }
    </style>
</head>

<body>
    <header>
        <nav>
            <!-- Menu vers les pages de gestion des athlètes, genres et pays -->
            <ul class="menu">
                <li><a href="../admin.php">Accueil Administration</a></li>
                <li><a href="manage-sports.php">Gestion Sports</a></li>
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
        <h1>Gestion des Athlètes</h1>
        <div class="action-buttons">
            <button onclick="openAddAthletesForm()">Ajouter un Athlète</button>
            <!-- Autres boutons... -->
        </div>
        <!-- Liste des athlètes -->
        <?php
        require_once("../../../database/database.php");

        try {
            // Requête pour récupérer la liste des athlètes depuis la base de données
            $query = "SELECT a.*, g.nom_genre, p.nom_pays 
                      FROM athlete a
                      LEFT JOIN genre g ON a.id_genre = g.id_genre
                      LEFT JOIN pays p ON a.id_pays = p.id_pays
                      ORDER BY a.nom_athlete, a.prenom_athlete";
            $statement = $connexion->prepare($query);
            $statement->execute();
        
            // Vérifier s'il y a des résultats
            if ($statement->rowCount() > 0) {
                echo "<table><tr><th>Nom</th><th>Prénom</th><th>Genre</th><th>Pays</th><th>Modifier</th><th>Supprimer</th></tr>";

                // Afficher les données dans un tableau
                while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>";
                    // Assainir les données avant de les afficher
                    echo "<td>" . htmlspecialchars($row['nom_athlete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['prenom_athlete']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_genre']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_pays']) . "</td>";
                    echo "<td><button onclick='openModifyAthletesForm({$row['id_athlete']})'>Modifier</button></td>";
                    echo "<td><button onclick='deleteAthletesConfirmation({$row['id_athlete']})'>Supprimer</button></td>";
                    echo "</tr>";
                }

                echo "</table>";
            } else {
                echo "<p>Aucun athlète trouvé.</p>";
            }
        } catch (PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
        ?>
        <p class="paragraph-link">
            <a class="link-home" href="../admin.php">Accueil administration</a>
        </p>

    </main>
    <footer>
        <figure>
            <img src="../../../img/logo-jo-2024.png" alt="logo jeux olympiques 2024">
        </figure>
    </footer>
    <script>
        function openAddAthletesForm() {
            // Ouvrir une fenêtre pop-up avec le formulaire d'ajout
            window.location.href = 'add-athletes.php';
        }

        function openModifyAthletesForm(id_athlete) {
            // Ajoutez ici le code pour afficher un formulaire stylisé pour modifier un athlète
            window.location.href = 'modify-athletes.php?id_athlete=' + id_athlete;
        }

        function deleteAthletesConfirmation(id_athlete) {
            // Ajoutez ici le code pour afficher une fenêtre de confirmation pour supprimer un athlète
            if (confirm("Êtes-vous sûr de vouloir supprimer cet athlète?")) {
                window.location.href = 'delete-athletes.php?id_athlete=' + id_athlete;
            }
        }

    </script>
</body>

</html>
