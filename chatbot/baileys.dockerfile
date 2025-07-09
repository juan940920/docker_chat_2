# Usar la imagen base de Node.js
FROM node:22 AS build-stage

# Establecer directorio de trabajo
WORKDIR /app

# Copiar el archivo package.json y package-lock.json
COPY package*.json ./

# Instalar las dependencias
RUN npm install

# Copiar el resto del código fuente al contenedor
COPY . .

# Exponer el puerto si es necesario (puedes ajustar según tu configuración)
EXPOSE 3001

# Comando para ejecutar la aplicación (ajusta el nombre de archivo según tu entrada principal)
CMD ["node", "src/index.js"]