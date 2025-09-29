# Beauty Salon Scheduling API

A Laravel RESTful API for managing a beauty salon's appointment scheduling system.  
Supports listing available slots, booking, and canceling appointments, secured with Bearer Token authentication.

---

## Features
- Specialists (A, B, C) with service capabilities
- Services (Haircut, Hairstyling, Manicure) with different durations
- Working hours: **09:00 – 17:00**
- Time slots aligned to service durations
- RESTful endpoints:
  - **List available slots**
  - **Book an appointment**
  - **Cancel an appointment**
- Secured with Bearer Token authentication

---

## Setup Instructions

### 1. Clone Repository
bash
git clone https://github.com/your-username/beauty-salon-api.git
cd beauty-salon-api

### 2. Install Depedencies
composer install

### 3. Environment Setup
Copy .env.example to .env and update:

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

### 4. Generate API Key
php artisan key:generate

### 5. Run Migrations & Seeders
php artisan migrate:fresh --seed

### 6. Run Application
Use Apache/Nginx with document root pointing to:
public/index.php

## Authentication
Auth Type: Bearer Token
Token: salon123

Add header:
Authorization: Bearer salon123

## API Endpoints

### 1. List available slots
GET http://salon.test/api/slots?specialist_id=1&service_id=1&date=2025-09-26

### 2. Book an Appointment
POST http://salon.test/api/book

JSON:
{
  "specialist_id": 1,
  "service_id": 2,
  "date": "2025-10-10",
  "start_time": "10:00",
  "client_name": "Bill"
}

Success: { "success": true, "appointment_id": {id} }

Error: { "error": "Outside working hours" }

### 3. Cancel an Appointment
DELETE http://salon.test/api/cancel/{id}

Success: { "success": true, "message": "Appointment canceled" }

Error: { "error": "Appointment not found" }


