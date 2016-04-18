## BeeCloud PHP SDK (Open Source)

![license](https://img.shields.io/badge/license-MIT-brightgreen.svg) ![version](https://img.shields.io/badge/version-v2.3.0-blue.svg)

## 简介

本项目的官方GitHub地址是 [https://github.com/beecloud/beecloud-php](https://github.com/beecloud/beecloud-php)，目前支持以下功能：
- 微信支付、支付宝支付、银联在线支付、百度钱包支付、京东支付等多种支付方式
- 支付/退款订单总数的查询
- 订单状态的查询与订单撤销
- 支付订单和退款订单的查询
- 根据ID(支付/退款订单唯一标识)查询订单记录、退款记录

本SDK 基于 [BeeCloud RESTful API](https://github.com/beecloud/beecloud-rest-api)

依赖: PHP 5.3+, PHP-curl

## 准备

1. BeeCloud[注册](http://beecloud.cn/register/)账号
2. BeeCloud中创建应用，[填写支付渠道所需参数](http://beecloud.cn/doc/payapply)

具体可参考[快速开始](https://beecloud.cn/apply/)

## 引入BeeCloud API

###使用[composer](https://getcomposer.org/)
在你的composer.json中添加如下依赖

	{
		{
		"require": {
			"beecloud.cn/rest": "{version}"
		}
	}

>composer 是php的包管理工具， 通过json里的配置管理依赖的包， 同时可以在使用类时自动加载对应的包

其中composer支持需要version>=v2.2.0  
version ＝ dev-master为主干分支开发版本,请酌情使用

然后命令行执行

```
composer install
```

在需要使用的php文件中使用 Composer 的 autoload 引入

```
require_once('vendor/autoload.php');
```

###手动使用
适合不能使用composer（PHP < 5.3.2）或者namespace(PHP < 5.3)的情况
拷贝当前所有文件（demo可以忽略）到你指定的目录<YourPath>下，你的代码中
	
	require_once("<YourPath>/loader.php");
	
####原有使用v2.2.0以下的用户和不使用namespace的用户则请修改为

	require_once("<YourPath>/degrade/beecloud.php");	
		
	
	

## BeeCloud API 
>$data参数和返回参数请参考BeeCloud RESTfull API,同时可以参考demo中各渠道的代码示例）

##发起支付订单 

###国际支付
    
~~~
\beecloud\rest\international::bill(array $data);
~~~

不使用namespace的用户和2.2.0之前的v2版本用户请使用
    
~~~
BCRESTInteraional::bill(array $data);
~~~

data参数（array类型）:


参数名 | 类型 | 含义 | 描述 | 例子 | 是否必填
----  | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud平台的AppID | App在BeeCloud平台的唯一标识 | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb | 是
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+app\_secret)，32位16进制格式,不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
channel| String | 渠道类型 | 根据不同场景选择不同的支付方式 | PAYPAL_PAYPAL, PAYPAL_CREDITCARD, PAYPAL_SAVED_CREDITCARD(详见附注）| 是
total_fee | String | 订单总金额 | 正数，最多两位小数 | 0.01 | 是
currency | String | 三位货币种类代码 | 见附录 | USD | 是
bill_no | String | 商户订单号 | 8到32位数字和/或字母组合，请自行确保在商户系统中唯一，同一订单号不可重复提交，否则会造成订单重复 | 201506101035040000001 | 是
title| String | 订单标题 | UTF8编码格式，32个字节内，最长支持16个汉字 | 白开水 | 是
credit\_card_info | Map | 信用卡信息 | 行用卡信息 | {"card\_number":"420243123344","expire\_month":07,"expire\_year":2020,"cvv":"204","first\_name:"jim","last\_name":"Green", "card\_type":"visa"} | 当channel 为PAYPAL_CREDITCARD必填
credit\_card_id| String | 信用卡id | 当使用PAYPAL_CREDITCARD支付完成后会返回一个credit\_card_id | CARD\_ADMJ324234DJLKJS | 当channel为PAYPAL_SAVED_CREDITCARD时为必填
return_url | String | 同步返回页面| 支付渠道处理完请求后,当前页面自动跳转到商户网站里指定页面的http路径不包含?及&，必须为http://或者https://开头 | http://baidu.com | 当channel参数为PAYPAL_PAYPAL时为必填


- 以下是`credit_card_info`的参数

参数名 | 类型 | 含义 | 例子
---- | ---- | ---- | ----
card\_number| String | 卡号 | 420243123344
expire\_month| int | 过期时间中的月 | 12
expire\_year| int | 过期时间中的年 | 2020
cvv| int | 信用卡的三位cvv码 | 123
first\_name | String | 用户名字 | Jim
last\_name | String | 用户的姓 | Green
card\_type | String | 卡类别 visa/mastercard/discover/amex | visa

- 以下是`currency`参数 的对照表

名称 | 缩写 
---- | ---- | 
Australian dollar|	AUD  
Brazilian real**	|BRL  
Canadian dollar|	CAD  
Czech koruna|	CZK    
Danish krone|	DKK  
Euro|	EUR  
Hong Kong dollar|	HKD  
Hungarian forint|	HUF  
Israeli new shekel|	ILS  
Japanese yen|	JPY  
Malaysian ringgit|	MYR  
Mexican peso|	MXN  
New Taiwan dollar|	TWD  
New Zealand dollar|	NZD  
Norwegian krone|	NOK  
Philippine peso|	PHP  
Polish złoty|	PLN  
Pound sterling|	GBP  
Singapore dollar|	SGD  
Swedish krona	|SEK  
Swiss franc|	CHF  
Thai baht	|THB  
Turkish lira|	TRY  
United States dollar|	USD  

返回结果（Object类型）:

- **公共返回参数**

参数名 | 类型 | 含义 
---- | ---- | ----
result_code | Integer | 返回码，0为正常
result_msg  | String | 返回信息， OK为正常
err_detail  | String | 具体错误信息
url |String| 当channel 为PAYPAL_PAYPAL时返回，跳转支付的url
credit_card_id | String| 当channel为PAYPAL_CREDITCARD时返回， 信用卡id
id| String| 订单id

- **公共返回参数取值列表及其含义**

result_code | result_msg             | 含义
----        | ----      		       | ----
0           | OK                     | 调用成功
1           | APP\_INVALID           | 根据app\_id找不到对应的APP或者app\_sign不正确
2           | PAY\_FACTOR_NOT\_SET   | 支付要素在后台没有设置
3           | CHANNEL\_INVALID       | channel参数不合法
4           | MISS\_PARAM            | 缺少必填参数
5           | PARAM\_INVALID         | 参数不合法
6           | CERT\_FILE\_ERROR      | 证书错误
7           | CHANNEL\_ERROR         | 渠道内部错误
14          | RUN\_TIME_ERROR        | 实时未知错误，请与技术联系帮助查看


### 国内支付

~~~
\beecloud\rest\api::bill(array $data);
~~~

不使用namespace的用户和2.2.0之前的v2版本用户请使用

~~~
BCRESTApi::bill(array $data);
~~~

data参数（array类型）:

参数名 | 类型 | 含义 | 描述 | 例子 | 是否必填
----  | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud平台的AppID | App在BeeCloud平台的唯一标识 | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb | 是
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+app\_secret)，32位16进制格式,不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
channel| String | 渠道类型 | 根据不同场景选择不同的支付方式 | WX\_APP、WX\_NATIVE、WX\_JSAPI、ALI\_APP、ALI\_WEB、ALI\_QRCODE、ALI\_OFFLINE_QRCODE、ALI_WAP、UN\_APP、UN\_WEB、JD_WAP、JD_WEB、YEE_WAP、YEE_WEB、KUAIQIAN_WAP、KUAIQIAN_WEB、BD\_WAP、BD\_WEB(详见附注）| 是
total_fee | Integer | 订单总金额 | 必须是正整数，单位为分 | 1 | 是
bill_no | String | 商户订单号 | 8到32位数字和/或字母组合，请自行确保在商户系统中唯一，同一订单号不可重复提交，否则会造成订单重复 | 201506101035040000001 | 是
title| String | 订单标题 | UTF8编码格式，32个字节内，最长支持16个汉字 | 白开水 | 是
optional | Map | 附加数据 | 用户自定义的参数，将会在webhook通知中原样返回，该字段主要用于商户携带订单的自定义数据 | {"key1":"value1","key2":"value2",...} | 否
return_url | String | 同步返回页面| 支付渠道处理完请求后,当前页面自动跳转到商户网站里指定页面的http路径，必须为http://或者https://开头 | beecloud.cn/returnUrl.jsp | 当channel参数为 ALI\_WEB 或 ALI\_QRCODE 或 UN\_WEB时为必填

> 注：channel的参数值含义：  
WX\_APP: 微信手机原生APP支付  
WX\_NATIVE: 微信公众号二维码支付  
WX\_JSAPI: 微信公众号支付  
ALI\_APP: 支付宝手机原生APP支付  
ALI\_WEB: 支付宝PC网页支付  
ALI\_QRCODE: 支付宝内嵌二维码支付  
ALI\_OFFLINE_QRCODE: 支付宝线下二维码支付  
ALI\_WAP: 支付宝移动网页支付  
UN\_APP: 银联手机原生APP支付  
UN\_WEB: 银联PC网页支付  
JD\_WAP: 京东移动网页支付  
JD\_WEB: 京东PC网页支付  
YEE\_WAP: 易宝移动网页支付   
YEE\_WEB: 易宝PC网页支付  
KUAIQIAN\_WAP: 快钱移动网页支付  
KUAIQIAN\_WEB: 快钱PC网页支  
BD\_WAP: 百度移动网页支付  
BD\_WEB: 百度PC网页支付

  

- 以下是`微信公众号支付(WX_JSAPI)`的**<mark>必填</mark>**参数

参数名 | 类型 | 含义 | 例子
---- | ---- | ---- | ----
openid| String | 用户相对于微信公众号的唯一id | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb

- 以下是`支付宝网页支付(ALI_WEB)`的**<mark>选填</mark>**参数

参数名 | 类型 | 含义 | 例子
---- | ---- | ---- | ----
show_url| String | 商品展示地址以http://开头 | http://beecloud.cn

- 以下是`支付宝内嵌二维码支付(ALI_QRCODE)`的**<mark>选填</mark>**参数

参数名 | 类型 | 含义 | 例子
---- | ---- | ---- | ----
qr\_pay\_mode| String | 二维码类型 | 0,1,3

- 以下是`易宝移动网页支付(YEE_WAP)`的**<mark>必填</mark>**参数

参数名 | 类型 | 含义 
---- | ---- | ----
identity_id | String | 50位以内数字和/或字母组合，易宝移动网页（一键）支付用户唯一标识符，用于绑定用户一键支付的银行卡信息

> 注： 二维码类型含义   
0： 订单码-简约前置模式, 对应 iframe 宽度不能小于 600px, 高度不能小于 300px   
1： 订单码-前置模式, 对应 iframe 宽度不能小于 300px, 高度不能小于 600px  
3： 订单码-迷你前置模式, 对应 iframe 宽度不能小于 75px, 高度不能小于 75px  

返回结果（Object类型）:

- 公共返回参数

参数名 | 类型 | 含义 
---- | ---- | ----
result\_code | Integer | 返回码，0为正常
result\_msg  | String | 返回信息，OK为正常
err\_detail  | String | 具体错误信息

- 公共返回参数取值及含义参见支付公共返回参数部分, 以下是退款所特有的

result\_code | result\_msg                | 含义
----        | ----      			       | ----
8           | NO\_SUCH_BILL             | 没有该订单
9           | BILL\_UNSUCCESS            | 该订单没有支付成功
10          | REFUND\_EXCEED\_TIME       | 已超过可退款时间
11          | ALREADY\_REFUNDING         | 该订单已有正在处理中的退款
12          | REFUND\_AMOUNT\_TOO\_LARGE | 提交的退款金额超出可退额度
13          | NO\_SUCH\_REFUND           | 没有该退款记录

**当channel为`ALI_APP`、`ALI_WEB`、`ALI_QRCODE`时，以下字段在result_code为0时有返回**
 
参数名 | 类型 | 含义 
---- | ---- | ----
url | String | 支付宝退款地址，需用户在支付宝平台上手动输入支付密码处理


- 以下是`微信公众号支付(WX_JSAPI)`的**<mark>必填</mark>**参数

参数名 | 类型 | 含义 | 例子
---- | ---- | ---- | ----
openid| String | 用户相对于微信公众号的唯一id | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb

- 以下是`支付宝网页支付(ALI_WEB)`的**<mark>选填</mark>**参数

参数名 | 类型 | 含义 | 例子
---- | ---- | ---- | ----
show_url| String | 商品展示地址以http://开头 | http://beecloud.cn

- 以下是`支付宝内嵌二维码支付(ALI_QRCODE)`的**<mark>选填</mark>**参数

参数名 | 类型 | 含义 | 例子
---- | ---- | ---- | ----
qr\_pay\_mode| String | 二维码类型 | 0,1,3

> 注： 二维码类型含义   
0： 订单码-简约前置模式, 对应 iframe 宽度不能小于 600px, 高度不能小于 300px   
1： 订单码-前置模式, 对应 iframe 宽度不能小于 300px, 高度不能小于 600px  
3： 订单码-迷你前置模式, 对应 iframe 宽度不能小于 75px, 高度不能小于 75px  

返回结果（Object类型）:

- 公共返回参数

参数名 | 类型 | 含义 
---- | ---- | ----
result\_code | Integer | 返回码，0为正常
result\_msg  | String | 返回信息，OK为正常
err\_detail  | String | 具体错误信息

- 公共返回参数取值及含义参见支付公共返回参数部分, 以下是退款所特有的

result\_code | result\_msg                | 含义
----        | ----      			       | ----
8           | NO\_SUCH_BILL             | 没有该订单
9           | BILL\_UNSUCCESS            | 该订单没有支付成功
10          | REFUND\_EXCEED\_TIME       | 已超过可退款时间
11          | ALREADY\_REFUNDING         | 该订单已有正在处理中的退款
12          | REFUND\_AMOUNT\_TOO\_LARGE | 提交的退款金额超出可退额度
13          | NO\_SUCH\_REFUND           | 没有该退款记录

**当channel为`ALI_APP`、`ALI_WEB`、`ALI_QRCODE`时，以下字段在result_code为0时有返回**
 
参数名 | 类型 | 含义 
---- | ---- | ----
url | String | 支付宝退款地址，需用户在支付宝平台上手动输入支付密码处理


参数名 | 类型 | 含义 
---- | ---- | ----
result_code | Integer | 返回码，0为正常
result_msg  | String | 返回信息， OK为正常
err_detail  | String | 具体错误信息

- **公共返回参数取值列表及其含义**

result_code | result_msg             | 含义
----        | ----      		       | ----
0           | OK                     | 调用成功
1           | APP\_INVALID           | 根据app\_id找不到对应的APP或者app\_sign不正确
2           | PAY\_FACTOR_NOT\_SET   | 支付要素在后台没有设置
3           | CHANNEL\_INVALID       | channel参数不合法
4           | MISS\_PARAM            | 缺少必填参数
5           | PARAM\_INVALID         | 参数不合法
6           | CERT\_FILE\_ERROR      | 证书错误
7           | CHANNEL\_ERROR         | 渠道内部错误
14          | RUN\_TIME_ERROR        | 实时未知错误，请与技术联系帮助查看

> **当result_code不为0时，如需详细信息，请查看err\_detail字段**

**<mark>以下字段在result_code为0时有返回</mark>**

- WX_APP

参数名 | 类型 | 含义 
---- | ---- | ----
app_id | String | 微信应用APPID
partner_id | String | 微信支付商户号
package  | String | 微信支付打包参数
nonce_str  | String | 随机字符串
timestamp | String | 当前时间戳，单位是毫秒，13位
pay_sign  | String | 签名值
prepay_id  | String | 微信预支付id

- WX_NATIVE

参数名 | 类型 | 含义 
---- | ---- | ----
code_url | String | 二维码地址

- WX_JSAPI

参数名 | 类型 | 含义 
---- | ---- | ----
app_id | String | 微信应用APPID
package  | String | 微信支付打包参数
nonce_str  | String | 随机字符串
timestamp | String | 当前时间戳，单位是毫秒，13位
pay_sign  | String | 签名
sign_type  | String | 签名类型，固定为MD5

- ALI_APP

参数名 | 类型 | 含义 
---- | ---- | ----
order\_string | String | 支付宝签名串

- ALI_WEB

参数名 | 类型 | 含义 
---- | ---- | ----
html | String | 支付宝跳转form，是一段HTML代码，自动提交
url  | String | 支付宝跳转url，推荐使用html

- ALI_OFFLINE_QRCODE

参数名 | 类型 | 含义 
---- | ---- | ----
qr_code | String | 二维码码串,可以用二维码生成工具根据该码串值生成对应的二维码

- ALI_QRCODE

参数名 | 类型 | 含义 
---- | ---- | ----
html | String | 支付宝跳转form，是一段HTML代码，自动提交
url  | String | 支付宝内嵌二维码地址，是一个URL

- UN_APP

参数名 | 类型 | 含义 
---- | ---- | ----
tn | String | 银联支付ticket number

- UN_WEB、JD_WAP、JD_WEB、KUAIQIAN_WAP、KUAIQIAN_WEB

参数名 | 类型 | 含义 
---- | ---- | ----
html | String | 支付页自动提交form表单内容

- YEE_WAP、YEE_WEB、BD_WAP、BD_WEB

参数名 | 类型 | 含义 
---- | ---- | ----
url | String | 支付页跳转地址
    
   
    
## 查询支付订单

~~~
\beecloud\rest\api::bills(array $data);
~~~

不使用namespace的用户和2.2.0之前的v2版本用户请使用

~~~
BCRESTApi::bills(array $data);
~~~

data参数（array类型）:

参数名 | 类型 | 含义 | 描述 | 例子 | 是否必填
----  | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud应用APPID | BeeCloud的唯一标识 | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb | 是
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+app\_secret)，32位16进制格式,不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
channel| String | 渠道类型 | 根据不同场景选择不同的支付方式 | WX、WX\_APP、WX\_NATIVE、WX\_JSAPI、ALI、ALI\_APP、ALI\_WEB、ALI\_QRCODE、ALI\_OFFLINE\_QRCODE、ALI_WAP、UN、UN\_APP、UN\_WEB、JD_WAP、JD_WEB、YEE_WAP、YEE_WEB、KUAIQIAN_WAP、KUAIQIAN_WEB、BD_WAP、BD_WEB、JD、YEE、KUAIQIAN、BD(详见附注）| 否
bill_no | String | 商户订单号 | 发起支付时填写的订单号 | 201506101035040000001 | 否
start_time | Long | 起始时间 | 毫秒时间戳, 13位 | 1435890530000 | 否
end_time | Long | 结束时间 | 毫秒时间戳, 13位   | 1435890540000 | 否
skip | Integer| 查询起始位置 | 默认为0. 设置为10表示忽略满足条件的前10条数据| 0 | 否
limit| Integer | 查询的条数 | 默认为10，最大为50. 设置为10表示只返回满足条件的10条数据 | 10 | 否

> 注：  
1. bill\_no, trace\_id, start\_time, end\_time等查询条件互相为**<mark>且</mark>**关系  
2. start\_time, end\_time指的是订单生成的时间，而不是订单支付的时间   

返回结果（Object类型）:

- 公共返回参数

参数名 | 类型 | 含义 
---- | ---- | ----
result\_code | Integer| 返回码，0为正常
result\_msg  | String | 返回信息， OK为正常
err\_detail  | String | 具体错误信息
count | Integer | 查询订单结果数量
bills | List<Map> | 订单列表

> 公共返回参数取值及含义参见支付公共返回参数部分  

- bills说明，每个Map的key\-value

参数名         | 类型          | 含义 
----          | ----         | ----
bill\_no      | String       | 订单号
total\_fee    | Integer         | 订单金额，单位为分
channel       | String       | WX、WX\_NATIVE、WX\_JSAPI、WX\_APP、ALI、ALI\_APP、ALI\_WEB、ALI\_QRCODE、ALI\_OFFLINE_QRCODE、ALI_WAP、UN、UN\_APP、UN\_WEB(详见 1. 支付 附注）
title         | String       | 订单标题
spay\_result  | Bool         | 订单是否成功
created\_time | Long         | 订单创建时间, 毫秒时间戳, 13位
    
## 发起退款 

~~~
\beecloud\rest\api::refund(array $data);
~~~

不使用namespace的用户和2.2.0之前的v2版本用户请使用

~~~
BCRESTApi::refund(array $data);
~~~

data参数（array类型）:

参数名 | 类型 | 含义   | 描述 | 例子 | 是否必填 |
---- | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud应用APPID | BeeCloud的唯一标识 | 0950c062\-5e41\-44e3\-8f52\-f89d8cf2b6eb | 是 
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+app\_secret)，32位16进制格式,不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
channel| String | 渠道类型 | 根据不同渠道选不同的值 | WX ALI UN KUAIQIAN YEE JD BD| 否
refund_no | String | 商户退款单号 | 格式为:退款日期(8位) + 流水号(3~24 位)。请自行确保在商户系统中唯一，且退款日期必须是发起退款的当天日期,同一退款单号不可重复提交，否则会造成退款单重复。流水号可以接受数字或英文字符，建议使用数字，但不可接受“000” | 201506101035040000001 | 是
bill_no | String | 商户订单号 | 发起支付时填写的订单号 | 201506101035040000001 | 是 
refund_fee | Integer | 退款金额 | 必须为正整数，单位为分，必须小于或等于对应的已支付订单的total_fee | 1 | 是
optional | Map | 附加数据 | 用户自定义的参数，将会在webhook通知中原样返回，该字段主要用于商户携带订单的自定义数据 | {"key1":"value1","key2":"value2",...} | 否

返回结果（Object类型）:
- 公共返回参数

参数名 | 类型 | 含义 
---- | ---- | ----
result\_code | Integer | 返回码，0为正常
result\_msg  | String | 返回信息，OK为正常
err\_detail  | String | 具体错误信息

- 公共返回参数取值及含义参见支付公共返回参数部分, 以下是退款所特有的

result\_code | result\_msg                | 含义
----        | ----      			       | ----
8           | NO\_SUCH_BILL             | 没有该订单
9           | BILL\_UNSUCCESS            | 该订单没有支付成功
10          | REFUND\_EXCEED\_TIME       | 已超过可退款时间
11          | ALREADY\_REFUNDING         | 该订单已有正在处理中的退款
12          | REFUND\_AMOUNT\_TOO\_LARGE | 提交的退款金额超出可退额度
13          | NO\_SUCH\_REFUND           | 没有该退款记录

**当channel为`ALI_APP`、`ALI_WEB`、`ALI_QRCODE`时，以下字段在result_code为0时有返回**
 
参数名 | 类型 | 含义 
---- | ---- | ----
url | String | 支付宝退款地址，需用户在支付宝平台上手动输入支付密码处理

	
## 退款状态查询

~~~
\beecloud\rest\api::refunds(array $data);
~~~

不使用namespace的用户和2.2.0之前的v2版本用户请使用

~~~
BCRESTApi::refunds(array $data);
~~~

data参数（array类型）:

参数名 | 类型 | 含义 | 描述 | 例子 | 是否必填
----  | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud应用APPID | BeeCloud的唯一标识 | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb | 是
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+app\_secret)，不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
channel| String | 渠道类型 | 根据不同场景选择不同的支付方式 | WX、WX\_APP、WX\_NATIVE、WX\_JSAPI、ALI、ALI\_APP、ALI\_WEB、ALI\_QRCODE、ALI\_OFFLINE\_QRCODE、ALI_WAP、UN、UN\_APP、UN\_WEB、JD\_WAP、JD\_WEB、YEE\_WAP、YEE\_WEB、KUAIQIAN\_WAP、KUAIQIAN\_WEB、BD\_WAP、BD\_WEB、JD、YEE、KUAIQIAN、BD(详见附注）| 否
bill_no | String | 商户订单号 | 发起支付时填写的订单号 | 201506101035040000001 | 否
refund_no | String | 商户退款单号 | 发起退款时填写的退款单号 | 201506101035040000001 | 否
start_time | Long | 起始时间 | 毫秒时间戳, 13位 | 1435890530000 | 否
end_time | Long | 结束时间 | 毫秒时间戳, 13位   | 1435890540000 | 否
skip | Integer | 查询起始位置 | 默认为0. 设置为10，表示忽略满足条件的前10条数据| 0 | 否
limit| Integer | 查询的条数 | 默认为10，最大为50. 设置为10，表示只查询满足条件的10条数据 | 10 | 否


> 注：  
1. bill\_no, refund\_no, start\_time, end\_time等查询条件互相为**<mark>且</mark>**关系.   
2. start\_time, end\_time指的是订单生成的时间，而不是订单支付的时间.   

返回结果（Object类型）:

- 公共返回参数

参数名 | 类型 | 含义 
---- | ---- | ----
result\_code | Integer| 返回码，0为正常
result\_msg  | String | 返回信息， OK为正常
err\_detail  | String | 具体错误信息
count | Integer | 查询退款结果数量
refunds | List<Map> | 退款列表

> 公共返回参数取值及含义参见支付公共返回参数部分

- refunds说明，每个Map的key\-value

参数名      | 类型         | 含义 
----       | ----        | ----
bill\_no    | String      | 订单号
refund\_no  | String      | 退款号
total\_fee  | Integer      | 订单金额，单位为分
refund\_fee | Integer      | 退款金额，单位为分
title         | String       | 订单标题
channel    | String      |  WX、WX\_APP、WX\_NATIVE、WX\_JSAPI、ALI、ALI\_APP、ALI\_WEB、ALI\_QRCODE、ALI\_OFFLINE\_QRCODE、ALI_WAP、UN、UN\_APP、UN\_WEB、JD\_WAP、JD\_WEB、YEE\_WAP、YEE\_WEB、KUAIQIAN\_WAP、KUAIQIAN\_WEB、BD\_WAP、BD\_WEB、JD、YEE、KUAIQIAN、BD(详见 1. 支付 附注）
finish     | bool        | 退款是否完成
result     | bool        | 退款是否成功
created\_time | Long       | 退款创建时间, 毫秒时间戳, 13位



## 退款状态更新(仅微信需要) 

~~~
\beecloud\rest\api::refundStatus(array $data);
~~~

不使用namespace的用户和2.2.0之前的v2版本用户请使用

~~~
BCRESTApi::refundStatus(array $data);
~~~

data参数（array类型）:

参数名 | 类型 | 含义 | 描述 | 例子 | 是否必填
----  | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud应用APPID | BeeCloud的唯一标识 | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb | 是
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+app\_secret)，32位16进制格式，不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
channel| String | 渠道类型 | 根据不同场景选择不同的支付方式 | 目前只支持WX | 是
refund_no | String | 商户退款单号 | 发起退款时填写的退款单号 | 201506101035040000001 | 是

返回结果（Object类型）:

- 公共返回参数

参数名 | 类型 | 含义 
---- | ---- | ----
result\_code | Integer | 返回码，0为正常
result\_msg  | String | 返回信息， OK为正常
err\_detail  | String | 具体错误信息
refund_status | String | 退款状态

> 公共返回参数取值及含义参见支付公共返回参数部分

## 单笔打款

~~~
\beecloud\rest\api::transfer(array $data);
~~~

不使用namespace的用户和2.2.0之前的v2版本用户请使用

~~~
BCRESTApi::transfer(array $data);
~~~

data参数（array类型）:

 参数名 | 类型 | 含义 | 描述 | 例子 | 是否必填
----  | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud平台的AppID | App在BeeCloud平台的唯一标识 | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb | 是
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+**app\_secret**)，32位16进制格式,不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
channel| String | 渠道类型 | 根据不同场景选择不同的支付方式 | WX_REDPACK, WX\_TRANSFER, ALI\_TRANSFER(详见附注）| 是
transfer_no | String | 打款单号 | 支付宝为11-32位数字字母组合， 微信为10位数字 | udjfiienx2334/8372839123 | 是
total_fee | Int | 打款金额 | 此次打款的金额,单位分,正整数(微信红包1.00-200元，微信打款>=1元) | 1 | 是
desc | String | 打款说明 | 此次打款的说明 | 赔偿 | 是
channel_user\_id | String | 用户id | 支付渠道方内收款人的标示, 微信为openid, 支付宝为支付宝账户 | someone@126.com |是
channel_user\_name | String | 用户名| 支付渠道内收款人账户名， 支付宝必填 | 支付宝某人 | 否
redpack_info | Object | 红包信息 | 微信红包的详细描述，详见附注, 微信红包必填 | - | 否
account_name|String|打款方账号名称|打款方账号名全称，支付宝必填|苏州比可网络科技有限公司|否



>注1：channel的参数值含义：  
WX\_REDPACK: 微信红包  
WX\_TRANSFER: 微信企业打款  
ALI_TRANSFER: 支付宝企业打款 

> 注2: redpack_info 参数列表
 
 参数名 | 类型 | 含义 | 例子
---- | ---- | ---- | ----
send_name| String | 红包发送者名称 32位 | BeeCloud
wishing | String | 红包祝福语 128 位| BeeCloud祝福开发者工作顺利!
act_name | String | 红包活动名称 32位 | BeeCloud开发者红包轰动

返回结果 (JSON, Map)
 
 参数名 | 类型 | 含义 
---- | ---- | ----
result_code | Integer | 返回码，0为正常
result_msg  | String | 返回信息， OK为正常
err_detail  | String | 具体错误信息
url | String | 支付宝需要跳转到支付宝链接输入支付密码确认
 
> 注1: 错误码（错误详细信息 参考 **err_detail**字段)
 
 result_code | result_msg             | 含义
----        | ----      		       | ----
0           | OK                     | 调用成功
1           | APP\_INVALID           | 根据app\_id找不到对应的APP或者app\_sign不正确
2           | PAY\_FACTOR_NOT\_SET   | 支付要素在后台没有设置
3           | CHANNEL\_INVALID       | channel参数不合法
4           | MISS\_PARAM            | 缺少必填参数
5           | PARAM\_INVALID         | 参数不合法
6           | CERT\_FILE\_ERROR      | 证书错误
7           | CHANNEL\_ERROR         | 渠道内部错误
14          | RUN\_TIME_ERROR        | 实时未知错误，请与技术联系帮助查看

## 批量打款

~~~
\beecloud\rest\api::transfers(array $data);
~~~

不使用namespace的用户和2.2.0之前的v2版本用户请使用

~~~
BCRESTApi::transfers(array $data);
~~~
    
data参数（array类型）:

参数名 | 类型 | 含义 | 描述 | 例子 | 是否必填
----  | ---- | ---- | ---- | ---- | ----
app_id | String | BeeCloud应用APPID | BeeCloud的唯一标识 | 0950c062-5e41-44e3-8f52-f89d8cf2b6eb | 是
timestamp | Long | 签名生成时间 | 时间戳，毫秒数 | 1435890533866 | 是
app_sign | String | 加密签名 | 算法: md5(app\_id+timestamp+app_key)，32位16进制格式，不区分大小写 | b927899dda6f9a04afc57f21ddf69d69 | 是
channel| String | 渠道类型 | ---- | 目前只支持ALI | 是
batch_no | String | 批量付款批号 | 此次批量付款的唯一标示，11-32位数字字母组合 | 201506101035040000001 | 是
account_name | String | 付款方的支付宝账户名 | 支付宝账户名称 | 毛毛 | 是
transfer_data | List<Map> | 付款的详细数据 | 每一个Map对应一笔付款的详细数据, list size 小于等于 1000。 Map的参数结构如下表 | 是

transfer_data:

参数名 | 类型 | 含义 | 例子 
----  | ---- | ---- | ---- 
transfer_id | String | 付款流水号，32位以内数字字母 | 1507290001
receiver_account | String | 收款方支付宝账号 | someone@126.com
receiver_name | String | 收款方支付宝账户名 | 某某人必须和支付宝账号的一致
transfer_fee | int | 付款金额，单位为分 | 100
transfer_note | String | 付款备注 | 打赏

返回结果（Object类型）:

参数名 | 类型 | 含义 
---- | ---- | ----
result\_code | Integer | 返回码，0为正常
result\_msg  | String | 返回信息， OK为正常
err\_detail  | String | 具体错误信息
url | String | 需要跳转到支付宝输入密码确认批量打款

### FAQ

+ BeeCloud企业认证后还需要做各个渠道的注册么？
	
BeeCloud还未提供代理申请各渠道支付账户和资质服务，企业认证只确保用户信息真实，支付渠道还需要用户自己申请
  

+ 页面不是预期结果如何捕获代码错误？
	
代码请使用try catch处理异常情况，并对返回的数据结果错误做处理
	
~~~
try {
    $result = \beecloud\rest\api::transfers($data);
    if ($result->result_code != 0) {
    	 //返回结果提示错误，此处显示错误或者打印到log中
        echo json_encode($result);
        exit();
    }

    $htmlContent = $result->html;
    $url = $result->url;
    echo $url."<br>";
    echo $htmlContent;
} catch (Exception $e) {
	 //处理异常情况
    echo $e->getMessage();
}
?>
~~~
	

+ 如何获取特定订单支付结果

1.使用bills接口，指定bill_no参数可以查询

2.服务器上处理webhook消息，异步被动通知获取到结果

3.return_url参数指定支付页面完成后的跳转的url，在url指定的页面中处理（强烈不建议，客户可能关闭页面导致不成功）

+ return_url, webhook区别， 支付宝参数中为啥没有notify_url
    
1.return_url为商品支付完成后，在支付方浏览器自动跳转访问的地址
2.webhook集中处理各渠道异步通知结果，然后再转发支付结果到你指定的webhook的url中，使用方式请前往[webhook指南](https://github.com/beecloud/beecloud-webhook)

+ 如何处理webhook
   
请参考demo文件夹下webhook.php的处理方式

+ 微信公众号支付无法调起(demo/wx.jsapi.php)

1. 请检查获取到的jsApiParams是否正确,不正确可能BeeCloud的APP下微信公众号的支付参数填写错误
2. jsApiParams正确，请将js中alert打开Debug：

~~~
....
WeixinJSBridge.invoke(
    'getBrandWCPayRequest',
    <?php echo json_encode($jsApiParam);?>,
    function(res){
        WeixinJSBridge.log(res.err_msg);
         //下面这行
            //alert(res.err_code+res.err_desc+res.err_msg);
    }
);
....
~~~

提示无支付权限请检查微信公众平台下”微信支付-＞支付授权目录“是否设置了文件所在目录，只有授权目录下的文件才能发起支付

提示openid不正确，请检查”开发者中心->网页服务->网页授权获取用户基本信息"的域名是否设置正确，并检查你是否正确获取到了openid


+ 微信公众平台内扫码支付回调URL是否需要设置

BeeCloud使用的扫码模式二，扫码结果也会通过webhook传递；

扫码支付回调URL是模式一中需要填写的，故如果你使用BeeCloud实现的扫码不需要再设置该url


+ 常见BeeCloud错误提示定位
	
```
支付宝BeeCloud的Demo提示"ILLEGAL_PARTNER": 
由于支付宝不允许跨域调用支付功能，在非beecloud域名下发起支付会被加入黑名单，所以我们不再提供真实的参数，请替换为自己的参数后测试
```

```
支付宝即时到账提示"illegal_partner_exterface":
支付宝对应产品未开通,[参考问题](http://www.oschina.net/question/163899_23976)
```

```
xxx字段必填：
PHP接口中$data中必填参数未填写
```

```
字段不合法，需要xxx类型：
$data参数字段有类型要求，请对照文档中的说明确认类型
```

```
微信提示"CHANNEL_ERROR:签名错误",微信提示"CHANNEL_ERROR:渠道方错误":  
请确认BeeCloud微信公众号的参数和证书正确，微信APPID和证书密码对应,证书和API密码应该是从微信商户平台下获取的
```

```
支付宝支付跳转后提示"ALI59": 
bill_no字段只能是字母和数字组合
```

```
银联支付跳转后提示"HTTP Status 400 - Invalid request": 
请确认BeeCloud的银联参数填写正确，根据[银联文档](http://7xavqo.com1.z0.glb.clouddn.com/证书下载、导出及上传流程.docx)确认证书正确
```

```
银联跳转后提示"Signature verification failed": 
请确认使用的证书为生产证书而非测试证书，并且证书密码正确
```

## 联系我们
- 如果有什么问题，可以到BeeCloud开发者1群:**321545822** 或 BeeCloud开发者2群:**427128840** 提问
- 如果发现了bug，欢迎提交[issue](https://github.com/beecloud/beecloud-webhook/issues)
- 如果有新的需求，欢迎提交[issue](https://github.com/beecloud/beecloud-webhook/issues)


## 代码许可
The MIT License (MIT).	
