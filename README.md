# Image Search Processor

## Overview

This project is an image processing script designed for full-stack developers proficient in PHP, Laravel, and Vue.js. It allows users to download, resize, and securely store images from Google search results based on specified queries.

## Features

- Image downloading from Google search results.
- Image resizing to predefined dimensions.
- Secure storage of resized images in a PostgreSQL database.
- User-friendly interface for inputting search queries and viewing/download resized images.

## Getting Started

### Prerequisites

- PHP (>= 7.3)
- Composer
- Node.js and npm
- PostgreSQL

### Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/mdhesari/image-search-processor-laravel-vuejs.git
   ```
   
2. Install PHP dependencies:

   ```bash
   composer install
   ```
3. Install Javascript & Vuejs dependencies:

    ```bash
    npm install
    ```
    
4. Configure your environment variables:

    ```bash
    cp .env.example .env
    ```
    
    Please register an account in https://serpapi.com/ and set SERAPI_API_KEY in .env
    
5. Application Key:

    ```bash
    php artisan key:generate
    ```

5. Run Docker Compose:

    ```bash
    docker compose up -d
    ```
    
6. Run migrations:

    ```
    sail artisan migrate
    ```

7. Build the Vue.js frontend:

    ```
    npm run dev
    ```

8. Run Horizon:

    ```
    sail artisan horizon
    ```
