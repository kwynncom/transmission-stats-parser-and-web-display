if (typeof dif !== 'function') {
    function dif(ele, v) {
	if (!ele || (!ele.innerHTML && ele.innerHTML !== '')) return;
	ele.innerHTML = v;
    }
}

function clockTickDisplay(ele, ss, ltrts) { 
   
    const displayDateO = new Date();
    let    h = displayDateO.getHours(); 
    let    m = displayDateO.getMinutes(); 
    let    s = displayDateO.getSeconds();
    let    a;
    if (h < 12) {
	if (h === 0 ) h  = 12;
	a = 'AM';
	if (h < 10)   h = '0' + h;	
    }
    else { 
	h -= 12;
	if (h < 10)   h = '0' + h;
	a = 'PM';
    }
    m = (m < 10) ? '0' + m : m;
    s = (s < 10) ? '0' + s : s;
    let st = h + ':' + m + a;
    
    st = 'now: ' + st;
    
    dif(ele, st);
    
    s = '&nbsp;(' + s + 's)';
    dif(ss, s);
    
    const tsms = displayDateO.getTime();
    const ts = parseInt(tsms / 1000);
    const dmr = (ts - ltrts) / 60;
    const dm  = roundTo(dmr, 2).toFixed(2);
    dif(byid('lmago'), dm);
}

function clockTickStart(ele, ss, ltrts) { 
    clockTickDisplay(ele, ss, ltrts);
    setInterval(function() { 
	clockTickDisplay(ele, ss, ltrts); 
    }, 1000);   
}

function clockSetup(ele, ltrts, td0) {
       
   ele.className = 'clock';
   
   const ms = cree('span');
   const ss = cree('span');
   ele.append(ms);
   ele.append(ss);
   
   td0.innerHTML = "last tracker check (min ago): <span id='lmago' />";
   const magoe = byid('lmago');
    
   clockTickStart(ms, ss, ltrts);
    
}
