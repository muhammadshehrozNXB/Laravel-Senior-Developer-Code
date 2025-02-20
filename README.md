Translation API Service

This repository contains a scalable and efficient Translation API service. The API allows you to store, update, retrieve, and search translations across multiple locales. It supports adding new languages, tagging translations for context, and exporting translations in JSON format. The service is designed to meet high-performance standards, with response times under 200ms for all endpoints.

Table of Contents

    Features
    Performance Requirements
    Technical Requirements
    Setup Instructions
    Design Choices
    Testing
    API Documentation
    
    
Features

    Store Translations: Store translations for multiple locales (e.g., en, fr, es) with the ability to add new languages in the future.
    Tag Translations: Tag translations for specific contexts like mobile, desktop, web, etc.
    CRUD Endpoints:
        Create, update, view, and search translations by keys, tags, or content.
        Search translations based on key, value, and tags for better context and flexibility.
    JSON Export Endpoint: Expose a JSON endpoint to supply translations for frontend applications like Vue.js. This endpoint will always return updated translations.
    Scalability: Command or factory to populate the database with 100k+ records to test scalability.
    
Performance Requirements

    Response Time: All endpoints should return a response within 200ms.
    JSON Export Endpoint: Should handle large datasets and return responses in less than 500ms.
    Efficient Database: Optimized SQL queries to ensure the API can scale under large data loads.


Technical Requirements

    PSR-12 Compliance: Code should follow PSR-12 coding standards.
    SOLID Design Principles: The application must adhere to SOLID principles for maintainability and scalability.
    Token-Based Authentication: The API will be secured with token-based authentication.
    No External Libraries for CRUD: Custom CRUD implementations and translation services will be used without relying on external libraries.
    
    Setup Instructions
    Prerequisites
    
        PHP 8.x or above
        Composer: For managing dependencies.
        MySQL or PostgreSQL Database: Ensure that the database server is up and running.
        
        
Install Dependencies

1: Clone this repository:

    git clone https://github.com/muhammadshehrozNXB/Laravel-Senior-Developer-Code.git
    cd translation-api

2: Install the dependencies:

    composer install
    
Database Setup

1: Create a database for the application.

2: Update your .env file with the correct database configuration.

3: Run the database migrations:
    
    php artisan migrate
    
1: Running the Application
    
     php artisan serve
     
Command to Populate Database with 100k+ Records

    php artisan db:seed
    
Authentication

The API uses token-based authentication. To interact with the protected endpoints, you'll need to generate an authorization token. You can do this by sending a POST request to the login endpoint.
Steps to Generate the Auth Token

Send a POST request to http://127.0.0.1:8000/api/login with the following body parameters:
        
        Email: testuser@example.com
        Password: password123

You will receive a response containing a token. Use this token for authenticating your subsequent requests.

Example response:
    
    {
        "token": "your_generated_token_here"
    }
Use the token in your request headers by including it as a Bearer token for authentication:

    Authorization: Bearer your_generated_token_here
    
You can now use this token to access the API endpoints that require authentication.

Design Choices

    Database Schema: The schema has been designed with scalability in mind, using efficient indexing and optimized queries to ensure fast access to translation data.

    API Structure: The API is designed following RESTful principles to ensure clarity and consistency in endpoint naming and actions. Each translation is identified by a key and locale, and tagged with contextual information.

    Performance Optimization: SQL queries are optimized to ensure fast data retrieval, particularly when handling large datasets. The JSON export endpoint ensures that data can be retrieved quickly and efficiently, using caching mechanisms when needed.
    
Testing

The service is equipped with unit and feature tests to ensure that all critical functionalities are working correctly. This includes testing for performance (response times under load) and ensuring the API responds as expected.
Running the Tests

To run the tests, use the following command:

    php artisan test
    
API Documentation

The API endpoints are documented using Swagger specification. You can view the full API documentation by navigating to the following URL after starting the application:

    http://localhost:8000/api/documentation

    

    


