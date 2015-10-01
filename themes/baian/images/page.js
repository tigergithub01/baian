function Ds(i) {document.getElementById(i).style.display = 'block';}
function Dh(i) {document.getElementById(i).style.display = 'none';}
function setTip(w) {Dh('search_tips'); document.getElementById('keyword').value = w; document.getElementById('searchForm').submit();}
var tip_word = '';
function STip(w) {
	if(w.length < 2) {document.getElementById('search_tips').innerHTML = ''; Dh('search_tips'); return;}
	if(w == tip_word) {return;} else {tip_word = w;}
	  Ajax.call('ajax.php', 'act=tipword&word=' + w, _STip, 'GET', 'JSON');
}
function _STip(result) {
	if (result.error == 0){
		if(result.content) {
			Ds('search_tips'); document.getElementById('search_tips').innerHTML = result.content + '<label onclick="Dh(\'search_tips\');">关闭&nbsp;&nbsp;</label>';
		} else {
			document.getElementById('search_tips').innerHTML = ''; Dh('search_tips');
		}
	}
}