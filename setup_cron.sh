#!/bin/bash

# Script pour configurer le cron job de vérification des statuts utilisateur

echo "=== Configuration du cron job pour GeoFound ==="

# Obtenir le chemin absolu du script
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/cron_check_user_status.php"

echo "Script path: $SCRIPT_PATH"

# Créer la commande cron (toutes les heures)
CRON_COMMAND="0 * * * * /usr/bin/php $SCRIPT_PATH >> $SCRIPT_PATH.log 2>&1"

echo "Commande cron: $CRON_COMMAND"

# Ajouter au crontab de l'utilisateur www-data
(crontab -u www-data -l 2>/dev/null; echo "$CRON_COMMAND") | crontab -u www-data -

echo "✅ Cron job configuré pour l'utilisateur www-data"
echo "Le script s'exécutera toutes les heures"
echo ""
echo "Pour vérifier le cron job:"
echo "sudo crontab -u www-data -l"
echo ""
echo "Pour voir les logs:"
echo "tail -f $SCRIPT_PATH.log" 