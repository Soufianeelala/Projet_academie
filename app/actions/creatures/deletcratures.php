<?php
// Inclure le fichier de configuration et démarrer la session
include('../../includes/function.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['userid']) ) {
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

// Vérifier si la créature existe et appartient à l'utilisateur connecté
$query = $bdd->prepare("SELECT * FROM creature WHERE id_creature = :id_creature AND id_per = :id_per");
$query->execute([
    'id_creature' => $id_creature,
    'id_per' => $_SESSION['userid'],
]);
$creature = $query->fetch(PDO::FETCH_ASSOC);
 if($_SESSION['userid']==$creature['id_per'] || ($_SESSION['roleid']==1)){

    if (!$creature) {
        echo "Créature introuvable ou vous n'avez pas les droits pour la supprimer.";
        exit;
    }

    // Supprimer l'image associée (si elle existe)
    if (!empty($creature['image_creature'])) {
        $imagePath = "../../../assets/img/" . $creature['image_creature'];
        if (file_exists($imagePath)) {
            unlink($imagePath); // Supprimer le fichier image
        }
    }

    // Supprimer la créature de la base de données
    $query = $bdd->prepare("DELETE FROM creature WHERE id_creature = :id_creature AND id_per = :id_per");
    $success = $query->execute([
        'id_creature' => $id_creature,
        'id_per' => $_SESSION['userid'],
    ]);

    if ($success) {
        header('Location: \Projet_academie\index.php?success=1'); // Redirection après suppression
        exit;
    } else {
        echo "Erreur lors de la suppression de la créature.";
    }
 }else{
    header('Location: \Projet_academie\index.php');

 }
?>
