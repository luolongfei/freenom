FROM php:8.5.3-cli-alpine3.23

LABEL author="mybsdc <mybsdc@gmail.com>" \
    maintainer="luolongfei <luolongf@gmail.com>"

ENV TZ=Asia/Shanghai

WORKDIR /app

COPY . ./
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN set -eux \
    && apk update \
    && apk add --no-cache tzdata bash \
    && apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install bcmath \
    && apk del .build-deps

# 由于部分环境不支持数据卷 VOLUME 关键字，故不再指定
# VOLUME ["/conf", "/app/logs"]

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && mkdir /conf

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["crond", "-f"]
