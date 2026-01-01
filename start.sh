#!/bin/bash

echo "Building and starting CRUD application..."

# Build containers
echo "Building containers..."
sudo docker compose build

# Start services
echo "Starting services..."
sudo docker compose up -d

# Wait for services to be ready
echo "Waiting for services to start..."
sleep 10

# Show status
echo ""
echo "Container Status:"
sudo docker compose ps

echo ""
echo "========================================="
echo "Services are running!"
echo "========================================="
echo "Laravel: http://localhost:8000"
echo "phpMyAdmin: http://localhost:8080"
echo "  - Upload limit: 500MB"
echo "MySQL: localhost:3307"
echo "========================================="
