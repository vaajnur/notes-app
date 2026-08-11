FROM node:18-alpine AS development

WORKDIR /app
RUN chown node:node /app
USER node

COPY --chown=node:node package.json package-lock.json ./
RUN npm ci
COPY --chown=node:node . .

EXPOSE 5173
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0", "--configLoader", "runner"]

FROM development AS build
RUN npm run build

FROM nginx:1.27-alpine AS production
COPY --from=build /app/public/build /usr/share/nginx/html/build
COPY docker/frontend/nginx.conf /etc/nginx/conf.d/default.conf
EXPOSE 80
HEALTHCHECK --interval=10s --timeout=3s --retries=5 CMD wget -qO- http://127.0.0.1/health || exit 1
