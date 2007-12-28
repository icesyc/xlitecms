/* 
** Summary:	tab menu
** author:	ice_berg16(寻梦的稻草人)
** lastModified: 2006-9-15
** copyright (c)2006 ice_berg16@163.com
*/

//首页菜单
function toggleMenu(){
	var menu = document.getElementById("menu");
	var item = menu.getElementsByTagName("li");
	lastItem = item[item.length-1];
	lastSubMenu = document.getElementById("tab_index");
	for(i=0;i<item.length;i++)
	{
		item[i].onmouseover = function(e){
			if(this.className == "active") return;
			this.className="over";
		}
		item[i].onmouseout = function(e){
			if(this.className == "active") return;
			this.className="";
		}
		item[i].onclick = function(e){
			if(lastItem)
				lastItem.className = "";
			if(lastSubMenu)
				lastSubMenu.className = "sub-menu";
			this.className ="active";
			submenuId = this.getAttribute("for");
			if(submenuId)			
			{				
				submenu = document.getElementById(submenuId);
				submenu.className = "sub-menu-show";
				lastSubMenu = submenu;
				if(this.getAttribute("jump"))
				{
					loc = this.getAttribute("jump").split(",");
					jump.apply(window,loc);
				}
			}
			lastItem = this;
		}
	}
}
// 设置当前项目
function activeItem(index){
	var menu = document.getElementById("menu");
	var item = menu.getElementsByTagName("li");
	if(lastItem)
		lastItem.className = "";
	if(lastSubMenu)
		lastSubMenu.className = "sub-menu";
	item[index].className ="active";
	submenuId = item[index].getAttribute("for");
	if(submenuId)			
	{				
		submenu = document.getElementById(submenuId);
		submenu.className = "sub-menu-show";
		lastSubMenu = submenu;
	}
	lastItem = item[index];

}

/* 列表项跳转 */
function jump(controller,action,param)
{
	if(!action) action = "";
	if(!param) param = "";
	var url = "admin.php?C="+controller+"&A="+action+"&"+param;
	top.document.getElementById("main").contentWindow.location = url;
}
function autoFixIframe(scroll)
{
	if(window.top != self)
	{
		if(browser.ie) h = document.documentElement.scrollHeight;
		if(browser.moz) h = document.body.scrollHeight + 20;
		top.document.getElementById("main").height = h;
		
		//是否滚到顶部
		if(typeof(scroll) == "undefined") scroll = true;
		if(scroll) window.scrollTo(0,0);
		//隐藏信息框
		parent.Utility.hidden('tip-box');
		parent.Utility.hidden('progress-box');
	}
}

/* 页面跳转 */
function loc(controller, action, param)
{
	url = "admin.php?C="+controller+"&A="+action;
	if(param)
		url += "&" + param;
	location.href = url;
}

//停止事件冒泡
function stopEvent(e){
	e = window.event || e;
	if(browser.moz)
		e.stopPropagation();
	if(browser.ie)
		e.cancelBubble = true;
}
Event.observe(window, 'load', autoFixIframe);

//选择颜色
function selectColor(rel){
	//------------- 执行FCKCommand ------------------
	var oEditor = FCKeditorAPI.GetInstance('content') ;

	// Execute the command.
	cmd = oEditor.Commands.GetCommand('TextColor');
	old = cmd.SetColor;
	cmd.SetColor = function(color){
		document.saveForm.elements['title'].style.color = color;
		document.saveForm.elements['title_color'].value = color;
		cmd.SetColor=old;
	};
	cmd.Execute(0, 23, rel);
}

//选择缩略图
function selectImage()
{
	oEditor = $('content___Frame').contentWindow;
	OpenFileBrowser(oEditor.FCKConfig.ImageBrowserURL,
		oEditor.FCKConfig.ImageBrowserWindowWidth,
		oEditor.FCKConfig.ImageBrowserWindowHeight);
}

function OpenFileBrowser( url, width, height )
{
	// oEditor must be defined.
	
	var iLeft = ( oEditor.FCKConfig.ScreenWidth  - width ) / 2 ;
	var iTop  = ( oEditor.FCKConfig.ScreenHeight - height ) / 2 ;

	var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes" ;
	sOptions += ",width=" + width ;
	sOptions += ",height=" + height ;
	sOptions += ",left=" + iLeft ;
	sOptions += ",top=" + iTop ;

	// The "PreserveSessionOnFileBrowser" because the above code could be 
	// blocked by popup blockers.
	if ( oEditor.FCKConfig.PreserveSessionOnFileBrowser && oEditor.FCKBrowserInfo.IsIE )
	{
		// The following change has been made otherwise IE will open the file 
		// browser on a different server session (on some cases):
		// http://support.microsoft.com/default.aspx?scid=kb;en-us;831678
		// by Simone Chiaretta.
		var oWindow = oEditor.window.open( url, 'FCKBrowseWindow', sOptions ) ;
		
		if ( oWindow )
		{
			// Detect Yahoo popup blocker.
			try
			{
				var sTest = oWindow.name ; // Yahoo returns "something", but we can't access it, so detect that and avoid strange errors for the user.
				oWindow.opener = window ;
			}
			catch(e)
			{
				alert( oEditor.FCKLang.BrowseServerBlocked ) ;
			}
		}
		else
			alert( oEditor.FCKLang.BrowseServerBlocked ) ;
    }
    else
		window.open( url, 'FCKBrowseWindow', sOptions ) ;
}