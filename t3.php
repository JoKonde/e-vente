<?php
session_start();


if (isset($_POST['ajouterAuPanier'])) {
    $produitId = $_POST['id'];
    $produitNom = $_POST['nom'];
    $prix = $_POST['prix'];
    $image = $_POST['image'];
    $qte = $_POST['qte'];

    ajouter($produitId, $produitNom, $prix, $image, $qte);
}





function ajouter($produitId, $produitNom, $prix, $image, $qte) {
    try {
        // Vérifier si le panier existe
    if (!isset($_SESSION['panier'])) {
        $_SESSION['panier'] = [];
    }

    // Vérifier si le produit est déjà dans le panier
    if (isset($_SESSION['panier'][$produitId])) {
        // Mettre à jour la quantité
        $_SESSION['panier'][$produitId]['qte'] += $qte;
    } else {
        // Ajouter un nouveau produit au panier
        $_SESSION['panier'][$produitId] = [
            'nom' => $produitNom,
            'prix' => $prix,
            'image' => $image,
            'qte' => $qte
        ];
    }
    $_SESSION['msgSuc'] = "Produit ajouté avec succès.";
    header("Location: index.php");
    } catch (\Throwable $th) {
        $_SESSION['msg'] = "Un Problème est survenu.";
        header("Location: index.php");
    }
    
}



?>
