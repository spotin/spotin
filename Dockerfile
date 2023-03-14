# First stage: Build the application
FROM node:18-alpine as build

WORKDIR /app

# Install dependencies
COPY package.json package.json
COPY package-lock.json package-lock.json
RUN npm install

# Copy source code
COPY . .

# Build the application
RUN npm run build

# Second stage: Create the production image
FROM node:18-alpine as production

WORKDIR /app

# Install production dependencies
COPY package.json package.json
COPY package-lock.json package-lock.json
RUN npm install --production

# Copy built application from stage 1
COPY --from=build /app/dist ./dist

COPY views views
COPY public public

# Set environment variables
ENV DATABASE_URL=${DATABASE_URL}

EXPOSE 3000

CMD ["npm", "run", "start"]
