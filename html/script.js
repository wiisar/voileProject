
/*

// Liste de phrases à afficher par thème
const themes = {
    cuisine: [
      'je prépare un délicieux gâteau au chocolat',
      'les épices ajoutent de la saveur aux plats',
    ],
    nature: [
      'la forêt sent bon après la pluie',
      'les montagnes sont majestueuses au lever du soleil',
    ],
  };
  
  // Variables globales pour le jeu
  let nomJoueur = '';
  let scoresJoueurs = JSON.parse(localStorage.getItem('scoresJoueurs')) || {};
  let jeuCommence = false;
  let timerGlobal;  // timer pour fin de la partie
  let timerMot;     // timer pour faire disparaître la phrase
  const TEMPS_PARTIE = 120; // durée d'une partie : 2 minutes
  const TEMPS_PAR_MOT = {
    facile: 20,     // facile : 20 sec pour écrire la phrase
    moyenne: 15,    // moyenne : 15 sec pour écrire la phrase
    difficile: 10,  // difficile : 10 sec pour écrire la phrase
  };
  
  let mots = themes.animaux;  // thème par défaut : animaux
  let tempsRestant = 20;      // temps initial pour le niveau facile
  let intervalId;             // pour stocker l'intervalle du compteur de temps
  let score = 0;              // score initial
  let meilleurScore = 0;      // nouveau score pour suivre le meilleur score
  
  const affichageMot = document.getElementById('affichage-mot');
  const affichageCompteur = document.createElement('div');
  
  let motActuel = '';
  let texteSaisi = '';  // texte que le joueur tape
  
  // Fonction pour afficher un nouveau mot
  function afficherNouveauMot() {
    // réinitialiser la saisie
    texteSaisi = '';
    // récupérer l'élément d'affichage
    const affichageMot = document.getElementById('affichage-mot');
    console.log(affichageMot);
    const theme = document.getElementById('theme').value;
    const motsTheme = themes[theme];
    const indexAleatoire = Math.floor(Math.random() * motsTheme.length);
    motActuel = motsTheme[indexAleatoire];
    // forcer la mise à jour de l'affichage
    affichageMot.textContent = motActuel;
    // démarrer le timer mot
    demarrerTimerMot();
  }
  
  // Fonction pour démarrer le timer du mot actuel
  function demarrerTimerMot() {
    if (timerMot) clearInterval(timerMot);
    const difficulte = document.getElementById('difficulte').value;
    let tempsRestantMot = TEMPS_PAR_MOT[difficulte];
    const compteurTempsMot = document.getElementById('compteur-temps-mot');
    compteurTempsMot.textContent = `Temps mot : ${tempsRestantMot}s`;
    timerMot = setInterval(() => {
      tempsRestantMot--;
      compteurTempsMot.textContent = `Temps mot : ${tempsRestantMot}s`;
      if (tempsRestantMot <= 0) {
        clearInterval(timerMot);
        afficherNouveauMot();
      }
    }, 1000);
  }
  
  // Fonction pour obtenir le temps restant selon la difficulté
  function getTempsRestant() {
    const difficulte = document.getElementById('difficulte').value;
    if (difficulte === 'facile') {
      return 20;
    } else if (difficulte === 'moyenne') {
      return 15;
    } else if (difficulte === 'difficile') {
      return 10;
    }
    return 20;  // valeur par défaut
  }
  
  // Fonction pour colorer le texte en fonction des lettres tapées
  function colorerTexte() {
    let texteColoré = ''; // chaîne qui contiendra le texte coloré
    // parcourir chaque lettre du mot actuel et vérifier la saisie
    for (let i = 0; i < motActuel.length; i++) {
      const lettre = motActuel[i];
      // si la lettre saisie correspond, on la colorie en violet
      if (i < texteSaisi.length && texteSaisi[i] === lettre) {
        texteColoré += `<span style="color: #9b7ed3">${lettre}</span>`;
      } else if (i < texteSaisi.length) {
        // si la lettre est incorrecte, on la colore en rouge
        texteColoré += `<span style="color: red">${lettre}</span>`;
      } else if (lettre === ' ' && texteSaisi[i] === ' ') {
        // si on tape un espace et qu'il correspond, on le laisse normal
        texteColoré += `<span>&nbsp;</span>`;  // utilisation de `&nbsp;` pour l'espace
      } else {
        // sinon, la lettre reste normale
        texteColoré += `<span>${lettre}</span>`;
      }
    }
    // mettre à jour le texte affiché avec les couleurs appropriées
    affichageMot.innerHTML = texteColoré;
    console.log('Le texte est saisi   ' + texteSaisi);
    console.log('Le texte est attendu ' + motActuel);
    // vérifier si la saisie complète est correcte
    if (texteSaisi === motActuel) {
      score += 10;  // ajouter des points
      scoreBouton = document.getElementById('score-bouton');
      scoreBouton.textContent = `Score : ${score}`;
      // mettre à jour le meilleur score
      if (score > meilleurScore) {
        meilleurScore = score;
      }
      // arrêter le timer mot actuel
      clearInterval(timerMot);
      // réinitialiser la saisie et afficher un nouveau mot
      texteSaisi = '';
      afficherNouveauMot();
    }
  }
  
  // Fonction pour gérer la saisie du joueur au clavier
  function gererSaisie(e) {
    if (!jeuCommence) return;
    if (/^[a-zA-ZÀ-ÖØ-öø-ÿÂ-ÎÏ-ÏÔ-Û-ÿ]$/.test(e.key)) {
      texteSaisi += e.key.toLowerCase();
      colorerTexte();
    } else if (e.key === 'Backspace') {
      texteSaisi = texteSaisi.slice(0, -1);
      colorerTexte();
    } else if (e.key === ' ') {
      texteSaisi += ' ';
      colorerTexte();
    }
  }
  
  // Changer la liste de mots en fonction du thème sélectionné
  document.getElementById('theme').addEventListener('change', function () {
    mots = themes[this.value];
    afficherNouveauMot();  // charger un nouveau mot du thème sélectionné
  });
  
  // Changer la durée du temps en fonction de la difficulté sélectionnée
  document.getElementById('difficulte').addEventListener('change', function () {
    // réinitialiser le score et ajuster le temps
    score = 0; // réinitialiser le score à zéro
    scoreBouton.textContent = `Score : ${score}`; // réinitialiser le score dans le bouton
    afficherNouveauMot(); // recharger un nouveau mot avec le temps réinitialisé
  });
  
  // Initialisation du jeu
  afficherNouveauMot();
  
  // Fonction pour démarrer le jeu
  function demarrerJeu() {
    // indique que le jeu a commencé
    jeuCommence = true;
    // récupère et nettoie le nom du joueur
    const nom = document.getElementById('nom-joueur').value.trim();
    // si aucun nom n'est saisi, on arrête la fonction
    if (!nom) return;
    // stocke le nom du joueur
    nomJoueur = nom;
    // cache le formulaire de saisie du nom et affiche les éléments du jeu
    document.getElementById('formulaire-joueur').style.display = 'none';
    document.getElementById('conteneur-jeu').style.display = 'block';
    document.getElementById('changer-nom-btn').style.display = 'block';
    document.getElementById('tableau-scores').style.display = 'block';
    // met à jour le tableau des scores
    mettreAJourTableauScores();
    // initialise le temps restant pour la partie
    let tempsRestantGlobal = TEMPS_PARTIE;
    const compteurTempsGlobal = document.getElementById('compteur-temps');
    // démarre un timer global pour le temps de la partie
    timerGlobal = setInterval(() => {
      // convertit le temps restant en minutes et secondes
      const minutes = Math.floor(tempsRestantGlobal / 60);
      const secondes = tempsRestantGlobal % 60;
      // met à jour l'affichage du temps restant
      compteurTempsGlobal.textContent = `Temps partie : ${minutes}:${secondes.toString().padStart(2, '0')}`;
      // si le temps est écoulé, arrêter le jeu
      if (tempsRestantGlobal <= 0) {
        clearInterval(timerGlobal); // arrête le timer global
        clearInterval(timerMot); // arrête le timer des mots
        finDePartie(); // termine la partie
      }
      // décrémente le temps restant
      tempsRestantGlobal--;
    }, 1000);
    // démarre le timer pour le mot actuel
    demarrerTimerMot();
    // ajoute un écouteur d'événements pour capter la saisie du joueur
    document.addEventListener('keydown', gererSaisie);
  }
  
  // Fonction pour vérifier si le mot est correctement tapé
  function verifierMot() {
    if (texteSaisi === motActuel) {
      clearInterval(timerMot);
      score += 10;
      document.getElementById('score-bouton').textContent = `Score : ${score}`;
      afficherNouveauMot();
    }
  }
  
  // Fonction pour mettre à jour le tableau des scores
  function mettreAJourTableauScores() {
    const scoresTriés = Object.entries(scoresJoueurs)
      .sort(([, a], [, b]) => b - a)
      .slice(0, 5);
    const tbody = document.getElementById('scores-body');
    tbody.innerHTML = '';
    scoresTriés.forEach(([nom, score], index) => {
      const row = tbody.insertRow();
      row.innerHTML = `
              <td>${index + 1}</td>
              <td>${nom}</td>
              <td>${score}</td>
          `;
    });
  }
  
  // Fonction pour terminer la partie
  function finDePartie() {
    jeuCommence = false;
    document.removeEventListener('keydown', gererSaisie);
    // arrêter tous les timers
    clearInterval(timerMot);
    clearInterval(timerGlobal);
    // sauvegarder le score si c'est le meilleur
    if (!scoresJoueurs[nomJoueur] || score > scoresJoueurs[nomJoueur]) {
      scoresJoueurs[nomJoueur] = score;
      localStorage.setItem('scoresJoueurs', JSON.stringify(scoresJoueurs));
    }
    // afficher la modal de fin
    const modalHTML = `
          <div id="modal-fin-partie" class="modal">
              <div class="modal-content">
                  <h2>Partie terminée!</h2>
                  <p>Votre score: <span class="score-final">${score}</span></p>
                  <p>Meilleur score personnel: <span class="meilleur-score">${scoresJoueurs[nomJoueur]}</span></p>
                  <div class="modal-buttons">
                      <button id="btn-rejouer" class="btn-primary">Rejouer</button>
                      <button id="btn-changer-nom" class="btn-secondary" onclick="changementNom()"> Changer de nom </button>
                  </div>
              </div>
          </div>
      `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.getElementById('conteneur-jeu').style.display = 'none';
  
    document.getElementById('btn-rejouer').addEventListener('click', () => {
      document.getElementById('modal-fin-partie').remove();
      reinitialiserPartie();
    });
    document.getElementById('btn-changer-nom').addEventListener('click', () => {
      document.getElementById('modal-fin-partie').remove();
      document.getElementById('formulaire-joueur').style.display = 'block';
      document.getElementById('conteneur-jeu').style.display = 'none';
      document.getElementById('nom-joueur').value = '';
    });
  }
  
  function reinitialiserPartie() {
    // réinitialiser le score
    score = 0;
    document.getElementById('score-bouton').textContent = `Score : ${score}`;
    // réinitialiser l'affichage du mot
    document.getElementById('affichage-mot').textContent = '...';
    // réafficher les éléments du jeu
    const elementsAAfficher = [
      'conteneur-jeu',
      'choix-difficulte',
      'choix-theme',
    ];
    elementsAAfficher.forEach((id) => {
      const element = document.getElementById(id);
      if (element) {
        element.style.display = 'block';
      }
    });
    // arrêter les anciens timers
    if (timerGlobal) clearInterval(timerGlobal);
    if (timerMot) clearInterval(timerMot);
    // réinitialiser les compteurs de temps
    document.getElementById('compteur-temps').textContent = 'Temps partie : 2:00';
    document.getElementById('compteur-temps-mot').textContent = 'Temps mot : --';
    // redémarrer une nouvelle partie
    demarrerJeu();
  }
  
  document.addEventListener('DOMContentLoaded', function () {
    // cacher les éléments du jeu au démarrage
    const elementsAMasquer = [
      'titre-centre-nautique',
      'choix-difficulte',
      'choix-theme',
      'score-bouton',
      'conteneur-jeu',
    ];
  
    elementsAMasquer.forEach((id) => {
      document.getElementById(id).style.display = 'none';
    });
  
    // gérer la soumission du formulaire
    document
      .getElementById('form-joueur')
      .addEventListener('submit', function (e) {
        e.preventDefault();
        const nom = document.getElementById('nom-joueur').value.trim();
        if (nom) {
          // stocker le nom et masquer le formulaire
          nomJoueur = nom;
          document.getElementById('formulaire-joueur').style.display = 'none';
          // afficher les éléments du jeu
          [
            'titre-centre-nautique',
            'choix-difficulte',
            'choix-theme',
            'conteneur-jeu',
            'changer-nom-btn',
          ].forEach((id) => {
            const element = document.getElementById(id);
            if (element) element.style.display = 'block';
          });
          // initialiser et démarrer la partie
          score = 0;
          document.getElementById(
            'score-bouton'
          ).textContent = `Score : ${score}`;
          demarrerJeu();
        }
      });
  });
  
  function changementNom() {
    jeuCommence = false;
    document.removeEventListener('keydown', gererSaisie);
    clearInterval(timerMot);
    clearInterval(timerGlobal);
    document.getElementById('formulaire-joueur').style.display = 'block';
    document.getElementById('nom-joueur').value = '';
    // masquer les éléments du jeu
    [
      'titre-centre-nautique',
      'choix-difficulte',
      'choix-theme',
      'conteneur-jeu',
      'changer-nom-btn',
    ].forEach((id) => {
      const element = document.getElementById(id);
      if (element) element.style.display = 'none';
    });
    // arrêter les timers en cours
    if (timerGlobal) clearInterval(timerGlobal);
    if (timerMot) clearInterval(timerMot);
    // réinitialiser le score
    score = 0;
    document.getElementById('score-bouton').textContent = `Score : ${score}`;
  }

  */