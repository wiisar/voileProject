document.querySelectorAll(".edit-btn").forEach(button => {
    button.addEventListener("click", () => {
        const id = button.dataset.id;
        // Récupération des infos directement depuis le tableau HTML
        const row = button.closest("tr");
        const nom = row.children[0].textContent.trim();
        const prenom = row.children[1].textContent.trim();
        const mail = row.children[2].textContent.trim();

        document.getElementById("modal-idAdherent").value = id;
        document.getElementById("modal-nom").value = nom;
        document.getElementById("modal-prenom").value = prenom;
        document.getElementById("modal-mail").value = mail;
        document.getElementById("updateModal").style.display = "block";
    });
});
