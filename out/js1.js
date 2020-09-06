window.onload = function() { proc1(); }

function shouldHide(fi, head) {
    if (fi && fi[head] && fi[head] === 'hide') return true;
    return false;
}

function proc30Clock(ele, ltrts, td0) {
    clockSetup(ele, ltrts, td0);
}

function proc20Clock(tab, fs, bigd) {
    
    const asofi = fs.indexOf('asof');
    const tr = cree('tr');
    
    let td0;
    
    for(let i=0; i < 3; i++) {
	const td = cree('td');
	if      (i === 0) { td.colSpan = asofi; td0 = td; }
	else if (i === 1) proc30Clock(td, bigd['ltrts'], td0);
	else if (i === 2) td.colSpan = fs.length - asofi + 1;
	tr.append(td);	
    }
    
    tab.append(tr);
    
}

function proc1() {
    const tabele = cree('table');
    document.getElementById('stattabc').append(tabele); 
    tabele.className = 'statTable';
    
    const bigd = KW_TSTATS_INIT_O;

    // byid('lmago').innerHTML = bigd['lmago'];
    const fs   = bigd['headers'];
    const fi   = bigd['finfo'];
    const trh = cree('tr');
    
    proc20Clock(tabele, fs, bigd);

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
