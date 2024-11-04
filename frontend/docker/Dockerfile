FROM node:20-alpine AS builder

WORKDIR /srv

COPY ../package.json .
COPY ../yarn.lock .

RUN yarn --freeze-lock

COPY . .

ARG VITE_REVERB_APP_KEY
ARG VITE_REVERB_HOST
ARG VITE_API_HOST

RUN VITE_REVERB_APP_KEY=$VITE_REVERB_APP_KEY \
    VITE_REVERB_HOST=$VITE_REVERB_HOST \
    VITE_API_HOST=$VITE_API_HOST \
    yarn build

FROM caddy:2.8.4-alpine

COPY --from=builder /srv/dist /srv
COPY ./docker/Caddyfile /etc/caddy/Caddyfile

EXPOSE 80