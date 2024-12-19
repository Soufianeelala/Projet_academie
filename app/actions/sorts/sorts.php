<?php
// Inclure le fichier de configuration
include('../../includes/function.php'); 

// // Vérifier si l'utilisateur est connecté
// if (!isset($_SESSION['userid'])) {
//     header('Location: ../auth/login.php'); // Rediriger vers la page de connexion si non connecté
//     exit;
// }

// Récupérer les données des créatures
$query = $bdd->prepare("
   SELECT 
        s.id_sorts ,
        s.nom_sorts AS nom_sorts,
        s.image_sort AS image_sort,
        s.id_userr AS id_userr,
        u.id_user AS id_user,
        u.username AS username,
        e.nom_element AS nom_element
    FROM sorts s 
        INNER JOIN user u ON s.id_userr = u.id_user
        INNER JOIN user_element ue ON ue.id_us = u.id_user
        INNER JOIN element e ON e.id_element = ue.id_elemen;

    
");
$query->execute();
$sorts = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include('../../includes/head.php'); ?>

<body>
    <?php include('../../includes/nav.php'); ?>

    <h1>Les Sorts</h1>

    <!-- Liste des créatures -->
    <?php if (!empty($sorts)): ?>
        <?php foreach ($sorts as $sorts): ?>
            <article class="sorts-article">
                <h2><?php echo htmlspecialchars($sorts['nom_sorts']); ?></h2>
                <p><strong>le Nom d'élément :</strong> <?php echo htmlspecialchars($sorts['nom_element']); ?></p>
                <p><strong>la personne qui a crée la sort  :</strong> <?php echo htmlspecialchars($sorts['username']); ?></p>
                
                <!-- Afficher l'image de la sorts  si disponible -->
                <?php if (!empty($sorts['image_sort'])): ?>
                    
                    <img  src="/projet_academie/assets/img/<?php echo $sorts['image_sort']; ?>" 
                         alt="Image de <?php echo $sorts['image_sort']; ?>" 
                         style="max-width: 300px; height: auto;" ><br>
                         <?php if(isset($_SESSION['userid'])): ?>
                <?php if($_SESSION['userid']==$sorts['id_userr']): ?>
                <a href="/Projet_academie/app/actions/sorts/updatsort.php?id=<?php echo $sorts['id_sorts']?> ">modifier</a>
                <a href="/Projet_academie/app/actions/sorts/deletsort.php?id=<?php echo $sorts['id_sorts']?> ">supprimer</a>
                <?php endif ?>
            <?php endif ?>

                <?php endif; ?>
            </article>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune sorts n'a encore été ajoutée.</p>
    <?php endif; ?>
</body>
</html>
