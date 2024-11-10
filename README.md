# DearBook
https://dearbook.fun
<p align="center"><img width="1000" src="frontend/public/cover.png"></p>

This is the main repository for my submission to [The Open Source AI Challenge](https://dev.to/challenges/pgai).

The project is organized into three folders:

- **[Backend](./backend)**: Contains the API, Queue, Database, ComfyUI, and Ollama.
- **[Frontend](./frontend)**: The UI that communicates with the API.
- **[Infrastructure](./infrastructure)**: The Terraform and stack files used to deploy the application on a Docker Swarm cluster.

Each subfolder includes instructions for running the project locally. Setup is straightforward,
as everything has been containerized, running `docker compose up` is all that’s needed.

> [!WARNING]
> You need a good NVIDIA GPU to run this project!!. 

For a more detailed overview, including screenshots, you can read the submission sent to the challenge here: https://dev.to/milewski/dearbook-466m-temp-slug-5505122.
