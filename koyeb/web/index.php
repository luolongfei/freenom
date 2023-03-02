<?php

set_time_limit(0);

header('X-Accel-Buffering: no');

$FF_TOKEN = $_GET['ff-token'] ?? '';
$VERIFIED = $FF_TOKEN === getenv('FF_TOKEN');

require_once __DIR__ . '/vendor/autoload.php';

use SensioLabs\AnsiConverter\AnsiToHtmlConverter;

$converter = new AnsiToHtmlConverter();
?>

<!DOCTYPE html>
<html lang="zh-cmn-Hans">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no"/>
    <meta name="renderer" content="webkit"/>
    <meta name="force-rendering" content="webkit"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>Freenom 续期控制台 | 你只需部署，剩下的事情交给我们</title>
    <link rel="stylesheet" href="css/mdui.min.css"/>
    <style>
        .loading-icon {
            width: 17px;
            height: 17px;
            margin-right: 10px;
            margin-top: -6px;
            font-size: 18px;
        }

        .success-icon {
            margin-top: -6px;
            margin-right: 10px;
        }

        #copy-btn {
            border-radius: 16px;
        }

        #output-box {
            word-wrap: break-word;
            font-size: 14px;
        }

        .a-tag {
            color: #f44336;
            text-decoration: none;
            font-weight: bold;
        }

        @-webkit-keyframes shake {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        @keyframes shake {
            0% {
                opacity: 1;
            }
            50% {
                opacity: 0;
            }
            100% {
                opacity: 1;
            }
        }

        .shake {
            -webkit-animation: shake 2s infinite;
            animation: shake 2s infinite;
        }
    </style>
</head>
<body>
<div class="mdui-container">
    <div class="mdui-ripple mdui-ripple-yellow"
         mdui-tooltip="{content: '前往项目 GitHub 仓库', position: 'auto', delay: 500}">
        <a href="https://github.com/luolongfei/freenom" target="_blank">
            <img class="mdui-img-rounded mdui-center mdui-valign mdui-img-fluid" src="images/logo_bear.png" alt="logo"/>
        </a>
    </div>

    <ul class="mdui-list mdui-m-t-4">
        <li class="mdui-list-item mdui-ripple mdui-shadow-1">
            <div class="mdui-list-item-avatar">
                <img src="https://q2.qlogo.cn/headimg_dl?dst_uin=593198779&spec=100" alt="作者头像"/>
            </div>
            <div class="mdui-list-item-content">
                Freenom 续期控制台
            </div>
        </li>
    </ul>

    <?php if ($VERIFIED) { ?>
    <div class="mdui-panel" mdui-panel>
        <div class="mdui-panel-item mdui-panel-item-open">
            <div class="mdui-panel-item-header">
                注意事项
            </div>
            <div class="mdui-panel-item-body">
                <p>1、当你看到我的时候，说明你已成功手动触发脚本执行</p>
                <p>2、如果不想每次手动触发执行都输入 FF_TOKEN 的值，你也可以点击右边的按钮，以复制执行地址，下次访问执行地址即可触发脚本执行 <strong><span
                                class="mdui-text-color-red" id="app-url"></span></strong>
                    <button class="mdui-btn mdui-btn-raised mdui-btn-dense mdui-ripple mdui-color-pink-accent"
                            id="copy-btn" data-clipboard-target="#app-url">
                        复制地址
                    </button>
                </p>
                <p>3、不要频繁刷新本页面，因为每次刷新都会手动触发脚本执行</p>
                <p>4、手动触发脚本执行只是为了方便你快速测试部署是否成功，而容器内部的定时任务是会自动执行脚本的，所以，部署完成后，剩下的一切就交给程序吧</p>
            </div>
        </div>
        <?php } else { ?>
            <div class="mdui-panel" mdui-panel>
                <div class="mdui-panel-item mdui-panel-item-open">
                    <div class="mdui-panel-item-header">
                        请先验证身份
                    </div>
                    <div class="mdui-panel-item-body">
                        <div class="mdui-textfield mdui-textfield-floating-label">
                            <i class="mdui-icon material-icons">lock</i>
                            <label class="mdui-textfield-label" for="pwd">请输入你在 Koyeb 配置的 FF_TOKEN
                                的值，即令牌</label>
                            <input class="mdui-textfield-input" type="text" id="pwd"/>
                            <div class="mdui-textfield-helper">
                                点击上行文字即可输入内容，输入完成后，请点击下方送信按钮以验证身份
                            </div>
                        </div>
                        <button class="mdui-btn mdui-btn-block mdui-color-red mdui-ripple" id="submit-btn">
                            送信
                        </button>
                    </div>
                </div>
            </div>
            <script>
                let submitBtn = document.getElementById('submit-btn');

                submitBtn.onclick = function () {
                    let ffToken = document.getElementById('pwd').value;

                    ffToken = ffToken.replace(/\s/g, '');

                    if (ffToken.length < 1) {
                        mdui.snackbar({message: '请输入令牌'});

                        return;
                    }

                    submitBtn.disabled = true;
                    submitBtn.innerText = '送信中...';

                    window.location.href = '?ff-token=' + ffToken;
                }
            </script>
        <?php } ?>

        <div class="mdui-panel-item mdui-panel-item-open" id="shell-box">
            <div class="mdui-panel-item-header" id="shell-title">
                <div id="running-box">
                    <i class="mdui-icon material-icons loading-icon mdui-text-color-red shake">fiber_manual_record</i>
                    正在执行
                </div>
                <div id="success-box" style="display: none;">
                    <i class="mdui-icon material-icons mdui-text-color-green-500 success-icon">check_circle</i>完成
                </div>
            </div>
            <div class="mdui-panel-item-body mdui-color-black" id="output-box">
                <?php
                if ($VERIFIED) {
                    echo '<p>Freenom 自动续期工具</p>';
                    echo '<p>开始执行</p><br>';

                    $cmd = 'php /app/run';

                    while (@ob_end_flush()) ;

                    $proc = popen($cmd, 'r');

                    while (!feof($proc)) {
                        echo '<p>' . $converter->convert(fread($proc, 4096)) . '</p>';
                        @flush();
                    }

                    echo '<p>执行完了</p>';
                    echo '<p>Made with <i class="mdui-icon material-icons mdui-text-color-pink-a200 shake">favorite</i> by <a class="mdui-text-color-white-text" href="https:\/\/github.com/luolongfei" target="_blank">luolongfei</a></p>';

                    echo '<script type="text/javascript">',
                    "document.getElementById('running-box').style.display = 'none';
                    document.getElementById('success-box').style.display = 'block';",
                    '</script>';
                } else {
                    echo '<p>你没有权限触发执行</p>';
                    echo '<script type="text/javascript">',
                    'document.getElementById("shell-title").innerHTML = "啊，出错啦";',
                    '</script>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="mdui-dialog" id="donation-dialog">
        <div class="mdui-dialog-content">
            <ul class="mdui-list mdui-list-dense">
                <li class="mdui-list-item mdui-ripple">
                    <div class="mdui-list-item-avatar">
                        <img src="https://q2.qlogo.cn/headimg_dl?dst_uin=593198779&spec=100" alt="作者头像"/>
                    </div>
                    <div class="mdui-list-item-content">
                        <div class="mdui-list-item-title">Freenom 续期工具</div>
                        <div class="mdui-list-item-text mdui-list-item-two-line">
                            <span class="mdui-text-color-theme-text">如果你觉得本项目对你有帮助，请考虑赞助本项目。</span>
                        </div>
                    </div>
                </li>
            </ul>
            <div class="mdui-card">
                <div class="mdui-card-media">
                    <img class="mdui-img-rounded" src="https://images.llfapp.com/pay.png" alt="赞助二维码"/>
                </div>

                <div class="mdui-card-content">
                    <div id="smart-button-container">
                        <div style="text-align: center;">
                            <div id="paypal-button-container"></div>
                        </div>
                    </div>

                    <script type='text/javascript' src='https://storage.ko-fi.com/cdn/widget/Widget_2.js'></script>
                    <script type='text/javascript'>kofiwidget2.init('Support Me on Ko-fi', '#F05D59', 'X7X8CA7S1');
                        kofiwidget2.draw();</script>
                </div>
            </div>
        </div>
        <div class="mdui-dialog-actions">
            <button class="mdui-btn mdui-ripple" mdui-dialog-close>不了</button>
            <button class="mdui-btn mdui-ripple" mdui-dialog-close
                    onclick="mdui.snackbar({message: '赞助在哪里，我没收到呢'});">已赞助
            </button>
        </div>
    </div>
</div>

<div class="mdui-container">
    <p>
        <a href="https://github.com/luolongfei/freenom" target="_blank"
           class="mdui-btn mdui-btn-raised mdui-ripple"><i class="mdui-icon material-icons">link</i>
            访问仓库</a>
        <a href="https://github.com/luolongfei/freenom/wiki/Donation-List"
           class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent"
           target="_blank"><i class="mdui-icon material-icons">format_list_bulleted</i>
            赞助名单
        </a>
        <button class="mdui-btn mdui-btn-raised mdui-ripple mdui-color-theme-accent mdui-text-color-red"
                mdui-dialog="{target: '#donation-dialog'}"><i class="mdui-icon material-icons">exposure_plus_1</i>
            <strong>赞助作者</strong>
        </button>
    </p>
</div>

<script src="js/mdui.min.js"></script>
<script src="js/clipboard.min.js"></script>
<script src="https://www.paypal.com/sdk/js?client-id=sb&enable-funding=venmo&currency=USD"
        data-sdk-integration-source="button-factory"></script>

<?php

if ($FF_TOKEN !== '' && !$VERIFIED) { // 验证失败
    echo '<script type="text/javascript">',
    "mdui.snackbar({message: '你输入的令牌有误，请重试'});",
    '</script>';
}

if ($VERIFIED) { // 验证成功
    ?>

    <script type="text/javascript">
        document.getElementById('app-url').innerHTML = `https://${document.domain}/?ff-token=<?php echo $FF_TOKEN; ?>`;

        let clipboard = new ClipboardJS('#copy-btn');
        clipboard.on('success', function (e) {
            console.info('Action:', e.action);
            console.info('Text:', e.text);
            console.info('Trigger:', e.trigger);
            mdui.snackbar({message: '复制成功'});

            e.clearSelection();
        });
        clipboard.on('error', function (e) {
            console.error('Action:', e.action);
            console.error('Trigger:', e.trigger);
            alert('复制失败，请手动复制');
        });

        setTimeout(function () {
            document.getElementById('shell-box').scrollIntoView({behavior: 'smooth', block: 'start', inline: 'start'});
        }, 1500);
    </script>

    <?php
}
?>
<script>
    function initPayPalButton() {
        paypal.Buttons({
            style: {
                shape: 'rect',
                color: 'gold',
                layout: 'horizontal',
                label: 'paypal',

            },
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        "description": "赞助 freenom 自动续期脚本的作者，以促进项目持续发展。",
                        "amount": {"currency_code": "USD", "value": 5}
                    }]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (orderData) {
                    const element = document.getElementById('paypal-button-container');
                    element.innerHTML = '';
                    element.innerHTML = '<h3>Thank you for your payment!</h3>';
                });
            },
            onError: function (err) {
                console.log(err);
            }
        }).render('#paypal-button-container');
    }

    initPayPalButton();
</script>
</body>
</html>
