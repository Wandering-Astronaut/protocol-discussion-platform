FROM node:20-alpine

WORKDIR /app

# Copy package files
COPY package.json package-lock.json* ./

# Install dependencies
RUN npm install --legacy-peer-deps

# Copy source
COPY . .

EXPOSE 3000

# Run dev server (hot reload works in container)
CMD ["npm", "run", "dev"]
