<div align="center">
<h1>Freenom: freenom domain name renews automatically</h1>

[![Build Status](https://img.shields.io/badge/build-passed-brightgreen?style=for-the-badge)](https://scrutinizer-ci.com/g/luolongfei/freenom/build-status/master)
[![Php Version](https://img.shields.io/badge/php-%3E=7.2-brightgreen.svg?style=for-the-badge)](https://secure.php.net/)
[![Scrutinizer Code Quality](https://img.shields.io/badge/scrutinizer-9.31-brightgreen?style=for-the-badge)](https://scrutinizer-ci.com/g/luolongfei/freenom/?branch=master)
[![MIT License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=for-the-badge)](https://github.com/luolongfei/freenom/blob/main/LICENSE)

Documentation: English version | [中文版](https://github.com/luolongfei/freenom)
</div>

[📃 Why write this script](#-Why-write-this-script)

[🍭 Demo](#-Demo)

[🎁 Preparation](#-Preparation)

[📪 Setting up Gmail](#-Setting-up-Gmail)

[🤶 Telegram bot](#-Telegram-bot)

[🚧 Configuration script](#-Configuration-script)

[🎈 Add scheduled task](#-Add-scheduled-task)

[☕ Verification](#-Verification)

[❤ Donate](#-Donate)

[🌚 Author](#-Author)

[🎉 Acknowledgements](#-Acknowledgements)

[🥝 Open source agreement](#-Open-source-agreement)

### 📃 Why write this script

As we all know, Freenom is the only merchant on the planet that provides free top-level domain names, but it needs to be
renewed every year for up to one year at a time. Since I applied for a bunch of domain names, and not at the same time,
So I felt frustrated every time I renewed, so I wrote this automatic renewal script.

### 🍭 Demo

![Email example](https://s2.ax1x.com/2020/01/31/139Rrd.png "Email content")

Regardless of the success or failure of the renewal or the execution of the script, you will receive emails from the
program. In the case of a renewal success or failure email, the email will include the number of days that the domain
name has not been renewed.

### 🎁 Preparation

- Email of robot: Used to send notification emails.
- Your email: Used to receive notification emails sent by robots.
- VPS: Any server can be used. The system recommends `Centos7`, and the PHP version must be` php7.1` or above.
- No more

### 📪 Setting up Gmail

***
1.In `Settings > Forwarding and POP/IMAP`, tick

- Enable POP for all messages
- Enable IMAP

![gmail Configuration 01](https://s2.ax1x.com/2020/02/01/1GDsMR.png "gmail Configuration 01")

Then save your changes.

2.Allow less secure applications

After logging into Google Mail, visit [this page](https://myaccount.google.com/u/0/lesssecureapps?pli=1&pageId=none) and
enable the application that is not secure enough.

Also, if prompted
> Do not allow access to account

After logging in to Google Mail, go to [this page](https://accounts.google.com/b/0/DisplayUnlockCaptcha) and click
Allow. This situation is relatively rare.

### 🤶 Telegram bot

***
If you don't want to use email push, you can also use Telegram bot. In the `.env` file, Change the value
of `TELEGRAM_BOT_ENABLE` to `1` to enable the Telegram bot. Similarly, change the value of `MAIL_ENABLE` to `0` to
disable the mail push method. Telegram bot has two configuration items, one is `chat_id` (corresponding
to `TELEGRAM_CHAT_ID` in `.env` file), You can get your own id by sending `/start` to `@userinfobot` using your Telegram
account, The other is `token` (corresponding to `TELEGRAM_BOT_TOKEN` in the `.env` file), your Telegram bot token, how
to create a Telegram bot and how to get the token please refer to:
[Official Document](https://core.telegram.org/bots#6-botfather)

*This completes the settings related to notifications, followed by the configuration related to this program* :)

### 🚧 Configuration script

All operations are performed under Centos7 system, other Linux distributions are similar

#### Get the source code

```bash
$ mkdir -p /data/wwwroot/freenom
$ cd /data/wwwroot/freenom

# clone the repository source
$ git clone https://github.com/luolongfei/freenom.git ./
```

#### Configuration process

```bash
# Copy configuration file template
$ cp .env.example .env

# Edit configuration file
$ vim .env

# .env Each item in the file has a detailed description, which will not be repeated here. In short, you need to change all the items in it to your own. Note the format of the multi-account configuration:
# e.g. MULTIPLE_ACCOUNTS='<account1>@<password1>|<account2>@<password2>|<account3>@<password3>'
# Of course, if you only have a single account, you only need to configure FREEENOM_USERNAME and FREEENOM_PASSWORD. The configurations of single account and multiple accounts will be read together and duplicated.

# After editing, press "Esc" to return to the command mode, enter ":wq" and press Enter to save and exit. If you don't use vim editor, you can ask Uncle Google. :)
```

### 🎈 Add scheduled task

#### Install crontabs and cronie

```bash
$ yum -y install cronie crontabs

# Verify if crond is installed and started
$ yum list cronie && systemctl status crond

# Verify that crontab is installed
$ yum list crontabs $$ which crontab && crontab -l
```

#### Open the task form and edit

```bash
$ crontab -e

# Task content is as follows
# The meaning of this task is to execute the run file under /data/wwwroot/freenom/ at 9 AM every day
# Note: In some cases, crontab may not find your php path. The following command will output an error message in the freenom_crontab.log file. You should specify the php path: replace the following php with /usr/local/php/bin/php (based on the actual situation)
00 09 * * * cd /data/wwwroot/freenom/ && php run > freenom_crontab.log 2>&1
```

#### Restart the crond daemon (This step is required each time you edit the task form for the task to take effect)

```bash
$ systemctl restart crond
```

To check if the `Task` is normal, you can set the execution time of the above task to a few minutes, and then wait until
the task execution is completed, check the contents of the `freenom_crontab.log` file in the `/data/wwwroot/freenom/`
directory for errors. Common error messages are as follows:

- /bin/sh: php: command not found
- /bin/sh: /usr/local/php: Is a directory

*(Click to expand or collapse)*
<details>
    <summary>solution</summary>
<br>

>
> execute
> ```bash
> $ whereis php
> # Determine the location of php, the general output is "php: /usr/local/php /usr/local/php/bin/php", we choose: /usr/local/php/bin/php
> ```
> Now we know that php's path is `/usr/local/php/bin/php` (may be different according to the actual situation of your own system),
> and then modify the commands in the form task, change
>
> `00 09 * * * cd /data/wwwroot/freenom/ && php run > freenom_crontab.log 2>&1`
>
> to
>
> `00 09 * * * cd /data/wwwroot/freenom/ && /usr/local/php/bin/php run > freenom_crontab.log 2>&1`
>
> More information: [click here](https://stackoverflow.com/questions/7397469/why-is-crontab-not-executing-my-php-script)
>

</details>

Of course, if your `crontab` can correctly find the `php path` without error, you don't need to do anything.

*So far, all the configurations have been completed, let's verify if the whole process works* :)

### ☕ Verification

You can first change the value of `NOTICE_FREQ` in `.env` to 1 (Push notification every time the script is executed),
and then execute

```bash
$ cd /data/wwwroot/freenom/ && php run
```

If nothing else, you will receive an email about the domain name.

If you encounter any problems or bugs, please mention [issues](https://github.com/luolongfei/freenom/issues). If freenom
changes the algorithm and causes this project to fail, Please
mention [issues](https://github.com/luolongfei/freenom/issues) to inform me that I will fix it in time and maintain this
project for a long time. Welcome star ~

### ❤ Donate

#### PayPal: [https://www.paypal.me/mybsdc](https://www.paypal.me/mybsdc)

> Every time you spend money, you're casting a vote for the kind of world you want .-- Anna Lappe

![Every time you spend your money, you are voting for the world you want. ](https://s2.ax1x.com/2020/01/31/13P8cF.jpg)

### 🌚 Author

- Main program and framework: [@luolongfei](https://github.com/luolongfei)
- English document: [@肖阿姨](#)

### 🎉 Acknowledgements

- [PHPMailer](https://github.com/PHPMailer/PHPMailer/) (Mail sending function depends on this library)
- [guzzle](https://github.com/guzzle/guzzle) (Curl library)

### 🥝 Open source agreement

[MIT](https://opensource.org/licenses/mit-license.php)
