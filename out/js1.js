window.onload = function() { proc1(); }

function shouldHide(fi, head) {
    if (fi && fi[head] && fi[head] === 'hide') return true;
    return false;
}

function proc1() {
    const tabele = cree('table');
    document.getElementById('stattabc').append(tabele); 
    tabele.className = 'statTable';
    
    const bigd = KW_TSTATS_INIT_O;

    byid('lmago').innerHTML = bigd['lmago'];
    const fs   = bigd['headers'];
    const fi   = bigd['finfo'];
    const trh = cree('tr');
    
    for (let i=0; i < fs.length; i++) {
	const head = fs[i];
	
	if (shouldHide(fi, head)) continue;

	const th = cree('th');
	th.innerHTML = head;
	trh.append(th);
    }
    tabele.append(trh);
    
    for (let i=0; i < bigd['v'].length; i++) {
	let r  = bigd['v'][i];
        let tre = cree('tr');
        tabele.append(tre);
	
	for (let j=0; j < r.length; j++) {
	    const head = fs[j];
	    
	    if (shouldHide(fi, head)) continue;

	    const tde = cree('td');
	    let tv =  r[j];
	    if      (head === 'rat') tde.className = 'rattd';
	    else if (head === 'MB')  tv = r[j].toFixed(1);
	    else if (head === 'myr') tv = r[j].toFixed(3);
	    
	    tde.innerHTML = tv;
	    tre.append(tde);	
	}
    }
    
    for (let i=0; i < bigd['perm'].length; i++) {
	const head = bigd['permheaders'][i];
	if (shouldHide(fi, head)) continue;	
	byid(head).innerHTML = bigd['perm'][i];
    }
}
