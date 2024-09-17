FROM chatgpt-base

WORKDIR /var/www/chatgpt/

COPY ./docker/dev/vhost.conf /etc/apache2/sites-available/chatgpt.conf
COPY ./docker/dev/php.ini /usr/local/etc/php/conf.d/

RUN a2dissite 000-default
RUN a2ensite chatgpt

RUN apt-get -y update && \
    apt-get -y upgrade && \
    a2ensite chatgpt
