# Backend

The backend was built in PHP using the laravel framework, and it contains several components:

- [Traefik](https://traefik.io/)
- [FrankenPHP](https://frankenphp.dev/) 
- [Laravel](https://laravel.com) ([Reverb](https://reverb.laravel.com/) + [Horizon](https://laravel.com/docs/11.x/horizon))
- [Redis](https://redis.io/)
- [TimescaleDB](https://www.timescale.com/)
- [ComfyUI](https://www.comfy.org/)
- [Ollama](https://ollama.com/)

---

# Local Development Setup Guide

### Step 1: Download / Clone the Project

Clone this repository and navigate into it. If you are on Windows, I recommend using WSL2, or at least utilize Git Bash instead of CMD or PowerShell to execute the next commands along.

### Step 2: Download mkcert

Download [mkcert](https://github.com/FiloSottile/mkcert), a tool for generating self-signed SSL certificates. Get the binary from the [release](https://github.com/FiloSottile/mkcert/releases) page.

Execute the following command in your terminal after obtaining the mkcert binary:

```shell
mkcert -install -cert-file ./traefik/tls/cert.pem -key-file ./traefik/tls/key.pem "*.docker.localhost" docker.localhost
```
> **Note**: If you are on Windows using WSL2, you have to run this command on the Windows side. This is because mkcert needs to install the certificates in your Windows trust store, not on Linux.

### Step 3: Start the Containers

- Create the network for traefik
```shell
docker network create traefik-network
```

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
./composer install
./php artisan key:generate
./php artisan migrate:fresh
./php artisan storage:link
./ollama pull llama3.2:3b
./ollama pull mxbai-embed-large
```

> **Note**: The `./` at the beginning of each command is an alias to `docker compose exec php`, allowing you to run commands within the container without entering it.

You're done! these domains will be available for you:

- Traefik: https://traefik.docker.localhost
- API: https://api.docker.localhost
- Horizon: https://horizon.docker.localhost
- ComfyUI: https://comfyui.docker.localhost
- Static Assets: https://assets.docker.localhost
- WebSocket: wss://reverb.docker.localhost
