

function auth_change()
{
    if(document.getElementById('authority').value == 3) document.getElementById('facilityno_div').style.visibility =  'visible';
    else document.getElementById('facilityno_div').style.visibility =  'collapse';        
}