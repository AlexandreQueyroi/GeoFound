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
<!-- <div id="cookie-banner"
    class="fixed flex bottom-10 left-1/2 transform -translate-x-1/2 bg-gray-800 text-white p-4 rounded-xl shadow-xl z-[9999] w-[95%] max-w-lg hidden">
    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
        <p class="text-sm text-center md:text-left">
            Ce site utilise des cookies pour améliorer votre expérience.
        </p>
        <div class="flex gap-2 justify-center md:justify-end">
            <button onclick="acceptCookies()"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 transition rounded-md text-sm">
                Accepter
            </button>
            <button onclick="declineCookies()"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 transition rounded-md text-sm">
                Refuser
            </button>
        </div>
    </div>
</div> -->
<div id="cookie-banner" class="hidden fixed bottom-0 left-0 w-full bg-gray-800 text-white text-sm px-6 py-4 z-50 shadow-md flex flex-col md:flex-row items-center justify-between gap-4">
  <span>Ce site utilise des cookies pour améliorer votre expérience.</span>
  <button id="accept-cookies" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded-md transition">
    J'accepte
  </button>
</div>


<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.1/dist/js.cookie.min.js"></script>
<script>
  if (!Cookies.get('cookie_accepted')) {
    document.getElementById('cookie-banner').classList.remove("hidden");
  }

  document.getElementById('accept-cookies').addEventListener('click', () => {
    Cookies.set('cookie_accepted', 'true', { expires: 365 });
    document.getElementById('cookie-banner').style.display = 'none';
    location.reload(); // Recharge pour que PHP puisse poser les cookies auto-login
  });
</script>
<script src="../build/cookies.js"></script>
</body>

</html>