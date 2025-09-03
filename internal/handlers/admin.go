package handlers

import (
	"log"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
	"github.com/wiisar/voileProject/internal/models"
)

// AdminUsers handles the user management page
func (h *Handler) AdminUsers(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)

	users, err := h.getAllUsers()
	if err != nil {
		log.Printf("Error getting users: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database error"})
		return
	}

	c.HTML(http.StatusOK, "admin_users.html", gin.H{
		"user":  user,
		"users": users,
	})
}

// AdminSejours handles the trip management page
func (h *Handler) AdminSejours(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)

	sejours, err := h.getAllSejoursForAdmin()
	if err != nil {
		log.Printf("Error getting sejours: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database error"})
		return
	}

	stages, err := h.getAllStages()
	if err != nil {
		log.Printf("Error getting stages: %v", err)
	}

	ports, err := h.getAllPorts()
	if err != nil {
		log.Printf("Error getting ports: %v", err)
	}

	c.HTML(http.StatusOK, "admin_sejours.html", gin.H{
		"user":    user,
		"sejours": sejours,
		"stages":  stages,
		"ports":   ports,
	})
}

// AdminSejourDetails handles the detailed trip management page
func (h *Handler) AdminSejourDetails(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)

	sejourID, err := strconv.Atoi(c.Query("idSejour"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid sejour ID"})
		return
	}

	sejour, err := h.getSejourDetails(sejourID)
	if err != nil {
		log.Printf("Error getting sejour details: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database error"})
		return
	}

	participants, err := h.getSejourParticipants(sejourID)
	if err != nil {
		log.Printf("Error getting participants: %v", err)
	}

	encadrants, err := h.getSejourEncadrants(sejourID)
	if err != nil {
		log.Printf("Error getting encadrants: %v", err)
	}

	availableUsers, err := h.getAvailableUsers(sejourID)
	if err != nil {
		log.Printf("Error getting available users: %v", err)
	}

	c.HTML(http.StatusOK, "admin_sejour_details.html", gin.H{
		"user":           user,
		"sejour":         sejour,
		"participants":   participants,
		"encadrants":     encadrants,
		"availableUsers": availableUsers,
	})
}

// AddSejour handles adding a new trip
func (h *Handler) AddSejour(c *gin.Context) {
	capacite, _ := strconv.Atoi(c.PostForm("capacite"))
	prix, _ := strconv.ParseFloat(c.PostForm("prix"), 64)
	idPortsDepart, _ := strconv.Atoi(c.PostForm("idPortsDepart"))
	idPortsArriver, _ := strconv.Atoi(c.PostForm("idPortsArriver"))
	dates := c.PostForm("dates")
	idStage, _ := strconv.Atoi(c.PostForm("idStage"))

	dateTime, err := time.Parse("2006-01-02", dates)
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid date format"})
		return
	}

	err = h.createSejour(capacite, prix, idPortsDepart, idPortsArriver, dateTime, idStage)
	if err != nil {
		log.Printf("Error creating sejour: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create sejour"})
		return
	}

	c.Redirect(http.StatusSeeOther, "/adminpage/sejours.php")
}

// DeleteSejour handles trip deletion
func (h *Handler) DeleteSejour(c *gin.Context) {
	sejourID, err := strconv.Atoi(c.PostForm("idSejour"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid sejour ID"})
		return
	}

	err = h.deleteSejour(sejourID)
	if err != nil {
		log.Printf("Error deleting sejour: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete sejour"})
		return
	}

	c.Redirect(http.StatusSeeOther, "/adminpage/sejours.php")
}

// AddUser handles adding a new user
func (h *Handler) AddUser(c *gin.Context) {
	nom := c.PostForm("nom")
	prenom := c.PostForm("prenom")
	mail := c.PostForm("mail")
	password := c.PostForm("password")

	err := h.createUser(nom, prenom, mail, password)
	if err != nil {
		log.Printf("Error creating user: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to create user"})
		return
	}

	c.Redirect(http.StatusSeeOther, "/adminpage/users.php")
}

// DeleteUser handles user deletion
func (h *Handler) DeleteUser(c *gin.Context) {
	userID, err := strconv.Atoi(c.PostForm("idAdherent"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid user ID"})
		return
	}

	err = h.deleteUser(userID)
	if err != nil {
		log.Printf("Error deleting user: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to delete user"})
		return
	}

	c.Redirect(http.StatusSeeOther, "/adminpage/users.php")
}

// UpdateUser handles user updates
func (h *Handler) UpdateUser(c *gin.Context) {
	userID, err := strconv.Atoi(c.PostForm("idAdherent"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid user ID"})
		return
	}

	nom := c.PostForm("nom")
	prenom := c.PostForm("prenom")
	mail := c.PostForm("mail")

	err = h.updateUser(userID, nom, prenom, mail)
	if err != nil {
		log.Printf("Error updating user: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Failed to update user"})
		return
	}

	c.Redirect(http.StatusSeeOther, "/adminpage/users.php")
}

// Database helper methods for admin functions

func (h *Handler) getAllUsers() ([]models.Adherent, error) {
	query := `SELECT idAdherent, nom, prenom, mail, dateAdhesion FROM Adherents ORDER BY nom, prenom`
	
	rows, err := h.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var users []models.Adherent
	for rows.Next() {
		var user models.Adherent
		err := rows.Scan(&user.ID, &user.Nom, &user.Prenom, &user.Mail, &user.DateAdhesion)
		if err != nil {
			continue
		}
		users = append(users, user)
	}
	return users, nil
}

func (h *Handler) getAllSejoursForAdmin() ([]models.SejourDetails, error) {
	query := `SELECT Se.idSejour, Se.capacite, Se.prix, Se.dates, 
                     S.intitule, P1.emplacements AS P_Depart, P2.emplacements AS P_Arriver,
                     M.type, S.duree
              FROM Sejours Se 
              JOIN Stages S ON Se.idStage = S.idStage
              JOIN Modeles M ON S.idModele = M.idModele
              JOIN Ports P1 ON Se.idPortsDepart = P1.idPort
              JOIN Ports P2 ON Se.idPortsArriver = P2.idPort
              ORDER BY Se.dates DESC`
	
	rows, err := h.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var sejours []models.SejourDetails
	for rows.Next() {
		var s models.SejourDetails
		err := rows.Scan(&s.ID, &s.Capacite, &s.Prix, &s.Dates,
			&s.Intitule, &s.PortDepart, &s.PortArriver, &s.Type, &s.Duree)
		if err != nil {
			continue
		}
		sejours = append(sejours, s)
	}
	return sejours, nil
}

func (h *Handler) getAllStages() ([]models.Stage, error) {
	query := `SELECT idStage, intitule, duree, niveau, prixMin, prixMax, idModele FROM Stages`
	
	rows, err := h.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var stages []models.Stage
	for rows.Next() {
		var stage models.Stage
		err := rows.Scan(&stage.ID, &stage.Intitule, &stage.Duree, &stage.Niveau,
			&stage.PrixMin, &stage.PrixMax, &stage.IDModele)
		if err != nil {
			continue
		}
		stages = append(stages, stage)
	}
	return stages, nil
}

func (h *Handler) getAllPorts() ([]models.Port, error) {
	query := `SELECT idPort, emplacements FROM Ports`
	
	rows, err := h.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var ports []models.Port
	for rows.Next() {
		var port models.Port
		err := rows.Scan(&port.ID, &port.Emplacements)
		if err != nil {
			continue
		}
		ports = append(ports, port)
	}
	return ports, nil
}

func (h *Handler) getSejourDetails(sejourID int) (*models.SejourDetails, error) {
	query := `SELECT Se.idSejour, Se.capacite, Se.prix, Se.dates, 
                     S.intitule, P1.emplacements AS P_Depart, P2.emplacements AS P_Arriver,
                     M.type, S.duree
              FROM Sejours Se 
              JOIN Stages S ON Se.idStage = S.idStage
              JOIN Modeles M ON S.idModele = M.idModele
              JOIN Ports P1 ON Se.idPortsDepart = P1.idPort
              JOIN Ports P2 ON Se.idPortsArriver = P2.idPort
              WHERE Se.idSejour = ?`
	
	var s models.SejourDetails
	err := h.db.QueryRow(query, sejourID).Scan(&s.ID, &s.Capacite, &s.Prix, &s.Dates,
		&s.Intitule, &s.PortDepart, &s.PortArriver, &s.Type, &s.Duree)
	if err != nil {
		return nil, err
	}
	return &s, nil
}

func (h *Handler) getSejourParticipants(sejourID int) ([]models.Adherent, error) {
	query := `SELECT A.idAdherent, A.nom, A.prenom, A.mail, A.dateAdhesion 
              FROM Adherents A 
              JOIN Participer P ON A.idAdherent = P.idAdherent 
              WHERE P.idSejour = ?`
	
	rows, err := h.db.Query(query, sejourID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var participants []models.Adherent
	for rows.Next() {
		var user models.Adherent
		err := rows.Scan(&user.ID, &user.Nom, &user.Prenom, &user.Mail, &user.DateAdhesion)
		if err != nil {
			continue
		}
		participants = append(participants, user)
	}
	return participants, nil
}

func (h *Handler) getSejourEncadrants(sejourID int) ([]models.Adherent, error) {
	query := `SELECT A.idAdherent, A.nom, A.prenom, A.mail, A.dateAdhesion 
              FROM Adherents A 
              JOIN Encadrer E ON A.idAdherent = E.idAdherent 
              WHERE E.idSejour = ?`
	
	rows, err := h.db.Query(query, sejourID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var encadrants []models.Adherent
	for rows.Next() {
		var user models.Adherent
		err := rows.Scan(&user.ID, &user.Nom, &user.Prenom, &user.Mail, &user.DateAdhesion)
		if err != nil {
			continue
		}
		encadrants = append(encadrants, user)
	}
	return encadrants, nil
}

func (h *Handler) getAvailableUsers(sejourID int) ([]models.Adherent, error) {
	query := `SELECT A.idAdherent, A.nom, A.prenom, A.mail, A.dateAdhesion 
              FROM Adherents A 
              WHERE A.idAdherent NOT IN (
                  SELECT P.idAdherent FROM Participer P WHERE P.idSejour = ?
              ) AND A.idAdherent NOT IN (
                  SELECT E.idAdherent FROM Encadrer E WHERE E.idSejour = ?
              )`
	
	rows, err := h.db.Query(query, sejourID, sejourID)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var users []models.Adherent
	for rows.Next() {
		var user models.Adherent
		err := rows.Scan(&user.ID, &user.Nom, &user.Prenom, &user.Mail, &user.DateAdhesion)
		if err != nil {
			continue
		}
		users = append(users, user)
	}
	return users, nil
}

func (h *Handler) createSejour(capacite int, prix float64, idPortsDepart, idPortsArriver int, dates time.Time, idStage int) error {
	query := `INSERT INTO Sejours (capacite, prix, idPortsDepart, idPortsArriver, dates, idStage) 
              VALUES (?, ?, ?, ?, ?, ?)`
	_, err := h.db.Exec(query, capacite, prix, idPortsDepart, idPortsArriver, dates, idStage)
	return err
}

func (h *Handler) deleteSejour(sejourID int) error {
	// Delete participants first (foreign key constraint)
	_, err := h.db.Exec("DELETE FROM Participer WHERE idSejour = ?", sejourID)
	if err != nil {
		return err
	}
	
	// Delete encadrants
	_, err = h.db.Exec("DELETE FROM Encadrer WHERE idSejour = ?", sejourID)
	if err != nil {
		return err
	}

	// Delete sejour
	_, err = h.db.Exec("DELETE FROM Sejours WHERE idSejour = ?", sejourID)
	return err
}

func (h *Handler) deleteUser(userID int) error {
	// Delete user relationships first
	h.db.Exec("DELETE FROM Participer WHERE idAdherent = ?", userID)
	h.db.Exec("DELETE FROM Encadrer WHERE idAdherent = ?", userID)
	h.db.Exec("DELETE FROM Posseder WHERE idAdherent = ?", userID)
	h.db.Exec("DELETE FROM PossederDroits WHERE idAdherent = ?", userID)

	// Delete user
	_, err := h.db.Exec("DELETE FROM Adherents WHERE idAdherent = ?", userID)
	return err
}

func (h *Handler) updateUser(userID int, nom, prenom, mail string) error {
	query := `UPDATE Adherents SET nom = ?, prenom = ?, mail = ? WHERE idAdherent = ?`
	_, err := h.db.Exec(query, nom, prenom, mail, userID)
	return err
}