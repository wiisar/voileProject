package handlers

import (
	"crypto/sha256"
	"database/sql"
	"fmt"
	"log"
	"net/http"
	"strconv"
	"time"

	"github.com/gin-contrib/sessions"
	"github.com/gin-contrib/sessions/cookie"
	"github.com/gin-gonic/gin"
	"github.com/wiisar/voileProject/internal/models"
)

// Handler holds the database connection and provides HTTP handlers
type Handler struct {
	db *sql.DB
}

// New creates a new Handler instance
func New(db *sql.DB) *Handler {
	return &Handler{db: db}
}

// SetupSessions configures session middleware
func SetupSessions(r *gin.Engine) {
	store := cookie.NewStore([]byte("secret-key-change-in-production"))
	r.Use(sessions.Sessions("voile-session", store))
}

// Home handler for the main page
func (h *Handler) Home(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)
	
	c.HTML(http.StatusOK, "home.html", gin.H{
		"user": user,
		"success_message": session.Get("success_message"),
	})
	
	// Clear success message after displaying
	session.Delete("success_message")
	session.Save()
}

// Sejours handler for trips page
func (h *Handler) Sejours(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)

	// Get trip types
	types, err := h.getSejourTypes()
	if err != nil {
		log.Printf("Error getting sejour types: %v", err)
	}

	// Get boat models
	boats, err := h.getBoatModels()
	if err != nil {
		log.Printf("Error getting boat models: %v", err)
	}

	c.HTML(http.StatusOK, "sejours.html", gin.H{
		"user":  user,
		"types": types,
		"boats": boats,
	})
}

// SejoursPost handles the trip search form submission
func (h *Handler) SejoursPost(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)

	intitule := c.PostForm("intitule")
	boat := c.PostForm("boat")

	sejours, err := h.searchSejours(intitule, boat, user)
	if err != nil {
		log.Printf("Error searching sejours: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Database error"})
		return
	}

	// Get trip types and boats again for the form
	types, _ := h.getSejourTypes()
	boats, _ := h.getBoatModels()

	c.HTML(http.StatusOK, "sejours.html", gin.H{
		"user":    user,
		"types":   types,
		"boats":   boats,
		"sejours": sejours,
		"search":  gin.H{"intitule": intitule, "boat": boat},
	})
}

// Login handler
func (h *Handler) Login(c *gin.Context) {
	var req models.LoginRequest
	if err := c.ShouldBind(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid form data"})
		return
	}

	// Hash password
	hasher := sha256.New()
	hasher.Write([]byte(req.Password))
	hashedPassword := fmt.Sprintf("%x", hasher.Sum(nil))

	// Check credentials
	user, err := h.authenticateUser(req.Username, hashedPassword)
	if err != nil {
		log.Printf("Authentication error: %v", err)
		c.Redirect(http.StatusSeeOther, "/home.php")
		return
	}

	// Set session
	session := sessions.Default(c)
	session.Set("user_id", user.ID)
	session.Set("username", user.Username)
	session.Set("nom", user.Nom)
	session.Set("prenom", user.Prenom)
	session.Set("group", user.Group)
	session.Save()

	c.Redirect(http.StatusSeeOther, "/home.php")
}

// Signup handler
func (h *Handler) Signup(c *gin.Context) {
	var req models.SignupRequest
	if err := c.ShouldBind(&req); err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid form data"})
		return
	}

	// Hash password
	hasher := sha256.New()
	hasher.Write([]byte(req.Password))
	hashedPassword := fmt.Sprintf("%x", hasher.Sum(nil))

	// Create user
	err := h.createUser(req.Nom, req.Prenom, req.Mail, hashedPassword)
	if err != nil {
		log.Printf("User creation error: %v", err)
	}

	c.Redirect(http.StatusSeeOther, "/home.php")
}

// Logout handler
func (h *Handler) Logout(c *gin.Context) {
	session := sessions.Default(c)
	session.Clear()
	session.Save()
	c.Redirect(http.StatusSeeOther, "/home.php")
}

// Contact page handler
func (h *Handler) Contact(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)
	c.HTML(http.StatusOK, "contact.html", gin.H{"user": user})
}

// Club page handler
func (h *Handler) Club(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)
	c.HTML(http.StatusOK, "club.html", gin.H{"user": user})
}

// Horaires page handler
func (h *Handler) Horaires(c *gin.Context) {
	session := sessions.Default(c)
	user := h.getCurrentUser(session)
	c.HTML(http.StatusOK, "horaires.html", gin.H{"user": user})
}

// InscrireSejour handles trip registration
func (h *Handler) InscrireSejour(c *gin.Context) {
	session := sessions.Default(c)
	userID := session.Get("user_id")
	if userID == nil {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "Not logged in"})
		return
	}

	sejourID, err := strconv.Atoi(c.PostForm("inscrireSejour"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid sejour ID"})
		return
	}

	err = h.registerForSejour(userID.(int), sejourID)
	if err != nil {
		log.Printf("Registration error: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Registration failed"})
		return
	}

	session.Set("success_message", "Inscription réussie !")
	session.Save()
	c.Redirect(http.StatusSeeOther, "/sejour.php")
}

// DesinscrireSejour handles trip unregistration
func (h *Handler) DesinscrireSejour(c *gin.Context) {
	session := sessions.Default(c)
	userID := session.Get("user_id")
	if userID == nil {
		c.JSON(http.StatusUnauthorized, gin.H{"error": "Not logged in"})
		return
	}

	sejourID, err := strconv.Atoi(c.PostForm("desinscrireSejour"))
	if err != nil {
		c.JSON(http.StatusBadRequest, gin.H{"error": "Invalid sejour ID"})
		return
	}

	err = h.unregisterFromSejour(userID.(int), sejourID)
	if err != nil {
		log.Printf("Unregistration error: %v", err)
		c.JSON(http.StatusInternalServerError, gin.H{"error": "Unregistration failed"})
		return
	}

	session.Set("success_message", "Désinscription réussie.")
	session.Save()
	c.Redirect(http.StatusSeeOther, "/sejour.php")
}

// getCurrentUser extracts user information from session
func (h *Handler) getCurrentUser(session sessions.Session) *models.UserSession {
	userID := session.Get("user_id")
	if userID == nil {
		return nil
	}

	return &models.UserSession{
		ID:       userID.(int),
		Username: session.Get("username").(string),
		Nom:      session.Get("nom").(string),
		Prenom:   session.Get("prenom").(string),
		Group:    session.Get("group").(string),
	}
}

// Admin middleware
func (h *Handler) AdminMiddleware() gin.HandlerFunc {
	return func(c *gin.Context) {
		session := sessions.Default(c)
		group := session.Get("group")
		
		if group == nil || (group != "Administrateur" && group != "Planificateur") {
			c.AbortWithStatus(http.StatusForbidden)
			return
		}
		
		c.Next()
	}
}

// Database helper methods
func (h *Handler) getSejourTypes() ([]string, error) {
	query := `SELECT DISTINCT intitule 
              FROM Sejours Se 
              JOIN Stages S ON Se.idStage=S.idStage`
	
	rows, err := h.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var types []string
	for rows.Next() {
		var intitule string
		if err := rows.Scan(&intitule); err != nil {
			continue
		}
		types = append(types, intitule)
	}
	return types, nil
}

func (h *Handler) getBoatModels() ([]models.Modele, error) {
	query := `SELECT DISTINCT idModele, type FROM Modeles`
	
	rows, err := h.db.Query(query)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var boats []models.Modele
	for rows.Next() {
		var boat models.Modele
		if err := rows.Scan(&boat.ID, &boat.Type); err != nil {
			continue
		}
		boats = append(boats, boat)
	}
	return boats, nil
}

func (h *Handler) searchSejours(intitule, boat string, user *models.UserSession) ([]models.SejourDetails, error) {
	query := `SELECT S.intitule, M.type, P1.emplacements AS P_Depart, P2.emplacements AS P_Arriver, 
                     S.duree, Se.capacite, Se.prix, Se.idSejour
              FROM Sejours Se 
              JOIN Stages S ON Se.idStage = S.idStage
              JOIN Modeles M ON S.idModele = M.idModele
              JOIN Ports P1 ON Se.idPortsDepart = P1.idPort
              JOIN Ports P2 ON Se.idPortsArriver = P2.idPort
              WHERE (? = '' OR S.intitule = ?) 
              AND (? = '' OR M.idModele = ?)`

	rows, err := h.db.Query(query, intitule, intitule, boat, boat)
	if err != nil {
		return nil, err
	}
	defer rows.Close()

	var sejours []models.SejourDetails
	for rows.Next() {
		var s models.SejourDetails
		err := rows.Scan(&s.Intitule, &s.Type, &s.PortDepart, &s.PortArriver,
			&s.Duree, &s.Capacite, &s.Prix, &s.ID)
		if err != nil {
			continue
		}

		// Check if user is registered for this trip
		if user != nil {
			s.IsInscrit = h.isUserRegistered(user.ID, s.ID)
		}

		sejours = append(sejours, s)
	}
	return sejours, nil
}

func (h *Handler) isUserRegistered(userID, sejourID int) bool {
	var count int
	query := `SELECT COUNT(*) FROM Participer WHERE idAdherent = ? AND idSejour = ?`
	h.db.QueryRow(query, userID, sejourID).Scan(&count)
	return count > 0
}

func (h *Handler) authenticateUser(username, hashedPassword string) (*models.UserSession, error) {
	query := `SELECT idAdherent, nom, prenom, mail FROM Adherents WHERE mail = ? AND passwd = ?`
	
	var user models.UserSession
	var id int
	var nom, prenom, mail string
	
	err := h.db.QueryRow(query, username, hashedPassword).Scan(&id, &nom, &prenom, &mail)
	if err != nil {
		return nil, fmt.Errorf("invalid credentials")
	}

	// Get user group/rights
	groupQuery := `SELECT D.nom FROM PossederDroits P JOIN DroitsRef D ON P.idDroit = D.idDroit WHERE P.idAdherent = ?`
	var group string
	err = h.db.QueryRow(groupQuery, id).Scan(&group)
	if err != nil {
		group = "no group"
	}

	user.ID = id
	user.Username = mail
	user.Nom = nom
	user.Prenom = prenom
	user.Group = group

	return &user, nil
}

func (h *Handler) createUser(nom, prenom, mail, hashedPassword string) error {
	query := `INSERT INTO Adherents (nom, prenom, mail, dateAdhesion, passwd) VALUES (?, ?, ?, ?, ?)`
	_, err := h.db.Exec(query, nom, prenom, mail, time.Now().Format("2006-01-02"), hashedPassword)
	return err
}

func (h *Handler) registerForSejour(userID, sejourID int) error {
	// Check if already registered
	var count int
	checkQuery := `SELECT COUNT(*) FROM Participer WHERE idAdherent = ? AND idSejour = ?`
	h.db.QueryRow(checkQuery, userID, sejourID).Scan(&count)
	
	if count > 0 {
		return fmt.Errorf("already registered")
	}

	// Register
	query := `INSERT INTO Participer (idAdherent, idSejour) VALUES (?, ?)`
	_, err := h.db.Exec(query, userID, sejourID)
	return err
}

func (h *Handler) unregisterFromSejour(userID, sejourID int) error {
	query := `DELETE FROM Participer WHERE idAdherent = ? AND idSejour = ?`
	_, err := h.db.Exec(query, userID, sejourID)
	return err
}