<div align="center">
<h1>Freenom：freenom域名自动续期</h1>

[![Build Status](https://img.shields.io/badge/build-passed-brightgreen?style=for-the-badge)](https://scrutinizer-ci.com/g/luolongfei/freenom/build-status/master)
[![Php Version](https://img.shields.io/badge/php-%3E=7.3-brightgreen.svg?style=for-the-badge)](https://secure.php.net/)
[![Scrutinizer Code Quality](https://img.shields.io/badge/scrutinizer-9.31-brightgreen?style=for-the-badge)](https://scrutinizer-ci.com/g/luolongfei/freenom/?branch=master)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge)](https://github.com/luolongfei/freenom/blob/main/LICENSE)

Documentation: [English version](https://github.com/luolongfei/freenom/blob/main/README_EN.md) | 中文版
</div>

[📢 注意](#-注意)

[📃 引言](#-引言)

[🍭 效果](#-效果)

[🎁 事前准备](#-事前准备)

[📪 配置送信功能](#-配置送信功能)（支持 邮件送信 / Telegram Bot / 企业微信 / Server 酱 / Bark 等送信方式）

[⛵ 通过 Docker 方式部署](#-方式一通过-docker-部署推荐最简单的部署方式)（推荐，最简单的部署方式）

[🕹 通过腾讯云函数部署](#-方式二通过腾讯云函数部署)（供无服务器的用户使用）

[🧊 通过阿里云函数部署](#-方式三通过阿里云函数部署)（同上，供无服务器的用户使用）

[🚧 直接拉取源码部署](#-方式四直接拉取源码部署)

[📋 捐赠名单 Donate List](#-捐赠名单-donate-list)

[❤ 捐赠 Donate](#-捐赠-donate)

[🍺 信仰](#-信仰)

[🌚 作者](#-作者)

[📝 TODO List](#-TODO-List)

[📰 更新日志](#-更新日志)（每次新版本发布，可以参考此日志决定是否更新）

[🎉 鸣谢](#-鸣谢)

[🥝 开源协议](#-开源协议)

### 📢 注意

- 之前因为 GitHub Action 事件导致本项目被封禁，而后我短暂将项目转移到了 https://github.com/luolongfei/next-freenom
  仓库，然后在 [@Mattraks](https://github.com/Mattraks) 的提醒下，通过特别的方式恢复了本仓库。
- 本次封禁导致的直接后果是以前的`issues`全部丢失，以及近`1.8k`的`star`数重新归零，在动力上面确实有受到影响，不过也不会有太大影响，本项目依然长期维护，如果项目有帮到你，欢迎 star。
- 狡兔三窟，临时仓库 https://github.com/luolongfei/next-freenom 也是备用仓库，如若本仓库再次失联，可以移步到备用仓库获取最新消息，正常情况下以后的开发维护依然在本仓库进行。
- 推荐 [⛵ 通过 Docker 方式部署](#-方式一通过-docker-部署推荐最简单的部署方式)，也可以参考下方文档 [🕹 通过腾讯云函数部署](#-方式二通过腾讯云函数部署)
  或者 [🧊 通过阿里云函数部署](#-方式三通过阿里云函数部署)，腾讯云函数和阿里云函数不需要你有自己的服务器。

### 📃 引言

众所周知，Freenom是地球上唯一一个提供免费顶级域名的商家，不过需要每年续期，每次续期最多一年。由于我申请了一堆域名，而且不是同一时段申请的， 所以每次续期都觉得折腾，于是就写了这个自动续期的脚本。

### 🍭 效果

![邮件示例](https://s2.ax1x.com/2020/01/31/139Rrd.png "邮件内容")

无论是续期成败或者脚本执行出错，都会收到的程序发出的邮件。如果是续期成败相关的邮件，邮件会包括未续期域名的到期天数等内容。 邮件参考了微信发送的注销公众号的邮件样式。

### 🎁 事前准备

- VPS：随便一台服务器都行，系统推荐`Debian`或者`Centos7`，另外 PHP 版本需在`php7.3`及以上。如果你没有服务器，推荐参考下方文档
  [🕹 通过腾讯云函数部署](#-方式二通过腾讯云函数部署) 或者 [🧊 通过阿里云函数部署](#-方式三通过阿里云函数部署) 。
- 送信邮箱（可选）：为了方便理解又称机器人邮箱，用于发送通知邮件。目前针对`Gmail`、`QQ邮箱`、`163邮箱`以及`Outlook邮箱`，程序会自动判断送信邮箱类型并使用合适的配置。
  如果你使用的是其它第三方邮箱或者自建邮件服务，那么请参考 [.env.example](https://github.com/luolongfei/freenom/blob/main/.env.example)
  文件中与邮件配置相关的注释进行配置。
- 收信邮箱（可选）：用于接收机器人发出的通知邮件。
- 上面的`送信邮箱`和`收信邮箱`是可选项，因为目前程序已支持`邮件送信` / `Telegram Bot` / `企业微信` / `Server 酱` / `Bark`等送信方式，仅当你使用`邮件送信`的时候，`送信邮箱`和`收信邮箱`
  才是必须的，其它送信方式所需请参考下面的 [配置送信功能](#-配置送信功能) 。
- 耐心。

### 📪 配置送信功能

此处会分别介绍`邮件送信` / `Telegram Bot` / `企业微信` / `Server 酱` / `Bark`送信方式的配置方法，以及其所需的资料，你可以任选一种送信方式进行配置，直接跳到对应的文档查看即可。 如果你是 IOS
用户，推荐使用 `Bark`
送信方式，其它平台的用户根据自己喜好选择可接受的送信方式即可。不太推荐使用`Server 酱`送信，`Server 酱`每日送信条数的限制，以及需要开会员才能直接看到送信内容，否则需要跳到 `Server 酱`
网站才能查看内容，都是不推荐的原因。同样的配置完全可以直接使用`企业微信`送信方式，`企业微信`送信直接在普通微信客户端就能看到信件内容。

*快速到文档指定位置：*

[邮件送信](#邮件送信)

[Telegram Bot](#Telegram-Bot)

[企业微信](#企业微信)

[Server 酱](#Server-酱)

[Bark 送信](#Bark-送信)

#### 邮件送信

下面分别介绍`Gmail`、`QQ邮箱`以及`163邮箱`的设置，你只用看自己需要的部分。注意，`QQ邮箱`与`163邮箱`均使用`账户加授权码`的方式登录，
`谷歌邮箱`使用`账户加密码`或者`账户加授权码`的方式登录，请知悉。另外还想吐槽一下，国产邮箱你得花一毛钱给邮箱提供方发一条短信才能拿到授权码。

*（点击即可展开或收起）*

<details>
    <summary>设置Gmail</summary>
<br>

1、在`设置>转发和POP/IMAP`中，勾选

- 对所有邮件启用 POP
- 启用 IMAP

![gmail配置01](https://s2.ax1x.com/2020/01/31/13tKsg.png "gmail配置01")

然后保存更改。

2、允许不够安全的应用

登录谷歌邮箱后，访问 [谷歌权限设置界面](https://myaccount.google.com/u/0/lesssecureapps?pli=1&pageId=none) ，启用允许不够安全的应用。

![gmail配置02](https://s2.ax1x.com/2020/01/31/1392KH.png "gmail配置02")

另外，若遇到提示
> 不允许访问账户

登录谷歌邮箱后，去 [gmail的这个界面](https://accounts.google.com/b/0/DisplayUnlockCaptcha) 点击允许。这种情况较为少见。

***

</details>

<details>
    <summary>设置QQ邮箱</summary>
<br>

在`设置>账户>POP3/IMAP/SMTP/Exchange/CardDAV/CalDAV服务`下，开启`POP3/SMTP服务`

![qq邮箱配置01](https://s2.ax1x.com/2020/01/31/13cIKA.png "qq邮箱配置01")

此时坑爹的QQ邮箱会要求你用手机发送一条短信给腾讯，发送完了点一下`我已发送`

![qq邮箱配置02](https://s2.ax1x.com/2020/01/31/13c4vd.png "qq邮箱配置02")

然后你就能看到你的邮箱授权码了，使用邮箱账户加授权码即可登录，记下授权码

![qq邮箱配置03](https://s2.ax1x.com/2020/01/31/13cTbt.png "qq邮箱配置03")

![qq邮箱配置04](https://s2.ax1x.com/2020/01/31/13coDI.png "qq邮箱配置04")

***

</details>

<details>
    <summary>设置163邮箱</summary>
<br>

在`设置>POP3/SMTP/IMAP`下，开启`POP3/SMTP服务`和`IMAP/SMTP服务`并保存

![163邮箱配置01](https://s2.ax1x.com/2020/01/31/13WKZn.png "163邮箱配置01")

![163邮箱配置02](https://s2.ax1x.com/2020/01/31/13WQI0.png "163邮箱配置02")

现在点击侧边栏的`客户端授权密码`，并获取授权码，你看到画面可能和我不一样，因为我已经获取了授权码，所以只有`重置授权码`按钮，这里自己根据网站提示申请获取授权码，网易和腾讯一样恶心，需要你用手机给它发一条短信才能拿到授权码

![163邮箱配置03](https://s2.ax1x.com/2020/01/31/13WMaq.png "163邮箱配置03")

163 邮箱送信后，接收方如果没收到可以在垃圾邮件里面找一下。

***

</details>

上面介绍了三种邮箱的设置方法，如果你不想使用邮件送信，而**由于程序默认启用邮件送信方式，故不配置邮件送信的话，一定要记得关闭邮件推送方式。**
将根目录下的`.env`文件中的`MAIL_ENABLE`的值改为`0`即可关闭邮件推送方式。

*邮件 送信部分完。*

#### Telegram Bot

1、将`.env`文件中的`TELEGRAM_BOT_ENABLE`的值改为`1`，即可启用 Telegram Bot 送信功能

2、在 Telegram 客户端中搜索`@userinfobot`，并打开聊天窗口

3、发送`/start`给`@userinfobot`即可以获取自己的 Id，将`.env`文件中的`TELEGRAM_CHAT_ID`的值改为前面获取到的 Id

4、在 Telegram 客户端中搜索`@BotFather`，并打开聊天窗口

5、发送`/newbot`给`@BotFather`，然后根据提示创建，创建完成后根据图示操作获取`token`

[![I1gpFA.png](https://z3.ax1x.com/2021/11/07/I1gpFA.png)](https://imgtu.com/i/I1gpFA)

6、将`.env`文件中的`TELEGRAM_BOT_TOKEN`的值改为上一步获取的`token`值

7、在 Telegram 客户端中搜索你创建的机器人的账户，上面示例中机器人账户为`@fat_tiger_bot`，请替换为你自己的。找到机器人账户并打开聊天对话框，点击聊天输入框中的 `/start`
按钮或者直接给机器人发送 `/start`，以启用机器人

8、（可选）为 Telegram Bot 设置代理。针对国内网络环境，可将`.env`文件中的`TELEGRAM_PROXY`的值改为代理值，具体参考`.env`文件中的注释

更多与 Telegram Bot 相关内容请参考：[官方文档](https://core.telegram.org/bots#6-botfather)

*Telegram bot 送信部分完。*

#### 企业微信

1、在电脑上打开 [https://work.weixin.qq.com](https://work.weixin.qq.com) ，注册一个企业。注册的过程需要填的信息，腾讯已经做了详尽的说明，根据提示操作即可

2、注册成功后，会跳到注册成功画面，点击页面最下方的`进入管理后台`按钮，将打开管理后台画面

3、在管理后台，点击`应用管理`，然后往下翻，在`自建`部分找到并点击`创建应用`

[![wechat_01.png](https://z3.ax1x.com/2021/11/08/I8160O.png)](https://imgtu.com/i/I8160O)

4、创建应用，应用名称随意，注意下面的`可见范围`选公司名，以使得公司下的所有人可见

[![wechat_02.png](https://z3.ax1x.com/2021/11/08/I8N4IK.png)](https://imgtu.com/i/I8N4IK)

5、应用创建完成后，会跳到应用详情页面，在详情页面，你可以拿到`AgentId`和`Secret`的值， 在`.env`文件中，将`WECHAT_AGENT_ID`的值改为这里拿到的 `AgentId`
的值，将`WECHAT_CORP_SECRET`的值改为这里拿到的`Secret`的值

[![wechat_03.png](https://z3.ax1x.com/2021/11/08/I8auAP.png)](https://imgtu.com/i/I8auAP)

注意，此处要查看`Secret`的值的话，需要先安装`企业微信 app`，点击`发送`后会在`企业微信 app`客户端收到`Secret`的值，将值记录下来后，便可以卸载`企业微信 app`，然后记得将 `.env`
文件中的`WECHAT_CORP_SECRET`的值改为这里拿到的`Secret`的值

[![wechat_04.png](https://z3.ax1x.com/2021/11/08/I8009f.png)](https://imgtu.com/i/I8009f)

[![wechat_05.png](https://z3.ax1x.com/2021/11/08/I8rqEj.png)](https://imgtu.com/i/I8rqEj)

6、获取`企业 ID`，并将`.env`文件中`WECHAT_CORP_ID`的值改为`企业 ID`的值

[![wechat_06.png](https://z3.ax1x.com/2021/11/08/I8sLLD.png)](https://imgtu.com/i/I8sLLD)

7、推送消息到微信客户端。在管理后台点击`我的企业`，再点击`微信插件`，接着往下翻，找到`邀请关注`部分的二维码，用微信扫码关注即可

[![wechat_07.png](https://z3.ax1x.com/2021/11/08/I86TKK.png)](https://imgtu.com/i/I86TKK)

关注后，就可以在微信收到推送消息了

8、将`.env`文件中的`WECHAT_ENABLE`的值改为`1`，以启用微信推送功能

*企业微信 送信部分完。*

#### Server 酱

参考 [Server 酱 教程之企业微信应用消息配置说明](https://sct.ftqq.com/forward) ，这里的配置过程跟上面的`企业微信`配置过程一模一样，所以同样的配置，还是建议直接使用上面的`企业微信`
，不需要开会员也能直接查看消息，不用跳到`Server 酱`的网页查看消息，也不会有每天 5 条送信次数的限制，何乐而不为。

上一步配置完成，你会得到一个`SendKey`，在`.env`文件中，将`SCT_SEND_KEY`的值改为这个`SendKey`所对应的值，然后再将`SCT_ENABLE`的值改为`1`，即可启用`Server 酱`送信。

*Server 酱 送信部分完。*

#### Bark 送信

Bark 是一款 IOS 端用于推送自定义通知的 app，是个人开发者在维护，项目地址为 [https://github.com/Finb/Bark](https://github.com/Finb/Bark) ，客户端和服务端均开源。

1、前往 App Store 搜索`Bark`并安装

[![bark_01.png](https://z3.ax1x.com/2021/11/08/I845nI.png)](https://imgtu.com/i/I845nI)

2、打开`Bark` app，点击`注册设备`，记得允许通知，然后就可以看到，右边红框中两个`/`之间的字符便是你的`BARK_KEY`，请将`.env`文件中的`BARK_KEY`的值设为此处获取的值

[![bark_02.png](https://z3.ax1x.com/2021/11/08/I8Iqyj.png)](https://imgtu.com/i/I8Iqyj)

3、将`.env`文件中的`BARK_ENABLE`的值设为`1`，以启用`Bark`送信功能

*Bark 送信部分完。*

***

*与 配置送信功能 相关的篇幅完。下面开始讲本项目的几种使用方式。推荐使用 Docker 方式，无需纠结环境。*

***

### ⛵ 方式一：通过 Docker 部署（推荐，最简单的部署方式）

Docker 仓库地址为： [https://hub.docker.com/r/luolongfei/freenom](https://hub.docker.com/r/luolongfei/freenom) ，同样欢迎 star 。
此镜像支持的架构为`linux/amd64`，`linux/arm64`，`linux/ppc64le`，`linux/s390x`，`linux/386`，`linux/arm/v7`，`linux/arm/v6`， 理论上支持`群晖`
、`威联通`、`树莓派`以及各种类型的`VPS`。

#### 1、安装 Docker

##### 1.1 以 root 用户登录，执行一键脚本安装 Docker

升级源并安装软件（下面两行命令二选一，根据你自己的系统）

Debian / Ubuntu

```shell
apt-get update && apt-get install -y wget vim
```

CentOS

```shell
yum update && yum install -y wget vim
```

执行此命令等候自动安装 Docker

```shell
wget -qO- get.docker.com | bash
```

说明：请使用 KVM 架构的 VPS，OpenVZ 架构的 VPS 不支持安装 Docker，另外 CentOS 8 不支持用此脚本来安装 Docker。 更多关于 Docker
安装的内容参考 [Docker 官方安装指南](https://docs.docker.com/engine/install/) 。

##### 1.2 针对 Docker 执行以下命令

启动 Docker 服务

```shell
systemctl start docker
```

查看 Docker 运行状态

```shell
systemctl status docker
```

将 Docker 服务加入开机自启动

```shell
systemctl enable docker
```

#### 2、通过 Docker 部署域名续期脚本

##### 2.1 用 Docker 创建并启动容器

命令如下

```shell
docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs luolongfei/freenom
```

或者，如果你想自定义脚本执行时间，则命令如下

```shell
docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs -e RUN_AT="11:24" luolongfei/freenom
```

上面这条命令只比上上条命令多了个` -e RUN_AT="11:24"`，其中`11:24`表示在北京时间每天的 11:24 执行续期任务，你可以自定义这个时间。 这里的`RUN_AT`参数同时也支持 CRON
命令里的时间形式，比如，` -e RUN_AT="9 11 * * *"`，表示每天北京时间 11:09 执行续期任务， 如果你不想每天执行任务，只想隔几天执行，只用修改`RUN_AT`的值即可。

**注意：不推荐自定义脚本执行时间。因为你可能跟很多人定义的是同一个时间点，这样可能导致所有人都是同一时间向 Freenom 的服务器发起请求， 使得 Freenom 无法稳定提供服务。而如果你不自定义时间，程序会自动指定北京时间 06 ~
23 点全时段随机的一个时间点作为执行时间， 每次重启容器都会自动重新指定。**

<details>
    <summary>点我查看上方 Docker 命令的参数解释</summary>
<br>

| 命令 | 含义 |
| :--- | :--- |
| docker run | 开始运行一个容器 |
| -d 参数 | 容器以后台运行并输出容器 ID |
| --name 参数 | 给容器分配一个识别符，方便将来的启动，停止，删除等操作 |
| --restart 参数 | 配置容器启动类型，always 即为 docker 服务重新启动时自动启动本容器 |
| -v 参数 | 挂载卷（volume），冒号后面是容器的路径，冒号前面是宿主机的路径（只支持绝对路径），`$(pwd)`表示当前目录，如果是 Windows 系统，则可用`${PWD}`替换此处的`$(pwd)` |
| -e 参数 | 指定容器中的环境变量 |
| luolongfei/freenom | 这是从 docker hub 下载回来的镜像完整路径名 |

</details>

至此，你的自动续期容器就跑起来了，执行`ls -a`后你就可以看到在你的当前目录下，有一个`.env`文件和一个`logs`目录，`logs`目录里面存放的是程序日志， 而`.env`则是配置文件，现在直接执行`vim .env`
将`.env`文件里的所有配置项改为你自己的并保存即可。然后重启容器，如果配置正确的话，便很快可以收到相关邮件。

<details>
    <summary>点我查看 .env 文件中部分配置项的含义</summary>
<br>

| 变量名 | 含义 | 默认值 | 是否必须 | 备注 |
| :---: | :---: | :---: | :---: | :---: |
| FREENOM_USERNAME | Freenom 账户 | - | 是 | 只支持邮箱账户，如果你是使用第三方社交账户登录的用户，请在 Freenom 管理页面绑定邮箱，绑定后即可使用邮箱账户登录 |
| FREENOM_PASSWORD | Freenom 密码 | - | 是 | 某些特殊字符可能需要转义，详见`.env`文件内注释 |
| MULTIPLE_ACCOUNTS | 多账户支持 | - | 否 | 多个账户和密码的格式必须是“`<账户1>@<密码1>\|<账户2>@<密码2>\|<账户3>@<密码3>`”，注意不要省略“<>”符号，否则无法正确匹配。如果设置了多账户，上面的`FREENOM_USERNAME`和`FREENOM_PASSWORD`可不设置 |
| MAIL_USERNAME | 机器人邮箱账户 | - | 是 | 支持`Gmail`、`QQ邮箱`、`163邮箱`以及`Outlook邮箱`，尽可能使用`163邮箱`或者`QQ邮箱`而非`Gmail`。因为谷歌的安全机制，每次在新设备登录 `Gmail` 都会先被限制，需要手动解除限制才行。具体的配置方法参考「 [配置送信功能](#-配置送信功能) 」 |
| MAIL_PASSWORD | 机器人邮箱密码 | - | 是 | `Gmail`填密码，`QQ邮箱`或`163邮箱`填授权码 |
| TO | 接收通知的邮箱 | - | 是 | 你自己最常用的邮箱，用来接收机器人邮箱发出的域名相关邮件 |
| MAIL_ENABLE | 是否启用邮件推送功能 | `1` | 否 | `1`：启用<br>`0`：不启用<br>默认启用，如果设为`0`，不启用邮件推送功能，则上面的`MAIL_USERNAME`、`MAIL_PASSWORD`、`TO`变量变为非必须，可不设置 |
| TELEGRAM_CHAT_ID | 你的`chat_id` | - | 否 | 通过发送`/start`给`@userinfobot`可以获取自己的`id` |
| TELEGRAM_BOT_TOKEN | 你的`Telegram bot`的`token` | - | 否 ||
| TELEGRAM_BOT_ENABLE | 是否启用`Telegram Bot`推送功能 | `0` | 否 | `1`：启用<br>`0`：不启用<br>默认不启用，如果设为`1`，则必须设置上面的`TELEGRAM_CHAT_ID`和`TELEGRAM_BOT_TOKEN`变量 |
| NOTICE_FREQ | 通知频率 | `1` | 否 | `0`：仅当有续期操作的时候<br>`1`：每次执行 |

**更多配置项含义，请参考 [.env.example](https://github.com/luolongfei/freenom/blob/main/.env.example) 文件中的注释。**

</details>

> 如何验证你的配置是否正确呢？
>

修改并保存`.env`文件后，执行`docker restart freenom`重启容器，等待 5 秒钟左右，然后执行`docker logs freenom`查看输出内容， 观察输出内容中有`执行成功`
字样，则表示配置无误。如果你还来不及配置送信邮箱等内容，可先停用邮件功能。

> 如何升级到最新版或者重新部署呢？
>

在`.env`所在目录，执行`docker rm -f freenom`删除现有容器，然后再执行 `docker rmi -f luolongfei/freenom`
删除旧的镜像，然后再执行上面的 `docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs luolongfei/freenom`
重新部署即可，这样部署后就是最新的代码了。当然，新版对应的`.env`文件可能有变动，不必担心，程序会自动更新`.env`文件内容，并将已有的配置迁移过去。

##### 2.2 后期容器管理以及 Docker 常用命令

查看容器在线状态及大小

```shell
docker ps -as
```

查看容器的运行输出日志

```shell
docker logs freenom
```

重新启动容器

```shell
docker restart freenom
```

停止容器的运行

```shell
docker stop freenom
```

移除容器

```shell
docker rm -f freenom
```

查看 docker 容器占用 CPU，内存等信息

```shell
docker stats --no-stream
```

查看 Docker 安装版本等信息

```shell
docker version
```

重启 Docker（非容器）

```shell
systemctl restart docker
```

*有关容器部署的内容结束。*

***

### 🕹 方式二：通过腾讯云函数部署

*推荐没有自己服务器的用户使用。*

**注意，由于下方文档中图片文件过大，可能会出现图片加载失败的情况，点击裂掉的图片名即可跳转到新页面打开原图。**

#### 1、下载腾讯云函数版的压缩包

腾讯云函数版将与主版同步维护更新，腾讯云函数和阿里云函数使用的是同一个压缩包，下载地址：
[https://github.com/luolongfei/freenom/releases/download/v0.4.4/freenom_scf.zip](https://github.com/luolongfei/freenom/releases/download/v0.4.4/freenom_scf.zip)
。本文档会在发布新版的时候同步更新此处的压缩包下载地址，所以不必担心，你看到的下载地址指向的包一定是最新版本。

下载后你将得到一个 zip 文件，将 zip 文件放到你能找到的任意目录，后面我们将以 zip 文件的形式上传到腾讯云函数。

#### 2、创建腾讯云函数

直接访问腾讯云函数控制台创建云函数： [https://console.cloud.tencent.com/scf/list-create?rid=5&ns=default&createType=empty](https://console.cloud.tencent.com/scf/list-create?rid=5&ns=default&createType=empty)
，按照下图所示的说明进行创建。如果无法看清图片，直接点击图片即可查看原图。

[![scf01](https://z3.ax1x.com/2021/10/14/5lMweU.png)](https://z3.ax1x.com/2021/10/14/5lMweU.png)

按照上图所示部署完成后，可以点击云函数的名称进入云函数管理画面，管理画面点击函数代码，然后往下翻可看到`部署`与`测试`按钮，点击`测试`，稍等几秒钟，即可看到输出日志， 根据输出日志判断配置以及部署是否正确。

[![scf02](https://z3.ax1x.com/2021/10/14/5l3oHf.png)](https://z3.ax1x.com/2021/10/14/5l3oHf.png)

> 如何在腾讯云函数修改或者新增环境变量呢？
>
如果你在创建腾讯云函数的时候，某些环境变量忘记填了，或者在创建腾讯云函数后想要修改或者新增某些环境变量，可以参考这里操作，无需重建：

[![scf03](https://z3.ax1x.com/2021/11/07/I13Ku8.png)](https://z3.ax1x.com/2021/11/07/I13Ku8.png)

所有受支持的环境变量及其含义请参考 [.env.example](https://github.com/luolongfei/freenom/blob/main/.env.example) 文件。

> 如何在腾讯云函数更新部署的代码呢？
>
当有新版本可用时，想升级到最新版本，请按下图所示操作。更新代码包并部署后，可以点击测试查看部署是否成功。在更新完代码后，腾讯云函数编辑器里面可能会提示你同步代码，点击确定即可。

[![scf04.png](https://s4.ax1x.com/2021/12/14/ovruHf.png)](https://s4.ax1x.com/2021/12/14/ovruHf.png)

*有关腾讯云函数部署的内容结束。*

***

### 🧊 方式三：通过阿里云函数部署

*推荐没有自己服务器的用户使用。*

**注意，由于下方文档中图片文件过大，可能会出现图片加载失败的情况，点击裂掉的图片名即可跳转到新页面打开原图。**

#### 1、下载阿里云函数版的压缩包

阿里云函数版将与主版同步维护更新，阿里云函数和腾讯云函数使用的是同一个压缩包，下载地址：
[https://github.com/luolongfei/freenom/releases/download/v0.4.4/freenom_scf.zip](https://github.com/luolongfei/freenom/releases/download/v0.4.4/freenom_scf.zip)
。本文档会在发布新版的时候同步更新此处的压缩包下载地址，所以不必担心，你看到的下载地址指向的包一定是最新版本。

下载后你将得到一个 zip 文件，将 zip 文件放到你能找到的任意目录，后面我们将以 zip 文件的形式上传到阿里云函数。

#### 2、在阿里云开通云函数服务

在 [https://common-buy.aliyun.com/?commodityCode=fc#/buy](https://common-buy.aliyun.com/?commodityCode=fc#/buy)
可以免费开通阿里云函数服务。 详情请参考 [阿里云函数开通流程介绍](https://free.aliyun.com/product/fcfreetrial)
以及 [阿里云函数官方指南](https://help.aliyun.com/product/50980.html) 。

[![aliyun00.png](https://s4.ax1x.com/2021/12/14/ovCauQ.png)](https://s4.ax1x.com/2021/12/14/ovCauQ.png)

#### 3、创建服务以及创建函数

##### 3.1 创建服务

直接访问 [https://fcnext.console.aliyun.com/cn-hongkong/services](https://fcnext.console.aliyun.com/cn-hongkong/services)
，然后点击【创建服务】，地点推荐选择【香港】。此处我没有勾选【日志功能】，如果你需要【日志功能】请勾选后根据官方引导开启，否则跟下图配置一致即可。

[![aliyun01.png](https://s4.ax1x.com/2021/12/14/ovPFKg.png)](https://s4.ax1x.com/2021/12/14/ovPFKg.png)

##### 3.2 创建函数

上一步创建了服务，会跳到函数管理画面，点击创建函数，然后根据下面图示流程进行即可。

[![aliyun02.png](https://s4.ax1x.com/2021/12/14/ovinwd.png)](https://s4.ax1x.com/2021/12/14/ovinwd.png)

[![aliyun03.png](https://s4.ax1x.com/2021/12/14/ovidkn.png)](https://s4.ax1x.com/2021/12/14/ovidkn.png)

[![aliyun04.png](https://s4.ax1x.com/2021/12/14/ovig0J.png)](https://s4.ax1x.com/2021/12/14/ovig0J.png)

配置环境变量

[![aliyun05.png](https://s4.ax1x.com/2021/12/14/oviq7d.png)](https://s4.ax1x.com/2021/12/14/oviq7d.png)

在上图所在画面向下滚动，可以很容易找到【环境变量】的配置位置，注意此处我只示例了最简单的几个变量的配置，没有启用任何送信功能，
建议你在配置时记得启用送信功能，以掌握脚本的执行情况以及续期相关讯息。如何配置以及启用送信功能可以参考本文档的 [配置送信功能](https://github.com/luolongfei/freenom#-%E9%85%8D%E7%BD%AE%E9%80%81%E4%BF%A1%E5%8A%9F%E8%83%BD)
部分。所有受支持的环境变量及其含义请参考 [.env.example](https://github.com/luolongfei/freenom/blob/main/.env.example) 文件。

[![aliyun06.png](https://s4.ax1x.com/2021/12/14/ovFauD.png)](https://s4.ax1x.com/2021/12/14/ovFauD.png)

[![aliyun07.png](https://s4.ax1x.com/2021/12/14/ovkhQK.png)](https://s4.ax1x.com/2021/12/14/ovkhQK.png)

添加函数触发器，即计划任务。这里设置的 Cron 表达式为【CRON_TZ=Asia/Shanghai 49 24 11 * * *】，意为北京时间每天 11:24:49 执行，
**注意，执行时间一定要记得改为你自定义的时间，不要跟我这里一模一样，尽可能跟大多数人的设置不一样，否则可能出现多人在同一时间向 freenom 的服务器发起请求的情况，导致 freenom 无法稳定提供服务，影响续期操作。**

[![aliyun08.png](https://s4.ax1x.com/2021/12/14/ovAnw4.png)](https://s4.ax1x.com/2021/12/14/ovAnw4.png)

上传压缩包（在上面步骤 1 中下载得到 zip 压缩包），部署代码

[![aliyun09.png](https://s4.ax1x.com/2021/12/14/ovmKRs.png)](https://s4.ax1x.com/2021/12/14/ovmKRs.png)

[![aliyun10.png](https://s4.ax1x.com/2021/12/14/ovmDL6.png)](https://s4.ax1x.com/2021/12/14/ovmDL6.png)

由于阿里云函数默认有一个示例文件，所以此处我们上传完 zip 文件后，会提示我们是否需要同步代码，点击【是】即可

[![aliyun11.png](https://s4.ax1x.com/2021/12/14/ovmqYQ.png)](https://s4.ax1x.com/2021/12/14/ovmqYQ.png)

然后点击部署代码

[![aliyun12.png](https://s4.ax1x.com/2021/12/14/ovnn0K.png)](https://s4.ax1x.com/2021/12/14/ovnn0K.png)

【可选】 延长执行超时时间。阿里云函数默认的执行超时时间为 60 s，满足大多数人的需求。如果你的账户或者域名特别多的话，可以考虑延长它， 在【函数配置】->【环境信息】处可以编辑配置

[![aliyun13.png](https://s4.ax1x.com/2021/12/14/ovumgs.png)](https://s4.ax1x.com/2021/12/14/ovumgs.png)

> 在阿里云函数如何修改或者新增环境变量呢？
>
请参考下图操作。

[![aliyun14.png](https://s4.ax1x.com/2021/12/14/ovRKQe.png)](https://s4.ax1x.com/2021/12/14/ovRKQe.png)

> 在阿里云函数如何更新代码或者升级代码到最新版本呢？
>
参考下图，上传新的代码包后，阿里云函数编辑器可能会提示你同步代码，点击【是】即可。然后记得点击【部署】并测试。

[![aliyun15.png](https://s4.ax1x.com/2021/12/14/ovW6HA.png)](https://s4.ax1x.com/2021/12/14/ovW6HA.png)

*有关阿里云函数部署的内容结束。*

***

### 🚧 方式四：直接拉取源码部署

*所有操作均在Centos7系统下进行，其它Linux发行版大同小异*

#### 1、获取源码

创建文件夹

```shell script
mkdir -p /data/wwwroot/freenom && cd /data/wwwroot/freenom
```

clone 本仓库源码

```shell script
git clone https://github.com/luolongfei/freenom.git ./
```

#### 2、修改配置

复制配置文件模板

```shell script
cp .env.example .env
```

编辑配置文件

```shell script
vim .env
```

```shell script
# 注意事项
# .env 文件里每个项目都有详细的说明，这里不再赘述，简言之，你需要把里面所有项都改成你自己的。需要注意的是多账户配置的格式：
# e.g. MULTIPLE_ACCOUNTS='<账户1>@<密码1>|<账户2>@<密码2>|<账户3>@<密码3>'
# （注意不要省略“<>”符号，否则无法正确匹配）
# 当然，若你只有单个账户，只配置 FREENOM_USERNAME 和 FREENOM_PASSWORD 就够了，单账户和多账户的配置会被合并在一起读取并去重。

# 编辑完成后，按“Esc”回到命令模式，输入“:wq”回车即保存并退出，不会用 vim 编辑器的可以谷歌一下:)
```

#### 3、添加计划任务

##### 3.1 安装 crontabs 以及 cronie

```shell script
yum -y install cronie crontabs
```

验证 crond 是否安装及启动

```shell script
yum list cronie && systemctl status crond
```

验证crontab是否安装

```shell script
yum list crontabs $$ which crontab && crontab -l
```

##### 3.2 打开任务表单，并编辑

```shell script
crontab -e
```

```shell script
# 任务内容如下
# 此任务的含义是在每天早上 9点 执行 /data/wwwroot/freenom/ 路径下的 run 文件，最佳实践是将这个时间修改为一个非整点的时间，防止与很多人在同一时间进行续期操作导致 freenom 无法稳定提供服务
# 注意：某些情况下，crontab 可能找不到你的 php 路径，下面的命令执行后会在 freenom_crontab.log 文件输出错误信息，你应该指定 php 路径：把下面的 php 替换为 /usr/local/php/bin/php （根据实际情况，执行 whereis php 即可看到 php 执行文件的真实路径）
00 09 * * * cd /data/wwwroot/freenom/ && php run > freenom_crontab.log 2>&1
```

##### 3.3 重启crond守护进程（每次编辑任务表单后都需此步，以使任务生效）

```shell script
systemctl restart crond
```

若要检查`计划任务`是否正常，你可以将上面的任务执行时间设置在几分钟后，然后等到任务执行完成， 检查`/data/wwwroot/freenom/`目录下的`freenom_crontab.log`
文件内容，是否有报错信息。常见的错误信息如下：

- /bin/sh: php: command not found
- /bin/sh: /usr/local/php: Is a directory

*（点击即可展开或收起）*
<details>
    <summary>解决方案</summary>
<br>

>
> 执行
> ```shell script
> whereis php
> ```
> ```shell script
> # 上面的命令可确定 php 执行文件的位置，一般输出为“php: /usr/local/php /usr/local/php/bin/php”，选长的那个即：/usr/local/php/bin/php
> ```
>
> 现在我们知道 php 执行文件的路径是`/usr/local/php/bin/php`（根据你自己系统的实际情况，可能不同），然后修改表单任务里的命令，把
>
> `00 09 * * * cd /data/wwwroot/freenom/ && php run > freenom_crontab.log 2>&1`
>
> 改为
>
> `00 09 * * * cd /data/wwwroot/freenom/ && /usr/local/php/bin/php run > freenom_crontab.log 2>&1`
>
> 更多参考：[点这里](https://stackoverflow.com/questions/7397469/why-is-crontab-not-executing-my-php-script)
>

</details>

当然，如果你的`计划任务`能正确找到`php路径`，没有错误，那你什么也不用做。

*至此，所有的配置都已经完成，下面我们验证一下整个流程是否走通。*

##### 3.4 验证

你可以先将`.env`中的`NOTICE_FREQ`的值改为1（即每次执行都推送通知），然后执行

```shell script
cd /data/wwwroot/freenom/ && php run
```

不出意外的话，你将收到一封关于域名情况的邮件。

*有关 直接拉取源码部署 的内容结束。*

***

遇到任何问题或 Bug 欢迎提 [issue](https://github.com/luolongfei/freenom/issues) （请按模板格式提`issue`，以便我快速复现你的问题，否则问题会被忽略）， 如果`Freenom`
改变算法导致此项目失效，请提 [issue](https://github.com/luolongfei/freenom/issues) 告知，我会及时修复，本项目长期维护。 欢迎`star`~

### 📋 捐赠名单 Donate List

非常感谢「 [这些用户](https://github.com/luolongfei/freenom/wiki/Donate-List) 」对本项目的捐赠支持！

### ❤ 捐赠 Donate

如果你觉得本项目真的有帮助到你并且想回馈作者，感谢你的捐赠。

#### PayPal: [https://www.paypal.me/mybsdc](https://www.paypal.me/mybsdc)

> Every time you spend money, you're casting a vote for the kind of world you want. -- Anna Lappe

![pay](https://s2.ax1x.com/2020/01/31/1394at.png "Donate")

![每一次你花的钱都是在为你想要的世界投票。](https://s2.ax1x.com/2020/01/31/13P8cF.jpg)

**你的 star 或者`小额打赏`是我长期维护此项目的动力所在，由衷感谢每一位支持者，“每一次你花的钱都是在为你想要的世界投票”。 另外，将本项目推荐给更多的人，也是一种支持的方式，用的人越多更新的动力越足。**

### 🍺 信仰

![南京市民李先生](https://s2.ax1x.com/2020/02/04/1Bm3Ps.jpg "南京市民李先生")
>
> 认真是我们参与这个社会的方式，认真是我们改变这个社会的方式。 ——李志

### 🌚 作者

- 主程序以及框架：[@luolongfei](https://github.com/luolongfei)
- 英文版文档：[@肖阿姨](#)

### 📝 TODO List

- 支持交互式安装，免去手动修改配置的繁琐操作
- 支持自动升级
- 多个账户的续期结果通知合并为同一条消息

### 📰 更新日志

此处省略了很多较为久远的记录，以前的日志只记录了比较大的变更，以后的日志会尽可能详尽一些。

#### [Unreleased]

##### Changed

- 解决 企业微信 因送信内容过长被截断问题
- PHP 版本最低要求不低于 7.3
- 增加英文相关文言，支持中英文切换

#### [v0.4.4](https://github.com/luolongfei/freenom/releases/tag/v0.4.4) - 2021-12-14

##### Changed

- 改进与 Cron 表达式验证相关的正则，兼容各种花里胡哨的表达式

##### Added

- 支持自动从 Bark url 中提取有效的 Bark key
- 支持通过 阿里云函数 部署

#### [v0.4.3](https://github.com/luolongfei/freenom/releases/tag/v0.4.3) - 2021-11-07

##### Added

- 增加了 企业微信 / Server 酱 / Bark 等送信方式
- Telegram Bot 支持使用代理，应对国内网络环境问题
- Freenom 账户支持使用代理，应对国内网络环境问题
- 支持检测新版，有新版本可用时能第一时间收到通知
- 支持自动热更新 .env 文件内容，免去每次更新后手动复制配置的繁琐步骤

##### Changed

- 重构了核心续期代码
- 重构了送信模块
- 简化 .env 文件中的配置项

#### [v0.3](https://github.com/luolongfei/freenom/releases/tag/v0.3) - 2021-05-27

##### Added

- 追加 Docker 版本，支持通过 Docker 方式部署，简化部署流程

#### v0.2.5 - 2020-06-23

##### Added

- 支持在 Github Actions 上执行（应 GitHub 官方要求，已移除此功能）

#### v0.2.2 - 2020-02-06

##### Added

- 新增通过 Telegram bot 送信
- 各种送信方式支持单独开关

#### v0.2 - 2020-02-01

##### Added

- 支持多个 Freenom 账户进行域名续期

##### Changed

- 进行了彻底的重构，框架化
- 优化邮箱模块，支持自动选择合适的邮箱配置

*（版本在 v0.1 到 v0.2 期间代码有过很多次变更，之前没有发布版本，故此处不再赘述相关变更日志）*

#### v0.1 - 2018-8-13

##### Added

- 初版，开源，基础的续期功能

### 🎉 鸣谢

- [PHPMailer](https://github.com/PHPMailer/PHPMailer/) （邮件发送功能依赖此库）
- [guzzle](https://github.com/guzzle/guzzle) （Curl库）
- [秋水逸冰](https://teddysun.com/569.html) （本项目 Docker 相关文档有参考秋水逸冰的文章）

### 🥝 开源协议

[MIT](https://opensource.org/licenses/mit-license.php)
