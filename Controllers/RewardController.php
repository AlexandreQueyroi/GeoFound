<?php
namespace App\Controllers;

use App\Models\Reward;
use App\Models\User;
use App\Helpers\PermissionMiddleware;

class RewardController {
    
    public function __construct() {
        
      
    }
    
    public function index() {
        
        if (!isset($_SESSION['user_id'])) {
            $userId = 1; 
        } else {
            $userId = $_SESSION['user_id'];
        }
        
        $user = User::find($userId);
        
        
        $allRewards = Reward::getAll();
        
        
        $userRewards = Reward::getUserRewards($userId);
        
        
        $userLevel = $user['level'] ?? 1;
        $availableRewards = Reward::getAvailableRewards($userLevel);
        
        
        $rewardsWithStatus = [];
        foreach ($allRewards as $reward) {
            $userHasReward = Reward::userHasReward($userId, $reward['id']);
            $userReward = null;
            
            if ($userHasReward) {
                foreach ($userRewards as $ur) {
                    if ($ur['id'] == $reward['id']) {
                        $userReward = $ur;
                        break;
                    }
                }
            }
            
            $rewardsWithStatus[] = [
                'reward' => $reward,
                'user_has' => $userHasReward,
                'user_reward' => $userReward,
                'can_unlock' => $reward['required_level'] <= $userLevel && !$userHasReward
            ];
        }
        
        require __DIR__ . '/../Views/reward/index.php';
    }
    
    public function unlock($id) {
        
        if (!isset($_SESSION['user_id'])) {
            $userId = 1; 
        } else {
            $userId = $_SESSION['user_id'];
        }
        
        $rewardId = $id;
        
        
        $reward = Reward::getById($rewardId);
        if (!$reward) {
            $_SESSION['error'] = "Récompense introuvable.";
            header('Location: /reward');
            exit;
        }
        
        
        if (Reward::userHasReward($userId, $rewardId)) {
            $_SESSION['error'] = "Vous avez déjà cette récompense.";
            header('Location: /reward');
            exit;
        }
        
        
        $user = User::find($userId);
        $userLevel = $user['level'] ?? 1;
        
        if ($reward['required_level'] > $userLevel) {
            $_SESSION['error'] = "Niveau insuffisant pour débloquer cette récompense.";
            header('Location: /reward');
            exit;
        }
        
        
        if (Reward::unlockReward($userId, $rewardId)) {
            $_SESSION['success'] = "Récompense débloquée avec succès !";
        } else {
            $_SESSION['error'] = "Erreur lors du déblocage de la récompense.";
        }
        
        header('Location: /reward');
        exit;
    }
    
    public function equip($id) {
        
        if (!isset($_SESSION['user_id'])) {
            $userId = 1; 
        } else {
            $userId = $_SESSION['user_id'];
        }
        
        $rewardId = $id;
        
        
        if (!Reward::userHasReward($userId, $rewardId)) {
            $_SESSION['error'] = "Vous n'avez pas cette récompense.";
            header('Location: /reward');
            exit;
        }
        
        
        if (Reward::toggleEquip($userId, $rewardId)) {
            $_SESSION['success'] = "Récompense équipée/déséquipée avec succès.";
        } else {
            $_SESSION['error'] = "Erreur lors de l'équipement de la récompense.";
        }
        
        header('Location: /reward');
        exit;
    }
    
    
    public function apiUnlock() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }
        
        
        if (!isset($_SESSION['user_id'])) {
            $userId = 1; 
        } else {
            $userId = $_SESSION['user_id'];
        }
        
        $rewardId = $_POST['reward_id'] ?? null;
        
        if (!$rewardId) {
            echo json_encode(['error' => 'ID de récompense manquant']);
            return;
        }
        
        $reward = Reward::getById($rewardId);
        if (!$reward) {
            echo json_encode(['error' => 'Récompense introuvable']);
            return;
        }
        
        if (Reward::userHasReward($userId, $rewardId)) {
            echo json_encode(['error' => 'Vous avez déjà cette récompense']);
            return;
        }
        
        $user = User::find($userId);
        $userLevel = $user['level'] ?? 1;
        
        if ($reward['required_level'] > $userLevel) {
            echo json_encode(['error' => 'Niveau insuffisant']);
            return;
        }
        
        if (Reward::unlockReward($userId, $rewardId)) {
            echo json_encode(['success' => true, 'message' => 'Récompense débloquée !']);
        } else {
            echo json_encode(['error' => 'Erreur lors du déblocage']);
        }
    }
    
    public function apiEquip() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
            return;
        }
        
        
        if (!isset($_SESSION['user_id'])) {
            $userId = 1; 
        } else {
            $userId = $_SESSION['user_id'];
        }
        
        $rewardId = $_POST['reward_id'] ?? null;
        
        if (!$rewardId) {
            echo json_encode(['error' => 'ID de récompense manquant']);
            return;
        }
        
        if (!Reward::userHasReward($userId, $rewardId)) {
            echo json_encode(['error' => 'Vous n\'avez pas cette récompense']);
            return;
        }
        
        if (Reward::toggleEquip($userId, $rewardId)) {
            echo json_encode(['success' => true, 'message' => 'Récompense équipée/déséquipée']);
        } else {
            echo json_encode(['error' => 'Erreur lors de l\'équipement']);
        }
    }
} 