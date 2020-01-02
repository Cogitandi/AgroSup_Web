surname_reg = /^[a-zA-ZąćęłńóśżźĄĆĘŁŃÓŚŻŹ]{2,40}$/;    //reg nazwisko
firstName_reg = /^[a-zA-ZąćęłńóśżźĄĆĘŁŃÓŚŻŹ]{2,20}$/;    //reg imie
//arimrNumber_reg = /^[0-9]{11}$/;
telephone = /^([1-9]{1,1}[0-9]{1,1}(-)?[1-9]{1,1}[0-9]{6,6})|([1-9]{1,1}[0-9]{8,8})$/;    //reg telefon
email_reg = /^([\w\.\-]+)@([\w\-]+)((\.(\w){2,3})+)$/; //reg email
startYear_reg = /^(200[5-9]|20[12][0-9]|2030)$/;
postcode_reg = /^[0-9]{2}-[0-9]{3}$/; //reg imie
woj_reg = /^(?!Wybierz)/; // reg wojewodztwo
city_reg = /^[a-zA-ZąćęłńóśżźĄĆĘŁŃÓŚŻŹ]{2,20}$/; //reg miasto
adres_reg = /^[a-zA-ZąćęłńóśżźĄĆĘŁŃÓŚŻŹ]{2,40}$/; //reg adres
message_reg = /^[a-zA-ZąćęłńóśżźĄĆĘŁŃÓŚŻŹ]{10,40}$/; //reg text

FieldName_reg = /^\w+$/;
arimrNumber_reg = /^[0-9]{11}$/;
parcelNumber_reg = /^[0-9|/]+$/;
cultivatedArea_reg= /^[0-9]+$/;

function sprawdzPole(pole_id, obiektRegex) {
    if(pole_id === "radio") {
        if(document.getElementById("polecam").checked == false && document.getElementById("niePolecam").checked == false ) {
            polecam.classList.add('is-invalid');
            niePolecam.classList.add('is-invalid');
        } else {
            polecam.classList.remove('is-invalid');
            niePolecam.classList.remove('is-invalid');
        }
        return true;
    }
    if(pole_id === "allContact") {
        sprawdzPole('name',name_reg);
        sprawdzPole('lastname',lastname_reg);
        sprawdzPole('email',email_reg);
        sprawdzPole('adres',adres_reg);
        sprawdzPole('city',city_reg);
        sprawdzPole('wojewodztwo',woj_reg);
        sprawdzPole('message',message_reg);
        sprawdzPole('post-code',postcode_reg)
        return true;
    }
    if(pole_id === "allOpinion") {
        sprawdzPole('name',name_reg);
        sprawdzPole('lastname',lastname_reg);
        sprawdzPole('message',message_reg);
        sprawdzPole('radio',name_reg);
        
        return true;
    }
    //console.log("zle"); 
    //---------------------------------
    var obiektPole = document.getElementById(pole_id);
    if(!obiektRegex.test(obiektPole.value))  
    {
        obiektPole.classList.remove('is-valid');
        obiektPole.classList.add('is-invalid');

    } else { 
        obiektPole.classList.add('is-valid');
        obiektPole.classList.remove('is-invalid');
       // console.log("git"); 
    } 
    return true;

}