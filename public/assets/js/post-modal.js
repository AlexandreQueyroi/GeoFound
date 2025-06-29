
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
            
            document.getElementById('postForm').reset();
        }, 300);
    }
}

function submitPostForm() {
    const form = document.getElementById('postForm');
    
    if (!form) {
        console.error('PostModal: Formulaire non trouvé!');
        showErrorMessage('Erreur: Formulaire non trouvé');
        return;
    }
    
    
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
    
    
    if (!formData.get('latitude') || !formData.get('longitude')) {
        formData.set('latitude', '48.8566');
        formData.set('longitude', '2.3522');
    }
    
    
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
        return response.json();
    })
    .then(data => {
        
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            showSuccessMessage(data.message || 'Post créé avec succès !');
            hidePostModal();
            
            
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            showErrorMessage(data.message || 'Erreur lors de la création du post');
        }
    })
    .catch(error => {
        console.error('PostModal: Erreur:', error);
        
        
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
    
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    
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
    
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 300);
    }, 5000);
}


document.addEventListener('DOMContentLoaded', function() {
    
    window.openPostModal = showPostModal;
    
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hidePostModal();
        }
    });
    
    
    document.getElementById('post-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            hidePostModal();
        }
    });
}); 