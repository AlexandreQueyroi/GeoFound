class AvatarCustomizer {
    constructor() {
        this.canvas = document.getElementById('avatarCanvas');
        this.ctx = this.canvas.getContext('2d');
        this.skinSelect = document.getElementById('skinSelect');
        this.headSelect = document.getElementById('headSelect');
        this.hairSelect = document.getElementById('hairSelect');
        this.exportBtn = document.getElementById('exportBtn');
        
        this.skinImage = new Image();
        this.headImage = new Image();
        this.hairImage = new Image();
        
        this.currentSkin = 'Skin/tint1_head.png';
        this.currentHead = 'Face/face1.png';
        this.currentHair = 'Hair/Black/Man1B.png';
        
        this.initialize();
    }
    
    initialize() {
        this.loadImages();
        this.skinSelect.addEventListener('change', () => this.updateSkin());
        this.headSelect.addEventListener('change', () => this.updateHead());
        this.hairSelect.addEventListener('change', () => this.updateHair());
        this.saveAvatarBtn = document.getElementById('saveAvatarBtn');
        this.saveAvatarBtn.addEventListener('click', () => this.saveAvatar());
    }
    
    loadImages() {
        this.skinImage.onload = () => this.drawAvatar();
        this.skinImage.src = `/assets/img/avatars/${this.currentSkin}`;
        this.headImage.onload = () => this.drawAvatar();
        this.headImage.src = `/assets/img/avatars/${this.currentHead}`;
        this.hairImage.onload = () => this.drawAvatar();
        this.hairImage.src = `/assets/img/avatars/${this.currentHair}`;
    }
    
    updateSkin() {
        this.currentSkin = this.skinSelect.value;
        this.skinImage.src = `/assets/img/avatars/${this.currentSkin}`;
    }
    
    updateHead() {
        this.currentHead = this.headSelect.value;
        this.headImage.src = `/assets/img/avatars/${this.currentHead}`;
    }
    
    updateHair() {
        this.currentHair = this.hairSelect.value;
        this.hairImage.src = `/assets/img/avatars/${this.currentHair}`;
    }
    
    drawAvatar() {
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        const centerX = this.canvas.width / 2;
        const centerY = this.canvas.height / 2;
        if (this.skinImage.complete) {
            const skinX = centerX - (this.skinImage.width / 2);
            const skinY = centerY - (this.skinImage.height / 2);
            this.ctx.drawImage(this.skinImage, skinX, skinY);
        }
        if (this.headImage.complete) {
            const headX = centerX - (this.headImage.width / 2);
            const headY = centerY - (this.headImage.height / 2);
            this.ctx.drawImage(this.headImage, headX, headY);
        }
        if (this.hairImage.complete) {
            const hairX = centerX - (this.hairImage.width / 2);
            const hairY = centerY - (this.hairImage.height / 2);
            this.ctx.drawImage(this.hairImage, hairX, hairY);
        }
    }
    
    saveAvatar() {
        const dataUrl = this.canvas.toDataURL('image/png');
        fetch('/api/update_avatar.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ avatar: dataUrl })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Avatar enregistré avec succès !');
            } else {
                alert('Erreur lors de l\'enregistrement de l\'avatar.');
            }
        })
        .catch(() => alert('Erreur lors de l\'envoi de l\'avatar.'));
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new AvatarCustomizer();
}); 