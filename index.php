<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" href="css/style.css" />
	<title>Lecture récursive</title>
</head>

<body>
	<p>
		<b class="title">DEBUT DU PROCESSUS :</b>
		<br />
		<?php echo " ", date("h:i:s"); ?>
	</p>
	<?php

	// Fixer le temps d'expiration du script après 500 secondes
	set_time_limit(500);
	$path = "docs";

	// Appeler la fonction pour la première fois pour parcourir le répértoire docs
	explorerDir($path);


	function explorerDir($path)
	{
		// Ouvrir le répertoire
		$folder = opendir($path);

		// Récupérer le nom de chaque entrée du dossier (fichier ou dossier)
		// Tant qu'on a une entrée la boucle ne s'arrête pas jusqu'au dernier élément
		while ($entree = readdir($folder)) {
			// Si le nom de l'entrée n'est pas "." qui veut dire le dossier courant ou ".." le dossier précédent
			if ($entree != "." && $entree != "..") {

				// Si l'entrée est un dossier
				if (is_dir($path . "/" . $entree)) {
					echo '<div class="folder"> &nbsp; &nbsp ++<b>DOSSIER : ' . $entree . '</b></div>';

					// Récupérer le chemin du dossier courant
					$sav_path = $path;

					// Concaténer le chemin du dossier docs avec le celui du dossier courant (exemple : docs/dir1)
					$path .= "/" . $entree;

					// Appeler la fonction pour parcourir le sous-dossier courant	
					explorerDir($path);

					// Affecter au path le chemin du dossier parent du dossier courant (c'est-à-dire revenir vers l'arrière)
					$path = $sav_path;
				}
				// Si l'entrée est un fichier
				else {
					echo '<li> &nbsp; &nbsp;<b>FICHIER : </b>' . $entree . '</li>';
					// Récupérer le chemin complet du fichier courant
					$path_source = $path . "/" . $entree;

					// Récupérer l'extension et la taille de l'image
					$extension = pathinfo($entree, PATHINFO_EXTENSION);
					$size = filesize($path_source);
					$accepted_extensions = ["png", "jpeg", "jpg", "pjpeg"];

					// Vérifier si le fichier est une image		
					if (in_array($extension, $accepted_extensions)) {

						// Connexion à la base de données
						$pdo = new PDO('mysql:host=localhost;dbname=db_upload_files', 'root', '');
						$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
						$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

						// Insertion des informations de l'image dans la base de données
						$query =  "INSERT INTO files (name, type, path, size) VALUES (?, ?, ?, ?)";
						$statement = $pdo->prepare($query);
						$statement->execute(array($entree, $extension, $path_source, $size));
					}
				}
			}
		}
		closedir($folder);
	}
	?>
	<p>
		<b>FIN DU PROCESSUS :</b>
		<br />
		<?php echo " ", date("h:i:s"); ?>
	</p>

</body>

</html>