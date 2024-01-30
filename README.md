[!['Build Status'](https://travis-ci.org/shopwwi/admin.svg?branch=main)](https://github.com/shopwwi/admin) [!['Latest Stable Version'](https://poser.pugx.org/shopwwi/admin/v/stable.svg)](https://packagist.org/packages/shopwwi/admin) [!['Total Downloads'](https://poser.pugx.org/shopwwi/admin/d/total.svg)](https://packagist.org/packages/shopwwi/admin) [!['License'](https://poser.pugx.org/shopwwi/admin/license.svg)](https://packagist.org/packages/shopwwi/admin)

# 项目介绍
ShopwwiAdmin是为了方便开发人员快速扩展其它应用而开发的，你可以很方便的使用此管理系统进行快速开发，甚至你都不需要懂前端就能完成一个系统的开发,基于此你可以在一周内甚至更多的时间完成一个CMS的开发 单用户商城系统的开发 教育类站点的开发等。

* 内置支付系统，直接调用就可完成整套支付体系
* 内置第三方账号同步，无需在额外区支持微信登入 QQ登入等
* 内置附件云存储，支持阿里云 腾讯云 七牛云 本地 FTP等
* 内置会员体系
* 内置会员积分体系
* 内置会员等级体系
* 内置会员成长值体系
* 内置会员充值 提现 变动等
* 内置会员消息通知 提醒
* 内置短信 邮件功能
* 内置文章管理 帮助管理 系统公告 友情链接
* 内置敏感词过滤
* 内置地区 运费模板 物流等

# 使用技术
系统默认使用半分离式架构，如需使用分离式无需在额外开发接口！ 内置多种状态来获取不同的数据类型 _format=json|data|web
* 后端使用webman + laravel orm
* 前端使用 VUE3 + AMIS + TDesign


# 安装

```
composer require shopwwi/admin
```

# 使用方法
1. 如果你未安装webman主体框架 需先安装webman框架 并启动webman项目
```
composer create-project workerman/webman 项目名称
```

2. 访问 //127.0.0.1:8787/admin/install进行安装

# 捐赠
赠人玫瑰，手留余香！SHOPWWI诚挚地邀请大家积极参与捐赠，让善意无限传递下去！ 在此深表感谢~

# 特别鸣谢
排名不分先后，感谢这些软件的开发者：webman、laravel、vue、amis、TDesign、mysql、redis、uniapp、echarts、等，如有遗漏请联系！

# 使用须知
* 允许用于个人学习、毕业设计、教学案例、公益事业、商业使用；
* 如果商用必须保留版权信息，请自觉遵守；
* 禁止将本项目的代码和资源进行任何形式的出售，产生的一切任何后果责任由侵权者自负。

# 版权信息
版权所有Copyright © 2020-2024 by SHOPWWI(http://www.shopwwi.com)
All rights reserved。
SHOPWWI® 商标和著作权所有者为无锡豚豹科技有限公司。