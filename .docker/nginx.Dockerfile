FROM nginx:latest
COPY localhost.* /etc/nginx/
COPY default.conf /etc/nginx/conf.d/
