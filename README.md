# Laravel + Traefik + FrankenPHP

This is a boilerplate that you can copy/paste into any Laravel project that you intend to dockerize. 
It addresses the most basic needs a developer has when running Laravel locally.

### The benefits of this boilerplate include:

- Everything runs through HTTPS with self-signed certificates, providing an environment closely resembling production right on your local machine.
- It does not require installing any development tools on your actual machine (except Docker), avoiding annoyance for developers who prefer to keep their development computer clean.
- The setup instructions serve as an easy-to-follow guide for anyone onboarding to the project.
- It also provides production-ready files to set up the same environment seamlessly and with ease on a production server.

### Tips:

- Pre-configure your `.env.example` file with the correct values so that when other users clone the repository and follow the instructions below, the project works out of the box with zero friction.
- Review the `docker-compose.yml` file and add/remove/update the services according to your own needs.
- Review the Docker file located at `./docker/php/php.x.dockerfile`; it includes all the extensions needed for Laravel to function, but you may require more.
- If your project has seeds that set up default user accounts, also include the default login/password below to make it easier to log in to the application.

You can delete everything above this and input your own instructions specific to your project.

---

# Local Development Setup Guide

### Step 1: Download / Clone the Project

Clone this repository and navigate into it. If you are on Windows, I recommend using WSL2, or at least utilize Git Bash instead of CMD or PowerShell to execute the next commands along.

### Step 2: Download mkcert

Download [mkcert](https://github.com/FiloSottile/mkcert), a tool for generating self-signed SSL certificates. Get the binary from the [release](https://github.com/FiloSottile/mkcert/releases) page.

> **Note**: This is a one-time setup, and you can reuse the generated certificates for any subsequent projects on the same machine.

Execute the following command in your terminal after obtaining the mkcert binary:

```shell
mkcert -install -cert-file ./traefik/tls/cert.pem -key-file ./traefik/tls/key.pem "*.docker.localhost" docker.localhost
```
> **Note**: If you plan to use other domains, simply replace `docker.localhost` with the desired domain. You can add multiple domains to the list as needed. Keep in mind that any domain not ending in `.localhost` will require a manual edit of the hosts file.

> **Note**: If you are on Windows using WSL2, you have to run this command on the Windows side. This is because mkcert needs to install the certificates in your Windows trust store, not on Linux.

### Step 3: Start the Containers

- Build the images and start the containers with:

```shell
docker-compose up -d
```

- Ensure correct file permissions for modified files within the container. Set the entire directory's ownership to the user with a UID of 1000:

```shell
chown -R 1000:1000 .
```
> **Note**: This is because the container runs as a non-root user with a UID of 1000.

Make necessary scripts executable:

```shell
chmod +x ./php ./composer
```

Install dependencies and prepare framework:

```shell
./composer install
./php artisan key:generate
./php artisan migrate:fresh --seed
```

> **Note**: The `./` at the beginning of each command is an alias to `docker compose exec php`, allowing you to run commands within the container without entering it.

You're done! Open https://app.docker.localhost to view application.
