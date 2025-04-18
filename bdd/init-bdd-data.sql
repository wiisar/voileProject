# Set character encoding
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = utf8mb4_unicode_ci;
#------------------------------------------------------------
# Table: Adherents
#------------------------------------------------------------

CREATE TABLE Adherents( 
    idAdherent  Int  Auto_increment  NOT NULL ,
    nom          Varchar (50) NOT NULL ,
    prenom       Varchar (50) NOT NULL ,
    mail         Varchar (50) NOT NULL ,
    dateAdhesion Date NOT NULL,
    passwd Varchar(500) NOT NULL,
    CONSTRAINT Adherents_PK PRIMARY KEY (idAdherent)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Modeles
#------------------------------------------------------------

CREATE TABLE Modeles(
    idModele Int  Auto_increment  NOT NULL ,
    type     Varchar (50) NOT NULL ,
    capacite Int NOT NULL ,
    CONSTRAINT Modeles_PK PRIMARY KEY (idModele)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Stages
#------------------------------------------------------------

CREATE TABLE Stages(
    idStage Int  Auto_increment  NOT NULL ,
    intitule Varchar (50) NOT NULL ,
    duree    Time NOT NULL ,
    niveau   Int NOT NULL ,
    prixMin  Float NOT NULL ,
    prixMax  Float NOT NULL,
    idModele Int NOT NULL,
    CONSTRAINT Stages_PK PRIMARY KEY (idStage),
    CONSTRAINT Stages_Modeles_FK FOREIGN KEY (idModele) REFERENCES Modeles(idModele)

)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Brevets
#------------------------------------------------------------

CREATE TABLE Brevets(
    idBrevet   Int  Auto_increment  NOT NULL ,
    intitule   Varchar (50) NOT NULL ,
    encadrement Bool NOT NULL ,
    idStage    Int NOT NULL,
    CONSTRAINT Brevets_PK PRIMARY KEY (idBrevet),
    CONSTRAINT Brevets_Stages_FK FOREIGN KEY (idStage) REFERENCES Stages(idStage)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Bateaux
#------------------------------------------------------------

CREATE TABLE Bateaux(
    idBateau  Int  Auto_increment  NOT NULL ,
    nom       Varchar (50) NOT NULL ,
    dateAchat Date NOT NULL,
    idModele Int NOT NULL,
    CONSTRAINT Bateaux_PK PRIMARY KEY (idBateau),
    CONSTRAINT Modeles_Bateaux_FK FOREIGN KEY (idModele) REFERENCES Modeles(idModele)
    
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Ports
#------------------------------------------------------------

CREATE TABLE Ports(
    idPort     Int  Auto_increment  NOT NULL ,
    emplacements Varchar (50) NOT NULL,
    CONSTRAINT Ports_PK PRIMARY KEY (idPort)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Sejours
#------------------------------------------------------------

CREATE TABLE Sejours(
    idSejour         Int  Auto_increment  NOT NULL ,
    capacite         Int NOT NULL ,
    prix             Float NOT NULL ,
    idPortsDepart    Int NOT NULL ,
    idPortsArriver   Int NOT NULL,
    dates Date NOT NULL,
    idStage    Int NOT NULL,
    CONSTRAINT Sejour_PK PRIMARY KEY (idSejour),
    CONSTRAINT Sejour_Ports_Depart_FK FOREIGN KEY (idPortsDepart) REFERENCES Ports(idPort),
    CONSTRAINT Sejour_Ports_Arriver_FK FOREIGN KEY (idPortsArriver) REFERENCES Ports(idPort),
    CONSTRAINT Sejour_Stages_FK FOREIGN KEY (idStage) REFERENCES Stages(idStage)

)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Posseder
#------------------------------------------------------------

CREATE TABLE Posseder(
    idAdherent Int NOT NULL ,
    idBrevet    Int NOT NULL,
    CONSTRAINT Posseder_PK PRIMARY KEY (idAdherent,idBrevet),
    CONSTRAINT Posseder_Adherents_FK FOREIGN KEY (idAdherent) REFERENCES Adherents(idAdherent),
    CONSTRAINT Posseder_Brevets0_FK FOREIGN KEY (idBrevet) REFERENCES Brevets(idBrevet)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Requerir
#------------------------------------------------------------

CREATE TABLE Requerir(
    idStageIn        Int NOT NULL ,
    idStageOut  Int NOT NULL,
    CONSTRAINT Requerir_PK PRIMARY KEY (idStageIn,idStageOut),
    CONSTRAINT Requerir_Stages_FK FOREIGN KEY (idStageIn) REFERENCES Stages(idStage),
    CONSTRAINT Requerir_Stages0_FK FOREIGN KEY (idStageOut) REFERENCES Stages(idStage)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Deplacer
#------------------------------------------------------------

CREATE TABLE Deplacer(
    idBateau Int NOT NULL ,
    dates    date NOT NULL ,
    idPort  Int NOT NULL,
    CONSTRAINT Deplacer_PK PRIMARY KEY (idBateau,dates,idPort),
    CONSTRAINT Deplacer_Bateaux_FK FOREIGN KEY (idBateau) REFERENCES Bateaux(idBateau),
    CONSTRAINT Deplacer_Ports1_FK FOREIGN KEY (idPort) REFERENCES Ports(idPort)
)ENGINE=InnoDB;

#------------------------------------------------------------
# Table: Necessiter
#------------------------------------------------------------

CREATE TABLE Necessiter(
    idBrevet Int NOT NULL ,
    idStage  Int NOT NULL,
    CONSTRAINT Necessiter_PK PRIMARY KEY (idBrevet,idStage),
    CONSTRAINT Necessiter_Brevets_FK FOREIGN KEY (idBrevet) REFERENCES Brevets(idBrevet),
    CONSTRAINT Necessiter_Stages0_FK FOREIGN KEY (idStage) REFERENCES Stages(idStage)
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Encadrer
#------------------------------------------------------------

CREATE TABLE Encadrer(
    idAdherent Int NOT NULL ,
    idSejour    Int NOT NULL,
    CONSTRAINT Encadrer_PK PRIMARY KEY (idAdherent,idSejour),
    CONSTRAINT Encadrer_Adherents_FK FOREIGN KEY (idAdherent) REFERENCES Adherents(idAdherent),
    CONSTRAINT Encadrer_Sejour0_FK FOREIGN KEY (idSejour) REFERENCES Sejours(idSejour)
)ENGINE=InnoDB;



DELIMITER //
CREATE TRIGGER verif_dip_encadrement
BEFORE INSERT ON Encadrer
FOR EACH ROW
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM Brevets B
        JOIN Necessiter N ON B.idBrevet = N.idBrevet
        JOIN Stages S ON N.idStage = S.idStage 
        JOIN Sejours Se ON S.idStage= Se.idStage
        JOIN Posseder P ON B.idBrevet = P.idBrevet
        JOIN Adherents A ON P.idAdherent = A.idAdherent
        WHERE Se.idSejour = NEW.idSejour
          AND A.idAdherent = NEW.idAdherent
    ) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L''encadrant ne possède pas le diplôme nécessaire pour ce stage.';
    END IF;
END; //
DELIMITER ;


-- -- Exception






#------------------------------------------------------------
# Insertion des données de test
#------------------------------------------------------------

-- Insertion de données dans la table Adherents
INSERT INTO Adherents (nom, prenom, mail, dateAdhesion,passwd) VALUES
('Dupont', 'Jean', 'jean.dupont@example.com', '2023-01-15','5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8'), -- mdp : password
('Martin', 'Marie', 'marie.martin@example.com', '2023-02-20','5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8'),
('Lefevre', 'Paul', 'paul.lefevre@example.com', '2023-03-25','5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8'),
('Garnier', 'Sophie', 'sophie.garnier@example.com', '2023-04-30','5e884898da28047151d0e56f8dc6292773603d0d6aabbdd62a11ef721d1542d8');

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
INSERT INTO Bateaux (nom, dateAchat,idModele) VALUES
('Voilier A', '2022-05-10',1),
('Voilier B', '2022-06-20',1),
('Catamaran C', '2021-07-15',2),
('Canoe 1', '2020-08-25',3),
('Plouf', '2020-08-25',6),
('Paddle 1', '2020-08-25',8),
('Surf 1', '2020-08-25',7);


-- Insertion de données dans la table Stages
INSERT INTO Stages (intitule, duree, niveau, prixMin, prixMax,idModele) VALUES
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
INSERT INTO Sejours (capacite, prix, idPortsDepart, idPortsArriver,dates,idStage) VALUES
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
