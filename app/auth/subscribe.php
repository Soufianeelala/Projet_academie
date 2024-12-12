<?php 
include('../includes/function.php');
// Methode alternative au isset qui permet de vérifier si des requêtes de type POST ont bien été envoyé
if($_SERVER['REQUEST_METHOD']==='POST'){
    $username = sanitarize($_POST['username']);
    $password = htmlspecialchars($_POST['password']);
    $passwordConfirm = htmlspecialchars($_POST['passwordConfirm']);

    // REQUETE read qui va permettre de lire la table user
    $request = $bdd->prepare('  SELECT COUNT(*) as id_user
                                FROM user 
                                WHERE username = ? '
                            
    );

    $request->execute(array($username));

    $data = $request->fetch();

    // Grace a la requete READ on peut vérifier si l'utilisateur existe deja en BDD 
    if($data['id_user'] >= 1){
        header('location:subscribe.php?error=2');
    }else{

        // Cryptage du mot de passe
        if($password == $passwordConfirm){
            $passwordCrypt = password_hash($password,PASSWORD_BCRYPT);

            // Préparation de la requête
            $request = $bdd->prepare('INSERT INTO user (username,password)
            VALUE (:username,:password)'
        );

            // Exécution de la requête
            $request->execute(array(
            'username' =>  $username,
            'password'  =>  $passwordCrypt
            ));

            // header('location:/C:\wamp64\www\Projet académie/index.php?success=4');
        }else{
            header('location:subscribe.php?error=1');
        }
    }   
}

?>

<?php include('../includes/head.php'); ?>
<body>
    <?php include('../includes/nav.php') ?>
    <h1>Inscription</h1>
    
    <?php if(isset($_GET['error'])){ ?>
       <?php  switch($_GET['error']){
                case 1:
                    echo "<p class='error'>Vos mots de passe ne correspondent pas</p>";
                    break;
                case 2:
                    echo "<p class='error'>Ce nom d'utilisateur existe déjà</p>";
                    break;
            }
        } ?>

    <form action="subscribe.php" method="post">
        <label for="username">Votre nom d'utilisateur</label>
        <input type="text" name="username" id="username">
        <label for="password">Votre mot de passe</label>
        <input type="password" name="password" id="password">
        <label for="passwordConfirm">Confirmez votre mot de passe</label>
        <input type="password" name="passwordConfirm" id="passwordConfirm">
       
          
        </select>
        <br><br>
        <button>s'inscrire</button>
    </form>
</body>
</html>