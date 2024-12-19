<?php
// Inclure le fichier de configuration et démarrer la session
include('../../includes/function.php'); // Changez en fonction de votre fichier config

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['userid'])) {
    header('Location: ../auth/login.php'); // Rediriger vers la page de connexion si non connecté
    exit;
}
// Vérifier si le champ image est vide, sinon lui attribuer une valeur
// Vérifier si le champ image est vide, sinon lui attribuer une valeur
$imageUploaded = false; // Variable pour vérifier si l'image a été uploadée
$uniqueName = '';

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageName = $_FILES['image']['name'];
    $imageInfo = pathinfo($imageName);
    $imageExt = strtolower($imageInfo['extension']); // Convertir en minuscule pour éviter des problèmes d'extension
    $authorizedExt = ['png', 'jpeg', 'jpg', 'webp', 'bmp', 'svg'];

    // Vérification de l'extension du fichier
    if (in_array($imageExt, $authorizedExt)) {
        $uniqueName = time() . rand(1, 1000) . "." . $imageExt;
        if (move_uploaded_file($_FILES['image']['tmp_name'], "../../../assets/img/" . $uniqueName)) {
            $imageUploaded = true; // Indiquer que l'image a bien été téléchargée
        } else {
            echo "<p>Erreur lors du téléchargement de l'image.</p>";
        }
    } else {
        echo "<p>Veuillez choisir un format de fichier valide (png, jpg, webp, bmp, svg).</p>";
    }
}

     
     
    
  // Récupérer la liste des éléments depuis la table `elements`
  $queryread = $bdd->prepare("SELECT id_element, nom_element FROM element ");
  $queryread->execute();
// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nom_sorts = htmlspecialchars(trim($_POST['nom_sort'])); 

    $id_userr = $_SESSION['userid']; // L'utilisateur connecté
   
    

    $id_element=($_POST['element']) ;
    

      // Vérifier que tous les champs sont remplis
      if (!empty($nom_sorts) && !empty($uniqueName) && !empty($id_element)) {

                    // Préparer la requête d'insertion
                    $query = $bdd->prepare("INSERT INTO sorts 
                    (nom_sorts, image_sort, id_elemen, id_userr) 
                     VALUES (:nom_sorts, :image, :id_elemen, :id_userr)
                     ");


            $success = $query->execute([
           
            'nom_sorts' => $nom_sorts,
            'image' => $uniqueName,
            'id_elemen' => $id_element,
            'id_userr' => $id_userr
            
            ]);

     
       // Vérifier si l'insertion a réussi
       if ($success) {
           $message = "Sort ajouté avec succès.";
           header('Location:\Projet_academie\index.php');

       } else {
           $message = "Erreur lors de l'ajout du sort.";
       }
   } else {
       $message = "Veuillez remplir tous les champs.";
   }
}

?>
<?php include('../../includes/head.php'); ?>

<body>
   <?php include('../../includes/nav.php'); ?>

   <h1>Ajouter un Sort</h1>

   <!-- Afficher un message (succès ou erreur) -->
   <?php if (isset($message)): ?>
       <p><?php echo $message; ?></p>
   <?php endif; ?>

   <!-- Formulaire pour ajouter un sort -->
   <form id="image" action="addsort.php" method="POST" enctype='multipart/form-data'>
       <label for="nom_sort">Nom du Sort :</label>
       <input type="text" id="nom_sort" name="nom_sort" required>
       <label for="element">Élément :</label>
       <select id="element" name="element" required>
           <?php while ($element = $queryread->fetch()): ?>
               <option value="<?php echo $element['id_element']; ?>">
                <?php echo htmlspecialchars($element['nom_element']); ?>
               </option>
           <?php endwhile; ?>
       </select>

       <label for="image">Choisissez une image :</label>
       <input id="image" type="file" name="image">


       <button type="submit">Ajouter la Sort</button>
   </form>

</body>
</html>
