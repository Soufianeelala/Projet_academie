<?php
// Inclure le fichier de configuration et démarrer la session
include('../../includes/function.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['userid'])) {
    header('Location: ../auth/login.php'); // Rediriger vers la page de connexion si non connecté
    exit;
}

// Vérifier si un ID de créature est fourni dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Identifiant de créature manquant.";
    exit;
}

// Récupérer l'ID de la créature
$id_creature = intval($_GET['id']);

// Récupérer les informations de la créature dans la base de données
$query = $bdd->prepare("SELECT * FROM creature WHERE id_creature = :id_creature");
$query->execute(['id_creature' => $id_creature]);
$creature = $query->fetch(PDO::FETCH_ASSOC);

// Vérifier si la créature existe
if (!$creature) {
    echo "Créature introuvable.";
    exit;
}
if(($_SESSION['userid']==$creature['id_user']) || ($_SESSION['roleid']==1)): 

    // Gérer la mise à jour si le formulaire est soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupérer les données du formulaire
        $nom_creature = htmlspecialchars(trim($_POST['nom_creature']));
        $description = htmlspecialchars(trim($_POST['description']));
        $id_type_crea = intval($_POST['id_type_crea']);
        $id_per = $_SESSION['userid']; // ID de l'utilisateur connecté

        // Vérifier si une image a été uploadée
        if (!empty($_FILES['image']['name'])) {
            $imageName = sanitarize($_FILES['image']['name']);
            $imageInfo = pathinfo($imageName);
            $imageExt = $imageInfo['extension'];

            // Vérification des extensions autorisées
            $authorizedExt = ['png', 'jpeg', 'jpg', 'webp', 'bmp', 'svg'];

            if (in_array($imageExt, $authorizedExt)) {
                $img = time() . rand(1, 1000) . "." . $imageExt;
                move_uploaded_file($_FILES['image']['tmp_name'], "../../../assets/img/" . $img);
            } else {
                echo 'Extension non autorisée.';
                exit;
            }
        } else {
            // Si aucune nouvelle image n'est fournie, conserver l'ancienne
            $img = $creature['image_creature'];
        }

        // Vérifier que tous les champs nécessaires sont remplis
        if (!empty($nom_creature) && !empty($description) && !empty($id_type_crea)) {
            // Mise à jour des données dans la base de données
            $query = $bdd->prepare("UPDATE creature 
                                    SET nom_creature = :nom_creature, 
                                        description = :description, 
                                        id_type_crea = :id_type_crea, 
                                        image_creature = :image 
                                    WHERE id_creature = :id_creature AND id_per = :id_per");
            $success = $query->execute([
                'nom_creature' => $nom_creature,
                'description' => $description,
                'id_type_crea' => $id_type_crea,
                'image' => $img,
                'id_creature' => $id_creature,
                'id_per' => $id_per,
            ]);

            if ($success) {
                $message = "Créature mise à jour avec succès.";
                header('Location: ../creatures/creatures.php'); // Rediriger vers la page creature
            } else {
                $message = "Erreur lors de la mise à jour de la créature.";
            }
        } else {
            $message = "Veuillez remplir tous les champs.";
        }
    }
}else{
    header('Location: \Projet_academie\index.php');
 }
?>

<?php include('../../includes/head.php'); ?>

<body>
    <?php include('../../includes/nav.php'); ?>

    <h1>Modifier une Créature</h1>

    <!-- Afficher un message (succès ou erreur) -->
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Formulaire pour modifier la créature -->
    <form action="updatecreatures.php?id=<?php echo $id_creature; ?>" method="POST" enctype="multipart/form-data">
        <label for="nom_creature">Nom de la Créature :</label>
        <input type="text" id="nom_creature" name="nom_creature" 
               value="<?php echo htmlspecialchars($creature['nom_creature']); ?>" required>

        <label for="description">Description :</label>
        <textarea id="description" name="description" required><?php echo htmlspecialchars($creature['description']); ?></textarea>

        <label for="id_type_crea">Type de Créature :</label>
        <select id="id_type_crea" name="id_type_crea" required>
            <option value="1" <?php echo ($creature['id_type_crea'] == 1) ? 'selected' : ''; ?>>Aquatique</option>
            <option value="2" <?php echo ($creature['id_type_crea'] == 2) ? 'selected' : ''; ?>>Démoniaque</option>
            <option value="3" <?php echo ($creature['id_type_crea'] == 3) ? 'selected' : ''; ?>>Mort Vivante</option>
            <option value="4" <?php echo ($creature['id_type_crea'] == 4) ? 'selected' : ''; ?>>Mi-Bête</option>
        </select>

        <label for="image">Changer l'image  :</label>
        <input id="image" type="file" name="image">

        <!-- Afficher l'image actuelle -->
        <?php if (!empty($creature['image_creature'])): ?>
            <p>Image actuelle :</p>
            <img src="../../../assets/img/<?php echo htmlspecialchars($creature['image_creature']); ?>" 
                 alt="Image de <?php echo htmlspecialchars($creature['nom_creature']); ?>" 
                 style="max-width: 200px; height: auto;">
        <?php endif; ?>

        <button type="submit">Mettre à jour la Créature</button>
    </form>
</body>
</html>
