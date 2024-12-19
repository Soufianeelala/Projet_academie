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
$id_sorts = intval($_GET['id']);

// Vérifier si la créature existe et appartient à l'utilisateur connecté
$query = $bdd->prepare("SELECT * FROM sorts WHERE id_sorts = :id_sorts AND id_userr = :id_userr");
$query->execute([
    'id_sorts' => $id_sorts,
    'id_userr' => $_SESSION['userid']
]);
$sorts = $query->fetch(PDO::FETCH_ASSOC);

if (!$sorts) {
    echo " Sort introuvable ou vous n'avez pas les droits pour la supprimer." ;
    exit;
}

// Supprimer l'image associée (si elle existe)
if (!empty($sorts['image_sorts'])) {
    $imagePath = "../../../assets/img/" . $sorts['image_sorts'];
    if (file_exists($imagePath)) {
        unlink($imagePath); // Supprimer le fichier image
    }
}

// Supprimer la créature de la base de données
$query = $bdd->prepare("DELETE FROM sorts WHERE id_sorts = :id_sorts AND id_userr = :id_userr");
$success = $query->execute([
    'id_sorts' => $id_sorts,
    'id_userr' => $_SESSION['userid'],
]);

if ($success) {
    echo "Sorti supprimée avec succès.";
    header('Location: \Projet_academie\index.php'); // Redirection après suppression
    exit;
} else {
    echo "Erreur lors de la suppression de la Sort.";
}
?>
