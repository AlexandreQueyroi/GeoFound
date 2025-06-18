</container>
<footer class="border-gray-200 bg-[#081225] text-white text-center p-6">
    <div class="content has-text-centered">
        <p>
            &copy;
            <?php echo date("Y"); ?>
            <strong>Geo Found</strong>. Tous droits réservés.
        </p>
        <p>
            <a href="#" class="has-text-link">Politique de confidentialité</a> |
            <a href="#" class="has-text-link">Conditions d'utilisation</a>
        </p>
    </div>
</footer>

<div id="cookie-banner" class="hidden fixed bottom-0 left-0 w-full bg-gray-800 text-white text-sm px-6 py-4 z-50 shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
  <span>Ce site utilise des cookies pour améliorer votre expérience.</span>
  <button id="accept-cookies" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-md transition">
    J'accepte
  </button>
</div>


<script>
  console.log(document.cookie.includes('cookie=true'));
  if (!document.cookie.includes('cookie=true')) {
    document.getElementById('cookie-banner').style.display = 'flex';
  } else {
    document.getElementById('cookie-banner').style.display = 'none';
  }

  document.getElementById('accept-cookies').addEventListener('click', () => {
    document.cookie = "cookie=true; path=/; max-age=" + 365 * 24 * 60 * 60;
    document.getElementById('cookie-banner').style.display = 'none';
    location.reload();
  });
</script>
<script src="../build/cookies.js"></script>
</body>

</html>