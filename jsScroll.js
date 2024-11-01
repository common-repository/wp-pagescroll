/*
Script Name: jsScroll
Script URI: http://neo22s.com/jsscroll/ 
Version: 0.3
Release date: 10/09/2009
Author: Chema Garrido
License: GPL v3
Description: Smooth scroll for your web
Support: http://forum.neo22s.com/
Notes: 
Works in FF 3.5, Chromium, Opera 10, ie6
*/

//Global Variables
	var scrollCount;//count scroll in pixels
	var scrollJump;//jump in pixels
	var scrollJumpMin;//minimal jump in pixels
	var maxScroll;//max scroll
	var y; // coordinate Y

//Config
   scrollJumpMin=20;//we need to set up a minimal jump cuz  sometimes is really slow (when the page is small for example)
   
//Scrolling functions
   function scrollDown(scro,delay){//first parameter: where to stop scrolling down in px, second: delay for the formula
		scrollCount = returnInnerDimensions() + returnScrollOffset();//position that we are is = screen size + scroll in Y
		maxScroll = returnPageDimensions();//to know the maximum that we can scroll
		if (scro!=null && scro<maxScroll) maxScroll=scro;//if theres a scro seted and is not bigger than the maximun scroll allowed
		scrollJump=((maxScroll-scrollCount)/(delay*1000)*scrollJumpMin);//how much we jump per time
		if (scrollJump<scrollJumpMin) scrollJump=scrollJumpMin; //if the scroll is less than the minimal we set it
		scrollingDown();
	}
      
	function scrollUp(scro,delay){//first parameter: where to stop scrolling up in px, second: delay for the formula
		scrollCount = returnScrollOffset();//scroll on Y done, we dont need the inner position
		maxScroll = scro;//position we move       
		scrollJump=(scrollCount/(delay*1000)*scrollJumpMin);//how much we jump
		if (scrollJump<scrollJumpMin) scrollJump=scrollJumpMin; //if the scroll is less than the minimal we set it
		scrollingUp();
	}
	
//Recursive scrolling functions
	function scrollingDown(){//to scroll down we add
		if (scrollCount < maxScroll) {
			scrollCount+=scrollJump;
			scroll(0,scrollCount);
			setTimeout("scrollingDown()",1);   
		}       
	}

	function scrollingUp(){//to scroll up we rest
		if (scrollCount> maxScroll) {
			scrollCount-=scrollJump;
			scroll(0,scrollCount);
			setTimeout("scrollingUp()",1);   
		}       
	}
	
//Dimension functions
	function returnInnerDimensions(){//returns screen size X,Y
			if (self.innerHeight){ // all except Explorer
				y = self.innerHeight;
			}
			else if (document.documentElement && document.documentElement.clientHeight){// Explorer 6 Strict Mode
				y = document.documentElement.clientHeight;
			}
			else if (document.body){ // other Explorers
				y = document.body.clientHeight;
			}
			return y;
	}
	
	function returnScrollOffset(){//return how much we scrolled already
			if (self.pageYOffset){ // all except Explorer
				y = self.pageYOffset;
			}
			else if (document.documentElement && document.documentElement.scrollTop){// Explorer 6 Strict
				y = document.documentElement.scrollTop;
			}
			else if (document.body){ // all other Explorers
				y = document.body.scrollTop;
			}
			return y;
	}
	
	function returnPageDimensions(){//returns the page size, max X and max Y
			var sh = document.body.scrollHeight;
			var oh= document.body.offsetHeight
			if (sh > oh){ // all but Explorer Mac
				y = document.body.scrollHeight;
			}
			else {// Explorer Mac;would also work in Explorer 6 Strict, Mozilla and Safari
				y = document.body.offsetHeight;
			}
			return y;
	}