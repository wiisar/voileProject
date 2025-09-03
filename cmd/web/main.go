package main

import (
	"log"
	"net/http"
	"os"

	"github.com/gin-gonic/gin"
	"github.com/wiisar/voileProject/internal/database"
	"github.com/wiisar/voileProject/internal/handlers"
)

func main() {
	// Database connection
	db, err := database.Connect()
	if err != nil {
		log.Fatal("Failed to connect to database:", err)
	}
	defer db.Close()

	// Initialize handlers
	h := handlers.New(db)

	// Setup Gin router
	r := gin.Default()
	
	// Setup sessions
	handlers.SetupSessions(r)
	
	// Load HTML templates
	r.LoadHTMLGlob("web/templates/*")
	
	// Serve static files
	r.Static("/static", "./web/static")
	r.Static("/images", "./html/images")
	r.Static("/style", "./html/style")
	r.Static("/script", "./html/script")

	// Setup routes
	setupRoutes(r, h)

	// Get port from environment or default to 8080
	port := os.Getenv("PORT")
	if port == "" {
		port = "8080"
	}

	log.Printf("Server starting on port %s", port)
	log.Fatal(http.ListenAndServe(":"+port, r))
}

func setupRoutes(r *gin.Engine, h *handlers.Handler) {
	// Public routes
	r.GET("/", h.Home)
	r.GET("/home.php", h.Home) // Keep PHP routes for compatibility
	r.GET("/sejour.php", h.Sejours)
	r.GET("/contact.php", h.Contact)
	r.GET("/club.php", h.Club)
	r.GET("/horaires.php", h.Horaires)

	// Auth routes
	r.POST("/login", h.Login)
	r.POST("/signup", h.Signup)
	r.GET("/logout", h.Logout)

	// Trip management
	r.POST("/sejour", h.SejoursPost)
	r.POST("/inscrire-sejour", h.InscrireSejour)
	r.POST("/desinscrire-sejour", h.DesinscrireSejour)

	// Admin routes (protected)
	admin := r.Group("/adminpage")
	admin.Use(h.AdminMiddleware())
	{
		admin.GET("/users.php", h.AdminUsers)
		admin.GET("/sejours.php", h.AdminSejours)
		admin.GET("/sejour_details.php", h.AdminSejourDetails)
		admin.POST("/add-sejour", h.AddSejour)
		admin.POST("/delete-sejour", h.DeleteSejour)
		admin.POST("/add-user", h.AddUser)
		admin.POST("/delete-user", h.DeleteUser)
		admin.POST("/update-user", h.UpdateUser)
	}
}