function cree(type) { return document.createElement (type); }
function byid(id  ) { return document.getElementById(id  ); }
function roundTo(val, digits) {
    if (!digits) digits = 0;

    const pow = Math.pow(10, digits);
    const mul = val * pow;
    const rnd = Math.round(mul);
    const ret = rnd / pow;

    return ret;
}