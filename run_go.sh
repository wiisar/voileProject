#!/bin/bash

# Run the Go version of voileProject
echo "Starting Go version of voileProject..."

# Set environment variables for development
export MYSQL_HOST=${MYSQL_HOST:-localhost}
export MYSQL_PORT=${MYSQL_PORT:-3306}
export MYSQL_USER=${MYSQL_USER:-bmanem}
export MYSQL_PASSWORD=${MYSQL_PASSWORD:-jeXja8-septuf-fitsuj}
export MYSQL_DATABASE=${MYSQL_DATABASE:-bmanem}
export PORT=${PORT:-8080}

# Build and run the application
echo "Building application..."
go build -o bin/voile-app cmd/web/main.go

echo "Starting web server on port $PORT..."
./bin/voile-app