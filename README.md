<div align="center">

![freenom logo](https://s1.ax1x.com/2022/03/10/bhzMG9.png)

<h3>Freenom：freenom域名自动续期</h3>

[![PHP version](https://img.shields.io/badge/php-%3E=7.3-brightgreen.svg?style=for-the-badge)](https://secure.php.net/)
[![Docker pulls](https://img.shields.io/docker/pulls/luolongfei/freenom.svg?style=for-the-badge)](https://hub.docker.com/r/luolongfei/freenom)
[![GitHub stars](https://img.shields.io/github/stars/luolongfei/freenom?color=brightgreen&style=for-the-badge)](https://github.com/luolongfei/freenom/stargazers)
[![MIT license](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge)](https://github.com/luolongfei/freenom/blob/main/LICENSE)

Documentation: [English version](https://github.com/luolongfei/freenom/blob/main/README_EN.md) | 中文版
</div>

[📢 注意](#-注意)

[🌿 特别感谢 Special Thanks](#-特别感谢-special-thanks)

[📃 引言](#-引言)

[🍭 效果](#-效果)

[🎁 事前准备](#-事前准备)

[📪 配置送信功能](#-配置送信功能)（支持 邮件送信 / Telegram Bot / 企业微信 / Server 酱 / Bark 等送信方式）

[🐳 通过 Docker 方式部署](#-方式一通过-docker-部署推荐最简单的部署方式)（推荐，最简单的部署方式）

[🚈 通过 Railway 部署](#-方式二通过-Railway-部署)（推荐没有自己服务器的用户使用此方案）

[☁ 通过 各种云函数 部署](#-方式三通过各种云函数部署)（同上）

[🚧 直接拉取源码部署](#-方式四直接拉取源码部署)

[📋 赞助名单 Donation List](#-赞助名单-donation-list)

[❤ 赞助 Donation](#-赞助-donation)

[🪓 信仰](#-信仰)

[🌚 作者](#-作者)

[💖 所有贡献者](#-所有贡献者)

[📝 TODO List](#-TODO-List)

[📰 更新日志](#-更新日志)（每次新版本发布，可以参考此日志决定是否更新）

[🎉 鸣谢](#-鸣谢)

[🥝 开源协议](#-开源协议)

### 📢 注意

- 之前因为 GitHub Action 事件导致本项目被封禁，而后我短暂将项目转移到了 https://github.com/luolongfei/next-freenom
  仓库，然后在 [@Mattraks](https://github.com/Mattraks) 的提醒下，通过特别的方式恢复了本仓库。
- 本次封禁导致的直接后果是以前的`issues`全部丢失，以及近`1.8k`的`star`数重新归零，在动力上面确实有受到影响，不过也不会有太大影响，本项目依然长期维护，如果项目有帮到你，欢迎 star。
- 狡兔三窟，临时仓库 https://github.com/luolongfei/next-freenom 也是备用仓库，如若本仓库再次失联，可以移步到备用仓库获取最新消息，正常情况下以后的开发维护依然在本仓库进行。
- 推荐 [🐳 通过 Docker 方式部署](#-方式一通过-docker-部署推荐最简单的部署方式)，也可以参考下方文档 [🚈 通过 Railway 部署](#-方式二通过-Railway-部署)
  或者 [☁ 通过 各种云函数 部署](#-方式三通过各种云函数部署)，各种`云函数`不需要你有自己的服务器，不过由于`云函数`政策经常变化，也许 [Railway](https://railway.app/) 是更好的选择。

### 🌿 特别感谢 Special Thanks

感谢 JetBrains 提供的非商业开源软件开发授权。

Thanks for non-commercial open source development authorization by JetBrains.

<a href="https://www.jetbrains.com/?from=luolongfei/freenom" target="_blank" title="JetBrains Logo (Main) logo.">
<img src="https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg" width='200px' height='200px' alt="JetBrains Logo (Main) logo.">
</a>

### 📃 引言

众所周知，Freenom是地球上唯一一个提供免费顶级域名的商家，不过需要每年续期，每次续期最多一年。由于我申请了一堆域名，而且不是同一时段申请的， 所以每次续期都觉得折腾，于是就写了这个自动续期的脚本。

### 🍭 效果

[![邮件示例](https://s4.ax1x.com/2022/02/26/bZr7WQ.png)](https://s4.ax1x.com/2022/02/26/bZr7WQ.png)

无论是续期成败或者脚本执行出错，都会收到的程序发出的邮件。如果是续期成败相关的邮件，邮件会包括未续期域名的到期天数等内容。 邮件参考了微信发送的注销公众号的邮件样式。

### 🎁 事前准备

- VPS：随便一台服务器都行，系统推荐`Debian`或者`Centos7`，另外 PHP 版本需在`php7.3`及以上。如果你没有服务器，推荐参考下方文档
  [🚈 通过 Railway 部署](#-方式二通过-Railway-部署) 或者 [☁ 通过 各种云函数 部署](#-方式三通过各种云函数部署) 。
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

*推荐打开浏览器隐私模式后再登录 gmail 进行设置，防止当你有多个 gmail 账户时无法跳到正确的设置地址。*

登录谷歌邮箱后，访问 [谷歌权限设置界面](https://myaccount.google.com/u/0/lesssecureapps?pli=1&pageId=none) ，启用允许不够安全的应用。

![gmail配置02](https://s2.ax1x.com/2020/01/31/1392KH.png "gmail配置02")

另外，若遇到提示
> 不允许访问账户

登录谷歌邮箱后，去 [gmail的这个界面](https://accounts.google.com/b/0/DisplayUnlockCaptcha) 点击允许。这种情况较为少见。

**注意：由于直接使用 gmail
密码登录容易触发谷歌安全机制，故推荐参考官方文档启用应用专用密码：[https://support.google.com/mail/answer/185833?hl=zh-Hans](https://support.google.com/mail/answer/185833?hl=zh-Hans)**

**使用账户+应用专用密码登录，就算频繁换 ip 登录 gmail 也不会触发谷歌安全机制。**
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

上面的动作完成后，在`.env`文件中，将`MAIL_USERNAME`和`MAIL_PASSWORD`设置为你的邮箱和密码（或令牌），将`TO`设置为你的收信邮箱，然后将`MAIL_ENABLE`的值设为`1`以启用邮箱送信功能。

上面介绍了三种邮箱的设置方法，如果你不想使用邮件送信，将根目录下的`.env`文件中的`MAIL_ENABLE`的值改为`0`即可关闭邮件推送方式。

*邮件 送信部分完。*

#### Telegram Bot

有关 【Telegram Bot】 的具体配置步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/Telegram-Bot)

#### 企业微信

有关 【企业微信】 的具体配置步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/%E4%BC%81%E4%B8%9A%E5%BE%AE%E4%BF%A1)

#### Server 酱

有关 【Server 酱】 的具体配置步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/Server-%E9%85%B1)

#### Bark 送信

有关 【Bark 送信】 的具体配置步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/Bark-%E9%80%81%E4%BF%A1)

***

*与 配置送信功能 相关的篇幅完。下面开始讲本项目的几种使用方式。推荐使用 Docker 方式，无需纠结环境。*

***

### 🐳 方式一：通过 Docker 部署（推荐，最简单的部署方式）

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

| 变量名 | 含义 | 默认值 | 是否必须 |                                                                        备注                                                                        |
| :---: | :---: |:---:|:----:|:------------------------------------------------------------------------------------------------------------------------------------------------:|
| FREENOM_USERNAME | Freenom 账户 |  -  |  是   |                                           只支持邮箱账户，如果你是使用第三方社交账户登录的用户，请在 Freenom 管理页面绑定邮箱，绑定后即可使用邮箱账户登录                                           |
| FREENOM_PASSWORD | Freenom 密码 |  -  |  是   |                                                            某些特殊字符可能需要转义，详见`.env`文件内注释                                                            |
| MULTIPLE_ACCOUNTS | 多账户支持 |  -  |  否   |                                                           多个账户和密码的格式必须是“`<账户1>@<密码1>\                                                            |<账户2>@<密码2>\|<账户3>@<密码3>`”，注意不要省略“<>”符号，否则无法正确匹配。如果设置了多账户，上面的`FREENOM_USERNAME`和`FREENOM_PASSWORD`可不设置 |
| MAIL_USERNAME | 机器人邮箱账户 |  -  |  否   | 支持`Gmail`、`QQ邮箱`、`163邮箱`以及`Outlook邮箱`，尽可能使用`163邮箱`或者`QQ邮箱`而非`Gmail`。因为谷歌的安全机制，每次在新设备登录 `Gmail` 都会先被限制，需要手动解除限制才行。具体的配置方法参考「 [配置送信功能](#-配置送信功能) 」 |
| MAIL_PASSWORD | 机器人邮箱密码 |  -  |  否   |                                                          `Gmail`填密码，`QQ邮箱`或`163邮箱`填授权码                                                           |
| TO | 接收通知的邮箱 |  -  |  否   |                                                           你自己最常用的邮箱，用来接收机器人邮箱发出的域名相关邮件                                                           |
| MAIL_ENABLE | 是否启用邮件推送功能 | `0` |  否   |                           `1`：启用<br>`0`：不启用<br>默认不启用，如果设为`1`，启用邮件推送功能，则上面的`MAIL_USERNAME`、`MAIL_PASSWORD`、`TO`变量变为必填项                            |
| TELEGRAM_CHAT_ID | 你的`chat_id` |  -  |  否   |                                                      通过发送`/start`给`@userinfobot`可以获取自己的`id`                                                      |
| TELEGRAM_BOT_TOKEN | 你的`Telegram bot`的`token` |  -  |  否   ||
| TELEGRAM_BOT_ENABLE | 是否启用`Telegram Bot`推送功能 | `0` |  否   |                               `1`：启用<br>`0`：不启用<br>默认不启用，如果设为`1`，则必须设置上面的`TELEGRAM_CHAT_ID`和`TELEGRAM_BOT_TOKEN`变量                               |
| NOTICE_FREQ | 通知频率 | `1` |  否   |                                                            `0`：仅当有续期操作的时候<br>`1`：每次执行                                                            |

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

### 🚈 方式二：通过 Railway 部署

*推荐没有自己服务器的用户使用此方案。*

有关 【通过 Railway 部署】
的具体操作步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87-Railway-%E9%83%A8%E7%BD%B2)

***

### ☁ 方式三：通过各种云函数部署

所有云函数使用的是同一个压缩包，已做兼容处理，下载地址：
[https://github.com/luolongfei/freenom/releases/download/v0.5/freenom_scf.zip](https://github.com/luolongfei/freenom/releases/download/v0.5/freenom_scf.zip)
。本文档会在发布新版的时候同步更新此处的压缩包下载地址，所以不必担心，你看到的下载地址指向的包一定是最新版本。

下载后你将得到一个 zip 文件，将 zip 文件放到你能找到的任意目录，后面我们将以 zip 文件的形式上传到各种云函数。

有关 【通过腾讯云函数部署】
的具体操作步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E8%85%BE%E8%AE%AF%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)

有关 【通过阿里云函数部署】
的具体操作步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E9%98%BF%E9%87%8C%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)

有关 【通过华为云函数部署】
的具体操作步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E5%8D%8E%E4%B8%BA%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)

***

### 🚧 方式四：直接拉取源码部署

有关 【直接拉取源码部署】
的具体操作步骤请参考 [此处](https://github.com/luolongfei/freenom/wiki/%E7%9B%B4%E6%8E%A5%E6%8B%89%E5%8F%96%E6%BA%90%E7%A0%81%E9%83%A8%E7%BD%B2)

***

遇到任何问题或 Bug 欢迎提 [issue](https://github.com/luolongfei/freenom/issues) （请按模板格式提`issue`，以便我快速复现你的问题，否则问题会被忽略）， 如果`Freenom`
改变算法导致此项目失效，请提 [issue](https://github.com/luolongfei/freenom/issues) 告知，我会及时修复，本项目长期维护。 欢迎`star`~

### 📋 赞助名单 Donation List

非常感谢「 [这些用户](https://github.com/luolongfei/freenom/wiki/Donation-List) 」对本项目的赞助支持！

### ❤ 赞助 Donation

如果你觉得本项目真的有帮助到你并且想回馈作者，感谢你的赞助。
if you like my script, please consider supporting the project going forward. Your support is greatly appreciated 😃

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/X7X8CA7S1)

PayPal: [https://www.paypal.me/mybsdc](https://www.paypal.me/mybsdc)

> Every time you spend money, you're casting a vote for the kind of world you want. -- Anna Lappe

![pay](https://s2.ax1x.com/2020/01/31/1394at.png "Donation")

![每一次你花的钱都是在为你想要的世界投票。](https://s2.ax1x.com/2020/01/31/13P8cF.jpg)

**你的`star`或者`赞助`是我长期维护此项目的动力所在，由衷感谢每一位支持者，“每一次你花的钱都是在为你想要的世界投票”。 另外，将本项目推荐给更多的人，也是一种支持的方式，用的人越多更新的动力越足。**

### 🪓 信仰

相信未来，保持“理智”。

> 认真是我们参与这个社会的方式，认真是我们改变这个社会的方式。 ——李志

![南京市民李先生](https://s1.ax1x.com/2022/03/10/bhP7FO.jpg "南京市民李先生")

### 🌚 作者

- 主程序以及框架：[@luolongfei](https://github.com/luolongfei)
- 英文版文档：[@肖阿姨](#)

### 💖 所有贡献者

<a href="https://github.com/luolongfei/freenom/graphs/contributors">
  <img alt="All Contributors" src="https://contrib.rocks/image?repo=luolongfei/freenom" />
</a>

[@anjumrafidofficial](https://github.com/anjumrafidofficial)

### 📝 TODO List

- 支持交互式安装，免去手动修改配置的繁琐操作
- 支持自动升级
- 多个账户的续期结果通知合并为同一条消息

### 📰 更新日志

此处只含最新版本的更新日志，完整的日志记录请参考 [Changelog.md](https://github.com/luolongfei/freenom/blob/main/Changelog.md)

#### [Unreleased](#)

- 解决 企业微信 因送信内容过长被截断问题

#### [v0.5](https://github.com/luolongfei/freenom/releases/tag/v0.5) - 2022-05-15

- 增加支持 华为云函数、Railway 等部署方式
- 支持在消息中显示服务器信息，该功能默认关闭
- 优化部分代码逻辑

### 🎉 鸣谢

- 项目依赖 [PHPMailer](https://github.com/PHPMailer/PHPMailer/) 、 [guzzle](https://github.com/guzzle/guzzle) 等第三方库
- 本项目 Docker 相关文档有参考 [秋水逸冰](https://teddysun.com/569.html) 的文章
- [@anjumrafidofficial](https://github.com/anjumrafidofficial) 完善英文版邮件内容

### 🥝 开源协议

[MIT](https://opensource.org/licenses/mit-license.php)
