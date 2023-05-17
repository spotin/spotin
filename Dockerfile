## First stage: Build the application
FROM node:20-alpine as build

# Work directory
WORKDIR /app

# Copy package files
COPY package.json package.json
COPY package-lock.json package-lock.json

# Install dependencies
RUN npm ci

# Copy source files
COPY prisma prisma
COPY public public
COPY src src
COPY views views
COPY nest-cli.json nest-cli.json
COPY tsconfig.build.json tsconfig.build.json
COPY tsconfig.json tsconfig.json

# Build the application
RUN npm run build

## Second stage: Create the production image
FROM node:20-alpine as production

# Work directory
WORKDIR /app

# Copy package files
COPY --from=build /app/package.json package.json
COPY --from=build /app/package-lock.json package-lock.json

# Install production dependencies
RUN npm ci --omit=dev

# Copy built application from stage 1
COPY --from=build /app/.nest .nest
COPY --from=build /app/prisma prisma
COPY --from=build /app/public public
COPY --from=build /app/views views

# Environment variables
ENV NODE_ENV=production
ENV SPOT_IN_FQDN=${SPOT_IN_FQDN}
ENV SPOT_IN_ADMIN_EMAIL=${SPOT_IN_ADMIN_EMAIL}
ENV SPOT_IN_ADMIN_PASSWORD=${SPOT_IN_ADMIN_PASSWORD}
ENV SPOT_IN_JWT_SECRET=${SPOT_IN_JWT_SECRET}
ENV SPOT_IN_JWT_EXPIRATION_TIME=${SPOT_IN_JWT_EXPIRATION_TIME}
ENV SPOT_IN_DATABASE_URL=${SPOT_IN_DATABASE_URL}

# Exposed ports
EXPOSE 3000

# Command to run on start
CMD ["npm", "run", "start"]
