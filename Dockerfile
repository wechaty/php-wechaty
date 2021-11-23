FROM yiluxiangbei/centos-php:php74
LABEL maintainer="Peter Zhang (张春生) <zhangchunsheng423@gmail.com>"

ENV PHP_WECHATY_DOCKER  1

WORKDIR /php-wechaty

COPY docker/composer.json .
COPY . .

RUN mkdir /var/log/wechaty

RUN composer config repo.packagist composer https://mirrors.aliyun.com/composer/
# https://packagist.org/packages/wechaty/php-wechaty
RUN composer install

RUN chmod +x /php-wechaty/bin/entrypoint.sh

WORKDIR /bot

ENTRYPOINT  [ "/php-wechaty/bin/entrypoint.sh" ]
CMD        [ "" ]

#
# https://docs.docker.com/docker-cloud/builds/advanced/
# http://label-schema.org/rc1/
#
LABEL \
  org.label-schema.license="Apache-2.0" \
  org.label-schema.build-date="$(date -u +'%Y-%m-%dT%H:%M:%SZ')" \
  org.label-schema.version="$DOCKER_TAG" \
  org.label-schema.schema-version="$(php-wechaty-version)" \
  org.label-schema.name="PHP-Wechaty" \
  org.label-schema.description="PHP-Wechat for Bot" \
  org.label-schema.usage="https://github.com/wechaty/php-wechaty/wiki/Docker" \
  org.label-schema.url="https://github.com/zhangchunsheng" \
  org.label-schema.vendor="Yunqiic" \
  org.label-schema.vcs-ref="$SOURCE_COMMIT" \
  org.label-schema.vcs-url="https://github.com/wechaty/php-wechaty" \
  org.label-schema.docker.cmd="docker run -ti --rm yiluxiangbei/php-wechaty <code.php>" \
  org.label-schema.docker.cmd.test="docker run -ti --rm yiluxiangbei/php-wechaty test" \
  org.label-schema.docker.cmd.help="docker run -ti --rm yiluxiangbei/php-wechaty help" \
  org.label-schema.docker.params="WECHATY_PUPPET_HOSTIE_TOKEN=token token from https://wechaty.js.org/docs/puppet-services/diy, PHP-WECHATY_LOG=verbose Set Verbose Log, TZ='Asia/Shanghai' TimeZone"