ARG NGINX_VERSION

FROM nginx:${NGINX_VERSION} as nginx

USER root

RUN rm /etc/nginx/conf.d/default.conf

COPY . /var/www
COPY ./docker-compose/nginx/local.conf /etc/nginx/conf.d
