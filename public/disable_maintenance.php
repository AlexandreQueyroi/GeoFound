<?php
require_once __DIR__ . '/../vendor/autoload.php';
use App\Models\Permission;

Permission::setAllPagesMaintenance(0);
echo "Maintenance désactivée sur toutes les pages !"; 