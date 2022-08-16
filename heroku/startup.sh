#!/usr/bin/env bash

#===================================================================#
#   Author: luolongfei <luolongf@gmail.com>                         #
#   Intro: https://github.com/luolongfei/freenom                    #
#===================================================================#

set -e

echo '[Info] 检测到你当前在 Heroku 环境运行容器，此环境依赖外部请求触发脚本运行，请自行前往 https://uptimerobot.com 配置心跳任务'
echo '[Info] 更多部署手顺请参考文档：https://github.com/luolongfei/freenom'

php run

# 替换端口变量
envsubst '\$PORT' < /app/nginx.template.conf > /app/nginx.conf

# 启动 nginx
nginx -c /app/nginx.conf -g 'daemon off;'