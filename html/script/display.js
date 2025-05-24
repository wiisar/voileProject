// Fonctions de bascule génériques
function toggleDisplayById(id) {
    const elem = document.getElementById(id);
    if (elem) {
        elem.style.display = (elem.style.display === 'block') ? 'none' : 'block';
    }
}

function toggleFAQ(questionId) {
    const answerId = `r${questionId.substring(1)}`; // réponse correspondante
    toggleDisplayById(answerId);
}

// Gestionnaires d'événements
document.addEventListener("DOMContentLoaded", () => {
    const logButton = document.querySelector(".login-signup a");
    const faqButtons = document.getElementsByClassName("question-FAQ");
    const userAddBtn = document.getElementById("addUserBtn");
    const sejAddBtn = document.getElementById("addSejBtn");

    if (logButton) {
        logButton.addEventListener("click", () => toggleDisplayById("loginModal"));
    }

    if (userAddBtn) {
        userAddBtn.addEventListener("click", () => toggleDisplayById("userAddModal"));
    }

    if (sejAddBtn) {
        sejAddBtn.addEventListener("click", () => toggleDisplayById("addSejour"));
    }

    Array.from(faqButtons).forEach(button => {
        button.addEventListener("click", () => toggleFAQ(button.id));
    });
});
