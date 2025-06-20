FROM node:22-alpine

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

# Exposed ports
EXPOSE 3000

# Command to run on start
CMD ["npm", "run", "start"]
