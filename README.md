# Project Management API

A Laravel-based API for managing users, projects, timesheets, and dynamic project attributes using EAV.

php version should 8.2 or higher
## Setup Instructions

composer install
Configure environment:

cp .env.example .env

Update .env with your database credentials:

DB_CONNECTION=mysql
DB_DATABASE=project-management
DB_USERNAME=root
DB_PASSWORD=

Generate application key:

php artisan key:generate

## If you are using the provided SQL dump (named project-management.sql), which is located in the project directory, you do not need to run fresh migrations because the dump contains the necessary schema and data.

Alternatively, if you prefer to build the database from scratch, run the migrations and seeders:

php artisan migrate --seed

Install Laravel Passport:
php artisan passport:install
php artisan passport:client --personal

Start the server:
php artisan serve

## API Documentation

Authentication
Endpoint        Method  Description          Parameters
/api/register   POST    Register a new user  first_name, last_name, email, password
/api/login      POST    Login and get token  email, password
/api/logout     POST    Revoke current token (Requires authentication)

Projects
Endpoint           Method   Description
/api/projects      GET      List all projects (supports filtering)
/api/projects/{id} GET      Get a project with dynamic attributes
/api/projects      POST     Create a project
/api/projects/{id} PUT      Update a project
/api/projects/{id} DELETE   Delete a project
Filtering Projects:

Use filters for regular attributes:
GET /api/projects?filters[name]=HR&filters[status]=in_progress

Use eav_filters for dynamic attributes:

GET /api/projects?filters[name][operator]=LIKE&filters[name][value]=HR&filters[department][operator]=LIKE&filters[department][value]=IT


                Timesheets
    Endpoint         Method       Description
/api/timesheets      GET        List all timesheets
/api/timesheets/{id} GET        Get a timesheet
/api/timesheets      POST       Create a timesheet
/api/timesheets/{id} PUT        Update a timesheet
/api/timesheets/{id} DELETE     Delete a timesheet

                Attributes (EAV)
Endpoint                Method      Description
/api/attributes         GET         List all attributes
/api/attributes/{id}    GET         Get an attribute
/api/attributes         POST        Create an attribute
/api/attributes/{id}    PUT         Update an attribute
/api/attributes/{id}    DELETE      Delete an attribute

##  Example Requests & Responses

Auth: Register
Request:

curl -X POST http://localhost:8000/api/register \
 -H "Content-Type: application/json" \
 -d '{
"first_name": "John",
"last_name": "Doe",
"email": "john@example.com",
"password": "password123"
}'
Response:
{
    "access_token": {
        "accessToken": "eyJ0eXAiOiJKV1QiLCJ..
        "token": {
            "id": "e4fdd3a07a07c6864be3fea0fe22ae7efe95a97836aa0d4da04e1012ddd43b4330dd022057feb854",
            "user_id": 5,
            "client_id": 1,
            "name": "API Token",
            "scopes": [],
            "revoked": false,
            "created_at": "2025-03-07T16:29:45.000000Z",
            "updated_at": "2025-03-07T16:29:45.000000Z",
            "expires_at": "2026-03-07T16:29:45.000000Z"
        }
    },
    "token_type": "Bearer"
}


Projects: Create with Dynamic Attributes
Request:

curl -X POST http://localhost:8000/api/projects \
 -H "Authorization: Bearer YOUR_TOKEN" \
 -H "Content-Type: application/json" \
 -d '{
"name": "Project A",
"status": "active",
"attributes": {
"1": "IT", // department (attribute_id=1)
"2": "2023-10-01" // start_date (attribute_id=2)
}
}'

Response:
{
"id": 1,
"name": "Project A",
"status": "active",
"created_at": "2023-10-01T12:00:00.000000Z",
"updated_at": "2023-10-01T12:00:00.000000Z",
"attribute_values": [
{
"id": 1,
"attribute_id": 1,
"entity_id": 1,
"value": "IT"
},
{
"id": 2,
"attribute_id": 2,
"entity_id": 1,
"value": "2023-10-01"
}
]
}

## Test Credentials

Email: test@example.com
Password: 12345678

## For Easy Testing A Postman collection is included in the project directory for easy testing.

project-management.postman_collection.json file
