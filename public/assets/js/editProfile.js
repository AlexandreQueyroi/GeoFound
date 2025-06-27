document.addEventListener('DOMContentLoaded', function() {
    const editProfileForm = document.getElementById('editProfileForm');
    editProfileForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        try {
            const response = await fetch('/api/update_profile.php', {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.success) {
                alert('Profil mis à jour avec succès !');
                if (data.requiresEmailVerification) {
                    alert('Un email de confirmation a été envoyé à votre nouvelle adresse email.');
                }
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        }
    });

    const avatarEditorModal = document.getElementById('avatarEditorModal');
    const openAvatarEditor = document.getElementById('openAvatarEditor');
    const closeAvatarEditor = document.getElementById('closeAvatarEditor');
    const saveAvatar = document.getElementById('saveAvatar');
    const cancelAvatar = document.getElementById('cancelAvatar');

    openAvatarEditor.addEventListener('click', () => {
        avatarEditorModal.classList.remove('hidden');
        avatarEditorModal.classList.add('flex');
    });

    [closeAvatarEditor, cancelAvatar].forEach(btn => {
        btn.addEventListener('click', () => {
            avatarEditorModal.classList.add('hidden');
            avatarEditorModal.classList.remove('flex');
        });
    });

    saveAvatar.addEventListener('click', async () => {
        const avatarData = {
            hairStyle: document.getElementById('hairStyle').value,
            faceStyle: document.getElementById('faceStyle').value,
        };

        try {
            const response = await fetch('/api/update_avatar.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(avatarData)
            });
            const data = await response.json();
            
            if (data.success) {
                document.getElementById('avatarPreview').src = data.avatarUrl;
                avatarEditorModal.classList.add('hidden');
                avatarEditorModal.classList.remove('flex');
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        }
    });

    document.getElementById('exportPDF').addEventListener('click', async () => {
        try {
            const response = await fetch('/api/export_data.php?format=pdf');
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'mes-donnees.pdf';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'export');
        }
    });

    document.getElementById('exportJSON').addEventListener('click', async () => {
        try {
            const response = await fetch('/api/export_data.php?format=json');
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'mes-donnees.json';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue lors de l\'export');
        }
    });

    const deactivateModal = document.getElementById('deactivateModal');
    document.getElementById('deactivateAccount').addEventListener('click', () => {
        deactivateModal.classList.remove('hidden');
        deactivateModal.classList.add('flex');
    });

    document.getElementById('confirmDeactivate').addEventListener('click', async () => {
        try {
            const response = await fetch('/api/deactivate_account.php', {
                method: 'POST'
            });
            const data = await response.json();
            
            if (data.success) {
                alert('Compte désactivé avec succès');
                window.location.href = '/';
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        }
    });

    document.getElementById('cancelDeactivate').addEventListener('click', () => {
        deactivateModal.classList.add('hidden');
        deactivateModal.classList.remove('flex');
    });

    const deleteModal = document.getElementById('deleteModal');
    document.getElementById('deleteAccount').addEventListener('click', () => {
        deleteModal.classList.remove('hidden');
        deleteModal.classList.add('flex');
    });

    document.getElementById('confirmDelete').addEventListener('click', async () => {
        try {
            const response = await fetch('/api/delete_account.php', {
                method: 'POST'
            });
            const data = await response.json();
            
            if (data.success) {
                alert('Compte supprimé avec succès');
                window.location.href = '/';
            } else {
                alert(data.message || 'Une erreur est survenue');
            }
        } catch (error) {
            console.error('Erreur:', error);
            alert('Une erreur est survenue');
        }
    });

    document.getElementById('cancelDelete').addEventListener('click', () => {
        deleteModal.classList.add('hidden');
        deleteModal.classList.remove('flex');
    });
}); 