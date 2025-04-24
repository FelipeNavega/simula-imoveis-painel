FROM node:22-alpine as build

RUN apk upgrade && apk add git

RUN git clone https://github.com/ollama-webui/ollama-webui-lite.git app

WORKDIR /app

RUN npm ci && npm run build

COPY server.js build

#FROM node:lts-alpine AS production
#COPY --from=build /app/build .
#COPY --from=build /app/node_modules .
#
#EXPOSE 3000
#CMD ["node", "."]
