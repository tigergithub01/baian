 window.pageConfig = {
		compatible: true,
       	product: {
			skuid: 592892,
			name: '',
			skuidkey:'',
			href: 'http://127.0.0.1/goods.php?id=58',
			src: '382/d528fe19-37b8-4968-981b-8a2b7978689a.jpg',
			cat: [111],
			brand: 1,
			nBrand: 1,
			tips: false,
			type: 1,
			venderId:0,
			TJ:'0',
			specialAttrs:[]
			/**add by wanggz
		*/
		}
	};
	
	
	
	

function $id(element) {
  return document.getElementById(element);
}
//切屏--是按钮，_v是内容平台，_h是内容库
function reg(str){
  var bt=$id(str+"_b").getElementsByTagName("h2");
  for(var i=0;i<bt.length;i++){
    bt[i].subj=str;
    bt[i].pai=i;
    bt[i].style.cursor="pointer";
    bt[i].onclick=function(){
      $id(this.subj+"_v").innerHTML=$id(this.subj+"_h").getElementsByTagName("blockquote")[this.pai].innerHTML;
      for(var j=0;j<$id(this.subj+"_b").getElementsByTagName("h2").length;j++){
        var _bt=$id(this.subj+"_b").getElementsByTagName("h2")[j];
        var ison=j==this.pai;
        _bt.className=(ison?"":"h2bg");
      }
    }
  }
  $id(str+"_h").className="none";
  $id(str+"_v").innerHTML=$id(str+"_h").getElementsByTagName("blockquote")[0].innerHTML;
}



var warestatus = 1;


function showlayer(layerid) 
{
	l = document.getElementById(layerid);
	l.style.display="block";
}
function hidelayer(layerid) 
{
	l = document.getElementById(layerid);
	l.style.display="none";
}


function goods_cut(){  
	var num_val=document.getElementById('number');  
	var new_num=num_val.value;  
	var Num = parseInt(new_num);  
	if(Num>1)Num=Num-1;  num_val.value=Num;  
}  

function goods_add(){  
	var num_val=document.getElementById('number');  
	var new_num=num_val.value;  
	var Num = parseInt(new_num);  
	Num=Num+1;  
	num_val.value=Num;  
} 




/*第一种形式 第二种形式 更换显示样式*/
function setTabCatGoods(name,cursel,n){
for(i=1;i<=n;i++){
var menu=document.getElementById(name+i);
var con=document.getElementById("con_"+name+"_"+i);
con.style.display=i==cursel?"block":"none";
menu.className=i==cursel?"curr":"";
}
}