
<nav>
    <ul>
        <li><a href="\Projet_academie\index.php">Accueil</a></li>

        <!-- Affichage en fonction de l'état de connexion -->
        <?php if (isset($_SESSION['userid'])): ?>
            <li><a href="/Projet_academie/app/actions/creatures/addcreatures.php">Ajouter des créatures  </a></li>
            <li><a href="/Projet_academie/app/actions/sorts/addsort.php">Ajouter un sort </a></li>
            <li><a href="/Projet_academie/app/auth/logout.php">Se déconnecter</a></li>
            
        <?php else: ?>
            <li><a href="/Projet_academie/app/actions/creatures/creatures.php">les créatures  </a></li>
            <li><a href="/Projet_academie/app/actions/sorts/updatsort.php"> Les Sorts </a></li>

            <li><a href="\Projet_academie\app\auth\login.php">Se connecter</a></li>
            <li><a href="\Projet_academie\app\auth\subscribe.php">S'inscrire</a></li>
            

        <?php endif; ?>
    </ul>
</nav>
