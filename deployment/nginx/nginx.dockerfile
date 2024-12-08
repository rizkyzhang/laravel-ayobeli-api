FROM nginx:1.26.2-alpine

COPY ./deployment/nginx/nginx.conf /etc/nginx/nginx.conf

EXPOSE 80
