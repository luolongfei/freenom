#!/usr/bin/env bash

#===================================================================#
#   Author: luolongfei <luolongf@gmail.com>                         #
#   Intro: https://github.com/luolongfei/freenom                    #
#===================================================================#

# 自定义颜色变量
red='\033[0;31m'
green='\033[0;32m'
yellow='\033[0;33m'
plain='\033[0m'

# 生成配置文件
if [ ! -f /conf/.env ]; then
    cp /app/.env.example /conf/.env && echo -e "[${green}Info${plain}] 已生成 .env 文件，请将 .env 文件中的配置项改为你自己的，然后重启容器，如果当前环境非普通 VPS，可忽略此提示" || echo -e "[${yellow}Warn${plain}] 未能正常生成 .env 文件"
fi
if [ ! -f /app/.env ]; then
    ln -s /conf/.env /app/.env || echo -e "[${yellow}Warn${plain}] 未能正常创建 .env 文件链接"
fi

# PHP 命令
PHP_COMMAND='/usr/local/bin/php /app/run > /app/logs/freenom_cron.log 2>&1'

# 指定脚本执行时间
if [ -z "${RUN_AT}" ]; then
    minute=$(shuf -i 0-59 -n 1)
    hour=$(shuf -i 6-23 -n 1)
    CRON_COMMAND="${minute} ${hour} * * * ${PHP_COMMAND}"
    echo -e "[${green}Info${plain}] 已自动指定执行时间，续期任务将在北京时间每天 「${hour}:${minute}」 执行"
    echo -e "[${green}Info${plain}] 在没有手动指定 RUN_AT 环境变量的情况下，每次重启容器，程序都会重新在 06 ~ 23 点全时段中自动随机指定一个执行时间，目的是防止很多人在同一个时间点执行任务导致 Freenom 无法稳定提供服务"
else
    if [[ "${RUN_AT}" =~ ^([01][0-9]|2[0-3]|[0-9]):([0-5][0-9]|[0-9])$ ]]; then
        minute=$(echo ${RUN_AT} | egrep -o '([0-5][0-9]|[0-9])$')
        hour=$(echo ${RUN_AT} | egrep -o '^([01][0-9]|2[0-3]|[0-9])')
        CRON_COMMAND="${minute} ${hour} * * * ${PHP_COMMAND}"
        echo -e "[${green}Info${plain}] 你已指定执行时间，续期任务将在北京时间每天 「${hour}:${minute}」 执行"
    else
        php /app/run -c=Cron -m=verify --cron_exp="${RUN_AT}"
        if [ $? -eq 0 ]; then
            CRON_COMMAND="${RUN_AT} ${PHP_COMMAND}"
            echo -e "[${green}Info${plain}] 你自定义的 Cron 表达式为「${RUN_AT}」，已通过正则验证"
        else
            echo -e "[${red}Error${plain}] RUN_AT 的值无效，你的输入为 ${RUN_AT}"
            echo -e "${yellow}请输入一个有效的时间指令，其值可以为时分格式，如：11:24，也可以为 Cron 表达式，如：'24 11 * * *'，甚至可以不输入，让程序自动生成，推荐采用自动生成的方式，不建议手动指定此环境变量"
            exit 1
        fi
    fi
fi

# 添加计划任务
sed -i '/freenom_cron/'d /etc/crontabs/root
echo -e "${CRON_COMMAND}" >>/etc/crontabs/root

echo -e "[${green}Info${plain}] 计划任务：${CRON_COMMAND}"

php /app/run

exec "$@"
