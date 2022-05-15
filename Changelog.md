### 📰 所有更新日志

此处包含了自脚本发布以来的所有更新日志。以前的日志只记录了比较大的变更，以后的日志会尽可能详尽一些。

#### [v0.5](https://github.com/luolongfei/freenom/releases/tag/v0.5) - 2022-05-15

- 增加支持 华为云函数、Railway 等部署方式
- 支持在消息中显示服务器信息，该功能默认关闭
- 优化部分代码逻辑

#### [v0.4.5](https://github.com/luolongfei/freenom/releases/tag/v0.4.5) - 2022-02-26

- 支持多语言，中英文切换
- 支持自建 Telegram 反代地址 [@Mattraks](https://github.com/Mattraks)
- 更新各种依赖库，PHP 版本最低要求不低于 7.3

#### [v0.4.4](https://github.com/luolongfei/freenom/releases/tag/v0.4.4) - 2021-12-14

- 改进与 Cron 表达式验证相关的正则，兼容各种花里胡哨的表达式
- 支持自动从 Bark url 中提取有效的 Bark key
- 支持通过 阿里云函数 部署

#### [v0.4.3](https://github.com/luolongfei/freenom/releases/tag/v0.4.3) - 2021-11-07

- 增加了 企业微信 / Server 酱 / Bark 等送信方式
- Telegram Bot 支持使用代理，应对国内网络环境问题
- Freenom 账户支持使用代理，应对国内网络环境问题
- 支持检测新版，有新版本可用时能第一时间收到通知
- 支持自动热更新 .env 文件内容，免去每次更新后手动复制配置的繁琐步骤
- 重构了核心续期代码
- 重构了送信模块
- 简化 .env 文件中的配置项

#### [v0.3](https://github.com/luolongfei/freenom/releases/tag/v0.3) - 2021-05-27

- 追加 Docker 版本，支持通过 Docker 方式部署，简化部署流程

#### [v0.2.5](#) - 2020-06-23

- 支持在 Github Actions 上执行（应 GitHub 官方要求，已移除此功能）

#### [v0.2.2](#) - 2020-02-06

- 新增通过 Telegram bot 送信
- 各种送信方式支持单独开关

#### [v0.2](#) - 2020-02-01

- 支持多个 Freenom 账户进行域名续期
- 进行了彻底的重构，框架化
- 优化邮箱模块，支持自动选择合适的邮箱配置

*（版本在 v0.1 到 v0.2 期间代码有过很多次变更，之前没有发布版本，故此处不再赘述相关变更日志）*

#### [v0.1](#) - 2018-8-13

- 初版，开源，基础的续期功能