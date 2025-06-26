<?php
namespace App\Controllers;

class RewardController {
    public function index() {
        require __DIR__ . '/../Views/reward/index.php';
    }
    public function unlock($id) {
        require __DIR__ . '/../Views/reward/unlock.php';
    }
} 