function togglePasswordVisibility(fieldId, iconId) {
  const field = document.getElementById(fieldId);
  const icon = document.getElementById(iconId);
  if (field.type === "password") {
    field.type = "text";
    icon.setAttribute("data-icon", "tabler:eye");
  } else {
    field.type = "password";
    icon.setAttribute("data-icon", "tabler:eye-closed");
  }
}
function checkModalIsValid() {
  const newuser = document.getElementById("newuser").value.trim();
  const newmail = document.getElementById("newmail").value.trim();
  const newpass = document.getElementById("newpass").value.trim();
  const newpass_confirm = document
    .getElementById("newpass_confirm")
    .value.trim();
  if (newuser) {
    fetch(`/api/user?checkuser=${encodeURIComponent(newuser)}`)
      .then((response) => response.text())
      .then((data) => {
        data = data.trim();
        const submitBtn = document.getElementById("submitBtn");
        let errorMsgUserAlreadyUsed = document.getElementById("error-msg");
        if (data === "exist") {
          if (!errorMsgUserAlreadyUsed) {
            errorMsgUserAlreadyUsed = document.createElement("div");
            errorMsgUserAlreadyUsed.id = "error-msg";
            errorMsgUserAlreadyUsed.className = "text-red-500 text-sm mb-2";
            submitBtn.parentNode.insertBefore(
              errorMsgUserAlreadyUsed,
              submitBtn
            );
          }
          errorMsgUserAlreadyUsed.textContent =
            "Cet identifiant est déjà utilisé";
        } else {
          if (errorMsgUserAlreadyUsed) {
            errorMsgUserAlreadyUsed.remove();
          }
        }
      })
      .catch((err) => console.error(err));
  }
  if (newmail) {
    fetch(`/api/user?checkemail=${encodeURIComponent(newmail)}`)
      .then((response) => response.text())
      .then((data) => {
        data = data.trim();
        const submitBtn = document.getElementById("submitBtn");
        let errorMsgMailAlreadyUsed = document.getElementById("error-msg");
        if (data === "exist") {
          if (!errorMsgMailAlreadyUsed) {
            errorMsgMailAlreadyUsed = document.createElement("div");
            errorMsgMailAlreadyUsed.id = "error-msg";
            errorMsgMailAlreadyUsed.className = "text-red-500 text-sm mb-2";
            submitBtn.parentNode.insertBefore(
              errorMsgMailAlreadyUsed,
              submitBtn
            );
          }
          errorMsgMailAlreadyUsed.textContent = "Ce mail est déjà utilisé";
        } else {
          if (errorMsgMailAlreadyUsed) {
            errorMsgMailAlreadyUsed.remove();
          }
        }
      })
      .catch((err) => console.error(err));
  }

  if (newpass) {
    const submitBtn = document.getElementById("submitBtn");
    let errorMsgPassword = document.getElementById("error-msg-password");
    if (newpass !== newpass_confirm) {
      if (!errorMsgPassword) {
        errorMsgPassword = document.createElement("div");
        errorMsgPassword.id = "error-msg-password";
        errorMsgPassword.className = "text-red-500 text-sm mb-2";
        submitBtn.parentNode.insertBefore(errorMsgPassword, submitBtn);
      }
      errorMsgPassword.textContent = "Vos mots de passe ne correspondent pas";
    } else {
      if (errorMsgPassword) {
        errorMsgPassword.remove();
      }
    }
  }
}

function checkModalIsValidConfirm(event) {
  const newuser = document.getElementById("newuser").value.trim();
  const newmail = document.getElementById("newmail").value.trim();
  const newpass = document.getElementById("newpass").value.trim();
  const newpass_confirm = document
    .getElementById("newpass_confirm")
    .value.trim();
  let isValid = true;

  if (newuser) {
    fetch(`/api/user?checkuser=${encodeURIComponent(newuser)}`)
      .then((response) => response.text())
      .then((data) => {
        data = data.trim();
        if (data === "exist") {
          isValid = false;
        }
      })
      .catch((err) => {
        console.error(err);
        isValid = false;
      });
  }

  if (newuser) {
    fetch(`/api/user?checkemail=${encodeURIComponent(newmail)}`)
      .then((response) => response.text())
      .then((data) => {
        data = data.trim();
        if (data === "exist") {
          isValid = false;
        }
      })
      .catch((err) => {
        console.error(err);
        isValid = false;
      });
  }

  if (newpass && newpass !== newpass_confirm) {
    isValid = false;
  }

  if (!validCaptcha) {
    isValid = false;
  }

  if (!isValid) {
    event.preventDefault();
  }

  return isValid;
}

let correctAnswer = "";

async function fetchCaptcha() {
  try {
    let validCaptcha = false;
    const response = await fetch("/api/captcha.php");
    const data = await response.json();
    correctAnswer = data.answer;
    document.getElementById("captcha-question").textContent = data.question;
    document.getElementById("captcha-answer").value = "";
  } catch (error) {
    console.error("Erreur lors du chargement du captcha:", error);
    document.getElementById("response").textContent =
      "Erreur lors du chargement du captcha.";
  }
}

function checkCaptcha() {
  let userInput = document.getElementById("captcha-answer").value.trim();
  const responseElement = document.getElementById("response");
  if (!userInput) {
    responseElement.textContent = "❌ Veuillez entrer une réponse.";
    return;
  }
  if (userInput === correctAnswer) {
    responseElement.textContent = "✅ Réponse correcte";
    let validCaptcha = true;
  } else {
    responseElement.textContent = "❌ Réponse incorrecte";
    fetchCaptcha();
  }
}
