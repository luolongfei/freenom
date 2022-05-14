FROM php:7.4.19-alpine3.13

LABEL author="mybsdc <mybsdc@gmail.com>" \
    maintainer="luolongfei <luolongf@gmail.com>"

ENV TZ Asia/Shanghai

WORKDIR /app

COPY . ./

RUN set -eux \
    && apk update \
    && apk add --no-cache tzdata bash

VOLUME ["/conf", "/app/logs"]

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["crond", "-f"]
