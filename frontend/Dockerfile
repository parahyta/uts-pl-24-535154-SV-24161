
# Stage 1: Build Vue application with Node.js
FROM node:22-alpine AS build

WORKDIR /app

# Install dependencies using cached layers
COPY package.json package-lock.json ./

RUN npm ci

# Copy Vue application source code
COPY . .

# Use a relative API path for Docker deployment
ARG VITE_API_BASE_URL=/api

RUN npm run build

# Stage 2: Serve built files using Nginx
FROM nginx:1.27-alpine AS runtime

# Replace default Nginx configuration
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copy only the compiled frontend files
COPY --from=build /app/dist /usr/share/nginx/html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
