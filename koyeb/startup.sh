#!/usr/bin/env bash

#===================================================================#
#   Author: luolongfei <luolongf@gmail.com>                         #
#   Intro: https://github.com/luolongfei/freenom                    #
#===================================================================#

set -e

# 自定义颜色变量
red='\033[0;31m'
green='\033[0;32m'
yellow='\033[0;33m'
plain='\033[0m'

echo -e "[${green}Info${plain}] 项目地址：https://github.com/luolongfei/freenom"
echo -e "[${green}Info${plain}] 洛阳亲友如相问，一片冰心在玉壶。 by luolongfei"

# PHP 命令
PHP_COMMAND='/usr/local/bin/php /app/run > /app/logs/freenom_cron.log 2>&1'

# 指定脚本执行时间
if [ -z "${RUN_AT}" ]; then
    minute=$( shuf -i 0-59 -n 1 )
    hour=$( shuf -i 6-23 -n 1 )
    CRON_COMMAND="${minute} ${hour} * * * ${PHP_COMMAND}"
    echo -e "[${green}Info${plain}] 已自动指定执行时间，续期任务将在北京时间每天 「${hour}:${minute}」 执行"
    echo -e "[${green}Info${plain}] 在没有手动指定 RUN_AT 环境变量的情况下，每次重建容器，程序都会重新在 06 ~ 23 点全时段中自动随机指定一个执行时间，目的是防止很多人在同一个时间点执行任务导致 Freenom 无法稳定提供服务"
else
    if [[ "${RUN_AT}" =~ ^([01][0-9]|2[0-3]|[0-9]):([0-5][0-9]|[0-9])$ ]]; then
        minute=$( echo ${RUN_AT} | egrep -o '([0-5][0-9]|[0-9])$' )
        hour=$( echo ${RUN_AT} | egrep -o '^([01][0-9]|2[0-3]|[0-9])' )
        CRON_COMMAND="${minute} ${hour} * * * ${PHP_COMMAND}"
        echo -e "[${green}Info${plain}] 你已指定执行时间，续期任务将在北京时间每天 「${hour}:${minute}」 执行"
    elif [ "$(php /app/run -c=Cron -m=verify --cron_exp="${RUN_AT}")" -eq 1 ]; then
        CRON_COMMAND="${RUN_AT} ${PHP_COMMAND}"
        echo -e "[${green}Info${plain}] 你自定义的 Cron 表达式为「${RUN_AT}」，已通过正则验证"
    else
        echo -e "[${red}Error${plain}] RUN_AT 的值无效"
        echo -e "${yellow}请输入一个有效的时间指令，其值可以为时分格式，如：11:24，也可以为 Cron 表达式，如：'24 11 * * *'，甚至可以不输入，让程序自动生成，推荐采用自动生成的方式，不建议手动指定此环境变量"
        exit 1
    fi
fi

# 添加计划任务
sed -i '/freenom_cron/'d /etc/crontabs/root
echo -e "${CRON_COMMAND}" >> /etc/crontabs/root
echo -e "[${green}Info${plain}] 计划任务：${CRON_COMMAND}"

# 启动 Cron
/usr/sbin/crond

# nginx 配置
cp /app/nginx.template.conf /app/nginx.conf

# 启动 php-fpm 与 nginx
php-fpm -D -R; nginx -c /app/nginx.conf -g 'daemon off;'
