<?php session_start();

 ?>
<nav>
    <ul>
        <li><a href="\Projet_academie\index.php">Accueil</a></li>

        <!-- Affichage en fonction de l'état de connexion -->
        <?php if (isset($_SESSION['userid'])): ?>
            <li><a href="/Projet_academie/app/actions/creatures/addcreatures.php">Ajouter une créature  </a></li>
            <li><a href="/Projet_academie/app/actions/sorts/addsort.php">Ajouter un sort </a></li>
            <li><a href="/Projet_academie/app/auth/logout.php">Se déconnecter</a></li>
            <?php echo"1"?>
        <?php else: ?>
            <li><a href="\Projet_academie\app\auth\login.php">Se connecter</a></li>
            <li><a href="\Projet_academie\app\auth\subscribe.php">S'inscrire</a></li>
            <?php echo"2"?>

        <?php endif; ?>
    </ul>
</nav>
