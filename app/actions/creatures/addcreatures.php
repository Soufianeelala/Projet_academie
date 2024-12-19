<?php
// Inclure le fichier de configuration et démarrer la session
include('../../includes/function.php'); // Changez en fonction de votre fichier config

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['userid'])) {
    header('Location: ../auth/login.php'); // Rediriger vers la page de connexion si non connecté
    exit;
}

// Vérifier si le champ image est vide, sinon lui attribuer une valeur
if (empty($_FILES['image']['name'])) {
    $img = NULL;
} else {
    $imageName = sanitarize($_FILES['image']['name']);
    $imageInfo = pathinfo($imageName);
    $imageExt = $imageInfo['extension'];

    // Tableau qui spécifie les extensions autorisées
    $authorizedExt = ['png', 'jpeg', 'jpg', 'webp', 'bmp', 'svg'];

    // Vérification de l'extension du fichier
    if (in_array($imageExt, $authorizedExt)) {
        $img = time() . rand(1, 1000) . "." . $imageExt;
        move_uploaded_file($_FILES['image']['tmp_name'], "../../../assets/img/" . $img );
    } else {
        echo 'Extension non autorisée.';
        exit;
    }
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom_creature = htmlspecialchars(trim($_POST['nom_creature']));
    $description = htmlspecialchars(trim($_POST['description']));
    $id_type_crea = intval($_POST['id_type_crea']);
    $id_per = $_SESSION['userid']; // L'utilisateur connecté

    // Vérifier que les champs requis sont remplis
    if (!empty($nom_creature) && !empty($description) && !empty($id_type_crea)) {
        // Préparer la requête d'insertion
        $query = $bdd->prepare("INSERT INTO creature (nom_creature, description, id_type_crea, id_per, image_creature) 
                                VALUES (:nom_creature, :description, :id_type_crea, :id_per, :image)");
        $success = $query->execute([
            'nom_creature' => $nom_creature,
            'description' => $description,
            'id_type_crea' => $id_type_crea,
            'id_per' => $id_per,
            'image' => $img,
        ]);

        // Vérifier si l'insertion a réussi
        if ($success) {
            $message = "Créature ajoutée avec succès !";
        } else {
            $message = "Erreur lors de l'ajout de la créature.";
        }
    } else {
        $message = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<?php include('../../includes/head.php'); ?>

<body>
    <?php include('../../includes/nav.php'); ?>

    <h1>Ajouter une Créature</h1>

    <!-- Afficher un message (succès ou erreur) -->
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Formulaire pour ajouter une créature -->
    <form action="addcreatures.php" method="POST" enctype="multipart/form-data">
        <label for="nom_creature">Nom de la Créature :</label>
        <input type="text" id="nom_creature" name="nom_creature" required>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required></textarea>

        <label for="id_type_crea">Type de Créature :</label>
        <select id="id_type_crea" name="id_type_crea" required>
            <option value="1">Aquatique</option>
            <option value="2">Démoniaque</option>
            <option value="3">Mort Vivante</option>
            <option value="4">Mi-Bête</option>
        </select>

        <label for="image">Choisissez une image :</label>
        <input id="image" type="file" name="image">

        <button type="submit">Ajouter la Créature</button>
    </form>
</body>
</html>
