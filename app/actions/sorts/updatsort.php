<?php
// Inclure le fichier de configuration et démarrer la session
include('../../includes/function.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['userid'])) {
    header('Location: ../auth/login.php'); // Rediriger vers la page de connexion si non connecté
    exit;
}
var_dump($_SESSION['userid']);
// Vérifier si un ID de sorts est fourni dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Identifiant de sort manquant.";
    exit;
}

// Récupérer l'ID de la sort
 

$id_sorts = intval($_GET['id']);


  
// Récupérer la liste des éléments depuis la table `elements`
  $queryread = $bdd->prepare("SELECT id_element, nom_element FROM element ");
  $queryread->execute();

// Récupérer les informations de la sort dans la base de données
$query = $bdd->prepare("SELECT * FROM sorts WHERE id_sorts = :id_sorts AND id_userr = :id_userr ");
$query->execute([
    'id_sorts' => $id_sorts,
    'id_userr' => $_SESSION['userid']

]);
$sorts = $query->fetch(PDO::FETCH_ASSOC);

// Vérifier si la créature existe
if (!$sorts) {
    echo "sorts introuvable.";
    exit;
}

// Gérer la mise à jour si le formulaire est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom_sorts = htmlspecialchars(trim($_POST['nom_sorts']));
    
   
    $id_userr = $_SESSION['userid']; // ID de l'utilisateur connecté

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
        $img = $sorts['image_sort'];
        
    }
    // Vérifier que tous les champs nécessaires sont remplis
    if (!empty($nom_sorts) && !empty($img) && !empty($id_element)) {
        // Mise à jour des données dans la base de données
        $query = $bdd->prepare("UPDATE sorts 
                                SET 
                                    nom_sorts = :nom_sorts, 
                                    image_sort = :image ,
                                    id_elemen = :id_elemen, 
                                    id_userr = :id_userr  
                                WHERE id_sorts = :id_sorts AND id_userr = :id_userr");
        $success = $query->execute([
            'id_sorts' => $id_sorts,
            'nom_sorts' => $nom_sorts,           
            'image' => $img,
            'id_elemen' => $id_element,
            'id_userr' => $id_userr
        ]);


        if ($success) {
            $message = "sort mise à jour avec succès.";
        } else {
            $message = "Erreur lors de la mise à jour de la sort.";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}

?>

<?php include('../../includes/head.php'); ?>

<body>
    <?php include('../../includes/nav.php'); ?>

    <h1>Modifier une Sort</h1>

    <!-- Afficher un message (succès ou erreur) -->
    <?php if (isset($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <!-- Formulaire pour modifier la créature -->
    <!-- Formulaire pour ajouter un sort -->
    <form id="image" action="updatsort.php?id=<?php echo $id_sorts; ?>" method="POST" enctype='multipart/form-data'>
    <label for="nom_sorts">Nom du Sort :</label>
       <input type="text" id="nom_sorts" name="nom_sorts"  value="<?php echo htmlspecialchars($sorts['nom_sorts']); ?>" required> 
       <label for="element">Élément :</label>
       <select id="element" name="id_element" required>
       <?php while ($element = $queryread->fetch()): ?>
               <option value="<?php echo $element['id_element']; ?>">
                <?php echo htmlspecialchars($element['nom_element']); ?>
               </option>
           <?php endwhile; ?>
       </select>
<!-- Afficher l'image actuelle -->
<?php if (!empty($sorts['image_sort'])): ?>
            <p>Image actuelle :</p>
            <img src="../../../assets/img/<?php echo htmlspecialchars($sorts['image_sort']); ?>" 
                 alt="Image de <?php echo htmlspecialchars($sorts['nom_sorts']); ?>" 
                 style="max-width: 200px; height: auto;">
        <?php endif; ?>

        <label for="image">Changer l'image  :</label>
        <input id="image" type="file" name="image">



        <button type="submit">Mettre à jour la Sort</button>
    </form>

</body>
</html>
