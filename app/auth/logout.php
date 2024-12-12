<?php
include('../includes/function.php');
session_unset();
session_destroy();
header('location:\Projet_academie\index.php');