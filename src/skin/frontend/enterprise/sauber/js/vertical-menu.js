
var id_menu = new Array('sub_menu_1');
startList = function allclose() {
	for (i=0; i < id_menu.length; i++){
		if(document.getElementById(id_menu[i])) {
			document.getElementById(id_menu[i]).style.display = "none";
		}
	}
}
function openMenu(id){
	for (i=0; i < id_menu.length; i++){
		if (id != id_menu[i]){
			document.getElementById(id_menu[i]).style.display = "none";
		}
	}
	if (document.getElementById(id).style.display == "block"){
		document.getElementById(id).style.display = "none";
		//document.getElementById(id).style.background = "url(../images/arrow.png) 100% 50% no-repeat";
	}else{
		document.getElementById(id).style.display = "block";
		//document.getElementById(id).style.background = "url(../images/arrow-grey.png) 100% 50% no-repeat";
	}
}
window.onload=startList;




