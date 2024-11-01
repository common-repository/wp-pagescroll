//wp-pagescroll
	var scrolls= new Array();//to count how many scrolls we do
	scrolls.push(new Array(1,0));//we set manually the first page with 0 px


	//changing classes
	function getElementsByClassDustin(searchClass,node,tag) { //by http://www.dustindiaz.com/getelementsbyclass/
	    var classElements = new Array();
	    if ( node == null )
		    node = document;
	    if ( tag == null )
		    tag = '*';
	    var els = node.getElementsByTagName(tag);
	    var elsLen = els.length;
	    var pattern = new RegExp("(^|\\s)"+searchClass+"(\\s|$)");
	    for (i = 0, j = 0; i < elsLen; i++) {
		    if ( pattern.test(els[i].className) ) {
		            classElements[j] = els[i];
		            j++;
		    }
	    }
	    return classElements;
	}

	function getElementsByClass(searchClass,node,tag) { 
		//if is Netscape we use the native procedure
		if (navigator.appName=="Netscape") return document.getElementsByClassName(searchClass);
		else return getElementsByClassDustin(searchClass,node,tag);
	}

	//show page
	function ShowPage(id){
		document.getElementById("currentpage").innerHTML=id;
		//change page class all normal
		var ePage = getElementsByClass("current_page",document.getElementById('page-corner'),"a");//all the page
		for (i in ePage) ePage[i].className ='page';
		//set on the the display page
		page=document.getElementById('page'+id)
		if(page!=null) page.className ='current_page';
	}

	//scrolol to page X
	function GoToPage(id){
		//if its in the array
		if (scrolls.length>1){
			var n=0;//counter
			var found=false;//chivato
			
			while (n<scrolls.length && !found){
				if (scrolls[n][0]==id) found=true;
				else n++;
			}
			
			if (found){//the page is in the array
				var y=returnScrollOffset();//where are we in the Y
				//up or down
				if (scrolls[n][1]>y) scrollDown(scrolls[n][1]+25,1);
				else scrollUp(scrolls[n][1]-25,1);
			}
			else scrollDown(null,1);//the page is not in the array :(
		
		}
		else scrollDown(null,1);//not in array, scroll down
					
	}


	//Control on scroll event
	function addLoadEvent(func) {
	    var oldonload = window.onscroll;
	    if (typeof window.onscroll != 'function') {
		window.onscroll = func;
	    } else {
		window.onscroll = function() {
		    if (oldonload) {
		        oldonload();
		    }
		    func();
		}
	    }
	}

	addLoadEvent(function() {
		var n=0;//counter
		var found=false;//chivato
		var y;//where are we in the Y

		while (n<=scrolls.length && !found){
			y=returnScrollOffset();

			if((n+1)<scrolls.length){
				if (y>=scrolls[n][1] && y<scrolls[n+1][1]){
					ShowPage(scrolls[n][0]);
					found=true;
				}
			}
			else{
				if (y>=scrolls[n][1]){
					ShowPage(scrolls[n][0]);
					found=true;
				}
			}

			n++;
		}
	})



