# Server setup for the pgai and Ollama Dev Challenge

This repository contains a basic Terraform configuration for setting up this project on Vultr. 
While this is primarily for my own reference, feel free to use these files if you’re interested in hosting the project online.

### Server instructions

The commands are self-explanatory:

```shell
mkdir -p /srv/traefik /srv/traefik/.certificates /srv/traefik/config /srv/traefik/log
```

```shell
# append { "default-runtime": "nvidia" } to:
nano /etc/docker/daemon.json

# uncomment
# swarm-resource = "DOCKER_RESOURCE_GPU”
nano /etc/nvidia-container-runtime/config.toml

# Then restart docker
restart sudo systemctl restart docker
```

## Open the following ports

```shell
sudo ufw allow 2377/tcp
sudo ufw allow 7946/tcp
sudo ufw allow 7946/udp
sudo ufw allow 4789/udp
```
Copy [docker-compose.stack.yml](docker-compose.stack.yml) and [.env.example](.env.example) as `.env` to the server

And start the stack

```shell
docker swarm init
docker network create traefik-network -d overlay
docker login https://ewr.vultrcr.com/dearbook001 -u XXXX -p XXX

env $(cat .env | grep ^[A-Z] | xargs) docker stack deploy -c ./docker-compose.stack.yml dearbook --with-registry-auth

docker exec -it dearbook_ollama.* ollama pull llama3.1:8b
docker exec -it dearbook_ollama.* ollama pull mxbai-embed-large
docker exec -it dearbook_backend.* php artisan migrate
```

Done.