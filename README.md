# Star Wars App

This project is a full-stack application with a React frontend, Laravel backend, Redis for cache and using Docker for local development and testing.

https://swapi.dev/ wasn't serving a secure connection, so this project is currently targetting https://swapi.py4e.com/api/ which is unstable and sometimes returns empty responses. The Cache system implemented in the backend must improve its usability, but occasional delay or empty responses might impact api performance.

## Getting Started

1. **Clone the repository:**
   ```sh
   git clone https://github.com/taper1513/starwars-app.git
   
   cd starwarsapp
   ```

2. **Build and start all containers:**
   ```sh
   docker-compose up --build
   ```
   This will build and start all services defined in the `docker-compose.yml` file.

---

## API Documentation (Swagger / OpenAPI)

- **Swagger UI is automatically generated for the backend API.**
- **Access the documentation at:**
  - [http://localhost:8000/api/documentation](http://localhost:8000/api/documentation)
- **Features:**
  - Interactive documentation for all API endpoints (search, people, movies, stats)
  - Try out API requests directly from the browser
  - View request/response formats, parameters, and error codes
- **How it works:**
  - The backend uses [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger) to generate OpenAPI docs from PHP annotations in the controllers.
  - Docs are automatically updated when you rebuild the containers or change the backend code.

---

## Containers Overview

### 1. **app**
- **Purpose:** Runs the Laravel backend API server.
- **Exposes:** http://localhost:8000
- **Features:**
  - Serves API endpoints for Star Wars data.
  - Generates and serves Swagger API documentation.
  - Handles all backend logic, caching, and storage.

### 2. **frontend**
- **Purpose:** Runs the React frontend application.
- **Exposes:** http://localhost:3000
- **Features:**
  - User interface for searching and viewing Star Wars data.
  - Communicates with the backend API.

### 3. **queue**
- **Purpose:** Runs Laravel's queue worker for background jobs.
- **Features:**
  - Processes queued jobs such as event listeners and statistics updates.

### 4. **cachewarmer**
- **Purpose:** Warms up the backend cache by pre-fetching data from SWAPI.
- **Features:**
  - Fetches and caches people and movies data from the Star Wars API.
  - Helps improve API response times for initial requests.

### 5. **scheduler**
- **Purpose:** Runs scheduled tasks (cron jobs) for the backend.
- **Features:**
  - Executes scheduled Laravel commands, such as updating statistics.

### 6. **redis**
- **Purpose:** Provides a Redis instance for caching and queue management.
- **Exposes:** port 6379
- **Features:**
  - Used by the backend for cache and queue storage.

### 7. **api-tests**
- **Purpose:** Runs backend (Laravel) automated tests.
- **Features:**
  - Executes PHPUnit tests for the backend codebase.

### 8. **frontend-tests**
- **Purpose:** Runs frontend (React) automated tests.
- **Features:**
  - Executes Jest and React Testing Library tests for the frontend codebase.

---

## Stats Endpoint
   - Stats Endpoint should be accessed via http://localhost:8000/api/stats
   - They're updated via events, queue and job every 5 minutes.
   - Each search via search endpoint triggers an event, registered by a    Listener in Cache
   - Every 5 minutes a Job is dispatched via Cron and Laravel Command to update the Statistics 



## Useful Commands

- **Start all containers:**
  ```sh
  docker-compose up --build
  ```
- **Stop all containers:**
  ```sh
  docker-compose down
  ```
- **Run only backend tests:**
  ```sh
  docker-compose run --rm api-tests
  ```
- **Run only frontend tests:**
  ```sh
  docker-compose run --rm frontend-tests
  ```

---

docker-compose up --build will start all containers and you will be able to see their logs by color.

For more details, see the `docker-compose.yml` file and the `Dockerfile`'s in each subdirectory. 