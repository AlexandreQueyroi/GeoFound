
function submitPostForm() {
    console.log('PostModal: Soumission du formulaire...');
    const form = document.getElementById('postForm');
    
    if (form) {
        const formData = new FormData(form);
        
        
        if (!formData.get('latitude') || !formData.get('longitude')) {
            console.log('PostModal: Coordonnées manquantes, utilisation de coordonnées par défaut (Paris)');
            formData.set('latitude', '48.8566');
            formData.set('longitude', '2.3522');
        }
        
        console.log('PostModal: Données du formulaire:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }
        
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
            if (data.success) {
                
                const modal = document.getElementById('post-modal');
                if (modal) {
                    const modalInstance = new Modal(modal);
                    modalInstance.hide();
                }
                
                
                showSuccessMessage(data.message);
                
                
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showErrorMessage(data.message);
            }
        })
        .catch(error => {
            console.error('PostModal: Erreur:', error);
            showErrorMessage('Erreur lors de la création du post');
        });
    } else {
        console.error('PostModal: Formulaire non trouvé!');
    }
}


function showSuccessMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2 animate-fade-in-up';
    toast.innerHTML = `<iconify-icon icon="tabler:check" width="20" height="20"></iconify-icon><span>${message}</span>`;
    document.getElementById('toast-container').appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}

function showErrorMessage(message) {
    const toast = document.createElement('div');
    toast.className = 'bg-red-600 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-2 animate-fade-in-up';
    toast.innerHTML = `<iconify-icon icon="tabler:alert-circle" width="20" height="20"></iconify-icon><span>${message}</span>`;
    document.getElementById('toast-container').appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('opacity-0');
        setTimeout(() => toast.remove(), 500);
    }, 5000);
} 