<?php
if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Illuminate\Database\Capsule\Manager as Capsule;

class alipay_config{   
    function get_configuration (){     
        global $_ADMINLANG, $CONFIG;
        $type = Capsule::table("tblpaymentgateways")->where("gateway","alipay")->where("setting","type")->first();
        if ($type->value != "即时到账" and $type->value != "当面付"){
            $extra_config = [
                "notice" => [
					'FriendlyName' => '温馨提示',
					'Type' => 'dropdown',
					'Options' => [
						'warn' => "</option></select><div class='alert alert-danger' role='alert' id='alipay_notice' style='margin-bottom: 0px;'>请点击 [ ".$_ADMINLANG['global']['savechanges']." ] 后 , 再进行修改配置</div><script>$('#alipay_notice').prev().hide();</script><select style='display:none'>"
                    ]
                ]
            ];
        } else {
			if ($type->value == "即时到账") {
				$extra_config = [
                    "app_id" => ["FriendlyName" => "应用ID (APPID)", "Type" => "text", "Size" => "60"],
					"alipay_key" => ["FriendlyName" => "支付宝公钥", "Type" => "textarea",  'Rows' => '7', 'Cols' => '60'],
                    "rsa_key" => ["FriendlyName" => "RSA2(SHA256) 私钥", "Type" => "textarea",  'Rows' => '10', 'Cols' => '60',"Description" => "您可能需要 :<br/><a type='button' class='btn btn-primary' href='https://os.alipayobjects.com/download/secret_key_tools_RSA256_win.zip' target='_blank'><span class='glyphicon glyphicon-new-window'></span> RSA2(SHA256) 生成器下载</a><a type='button' class='btn btn-primary' href='https://doc.open.alipay.com/docs/doc.htm?articleId=106130&docType=1' target='_blank'> <span class='glyphicon glyphicon-new-window'></span> OpenSSL生成教程</a><br/>私钥文件名 : rsa_private_key.pem 公钥文件名 : rsa_public_key.pem<br/>将私钥文件内容使用<span style='color:red'>非Windows记事本打开，</span>掐头去尾，删除换行<br/>并将里面内容复制到上面文本框中(格式和支付宝公钥的一样)<br/>公钥则请到<a href='https://openhome.alipay.com/platform/keyManage.htm' target='_blank'><span class='glyphicon glyphicon-new-window'></span>商家支付宝 开放平台</a>绑定"],
                    "notice" => [
						'FriendlyName' => '',
						'Type' => 'dropdown',
						'Options' => [
							'PcPay' => "</option></select><div class='alert alert-info' role='alert' id='alipay_notice' style='margin-bottom: 0px;'>以上信息均可以在<a href='https://openhome.alipay.com/platform/keyManage.htm' target='_blank'><span class='glyphicon glyphicon-new-window'></span>商家支付宝 开放平台</a>找到 。 请确保已经在支付宝签约 即时到账 必需合约</div><script>$('#alipay_notice').prev().hide();</script><select style='display:none'>"
                        ]
                    ]
                ];
			}
			if ($type->value == "当面付") {
				$extra_config = [
					"app_id" => ["FriendlyName" => "应用ID (APPID)", "Type" => "text", "Size" => "60"],
					"alipay_key" => ["FriendlyName" => "支付宝公钥", "Type" => "textarea",  'Rows' => '7', 'Cols' => '60'],
					"rsa_key" => ["FriendlyName" => "RSA2(SHA256) 私钥", "Type" => "textarea",  'Rows' => '10', 'Cols' => '60',"Description" => "您可能需要 :<br/><a type='button' class='btn btn-primary' href='https://os.alipayobjects.com/download/secret_key_tools_RSA256_win.zip' target='_blank'><span class='glyphicon glyphicon-new-window'></span> RSA2(SHA256) 生成器下载</a><a type='button' class='btn btn-primary' href='https://doc.open.alipay.com/docs/doc.htm?articleId=106130&docType=1' target='_blank'> <span class='glyphicon glyphicon-new-window'></span> OpenSSL生成教程</a><br/>私钥文件名 : rsa_private_key.pem 公钥文件名 : rsa_public_key.pem<br/>将私钥文件内容使用<span style='color:red'>非Windows记事本打开，</span>掐头去尾，删除换行<br/>并将里面内容复制到上面文本框中(格式和支付宝公钥的一样)<br/>公钥则请到<a href='https://openhome.alipay.com/platform/keyManage.htm' target='_blank'><span class='glyphicon glyphicon-new-window'></span>商家支付宝 开放平台</a>绑定" ],
					"notice" => [
						'FriendlyName' => '',
						'Type' => 'dropdown',
						'Options' => [
							'QrPay' => "</option></select><div class='alert alert-info' role='alert' id='alipay_notice' style='margin-bottom: 0px;'>以上信息均可以在<a href='https://openhome.alipay.com/platform/keyManage.htm' target='_blank'><span class='glyphicon glyphicon-new-window'></span>商家支付宝 开放平台</a>找到 。 请确保已经在支付宝签约 当面付 必需合约</div><script>$('#alipay_notice').prev().hide();</script><select style='display:none'>"
						]
					]
				];
			}
        }
        $base_config = [
            "FriendlyName" => ['Type' => 'System','Value' => '支付宝(52Fancy)'],
            "type" => ['FriendlyName' => '支付宝接口类型','Type' => 'dropdown',
                'Options' => [
                    "即时到账" => "[官方] 即时到账",
					"当面付" => "[官方] 当面付"
                ]
            ]
        ];
        
        $config = array_merge($base_config,$extra_config);
        $config["author"] = [
            'FriendlyName' => '',
            'Type' => 'dropdown',
            'Options' => [
                '52Fancy' => "</option></select><div class='alert alert-success' role='alert' id='alipay_author' style='margin-bottom: 0px;'>该插件由 <a href='https://github.com/52fancy' target='_blank'><span class='glyphicon glyphicon-new-window'></span>52fancy</a> 开发 ，本插件为免费开源插件<a target='_blank' href='//shang.qq.com/wpa/qunwpa?idkey=be0fad3bb9d82603cc491c1b8f51513e647e8eff4f9be752c5cc41d5d5429b4e'><img border='0' src='//pub.idqqimg.com/wpa/images/group.png' alt='Whmcs支付插件' title='575798563'></a><br/><span class='glyphicon glyphicon-ok'></span> 支持 WHMCS 5/6/7 , 当前WHMCS 版本 ".$CONFIG["Version"]."<br/><span class='glyphicon glyphicon-ok'></span> 仅支持 PHP 5.4 以上的环境 , 当前PHP版本 ".phpversion()."</div><script>$('#alipay_author').prev().hide();</script><style>* {font-family: Microsoft YaHei Light , Microsoft YaHei}</style><select style='display:none'>"
            ]
        ];
        return $config;
    }
}
