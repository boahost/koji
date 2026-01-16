# Base Image do Laravel com PHP, Composer, Node.js e Nginx pré-configurados
FROM bitnamilegacy/laravel:12

# Diretório de trabalho
WORKDIR /var/www/api

# Copia os arquivos da aplicação
COPY . .

# Copia e configura o ambiente
COPY .env.example .env

# Copia o php.ini customizado
COPY ./docs/memory-limit.ini /opt/bitnami/php/etc/conf.d/memory-limit.ini

# Definição de argumentos (opcional, para uso em tempo de build)
ARG DB_HOST
ARG DB_PORT
ARG DB_USERNAME
ARG DB_PASSWORD
ARG DB_DATABASE
ARG ASSET_URL


# Definição de variáveis de ambiente
ENV APP_NAME=Koji
ENV APP_ENV=production
ENV DB_HOST=$DB_HOST
ENV DB_PORT=$DB_PORT
ENV DB_USERNAME=$DB_USERNAME
ENV DB_PASSWORD=$DB_PASSWORD
ENV DB_DATABASE=$DB_DATABASE
ENV ASSET_URL=$ASSET_URL


# Exibe as variáveis (apenas para debug)
RUN printenv

# Instala dependências PHP
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Instala dependências JS (usar apenas um: npm ou yarn — aqui usamos npm)
RUN npm install && npm run build

# Executa comandos Laravel
RUN php artisan key:generate \
    && php artisan migrate --force \
    && php artisan storage:link \
    && chmod -R 777 storage bootstrap/cache

# Expõe a porta para o Laravel
EXPOSE 9010

# Inicializa o servidor Laravel
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=9010"]
