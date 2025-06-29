<?php include_once __DIR__ . '/../layouts/header.php'; ?>

<div class="flex flex-col items-center justify-center min-h-[70vh] px-4 py-12 bg-gradient-to-br from-[#0A0A23] to-[#1a2234]">
    <div class="max-w-2xl w-full text-center">
        <img src="/assets/img/logo.png" alt="GeoFound Logo" class="mx-auto w-24 h-24 mb-6 drop-shadow-lg animate-bounce">
        <h1 class="text-4xl md:text-5xl font-extrabold text-white mb-4 drop-shadow">Bienvenue sur <span class="text-blue-400">GeoFound</span></h1>
        <p class="text-lg md:text-xl text-gray-300 mb-8">La plateforme communautaire pour partager et découvrir les meilleurs spots autour de vous !</p>
        <div class="flex flex-col md:flex-row justify-center gap-4 mb-10">
            <a href="/post" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-lg shadow transition text-lg">Voir les posts</a>
            <a href="/reward" class="bg-yellow-400 hover:bg-yellow-500 text-yellow-900 font-semibold px-8 py-3 rounded-lg shadow transition text-lg">Récompenses</a>
        </div>
        <div class="flex flex-col md:flex-row justify-center gap-4">
            <a href="/auth/register" class="underline text-blue-300 hover:text-blue-400 transition">Créer un compte</a>
            <span class="text-gray-500 hidden md:inline">|</span>
            <a href="/auth/login" class="underline text-blue-300 hover:text-blue-400 transition">Se connecter</a>
        </div>
    </div>
    <div class="mt-12 flex justify-center">
        <img src="/assets/img/examplePostProfil.jpg" alt="Découverte" class="rounded-2xl shadow-2xl w-80 h-56 object-cover border-4 border-blue-600 bg-white/10">
    </div>
</div>

<?php include_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
function sharePost(postId) {
    const url = `${window.location.origin}/post/${postId}`;
    if (navigator.share) {
        navigator.share({
            title: 'GeoFound - Post',
            url: url
        }).catch(console.error);
    } else {
        navigator.clipboard.writeText(url).then(() => {
            alert('Lien copié dans le presse-papiers !');
        });
    }
}
</script> 