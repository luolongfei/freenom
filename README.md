<div align="center">

![freenom logo](https://s1.ax1x.com/2022/03/10/bhzMG9.png)

<h3>Freenom: automated renewal for Freenom domains</h3>

[![PHP version](https://img.shields.io/badge/php-%3E=8.1-brightgreen.svg?style=for-the-badge)](https://secure.php.net/)
[![Docker pulls](https://img.shields.io/docker/pulls/luolongfei/freenom.svg?style=for-the-badge)](https://hub.docker.com/r/luolongfei/freenom)
[![GitHub stars](https://img.shields.io/github/stars/luolongfei/freenom?color=brightgreen&style=for-the-badge)](https://github.com/luolongfei/freenom/stargazers)
[![MIT license](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge)](https://github.com/luolongfei/freenom/blob/main/LICENSE)

Documentation: English | [中文版](./README_ZH.md)
Changelog: [CHANGELOG.md](./CHANGELOG.md)
</div>

[📢 Announcement](#-announcement)

[📃 Why This Exists](#-why-this-exists)

[🍭 What Notifications Look Like](#-what-notifications-look-like)

[🎁 Before You Start](#-before-you-start)

[📪 Configure Notifications](#-configure-notifications) (Email / Telegram Bot / WeCom / ServerChan / Bark / Pushplus)

[⛵ Docker Compose Deployment](#-docker-compose-deployment)

[🐳 Docker Deployment](#-docker-deployment) (recommended, and usually the simplest option)

[🧊 Heroku Deployment](#-heroku-deployment)

[🚈 Railway Deployment](#-railway-deployment)

[📦 Koyeb Deployment](#-koyeb-deployment) (a good fit if you do not have your own server)

[🧪 Mogenius Deployment](#-mogenius-deployment) (no longer viable)

[☁ Cloud Function Deployment](#-cloud-function-deployment) (no longer actively supported)

[🚧 Deploy from Source](#-deploy-from-source)

[📋 Donation List](#-donation-list)

[❤ Support the Project](#-support-the-project)

[🪓 A Personal Note](#-a-personal-note)

[🌚 Author](#-author)

[💖 All Contributors](#-all-contributors)

[📝 TODO List](#-todo-list)

[🍅 Other Language Ports](#-other-language-ports)

[🎉 Acknowledgements](#-acknowledgements)

[🥝 License](#-license)

### 📢 Announcement

- Community members created a Telegram group called `Freenom Renewal Bureau` for discussion, testing, and feedback. You can join directly here: [https://t.me/freenom_auto_renew](https://t.me/freenom_auto_renew)

### 📃 Why This Exists

Freenom is the only provider I know of that offers free top-level domains, but those domains have to be renewed every year, and only one year at a time. I had a pile of domains registered at different times, and renewing them manually got old fast, so I wrote this script to automate the whole thing.

### 🍭 What Notifications Look Like

The script sends a notification whether renewal succeeds, fails, or crashes. Renewal-related notifications also include details like how many days remain before an unrenewed domain expires. The screenshot below shows the email version of that message.

<a href="https://s4.ax1x.com/2022/02/26/bZrtz9.png"><img src="https://s4.ax1x.com/2022/02/26/bZrtz9.png" alt="Example notification email" border="0" width="95%" height="100%" /></a>

### 🎁 Before You Start

- A VPS or server. Any box will do, although `Debian` is the easiest path. If you deploy without Docker, you need `PHP 8.1` or newer. If you do not have a server, the hosted options later in this README may be a better fit.
- A sender mailbox, if you want email notifications. The script knows how to auto-configure `Gmail`, `QQ Mail`, `163 Mail`, and `Outlook`. If you want to use another provider or your own mail server, check the email-related comments in [`.env.example`](./.env.example).
- A recipient mailbox, if you want to receive email notifications.
- Both mailbox fields are optional because the project also supports `Telegram Bot`, `WeCom`, `ServerChan`, `Bark`, and `Pushplus`. You only need `MAIL_USERNAME`, `MAIL_PASSWORD`, and `TO` if you choose email notifications.
- A little patience.

### 📪 Configure Notifications

This project supports `Email`, `Telegram Bot`, `WeCom`, `ServerChan`, `Bark`, and `Pushplus`. Pick one and configure only that path. If you are on iOS, `Bark` is usually the cleanest option. For most other users, use whichever channel you are already comfortable with. I generally do not recommend `ServerChan`: the daily message cap is restrictive, and some content is hidden behind its paid tier. The same basic setup effort usually goes further with `WeCom`, and those notifications show up directly in the standard WeChat client.

*Jump straight to a section:*

[Email Notifications](#email-notifications)

[Telegram Bot](#telegram-bot)

[WeCom](#wecom)

[ServerChan](#serverchan)

[Bark](#bark)

[Pushplus](#pushplus)

#### Email Notifications

This section covers `Gmail`, `QQ Mail`, and `163 Mail`. Only read the provider you actually use. `QQ Mail` and `163 Mail` both use your mailbox plus an authorization code. `Gmail` now effectively means your mailbox plus an app password.

*(Click to expand or collapse each provider.)*

<details>
    <summary>Gmail</summary>
<br>

*If you use multiple Gmail accounts, open the settings page in a private or incognito window first. That makes it much easier to land on the right account.*

1. In `Settings > Forwarding and POP/IMAP`, enable:

- `Enable POP for all mail`
- `Enable IMAP`

![gmail Configuration 01](https://s2.ax1x.com/2020/02/01/1GDsMR.png "gmail Configuration 01")

Then save the change.

2. Turn on 2-Step Verification.

Official guide: [Turn on 2-Step Verification](https://support.google.com/accounts/answer/185839?hl=en)

3. Create an app password for this script.

Official guide: [Sign in with App Passwords](https://support.google.com/mail/answer/185833?hl=en)

**Gmail no longer supports "less secure app" logins. Use your account plus an app password.**

***

</details>

<details>
    <summary>QQ Mail</summary>
<br>

In `Settings > Accounts > POP3/IMAP/SMTP/Exchange/CardDAV/CalDAV Service`, enable `POP3/SMTP Service`.

QQ Mail will ask you to send an SMS to Tencent. After that, it will display an authorization code. Use your mailbox account plus that authorization code to sign in, and keep the code for your `.env` configuration.

***

</details>

<details>
    <summary>163 Mail</summary>
<br>

In `Settings > POP3/SMTP/IMAP`, enable both `POP3/SMTP Service` and `IMAP/SMTP Service`, then save the change.

Next, open the `Client Authorization Password` section and generate an authorization code. The UI may look different from the screenshot in the Chinese README depending on whether you have already created one. Like QQ Mail, 163 Mail may also require an SMS step before it will issue the code.

If the recipient does not see messages from a 163 mailbox, check the spam folder first.

***

</details>

After that, set `MAIL_USERNAME` and `MAIL_PASSWORD` to your mailbox and password or token, set `TO` to the mailbox that should receive notifications, and set `MAIL_ENABLE=1` in `.env`.

If you do not want email notifications at all, set `MAIL_ENABLE=0` in the root `.env` file.

*That is it for email notifications.*

#### Telegram Bot

For the full Telegram Bot setup flow, see the wiki: [Telegram Bot](https://github.com/luolongfei/freenom/wiki/Telegram-Bot)

#### WeCom

For the full WeCom setup flow, see the wiki: [WeCom](https://github.com/luolongfei/freenom/wiki/%E4%BC%81%E4%B8%9A%E5%BE%AE%E4%BF%A1)

#### ServerChan

For the full ServerChan setup flow, see the wiki: [ServerChan](https://github.com/luolongfei/freenom/wiki/Server-%E9%85%B1)

#### Bark

For the full Bark setup flow, see the wiki: [Bark](https://github.com/luolongfei/freenom/wiki/Bark-%E9%80%81%E4%BF%A1)

#### Pushplus

`Pushplus` is also supported. Set `PUSHPLUS_KEY` to your token in `.env`, then set `PUSHPLUS_ENABLE=1` to turn it on. If Pushplus is the only channel you want to use, leave the other notification methods disabled.

***

*That covers notifications. Next up are the supported deployment options. Docker is still the path I recommend for most people because it removes nearly all environment drift.*

***

### ⛵ Docker Compose Deployment

**Note:** this path is currently marked beta and only supports `amd64`. If you are on `arm` or another architecture, wait for a later update. If you need a server, one option is [cheap US VPS](https://go.llfapp.com/cc).

#### 1. Install Docker and Docker Compose

Debian / Ubuntu (recommended)

```shell
apt-get update -y;
apt-get install -y wget vim git make;
wget -qO- get.docker.com | bash;
systemctl start docker;
sudo systemctl enable docker.service;
sudo systemctl enable containerd.service;
docker version;
DOCKER_COMPOSE_VER=2.24.3;
DOCKER_CONFIG=/usr/local/lib/docker;
mkdir -p $DOCKER_CONFIG/cli-plugins;
curl -SL https://github.com/docker/compose/releases/download/v${DOCKER_COMPOSE_VER}/docker-compose-linux-x86_64 -o $DOCKER_CONFIG/cli-plugins/docker-compose;
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose;
docker compose version;
```

CentOS

```shell
yum update -y;
yum install -y wget vim make;
wget -qO- get.docker.com | bash;
systemctl start docker;
sudo systemctl enable docker.service;
sudo systemctl enable containerd.service;
docker version;
DOCKER_COMPOSE_VER=2.24.3;
DOCKER_CONFIG=/usr/local/lib/docker;
mkdir -p $DOCKER_CONFIG/cli-plugins;
curl -SL https://github.com/docker/compose/releases/download/v${DOCKER_COMPOSE_VER}/docker-compose-linux-x86_64 -o $DOCKER_CONFIG/cli-plugins/docker-compose;
sudo chmod +x /usr/local/lib/docker/cli-plugins/docker-compose;
docker compose version;
```

#### 2. Clone the Repository

```shell
git clone https://github.com/luolongfei/freenom.git && cd freenom
```

#### 3. Configure the Project

##### 3.1 Get a Wit.ai Token

1. Open [https://wit.ai](https://wit.ai).
2. Sign in with Facebook or create an account with email only.
3. Go to [https://wit.ai/apps](https://wit.ai/apps) and create a new app.
4. Choose `English` as the language, pick any name you want, set the app to `Private`, and create it.
5. Open `Management > Settings` (`https://wit.ai/apps/<App ID>/settings`).
6. Copy the `Client Access Token` and put it in `.env` as `WIT_AI_KEY='your Client Access Token'`.

##### 3.2 Edit `.env`

Replace the sample values in `.env` with your own configuration. If you are upgrading from an older release, you can also copy your previous `.env` into the new project root and let the script update it for you. Field-by-field explanations live in [`.env.example`](./.env.example).

```shell
cp .env.example .env;
vim .env;
```

When you are done, save and quit.

#### 4. Start the Stack

Run these commands from the directory that contains `docker-compose.yml`.

```shell
make up
```

That is the whole startup flow. Use `make logs` if you want to tail the live logs.

##### 4.1 Common Commands

Start the stack or update to the latest version

```shell
make up
```

Stop the stack

```shell
make down
```

View live logs

```shell
make logs
```

Clean up disk space used by containers

```shell
make clear
```

Restart the containers

```shell
make restart
```

*That is the end of the Docker Compose section.*

### 🐳 Docker Deployment

*If you have your own server, this is the deployment mode I recommend most.*

Docker Hub: [https://hub.docker.com/r/luolongfei/freenom](https://hub.docker.com/r/luolongfei/freenom)

The image supports `linux/amd64`, `linux/arm64`, `linux/ppc64le`, `linux/s390x`, `linux/386`, `linux/arm/v7`, and `linux/arm/v6`, so it should work on most VPS platforms as well as NAS devices and Raspberry Pi-class hardware.

#### 1. Install Docker

##### 1.1 Log in as `root` and run the one-line installer

Update packages and install the basic tools first. Pick the command that matches your OS.

Debian / Ubuntu

```shell
apt-get update && apt-get install -y wget vim make
```

CentOS

```shell
yum update && yum install -y wget vim make
```

Then install Docker:

```shell
wget -qO- get.docker.com | bash
```

Notes:

- Use a KVM-based VPS. OpenVZ does not support Docker installation.
- CentOS 8 is not supported by this installer script.
- For anything more advanced, use the [official Docker installation guide](https://docs.docker.com/engine/install/).

##### 1.2 Start and enable Docker

Start the Docker service

```shell
systemctl start docker
```

Check Docker status

```shell
systemctl status docker
```

Enable Docker at boot

```shell
systemctl enable docker
```

#### 2. Run the Container

##### 2.1 Create and start the container

Basic command:

```shell
docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs luolongfei/freenom
```

If you want to set a custom run time:

```shell
docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs -e RUN_AT="11:24" luolongfei/freenom
```

That command is identical except for `-e RUN_AT="11:24"`, which tells the container to run the renewal task every day at `11:24` China Standard Time (Beijing time). `RUN_AT` also accepts cron-style expressions. For example, `-e RUN_AT="9 11 * * *"` means `11:09` China Standard Time every day. If you want to run less often than daily, change the cron expression accordingly.

**I do not recommend setting a custom schedule unless you have a real reason to do it. If a large number of users all pick the same timestamp, everyone ends up hitting Freenom at once and service quality gets worse for everybody. If you leave `RUN_AT` unset, the container automatically chooses a random time between 06:00 and 23:00 China Standard Time, and it re-rolls that time on each restart.**

<details>
    <summary>Click to see what the Docker flags mean</summary>
<br>

| Flag | Meaning |
| :--- | :--- |
| `docker run` | Starts a new container |
| `-d` | Runs the container in the background and prints the container ID |
| `--name` | Gives the container a stable name so you can start, stop, and remove it later |
| `--restart` | Sets the restart policy; `always` means Docker starts the container again when the Docker service comes back |
| `-v` | Mounts a volume. The path after the colon is the container path, and the path before the colon is the host path. Only absolute host paths are supported. `$(pwd)` means the current directory. On Windows, use `${PWD}` instead. |
| `-e` | Sets an environment variable inside the container |
| `luolongfei/freenom` | The full image name pulled from Docker Hub |

</details>

After the container starts, run `ls -a` in the current directory and you should see a `.env` file plus a `logs` directory. `logs` stores runtime logs, and `.env` is the configuration file. Edit `.env`, replace the sample values with your own, save it, and restart the container. If the config is valid, you should start receiving notifications quickly.

<details>
    <summary>Click to see what some `.env` variables mean</summary>
<br>

| Variable | Meaning | Default | Required | Notes |
| :--- | :--- | :---: | :---: | :--- |
| `FREENOM_USERNAME` | Freenom account email | - | Yes | Only email-based Freenom logins are supported. If you currently sign in through a third-party social account, bind an email inside the Freenom dashboard first. |
| `FREENOM_PASSWORD` | Freenom password | - | Yes | Some special characters may need escaping. See the comments in `.env`. |
| `MULTIPLE_ACCOUNTS` | Multiple-account support | - | No | Format must be `<account1>@<password1>\|<account2>@<password2>\|<account3>@<password3>`. Do not remove the angle brackets. If this is set, `FREENOM_USERNAME` and `FREENOM_PASSWORD` become optional. |
| `MAIL_USERNAME` | Sender mailbox account | - | No | Supports `Gmail`, `QQ Mail`, `163 Mail`, and `Outlook`. |
| `MAIL_PASSWORD` | Sender mailbox password | - | No | Use a Gmail app password, or the authorization code from QQ Mail / 163 Mail. |
| `TO` | Recipient mailbox | - | No | The mailbox that receives the notification emails sent by the script. |
| `MAIL_ENABLE` | Enable email notifications | `0` | No | `1` enables email notifications. `0` disables them. If enabled, `MAIL_USERNAME`, `MAIL_PASSWORD`, and `TO` all become required. |
| `TELEGRAM_CHAT_ID` | Your `chat_id` | - | No | Send `/start` to `@userinfobot` to retrieve it. |
| `TELEGRAM_BOT_TOKEN` | Your Telegram bot token | - | No | |
| `TELEGRAM_BOT_ENABLE` | Enable Telegram Bot notifications | `0` | No | `1` enables Telegram notifications. If enabled, `TELEGRAM_CHAT_ID` and `TELEGRAM_BOT_TOKEN` are required. |
| `NOTICE_FREQ` | Notification frequency | `1` | No | `0` only when a renewal operation happens. `1` on every run. |
| `NEZHA_SERVER` | Nezha probe server IP or domain | - | No | |
| `NEZHA_PORT` | Nezha probe server port | - | No | |
| `NEZHA_KEY` | Nezha client key | - | No | |
| `NEZHA_TLS` | Enable SSL/TLS for Nezha | - | No | `1` enables TLS. `0` disables it. |

**For the full set of variables, see the comments in [`.env.example`](./.env.example).**

</details>

> How do I know whether my config is correct?
>
> After you save `.env`, run `docker restart freenom`, wait about five seconds, then run `docker logs freenom`. If the output includes a success message, your configuration is in good shape. If you have not configured email yet, disable mail delivery first.

> How do I upgrade to the latest version or redeploy from scratch?
>
> From the directory that contains `.env`, delete the existing container with `docker rm -f freenom`, remove the old image with `docker rmi -f luolongfei/freenom`, and run the `docker run` command again. That redeploys the latest image. If the new release changes `.env`, the program will update the file and migrate your existing settings automatically.

One-line upgrade command:

```shell
docker rm -f freenom && docker rmi -f luolongfei/freenom && docker run -d --name freenom --restart always -v $(pwd):/conf -v $(pwd)/logs:/app/logs luolongfei/freenom
```

##### 2.2 Common container management commands

Show container status and size

```shell
docker ps -as
```

Show container logs

```shell
docker logs freenom
```

Restart the container

```shell
docker restart freenom
```

Stop the container

```shell
docker stop freenom
```

Remove the container

```shell
docker rm -f freenom
```

Show container CPU and memory usage

```shell
docker stats --no-stream
```

Show Docker version details

```shell
docker version
```

Restart Docker itself (not just the container)

```shell
systemctl restart docker
```

*That wraps up the container deployment section.*

***

### 🧊 Heroku Deployment

**Heroku ended its free tier on November 28, 2022, so this route is effectively dead. Official announcement: [https://blog.heroku.com/next-chapter](https://blog.heroku.com/next-chapter)**

If you still want the historical deployment guide, it lives in the wiki: [Deploy via Heroku](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87-Heroku-%E9%83%A8%E7%BD%B2)

***

### 🚈 Railway Deployment

*Railway's pricing changes often. Whether it is a good long-running deployment target depends on the plan you are on and the current billing rules, so check the official docs before you deploy: [Railway pricing](https://docs.railway.com/pricing), [Railway plans](https://docs.railway.com/reference/pricing/plans).*

The step-by-step Railway guide is in the wiki: [Deploy via Railway](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87-Railway-%E9%83%A8%E7%BD%B2)

***

### 📦 Koyeb Deployment

*If you do not have your own server, Koyeb can still be a reasonable option. Free allowances and whether a card is required can change with Koyeb's plan policy, so check the current pricing before you deploy: [Koyeb pricing](https://www.koyeb.com/pricing/).*

The step-by-step Koyeb guide is in the wiki: [Deploy via Koyeb](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87-Koyeb-%E9%83%A8%E7%BD%B2)

After you read that guide and you are comfortable with the setup, you can try the one-click deploy link here:

[Deploy on Koyeb](https://app.koyeb.com/deploy?type=docker&name=freenom&ports=80;http;/&env[FF_TOKEN]=20190214&env[SHOW_SERVER_INFO]=1&env[MOSAIC_SENSITIVE_INFO]=1&env[FREENOM_USERNAME]=&env[FREENOM_PASSWORD]=&env[MULTIPLE_ACCOUNTS]=&env[MAX_REQUEST_RETRY_COUNT]=200&env[TELEGRAM_CHAT_ID]=&env[TELEGRAM_BOT_TOKEN]=&env[TELEGRAM_BOT_ENABLE]=0&env[NEZHA_SERVER]=[OPTION]%20Nezha%20server&env[NEZHA_PORT]=[OPTION]%20Nezha%20port&env[NEZHA_KEY]=[OPTION]%20Nezha%20key&env[NEZHA_TLS]=[OPTION]%20Enable%20tls&image=docker.io/luolongfei/freenom:koyeb)

***

### 🧪 Mogenius Deployment

Mogenius removed its free plan, so this option is no longer usable. Background: [discussion #208](https://github.com/luolongfei/freenom/discussions/208)

***

### ☁ Cloud Function Deployment

All cloud-function targets use the same ZIP package, which was prepared for cross-platform compatibility:
[https://github.com/luolongfei/freenom/releases/download/v0.5.1/freenom_scf.zip](https://github.com/luolongfei/freenom/releases/download/v0.5.1/freenom_scf.zip)

Cloud-function deployment is no longer actively maintained, so this historical package remains the reference artifact for this path.

After downloading it, place the ZIP anywhere convenient on your machine. The deployment flow for each provider uploads that ZIP directly.

This deployment style is no longer actively supported because the major platforms have moved to paid pricing, but the old wiki pages are still here if you need them:

- [Deploy via Tencent Cloud Functions](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E8%85%BE%E8%AE%AF%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)
- [Deploy via Alibaba Cloud Functions](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E9%98%BF%E9%87%8C%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)
- [Deploy via Huawei Cloud Functions](https://github.com/luolongfei/freenom/wiki/%E9%80%9A%E8%BF%87%E5%8D%8E%E4%B8%BA%E4%BA%91%E5%87%BD%E6%95%B0%E9%83%A8%E7%BD%B2)

***

### 🚧 Deploy from Source

The source-based deployment guide lives in the wiki: [Deploy from Source](https://github.com/luolongfei/freenom/wiki/%E7%9B%B4%E6%8E%A5%E6%8B%89%E5%8F%96%E6%BA%90%E7%A0%81%E9%83%A8%E7%BD%B2)

***

If you run into a bug, please open an [issue](https://github.com/luolongfei/freenom/issues) and follow the template so the problem is easy to reproduce. If Freenom changes its algorithm and breaks the project, open an issue and let me know. I maintain this repository for the long haul, and stars are always appreciated.

### 📋 Donation List

Huge thanks to [these supporters](https://github.com/luolongfei/freenom/wiki/Donation-List) for backing the project.

### ❤ Support the Project

If this project saves you time, consider supporting it. Support makes it much easier to keep maintaining and improving the codebase.

[![ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/X7X8CA7S1)

PayPal: [https://www.paypal.me/mybsdc](https://www.paypal.me/mybsdc)

> Every time you spend money, you're casting a vote for the kind of world you want. -- Anna Lappe

![Every time you spend your money, you are voting for the world you want.](https://s2.ax1x.com/2020/01/31/13P8cF.jpg)

If you leave a message with your donation, it will be shown on the [Donation List](https://github.com/luolongfei/freenom/wiki/Donation-List).

**Every `star` and every donation helps keep this project alive. Thank you to everyone who has supported it. Recommending the project to other people helps too. The more people use it, the easier it is to justify spending time on updates.**

### 🪓 A Personal Note

Believe in the future. Stay rational.

> Taking things seriously is how we participate in society, and how we change it. -- Li Zhi

### 🌚 Author

- Main program and framework: [@luolongfei](https://github.com/luolongfei)
- English documentation: [@肖阿姨](#)

### 💖 All Contributors

<a href="https://github.com/luolongfei/freenom/graphs/contributors">
  <img alt="All Contributors" src="https://contrib.rocks/image?repo=luolongfei/freenom" />
</a>

[@anjumrafidofficial](https://github.com/anjumrafidofficial)

### 📝 TODO List

- Add an interactive installer so users do not have to edit config files by hand
- Support automatic upgrades
- Merge multi-account renewal results into a single notification

### 🍅 Other Language Ports

- [https://github.com/PencilNavigator/Freenom-Workers](https://github.com/PencilNavigator/Freenom-Workers) (JavaScript)
- [https://github.com/Oreomeow/freenom-py](https://github.com/Oreomeow/freenom-py) (Python)

*(If you have another implementation in a different language, feel free to open a PR and add it to this list.)*

### 🎉 Acknowledgements

- This project depends on third-party libraries such as [PHPMailer](https://github.com/PHPMailer/PHPMailer/) and [guzzle](https://github.com/guzzle/guzzle).
- Some of the Docker-related documentation was informed by [this article](https://teddysun.com/569.html).
- [@anjumrafidofficial](https://github.com/anjumrafidofficial) improved the English mail content.

### 🥝 License

[MIT](https://opensource.org/licenses/mit-license.php)
