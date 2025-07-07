<?php
/**
 * Classe GenesysPayAPI
 * 
 * Cette classe gère toutes les interactions avec l'API GenesysPay
 * pour les paiements mobile money en RDC.
 * 
 * Fonctionnalités :
 * - Initialisation de paiement
 * - Vérification de statut
 * - Gestion des callbacks
 * - Validation des réponses
 */

class GenesysPayAPI {
    
    // Nouvelle configuration pour l'API mobile money
    private $config = [
        'private_key' => 'GPPRIV-3e4ed2a4e78fd64d555216d3451751e841f1758f',
        'api_url' => 'https://api.genesyspay.com/v1/payment/mobile',
        'currency' => 'USD',
        'country_code' => 'CD',
        'timeout' => 30,
        'user_agent' => 'E-Vente/1.0'
    ];
    
    /**
     * Constructeur de la classe
     * 
     * @param array $customConfig Configuration personnalisée (optionnel)
     */
    public function __construct($customConfig = []) {
        // Fusionner la configuration personnalisée avec la configuration par défaut
        $this->config = array_merge($this->config, $customConfig);
    }
    
    /**
     * Initialiser un paiement mobile money
     * 
     * @param array $paymentData Données du paiement
     * @return array Résultat de l'initialisation
     */
    public function initMobileMoneyPayment($paymentData) {
        // Validation des données requises
        $requiredFields = ['order_id', 'amount', 'customer_email', 'customer_name', 'phone_number', 'provider', 'callback_url'];
        foreach ($requiredFields as $field) {
            if (empty($paymentData[$field])) {
                return [
                    'success' => false,
                    'error' => "Le champ '$field' est requis."
                ];
            }
        }
        
        // Validation du format du numéro de téléphone
        if (!$this->validatePhoneNumber($paymentData['phone_number'])) {
            return [
                'success' => false,
                'error' => "Format de numéro de téléphone invalide. Utilisez le format: 243XXXXXXXXX"
            ];
        }
        
        // Préparation des données pour l'API (JSON)
        $postData = [
            'amount' => $paymentData['amount'],
            'currency' => $this->config['currency'],
            'provider' => $paymentData['provider'],
            'country_code' => $this->config['country_code'],
            'phone_number' => $paymentData['phone_number'],
            'order_id' => $paymentData['order_id'],
            'callback_url' => $paymentData['callback_url'],
        ];
        
        return $this->makeRequest($postData, $this->config['api_url']);
    }
    
    /**
     * Vérifier le statut d'une transaction
     * 
     * @param string $transactionId ID de la transaction
     * @return array Statut de la transaction
     */
    public function checkTransactionStatus($transactionId) {
        $postData = [
            'public_key' => $this->config['public_key'],
            'transaction_id' => $transactionId
        ];
        
        $statusUrl = str_replace('/init', '/status', $this->config['api_url']);
        return $this->makeRequest($postData, $statusUrl);
    }
    
    /**
     * Effectuer une requête cURL vers l'API GenesysPay
     * 
     * @param array $data Données à envoyer
     * @param string $url URL de l'API
     * @return array Résultat de la requête
     */
    private function makeRequest($data, $url) {
        // Initialisation de cURL
        $ch = curl_init();
        
        // Configuration des options cURL
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => $this->config['timeout'],
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->config['private_key'],
                'User-Agent: ' . $this->config['user_agent']
            ],
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 10
        ]);
        
        // Exécution de la requête
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        $info = curl_getinfo($ch);
        
        // Fermeture de la connexion cURL
        curl_close($ch);
        
        // Analyse de la réponse
        $result = [
            'success' => ($httpCode >= 200 && $httpCode < 300) && empty($error),
            'http_code' => $httpCode,
            'response' => $response,
            'error' => $error,
            'info' => $info,
            'data_sent' => $data
        ];
        
        // Tentative de décodage JSON de la réponse
        if (!empty($response)) {
            $jsonResponse = json_decode($response, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $result['response_data'] = $jsonResponse;
            }
        }
        
        return $result;
    }
    
    /**
     * Valider une réponse de callback GenesysPay
     * 
     * @param array $callbackData Données du callback
     * @return array Résultat de la validation
     */
    public function validateCallback($callbackData) {
        // Vérification des champs requis dans le callback
        $requiredCallbackFields = ['transaction_id', 'order_id', 'status', 'amount'];
        foreach ($requiredCallbackFields as $field) {
            if (!isset($callbackData[$field])) {
                return [
                    'valid' => false,
                    'error' => "Champ manquant dans le callback: $field"
                ];
            }
        }
        
        // Vérification du statut
        $validStatuses = ['success', 'failed', 'pending', 'cancelled'];
        if (!in_array($callbackData['status'], $validStatuses)) {
            return [
                'valid' => false,
                'error' => "Statut invalide: " . $callbackData['status']
            ];
        }
        
        return [
            'valid' => true,
            'transaction_id' => $callbackData['transaction_id'],
            'order_id' => $callbackData['order_id'],
            'status' => $callbackData['status'],
            'amount' => $callbackData['amount']
        ];
    }
    
    /**
     * Générer un ID de commande unique
     * 
     * @param string $prefix Préfixe pour l'ID (optionnel)
     * @return string ID de commande unique
     */
    public function generateOrderId($prefix = 'CMD') {
        return $prefix . '_' . time() . '_' . uniqid();
    }
    
    /**
     * Formater un montant pour l'API
     * 
     * @param float $amount Montant à formater
     * @return float Montant formaté
     */
    public function formatAmount($amount) {
        return round($amount, 2);
    }
    
    /**
     * Obtenir la configuration actuelle
     * 
     * @return array Configuration
     */
    public function getConfig() {
        return $this->config;
    }
    
    /**
     * Définir une nouvelle configuration
     * 
     * @param array $newConfig Nouvelle configuration
     */
    public function setConfig($newConfig) {
        $this->config = array_merge($this->config, $newConfig);
    }
    
    /**
     * Valider le format d'un numéro de téléphone RDC (243XXXXXXXXX)
     * @param string $phoneNumber Numéro de téléphone à valider
     * @return bool True si le format est valide
     */
    public function validatePhoneNumber($phoneNumber) {
        // Format attendu: 243XXXXXXXXX (12 chiffres)
        return preg_match('/^243[0-9]{9}$/', $phoneNumber) === 1;
    }

    /**
     * Formater un numéro de téléphone pour l'API (243XXXXXXXXX)
     * @param string $phoneNumber Numéro de téléphone à formater
     * @return string Numéro formaté
     */
    public function formatPhoneNumber($phoneNumber) {
        // Supprimer tous les caractères non numériques
        $cleaned = preg_replace('/[^0-9]/', '', $phoneNumber);
        // Si déjà au format 243XXXXXXXXX
        if (preg_match('/^243[0-9]{9}$/', $cleaned)) {
            return $cleaned;
        }
        // Si commence par 0, remplacer par 243
        if (preg_match('/^0[0-9]{9}$/', $cleaned)) {
            return '243' . substr($cleaned, 1);
        }
        // Si juste 9 chiffres, préfixer 243
        if (preg_match('/^[0-9]{9}$/', $cleaned)) {
            return '243' . $cleaned;
        }
        // Sinon, retourner tel quel (pour debug)
        return $cleaned;
    }
    
    /**
     * Logger les transactions pour le débogage
     * 
     * @param string $action Action effectuée
     * @param array $data Données de la transaction
     * @param array $result Résultat de l'action
     */
    public function logTransaction($action, $data, $result) {
        $logEntry = [
            'timestamp' => date('Y-m-d H:i:s'),
            'action' => $action,
            'data' => $data,
            'result' => $result
        ];
        
        // Écrire dans un fichier de log (optionnel)
        $logFile = 'genesyspay_logs.txt';
        file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
    }
}

/**
 * Exemple d'utilisation de la classe GenesysPayAPI
 * 
 * // Initialisation
 * $genesysPay = new GenesysPayAPI();
 * 
 * // Données du paiement
 * $paymentData = [
 *     'order_id' => $genesysPay->generateOrderId(),
 *     'amount' => $genesysPay->formatAmount(150.00),
 *     'customer_email' => 'client@example.com',
 *     'customer_name' => 'John Doe',
 *     'redirect_url' => 'http://localhost/e-vente/payement-reussi.php',
 *     'cancel_url' => 'http://localhost/e-vente/payement-annule.php',
 *     'failed_url' => 'http://localhost/e-vente/payement-echec.php',
 *     'description' => 'Achat de produits'
 * ];
 * 
 * // Initialiser le paiement
 * $result = $genesysPay->initMobileMoneyPayment($paymentData);
 * 
 * if ($result['success']) {
 *     // Rediriger vers la page de paiement
 *     if (isset($result['response_data']['redirect_url'])) {
 *         header('Location: ' . $result['response_data']['redirect_url']);
 *         exit();
 *     }
 * } else {
 *     // Gérer l'erreur
 *     echo "Erreur: " . $result['error'];
 * }
 */
?> 