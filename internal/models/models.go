package models

import (
	"time"
)

// Adherent represents a user/member in the system
type Adherent struct {
	ID           int       `json:"id" db:"idAdherent"`
	Nom          string    `json:"nom" db:"nom"`
	Prenom       string    `json:"prenom" db:"prenom"`
	Mail         string    `json:"mail" db:"mail"`
	DateAdhesion time.Time `json:"dateAdhesion" db:"dateAdhesion"`
	Password     string    `json:"-" db:"passwd"` // Don't include in JSON
}

// DroitRef represents user rights/permissions
type DroitRef struct {
	ID  int    `json:"id" db:"idDroit"`
	Nom string `json:"nom" db:"nom"`
}

// Modele represents a boat model
type Modele struct {
	ID       int    `json:"id" db:"idModele"`
	Type     string `json:"type" db:"type"`
	Capacite int    `json:"capacite" db:"capacite"`
}

// Stage represents a training course
type Stage struct {
	ID       int     `json:"id" db:"idStage"`
	Intitule string  `json:"intitule" db:"intitule"`
	Duree    string  `json:"duree" db:"duree"` // Time as string for HTML display
	Niveau   int     `json:"niveau" db:"niveau"`
	PrixMin  float64 `json:"prixMin" db:"prixMin"`
	PrixMax  float64 `json:"prixMax" db:"prixMax"`
	IDModele int     `json:"idModele" db:"idModele"`
}

// Brevet represents a certification
type Brevet struct {
	ID          int    `json:"id" db:"idBrevet"`
	Intitule    string `json:"intitule" db:"intitule"`
	Encadrement bool   `json:"encadrement" db:"encadrement"`
	IDStage     int    `json:"idStage" db:"idStage"`
}

// Bateau represents a boat
type Bateau struct {
	ID        int       `json:"id" db:"idBateau"`
	Nom       string    `json:"nom" db:"nom"`
	DateAchat time.Time `json:"dateAchat" db:"dateAchat"`
	IDModele  int       `json:"idModele" db:"idModele"`
}

// Port represents a harbor/port
type Port struct {
	ID           int    `json:"id" db:"idPort"`
	Emplacements string `json:"emplacements" db:"emplacements"`
}

// Sejour represents a trip/stay
type Sejour struct {
	ID             int       `json:"id" db:"idSejour"`
	Capacite       int       `json:"capacite" db:"capacite"`
	Prix           float64   `json:"prix" db:"prix"`
	IDPortsDepart  int       `json:"idPortsDepart" db:"idPortsDepart"`
	IDPortsArriver int       `json:"idPortsArriver" db:"idPortsArriver"`
	Dates          time.Time `json:"dates" db:"dates"`
	IDStage        int       `json:"idStage" db:"idStage"`
}

// SejourDetails represents a detailed view of a trip with joined data
type SejourDetails struct {
	Sejour
	Intitule      string `json:"intitule" db:"intitule"`
	Type          string `json:"type" db:"type"`
	Duree         string `json:"duree" db:"duree"`
	PortDepart    string `json:"portDepart" db:"P_Depart"`
	PortArriver   string `json:"portArriver" db:"P_Arriver"`
	IsInscrit     bool   `json:"isInscrit"`
	NbParticipants int   `json:"nbParticipants"`
}

// User session data
type UserSession struct {
	ID       int    `json:"id"`
	Username string `json:"username"`
	Nom      string `json:"nom"`
	Prenom   string `json:"prenom"`
	Group    string `json:"group"`
}

// Login request
type LoginRequest struct {
	Username string `json:"username" form:"username"`
	Password string `json:"password" form:"password"`
}

// Signup request
type SignupRequest struct {
	Nom             string `json:"nom" form:"nom"`
	Prenom          string `json:"prenom" form:"prenom"`
	Mail            string `json:"mail" form:"mail"`
	Password        string `json:"password" form:"password"`
	ConfirmPassword string `json:"confirm_password" form:"confirm_password"`
}