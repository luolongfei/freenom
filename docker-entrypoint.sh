#!/usr/bin/env bash

#===================================================================#
#   Author: mybsdc <mybsdc@gmail.com>                               #
#   Intro: https://github.com/luolongfei/freenom                    #
#===================================================================#

set -e

# 自定义颜色变量
red='\033[0;31m'
green='\033[0;32m'
yellow='\033[0;33m'
plain='\033[0m'

# 生成配置文件
if [ ! -f /conf/.env ]; then
    cp /app/.env.example /conf/.env
    echo -e "[${green}Info${plain}] 已生成 .env 文件，请将 .env 文件中的配置项改为你自己的，然后重启容器"
fi
if [ ! -f /app/.env ]; then
    ln -s /conf/.env /app/.env
fi

# PHP 命令
PHP_COMMAND='php /app/run > /app/logs/freenom_cron.log 2>&1'

# 指定脚本执行时间
if [ -z "${RUN_AT}" ]; then
    minute=$( shuf -i 0-59 -n 1 )
    hour=$( shuf -i 6-23 -n 1 )
    CRON_COMMAND="${minute} ${hour} * * * ${PHP_COMMAND}"
    echo -e "[${green}Info${plain}] 已自动指定执行时间，续期任务将在北京时间每天 「${hour}:${minute}」 执行"
    echo -e "[${green}Info${plain}] 在没有手动指定 RUN_AT 环境变量的情况下，每次重启容器，程序都会重新在 06 ~ 23 点全时段中自动随机指定一个执行时间，目的是防止很多人在同一个时间点执行任务导致 Freenom 无法稳定提供服务"
else
    if [[ "${RUN_AT}" =~ ^([01][0-9]|2[0-3]|[0-9]):([0-5][0-9]|[0-9])$ ]]; then
        minute=$( echo ${RUN_AT} | egrep -o '([0-5][0-9]|[0-9])$' )
        hour=$( echo ${RUN_AT} | egrep -o '^([01][0-9]|2[0-3]|[0-9])' )
        CRON_COMMAND="${minute} ${hour} * * * ${PHP_COMMAND}"
        echo -e "[${green}Info${plain}] 你已指定执行时间，续期任务将在北京时间每天 「${hour}:${minute}」 执行"
    elif [[ "${RUN_AT}" =~ ^([0-9\/*-]+( |$)){5}$ ]]; then
        CRON_COMMAND="${RUN_AT} ${PHP_COMMAND}"
    else
        echo -e "[${red}Error${plain}] RUN_AT 的值无效"
        echo -e "${yellow}请输入一个有效的时间指令，其值可以为时分格式，如：11:24，也可以为 CRON 命令中的时间格式，如：'24 11 * * *'，甚至可以不输入，让程序自动生成，推荐采用自动生成的方式，不建议手动指定此环境变量"
        exit 1
    fi
fi

# 添加计划任务
sed -i '/freenom_cron/'d /etc/crontabs/root
echo -e "${CRON_COMMAND}" >> /etc/crontabs/root

echo -e "[${green}Info${plain}] CRON_COMMAND: ${CRON_COMMAND}"

php run

exec "$@"
