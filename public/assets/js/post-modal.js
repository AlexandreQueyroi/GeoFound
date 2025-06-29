// Fonctions pour gérer le modal de post
function showPostModal() {
    const modal = document.getElementById('post-modal');
    const content = document.getElementById('post-modal-content');
    
    if (modal && content) {
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }
}

function hidePostModal() {
    const modal = document.getElementById('post-modal');
    const content = document.getElementById('post-modal-content');
    
    if (modal && content) {
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            // Réinitialiser le formulaire
            document.getElementById('postForm').reset();
        }, 300);
    }
}

function submitPostForm() {
    console.log('PostModal: Soumission du formulaire...');
    const form = document.getElementById('postForm');
    
    if (!form) {
        console.error('PostModal: Formulaire non trouvé!');
        showErrorMessage('Erreur: Formulaire non trouvé');
        return;
    }
    
    // Validation des champs requis
    const title = form.querySelector('#title').value.trim();
    const content = form.querySelector('#content').value.trim();
    const address = form.querySelector('#address').value.trim();
    const image = form.querySelector('#image').files[0];
    
    if (!title) {
        showErrorMessage('Le titre est requis');
        return;
    }
    
    if (!content) {
        showErrorMessage('La description est requise');
        return;
    }
    
    if (!address) {
        showErrorMessage('L\'adresse est requise');
        return;
    }
    
    if (!image) {
        showErrorMessage('Une image est requise');
        return;
    }
    
    const formData = new FormData(form);
    
    // Coordonnées par défaut (Paris) si non définies
    if (!formData.get('latitude') || !formData.get('longitude')) {
        console.log('PostModal: Coordonnées manquantes, utilisation de coordonnées par défaut (Paris)');
        formData.set('latitude', '48.8566');
        formData.set('longitude', '2.3522');
    }
    
    console.log('PostModal: Données du formulaire:');
    for (let [key, value] of formData.entries()) {
        console.log(`  ${key}: ${value}`);
    }
    
    // Afficher un indicateur de chargement
    const submitBtn = document.querySelector('button[onclick="submitPostForm()"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<iconify-icon icon="tabler:loader-2" class="animate-spin" width="16" height="16"></iconify-icon><span>Création...</span>';
    submitBtn.disabled = true;
    
    fetch('/post/create', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('PostModal: Réponse reçue:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('PostModal: Données reçues:', data);
        
        // Restaurer le bouton
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            showSuccessMessage(data.message || 'Post créé avec succès !');
            hidePostModal();
            
            // Recharger la page après un délai
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showErrorMessage(data.message || 'Erreur lors de la création du post');
        }
    })
    .catch(error => {
        console.error('PostModal: Erreur:', error);
        
        // Restaurer le bouton
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        showErrorMessage('Erreur de connexion lors de la création du post');
    });
}

function showSuccessMessage(message) {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    toast.className = 'bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2 transform transition-all duration-300 translate-x-full';
    toast.innerHTML = `
        <iconify-icon icon="tabler:check" width="20" height="20"></iconify-icon>
        <span>${message}</span>
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
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, 3000);
}

function showErrorMessage(message) {
    const toastContainer = document.getElementById('toast-container');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    toast.className = 'bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2 transform transition-all duration-300 translate-x-full';
    toast.innerHTML = `
        <iconify-icon icon="tabler:alert-circle" width="20" height="20"></iconify-icon>
        <span>${message}</span>
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
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, 5000);
}

// Initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Remplacer la fonction openPostModal du header
    window.openPostModal = showPostModal;
    
    // Ajouter des écouteurs d'événements pour la fermeture du modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hidePostModal();
        }
    });
    
    // Fermer le modal en cliquant à l'extérieur
    document.getElementById('post-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            hidePostModal();
        }
    });
}); 