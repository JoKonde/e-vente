<?php
/**
 * Paiement Mobile Money Simplifié - Utilise la classe GenesysPayAPI
 * 
 * Cette version utilise notre classe utilitaire pour une gestion plus propre
 * et modulaire des paiements GenesysPay.
 */

session_start();

// Inclure la classe GenesysPayAPI
require_once 'GenesysPayAPI.php';

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['idUser'])) {
    header('Location: connexion.php');
    exit();
}

// Vérification si le panier existe et n'est pas vide
if (!isset($_SESSION['panier']) || empty($_SESSION['panier'])) {
    $_SESSION['msg'] = "Votre panier est vide. Veuillez ajouter des produits.";
    header('Location: index.php');
    exit();
}

// Calcul du total du panier
$totalPrix = 0;
foreach ($_SESSION['panier'] as $produit) {
    $totalPrix += $produit['prix'] * $produit['qte'];
}

// Initialisation de l'API GenesysPay
$genesysPay = new GenesysPayAPI();

// Traitement du formulaire de paiement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['payer_mobile_money'])) {
    
    // Validation du montant
    if ($totalPrix <= 0) {
        $_SESSION['msg'] = "Montant invalide pour le paiement.";
        header('Location: index.php');
        exit();
    }
    
    // Récupération du numéro de téléphone depuis le formulaire
    $phoneNumber = $_POST['phone_number'] ?? '';
    
    // Validation et formatage du numéro de téléphone
    if (empty($phoneNumber)) {
        $_SESSION['msg'] = "Le numéro de téléphone est requis pour le paiement mobile money.";
        header('Location: paiement_mobile_simple.php');
        exit();
    }
    
    $formattedPhone = $genesysPay->formatPhoneNumber($phoneNumber);
    
    // Récupération du provider
    $provider = $_POST['provider'] ?? '';
    $providersAllowed = ['mpesa_cd', 'airtel_cd', 'orange_cd'];
    if (!in_array($provider, $providersAllowed)) {
        $_SESSION['msg'] = "Veuillez sélectionner un opérateur mobile money valide.";
        header('Location: paiement_mobile_simple.php');
        exit();
    }
    
    // Préparation des données de paiement
    $paymentData = [
        'order_id' => $genesysPay->generateOrderId(),
        'amount' => $genesysPay->formatAmount($totalPrix),
        'customer_email' => $_SESSION['email'] ?? '',
        'customer_name' => $_SESSION['noms'] ?? '',
        'phone_number' => $formattedPhone, // Ajout du numéro de téléphone
        'provider' => $provider, // Ajout du provider
        'callback_url' => 'http://localhost/e-vente/genesyspay_callback.php',
    ];
    
    // Initialiser le paiement
    $result = $genesysPay->initMobileMoneyPayment($paymentData);
    
    // Logger la transaction pour le débogage
    $genesysPay->logTransaction('init_payment', $paymentData, $result);
    
    if ($result['success']) {
        // Sauvegarder les informations de la transaction en session
        $_SESSION['transaction'] = [
            'order_id' => $paymentData['order_id'],
            'amount' => $totalPrix,
            'status' => 'pending',
            'timestamp' => time()
        ];
        // Afficher un message de succès clair
        $msg = "<strong>Paiement en attente de validation !</strong><br>Veuillez confirmer l'opération sur votre téléphone. Vous recevrez un SMS de confirmation.";
        $_SESSION['msgSuc'] = $msg;
        header('Location: paiement_mobile_simple.php');
        exit();
    } else {
        // Message d'erreur utilisateur simple
        $_SESSION['msg'] = "Une erreur est survenue lors de l'initialisation du paiement. Veuillez vérifier vos informations ou réessayer plus tard.";
        header('Location: paiement_mobile_simple.php');
        exit();
    }
}

// Générer un ID de commande pour l'affichage
$orderId = $genesysPay->generateOrderId();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement Mobile Money - E-Vente</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,700" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="css/bootstrap.min.css" />
    <link type="text/css" rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="css/font-awesome.min.css">
</head>
<body>
    <!-- HEADER -->
    <header>
        <div id="top-header">
            <div class="container">
                <ul class="header-links pull-right">
                    <li><a href="index.php"><i class="fa fa-home"></i> Accueil</a></li>
                    <?php if (isset($_SESSION['email'])): ?>
                        <li><a href="#"><i class="fa fa-user"></i> <?php echo $_SESSION['noms']; ?></a></li>
                        <li><a href="deconnexion.php"><i class="fa fa-sign-out"></i> Déconnexion</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </header>

    <!-- SECTION PRINCIPALE -->
    <div class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-8 col-md-offset-2">
                    <div class="section-title">
                        <h3 class="title">Paiement Mobile Money</h3>
                        <p class="text-muted">Paiement sécurisé via GenesysPay</p>
                    </div>
                    
                    <!-- Résumé de la commande -->
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4><i class="fa fa-shopping-cart"></i> Résumé de votre commande</h4>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Nombre de produits:</strong> <?php echo count($_SESSION['panier']); ?></p>
                                    <p><strong>Total à payer:</strong> $<?php echo number_format($totalPrix, 2); ?></p>
                                    <p><strong>Devise:</strong> USD</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Méthode de paiement:</strong> Mobile Money</p>
                                    <p><strong>Pays:</strong> RDC</p>
                                    <p><strong>ID Commande:</strong> <?php echo $orderId; ?></p>
                                </div>
                            </div>
                            
                            <!-- Détails des produits -->
                            <h5>Produits dans votre panier:</h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Produit</th>
                                            <th>Quantité</th>
                                            <th>Prix unitaire</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($_SESSION['panier'] as $produit): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($produit['nom']); ?></td>
                                            <td><?php echo $produit['qte']; ?></td>
                                            <td>$<?php echo number_format($produit['prix'], 2); ?></td>
                                            <td>$<?php echo number_format($produit['prix'] * $produit['qte'], 2); ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Formulaire de paiement -->
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h4><i class="fa fa-mobile"></i> Procéder au paiement Mobile Money</h4>
                        </div>
                        <div class="panel-body">
                            <?php
                            if (isset($_SESSION['msg'])) {
                                echo "<p class='alert alert-danger'>" . $_SESSION['msg'] . "</p>";
                                unset($_SESSION['msg']);
                            }
                            ?>
                            <?php
                            if (isset($_SESSION['msgSuc'])) {
                                echo "<p class='alert alert-success'>" . $_SESSION['msgSuc'] . "</p>";
                                unset($_SESSION['msgSuc']);
                            }
                            ?>
                            
                            <div class="alert alert-info">
                                <h5><i class="fa fa-info-circle"></i> Instructions de paiement</h5>
                                <ol>
                                    <li>Entrez votre numéro de téléphone Mobile Money ci-dessous</li>
                                    <li>Sélectionnez votre opérateur mobile money (M-Pesa, Airtel, Orange)</li>
                                    <li>Cliquez sur le bouton "Payer"</li>
                                    <li>Vous serez redirigé vers la plateforme GenesysPay</li>
                                    <li>Confirmez le paiement sur votre téléphone</li>
                                    <li>Vous recevrez une confirmation par SMS</li>
                                </ol>
                            </div>
                            
                            <form method="post" action="">
                                <input type="hidden" name="payer_mobile_money" value="1">
                                
                                <!-- Champ pour le numéro de téléphone -->
                                <div class="form-group">
                                    <label for="phone_number" class="control-label">
                                        <i class="fa fa-phone"></i> Numéro de téléphone Mobile Money
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">+243</span>
                                        <input type="tel" 
                                               class="form-control input-lg" 
                                               id="phone_number" 
                                               name="phone_number" 
                                               placeholder="9XXXXXXXX" 
                                               pattern="[0-9]{9}" 
                                               maxlength="9"
                                               required
                                               value="<?php echo isset($_POST['phone_number']) ? htmlspecialchars($_POST['phone_number']) : ''; ?>">
                                    </div>
                                    <small class="help-block">
                                        <i class="fa fa-info-circle"></i> 
                                        Entrez votre numéro de téléphone sans le préfixe +243 (ex: 991234567)
                                    </small>
                                </div>
                                
                                <!-- Champ pour le provider -->
                                <div class="form-group">
                                    <label for="provider" class="control-label">
                                        <i class="fa fa-sim-card"></i> Opérateur Mobile Money
                                    </label>
                                    <select name="provider" id="provider" class="form-control input-lg" required>
                                        <option value="">-- Sélectionnez un opérateur --</option>
                                        <option value="mpesa_cd" <?php if(isset($_POST['provider']) && $_POST['provider']==='mpesa_cd') echo 'selected'; ?>>M-Pesa RDC</option>
                                        <option value="airtel_cd" <?php if(isset($_POST['provider']) && $_POST['provider']==='airtel_cd') echo 'selected'; ?>>Airtel Money RDC</option>
                                        <option value="orange_cd" <?php if(isset($_POST['provider']) && $_POST['provider']==='orange_cd') echo 'selected'; ?>>Orange Money RDC</option>
                                    </select>
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-lg btn-block">
                                    <i class="fa fa-mobile"></i> Payer $<?php echo number_format($totalPrix, 2); ?> via Mobile Money
                                </button>
                            </form>
                            
                            <div class="text-center" style="margin-top: 20px;">
                                <a href="index.php" class="btn btn-default">
                                    <i class="fa fa-arrow-left"></i> Retour au panier
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Informations de sécurité -->
                    <div class="alert alert-success">
                        <h5><i class="fa fa-shield"></i> Sécurité garantie</h5>
                        <ul>
                            <li><strong>Chiffrement SSL:</strong> Toutes les communications sont sécurisées</li>
                            <li><strong>Certification PCI:</strong> GenesysPay est certifié pour la sécurité des paiements</li>
                            <li><strong>Aucun stockage:</strong> Vos données bancaires ne sont jamais stockées sur nos serveurs</li>
                            <li><strong>Paiement instantané:</strong> Traitement en temps réel</li>
                        </ul>
                    </div>
                    
                    <!-- Opérateurs supportés -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h5><i class="fa fa-mobile"></i> Opérateurs Mobile Money supportés</h5>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <i class="fa fa-mobile fa-2x text-primary"></i>
                                    <p><strong>Airtel Money</strong></p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fa fa-mobile fa-2x text-success"></i>
                                    <p><strong>M-Pesa</strong></p>
                                </div>
                                <div class="col-md-4 text-center">
                                    <i class="fa fa-mobile fa-2x text-warning"></i>
                                    <p><strong>Orange Money</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <footer id="footer">
        <div id="bottom-footer" class="section">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 text-center">
                        <span class="copyright">
                            Copyright &copy; <?php echo date('Y'); ?> E-Vente. Tous les droits réservés.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <!-- Script de validation du numéro de téléphone -->
    <script>
        $(document).ready(function() {
            // Validation en temps réel du numéro de téléphone
            $('#phone_number').on('input', function() {
                var phoneNumber = $(this).val();
                var phoneRegex = /^[0-9]{9}$/;
                
                if (phoneNumber.length > 0) {
                    if (phoneRegex.test(phoneNumber)) {
                        $(this).removeClass('is-invalid').addClass('is-valid');
                        $('.phone-error').remove();
                    } else {
                        $(this).removeClass('is-valid').addClass('is-invalid');
                        if ($('.phone-error').length === 0) {
                            $(this).after('<div class="phone-error text-danger"><small>Format invalide. Utilisez 9 chiffres (ex: 991234567)</small></div>');
                        }
                    }
                } else {
                    $(this).removeClass('is-valid is-invalid');
                    $('.phone-error').remove();
                }
            });
            
            // Validation du formulaire avant soumission
            $('form').on('submit', function(e) {
                var phoneNumber = $('#phone_number').val();
                var phoneRegex = /^[0-9]{9}$/;
                
                if (!phoneRegex.test(phoneNumber)) {
                    e.preventDefault();
                    alert('Veuillez entrer un numéro de téléphone valide (9 chiffres)');
                    $('#phone_number').focus();
                    return false;
                }
            });
        });
    </script>
</body>
</html> 