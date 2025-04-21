function acceptCookies() {
  localStorage.setItem("cookiesConsent", "accepted");
  document.getElementById("cookie-banner").classList.add("hidden");
}

function declineCookies() {
  localStorage.setItem("cookiesConsent", "declined");
  document.getElementById("cookie-banner").classList.add("hidden");
}

document.addEventListener("DOMContentLoaded", () => {
  const banner = document.getElementById("cookie-banner");
  if (!localStorage.getItem("cookiesConsent") && banner) {
    banner.classList.remove("hidden");
  }
});
