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
<div id="cookie-banner"
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
</div>
<script src="../build/cookies.js"></script>
</body>

</html>