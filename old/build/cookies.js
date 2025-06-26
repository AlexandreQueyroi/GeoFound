function acceptCookies() {
  localStorage.setItem("cookie", "accepted");
  document.getElementById("cookie-banner").classList.add("hidden");
}

function declineCookies() {
  localStorage.setItem("cookie", "declined");
  document.getElementById("cookie-banner").classList.add("hidden");
}

document.addEventListener("DOMContentLoaded", () => {
  const banner = document.getElementById("cookie-banner");
  if (!localStorage.getItem("cookie") && banner) {
    banner.classList.remove("hidden");
  }
});
