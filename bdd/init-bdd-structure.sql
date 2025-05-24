SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;
SET collation_connection = utf8mb4_unicode_ci;

CREATE TABLE FAQ( 
    idFAQ  Int  Auto_increment  NOT NULL ,
    question      Varchar (500) NOT NULL ,
    reponse       Varchar (5000) NOT NULL ,
    CONSTRAINT FAQ_PK PRIMARY KEY (idFAQ)
)ENGINE=InnoDB;

CREATE TABLE DroitsRef(
    idDroit Int Auto_increment NOT NULL,
    nom     Varchar(50) NOT NULL,
    CONSTRAINT Droit_PK PRIMARY KEY (idDroit)
)ENGINE=InnoDB;

CREATE TABLE Adherents( 
    idAdherent  Int  Auto_increment  NOT NULL ,
    nom          Varchar (50) NOT NULL ,
    prenom       Varchar (50) NOT NULL ,
    mail         Varchar (50) NOT NULL ,
    dateAdhesion Date NOT NULL,
    passwd Varchar(500) NOT NULL,
    CONSTRAINT Adherents_PK PRIMARY KEY (idAdherent)
)ENGINE=InnoDB;

CREATE TABLE PossederDroits(
    idAdherent Int NOT NULL,
    idDroit Int NOT NULL,
    CONSTRAINT PossederDroits_PK PRIMARY KEY (idAdherent, idDroit),
    CONSTRAINT PossederDroits_Adherents_FK FOREIGN KEY (idAdherent) REFERENCES Adherents(idAdherent),
    CONSTRAINT PossederDroits_DroitsRef_FK FOREIGN KEY (idDroit) REFERENCES DroitsRef(idDroit)
)ENGINE = InnoDB;

CREATE TABLE Modeles(
    idModele Int  Auto_increment  NOT NULL ,
    type     Varchar (50) NOT NULL ,
    capacite Int NOT NULL ,
    CONSTRAINT Modeles_PK PRIMARY KEY (idModele)
)ENGINE=InnoDB;

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

CREATE TABLE Brevets(
    idBrevet   Int  Auto_increment  NOT NULL ,
    intitule   Varchar (50) NOT NULL ,
    encadrement Bool NOT NULL ,
    idStage    Int NOT NULL,
    CONSTRAINT Brevets_PK PRIMARY KEY (idBrevet),
    CONSTRAINT Brevets_Stages_FK FOREIGN KEY (idStage) REFERENCES Stages(idStage)
)ENGINE=InnoDB;

CREATE TABLE Bateaux(
    idBateau  Int  Auto_increment  NOT NULL ,
    nom       Varchar (50) NOT NULL ,
    dateAchat Date NOT NULL,
    idModele Int NOT NULL,
    CONSTRAINT Bateaux_PK PRIMARY KEY (idBateau),
    CONSTRAINT Modeles_Bateaux_FK FOREIGN KEY (idModele) REFERENCES Modeles(idModele)
)ENGINE=InnoDB;

CREATE TABLE Ports(
    idPort     Int  Auto_increment  NOT NULL ,
    emplacements Varchar (50) NOT NULL,
    CONSTRAINT Ports_PK PRIMARY KEY (idPort)
)ENGINE=InnoDB;

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

CREATE TABLE Posseder(
    idAdherent Int NOT NULL ,
    idBrevet    Int NOT NULL,
    CONSTRAINT Posseder_PK PRIMARY KEY (idAdherent,idBrevet),
    CONSTRAINT Posseder_Adherents_FK FOREIGN KEY (idAdherent) REFERENCES Adherents(idAdherent),
    CONSTRAINT Posseder_Brevets0_FK FOREIGN KEY (idBrevet) REFERENCES Brevets(idBrevet)
)ENGINE=InnoDB;

CREATE TABLE Requerir(
    idStageIn        Int NOT NULL ,
    idStageOut  Int NOT NULL,
    CONSTRAINT Requerir_PK PRIMARY KEY (idStageIn,idStageOut),
    CONSTRAINT Requerir_Stages_FK FOREIGN KEY (idStageIn) REFERENCES Stages(idStage),
    CONSTRAINT Requerir_Stages0_FK FOREIGN KEY (idStageOut) REFERENCES Stages(idStage)
)ENGINE=InnoDB;

CREATE TABLE Deplacer(
    idBateau Int NOT NULL ,
    dates    date NOT NULL ,
    idPort  Int NOT NULL,
    CONSTRAINT Deplacer_PK PRIMARY KEY (idBateau,dates,idPort),
    CONSTRAINT Deplacer_Bateaux_FK FOREIGN KEY (idBateau) REFERENCES Bateaux(idBateau),
    CONSTRAINT Deplacer_Ports1_FK FOREIGN KEY (idPort) REFERENCES Ports(idPort)
)ENGINE=InnoDB;

CREATE TABLE Necessiter(
    idBrevet Int NOT NULL ,
    idStage  Int NOT NULL,
    CONSTRAINT Necessiter_PK PRIMARY KEY (idBrevet,idStage),
    CONSTRAINT Necessiter_Brevets_FK FOREIGN KEY (idBrevet) REFERENCES Brevets(idBrevet),
    CONSTRAINT Necessiter_Stages0_FK FOREIGN KEY (idStage) REFERENCES Stages(idStage)
)ENGINE=InnoDB;

CREATE TABLE Encadrer(
    idAdherent Int NOT NULL ,
    idSejour    Int NOT NULL,
    CONSTRAINT Encadrer_PK PRIMARY KEY (idAdherent,idSejour),
    CONSTRAINT Encadrer_Adherents_FK FOREIGN KEY (idAdherent) REFERENCES Adherents(idAdherent),
    CONSTRAINT Encadrer_Sejour0_FK FOREIGN KEY (idSejour) REFERENCES Sejours(idSejour)
)ENGINE=InnoDB;

CREATE TABLE Participer (
    idAdherent INT NOT NULL,
    idSejour INT NOT NULL,
    PRIMARY KEY (idAdherent, idSejour),
    CONSTRAINT Participer_Adherents_FK FOREIGN KEY (idAdherent) REFERENCES Adherents(idAdherent),
    CONSTRAINT Participer_Sejour_FK FOREIGN KEY (idSejour) REFERENCES Sejours(idSejour)
) ENGINE=InnoDB;

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