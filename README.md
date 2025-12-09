# Project Setup

This project consists of a **Laravel backend** and a **Vue.js frontend**. Follow the instructions below to set up the project locally.

---

## 1. Backend Setup (Laravel)

1. Navigate to the backend folder:
    ```bash
    cd backend
    ```

2. Install PHP dependencies:
    ```bash
    composer install
    ```

3. Copy the example environment file and generate an app key:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. Run database migrations:
    ```bash
    php artisan migrate
    ```

5. Start the Laravel development server:
    ```bash
    php artisan serve
    ```

The backend will run at: [http://localhost:8000](http://localhost:8000)

---

## 2. Frontend Setup (Vue.js)

1. Navigate to the frontend folder:
    ```bash
    cd frontend
    ```

2. Install Node.js dependencies:
    ```bash
    npm install
    ```

3. Start the Vue.js development server:
    ```bash
    npm run serve
    ```

The frontend will run at: [http://localhost:8080](http://localhost:8080)

---

## Project Structure

iNiLabs-Laravel-Vue-Products/
├── backend/ # Laravel project
│ ├── app/
│ ├── routes/
│ ├── database/
│ ├── views/app.blade.php
│ ├── resourses/js/
├── README.md
└── .gitignore


## Usage

1. Open the frontend in your browser.
2. Register a new user or login.
3. Add new products with name, price, description, and image.
4. Edit or delete existing products.
5. View all products in a responsive product list.

---

## Notes

- Make sure your backend database is properly configured in the `.env` file.
- This project uses Laravel 12 and Vue 3.