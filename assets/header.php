<script src="/js/transport1.js"></script>
<script language="javascript">
    <!--
/*屏蔽所有的js错误*/
function killerrors() { 
return true; 
} 
window.onerror = killerrors; 
//-->
  var process_request = "正在处理您的请求...";
</script>
<link rel="stylesheet" type="text/css" href="/themes/baian/images/misc/lib/skin/2012/base.css" media="all" />
<link rel="stylesheet" type="text/css" href="/themes/baian/css/adv.css">
<style>
  .qq_jiesuan, .qq_jiesuan_on {
  position:relative;
  cursor:pointer;
  z-index:1000;
  }
  .qq_jiesuan .jiesuan_t {
  position:absolute;
  width:300px;
  border:1px solid #e6e6e6;
  top:30px;
  right:0;
  text-align:center;
  color:#999;
  display:none;
  }
  #settleup .qq_jiesuan_on dt{background-position:0 -284px;}
  .qq_jiesuan_on .jiesuan_t {
  position:absolute;
  width:268px;
  border:1px solid #e6e6e6; border-top:none;
  top:32px;
  right:0;
  text-align:center;
  color:#999;
  display:block;
  background:url(/themes/baian/images/cart_top_line.png) #fff no-repeat left top;
  z-index:2000;
  }
  .cart_info_div {
  float: left;
  font-size:12px;
  width:260px;
  height:94px;
  overflow:hidden; margin-left:3px;
  border-bottom:#ddd 1px dotted;
  }
  .cart_info_div a.img_l {
  float:left;
  display:block;
  border:1px solid #e4e4e4;
  margin:5px;
  width:82px;
  height:82px;
  }
  .cart_info_div .f_right {
  width:160px;
  float:right;
  padding:5px 0px;
  }
  .cart_info_div .f_right p.name {
  border:0;
  height:36px;
  line-height:40px;
  overflow:hidden;
  }
  .cart_info_div .f_right p.name a {
  text-align:left;
  border:0;
  color:#1f1f1f;
  }
  .cart_info_div .f_right p a {
  color:#1f1f1f;
  font-size:12px;
  height:30px;
  line-height:30px
  }
  .cart_info_div .f_right p.cheng {
  color:#1f1f1f;
  }
  .cart_info_div .f_right p font {
  color:#1f1f1f;
  font-size:12px
  }
  .cart_info_div .f_right p {
  line-height:20px;
  height:16px;
  overflow:hidden;
  text-align:left;
  }
  .qq_jiesuan_on .jiesuan_t p.btm {
  line-height:17px;
  text-align:right;
  color:#161616;
  }
  .qq_jiesuan_on .jiesuan_t p.btm font {
  color:#C00;
  font-weight:bold;
  font-size:12px;
  }
  .blank5 {
  height:5px;
  line-height:5px;
  overflow:hidden;
  clear:both
  }

  .divJeepDiv
  {
  left: 0px;
  top: 0px;
  opacity: 0.1;
  background: #000;
  filter: alpha(opacity=10);
  overflow: hidden;
  position: absolute;
  height: 100%;
  width: 100%;
  z-index: 1000;

  }
</style>

  <!--<div class="divJeepDiv"></div>-->

  <div id="shortcut">
    <div class="w">
      <ul class="fl lh">
        <li class="fore1 ld">
          <script type="text/javascript" src="/js/common.min.js?0705"></script> <font id="ECS_MEMBERZONE"> 
<font id="hyxin">您好，欢迎来到百安母婴商城</font> &nbsp;&nbsp; <a id="logA" style="color:red;" href="/user.php">登录</a>&nbsp;|&nbsp;<a id="regA" style="color:red;" href="/user.php?act=register">免费注册</a>
<!--
<a id="qq_login" title="用QQ账号登录" href="/login/">qq登录</a>
<a id="qq_login" title="用QQ账号登录" href="/login/">qq登录</a>
<a id="qq_login" title="用QQ账号登录" href="/login/">qq登录</a>
<a id="qq_login" title="用QQ账号登录" href="/login/">qq登录</a>
<a id="qq_login" title="用QQ账号登录" href="/login/">qq登录</a>
<a id="qq_login" title="用QQ账号登录" href="/login/">qq登录</a>-->
 </font>
        </li>
      </ul>
      <ul class="fr lh">
        <!--<li class="fore1 ld loginbar" style="width: 20px;">
          <b id="gouwuchetu"></b>
        </li>
        <li class="fore1 ld loginbar" style="padding-left: 0px;">
          <a href="/flow.php">购物车</a>
        </li>
        <li class="fore1 ld loginbar">|</li>
        <li class="fore1 ld loginbar">
          <a href="/flow.php">去结算</a>
        </li>
        <li class="fore1 ld loginbar">|</li>-->
        <li class="fore6 menu menu2">
          <!--<a target="_blank">安装手机客户端</a>-->
          <dl>
            <dt class="ld" style="width:100px;">
              <a href="/#" target="_blank">
                下载手机客户端
              </a>
            </dt>
            <dd class="hideMenu" style="width:250px;">
              <div>
              	<div style="float:left;">
              		<img alt="" src="/themes/baian/images/download_app.png">
              	</div>
                <div style="padding-left:7px;float:left;font-size:16px;color:rgb(253,100,27);">
                	&nbsp;&nbsp;百安客户端
                	<a style="width:97px;height:29px;padding-top:15px;display:block;" href="/download/Android.apk"><img src="/themes/baian/images/app_android.jpg"></a>
                	<a style="width:97px;height:29px;padding-top:15px;display:block;" href="/download/iPhone.ipa"><img src="/themes/baian/images/app_apple.jpg"></a>
                </div>
              </div>
            </dd>
          </dl>
        </li>
        <li class="fore1 ld loginbar">|</li>
        <li class="fore1 ld loginbar">
          <a href="/user.php?act=order_list" target="_blank">我的订单</a>
        </li>
        <!--<li class="fore1 ld loginbar">|</li>
        <li class="fore1 ld loginbar">
          <a target="_blank" href="http://www.kuaidi100.com/">快递查询</a>
        </li>-->

        <li class="fore1 ld loginbar">|</li>
        <li class="fore6 menu"   id="JS_hide_topNav_menu_3"  onmouseover="showMenu(this,'topNav',3);" onmouseout="hideMenu(this,'topNav',3);">
          <dl>
            <dt class="ld">
              <a href="/#" target="_blank">
                客户服务<b></b>
              </a>
            </dt>
            <dd class="hideMenu">
              <div>
                <a href="/baian/help.html" target="_blank">帮助中心</a>
              </div>
              <div>
                <a href="/help-114.html" target="_blank" rel="nofollow">售后服务</a>
              </div>
              <div>
                <a href="/dialog_1.htm" target="_blank" rel="nofollow">在线客服</a>
              </div>
              <div>
                <a href="/message.php" target="_blank" rel="nofollow">投诉中心</a>
              </div>
              <div>
                <a href="/service.html" target="_blank">客服邮箱</a>
              </div>
            </dd>
          </dl>
        </li>


        <li class="fore6 menu menu2" onmouseover="" onmouseout="">
          <dl>
            <dt class="ld">
              <span id="wxxl"></span>
              <a href="/#" target="_blank">
                微信商城
              </a>
            </dt>
            <dd class="hideMenus">
              <div>
                <img alt="" src="/themes/baian/images/ba_bottomerweima.jpg">
              </div>
            </dd>
          </dl>
        </li>
        
        
        
        <li class="fore1 ld loginbar" style="width: 25px;">
          <b id="wbxl"></b>
        </li>
        <li class="fore1 ld loginbar" style="padding-left: 0px;">
          <a href="http://weibo.com/baian123121" target="_blank">加关注</a>
        </li>
        <li class="fore1 ld loginbar" style="width: 22px;">
          <b id="wbtx"></b>
        </li>
        <li class="fore1 ld loginbar" style="padding-left: 0px;">
          <a href="http://t.qq.com/baianbaby" target="_blank">加关注</a>
        </li>
        <li class="fore1 ld loginbar" style="padding-left:4px;">
        	全国客服：4008-048-648
        </li>
      </ul>
      <span class="clr"></span>
    </div>
  </div>


  <div id="o-header">
    <div class="w" id="header">
      <div style="height:88px; float:left;">
        <div id="logo" class="ld">
          <a href="/" hidefocus="true" title="百安母婴商城是超低价正品母婴用品网购商城随时随地下单，儿童玩具,婴儿车,奶粉,儿童服装,纸尿裤等送货上门货到付款">
            <img src="/themes/baian/images/ba_logo.png" width="396" height="48" alt="百安母婴商城是超低价正品母婴用品网购商城随时随地下单，儿童玩具,婴儿车,奶粉,儿童服装,纸尿裤等送货上门货到付款" />
          </a>
        </div>
        <div id="zcjj" style="margin-left:55px;">
                        <a href="/affiche.php?ad_id=236&uri=http://www.123121.com/cuxiao/index.html" title="促销专场" target="_blank">
          <img src="/data/afficheimg/1405903962845826590.gif" width="200" height="88" />
        </a>
              </div>
      </div>

             
<div class="buydiv">
  <div id="my360buy">
    <dl class="">
      <dt class="ld">
        <s></s>
        <a href="/user.php" clstag="homepage|keycount|home2012|04a">我的百安</a>
        <b></b>
      </dt>
      <dd style="width: 108px;">
        <div class="prompt">
          <span class="fl">
                        您好，请<a style="color:red;" href="/user.php" clstag="homepage|keycount|home2013|04a">登录</a>
                      </span>
          <span class="fr"></span>
        </div>
              </dd>
    </dl>
  </div>
  <div id="settleup">
    <!--<dl class="">-->
      <dt class="ld">
        <s></s>
        <span class="shopping">
          <span id="shopping-amount">0</span>
        </span>
        <a href="/flow.php" id="settleup-url">去购物车结算</a>
        <b></b>
      </dt>
      <!--<dd>
        <div class="prompt">
          <div class="nogoods">
            <b></b>购物车中还没有商品，赶紧选购吧！
          </div>
        </div>
      </dd>-->
    <!--</dl>-->
  </div>
</div>      
      
      <div id="myxyd" style="height:88px; width:241px; float:right">
                        <div class="myx borri borbo "><a id="a1" style="text-decoration: none">信誉8年品牌</a></div>
<div class="myx borbo"><a id="a2" style="text-decoration: none">加微信享特价</a></div>
<div class="myx borri borbo"><a id="a3" style="text-decoration: none">注册送20元红包</a></div>
<div class="myx borbo"><a id="a4" style="text-decoration: none">100%正品保障</a></div>
<div class="myx borri">&nbsp;</div>
<div class="myx borbo">&nbsp;</div>
<p>&nbsp;</p>                <!--<div class="myx borri borbo">
          <a id="a1" style="text-decoration: none;">信誉10年品牌</a>
        </div>
        <div class="myx borbo">
          <a id="a2" style="text-decoration: none;">满额免运费</a>
        </div>
        <div class="myx borri">
          <a id="a3" style="text-decoration: none;">注册送20元劵</a>
        </div>
        <div class="myx">
          <a id="a4" style="text-decoration: none;">100%正品保障</a>
        </div>-->
      </div>
    </div>
		<style>
		#Js_tool{width:48px;position:fixed;bottom:120px;right:0;z-index:20000;}
		#Js_tool ul,#Js_tool li{margin:0;padding:0;list-style:none;}
		#Js_tool li{}
		#Js_tool li a{display:block;background:url('/themes/baian/images/shader_kefu.png') no-repeat;text-indent:-999999em;width:48px;height:50px;transition:ease .5s all;-webkit-transition:ease .5s all;-moz-transition:ease .5s all;-ms-transition:ease .5s all;-o-transition:ease .5s all;}
		
		#Js_tool li a.t_1{background-position:-62px -7px;}
		#Js_tool li a.t_1:hover{background-position:-8px -7px;}
		
		#Js_tool li a.t_2{background-position:-62px -58px;}
		#Js_tool li a.t_2:hover{background-position:-8px -58px;}
		
		#Js_tool li a.t_3{background-position:-62px -109px;}
		#Js_tool li a.t_3:hover{background-position:-8px -109px;}
		
		#Js_tool li a.t_4{background-position:-62px -160px;}
		#Js_tool li a.t_4:hover{background-position:-8px -160px;}
		
		#Js_tool li a.t_5{background-position:-62px -211px;}
		#Js_tool li a.t_5:hover{background-position:-8px -211px;}
		
		#Js_tool li a.t_6{background-position:-62px -262px;}
		#Js_tool li a.t_6:hover{background-position:-8px -262px;}
		
		#Js_tool li a.t_7{background-position:-62px -313px;}
		#Js_tool li a.t_7:hover{background-position:-8px -313px;}
		
		#Js_tool li a.t_8{background-position:-62px -364px;}
		#Js_tool li a.t_8:hover{background-position:-8px -364px;}
		</style>
		<div id="Js_tool">
			<ul>
				<li><a href="/flow.php" class="t_1">购物车</a></li>
				<li><a href="/user.php" class="t_2">购物车</a></li>
				<li><a href="/catalog.php" class="t_3">购物车</a></li>
				<li><a href="/dialog_1.htm" target="_blank" class="t_4">购物车</a></li>
				<li><a href="tencent://message/?uin=2658790390" class="t_5">购物车</a></li>
				<li>
					<div id="weixin" style="position:fixed;bottom:160px;right:51px;z-index:200000;display:none;"><img alt="" src="/themes/baian/images/ba_bottomerweima.jpg"></div>
					<a href="" class="t_6" onmouseover="document.getElementById('weixin').style.display='block';" onmouseout="document.getElementById('weixin').style.display='none';">购物车</a>
				</li>
				<li><a target="_blank" href="/baobao/index.php?ec_uid=0" class="t_7">购物车</a></li>
				<li id="backtop" style="display:none;"><a href="javascript:goTop();" class="t_8">购物车</a></li>
			</ul>
		</div>
    <div class="menus">
      <script>
	
        //var timmer;
        function showMc()
        {
        //clearTimeout(timmer);
        document.getElementById('categorys').className="hover";
        }
        function hideMc()
        {
        //timmer = setTimeout(function(){document.getElementById('categorys').className="";},700);
        document.getElementById('categorys').className="";
        }
      </script>

      <div class="w">
        <div id="nav">
          <div id="categorys" style="background-image: url('/themes/baian/images/ba_fljx.png');
                        background-position: 143px 18px; background-repeat: no-repeat; background-color: #41080A;">
            <div id="categorys" style="background-image: none;" onmouseover="showMc()" onmouseout="hideMc()">
              <div class="mt ld" style="background-image: none;">
                <h2>
                  <a href="/catalog.html">
                    全部商品分类<b style="background-image: none;"></b>
                  </a>
                </h2>
              </div>
              <div class="mc">
                                <div style='border-top:none;'                class="item fore"
                onMouseOver="this.className='item fore hover'"
                onmouseout="this.className='item fore'"
                id="allser1" >
                <span >
                <h3 style="text-align:left;"  id="allserh1">
                  <a href="/category-332-b0.html" style="text-decoration:none;font-weight:bold;">孕妈专区</a>
                </h3>
                <s class='s1'></s>
                <h4>
                                                                
                                    <a href="/category-344-b0.html" style="text-decoration:none">孕妈食品</a>
                                                                                                        <a href="/category-386-b0.html" style="text-decoration:none">孕妈用品</a>
                  
                                                                      </h4>
                </span>
                <div class="i-mc" style="top:3px;">
                  <div class="close" onclick="$(this).parent().parent().removeClass('hover')"></div>
                  <div class="subitem">
                                        <dl class="fore1">
                      <dt>
                        <a href="/category-344-b0.html" style="text-decoration:none">孕妈食品</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-365-b0.html" style="text-decoration:none">妈妈奶粉</a>
                        </em>
                                                <em>
                          <a href="/category-366-b0.html" style="text-decoration:none">妈妈保健品</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore2">
                      <dt>
                        <a href="/category-386-b0.html" style="text-decoration:none">孕妈用品</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-437-b0.html" style="text-decoration:none">卫生巾</a>
                        </em>
                                                <em>
                          <a href="/category-440-b0.html" style="text-decoration:none">妈咪两用巾</a>
                        </em>
                                                <em>
                          <a href="/category-444-b0.html" style="text-decoration:none">护理垫</a>
                        </em>
                                                <em>
                          <a href="/category-446-b0.html" style="text-decoration:none">吸乳器</a>
                        </em>
                                                <em>
                          <a href="/category-447-b0.html" style="text-decoration:none">妈咪护理</a>
                        </em>
                                                <em>
                          <a href="/category-459-b0.html" style="text-decoration:none">防溢乳垫</a>
                        </em>
                                                <em>
                          <a href="/category-466-b0.html" style="text-decoration:none">妈妈洗护品</a>
                        </em>
                                                <em>
                          <a href="/category-469-b0.html" style="text-decoration:none">妈咪口腔护理</a>
                        </em>
                                              </dd>
                    </dl>
                                                                                <div style="width:500px;position: relative;left:10px;top:40px;">
                    		                    <div style="float:left;width:220px;">
	                    <a href="/affiche.php?ad_id=336&uri=http://www.123121.com/brand-60-c332.html" target='_blank'><img src="/data/afficheimg/1416166980947736399.jpg" width='220' height='300' /></a>	                    </div>
	                    	                    	                    <div style="float:left;margin-left:21px;width:220px;">
	                    <a href="/affiche.php?ad_id=351&uri=" target='_blank'><img src="/data/afficheimg/1416166335459142653.jpg" width='220' height='300' /></a>	                    </div>
	                                        </div>
                                                                                                                                            
                  </div>
                  <div class="fr">
                                                            <dl class="categorys-brands">
                      <dt>推荐品牌</dt>
                      <dd>
                        <ul>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-74-c332.html">迈高</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-42-c332.html">美赞臣</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-41-c332.html">爱的营养大师</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-77-c332.html">爱得利</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-79-c332.html">贝亲</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-67-c332.html">亨氏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-99-c332.html">十月天使</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-63-c332.html">多美滋</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-68-c332.html">惠氏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-116-c332.html">金怡神</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-60-c332.html">雅培</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-61-c332.html">雅士利</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-221-c332.html">雀巢</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-46-c332.html">优之元</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-98-c332.html">千金净雅</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-103-c332.html">ABC</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-104-c332.html">护舒宝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-105-c332.html">七度空间</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-120-c332.html">茵茵</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-117-c332.html">美洁</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-119-c332.html">一片爽</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-34-c332.html">亲亲我</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-118-c332.html">小白熊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-78-c332.html">爱护</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-261-c332.html">百氏福</a>
                          </li>
                                                  </ul>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
                              <div                 class="item fore"
                onMouseOver="this.className='item fore hover'"
                onmouseout="this.className='item fore'"
                id="allser2" >
                <span style='background-color:#FFFCD6;'>
                <h3 style="text-align:left;"  id="allserh2">
                  <a href="/category-333-b0.html" style="text-decoration:none;font-weight:bold;">宝宝食品</a>
                </h3>
                <s class='s2'></s>
                <h4>
                                                                
                                    <a href="/category-340-b0.html" style="text-decoration:none">奶粉</a>
                                                                                                        <a href="/category-341-b0.html" style="text-decoration:none">辅食</a>
                  
                                                                                                      
                                    <a href="/category-342-b0.html" style="text-decoration:none">营养品</a>
                  
                                                                      </h4>
                </span>
                <div class="i-mc" style="top:3px;">
                  <div class="close" onclick="$(this).parent().parent().removeClass('hover')"></div>
                  <div class="subitem">
                                        <dl class="fore1">
                      <dt>
                        <a href="/category-340-b0.html" style="text-decoration:none">奶粉</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-375-b0.html" style="text-decoration:none">1段奶粉</a>
                        </em>
                                                <em>
                          <a href="/category-376-b0.html" style="text-decoration:none">2段奶粉</a>
                        </em>
                                                <em>
                          <a href="/category-377-b0.html" style="text-decoration:none">3段奶粉</a>
                        </em>
                                                <em>
                          <a href="/category-389-b0.html" style="text-decoration:none">4段奶粉</a>
                        </em>
                                                <em>
                          <a href="/category-390-b0.html" style="text-decoration:none">5段奶粉</a>
                        </em>
                                                <em>
                          <a href="/category-391-b0.html" style="text-decoration:none">羊奶粉</a>
                        </em>
                                                <em>
                          <a href="/category-392-b0.html" style="text-decoration:none">特殊配方奶粉</a>
                        </em>
                                                <em>
                          <a href="/category-393-b0.html" style="text-decoration:none">特惠组合装奶粉</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore2">
                      <dt>
                        <a href="/category-341-b0.html" style="text-decoration:none">辅食</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-378-b0.html" style="text-decoration:none">米粉</a>
                        </em>
                                                <em>
                          <a href="/category-379-b0.html" style="text-decoration:none">泥糊/果汁/水</a>
                        </em>
                                                <em>
                          <a href="/category-380-b0.html" style="text-decoration:none">面食类</a>
                        </em>
                                                <em>
                          <a href="/category-394-b0.html" style="text-decoration:none">冲调/调味</a>
                        </em>
                                                <em>
                          <a href="/category-395-b0.html" style="text-decoration:none">婴童零食</a>
                        </em>
                                                <em>
                          <a href="/category-545-b0.html" style="text-decoration:none">宝宝磨牙棒/饼</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore3">
                      <dt>
                        <a href="/category-342-b0.html" style="text-decoration:none">营养品</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-383-b0.html" style="text-decoration:none">DHA</a>
                        </em>
                                                <em>
                          <a href="/category-384-b0.html" style="text-decoration:none">牛初乳</a>
                        </em>
                                                <em>
                          <a href="/category-385-b0.html" style="text-decoration:none">调节肠胃</a>
                        </em>
                                                <em>
                          <a href="/category-396-b0.html" style="text-decoration:none">清火开胃</a>
                        </em>
                                                <em>
                          <a href="/category-397-b0.html" style="text-decoration:none">奶伴侣</a>
                        </em>
                                                <em>
                          <a href="/category-398-b0.html" style="text-decoration:none">葡萄糖</a>
                        </em>
                                                <em>
                          <a href="/category-399-b0.html" style="text-decoration:none">钙/铁/锌</a>
                        </em>
                                                <em>
                          <a href="/category-400-b0.html" style="text-decoration:none">鱼肝油</a>
                        </em>
                                                <em>
                          <a href="/category-417-b0.html" style="text-decoration:none">营养素</a>
                        </em>
                                                <em>
                          <a href="/category-559-b0.html" style="text-decoration:none">保健类</a>
                        </em>
                                              </dd>
                    </dl>
                                                                                                    <div style="width:500px;position: relative;left:10px;top:40px;">
                    		                    <div style="float:left;width:220px;">
	                    <a href="/affiche.php?ad_id=338&uri=http://www.123121.com/goods-1819.html" target='_blank'><img src="/data/afficheimg/1409781048534169973.jpg" width='200' height='200' /></a>	                    </div>
	                    	                    	                    <div style="float:left;margin-left:21px;width:220px;">
	                    <a href="/affiche.php?ad_id=350&uri=http://www.123121.com/brand-232-c333.html" target='_blank'><img src="/data/afficheimg/1409782215555904427.jpg" width='200' height='200' /></a>	                    </div>
	                                        </div>
                                                                                                                        
                  </div>
                  <div class="fr">
                                                            <dl class="categorys-brands">
                      <dt>推荐品牌</dt>
                      <dd>
                        <ul>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-43-c333.html">百立乐</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-74-c333.html">迈高</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-42-c333.html">美赞臣</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-41-c333.html">爱的营养大师</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-67-c333.html">亨氏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-63-c333.html">多美滋</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-50-c333.html">味奇</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-44-c333.html">伊利</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-45-c333.html">贝因美</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-53-c333.html">明一</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-57-c333.html">纽瑞滋</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-60-c333.html">雅培</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-61-c333.html">雅士利</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-68-c333.html">惠氏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-70-c333.html">康喜</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-71-c333.html">可瑞康</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-72-c333.html">可益多</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-221-c333.html">雀巢</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-55-c333.html">纽加力</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-49-c333.html">双熊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-62-c333.html">聪明蓓蓓</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-69-c333.html">吉米熊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-75-c333.html">每伴</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-222-c333.html">家宝氏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-223-c333.html">999</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-59-c333.html">西豆</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-224-c333.html">方广</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-46-c333.html">优之元</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-73-c333.html">乐宝士</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-65-c333.html">关山</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-66-c333.html">和氏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-51-c333.html">廷冠</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-52-c333.html">旺旺</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-95-c333.html">香港杏林堂</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-58-c333.html">香港衍生</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-76-c333.html">美力优</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-96-c333.html">日本KAWAI</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-265-c333.html">修正</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-230-c333.html">爱瑞嘉</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-231-c333.html">可瑞乐</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-232-c333.html">美素佳儿</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-227-c333.html">纽迪希亚</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-266-c333.html">澳优</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-262-c333.html">宝贝当家</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-268-c333.html">嘉宝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-264-c333.html">肥儿八珍</a>
                          </li>
                                                  </ul>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
                              <div                 class="item fore"
                onMouseOver="this.className='item fore hover'"
                onmouseout="this.className='item fore'"
                id="allser3" >
                <span >
                <h3 style="text-align:left;"  id="allserh3">
                  <a href="/category-336-b0.html" style="text-decoration:none;font-weight:bold;">宝宝用品</a>
                </h3>
                <s class='s1'></s>
                <h4>
                                                                
                                    <a href="/category-421-b0.html" style="text-decoration:none">纸尿裤</a>
                                                                                                        <a href="/category-548-b0.html" style="text-decoration:none">纸尿片</a>
                  
                                                                                                      
                                    <a href="/category-442-b0.html" style="text-decoration:none">宝宝用具</a>
                                                                                                        <a href="/category-461-b0.html" style="text-decoration:none">日常护理</a>
                  
                                                                                                        <a href="/category-475-b0.html" style="text-decoration:none">宝宝家电</a>
                                                                                                        <a href="/category-481-b0.html" style="text-decoration:none">护肤用品</a>
                                                                                                        <a href="/category-492-b0.html" style="text-decoration:none">洗涤用品</a>
                  
                                                                      </h4>
                </span>
                <div class="i-mc" style="top:3px;">
                  <div class="close" onclick="$(this).parent().parent().removeClass('hover')"></div>
                  <div class="subitem">
                                        <dl class="fore1">
                      <dt>
                        <a href="/category-421-b0.html" style="text-decoration:none">纸尿裤</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-428-b0.html" style="text-decoration:none">NB码</a>
                        </em>
                                                <em>
                          <a href="/category-429-b0.html" style="text-decoration:none">S码</a>
                        </em>
                                                <em>
                          <a href="/category-509-b0.html" style="text-decoration:none">M码</a>
                        </em>
                                                <em>
                          <a href="/category-430-b0.html" style="text-decoration:none">L码</a>
                        </em>
                                                <em>
                          <a href="/category-431-b0.html" style="text-decoration:none">XL码/XXL码</a>
                        </em>
                                                <em>
                          <a href="/category-436-b0.html" style="text-decoration:none">拉拉裤</a>
                        </em>
                                                <em>
                          <a href="/category-438-b0.html" style="text-decoration:none">隔尿垫</a>
                        </em>
                                                <em>
                          <a href="/category-441-b0.html" style="text-decoration:none">成人纸尿裤</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore2">
                      <dt>
                        <a href="/category-548-b0.html" style="text-decoration:none">纸尿片</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-433-b0.html" style="text-decoration:none">S码</a>
                        </em>
                                                <em>
                          <a href="/category-434-b0.html" style="text-decoration:none">M码</a>
                        </em>
                                                <em>
                          <a href="/category-435-b0.html" style="text-decoration:none">L码/XL码</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore3">
                      <dt>
                        <a href="/category-442-b0.html" style="text-decoration:none">宝宝用具</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-443-b0.html" style="text-decoration:none">奶瓶</a>
                        </em>
                                                <em>
                          <a href="/category-445-b0.html" style="text-decoration:none">奶嘴</a>
                        </em>
                                                <em>
                          <a href="/category-448-b0.html" style="text-decoration:none">安抚奶嘴</a>
                        </em>
                                                <em>
                          <a href="/category-450-b0.html" style="text-decoration:none">奶瓶吸管</a>
                        </em>
                                                <em>
                          <a href="/category-451-b0.html" style="text-decoration:none">水杯/水壶</a>
                        </em>
                                                <em>
                          <a href="/category-452-b0.html" style="text-decoration:none">保温用品</a>
                        </em>
                                                <em>
                          <a href="/category-454-b0.html" style="text-decoration:none">手动榨汁器</a>
                        </em>
                                                <em>
                          <a href="/category-456-b0.html" style="text-decoration:none">盘</a>
                        </em>
                                                <em>
                          <a href="/category-457-b0.html" style="text-decoration:none">碗</a>
                        </em>
                                                <em>
                          <a href="/category-458-b0.html" style="text-decoration:none">勺子</a>
                        </em>
                                                <em>
                          <a href="/category-460-b0.html" style="text-decoration:none">奶瓶刷</a>
                        </em>
                                                <em>
                          <a href="/category-546-b0.html" style="text-decoration:none">牙胶</a>
                        </em>
                                                <em>
                          <a href="/category-547-b0.html" style="text-decoration:none">奶瓶手柄</a>
                        </em>
                                                <em>
                          <a href="/category-549-b0.html" style="text-decoration:none">奶瓶盖</a>
                        </em>
                                                <em>
                          <a href="/category-562-b0.html" style="text-decoration:none">粉盒/粉芯</a>
                        </em>
                                                <em>
                          <a href="/category-563-b0.html" style="text-decoration:none">奶粉盒/格</a>
                        </em>
                                                <em>
                          <a href="/category-564-b0.html" style="text-decoration:none">奶嘴刷/吸管刷</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore4">
                      <dt>
                        <a href="/category-461-b0.html" style="text-decoration:none">日常护理</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-462-b0.html" style="text-decoration:none">医护用品</a>
                        </em>
                                                <em>
                          <a href="/category-464-b0.html" style="text-decoration:none">浴室用品</a>
                        </em>
                                                <em>
                          <a href="/category-467-b0.html" style="text-decoration:none">安全防护</a>
                        </em>
                                                <em>
                          <a href="/category-468-b0.html" style="text-decoration:none">宝宝牙膏</a>
                        </em>
                                                <em>
                          <a href="/category-470-b0.html" style="text-decoration:none">日常用品</a>
                        </em>
                                                <em>
                          <a href="/category-472-b0.html" style="text-decoration:none">坐便器</a>
                        </em>
                                                <em>
                          <a href="/category-473-b0.html" style="text-decoration:none">驱蚊防蚊</a>
                        </em>
                                                <em>
                          <a href="/category-561-b0.html" style="text-decoration:none">牙刷/牙擦</a>
                        </em>
                                                <em>
                          <a href="/category-601-b0.html" style="text-decoration:none">浴盆</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore5">
                      <dt>
                        <a href="/category-475-b0.html" style="text-decoration:none">宝宝家电</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-476-b0.html" style="text-decoration:none">消毒器</a>
                        </em>
                                                <em>
                          <a href="/category-477-b0.html" style="text-decoration:none">暖奶器</a>
                        </em>
                                                <em>
                          <a href="/category-478-b0.html" style="text-decoration:none">BB煲</a>
                        </em>
                                                <em>
                          <a href="/category-479-b0.html" style="text-decoration:none">理发器</a>
                        </em>
                                                <em>
                          <a href="/category-480-b0.html" style="text-decoration:none">日用小家电</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore6">
                      <dt>
                        <a href="/category-481-b0.html" style="text-decoration:none">护肤用品</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-482-b0.html" style="text-decoration:none">洗发/沐浴</a>
                        </em>
                                                <em>
                          <a href="/category-483-b0.html" style="text-decoration:none">面霜/手霜</a>
                        </em>
                                                <em>
                          <a href="/category-485-b0.html" style="text-decoration:none">护臀</a>
                        </em>
                                                <em>
                          <a href="/category-486-b0.html" style="text-decoration:none">BB油</a>
                        </em>
                                                <em>
                          <a href="/category-487-b0.html" style="text-decoration:none">橄榄油</a>
                        </em>
                                                <em>
                          <a href="/category-489-b0.html" style="text-decoration:none">爽身粉类</a>
                        </em>
                                                <em>
                          <a href="/category-544-b0.html" style="text-decoration:none">纸巾/湿纸巾</a>
                        </em>
                                                <em>
                          <a href="/category-560-b0.html" style="text-decoration:none">宝宝修护</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore7">
                      <dt>
                        <a href="/category-492-b0.html" style="text-decoration:none">洗涤用品</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-495-b0.html" style="text-decoration:none">洗衣液</a>
                        </em>
                                                <em>
                          <a href="/category-499-b0.html" style="text-decoration:none">消毒液</a>
                        </em>
                                                <em>
                          <a href="/category-501-b0.html" style="text-decoration:none">奶瓶清洁剂</a>
                        </em>
                                                <em>
                          <a href="/category-503-b0.html" style="text-decoration:none">洗手液</a>
                        </em>
                                              </dd>
                    </dl>
                                                                                                                        <div style="width:500px;position: relative;left:10px;top:40px;">
                    		                    <div style="float:left;width:220px;">
	                    <a href="/affiche.php?ad_id=349&uri=http://www.123121.com/search.php?encode=YToyOntzOjg6ImtleXdvcmRzIjtzOjc6IiDoirHnjosiO3M6MTg6InNlYXJjaF9lbmNvZGVfdGltZSI7aToxNDA5ODExNDU4O30=" target='_blank'><img src="/data/afficheimg/1409783140261013365.jpg" width='220' height='300' /></a>	                    </div>
	                    	                    	                    <div style="float:left;margin-left:21px;width:220px;">
	                    <a href="/affiche.php?ad_id=348&uri=http://www.123121.com/brand-79-c336.html" target='_blank'><img src="/data/afficheimg/1409783757834074075.jpg" width='220' height='300' /></a>	                    </div>
	                                        </div>
                                                                                                    
                  </div>
                  <div class="fr">
                                                            <dl class="categorys-brands">
                      <dt>推荐品牌</dt>
                      <dd>
                        <ul>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-77-c336.html">爱得利</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-79-c336.html">贝亲</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-141-c336.html">妈咪宝贝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-133-c336.html">帮宝适</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-138-c336.html">好奇</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-82-c336.html">鳄鱼宝宝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-137-c336.html">菲比</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-139-c336.html">可爱宝贝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-117-c336.html">美洁</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-145-c336.html">嘘嘘乐</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-119-c336.html">一片爽</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-146-c336.html">怡儿爽</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-120-c336.html">茵茵</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-40-c336.html">喜多</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-34-c336.html">亲亲我</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-39-c336.html">tutu兔兔</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-135-c336.html">比亲</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-102-c336.html">美啦美啦</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-144-c336.html">五和</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-142-c336.html">日康</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-84-c336.html">天明珠</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-32-c336.html">黑人牙膏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-35-c336.html">青蛙王子</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-36-c336.html">狮王</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-220-c336.html">otbaby</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-225-c336.html">芷御坊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-226-c336.html">花园宝宝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-213-c336.html">好娃娃</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-216-c336.html">宝乐堡</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-95-c336.html">香港杏林堂</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-78-c336.html">爱护</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-80-c336.html">丁果</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-134-c336.html">宝宝金水</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-140-c336.html">榄菊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-131-c336.html">舒氏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-118-c336.html">小白熊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-101-c336.html">塔塔</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-132-c336.html">百特</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-147-c336.html">易简</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-148-c336.html">振兴</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-33-c336.html">强生</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-81-c336.html">哆啦A梦</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-125-c336.html">蜜语</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-87-c336.html">金坡仕</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-89-c336.html">圣乐</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-143-c336.html">水亲亲</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-85-c336.html">娥罗纳英</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-86-c336.html">凡士林</a>
                          </li>
                                                  </ul>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
                              <div                 class="item fore"
                onMouseOver="this.className='item fore hover'"
                onmouseout="this.className='item fore'"
                id="allser4" >
                <span style='background-color:#FFFCD6;'>
                <h3 style="text-align:left;"  id="allserh4">
                  <a href="/category-335-b0.html" style="text-decoration:none;font-weight:bold;">婴童服饰</a>
                </h3>
                <s class='s2'></s>
                <h4>
                                                                
                                    <a href="/category-351-b0.html" style="text-decoration:none">婴幼童服装</a>
                                                                                                        <a href="/category-354-b0.html" style="text-decoration:none">宝宝配饰</a>
                  
                                                                                                      
                                    <a href="/category-355-b0.html" style="text-decoration:none">宝宝寝居</a>
                  
                                                                      </h4>
                </span>
                <div class="i-mc" style="top:3px;">
                  <div class="close" onclick="$(this).parent().parent().removeClass('hover')"></div>
                  <div class="subitem">
                                        <dl class="fore1">
                      <dt>
                        <a href="/category-351-b0.html" style="text-decoration:none">婴幼童服装</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-488-b0.html" style="text-decoration:none">肚兜</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore2">
                      <dt>
                        <a href="/category-354-b0.html" style="text-decoration:none">宝宝配饰</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-526-b0.html" style="text-decoration:none">口水围</a>
                        </em>
                                                <em>
                          <a href="/category-533-b0.html" style="text-decoration:none">毛巾</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore3">
                      <dt>
                        <a href="/category-355-b0.html" style="text-decoration:none">宝宝寝居</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-536-b0.html" style="text-decoration:none">枕头</a>
                        </em>
                                                <em>
                          <a href="/category-542-b0.html" style="text-decoration:none">浴巾</a>
                        </em>
                                                <em>
                          <a href="/category-543-b0.html" style="text-decoration:none">背带/背袋</a>
                        </em>
                                              </dd>
                    </dl>
                                                                                                                                            <div style="width:500px;position: relative;left:10px;top:40px;">
                    		                    	                    <div style="float:left;margin-left:21px;width:220px;">
	                    <a href="/affiche.php?ad_id=346&uri=http://www.123121.com/category-536-b0.html" target='_blank'><img src="/data/afficheimg/1409786216561776807.jpg" width='220' height='300' /></a>	                    </div>
	                                        </div>
                                                                                
                  </div>
                  <div class="fr">
                                                            <dl class="categorys-brands">
                      <dt>推荐品牌</dt>
                      <dd>
                        <ul>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-77-c335.html">爱得利</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-107-c335.html">多比兔</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-109-c335.html">丽婴十八坊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-111-c335.html">婴比迪</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-113-c335.html">婴悦家</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-108-c335.html">嘉贝适</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-112-c335.html">婴尚</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-114-c335.html">利卡熊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-115-c335.html">石头娃</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-130-c335.html">人之初</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-110-c335.html">雅卡儿</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-106-c335.html">聪明谷</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-260-c335.html">适婴坊</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-269-c335.html">贝莱特</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-257-c335.html">袋鼠仔仔</a>
                          </li>
                                                  </ul>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
                              <div                 class="item fore"
                onMouseOver="this.className='item fore hover'"
                onmouseout="this.className='item fore'"
                id="allser5" >
                <span >
                <h3 style="text-align:left;"  id="allserh5">
                  <a href="/category-334-b0.html" style="text-decoration:none;font-weight:bold;">童车童床</a>
                </h3>
                <s class='s1'></s>
                <h4>
                                                                
                                    <a href="/category-347-b0.html" style="text-decoration:none">童床</a>
                                                                                                        <a href="/category-349-b0.html" style="text-decoration:none">童车</a>
                  
                                                                      </h4>
                </span>
                <div class="i-mc" style="top:3px;">
                  <div class="close" onclick="$(this).parent().parent().removeClass('hover')"></div>
                  <div class="subitem">
                                        <dl class="fore1">
                      <dt>
                        <a href="/category-347-b0.html" style="text-decoration:none">童床</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-507-b0.html" style="text-decoration:none">木床</a>
                        </em>
                                                <em>
                          <a href="/category-510-b0.html" style="text-decoration:none">铁床</a>
                        </em>
                                                <em>
                          <a href="/category-512-b0.html" style="text-decoration:none">餐椅</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore2">
                      <dt>
                        <a href="/category-349-b0.html" style="text-decoration:none">童车</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-490-b0.html" style="text-decoration:none">宝宝手推车</a>
                        </em>
                                                <em>
                          <a href="/category-491-b0.html" style="text-decoration:none">学步车</a>
                        </em>
                                                <em>
                          <a href="/category-493-b0.html" style="text-decoration:none">自行车</a>
                        </em>
                                                <em>
                          <a href="/category-496-b0.html" style="text-decoration:none">滑板车</a>
                        </em>
                                                <em>
                          <a href="/category-500-b0.html" style="text-decoration:none">扭扭车</a>
                        </em>
                                                <em>
                          <a href="/category-502-b0.html" style="text-decoration:none">电瓶车</a>
                        </em>
                                                <em>
                          <a href="/category-504-b0.html" style="text-decoration:none">三轮车</a>
                        </em>
                                                <em>
                          <a href="/category-602-b0.html" style="text-decoration:none">滑行车</a>
                        </em>
                                              </dd>
                    </dl>
                                                                                                                                                                <div style="width:500px;position: relative;left:10px;top:40px;">
                    		                    <div style="float:left;width:220px;">
	                    <a href="/affiche.php?ad_id=345&uri=http://www.123121.com/category-334-b0.html" target='_blank'><img src="/data/afficheimg/1409787538373662631.jpg" width='220' height='300' /></a>	                    </div>
	                    	                                        </div>
                                                            
                  </div>
                  <div class="fr">
                                                            <dl class="categorys-brands">
                      <dt>推荐品牌</dt>
                      <dd>
                        <ul>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-207-c334.html">好孩子</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-206-c334.html">宝宝好</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-211-c334.html">硕士</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-212-c334.html">贝尔康</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-214-c334.html">圣得贝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-217-c334.html">强孩纳</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-208-c334.html">贺联</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-216-c334.html">宝乐堡</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-213-c334.html">好娃娃</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-209-c334.html">恒泰</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-210-c334.html">乐康</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-215-c334.html">金娃娃</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-251-c334.html">鑫贝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-248-c334.html">安贝琪</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-250-c334.html">小丽明</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-247-c334.html">爱婴宝恒</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-252-c334.html">醒目仔</a>
                          </li>
                                                  </ul>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
                              <div                 class="item fore"
                onMouseOver="this.className='item fore hover'"
                onmouseout="this.className='item fore'"
                id="allser6" >
                <span style='background-color:#FFFCD6;'>
                <h3 style="text-align:left;"  id="allserh6">
                  <a href="/category-337-b0.html" style="text-decoration:none;font-weight:bold;">图书玩具</a>
                </h3>
                <s class='s2'></s>
                <h4>
                                                                
                                    <a href="/category-357-b0.html" style="text-decoration:none">婴幼玩具</a>
                                                                                                        <a href="/category-570-b0.html" style="text-decoration:none">儿童玩具</a>
                  
                                                                                                      
                                    <a href="/category-580-b0.html" style="text-decoration:none">运动玩具</a>
                                                                                                        <a href="/category-583-b0.html" style="text-decoration:none">文具</a>
                  
                                                                                                        <a href="/category-587-b0.html" style="text-decoration:none">书籍</a>
                  
                                                                      </h4>
                </span>
                <div class="i-mc" style="top:3px;">
                  <div class="close" onclick="$(this).parent().parent().removeClass('hover')"></div>
                  <div class="subitem">
                                        <dl class="fore1">
                      <dt>
                        <a href="/category-357-b0.html" style="text-decoration:none">婴幼玩具</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-566-b0.html" style="text-decoration:none">益智玩具</a>
                        </em>
                                                <em>
                          <a href="/category-567-b0.html" style="text-decoration:none">摇铃床铃</a>
                        </em>
                                                <em>
                          <a href="/category-569-b0.html" style="text-decoration:none">推拉玩具</a>
                        </em>
                                                <em>
                          <a href="/category-603-b0.html" style="text-decoration:none">爬行垫</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore2">
                      <dt>
                        <a href="/category-570-b0.html" style="text-decoration:none">儿童玩具</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-571-b0.html" style="text-decoration:none">公仔/娃娃</a>
                        </em>
                                                <em>
                          <a href="/category-572-b0.html" style="text-decoration:none">彩泥/画板</a>
                        </em>
                                                <em>
                          <a href="/category-573-b0.html" style="text-decoration:none">积木/拼装</a>
                        </em>
                                                <em>
                          <a href="/category-574-b0.html" style="text-decoration:none">充气玩具</a>
                        </em>
                                                <em>
                          <a href="/category-575-b0.html" style="text-decoration:none">智力魔方</a>
                        </em>
                                                <em>
                          <a href="/category-576-b0.html" style="text-decoration:none">仿真模型</a>
                        </em>
                                                <em>
                          <a href="/category-577-b0.html" style="text-decoration:none">动漫玩具</a>
                        </em>
                                                <em>
                          <a href="/category-578-b0.html" style="text-decoration:none">电动玩具</a>
                        </em>
                                                <em>
                          <a href="/category-579-b0.html" style="text-decoration:none">遥控玩具</a>
                        </em>
                                                <em>
                          <a href="/category-606-b0.html" style="text-decoration:none">悠悠球</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore3">
                      <dt>
                        <a href="/category-580-b0.html" style="text-decoration:none">运动玩具</a>
                      </dt>
                      <dd>
                                              </dd>
                    </dl>
                                        <dl class="fore4">
                      <dt>
                        <a href="/category-583-b0.html" style="text-decoration:none">文具</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-585-b0.html" style="text-decoration:none">点读机/点读笔</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore5">
                      <dt>
                        <a href="/category-587-b0.html" style="text-decoration:none">书籍</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-588-b0.html" style="text-decoration:none">宝宝图书</a>
                        </em>
                                                <em>
                          <a href="/category-589-b0.html" style="text-decoration:none">孕产育儿</a>
                        </em>
                                              </dd>
                    </dl>
                                                                                                                                                                                    <div style="width:500px;position: relative;left:10px;top:40px;">
                    		                    <div style="float:left;width:220px;">
	                    <a href="/affiche.php?ad_id=343&uri=http://www.123121.com/category-337-b0.html" target='_blank'><img src="/data/afficheimg/1409787789485314956.jpg" width='220' height='300' /></a>	                    </div>
	                    	                    	                    <div style="float:left;margin-left:21px;width:220px;">
	                    <a href="/affiche.php?ad_id=342&uri=http://www.123121.com/category-337-b0.html" target='_blank'><img src="/data/afficheimg/1409788148377318416.jpg" width='220' height='300' /></a>	                    </div>
	                                        </div>
                                        
                  </div>
                  <div class="fr">
                                                            <dl class="categorys-brands">
                      <dt>推荐品牌</dt>
                      <dd>
                        <ul>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-154-c337.html">贝乐康</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-156-c337.html">比爱</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-195-c337.html">淘乐智汇</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-152-c337.html">宝丽</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-164-c337.html">彩虹</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-167-c337.html">迪孚</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-193-c337.html">喜乐天</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-196-c337.html">优贝比</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-149-c337.html">芭比</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-182-c337.html">乐吉儿</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-187-c337.html">挪拉马修</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-204-c337.html">智明星</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-163-c337.html">博高</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-170-c337.html">宏星</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-172-c337.html">华婴</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-189-c337.html">启蒙</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-194-c337.html">星钻积木</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-198-c337.html">俞氏兴</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-219-c337.html">大富翁</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-151-c337.html">彩珀</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-175-c337.html">凯迪威</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-191-c337.html">万宝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-199-c337.html">托马斯</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-150-c337.html">JHB</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-166-c337.html">东源</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-169-c337.html">孩之星</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-188-c337.html">锐视</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-192-c337.html">顺嘉</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-203-c337.html">星杰</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-153-c337.html">贝芬乐</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-157-c337.html">传奇玩具</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-168-c337.html">皇冠玩具</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-218-c337.html">智力宝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-184-c337.html">启智星</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-97-c337.html">阳光宝贝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-254-c337.html">宏兴</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-244-c337.html">小洛克</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-242-c337.html">创发</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-243-c337.html">大扬</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-229-c337.html">双钻</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-241-c337.html">汇乐</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-253-c337.html">百变战将</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-228-c337.html">飞仙女</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-240-c337.html">喜之宝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-249-c337.html">远大</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-246-c337.html">荣骏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-245-c337.html">富利时</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-270-c337.html">汇丰泰</a>
                          </li>
                                                  </ul>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
                              <div                 class="item fore"
                onMouseOver="this.className='item fore hover'"
                onmouseout="this.className='item fore'"
                id="allser7" >
                <span >
                <h3 style="text-align:left;"  id="allserh7">
                  <a href="/category-339-b0.html" style="text-decoration:none;font-weight:bold;">家居百货</a>
                </h3>
                <s class='s1'></s>
                <h4>
                                                                
                                    <a href="/category-590-b0.html" style="text-decoration:none">洗护用品</a>
                                                                                                        <a href="/category-596-b0.html" style="text-decoration:none">日常用品</a>
                  
                                                                      </h4>
                </span>
                <div class="i-mc" style="top:3px;">
                  <div class="close" onclick="$(this).parent().parent().removeClass('hover')"></div>
                  <div class="subitem">
                                        <dl class="fore1">
                      <dt>
                        <a href="/category-590-b0.html" style="text-decoration:none">洗护用品</a>
                      </dt>
                      <dd>
                                                <em>
                          <a href="/category-591-b0.html" style="text-decoration:none">洗发水</a>
                        </em>
                                                <em>
                          <a href="/category-592-b0.html" style="text-decoration:none">沐浴露</a>
                        </em>
                                                <em>
                          <a href="/category-593-b0.html" style="text-decoration:none">消毒液</a>
                        </em>
                                                <em>
                          <a href="/category-594-b0.html" style="text-decoration:none">柔顺剂</a>
                        </em>
                                              </dd>
                    </dl>
                                        <dl class="fore2">
                      <dt>
                        <a href="/category-596-b0.html" style="text-decoration:none">日常用品</a>
                      </dt>
                      <dd>
                                              </dd>
                    </dl>
                                                                                                                                                                                                        <div style="width:500px;position: relative;left:10px;top:40px;">
                    		                    <div style="float:left;width:220px;">
	                    <a href="/affiche.php?ad_id=339&uri=http://www.123121.com/brand-129-c339.html" target='_blank'><img src="/data/afficheimg/1409788651239288908.jpg" width='220' height='400' /></a>	                    </div>
	                    	                    	                    <div style="float:left;margin-left:21px;width:220px;">
	                    <a href="/affiche.php?ad_id=341&uri=http://www.123121.com/brand-126-c339.html" target='_blank'><img src="/data/afficheimg/1409788978590824694.jpg" width='220' height='400' /></a>	                    </div>
	                                        </div>
                    
                  </div>
                  <div class="fr">
                                                            <dl class="categorys-brands">
                      <dt>推荐品牌</dt>
                      <dd>
                        <ul>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-129-c339.html">威露士</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-122-c339.html">澳宝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-124-c339.html">海伦仙度丝</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-127-c339.html">飘柔</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-126-c339.html">潘婷</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-128-c339.html">琴叶</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-121-c339.html">艾诗</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-123-c339.html">滴露</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-125-c339.html">蜜语</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-131-c339.html">舒氏</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-239-c339.html">可爱多</a>
                          </li>
                                                    <li style="float:left;width:97px;">
                            <a target="_blank" href="/brand-263-c339.html">贝贝鸭</a>
                          </li>
                                                  </ul>
                      </dd>
                    </dl>
                  </div>
                </div>
              </div>
                          </div>
          </div>
          <div id="treasure">
          </div>
        </div>
        <div id="treasure">
        </div>
        <ul id="navitems">

          <li class="fore1" id="nav-home" class="hover">
            <a href="/">商城首页</a>
          </li>

                    <li     class="fore1"   onMouseOut="this.className='fore1'" onMouseOver="this.className='fore1 hover'" >
          <a href="/sales-promotion.html"

                        >特惠抢购          </a>
          </li>
                    <li     class="fore2"   onMouseOut="this.className='fore2'" onMouseOver="this.className='fore2 hover'" >
          <a href="/category-332-b0.html"

                        >孕妈专区          </a>
          </li>
                    <li     class="fore3"   onMouseOut="this.className='fore3'" onMouseOver="this.className='fore3 hover'" >
          <a href="/category-335-b0.html"

                        >婴童服饰          </a>
          </li>
                    <li     class="fore4"   onMouseOut="this.className='fore4'" onMouseOver="this.className='fore4 hover'" >
          <a href="/category-337-b0.html"

                        >婴童玩具          </a>
          </li>
                    <li     class="fore5"   onMouseOut="this.className='fore5'" onMouseOver="this.className='fore5 hover'" >
          <a href="/category-334-b0.html"

                        >童车童床          </a>
          </li>
          
        </ul>
        <div id="klcc">
          <a title="传宝宝照片有奖品" target="_blank" href="/baobao/index.php?ec_uid=0">
            <img alt="传宝宝照片有奖品" src="/themes/baian/images/ba_topzp.png" />
          </a>
        </div>
      </div>
    </div>
  </div> 
  
<script type="text/javascript">
//<![CDATA[

window.onload = function()
{
  fixpng();
}
function checkSearchForm()
{
    if(document.getElementById('keyword').value)
    {
        return true;
    }
    else
    {
		    alert("请输入关键词！");
        return false;
    }
}

function myValue1()
{
	document.getElementById('keyword').value = "请输入商品名称或编号...";
}

function myValue2()
{
	document.getElementById('keyword').value = "";
}




//document.oncontextmenu=function(e){return false;} 

var myul = document.getElementById('my_main_nav');
var myli = myul.getElementsByTagName('li');
for(var i = 0;i<myli.length;i++){
	
	if(myli[i].className !='cp'){
		myli[i].onmouseover = function(){this.className = 'nav_hover';}
		myli[i].onmouseout  = function(){this.className ='';}
	}
}


//]]>
</script>
<div class="blank10"></div>
<script type="text/javascript" src="/themes/baian/images/misc/lib/js/e/lib-v1.js"></script>
<div id="header_end" style="display:none;"></div>

  <script type="text/javascript" src="/themes/baian/js/jquery-1.32pack.js"></script>
<script>
  //var $$=jQuery.noConflict();

  /* *
  * 清除购物车购买商品数量
  */
  function delet(rec_id)
  {
  var formBuy      = document.forms['formCart'];
  var domname='goods_number_'+rec_id;
  var attr = getSelectedAttributes(document.forms['formCart']);
  var qty = parseInt(document.getElementById(domname).innerHTML)==0;
  Ajax.call('flow.php', 'step=price&rec_id=' + rec_id + '&number=' + qty, changecartPriceResponse, 'GET', 'JSON');
}			
/* *
 * 增加购物车购买商品数量
 */
function addcartnum(rec_id)
{
  var attr = getSelectedAttributes(document.forms['formCart']);
  var domname='goods_number_'+rec_id;
  var qty = parseInt(document.getElementById(domname).innerHTML)+1;
  Ajax.call('flow.php', 'step=price&rec_id=' + rec_id + '&number=' + qty, changecartPriceResponse, 'GET', 'JSON');
}
/* *
 * 减少购买商品数量
 */
function lesscartnum(rec_id)
{
    var formBuy      = document.forms['formCart'];
	var domname='goods_number_'+rec_id;
	var attr = getSelectedAttributes(document.forms['formCart']);
	var qty = parseInt(document.getElementById(domname).innerHTML)-1;
	Ajax.call('flow.php', 'step=price&rec_id=' + rec_id + '&number=' + qty, changecartPriceResponse, 'GET', 'JSON');
}
/**
 * 接收返回的信息
 */
function changecartPriceResponse(res)
{
  if (res.err_msg.length > 0 )
  {
    alert(res.err_msg);
  }
  else
  {
	var domnum='goods_number_'+res.rec_id;
	if(res.qty <= 0){
    document.getElementById('CART_INFO').innerHTML = res.content1;
    }else{
    document.getElementById(domnum).innerHTML = res.qty;
    }
    document.getElementById('ECS_CARTINFO').innerHTML = res.result;
    }
    }


    function changallser(allser)
    {
    document.getElementById(allser).className='item fore';
    }




    $(document).ready(function(){
    $("#kfao").bind("mouseenter",function(event){
    $("#kefu").animate({width:159},400,function(){});
    event.stopPropagation();
    });

    $("#kefu").bind("mouseleave",function(event){
    $("#kefu").animate({width:24},400,function(){});
    });
    });


    function tops_old(){
    var doc = (document.documentElement.scrollTop) ? document.documentElement : document.body;
    var time;
    time = setInterval(function(){
    doc.scrollTop -= Math.ceil(doc.scrollTop * 0.7);
    if(doc.scrollTop <= 0)  clearInterval(time);
    }, 1);
    }
  </script>

  <script type="text/javascript" src="/themes/baian/images/misc/lib/js/2012/base-v1.js"></script>
  <script type="text/javascript" src="/themes/baian/images/misc/lib/js/e/lib-v1.js"></script>
  <!--<script type="text/javascript" src="/themes/baian/images/misc/lib/js/2012/lib-v1.js"></script>-->
  
<div id="header_end" style="display:none;"></div>


<style>

	#top {
		display:none;
		position:fixed;
		_position:absolute;
		bottom: 10px;
		right: 2px;
		width: 27px;
		height: 92px;
		background:url(/themes/baian/images/top_bg.gif) no-repeat;
		cursor:pointer;
		z-index:9;
	}
</style>

<script type="text/javascript" src="/themes/baian/js/jqm1.7.jss"></script> 
<script type="text/javascript">
	function goTop() {	//跳到顶部
		$('html,body').animate({scrollTop:0},200); 
	}

	$(function($){
		
		//$('#backtops').click(function(){
		//	goTop();
		//});	
		$(window).bind('scroll',function(){
			if($(window).scrollTop()>0){
				//alert('scroll < 0!');
				$('#backtop').fadeIn(300);
			}else {
				//alert('scroll > 0');
				$('#backtop').fadeOut(300);	
			}
		});
		
	});
	
	// ie6中fixed定位函数
	function setIE6Fixed(element){
		var isIE6 = !!window.ActiveXObject && !window.XMLHttpRequest;
		if(!isIE6){return 0;}
		
		var o = arguments[1];
		var left=0,right=0,top=0,bottom=0;
		if(o){
			left = o.left;
			right = o.right;
			top = o.top;
			bottom = o.bottom;
		}
		function setFixed(){
			if(right||right===0){
				var width = element.offsetWidth;
				//x = document.documentElement.scrollLeft+document.documentElement.clientWidth-(width+right);
				element.style.setExpression("left","eval(document.documentElement.scrollLeft+document.documentElement.clientWidth-"+(width+right)+")+'px'");
			}else {
				//x = document.documentElement.scrollLeft+left;
				element.style.setExpression("left","eval(document.documentElement.scrollLeft+"+left+")+'px'");
			}
			
			if(bottom||bottom===0){
				var height = element.offsetHeight;
				//y = document.documentElement.scrollTop+document.documentElement.clientHeight-(height+bottom);
				element.style.setExpression("top","eval(document.documentElement.scrollTop+document.documentElement.clientHeight-"+(height+bottom)+")+'px'");
			}else {
				//y = top + document.documentElement.scrollTop;
				element.style.setExpression("top", "eval(document.documentElement.scrollTop+"+top+")+'px'");
			}
			//element.style.left = x;	//此方法会抖动
			//element.style.top = y;	    	
		}
		
		 window.attachEvent('onscroll',function(){
		 	//setFixed();
		 });
		 window.attachEvent('onresize',function(){
		 	//setFixed();
		 });
	}
	// ie6中fixed定位函数
</script>
