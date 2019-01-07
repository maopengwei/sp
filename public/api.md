速聘分销接口

host
    10.10.10.108
post
    1000



选厂入职   post   index/user/inJob
        工厂id

登录  post   index/user/login  
短信  post   index/total/send 不带tk
            类型  type:reg
            手机号 us_tel
注册  post   index/login/reg  不带tk
            姓名  us_real_name
            手机号  us_tel
            验证码   sode
            登录密码  us_pwd
            身份证   us_card_id
忘记密码  post   index/login/forget
            手机号   us_tel
            验证码    sode
            登陆密码  us_pwd
修改信息  post  index/user/edit
        姓名  us_real_name
        身份账号：us_card_id
        生日     us_birthday
        性别     us_sex
        学历     us_edu
        银行卡    us_bank_number
        支付宝    us_alipay
        微信      us_wechat
        头像      us_head_pic

修改密码  post   index/user/pass
            原密码   old_pwd
            新密码   us_pwd

上传图片  post    index/total/uploads 不带tk
            图片   img

城市列表    post   index/prod/area
视频        post   index/prod/video
工厂列表    post   index/prod/index
        地域      prod_area 
        产品名称   prod_name
        工种       gong  0,1,2,3

工厂详情   post   index/prod/detail
            工厂id    id
入职记录  post  index/user/ru

雅琥赏金   post   index/profit/msc


团队首页   post   index/user/team
门店输送   post  index/user/mensong
修改背景图   post    index/user/pic_cha
区代    post    index/user/qu
新闻详情 post index/news/xq
新闻id    id
新闻列表   post  index/news/index 

速聘直推   post   index/user/dir_sp
雅琥直推   post   index/user/dir_yh
name


支付宝支付   alipay/index
            用户id  us_id
            金额    num
            礼包id  relevance
            类型  type


玩家剩余次数  red/index

摇一摇请求金额 red/yao

摇完领取红包   red/ling
            金额  money