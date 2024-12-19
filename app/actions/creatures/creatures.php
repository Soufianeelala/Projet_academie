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
        c.id_creature,
        c.nom_creature,
        c.description,
        c.image_creature,
        u.username AS auteur,
        u.id_user,
        tc.nom_type_creature AS type_creature
    FROM 
        creature c
    JOIN 
        user u ON c.id_per = u.id_user
    JOIN 
        type_creature tc ON c.id_type_crea = tc.id_type_creature
");
$query->execute();
$creatures = $query->fetchAll(PDO::FETCH_ASSOC);

?>

<?php include('../../includes/head.php'); ?>

<body>
    <?php include('../../includes/nav.php'); ?>

    <h1>Le Codex des Créatures</h1>

    <!-- Liste des créatures -->
    <?php if (!empty($creatures)): ?>
        <?php foreach ($creatures as $creature): ?>
            <article class="creature-article">
                <h2><?php echo htmlspecialchars($creature['nom_creature']); ?></h2>
                <p><strong>Description :</strong> <?php echo htmlspecialchars($creature['description']); ?></p>
                <p><strong>Type :</strong> <?php echo htmlspecialchars($creature['type_creature']); ?></p>
                <p><strong>Créée par :</strong> <?php echo htmlspecialchars($creature['auteur']); ?></p>
                
                <!-- Afficher l'image de la créature si disponible -->
                <?php if (!empty($creature['image_creature'])): ?>
                    
                    <img  src="/projet_academie/assets/img/<?php echo $creature['image_creature']; ?>" 
                         alt="Image de <?php echo $creature['nom_creature']; ?>" 
                         style="max-width: 300px; height: auto;" ><br>
                         <?php if(isset($_SESSION['userid'])): ?>
                <?php if(($_SESSION['userid']==$creature['id_user']) || ($_SESSION['roleid']==1)): ?>
                    
                <a href="/Projet_academie/app/actions/creatures/updatecreatures.php?id=<?php echo $creature['id_creature']?> ">modifier</a>
                <a href="/Projet_academie/app/actions/creatures/deletcratures.php?id=<?php echo $creature['id_creature']?> ">supprimer</a>
                <?php endif ?>
            <?php endif ?>

                <?php endif; ?>
            </article>
            <hr>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Aucune créature n'a encore été ajoutée.</p>
    <?php endif; ?>
</body>
</html>
