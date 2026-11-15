<?php
require_once("conexao.php");

$r = $con->prepare("DELETE FROM membros WHERE id = :id");
$r->bindParam(":id", $_GET['id']);
$r->execute();

header("Location: consulta_membros.php");