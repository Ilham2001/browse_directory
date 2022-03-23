<?php
session_start();
// Initialiser l'objet PDO
$pdo = new PDO('mysql:host=localhost;dbname=db_upload_files', 'root', '');
// Utiliser une requête préparée pour se connecter à la BDD
$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$all_rows_query = "SELECT * FROM `files`";
// Préparer et exécuter la requête
$statement = $pdo->prepare($all_rows_query);
$statement->execute();
// Récupérer toutes les fichiers
$rows = $statement->fetchAll();

// Récupérer la page courante à partir de l'URL
if (isset($_GET['page'])) $page = $_GET['page'];
if (empty($page)) $page = 1; // Selectionner la page 1 par défaut

// Nombres de fichiers dans une page
$nb_rows_in_page = 6;

// Diviser toutes les lignes par le nombre de lignes dans un page pour avoir le nombre total de pages
$nb_pages = ceil(count($rows) / $nb_rows_in_page); // Fonction ceil pour avoir un entier

// Numéro de la ligne par quoi on doit commencer dans chaque page
$start = ($page - 1) * $nb_rows_in_page;

$query = "SELECT * FROM files ORDER BY id DESC LIMIT ?, ?";
$statement = $pdo->prepare($query);
// start est la ligner du début dans la bdd et nb_rows_in_page est le nombre de lignes à récupérer
$statement->execute(array($start, $nb_rows_in_page));
$page_rows = $statement->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style-pagination.css" />
    <title>Pagination des images</title>
</head>

<body>
    <div class="msg">
        <?php
        if (isset($_SESSION['message'])) {
            echo '<p>' . $_SESSION['message'] . '</p>';
            unset($_SESSION['message']);
        }
        ?>
    </div>
    <h2>Liste des images importées</h2>
    <center>
        <div class="images-block">
            <?php
            $cmpt =  1;
            if (count($page_rows) > 0) {
                foreach ($page_rows as $row) {
                    $cmpt++;
                    if ($cmpt % 2 == 0) echo '<br/>';
                    echo '<img src=' . $row['path'] . '>';
                }
            }
            ?>
        </div>
    </center>
</body>
<center>
    <div class="pagination">
        <?php
        // Ne page afficher le bouton précédant dans la première page
        if ($page != 1) echo '<a href="?page=' . ($page - 1) . '">&laquo;</a>';
        for ($i = 1; $i <= $nb_pages; $i++) {
            // Vérifier si une page n'est pas selectionné
            if ($page != $i) {
                echo '<a href="?page=' . $i . '">' . $i . '</a> &nbsp;';
            }
            // Si une page est selectionnée on applique un style
            else {
                echo '<a href="?page=' . $i . '" class="active">' . $i . '</a> &nbsp;';
            }
        }
        // Ne page afficher le bouton suivant dans la première page et si on n'a pas d'images
        if ($page != $nb_pages && $nb_pages != 0) echo '<a href="?page=' . ($page + 1) . '">&raquo;</a>';
        ?>
    </div>
    <br /><br />
</center>
<center>
    <form action="script.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="file" id="file">
        <input type="submit" value="Envoyer" name="submit" />
    </form>
</center>

</html>