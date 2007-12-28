/* 
** Summary:	JS common Library 
** author:	ice_berg16(Ѱ�εĵ�����)
** lastModified: 2006-9-19
** copyright (c)2006 ice_berg16@163.com
*/


/*
 *	basic browser property checking
 */
var browser = new function(){
	this.ua = window.navigator.userAgent.toLowerCase();
	this.dom = document.getElementById ? 1 :0;
	this.moz = this.ua.match(/gecko/i) ? 1 : 0;
	this.ie  = this.ua.match(/msie/i) ? 1 : 0;
	this.opera = !this.ie && !this.moz && this.ua.match(/opera/i) ? 1 : 0;
};

/* ---------- Form Validator ----------- */
var Validator = {
	init:		function(){
		var n = document.forms.length;
		var self = this;
		for(var i=0; i<n; i++)
		{
			if( document.forms[i].getAttribute("validate") == "true" )
			{	
				document.forms[i].onsubmit = function(){return self.validate(this);};
			}
		}
	},
	validate:	function( form ){
		var isValid = true;
		var n = form.elements.length;
		//�ж��Ƿ��Ѿ�����
		var scrolled = false;
		for( var i=0; i<n; i++ )
		{
			if( form.elements[i].getAttribute("rule") )	//��Ҫ��֤
			{
				//��ú������
				var f = this[form.elements[i].getAttribute("rule")];
				var v = form.elements[i].value;
				var msg = form.elements[i].getAttribute("tip");
				if( form.elements[i].getAttribute("param") )
				{
					var e = form.elements[i].getAttribute("param");
					var p = form.elements[e].value;
					var r = f.apply(this,[v,p]);
				}
				else if( form.elements[i].getAttribute("p") )
				{
					var p = form.elements[i].getAttribute("p");
					var r = f.apply(this,[v,p]);
				}
				else
					var r = f.apply(this,[v]);
				
				if( !r )
				{
					if(!form.elements[i].label)
					{
						var label = document.createElement("label");
						label.style.marginLeft = "10px";
						label.style.color      = "#F60";
						label.innerHTML = msg;
						form.elements[i].parentNode.insertBefore(label,form.elements[i].nextSibling);
						form.elements[i].label = label;
					}
					isValid = false;
					if(form.elements[i].getAttribute("scroll") == "yes" && !scrolled)
					{
						scrolled = true;
						form.elements[i].scrollIntoView();
					}
				}
				else
				{
					if(form.elements[i].label)
					{
						form.elements[i].parentNode.removeChild(form.elements[i].label);
						form.elements[i].label = null;
					}
				}
			}

		}
		return isValid;
	},
	//===== ��������� =======
	number:		function( str )	{return this.match( str, /^\d+(\.\d+)?$/ )},
	required:	function( str )	{return !str.replace(/^\s+|\s+$/,"") == ""},
	maxLength:	function( str, length ) {if(length==0)return true; return str.length <= length},
	email:		function( str )	{return this.match( str, /^[\w\.]+@\w+\.\w+$/ )},
	date:		function( str )	{return this.match( str, /^\d{4}-\d{2}-\d{2}$/ )},
	alphaNumber:function( str ) {return this.match( str, /^[a-zA-Z0-9]+$/ )},
	phone:		function( str ) {return this.match( str, /^\d+(\-\d+){0,2}$/ )},
	equal:		function( str, str2 ) {return str == str2},
	match:		function( str, re ) {return re.test(str)}
};

/* -------- Utility Object -------------- */
var Utility = {
	bindEvent: function( obj, event, handler ){
		if( document.all )
			obj.attachEvent( event, handler );
		else
			obj.addEventListener( event.substr(2), handler, true );
	},
	getPosition: function(e){
		l = e.offsetLeft;
		t = e.offsetTop;
		while( e=e.offsetParent )
		{
			l += e.offsetLeft;
			t += e.offsetTop;
		}
		return {left:l,top:t};
	},
	toggle: function(){
		for (var i = 0; i < arguments.length; i++) 
		{
			var e = document.getElementById(arguments[i]);
			if( document.all ) 
				d = e.currentStyle.display;
			else
				d = document.defaultView.getComputedStyle(e,null).getPropertyValue("display");
			
			e.style.display = (d == 'none' ? this.getDefaultDisplay(e) : 'none');
		}
	},
	display: function(els, action)
	{
		var attr,value;
		switch(action)
		{
			case 'block':
				attr = 'display';
				value= 'block';
				break;
			case 'inline':
				attr = 'display';
				value= 'inline';
				break;
			case 'none':
				attr = 'display';
				value= 'none';
				break;
			case 'visible':
				attr = 'visibility';
				value= 'visible';
				break;
			case 'hidden':
				attr = 'visibility';
				value= 'hidden';
				break;
		}
		for(var i=0;i<els.length;i++)
		{
			e = els[i];
			if( typeof(e) == "string" )
				e = document.getElementById(els[i]);
			e.style[attr] = value;
		}
	},

	/* ���� */
	hide: function(){this.display(arguments,'none');},

	/* ��ʾ */
	show: function(){this.display(arguments,'block');},

	showInline: function(){this.display(arguments, 'inline');},

	/* �ɼ� */
	visible: function(){this.display(arguments,'visible');},

	/* ���ɼ� */
	hidden: function (e){this.display(arguments,'hidden');},

	//����
	center: function (e,fixtop){
		if( typeof(e) == "string" )	e = document.getElementById(e);
		e.style.position="absolute";
		fixtop = fixtop || 0;
		var scrollx = top.document.documentElement.scrollLeft;
		var scrolly = top.document.documentElement.scrollTop;
		var ttop = ((top.document.documentElement.clientHeight - e.offsetHeight)/2 + scrolly + fixtop);
		//��Χ�����ж�
		ttop = Math.min(ttop,e.ownerDocument.documentElement.scrollHeight - e.offsetHeight);
		ttop = Math.max(0, ttop);

		e.style.top  = ttop + "px";
		e.style.left = ((top.document.documentElement.clientWidth - e.offsetWidth)/2 + scrollx) + "px";
	},

	getDefaultDisplay: function(e){
		if( document.all ) return 'block';
		switch( e.tagName.toUpperCase() )
		{
			case "TABLE":
				def = "table";
				break;
			case "TR":
				def = "table-row";
				break;
			case "TD":
			case "TH":
				def = "table-cell";
				break;
			case "INPUT":
			case "SELECT":
				def = "inline-block";
				break;
			case "LI":
				def = "list-item";
				break;
			default:
				def = "block";
		}
		return def;
	},
	checkAll: function( radio, refobj ) {
		var rads =	document.getElementsByName(radio);
		for(var i=0; i<rads.length; i++)
		{
			rads[i].checked = refobj.checked;
		}
	},
	checkRadio: function( name, value ) {
		var rs = document.getElementsByName( name );
		for(var i=0; i<rs.length; i++)
		{
			if( rs[i].value == value )
			{
				rs[i].checked = true;
				return;
			}
		}
	},
	disableButton: function(bool){
		if(typeof(bool) == "undefined") bool = true;
		var inp = document.getElementsByTagName("input");
		for(i=0;i<inp.length;i++)
			if(inp[i].type.toLowerCase() == "submit" || inp[i].type.toLowerCase() == "button")
				inp[i].disabled = bool;
	}
};

/* ----------- some function method ---------- */
if(!Function.prototype.bind)
{
	Function.prototype.bind = function(o){
		var self = this;
		var args = Array.prototype.slice.apply(arguments,[1]);
		return function(){
			self.apply(o,args);
		}
	}
}

/* ----------- window.onload ---------- */
Utility.bindEvent( window, "onload", Validator.init.bind(Validator) );

/* ajaxModel ���� */
var Model = new function(){

	//ɾ������
	this.Delete = function(controller, cfm){
		try{
			id = this.checkSelect();
			if(typeof(cfm) == "undefined") cfm = true;
			if(cfm && !confirm('ɾ�����ָܻ���ȷʵҪɾ����')){
				TRSelect.unselect();
				return;
			}
			var self = this;
			var handler = function(response){
				//alert(response.responseText);
				eval('res='+response.responseText);
				if(res.status == 'ACCESS_DENIED')
				{
					self.message('û��Ȩ�޽��д˲���', 'ALERT');
					return false;
				}
				if(res.status == "REQUEST_OK"){
					self.message("ɾ���ɹ�", "SUCCESS");
					TRSelect.removeSelect();
				}
				else
					self.message("ɾ��ʧ��", 'ALERT');
			};
			this.Get(controller,'delete', id, null, handler);
		}catch(e){
			this.message("����ѡ��Ҫɾ������Ŀ");
		}
	};
	//��˺���������
	this.Audit = function(status, all){
		try
		{			
			var act = status == 0 ? '����' : '���';
			id = all || this.checkSelect();
			var self = this;
			var handler = function(response){
				//alert(response.responseText);
				eval('res='+response.responseText);
				if(res.status == 'ACCESS_DENIED')
				{
					self.message('û��Ȩ�޽��д˲���', 'ALERT');
					return false;
				}
				auditText = res.action == 'audit' ? '<img src="images/audit1.gif" align="absmiddle"/> �����'
											  : '<img src="images/audit0.gif" align="absmiddle"/> δ���';
				act		  = res.action == 'audit' ? '���' : '����';
				if(res.status == 'REQUEST_OK')
				{
					$('ckall').checked = false;
					$('ckall2').checked = false;
					TRSelect.map(function(r){
						if(all)
							r.cells[r.cells.length-1].innerHTML = auditText;
						else if(r.selected)
						{
							TRSelect.selectSingle(r,false);
							r.cells[r.cells.length-1].innerHTML = auditText;
						}
					});
					self.message(act + '�ɹ�', 'SUCCESS');
				}
				else
				{
					self.message(act + 'ʧ��', 'ALERT');
				}
			}
			this.Get('article', 'setAudit', 'status='+status+'&'+id, null, handler);
		}catch(e){
			this.message('��ѡ��Ҫ' + act + '����Ŀ', 'TIP');
		}
	}

	//�Ƽ���ȡ���Ƽ�����
	this.Recmd = function(status){
		try
		{			
			var act = status == 0 ? 'ȡ���Ƽ�' : '�Ƽ�';
			id = this.checkSelect();
			var self = this;
			var handler = function(response){
				eval('res=' + response.responseText);
				if(res.status == 'ACCESS_DENIED')
				{
					self.message('û��Ȩ�޽��д˲���', 'ALERT');
					return false;
				}
				if(res.status == "REQUEST_OK")
				{
					$('ckall').checked = false;
					$('ckall2').checked = false;
					TRSelect.map(function(r){
						if(r.selected)
						{
							TRSelect.selectSingle(r,false);
							if(res.action == 'recmd'){
								r.cells[1].getElementsByTagName("span")[0].innerHTML = '��';
							}
							else{
								r.cells[1].getElementsByTagName("span")[0].innerHTML = '';
							}							
						}
					});
					self.message('�����ɹ�', 'SUCCESS');
				}
				else
				{
					self.message('����ʧ��', 'ALERT');
				}
			}
			this.Get('article', 'setRecmd', 'status='+status+'&'+id, null, handler);
		}catch(e){
			alert(e.message);
			this.message('��ѡ��Ҫ' + act + '����Ŀ', 'TIP');
		}	
	}

	//��������id��������
	this.UpdateArticleById = function(){
		try{
			id = this.checkSelect();
			var self = this;
			handler = function(response){
				//alert(response.responseText);
				eval('res='+ response.responseText);
				if(res.status == "REQUEST_OK")
				{
					TRSelect.unselect();
					self.message('���³ɹ�', 'SUCCESS');
				}
				else
				{
					self.message('����ʧ��', 'ALERT');
				}
			};
			this.Get('update', 'updateArticleById', id, null, handler);
		}catch(e){
			this.message("����ѡ��Ҫ���µ���Ŀ.");
		}
	}
	//���·�������ҳ��
	this.UpdateSortArticle = function(id){
		try{
			id = id || this.checkSelect();
			
			if(typeof(id) == "number") id = "id="+ id;
			this.progressRequest('update', 'updateSortArticle', id);
		}catch(e){
			this.message("����ѡ��Ҫ���µķ���");
		}
	}
	//���·��������б�ĺ���
	this.UpdateList = function(id){
		try{
			id = id || this.checkSelect();			
			if(typeof(id) == "number") id = "id="+ id;
			this.progressRequest('update', 'updateList', id);
		}catch(e){
			this.message("����ѡ��Ҫ���µķ���");
		}
	}
	//����������������
	this.progressRequest = function(controller, action, p){
		var url = 'admin.php';
		var param = "ajax=1&C=" + controller + "&A=" + action;
		if(p) param += "&" + p;
		//���浱ǰURL����
		this.reqParam = param;
		var self = this;
		var handLoad = function(){
			self.message('�����У����Ժ�...', 'LOADING', false);
		}
		var cmpt = function(response){
			self.progressHandler(response);
		};
		new Ajax.Request(url,{method:'get',parameters:param,onLoading:handLoad,onComplete:cmpt});
	}
	//����������������ص�����(�ݹ����)
	this.progressHandler = function(response){
		try
		{
			eval("res="+response.responseText);
			if(res.status == 'ACCESS_DENIED')
			{
				self.message('û��Ȩ�޽��д˲���', 'ALERT');
				return false;
			}
			if(res.status == 'PROCESS_OVER')
			{
				var self = this;
				this.progressMessage(res.processed+"/"+res.total, res.title);
				setTimeout(function(){
						Utility.hidden(top.$('progress-box'));
						//ִ��һ���ص�����
						if(res.handler && typeof(res.handler) == 'function'){
							res.handler();
						}
						else self.message("�������", "SUCCESS");
						}, 500);
			}
			else if(res.status == "PROCESSING")
			{
				url = 'admin.php';
				//���������URL����
				param = this.reqParam; 
				var self = this;
				var handLoad = function(){
					self.progressMessage(res.processed+"/"+res.total, res.title);
				}
				var cmpt = function(response){
					self.progressHandler(response);
				}
				this.progressMessage(res.processed+"/"+res.total, res.title);
				new Ajax.Request(url,{method:'get',parameters:param,onLoading:handLoad,onComplete:cmpt});
			}
		}catch(e){
			Utility.hidden(top.$('progress-box'));
			Utility.hidden(top.$('tip-box'));
			alert("�������������Ǵ�����Ϣ\n\n"+response.responseText);
		}

	}
	//���ѡ�е���Ŀ��û�����׳��쳣
	this.checkSelect = function(){
		id = TRSelect.getSelectedId();
		if(id.length == 0)
		{
			throw new Error('δѡ����Ŀ');
		}
		return id;
	}

	/** ͨ������
	* controller ������
	* action ����
	* p	���Ӳ���
	* msg �������������ʾ��Ϣ msg[0]Ϊ�ɹ���Ϣ,msg[1]Ϊʧ����Ϣ
	* handler �����ɹ��Ļص�����
	* method ����ʽ post��get
	*/
	this.Request = function(controller, action, p, msg, handler, met)
	{
		url = 'admin.php';
		param = "ajax=1&C=" + controller + "&A=" + action;
		if(p) param += "&" + p;
		var self = this;
		var handler = handler || function(response){
			eval("res="+response.responseText);
			if(res.status == 'ACCESS_DENIED')
			{
				self.message('û��Ȩ�޽��д˲���', 'ALERT');
				return false;
			}
			return res.status == "REQUEST_OK" ? self.message(msg[0], 'SUCCESS')
														 : self.message(msg[1], 'ALERT');
		};
		var handLoad= function(){self.onLoading()};
		new Ajax.Request(url,{method:met,parameters:param,onLoading:handLoad,onComplete:handler});
	}
	//post����
	this.Post = function(controller, action, p, msg, handler){
		this.Request(controller, action, p, msg, handler, 'post');
	}
	//get����
	this.Get  =function(controller, action, p, msg, handler){
		this.Request(controller, action, p, msg, handler, 'get');
	}
	this.onLoading = function(){
		this.message('�����У����Ժ�...', 'LOADING',false);
	}

	/*
	 * ������Ϣ��Ϣ
	 * msg �Ѵ�����Ŀ/�ܹ���Ŀ
	 * title ��ʾ�ı���
	 */
	this.progressMessage = function(msg, title){
		per = msg.split("/");
		title = title || "���ڴ���...";
		per = per[0]== "0" ? 0 : (per[0] / per[1] * 100).toFixed(2);
		bar = "<img src='images/loading.gif' align='absmiddle'/> "+ title +
			   "<div id='progress-bar'><p>"+msg+"</p><div style='width:"+per+"%'></div></div>";
		top.document.getElementById("bar").innerHTML = bar;
		Utility.hidden(top.$('tip-box'));
		Utility.center(top.$('progress-box'));
		Utility.visible(top.$('progress-box'));
	}
	this.message = function(msg,type){
		var msgBox = top.document.getElementById("tip-box");
		type = type || 'TIP';
		switch(type)
		{
			case 'TIP':
				img = "<img src='images/tip.gif' align='absmiddle'/> ";
				break;
			case 'ALERT':
				img = "<img src='images/alert.gif' align='absmiddle'/> ";
				break;
			case 'SUCCESS':
				img = "<img src='images/success.gif' align='absmiddle'/> ";
				break;
			case 'LOADING':
				img = "<img src='images/loading.gif' align='absmiddle'/> ";
				break;
		}
		//������Ϣ
		top.document.getElementById("container").innerHTML = img + msg;
		Utility.center(msgBox);
		Utility.visible(msgBox);
		//�Ƿ����أ�Ĭ��Ϊ��
		if(arguments.length > 2 && arguments[2] == false) return;
		setTimeout(function(){Utility.hidden(msgBox)}, 2000);
	};
};


/* �б�ѡ�� */
var TRSelect = new function(){
	/*�л� */
	this.toggle = function(tr)
	{		
		if(typeof(tr.selected) == "undefined") tr.selected = false;
		tr.selected = !tr.selected;
		this.selectSingle(tr, tr.selected);
	}

	/* ѡ��һ�ڵ� */
	this.selectSingle = function(tr,bool){
		tr.selected = bool;
		tr.className = bool ? "tr-select" : '';
		tr.getElementsByTagName("input")[0].checked = bool;
	}

	this.selectAll = function(bool){
		this.map(function(tr){
			TRSelect.selectSingle(tr,bool);
		});
	}

	/* ����ѡ���ȡ���ӽ�� */
	this.selectChild = function(parent)
	{
		//����ǰ�ڵ�
		this.toggle(parent);
		//���ø��ڵ�
		if(!parent.selected)
			this.unselectParent(parent);

		//��ȥ��һ�еı�ͷ�����һ�е�������Ϣ
		var tr = document.getElementsByTagName("table")[0].rows;
		//�����ӽڵ�ı�ʶ
		for(var i=parent.rowIndex+1; i<tr.length-1; i++)
		{
			if(tr[i].getAttribute("deep") <= parent.getAttribute("deep")) break;
			this.selectSingle(tr[i], parent.selected);
		}
	}
	/* ����ȡ���������� */
	this.unselectParent = function(node)
	{
		var tr = document.getElementsByTagName("table")[0].rows;
		deep = node.getAttribute("deep");
		for(var i=node.rowIndex-1; i>0; i--)
		{
			//�ȵ�ǰ�ڵ����С������ͬʱ����deep���
			if(tr[i].getAttribute("deep") < deep)
			{
				this.selectSingle(tr[i],false);
				deep = tr[i].getAttribute("deep");
			}
			//�������ˣ�����Ҫ����ѭ��
			if(tr[i].getAttribute("deep") == 0) break;
		}
	}

	//ȡ��ѡ���ID�����ַ�����ʽ����
	this.getSelectedId = function(){
		var self = this;
		self.selectedId = [];
		this.map(function(tr){
			if(tr.selected){
				var ckbox = tr.getElementsByTagName("input")[0];
				if(ckbox.checked)
				{
					self.selectedId.push("id[]=" + ckbox.value);
				}
			}
		});
		return self.selectedId.join("&");
	}

	this.map = function(f){
		var tr = document.getElementsByTagName("table")[0].rows;
		//��ȥ��һ�еı�ͷ�����һ�е�������Ϣ
		for(var i=1;i<tr.length-1;i++){f(tr[i]);}
	}
	//ɾ��ѡ�����
	this.removeSelect = function(){
		var tr = document.getElementsByTagName("table")[0].rows;
		for(var i=1;i<tr.length-1;i++){
			if(tr[i].selected)
			{
				tr[i].parentNode.removeChild(tr[i]);
				i--;
			}
		}
	}
	this.unselect = function(){
		this.map(function(tr){
			TRSelect.selectSingle(tr,false);					
		});
		//ȡ��ȫѡ
		$('ckall').checked = false;
		$('ckall2').checked = false;
	}
};

//------------- �ɼ������Կ� ----------------
var Scratcher = new function(){

	//�����б�URL
	this.DebugListURL = function(){
		var listURL = encodeURIComponent(document.scratchForm.list_url.value);
		if(listURL.length == 0)
			return Model.message("δָ���ɼ��б��URL", "TIP");
		var handler = function(response){
			Utility.hidden(parent.$("tip-box"));
			document.scratchForm.listResult.value = response.responseText;
		}
		Model.Get('scratcher', 'debugListURL', 'list_url='+listURL, null, handler);
	}
	this.DebugListSplit = function(){
		var lbs = encodeURIComponent(document.scratchForm.list_before_string.value);
		var las = encodeURIComponent(document.scratchForm.list_after_string.value);
		var handler = function(response){
			Utility.hidden(parent.$("tip-box"));
			document.scratchForm.listSplitResult.value = response.responseText;
		}
		Model.Get('scratcher', 'debugListSplit', 'lbs='+lbs+'&las='+las, null, handler);
	}
	//���Դ��б��л�ȡ����
	this.DebugArticleURL = function(){
		var articleURL = encodeURIComponent(document.scratchForm.article_url.value);
		if(articleURL.length == 0)
			return Model.message("δָ���������ӵ�URL", "TIP");
		var handler =  function(response){
			Utility.hidden(parent.$("tip-box"));
			document.scratchForm.linkResult.value = response.responseText;
		}
		Model.Get('scratcher', 'debugArticleURL', 'article_url='+articleURL, null, handler);
	}
	//�������б���ȡһ�����ӵ�����
	this.DebugArticle = function(){
		var link = encodeURIComponent(document.scratchForm.link.value);
		var listURL = encodeURIComponent(document.scratchForm.list_url.value);
		if(link.length == 0)
			return Model.message("δָ�����µ�URL", "TIP");
		if(listURL.length == 0)
			return Model.message("δָ���ɼ��б��URL", "TIP");

		var handler =  function(response){
			Utility.hidden(parent.$("tip-box"));
			document.scratchForm.articleResult.value = response.responseText;
		}
		Model.Get('scratcher', 'debugArticle', 'link='+link+"&list_url="+listURL, null, handler);
	}
	//���Ը���ģʽ
	this.DebugPattern = function(field){
		var pattern = encodeURIComponent(document.scratchForm.elements[field+"_pattern"].value);
		if(pattern.length == 0)
			return Model.message("δָ��"+ field + "����", "TIP");

		var handler =  function(response){
			Utility.hidden(parent.$("tip-box"));
			document.scratchForm.elements[field+"Result"].value = response.responseText;
		}
		Model.Get('scratcher', 'debugPattern', 'pattern='+pattern+"&tmp="+Math.random(), null, handler);
	}
	//�ɼ��б�
	this.ScratchList = function(id){
		Model.progressRequest('scratcher','scratchList', 'id='+id);
	}
	//�ɼ�ҳ��
	this.ScratchPage = function(id){
		Utility.disableButton();
		Model.progressRequest('scratcher','scratchPage', 'scratch=1&id='+id);
	}
	//�������
	this.ClearCache = function(id){
		try{
			id = id || Model.checkSelect();
			
			if(typeof(id) == "number") id = "id="+ id;
			Model.Get('scratcher', 'clearCache', id, ['����ɹ�', '����ʧ��']);
		}catch(e){
			Model.message("����ѡ��ɼ�����");
		}
	}

	//���򵼳�
	this.Export = function(){
		try{
			id = Model.checkSelect();
			loc('scratcher','export', id);
		}catch(e){
			Model.message("����ѡ��Ҫ�����Ĳɼ�����");
		}
	}

	//���
	this.SaveToDB = function(id){
		id = "id="+id;
		Utility.disableButton();
		Model.progressRequest('scratcher', 'saveToDB', id);
	}
	
	this.enableBtn = function(){
		Utility.disableButton(false);
	}
	//�ɼ������Ļص�����
	this.AfterScratchPage = function(id){
		if(window.confirm('���Ӳɼ���ϣ��Ƿ񱣴浽���ݿ⣿'))
			this.SaveToDB(id);
		else
		{
			Utility.disableButton(false);
		}
	}
};


//--------------- gallery select ------------------
/* �б�ѡ�� */
var Gallery = new function(){
	/*�л� */
	this.toggle = function(div)
	{		
		if(typeof(div.selected) == "undefined") div.selected = false;
		div.selected = !div.selected;
		this.selectSingle(div, div.selected);
	}

	/* ѡ��һ�ڵ� */
	this.selectSingle = function(div,bool){
		div.selected = bool;
		div.className = bool ? "gallery select" : 'gallery';
		div.getElementsByTagName("input")[0].checked = bool;
	}

	this.selectAll = function(bool){
		this.map(function(div){
			Gallery.selectSingle(div,bool);
		});
	}

	//ȡ��ѡ���ID�����ַ�����ʽ����
	this.getSelectedId = function(){
		var self = this;
		self.selectedId = [];
		this.map(function(div){
			if(div.selected){
				var ckbox = div.getElementsByTagName("input")[0];
				if(ckbox.checked)
				{
					self.selectedId.push("id[]=" + ckbox.value);
				}
			}
		});
		return self.selectedId.join("&");
	}

	this.map = function(f){
		var list = $("galleryList").getElementsByTagName("div");
		//��ȥ���һ�е�clear div
		for(var i=0;i<list.length-1;i++){f(list[i]);}
	}
	//ɾ��ѡ�����
	this.removeSelect = function(){
		var list = $("galleryList").getElementsByTagName("div");
		for(var i=0;i<list.length-1;i++){
			if(list[i].selected)
			{
				list[i].parentNode.removeChild(list[i]);
				i--;
			}
		}
	}
	this.unselect = function(){
		this.map(function(div){
			Gallery.selectSingle(div,false);					
		});
		//ȡ��ȫѡ
		$('ckall').checked = false;
		$('ckall2').checked = false;
	}
	//ɾ������
	this.Delete = function(controller, cfm){
		try{
			controller = controller || "gallery";
			id = this.getSelectedId();
			if(id.length == 0) throw new Error();
			if(typeof(cfm) == "undefined") cfm = true;
			if(cfm && !confirm('ɾ�����ָܻ���ȷʵҪɾ����')){
				this.unselect();
				return;
			}
			var handler = function(response){
				//alert(response.responseText);
				eval('res='+ resposne.responseText);
				if(res.status == 'ACCESS_DENIED')
				{
					self.message('û��Ȩ�޽��д˲���', 'ALERT');
					return false;
				}
				if(res.status == "REQUEST_OK")
				{
					Model.message("ɾ���ɹ�", "SUCCESS");
					Gallery.removeSelect();
				}
				else
					Model.message("ɾ��ʧ��", 'ALERT');
			};
			Model.Get(controller,'delete', id, null, handler);
		}catch(e){
			Model.message("����ѡ��Ҫɾ������Ŀ");
		}
	};
	this.EditTitle = function(span){
		var inp = document.createElement("input");
		inp.type= "text";
		inp.style.border	= "1px solid #ccc";
		inp.style.fontSize  = "12px";
		inp.style.width		= "50px";
		inp.value = span.innerHTML;
		inp.span  = span;
		inp.onclick = stopEvent;
		inp.onblur = function(){
			id = this.parentNode.parentNode.getElementsByTagName("input")[1].value;
			var handler = function(response){
				eval('res='+ resposne.responseText);
				if(res.status == 'ACCESS_DENIED')
				{
					self.message('û��Ȩ�޽��д˲���', 'ALERT');
					return false;
				}
				if(res.status == "REQUEST_OK")
					Utility.hidden(parent.$('tip-box'));
				else
					Model.message('�޸�ʧ��', 'ALERT');
			};
			Model.Get('gallery', 'editTitle', 'id='+id+'&title='+encodeURIComponent(this.value), null, handler);
			this.span.innerHTML = this.value;
			this.parentNode.replaceChild(this.span,this);
		}
		span.parentNode.replaceChild(inp,span);		
		inp.select();
	}
	//�������ҳ��
	this.UpdateGallery = function(){
		try{
			id = this.getSelectedId();
			if(id.length == 0) throw new Error();
			var handler = function(response){

				eval('res='+ resposne.responseText);
				if(res.status == 'ACCESS_DENIED')
				{
					self.message('û��Ȩ�޽��д˲���', 'ALERT');
					return false;
				}
				if(res.status == "REQUEST_OK"){
					Model.message("���³ɹ�", "SUCCESS");
					Gallery.unselect();
				}
				else
				{
					//����ʱ����������Ϣ
					alert(response.responseText);
					Model.message("����ʧ��", 'ALERT');
				}
			};
			Model.Get('update','updateGallery', id, null, handler);
		}catch(e){
			Model.message("����ѡ��Ҫ���µ����");
		}
	}

	//���·�������б�ĺ���
	this.UpdateList = function(id){
		param = "id=" + id + "&model="+'gallery';
		Model.progressRequest('update', 'updateList', param);
	}
};

//--------- FTP���� ------------
var Ftp = new function(){
	this.test = function(id){
		p = "id="+id;
		Model.Get('ftp', 'test', p, ['FTP��¼�ɹ�', 'FTP��¼ʧ��']);
	}
	this.pub = function(id){
		try{
			id =  id || Model.checkSelect();
			if(typeof(id) == "number") id = "id="+id;
			Model.progressRequest('ftp', 'pub', id);
		}
		catch(e){
			Model.message("����ѡ��Ҫ������FTP");
		}
	}
};

//---------- ���԰� ------------
var Guestbook = new function(){
	/*�л� */
	this.toggle = function(div)
	{		
		if(typeof(div.selected) == "undefined") div.selected = false;
		div.selected = !div.selected;
		this.selectSingle(div, div.selected);
	}

	/* ѡ��һ�ڵ� */
	this.selectSingle = function(div,bool){
		div.selected = bool;
		div.className = bool ? "select" : 'gallery';
		div.getElementsByTagName("input")[0].checked = bool;
	}

	this.selectAll = function(bool){
		this.map(function(div){
			Guestbook.selectSingle(div,bool);
		});
	}

	//ȡ��ѡ���ID�����ַ�����ʽ����
	this.getSelectedId = function(){
		var self = this;
		self.selectedId = [];
		this.map(function(div){
			if(div.selected){
				var ckbox = div.getElementsByTagName("input")[0];
				if(ckbox.checked)
				{
					self.selectedId.push("id[]=" + ckbox.value);
				}
			}
		});
		return self.selectedId.join("&");
	}

	this.map = function(f){
		var list = $("guestbook").getElementsByTagName("li");
		for(var i=0;i<list.length-1;i++){f(list[i]);}
	}
	//ɾ��ѡ�����
	this.removeSelect = function(){
		var list = $("guestbook").getElementsByTagName("li");
		for(var i=0;i<list.length-1;i++){
			if(list[i].selected)
			{
				list[i].parentNode.removeChild(list[i]);
				i--;
			}
		}
	}
	this.unselect = function(){
		this.map(function(div){
			Guestbook.selectSingle(div,false);					
		});
		//ȡ��ȫѡ
		$('ckall').checked = false;
		$('ckall2').checked = false;
	}
	//ɾ������
	this.Delete = function(cfm){
		try{
			controller = "guestbook";
			id = this.getSelectedId();
			if(id.length == 0) throw new Error();
			if(typeof(cfm) == "undefined") cfm = true;
			if(cfm && !confirm('ɾ�����ָܻ���ȷʵҪɾ����')){
				this.unselect();
				return;
			}
			var handler = function(response){
				//alert(response.responseText);
				eval('res='+ resposne.responseText);
				if(res.status == 'ACCESS_DENIED')
				{
					self.message('û��Ȩ�޽��д˲���', 'ALERT');
					return false;
				}
				if(res.status == "REQUEST_OK"){
					Model.message("ɾ���ɹ�", "SUCCESS");
					Guestbook.removeSelect();
				}
				else
					Model.message("ɾ��ʧ��", 'ALERT');
			};
			Model.Get(controller,'delete', id, null, handler);
		}catch(e){
			Model.message("����ѡ��Ҫɾ������Ŀ");
		}
	};
}