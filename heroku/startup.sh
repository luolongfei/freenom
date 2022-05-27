#!/usr/bin/env bash

#===================================================================#
#   Author: luolongfei <luolongf@gmail.com>                         #
#   Intro: https://github.com/luolongfei/freenom                    #
#===================================================================#

set -e

echo '[Info] 检测到你当前在 Heroku 环境运行容器'

# PHP 命令
PHP_COMMAND='php /app/run > /app/logs/freenom_cron.log 2>&1'

# 指定脚本执行时间
if [ -z "${RUN_AT}" ]; then
    minute=$( shuf -i 0-59 -n 1 )
    hour=$( shuf -i 6-23 -n 1 )
    CRON_COMMAND="${minute} ${hour} * * * ${PHP_COMMAND}"
    echo -e "[Info] 已自动指定执行时间，续期任务将在北京时间每天 「${hour}:${minute}」 执行"
    echo -e "[Info] 在没有手动指定 RUN_AT 环境变量的情况下，每次重启容器，程序都会重新在 06 ~ 23 点全时段中自动随机指定一个执行时间，目的是防止很多人在同一个时间点执行任务导致 Freenom 无法稳定提供服务"
else
    if [[ "${RUN_AT}" =~ ^([01][0-9]|2[0-3]|[0-9]):([0-5][0-9]|[0-9])$ ]]; then
        minute=$( echo ${RUN_AT} | egrep -o '([0-5][0-9]|[0-9])$' )
        hour=$( echo ${RUN_AT} | egrep -o '^([01][0-9]|2[0-3]|[0-9])' )
        CRON_COMMAND="${minute} ${hour} * * * ${PHP_COMMAND}"
        echo -e "[Info] 你已指定执行时间，续期任务将在北京时间每天 「${hour}:${minute}」 执行"
    elif [ "$(php /app/run -c=Cron -m=verify --cron_exp="${RUN_AT}")" -eq 1 ]; then
        CRON_COMMAND="${RUN_AT} ${PHP_COMMAND}"
        echo -e "[Info] 你自定义的 Cron 表达式为「${RUN_AT}」，已通过正则验证"
    else
        echo -e "[Error] RUN_AT 的值无效"
        echo -e "请设置一个有效的时间指令，其值可以为时分格式，如：11:24，也可以为 Cron 表达式，如：'24 11 * * *'，甚至可以不输入，让程序自动生成，推荐采用自动生成的方式，不建议手动指定此环境变量"
        exit 1
    fi
fi

# 添加计划任务
sed -i '/freenom_cron/'d /etc/crontabs/root
echo -e "${CRON_COMMAND}" >> /etc/crontabs/root

echo -e "[Info] 计划任务：${CRON_COMMAND}"

php run

# 替换端口变量
envsubst '\$PORT' < /app/nginx.template.conf > /app/nginx.conf

# 启动 nginx
nginx -c /app/nginx.conf -g 'daemon off;'