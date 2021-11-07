FROM node:16

# Create app directory
WORKDIR /usr/src/app

# Bundling the app source
COPY . .

# Building the code for production
RUN npm ci --only=production

EXPOSE 3000

CMD [ "node", "bin/www" ]