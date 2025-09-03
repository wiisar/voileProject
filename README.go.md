# Project VOILE - Go Version

**Converted from PHP to Golang**

- Salome RODRIGUEZ
- Benjamin MANEM

## Overview

This project has been converted from PHP to Go (Golang), maintaining all original functionality while improving performance and maintainability.

## Features

- **User Management**: Registration, login, role-based access control
- **Trip Management**: Browse and book sailing trips
- **Admin Functions**: User and trip management for administrators
- **Database**: MySQL with complex relational schema
- **Web Interface**: Responsive web application

## Technology Stack

- **Backend**: Go (Golang) with Gin web framework
- **Database**: MySQL 
- **Frontend**: HTML templates with CSS/JavaScript
- **Deployment**: Docker containers

## Running with Docker

To run the website you need to install Docker:

```bash
# Install Docker
sudo apt-get update
sudo apt-get install docker-ce docker-ce-cli containerd.io

# Verify Docker installation
docker --version
```

Before running the website you can modify credentials in `var.env` to specify strong passwords.

### Using Go Application

Run the Go version of the application:

```bash
# Use the Go docker-compose file
docker-compose -f docker-compose.go.yml up -d --build
```

When the containers are running you can access the website at http://localhost:8080

To stop the website and clean containers:

```bash
docker-compose -f docker-compose.go.yml down
```

### Legacy PHP Version (for comparison)

The original PHP version is still available:

```bash
# Use the original PHP docker-compose file
docker-compose up -d --build
```

## Development

### Running Locally

1. Install Go 1.21 or later
2. Install dependencies:
   ```bash
   go mod tidy
   ```
3. Set up MySQL database and run SQL scripts from `bdd/` directory
4. Set environment variables for database connection
5. Run the application:
   ```bash
   go run cmd/web/main.go
   ```

### Project Structure

```
voileProject/
├── cmd/web/                    # Main application entry point
│   └── main.go                 # Go web server
├── internal/                   # Private application code
│   ├── database/              # Database connection
│   ├── handlers/              # HTTP request handlers
│   ├── models/                # Data models
│   └── middleware/            # HTTP middleware
├── web/                       # Web assets
│   ├── templates/             # HTML templates
│   └── static/                # Static assets
├── bdd/                       # Database schema and data
├── html/                      # Legacy PHP files and static assets
├── docker-compose.go.yml      # Go application Docker setup
├── docker-compose.yml         # Legacy PHP Docker setup
├── Dockerfile.go              # Go application Dockerfile
└── Dockerfile                 # Legacy PHP Dockerfile
```

### Testing

Run tests:

```bash
go test ./...
```

## Migration from PHP

The following components have been converted:

- [x] **Web Server**: Apache/PHP → Go/Gin
- [x] **Database Layer**: mysqli → database/sql with MySQL driver
- [x] **Authentication**: PHP sessions → Go sessions with secure cookies
- [x] **Templates**: PHP → Go html/template
- [x] **Routing**: PHP files → Go HTTP handlers
- [x] **Admin Functions**: PHP admin pages → Go admin handlers
- [x] **Static Assets**: Served through Go instead of Apache
- [x] **Docker Setup**: Updated for Go application
- [x] **Environment Configuration**: Go-compatible environment handling

## Database Schema

The application uses a complex MySQL schema with the following main entities:

- **Adherents** (Users/Members)
- **Sejours** (Trips/Stays)
- **Stages** (Training Courses)
- **Bateaux** (Boats)
- **Ports** (Harbors)
- **Brevets** (Certifications)
- Relationship tables for permissions, participation, and supervision

## API Endpoints

### Public Routes
- `GET /` - Home page
- `GET /sejour.php` - Trip listing and booking
- `GET /contact.php` - Contact information
- `GET /club.php` - Club information
- `GET /horaires.php` - Schedule information

### Authentication
- `POST /login` - User login
- `POST /signup` - User registration
- `GET /logout` - User logout

### Trip Management
- `POST /sejour` - Search trips
- `POST /inscrire-sejour` - Register for trip
- `POST /desinscrire-sejour` - Unregister from trip

### Admin Routes (Protected)
- `GET /adminpage/users.php` - User management
- `GET /adminpage/sejours.php` - Trip management
- `GET /adminpage/sejour_details.php` - Detailed trip management
- `POST /adminpage/*` - Various admin operations

## Configuration

Environment variables:

- `MYSQL_HOST` - Database host (default: localhost)
- `MYSQL_PORT` - Database port (default: 3306)
- `MYSQL_USER` - Database username
- `MYSQL_PASSWORD` - Database password
- `MYSQL_DATABASE` - Database name
- `PORT` - Application port (default: 8080)

## License

This project maintains the same license as the original PHP version.