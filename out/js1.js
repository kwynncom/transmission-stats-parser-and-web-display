window.onload = function() { proc1(); }
function proc1() {
    const tabele = cree('table');
    document.getElementById('stattabc').append(tabele); 
    tabele.className = 'statTable';
    
    const bigd = KW_TSTATS_INIT_O;

    const fs   = bigd['headers'];
    const trh = cree('tr');
    
    for (let i=0; i < fs.length; i++) {
	const hv = fs[i];
	if (hv === 'hide') continue;
	let th = cree('th');
	th.innerHTML = hv;
	trh.append(th);
    }
    tabele.append(trh);
    
    for (let i=0; i < bigd['v'].length; i++) {
	let r  = bigd['v'][i];
        let tre = cree('tr');
        tabele.append(tre);
	
	for (let j=0; j < r.length; j++) {
	    let head = fs[j];
	    if (head === 'hide') continue;
	    let tde = cree('td');
	    let tv =  r[j];
	    if (head === 'rat') tde.className = 'rattd';
	    if (head === 'MB')  tv = r[j].toFixed(1);
	    tde.innerHTML = tv;
	    tre.append(tde);	
	}
    }
}
