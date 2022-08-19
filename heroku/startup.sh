#!/usr/bin/env bash

#===================================================================#
#   Author: luolongfei <luolongf@gmail.com>                         #
#   Intro: https://github.com/luolongfei/freenom                    #
#===================================================================#

set -e

echo '[Info] 检测到你当前在 Heroku 环境运行容器，此环境依赖外部请求触发脚本运行，请自行前往 https://uptimerobot.com 配置心跳任务'
echo '[Info] 由于 Heroku 对 cron 的支持不友好，故当前脚本只能通过请求 url 触发执行'
echo '[Info] 更多部署手顺请参考文档：https://github.com/luolongfei/freenom'

# 替换端口变量
envsubst '\$PORT' < /app/nginx.template.conf > /app/nginx.conf

# 启动 php-fpm 与 nginx
php-fpm -D -R; nginx -c /app/nginx.conf -g 'daemon off;'