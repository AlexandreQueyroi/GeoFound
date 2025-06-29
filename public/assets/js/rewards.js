// Script pour la gestion des récompenses

document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des filtres
    initFilters();
    
    // Initialisation des animations
    initAnimations();
});

/**
 * Initialise les filtres de récompenses
 */
function initFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const rewardCards = document.querySelectorAll('.reward-card');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Mettre à jour les boutons
            filterBtns.forEach(b => {
                b.classList.remove('active', 'bg-blue-600');
                b.classList.add('bg-gray-600');
            });
            this.classList.add('active', 'bg-blue-600');
            this.classList.remove('bg-gray-600');
            
            // Filtrer les cartes avec animation
            rewardCards.forEach(card => {
                if (filter === 'all' || card.classList.contains(filter)) {
                    card.style.display = 'block';
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'scale(1)';
                    }, 50);
                } else {
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        card.style.display = 'none';
                    }, 300);
                }
            });
        });
    });
}

/**
 * Initialise les animations des cartes
 */
function initAnimations() {
    const rewardCards = document.querySelectorAll('.reward-card');
    
    rewardCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Débloque une récompense
 * @param {number} rewardId - ID de la récompense
 */
function unlockReward(rewardId) {
    if (confirm('Êtes-vous sûr de vouloir débloquer cette récompense ?')) {
        // Afficher un indicateur de chargement
        const button = event.target;
        const originalText = button.innerHTML;
        button.innerHTML = '<iconify-icon icon="tabler:loader-2" class="animate-spin mr-1"></iconify-icon>Déblocage...';
        button.disabled = true;
        
        fetch('/reward/api/unlock', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'reward_id=' + rewardId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                
                // Animation de succès
                const card = button.closest('.reward-card');
                card.style.animation = 'rewardUnlock 0.5s ease';
                
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showToast(data.error, 'error');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            showToast('Erreur lors de la requête', 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}

/**
 * Équipe/déséquipe une récompense
 * @param {number} rewardId - ID de la récompense
 */
function toggleEquip(rewardId) {
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<iconify-icon icon="tabler:loader-2" class="animate-spin mr-1"></iconify-icon>...';
    button.disabled = true;
    
    fetch('/reward/api/equip', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'reward_id=' + rewardId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            
            // Animation de changement d'état
            const card = button.closest('.reward-card');
            card.style.animation = 'rewardEquip 0.3s ease';
            
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            showToast(data.error, 'error');
            button.innerHTML = originalText;
            button.disabled = false;
        }
    })
    .catch(error => {
        showToast('Erreur lors de la requête', 'error');
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

/**
 * Affiche un toast de notification
 * @param {string} message - Message à afficher
 * @param {string} type - Type de toast (success, error, info)
 */
function showToast(message, type = 'info') {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');
    
    const bgColor = type === 'success' ? 'bg-green-600' : 
                   type === 'error' ? 'bg-red-600' : 'bg-blue-600';
    
    const icon = type === 'success' ? 'tabler:check' : 
                type === 'error' ? 'tabler:alert-circle' : 'tabler:info-circle';
    
    toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center transform translate-x-full transition-transform duration-300`;
    toast.innerHTML = `
        <iconify-icon icon="${icon}" class="mr-2"></iconify-icon>
        ${message}
    `;
    
    toastContainer.appendChild(toast);
    
    // Animation d'entrée
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Animation de sortie
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            toast.remove();
        }, 300);
    }, 3000);
}

/**
 * Affiche les détails d'une récompense
 * @param {number} rewardId - ID de la récompense
 */
function showRewardDetails(rewardId) {
    // Ici on pourrait ouvrir un modal avec les détails de la récompense
    console.log('Afficher les détails de la récompense:', rewardId);
}

// Styles CSS pour les animations
const style = document.createElement('style');
style.textContent = `
    @keyframes rewardUnlock {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); box-shadow: 0 0 20px rgba(34, 197, 94, 0.5); }
        100% { transform: scale(1); }
    }
    
    @keyframes rewardEquip {
        0% { transform: scale(1); }
        50% { transform: scale(1.02); }
        100% { transform: scale(1); }
    }
    
    .reward-card {
        transition: all 0.3s ease;
    }
    
    .reward-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    
    .filter-btn {
        transition: all 0.2s ease;
    }
    
    .filter-btn:hover {
        transform: translateY(-2px);
    }
`;
document.head.appendChild(style); 