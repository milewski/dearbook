FROM node:20-alpine AS builder

WORKDIR /srv

COPY ../package.json .
COPY ../yarn.lock .

RUN yarn --freeze-lock

COPY . .

ARG VITE_API_HOST

RUN VITE_API_HOST=$VITE_API_HOST \
    yarn build

FROM caddy:2.9-alpine

COPY --from=builder /srv/dist /srv
COPY ./docker/Caddyfile /etc/caddy/Caddyfile

EXPOSE 80