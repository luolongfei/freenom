<div align="center">

![freenom logo](https://s1.ax1x.com/2022/03/10/bhzMG9.png)

<h3>Freenom: automatic renewal of freenom domain names</h3>

[![PHP version](https://img.shields.io/badge/php-%3E=7.3-brightgreen.svg?style=for-the-badge)](https://secure.php.net/)
[![Docker pulls](https://img.shields.io/docker/pulls/luolongfei/freenom.svg?style=for-the-badge)](https://hub.docker.com/r/luolongfei/freenom)
[![GitHub stars](https://img.shields.io/github/stars/luolongfei/freenom?color=brightgreen&style=for-the-badge)](https://github.com/luolongfei/freenom/stargazers)
[![MIT license](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge)](https://github.com/luolongfei/freenom/blob/main/LICENSE)

Documentation: English version | [中文版](https://github.com/luolongfei/freenom)
</div>

[📢 Notice](#-notice)

[🌿 Special Thanks To](#-special-thanks-to)

[📃 Introduction](#-introduction)

[🍭 Effect](#-effect)

[🎁 Prepare beforehand](#-prepare-beforehand)

[📪 Configure the sending function](#-Configure-the-sending-function)(Support mail delivery / Telegram Bot / Enterprise WeChat / Server sauce / Bark and other delivery methods)

[🐳 Deploy via Docker](#-deploy-via-docker) (recommended, one of the easiest way to deploy)

[🧊 Deploy via Heroku] (#-deploy-via-heroku)

[🚈 Deploy via Railway](#-deploy-via-railway)

[📦 Deploy via Koyeb](#-deploy-via-koyeb) (It is recommended for users who do not have their own server to use this solution, one-click deployment)

[🧪 Deploy via Mogenius](#-deploy-via-mogenius) (If you can't register Koyeb account, you can consider deploying in Mogenius)

[☁ Deploy through various SCF] (#-deploy-through-various-scf)

[🚧 Directly pull source code deployment](#-directly-pull-source-code-deployment)

[📋 Donation List](#-donation-list)

[❤ Sponsor Donation](#-Sponsor-donation)

[🌚 Author] (#-author)

[💖 All Contributors] (#-all-contributors)

[📝 TODO List](#-todo-list)

[📰 Changelog](https://github.com/luolongfei/freenom/blob/main/README.md#-更新日志) (every time a new version is released, you can refer to this log to decide whether to update)

[🍅 This project is implemented in other languages] (#-this-project-is-implemented-in-other-languages)

[🎉 Acknowledgments] (#-Acknowledgments)

[🥝 Open Source License] (#-open-source-license)

### 📢 Notice

- This project was banned because of the GitHub Action incident before, and then I briefly transferred the project to https://github.com/luolongfei/next-freenom
  Warehouse, and then under the reminder of [@Matraks](https://github.com/Matraks), restored this warehouse in a special way.
- The direct consequence of this ban is that all previous `issues` are lost, and the number of `star` of nearly `1.8k` returns to zero. The motivation is indeed affected, but it will not have much impact. The project is still maintained for a long time, if the project helps you, welcome to star.
- Three Caves of the Rabbit, the temporary warehouse https://github.com/luolongfei/next-freenom is also a backup warehouse. If this warehouse loses contact again, you can move to the backup warehouse to get the latest news. Under normal circumstances, future development and maintenance will still be in this warehouse conduct.
- Recommended [🐳 Deploy via Docker](#-deploy via-docker-). If you don't have your own server, you can refer to this document [📦 Deploying via Koyeb](#-via-Koyeb-deploying).
- Enthusiastic netizens have created the `Freenom Renewal Affairs Bureau` group, which can be used for communication, testing, and feedback. **Join can directly visit [https://t.me/freenom_auto_renew](https://t.me/freenom_auto_renew ), or scan the QR code to join: **

<a href="https://t.me/freenom_auto_renew"><img src="https://s2.loli.net/2022/10/11/k4sSoXqMVfpIY3d.png" alt="freenom_tg_group.png" border="0" width="220px" height="280px" /></a>

### 🌿 Special Thanks To

Thanks to JetBrains for the non-commercial open source software development license.

Thanks for non-commercial open source development authorization by JetBrains.

<a href="https://www.jetbrains.com/?from=luolongfei/freenom" target="_blank" title="JetBrains Logo (Main) logo.">
<img src="https://resources.jetbrains.com/storage/products/company/brand/logos/jb_beam.svg" width="200px" height="200px" alt="JetBrains Logo (Main) logo.">
</a>

### 📃 Introduction

As we all know, Freenom is the only merchant on the planet that provides free top-level domain names, but it needs to be renewed every year, and each renewal is up to one year. Since I applied for a bunch of domain names, and I did not apply at the same time, each renewal felt frustrating, so I wrote this automatic renewal script.

### 🍭 Effect

Regardless of the success or failure of the renewal or an error in the execution of the program, you will receive a notification from the script. If it is a notification related to the success or failure of the renewal, the notification will include the expiration days of the non-renewed domain name, etc. *Here is the content of the notification email. *

<a href="https://s4.ax1x.com/2022/02/26/bZr7WQ.png"><img src="https://s4.ax1x.com/2022/02/26/bZr7WQ.png" alt="邮件示例" border="0" width="95%" height="100%" /></a>

### 🎁 Prepare beforehand

- VPS: Any server is fine, the system recommends `Debian`. The `PHP` version needs to be `php7.3` and above. If you have a `Docker` environment, you can ignore this restriction. If you do not have a server, you can refer to this document to deploy in various free environments.
- Delivery mailbox (optional): For the convenience of understanding, it is also called robot mailbox, which is used to send notification emails. Currently for `Gmail`, `QQ mailbox`, `163 mailbox` and `Outlook mailbox`, the program will automatically determine the sending mailbox type and use the appropriate configuration.
  If you are using other third-party email or self-built email service, please refer to [.env.example](https://github.com/luolongfei/freenom/blob/main/.env.example)
  Comments related to mail configuration in the file are configured.
- Receiving mailbox (optional): used to receive notification emails from the robot.
- The above `Sending Mailbox` and `Receiving Mailbox` are optional, because the current program already supports `Email Send` / `Telegram Bot` / `Enterprise WeChat` / `Server Sauce` / `Bark` and other sending methods, only When you use `Send Mail`, `Sending Mailbox` and `Receive Mailbox`
  It is necessary. For other sending methods, please refer to the following [Configure Sending Function](#-Configuring Sending Function).
- patience.

### 📪 Configure the sending function

Here we will introduce the configuration methods of `Mail Delivery` / `Telegram Bot` / `Enterprise WeChat` / `Server Sauce` / `Bark` respectively, as well as the required information. You can choose one of the delivery methods. Configuration, just jump to the corresponding document to view it. If you are IOS
users, recommend using `Bark`
For the delivery method, users of other platforms can choose an acceptable delivery method according to their own preferences. It is not recommended to use `Server Sauce` to send letters, `Server Sauce` has a daily limit on the number of letters sent, and you need to be a member to directly see the content of the mail, otherwise you need to skip to `Server Sauce`
The website can only view the content, which is not recommended. The same configuration can directly use the `Enterprise WeChat` mail delivery method, and the `Enterprise WeChat' sender can see the content of the letter directly on the ordinary WeChat client.

*Quickly go to the specified location of the document:*

[Mail delivery] (#-mail-delivery)

[Telegram Bot] (#-telegram-bot)

[Enterprise WeChat] (#enterprise-wechat)

[Server sauce] (#server-sauce)

[Bark delivery] (#bark-delivery)

#### Mail delivery
The following introduces the settings of `Gmail`, `QQ mailbox` and `163 mailbox`, you only need to look at the parts you need. Note that `QQ Mailbox` and `163 Mailbox` both use the method of `account plus authorization code` to log in.
Please note that `Google Mail` uses `account plus password` or `account plus authorization code` to log in. In addition, I would like to complain that you have to spend a dime to send a text message to the mailbox provider to get the authorization code for domestic mailboxes.

*(Click to expand or collapse)*

<details>
    <summary>Set up Gmail</summary>
<br>

*It is recommended to open the privacy mode of the browser and then log in to gmail to set it up, so as to prevent you from being unable to jump to the correct setting address when you have multiple gmail accounts. *

1. In `Settings > Forwarding and POP/IMAP`, check the

- Enable POP for all mail
- enable IMAP

![gmail configuration 01](https://s2.ax1x.com/2020/01/31/13tKsg.png "gmail configuration 01")

Then save the changes.

2. Turn on two-step verification

Refer to the official document: [Enable two-step verification](https://support.google.com/accounts/answer/185839)

3. Configure the application-specific password to log in to the mailbox

Refer to the official document: [Login with application-specific password](https://support.google.com/mail/answer/185833?hl=zh-Hans)

**Because Gmail no longer supports "insecure login methods", currently you can only log in with an account plus an application-specific password. **

***

</details>

<details>
    <summary>Set up QQ mailbox</summary>
<br>

Under `Settings>Account>POP3/IMAP/SMTP/Exchange/CardDAV/CalDAV Service`, enable `POP3/SMTP Service`

![qq mailbox configuration 01](https://s2.ax1x.com/2020/01/31/13cIKA.png "qq mailbox configuration 01")

At this time, the cheating QQ mailbox will ask you to send a text message to Tencent with your mobile phone. After sending, click `I have sent`

![qq mailbox configuration 02](https://s2.ax1x.com/2020/01/31/13c4vd.png "qq mailbox configuration 02")

Then you can see your email authorization code, use the email account to add the authorization code to log in, and write down the authorization code

![qq mailbox configuration 03](https://s2.ax1x.com/2020/01/31/13cTbt.png "qq mailbox configuration 03")

![qq mailbox configuration 04](https://s2.ax1x.com/2020/01/31/13coDI.png "qq mailbox configuration 04")

***

</details>

<details>
    <summary>Set 163 mailbox</summary>
<br>

Under `Settings>POP3/SMTP/IMAP`, enable `POP3/SMTP Service` and `IMAP/SMTP Service` and save

![163 mailbox configuration 01](https://s2.ax1x.com/2020/01/31/13WKZn.png "163 mailbox configuration 01")

![163 Mailbox Configuration 02](https://s2.ax1x.com/2020/01/31/13WQI0.png "163 Mailbox Configuration 02")

Now click `Client Authorization Password` on the sidebar and get the authorization code. The screen you see may be different from mine, because I have already obtained the authorization code, so there is only the `Reset Authorization Code` button. Here I am according to the website Prompt to apply for an authorization code. Netease is as disgusting as Tencent. You need to send a text message to it with your mobile phone to get the authorization code.

![163 Mailbox Configuration 03](https://s2.ax1x.com/2020/01/31/13WMaq.png "163 Mailbox Configuration 03")

After the 163 mailbox is sent, if the recipient does not receive it, you can look it up in the spam.

***

</details>

After the above actions are completed, in the `.env` file, set `MAIL_USERNAME` and `MAIL_PASSWORD` to your mailbox and password (or token), set `TO` to your mailbox, and then ` The value of MAIL_ENABLE` is set to `1` to enable mailbox delivery.

The above describes three ways to set up mailboxes. If you don’t want to use mail delivery, change the value of `MAIL_ENABLE` in the `.env` file in the root directory to `0` to turn off the mail push method.

*Mail The delivery part is over. *

#### Telegram Bot

For the specific configuration steps of 【Telegram Bot】, please refer to [here](https://github.com/luolongfei/freenom/wiki/Telegram-Bot)

#### Enterprise WeChat

For the specific configuration steps of 【Enterprise WeChat】, Please refer to [here](https://github.com/luolongfei/freenom/wiki/%E4%BC%81%E4%B8%9A%E5%BE%AE%E4%BF%A1)

#### Server Sauce

For the specific configuration steps of [Server sauce], please refer to [here](https://github.com/luolongfei/freenom/wiki/Server-%E9%85%B1)

#### Bark delivers the letter

For the specific configuration steps of 【Bark Messenger】, please refer to [here](https://github.com/luolongfei/freenom/wiki/Bark-%E9%80%81%E4%BF%A1)

***

*End of the chapter related to configuring the sending function. Let's talk about several ways to use this project. It is recommended to use the Docker method without worrying about the environment. *

***

### 🐳 Deploy via Docker

*This is the most recommended way to deploy if you have your own server. *

The address of the Docker warehouse is: [https://hub.docker.com/r/luolongfei/freenom](https://hub.docker.com/r/luolongfei/freenom), welcome to star too.
The supported architectures of this image are `linux/amd64`, `linux/arm64`, `linux/ppc64le`, `linux/s390x`, `linux/386`, `linux/arm/v7`, `linux/arm/v6` `, theoretically supports `Synology`
, `QNAP`, `Raspberry Pi` and various types of `VPS`.

#### 1. Install Docker

##### 1.1 Log in as the root user and execute a one-click script to install Docker

Upgrade the source and install the software (choose one of the following two commands, according to your own system)

Debian / Ubuntu

```shell
apt-get update && apt-get install -y wget vim
```

CentOS

```shell
yum update && yum install -y wget vim
```

Execute this command and wait for Docker to be installed automatically

```shell
wget -qO- get.docker.com | bash
```

Note: Please use the VPS of KVM architecture, the VPS of OpenVZ architecture does not support Docker installation, and CentOS 8 does not support using this script to install Docker. More about Docker
The content of the installation refers to [Docker Official Installation Guide](https://docs.docker.com/engine/install/) 。

##### 1.2 Execute the following command against Docker

Start the Docker service

```shell
systemctl start docker
```

View Docker running status

```shell
systemctl status docker
```

Add the Docker service to start automatically at boot

```shell
systemctl enable docker
```

#### 2. Deploy the domain name renewal script through Docker

##### 2.1 Create and start a container with Docker

The command is as follows

```shell
docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs luolongfei/freenom
```

Or, if you want to customize the script execution time, the command is as follows

```shell
docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs -e RUN_AT="11:24" luolongfei/freenom
```

The above command is only one more than the previous command `-e RUN_AT="11:24"`, where `11:24` means that the renewal task will be executed at 11:24 Beijing time every day, you can customize this time. The `RUN_AT` parameter here also supports CRON
The time format in the command, for example, `-e RUN_AT="9 11 * * *"`, means that the renewal task will be executed at 11:09 Beijing time every day. If you don’t want to execute the task every day, but only want to execute it every few days, just use Just modify the value of `RUN_AT`.

**Note: Custom script execution time is not recommended. Because you may define the same time point with many people, this may cause everyone to initiate a request to Freenom's server at the same time, making Freenom unable to provide stable services. And if you do not customize the time, the program will automatically specify Beijing time 06 ~
A random time point at 23:00 is used as the execution time, and it will be automatically re-specified every time the container is restarted. **

<details>
    <summary>Click me to view the parameter explanation of the above Docker command</summary>
<br>

| command | meaning |
| :--- | :--- |
| docker run | start running a container |
| -d parameter | run the container in the background and output the container ID |
| --name parameter | Assign an identifier to the container for future start, stop, delete and other operations |
| --restart parameter | Configure the container startup type, always means that the container will be automatically started when the docker service is restarted |
| -v parameter| Mount the volume (volume), after the colon is the path of the container, before the colon is the path of the host (only absolute paths are supported), `$(pwd)` indicates the current directory, if it is a Windows system, it can be used `${PWD}` replaces `$(pwd)` here |
| -e parameter | Specify the environment variable in the container |
| luolongfei/freenom | This is the full path name of the image downloaded from docker hub |

</details>

At this point, your auto-renewal container is running. After executing `ls -a`, you can see that in your current directory, there is a `.env` file and a `logs` directory, `logs` directory The program log is stored in it, and `.env` is the configuration file, now execute `vim .env` directly
Change all the configuration items in the `.env` file to your own and save it. Then restart the container, and if the configuration is correct, you will receive relevant emails soon.

<details>
    <summary>Click me to view the meaning of some configuration items in the .env file</summary>
<br>

| Variable name | Meaning | Default value | Required | Remarks |
| :---: | :---: |:---:|:----:|:-------------------- -------------------------------------------------- ------------------ :|
| FREENOM_USERNAME | Freenom account | - | Yes | Only email accounts are supported. If you are a user who uses a third-party social account to log in, please bind your email on the Freenom management page. After binding, you can log in with your email account |
| FREENOM_PASSWORD | Freenom password | - | Yes | Some special characters may need to be escaped, see comments in `.env` file |
| MULTIPLE_ACCOUNTS | Multi-account support | - | No | The format of multiple accounts and passwords must be "`<account1>@<password1>\|<account2>@<password2>\|<account3>@< Password 3>`", be careful not to omit the "<>" symbol, otherwise it cannot be matched correctly. If multiple accounts are set, the above `FREENOM_USERNAME` and `FREENOM_PASSWORD` can not be set |
| MAIL_USERNAME | Robot email account | - | No | Support `Gmail`, `QQ mailbox`, `163 mailbox` and `Outlook mailbox` |
| MAIL_PASSWORD | Robot mailbox password | - | No | Fill in the application-specific password for `Gmail`, fill in the authorization code for `QQ mailbox` or `163 mailbox` |
| TO | E-mail to receive notifications | - | No | Your own most-used e-mail, used to receive domain-related e-mails from robot e-mails |
| MAIL_ENABLE | Whether to enable the mail push function | `0` | No | `1`: Enable<br>`0`: Disable<br>Default is not enabled, if set to `1`, enable the mail push function, then the above `MAIL_USERNAME`, `MAIL_PASSWORD`, `TO` variables become mandatory |
| TELEGRAM_CHAT_ID | Your `chat_id` | - | No | You can get your own `id` by sending `/start` to `@userinfobot` |
| TELEGRAM_BOT_TOKEN | `token` of your `Telegram bot` | - | No ||
| TELEGRAM_BOT_ENABLE | whether to enable `Telegram Bot` push function | `0` | `TELEGRAM_CHAT_ID` and `TELEGRAM_BOT_TOKEN` variables |
| NOTICE_FREQ | Notification frequency | `1` | No | `0`: Only when there is a renewal operation<br>`1`: Every time |
| NEZHA_SERVER | The IP or domain name of the Nezha probe server | - | No |
| NEZHA_PORT | The port of the Nezha probe server | - | No |
| NEZHA_KEY | Nezha probe client dedicated Key | - | No |  

**For more configuration item meanings, please refer to the comments in the [.env.example](https://github.com/luolongfei/freenom/blob/main/.env.example) file. **

</details>

> How to verify that your configuration is correct?
>

After modifying and saving the `.env` file, execute `docker restart freenom` to restart the container, wait for about 5 seconds, and then execute `docker logs freenom` to view the output content, and observe that there is `execution successful` in the output content
, it means the configuration is correct. If you do not have time to configure the sending mailbox and other content, you can disable the mail function first.

> How to upgrade to the latest version or redeploy?
>

In the directory where `.env` is located, execute `docker rm -f freenom` to delete the existing container, and then execute `docker rmi -f luolongfei/freenom`
Delete the old image, and then execute the above `docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs luolongfei/freenom`
Just redeploy, so that the latest code will be available after deployment. Of course, the `.env` file corresponding to the new version may change, don’t worry, the program will automatically update the contents of the `.env` file and migrate the existing configuration to it.

One-sentence operation, that is, execute the following command in the directory where the `.env` file is located to complete the update and upgrade:

```shell
docker rm -f freenom && docker rmi -f luolongfei/freenom && docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs luolongfei/freenom
```

##### 2.2 Post-container management and common commands of Docker

View the online status and size of the container

```shell
docker ps -as
```

View the running output log of the container

```shell
docker logs freenom
```

Restart the container

```shell
docker restart freenom
```

Stop the container from running

```shell
docker stop freenom
```

Remove container

```shell
docker rm -f freenom
```

Check the CPU, memory and other information occupied by the docker container

```shell
docker stats --no-stream
```

View Docker installation version and other information

```shell
docker version
```

Restart Docker (non-container)

```shell
systemctl restart docker
```

*End of content about container deployment. *

***

### 🧊 Deploy via Heroku

**Heroku has stopped offering free service on 2022-11-28, so forget about this article. Official announcement: [https://blog.heroku.com/next-chapter](https://blog.heroku.com/next-chapter)**

For the specific operation steps of [Deploy via Heroku], please refer to [here](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87-Heroku-%E9%83%A8%E7%BD%B2)

***

### 🚈 Deploy via Railway

*Railway has updated the terms of service and increased the usage time limit every month. The new service terms lead to a maximum of 21 days per month. **Unless you verify your credit card, there is no such limit**. For detailed terms and conditions, refer to [here](https://docs.railway.app/reference/pricing#execution-time-limit). *

For the specific operation steps of [Deploy via Railway], please refer to [here](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87-Railway-%E9%83%A8%E7%BD%B2)

***

### 📦 Deploy via Koyeb

*It is recommended that users who do not have their own server use this solution for deployment. This program is completely free. *

For the specific operation steps of [Deploy via Koyeb], please refer to [here](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87-Koyeb-%E9%83%A8%E7%BD%B2)

**After reading the specific content of the upstream document and confirming that you are OK**, you can click the button below to try one-click deployment:

[![Deploy to Koyeb](https://www.koyeb.com/static/images/deploy/button.svg)](https://app.koyeb.com/deploy?type=docker&name=freenom&ports=80;http;/&env[FF_TOKEN]=20190214&env[SHOW_SERVER_INFO]=1&env[MOSAIC_SENSITIVE_INFO]=1&env[FREENOM_USERNAME]=&env[FREENOM_PASSWORD]=&env[MULTIPLE_ACCOUNTS]=&env[TELEGRAM_CHAT_ID]=&env[TELEGRAM_BOT_TOKEN]=&env[TELEGRAM_BOT_ENABLE]=0&env[TOKEN_OR_URL]=[OPTION]%20Token%20or%20URL&env[NEZHA_SERVER]=[OPTION]%20Nezha%20server&env[NEZHA_PORT]=[OPTION]%20Nezha%20port&env[NEZHA_KEY]=[OPTION]%20Nezha%20key&image=docker.io/luolongfei/freenom:koyeb)

***

### 🧪 Deploy via Mogenius

I don't have time to write a detailed tutorial for the time being, if you are interested, you can try it yourself. Refer to my instructions here: [https://github.com/luolongfei/freenom/issues/146](https://github.com/luolongfei/freenom/issues/146) 

***

### ☁ Deploy through various SCF

All SCF uses the same compressed package, which has been processed and downloaded at:
[https://github.com/luolongfei/freenom/releases/download/v0.5.1/freenom_scf.zip](https://github.com/luolongfei/freenom/releases/download/v0.5.1/freenom_scf.zip)
This document will update the archive download address here when the new version is released, so don't worry, the package you see must point to the latest version.

After downloading, you will get a zip file, put the zip file in any directory you can find, and later we will upload it as a zip file to various cloud functions.

About [Deploy via Tencent SCF] For specific steps, please refer to [here](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E8%85%BE%E8%AE%AF%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)

About [Deploy via Alibaba Cloud Functions] For specific steps, please refer to [here](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E9%98%BF%E9%87%8C%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)

About [Deploy with HUAWEI VES] For specific steps, please refer to [here](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E5%8D%8E%E4%B8%BA%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)

***

### 🚧 Directly pull the source code for deployment

About [Directly pull source code deployment] For specific steps, please refer to [here](https://github.com/luolongfei/freenom/wiki/%E7%9B%B4%E6%8E%A5%E6%8B%89%E5%8F%96%E6%BA%90%E7%A0%81%E9%83%A8%E7%BD%B2)

***

Any problems or bugs are welcome [issue](https://github.com/luolongfei/freenom/issues) （Please file `issue` in template format so that I can reproduce your problem quickly, otherwise the problem will be ignored), if `Freenom`
Changing the algorithm will cause this project to fail, please mention [issue](https://github.com/luolongfei/freenom/issues) Let me know, I will fix it in time, and this project will be maintained for a long time. Welcome `star`~

### 📋 Donation List

Many thanks to "[these users](https://github.com/luolongfei/freenom/wiki/Donation-List)" Sponsorship support for this project!

### ❤ Sponsor Donation

If you find this project helpful, please consider sponsoring this project to motivate me to devote more time to maintenance and development.
If you find this project helpful, please consider supporting the project going forward. Your support is greatly
appreciated.

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/X7X8CA7S1)

PayPal: [https://www.paypal.me/mybsdc](https://www.paypal.me/mybsdc)

> Every time you spend money, you're casting a vote for the kind of world you want. -- Anna Lappe

![pay](https://s2.ax1x.com/2020/01/31/1394at.png "Donation")

![Every dollar you spend is a vote for the world you want.](https://s2.ax1x.com/2020/01/31/13P8cF.jpg)

**Your `star` or `sponsorship` is the motivation for me to maintain this project for a long time. I sincerely thank every supporter, "Every time you spend money, you are voting for the world you want." In addition, recommending this project to more people is also a way of support. The more people use it, the more motivated it will be to update. **

### 🌚 Author

- Main program and framework：[@luolongfei](https://github.com/luolongfei)
- English version of the document：[@肖阿姨](#)

### 💖 All Contributors

<a href="https://github.com/luolongfei/freenom/graphs/contributors">
  <img alt="All Contributors" src="https://contrib.rocks/image?repo=luolongfei/freenom" />
</a>

[@origamiofficial](https://github.com/origamiofficial)

### 📝 TODO List

- Support interactive installation, eliminating the tedious operation of manually modifying the configuration
- Support automatic upgrade
- Renewal result notifications for multiple accounts are merged into one message

### 🍅 This project is implemented in other languages

- [https://github.com/PencilNavigator/Freenom-Workers](https://github.com/PencilNavigator/Freenom-Workers) （JavaScript）
- [https://github.com/Oreomeow/freenom-py](https://github.com/Oreomeow/freenom-py) （Python） 

*(More languages are welcome to submit PR to update this list)*

### 🎉 Acknowledgments

- The project relies on [PHPMailer](https://github.com/PHPMailer/PHPMailer/) 、 [guzzle](https://github.com/guzzle/guzzle) and other third-party libraries
- The Docker related documents of this project refer to the article of [秋水逸冰](https://teddysun.com/569.html)
- [@origamiofficial](https://github.com/origamiofficial) Improve the content of the English version email & README_EN.md

### 🥝 Open Source License

[MIT](https://opensource.org/licenses/mit-license.php)