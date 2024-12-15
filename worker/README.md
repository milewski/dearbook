# Worker

---

# Local Development Setup Guide

### Step 1: Download / Clone the Project

Clone this repository and navigate into it. If you are on Windows, I recommend using WSL2, or at least utilize Git Bash instead of CMD or PowerShell to execute the next commands along.

### Step 3: Start the Containers

- Build the images and start the containers with:

```shell
cp .env.example .env
docker compose up -d
```

- Ensure correct file permissions for modified files within the container. Set the entire directory's ownership to the user with a UID of 1000:

```shell
chown -R 1000:1000 .
```
> **Note**: This is because the container runs as a non-root user with a UID of 1000.

Make necessary scripts executable:

```shell
chmod +x ./php ./composer ./ollama
```

Install dependencies and prepare framework:

```shell
touch ./database/database.sqlite

./composer install
./php artisan key:generate
./php artisan storage:link
./ollama pull llama3.2:3b
```

> **Note**: The `./` at the beginning of each command is an alias to `docker compose exec php`, allowing you to run commands within the container without entering it.
