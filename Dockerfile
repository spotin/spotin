## First stage: Build the application
FROM node:20-alpine as build

# Work directory
WORKDIR /app

# Copy package files
COPY package.json package.json
COPY package-lock.json package-lock.json

# Install dependencies
RUN npm ci --omit=dev

# Copy source files
COPY .nest .nest
COPY prisma prisma
COPY public public
COPY views views

# Environment variables
ENV NODE_ENV=production
ENV SPOT_IN_FQDN=${SPOT_IN_FQDN}
ENV SPOT_IN_ADMIN_EMAIL=${SPOT_IN_ADMIN_EMAIL}
ENV SPOT_IN_ADMIN_PASSWORD=${SPOT_IN_ADMIN_PASSWORD}
ENV SPOT_IN_DATABASE_URL=${SPOT_IN_DATABASE_URL}
ENV SPOT_IN_JWT_SECRET=${SPOT_IN_JWT_SECRET}
ENV SPOT_IN_JWT_EXPIRATION_TIME=${SPOT_IN_JWT_EXPIRATION_TIME}
ENV SPOT_IN_MAIL_HOST=${SPOT_IN_MAIL_HOST}
ENV SPOT_IN_MAIL_PORT=${SPOT_IN_MAIL_PORT}
ENV SPOT_IN_MAIL_USER=${SPOT_IN_MAIL_USER}
ENV SPOT_IN_MAIL_PASS=${SPOT_IN_MAIL_PASS}
ENV SPOT_IN_MAIL_SECURE=${SPOT_IN_MAIL_SECURE}
ENV SPOT_IN_MAIL_SENDER_NAME=${SPOT_IN_MAIL_SENDER_NAME}

# Exposed ports
EXPOSE 3000

# Command to run on start
CMD ["npm", "run", "start"]
