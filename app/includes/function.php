<?php 
//Ligne nécessaire à l'utilisation de la superglobale de session
session_start();
//echo  $_SESSION['roleid'];
 
//Connection a la base de donnée grace a l'objet PDO
//'sbdgr_utilisé:host=ip_de_la_bdd:dbname=le_nom_de_la_bdd;charset=utf8','username_de_bdd','password_de_bdd'
$bdd = new PDO('mysql:host=localhost;dbname=projet_académie;charset=utf8', 'root', '');

// Création d'une fonction pour: 
//     -enlever les caractères de code(htmlspecialchar), 
//     -les espaces(trim),
//     -mettre tout en minuscule(strtolower)
function sanitarize($input){
    return htmlspecialchars(trim(strtolower($input)));
}