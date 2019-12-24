function addNewParcel() {
    var number = document.forms["addParcelForm"]["number"].value;
    var area = document.forms["addParcelForm"]["area"].value;
    var operator = document.forms["addParcelForm"]["operator"].value;
    var fuel = document.forms["addParcelForm"]["fuel"].checked;


    var parcels = JSON.parse(sessionStorage.getItem('parcels'));
    if (parcels == null )  parcels = [];
    var parcel = {
        'number':   number,
        'area':     area,
        'operator': operator,
        'fuel':     fuel
    }
    parcels.push(parcel);

    sessionStorage.setItem('parcels', JSON.stringify(parcels)); //zapisz nową listę

    var pathname = window.location.pathname;

    $.post(pathname, parcels, function (returnedData)    {
        console.log(returnedData); 
    });



}
function show() {

    var parcels = JSON.parse(sessionStorage.getItem('parcels'));

    if (parcels!=null){

        html ='';
        for(i=0;i<parcels.length;i++)
        {
            html += parcels[i].number + parcels[i].area + parcels[i].operator + parcels[i].fuel;
        }
        document.getElementById('parcels').innerHTML = html;
    }
}