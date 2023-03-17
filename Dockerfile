## First stage: Build the application
FROM node:18-alpine as build

# Work directory
WORKDIR /app

# Install dependencies
COPY package.json package.json
COPY package-lock.json package-lock.json
RUN npm ci

# Copy source code
COPY . .

# Build the application
RUN npm run build

## Second stage: Create the production image
FROM node:18-alpine as production

# Work directory
WORKDIR /app

# Install production dependencies
COPY package.json package.json
COPY package-lock.json package-lock.json
RUN npm ci --omit=dev

# Copy built application from stage 1
COPY --from=build /app/dist ./dist

COPY views views
COPY public public

# Set environment variables
ENV DATABASE_URL=${DATABASE_URL}

# Exposed ports
EXPOSE 3000

# Command to run on start
CMD ["npm", "run", "start"]
