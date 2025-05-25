#------------------------------------------------------------
# Insertion des données de test
#------------------------------------------------------------

-- Insertion de données dans la table FAQ
INSERT INTO FAQ (question, reponse) VALUES
("Comment réserver une activité ?", "Pour réserver une activité, vous pouvez nous contacter directement via notre page de réservation ou nous appeler au numéro indiqué sur cette page."),
("Quels sont vos horaires d'ouverture ?", "Nos horaires d'ouverture sont disponibles sur la page dédiée de notre site, via l'onglet Informations/Horaires."),
("Comment peut-on venir au Centre Nautique ?", "Une piste cyclable longeant la côte vous permet d'arriver à notre centre nautique. Sinon, un parking est présent pour y venir en voiture et le bus 10 vous y dépose directement, à l'arrêt Les Flots."),
("Dois-je apporter mon propre matériel ?", "Non, nous fournissons tout le nécessaire pour vos activités. Vous pouvez cependant apporter votre propre matériel si vous le souhaitez."),
("Y a-t-il des casiers disponibles pour mes affaires ?", "Le centre est équipé de vestiaires, de douches collectives et individuelles et de casiers avec bracelets-clés pour stocker vos affaires pendant votre pratique.");

INSERT INTO DroitsRef (nom) VALUES
('Administrateur'),('Adherent'),('Planificateur');

-- Insertion de données dans la table Adherents
INSERT INTO Adherents (nom, prenom, mail, dateAdhesion, passwd) VALUES
('Dupont', 'Jean', 'jean.dupont@example.com', '2023-01-15','5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8'), -- mdp : password
('Martin', 'Marie', 'marie.martin@example.com', '2023-02-20','5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8'),
('Lefevre', 'Paul', 'paul.lefevre@example.com', '2023-03-25','5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8'),
('Garnier', 'Sophie', 'sophie.garnier@example.com', '2023-04-30','5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8');

INSERT INTO PossederDroits (idAdherent, idDroit) VALUES
(1,1),(2,2),(3,2),(3,3),(4,3);

-- Insertion de données dans la table Modeles
INSERT INTO Modeles (type, capacite) VALUES
('Voilier', 5),
('Catamaran', 2),
('Dériveur', 3),
('Habitable', 6),
('Canoe', 2),
('Planche à voile', 1),
('Surf', 1),
('Paddle', 1);

-- Insertion de données dans la table Bateaux
INSERT INTO Bateaux (nom, dateAchat, idModele) VALUES
('Voilier A', '2022-05-10',1),
('Voilier B', '2022-06-20',1),
('Catamaran C', '2021-07-15',2),
('Canoe 1', '2020-08-25',3),
('Plouf', '2020-08-25',6),
('Paddle 1', '2020-08-25',8),
('Surf 1', '2020-08-25',7);

-- Insertion de données dans la table Stages
INSERT INTO Stages (intitule, duree, niveau, prixMin, prixMax, idModele) VALUES
('Initiation à la voile', '02:00:00', 1, 50.00, 70.00,1),
('Perfectionnement', '03:00:00', 2, 80.00, 100.00,2),
('Voile en équipage', '04:00:00', 3, 120.00, 150.00,3),
('Navigation côtière', '05:00:00', 4, 180.00, 220.00,4);

-- Insertion de données dans la table Brevets
INSERT INTO Brevets (intitule, encadrement, idStage) VALUES
('Brevet de base', 0, 1),
('Brevet avancé', 1, 2),
('Brevet de moniteur', 1, 3),
('Brevet de capitaine', 1, 4);

-- Insertion de données dans la table Ports
INSERT INTO Ports (emplacements) VALUES
('Port de La Rochelle'),
('Port de Brest'),
('Port de Marseille'),
('Port de Nice');

-- Insertion de données dans la table Sejour
INSERT INTO Sejours (capacite, prix, idPortsDepart, idPortsArriver, dates, idStage) VALUES
(5, 150.00, 1, 2,'2023-07-01',1),
(8, 200.00, 2, 1,'2023-07-02',2),
(6, 250.00, 3, 4,'2023-07-03',2),
(8, 200.00, 2, 1,'2023-07-10',2),
(6, 250.00, 3, 4,'2023-07-05',2),
(4, 300.00, 4, 3,'2023-07-04',3),
(4, 300.00, 4, 3,'2023-07-04',3);

-- Insertion de données dans la table Posseder
INSERT INTO Posseder (idAdherent, idBrevet) VALUES
(1, 1),
(2, 2),
(2, 3),
(3, 3),
(4, 4);

-- Insertion de données dans la table Requerir
INSERT INTO Requerir (idStageIn, idStageOut) VALUES
(2, 1),
(3, 2),
(4, 3);

-- Insertion de données dans la table Deplacer
INSERT INTO Deplacer (idBateau, dates, idPort) VALUES
(1, '2023-07-01', 1),
(2, '2023-07-02', 2),
(3, '2023-07-03', 3),
(4, '2023-07-04', 4);

-- Insertion de données dans la table Necessiter
INSERT INTO Necessiter (idBrevet, idStage) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);

-- Insertion de données dans la table Encadrer
INSERT INTO Encadrer (idAdherent, idSejour) VALUES
(1, 1),
(2, 2),
(2, 3);

-- Insertion de données dans la table Participer
INSERT INTO Participer (idAdherent, idSejour) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4);
