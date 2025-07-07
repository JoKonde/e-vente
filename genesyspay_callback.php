<?php
/**
 * Callback GenesysPay - Gestion des notifications de paiement
 * 
 * Ce fichier reçoit les notifications de GenesysPay concernant le statut
 * des transactions de paiement mobile money.
 * 
 * IMPORTANT: Ce fichier doit être accessible publiquement pour que GenesysPay
 * puisse envoyer les notifications de statut.
 */

// Inclure la classe GenesysPayAPI
require_once 'GenesysPayAPI.php';

// Initialiser l'API
$genesysPay = new GenesysPayAPI();

// Logger toutes les données reçues pour le débogage
$logData = [
    'timestamp' => date('Y-m-d H:i:s'),
    'method' => $_SERVER['REQUEST_METHOD'],
    'headers' => getallheaders(),
    'post_data' => $_POST,
    'get_data' => $_GET,
    'raw_input' => file_get_contents('php://input')
];

// Écrire dans le fichier de log
file_put_contents('genesyspay_callback_log.txt', json_encode($logData) . "\n", FILE_APPEND | LOCK_EX);

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit();
}

// Récupérer les données du callback
$callbackData = $_POST;

// Si les données sont envoyées en JSON
if (empty($callbackData)) {
    $rawInput = file_get_contents('php://input');
    $callbackData = json_decode($rawInput, true);
}

// Valider les données du callback
$validation = $genesysPay->validateCallback($callbackData);

if (!$validation['valid']) {
    // Logger l'erreur de validation
    $genesysPay->logTransaction('callback_validation_error', $callbackData, $validation);
    
    http_response_code(400);
    echo json_encode(['error' => $validation['error']]);
    exit();
}

// Traiter le callback selon le statut
$transactionId = $validation['transaction_id'];
$orderId = $validation['order_id'];
$status = $validation['status'];
$amount = $validation['amount'];

// Logger la transaction
$genesysPay->logTransaction('callback_received', $callbackData, $validation);

// Traitement selon le statut
switch ($status) {
    case 'success':
        // Paiement réussi
        processSuccessfulPayment($orderId, $transactionId, $amount);
        break;
        
    case 'failed':
        // Paiement échoué
        processFailedPayment($orderId, $transactionId, $amount);
        break;
        
    case 'pending':
        // Paiement en attente
        processPendingPayment($orderId, $transactionId, $amount);
        break;
        
    case 'cancelled':
        // Paiement annulé
        processCancelledPayment($orderId, $transactionId, $amount);
        break;
        
    default:
        // Statut inconnu
        processUnknownStatus($orderId, $transactionId, $status);
        break;
}

// Répondre à GenesysPay
http_response_code(200);
echo json_encode(['status' => 'received']);

/**
 * Traiter un paiement réussi
 * 
 * @param string $orderId ID de la commande
 * @param string $transactionId ID de la transaction
 * @param float $amount Montant payé
 */
function processSuccessfulPayment($orderId, $transactionId, $amount) {
    // Ici, vous pouvez :
    // 1. Mettre à jour le statut de la commande en base de données
    // 2. Envoyer un email de confirmation au client
    // 3. Vider le panier de l'utilisateur
    // 4. Créer une facture
    // 5. Notifier l'équipe de vente
    
    // Exemple de traitement
    $successData = [
        'order_id' => $orderId,
        'transaction_id' => $transactionId,
        'amount' => $amount,
        'status' => 'success',
        'timestamp' => time()
    ];
    
    // Sauvegarder en base de données (exemple)
    // saveTransactionToDatabase($successData);
    
    // Envoyer un email de confirmation (exemple)
    // sendConfirmationEmail($orderId, $amount);
    
    // Logger le succès
    file_put_contents('successful_payments.txt', json_encode($successData) . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Traiter un paiement échoué
 * 
 * @param string $orderId ID de la commande
 * @param string $transactionId ID de la transaction
 * @param float $amount Montant
 */
function processFailedPayment($orderId, $transactionId, $amount) {
    $failedData = [
        'order_id' => $orderId,
        'transaction_id' => $transactionId,
        'amount' => $amount,
        'status' => 'failed',
        'timestamp' => time()
    ];
    
    // Logger l'échec
    file_put_contents('failed_payments.txt', json_encode($failedData) . "\n", FILE_APPEND | LOCK_EX);
    
    // Optionnel : envoyer un email au client pour l'informer de l'échec
    // sendFailureEmail($orderId, $amount);
}

/**
 * Traiter un paiement en attente
 * 
 * @param string $orderId ID de la commande
 * @param string $transactionId ID de la transaction
 * @param float $amount Montant
 */
function processPendingPayment($orderId, $transactionId, $amount) {
    $pendingData = [
        'order_id' => $orderId,
        'transaction_id' => $transactionId,
        'amount' => $amount,
        'status' => 'pending',
        'timestamp' => time()
    ];
    
    // Logger l'attente
    file_put_contents('pending_payments.txt', json_encode($pendingData) . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Traiter un paiement annulé
 * 
 * @param string $orderId ID de la commande
 * @param string $transactionId ID de la transaction
 * @param float $amount Montant
 */
function processCancelledPayment($orderId, $transactionId, $amount) {
    $cancelledData = [
        'order_id' => $orderId,
        'transaction_id' => $transactionId,
        'amount' => $amount,
        'status' => 'cancelled',
        'timestamp' => time()
    ];
    
    // Logger l'annulation
    file_put_contents('cancelled_payments.txt', json_encode($cancelledData) . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Traiter un statut inconnu
 * 
 * @param string $orderId ID de la commande
 * @param string $transactionId ID de la transaction
 * @param string $status Statut inconnu
 */
function processUnknownStatus($orderId, $transactionId, $status) {
    $unknownData = [
        'order_id' => $orderId,
        'transaction_id' => $transactionId,
        'status' => $status,
        'timestamp' => time()
    ];
    
    // Logger le statut inconnu
    file_put_contents('unknown_status_payments.txt', json_encode($unknownData) . "\n", FILE_APPEND | LOCK_EX);
}

/**
 * Exemple de fonction pour sauvegarder en base de données
 * 
 * @param array $transactionData Données de la transaction
 * @return bool Succès de la sauvegarde
 */
function saveTransactionToDatabase($transactionData) {
    // Ici, vous pouvez implémenter la sauvegarde en base de données
    // Exemple avec PDO :
    /*
    try {
        $pdo = new PDO("mysql:host=localhost;dbname=your_database", "username", "password");
        $stmt = $pdo->prepare("INSERT INTO transactions (order_id, transaction_id, amount, status, created_at) VALUES (?, ?, ?, ?, NOW())");
        return $stmt->execute([
            $transactionData['order_id'],
            $transactionData['transaction_id'],
            $transactionData['amount'],
            $transactionData['status']
        ]);
    } catch (PDOException $e) {
        error_log("Erreur base de données: " . $e->getMessage());
        return false;
    }
    */
    
    return true; // Placeholder
}

/**
 * Exemple de fonction pour envoyer un email de confirmation
 * 
 * @param string $orderId ID de la commande
 * @param float $amount Montant payé
 * @return bool Succès de l'envoi
 */
function sendConfirmationEmail($orderId, $amount) {
    // Ici, vous pouvez implémenter l'envoi d'email
    // Exemple avec mail() :
    /*
    $to = "client@example.com";
    $subject = "Confirmation de paiement - Commande #$orderId";
    $message = "Votre paiement de $amount USD pour la commande #$orderId a été confirmé.";
    $headers = "From: noreply@yourdomain.com";
    
    return mail($to, $subject, $message, $headers);
    */
    
    return true; // Placeholder
}
?> 