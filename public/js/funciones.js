var esRutValido = function(ruti, dvi) {
    var rut = ruti + "-" + dvi;
    if (rut.length < 7)
        return(false)
    i1 = rut.indexOf("-");
    dv = rut.substr(i1 + 1);
    dv = dv.toUpperCase();
    nu = rut.substr(0, i1);

    cnt = 0;
    suma = 0;
    for (i = nu.length - 1; i >= 0; i--) {
        dig = nu.substr(i, 1);
        fc = cnt + 2;
        suma += parseInt(dig) * fc;
        cnt = (cnt + 1) % 6;
    }
    dvok = 11 - (suma % 11);
    if (dvok == 11)
        dvokstr = "0";
    if (dvok == 10)
        dvokstr = "K";
    if ((dvok != 11) && (dvok != 10))
        dvokstr = "" + dvok;
    if (dvokstr == dv)
        return(true);
    else
        return(false);
}

var Fn = {
    validaRut : function (rutCompleto) {
        if (!/^[0-9]+-[0-9kK]{1}$/.test( rutCompleto ))
            return false;
        var tmp     = rutCompleto.split('-');
        var digv    = tmp[1];
        var rut     = tmp[0];
        if ( digv == 'K' ) digv = 'k' ;
        return (Fn.dv(rut) == digv );
    },
    dv : function(T){
        var M=0,S=1;
        for(;T;T=Math.floor(T/10))
            S=(S+T%10*(9-M++%6))%11;
        return S?S-1:'k';
    }
}
var Fn = {
	// Valida el rut con su cadena completa "XXXXXXXX-X"
	validaRut : function (rutCompleto) {
		if (!/^[0-9]+[-|‐]{1}[0-9kK]{1}$/.test( rutCompleto ))
			return false;
		var tmp 	= rutCompleto.split('-');
		var digv	= tmp[1]; 
		var rut 	= tmp[0];
		if ( digv == 'K' ) digv = 'k' ;
		return (Fn.dv(rut) == digv );
	},
	dv : function(T){
		var M=0,S=1;
		for(;T;T=Math.floor(T/10))
			S=(S+T%10*(9-M++%6))%11;
		return S?S-1:'k';
	}
}


var validarCasoPaciente = function(rut_sin_dv, url){
    $.ajax({
        dataType:json,
        data:{rut: rut_sin_dv},
        url: url,
        success: function(data){
            return
        },
        error: function(data){

        }
    });
}

var getCurrentDate = function() {
    var d = new Date();
    var curr_date = (d.getDate() < 10) ? "0" + d.getDate() : d.getDate();
    var curr_month = d.getMonth() + 1;
    curr_month = (curr_month < 10) ? "0" + curr_month : curr_month;
    var curr_year = d.getFullYear();
    var formattedDate = curr_date + "-" + curr_month + "-" + curr_year;
    return formattedDate;
}

var validarFormatoFecha = function(fecha) {
    var RegExPattern = /^\d{1,2}\-\d{1,2}\-\d{2,4}$/;
    if(!fecha.match(RegExPattern)) return false;
    return true;
}

var validarFormatoFechaHora = function(fecha){
    var RegExPattern = /^\d{1,2}\-\d{1,2}\-\d{2,4} \d{1,2}:\d{1,2}$/;
    //var RegExPattern = /^[0-3][0-9]-[0-1][0-9]-[0-9][0-9][0-9][0-9] [0-2][0-9]:[0-5][0-9]:[0-5][0-9]$/
    if(!fecha.match(RegExPattern)) return false;
    return true;
}


var esFechaMayor=function(fecha){

    var fechaPar = new Date();
    split = fecha.split("-");
    fechaPar.setFullYear(split[2],split[1]-1,split[0]);
    var dtToday = new Date();

    //return false;
    if(fechaPar > dtToday)
    {
        return true;
    }
return false;

    /*if (Date.parse(fecha) > Date.parse(current)) return true;
    return false;*/
}

var compararFecha=function(fechaMenor, fechaMayor){

    var fechaParMenor = new Date();
    split = fechaMenor.split("-");
    fechaParMenor.setFullYear(split[2],split[1]-1,split[0]);
    
    var fechaParMayor = new Date();
    split = fechaMayor.split("-");
    fechaParMayor.setFullYear(split[2],split[1]-1,split[0]);

    //return false;
    if(fechaParMenor > fechaParMayor)
    {
        return true;
    }
    return false;

    /*if (Date.parse(fecha) > Date.parse(current)) return true;
    return false;*/
}

var compararFechaIndicacion=function(fechaMenor, fechaMayor){

    split_menor = fechaMenor.split("-");
    split_2_menor = split_menor[2].split(" ");
    split_hr_min_menor = split_2_menor[1].split(":");

    var fechaParMenor = new Date(split_2_menor[0],split_menor[1]-1, split_menor[0],split_hr_min_menor[0], split_hr_min_menor[1]);

    split_mayor = fechaMayor.split("-");
    split_2_mayor = split_mayor[2].split(" ");
    split_hr_min_mayor = split_2_mayor[1].split(":");

    var fechaParMayor = new Date(split_2_mayor[0],split_mayor[1]-1, split_mayor[0],split_hr_min_mayor[0], split_hr_min_mayor[1]);

    if(fechaParMenor <= fechaParMayor)
    {
        return true;
    }
    return false;
}

var resizeMapaCamas = function(id){
    var h3mayor = 0;
    //console.log($(".mapa-camas-class h3"));
    $(".mapa-camas-class h3").each(function(){
        if ( $(this).height() > h3mayor ) h3mayor = $(this).height();
    });

    $(".mapa-camas-class h3").each(function(){
        if(h3mayor > 0) $(this).height(h3mayor);
    });

    $(".mapa-camas-class .fila-salas").each(function(){
        var altura = 0;
        var up = this;
        $(up).find(".bloque-sala").each(function(){
            var c = $(this).find(".row-cama").length
            //console.log("filas: " + c);
            var h = 0;
            $(this).find(".row-cama").each(function(){
                //console.log("row-cama h: " + $(this).height());
                if($(this).height() > h) h = $(this).height();
            });
            var cxh = c*h;
            //console.log(cxh);
            //console.log("cxh: "+cxh);
            if (cxh > altura) altura = cxh;
        });

        $(up).find(".panel-body").each(function(){
            if(altura > 0) $(this).height(altura);
        });
    });
}

var crearMapaCamas=function(mapaDiv, data){
    var rango=2;
    if(!data.tiene) $("#derivar").show();
    $("#"+mapaDiv).empty();
    $("#msgCamaSelected").hide();
    $("#cama").val("");
    $("#sala").val("");
    var salas = $.map(data.salas, function(value, index) {
        return [value];
    });
    var nombres = $.map(data.nombres, function(value, index) {
        return [value];
    });

    
    var totalDivs=(Math.round(salas.length/rango) == 0) ? 1 : Math.round(salas.length/rango);
    for(var i=1; i<=totalDivs+1; i++){
        var id=mapaDiv+"-div-sala-"+i;
        var div="<div id='"+id+"' class='row fila-salas mapa-camas-class' style='margin: 0;'>"+"</div><br>";
        $("#"+mapaDiv).append(div);
    }
    var range=0;
    var divIt=1;
    var sala=1;
    var fila4=4;
    var camas = 0;
    while(range < salas.length){
        var salasSelect=salas.slice(range, range+rango);
        for(var i=0; i<salasSelect.length; i++){
            var idSala=mapaDiv+"_sala_"+i;
            
            var divSala="<div id='"+mapaDiv+"-sala-"+sala+"' class='col-md-5 bloque-sala'> <div class='panel panel-primary'> <div class='panel-heading'><h3 id='"+idSala+"' class='headerCama'></h3></div> <div class='panel-body'>";
            var salasSelected = $.map(salasSelect[i], function(value, index) {
                return [value];
            });
            var cada4=1;
            for(var k=1; k<=salasSelected.length; k++){
                var div="";
                if(cada4 == 1) div="<div class='row  row-cama'>";
                var id=mapaDiv+"-div-cama-"+sala+"-"+k;
                
                nombrePaciente = salasSelect[i][k-1].nombrePaciente;
                div+="<div id='"+id+"' class='col-md-3 divContenedorCama' style='margin-top: 0px' data-nombre='"+nombrePaciente+"' data-toggle='tooltip' data-placement='top'></div>";
                camas++;
                divSala+=div;
                cada4++;
                if(cada4 == fila4+1){
                    cada4=1;
                    divSala+="</div>";
                }
            }
            divSala+="</div>";
            $("#"+mapaDiv+"-div-sala-"+divIt).append(divSala);
            var rangeCama=0;
            for(var j=0; j<salasSelected.length; j++){
                $("#"+mapaDiv+"_sala_"+j).text("Sala "+salasSelect[i][j].sala);
                $("#"+mapaDiv+"-div-cama-"+sala+"-"+(j+1)).append(salasSelect[i][j].img);
                //console.log();
            }
            sala+=1;
        }
        range+=rango;
        divIt+=1;
    }

    $("#"+mapaDiv+" h3").each(function(index, e){
        var sala=nombres[index];
        $(this).text(sala);
    });

    resizeMapaCamas(mapaDiv);

}

var soloNumeros=function(e){
    var keynum = window.event ? window.event.keyCode : e.which;
    if ((keynum == 8) || (keynum == 46))
        return true;
    return /\d/.test(String.fromCharCode(keynum));
}

var mensaje=function(data){
    if(data.exito){
        swalExito.fire({
          title: "Exito!",
          text: data["exito"],
          didOpen: function()  {
            setTimeout(function() {
              location.reload();
            }, 2000);
          },
        });

    }
    if(data.error){
       swalError.fire({
         title: "Error",
         text: data.error,
       });
        //console.log(data.error);
    }
}

function calcularEdad(fecha) {
    var hoy = new Date();
    var cumpleanos = new Date(fecha);
    var edad = hoy.getFullYear() - cumpleanos.getFullYear();
    var m = hoy.getMonth() - cumpleanos.getMonth();

    if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
        edad--;
    }

    return edad;
}

function truncate (num, places) {
    return Math.trunc(num * Math.pow(10, places)) / Math.pow(10, places);
}

function dosDias() {
  var today = moment().startOf("day");
  if (today.isoWeekday() >= 1 && today.isoWeekday() <= 4) {
    // Today is Monday - Wednesday
    return [today.clone(), today.clone().add(1, "d")];
  } else if (today.isoWeekday() == 5) {
    // Today is Friday
    return [today.clone(), today.clone().add(3, "d")];
  } else if (today.isoWeekday() > 5) {
    // Today is Saturday or Sunday
    var startWeek = today.clone().startOf("isoWeek");
    var newWeek = startWeek.clone().add(1, "week");
    return [newWeek.clone().isoWeekday(1), newWeek.clone().isoWeekday(2)];
  }
}

function cambiarAComa(num){
    return num.toString().replace(".", ",");
}

function verificarfloat(num) {
  if (num % 1 != 0) {
    return (num = num.toFixed(1).replace(".", ","));
  }
  return num;
}

function tieneDatos(array) {
     let resultado;
     array.forEach( function(element){
       if (element !== null) {
         return (resultado = true);
       }
     });
     return (resultado = resultado == undefined ? false : resultado);
}


//sweet alert
var swalExito = Swal.mixin({
  icon: 'success',
  allowOutsideClick: false,
  allowEscapeKey: false,
  showConfirmButton: false,
  timer:3000
});


var swalError = Swal.mixin({
  icon: "error",
  allowOutsideClick: false,
  allowEscapeKey: false,
  showDenyButton: true,
  showConfirmButton: false,
  denyButtonText: 'OK'
});

var swalAviso = Swal.mixin({
    icon: "info",
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    timer:3000
});

var swalInfo = Swal.mixin({
  icon: "info"
});
var swalInfo2 = Swal.mixin({
  icon: "info",
  allowOutsideClick: false,
  allowEscapeKey: false
});

var swalWarning = Swal.mixin({
  icon: "warning",
  allowOutsideClick: false,
  allowEscapeKey: false
});

var swalNormal = Swal.mixin({
  allowOutsideClick: false,
  allowEscapeKey: false
});


var swalPregunta = Swal.mixin({
  icon: "question",
  allowOutsideClick: false,
  allowEscapeKey: false,
  showDenyButton: true,
  reverseButtons: true,
});

var swalPregunta2 = Swal.mixin({
  icon: "question",
  allowOutsideClick: false,
  allowEscapeKey: false,
  showDenyButton: true,
  showCancelButton: true,
  cancelButtonColor: '#d33',
  confirmButtonColor: '#3085d6',
  denyButtonColor: '#5cb85c',
  cancelButtonText: 'Cancelar',
  confirmButtonText: 'Usar Relacionado',
  denyButtonText: 'Crear Nuevo',
  reverseButtons: true,
});

var swalCargando = Swal.mixin({
    title: 'Espere mientras carga porfavor',
    allowOutsideClick: false,
    allowEscapeKey: false,
    showConfirmButton: false,
    willOpen: function () {
        Swal.showLoading ();
    },
    didClose: function () {
        Swal.hideLoading();
    },
});

var swalSolicitudTraslado = Swal.mixin({
    icon: "info",
    allowOutsideClick: false,
    allowEscapeKey: false,
    reverseButtons: true,
    //showDenyButton: true,
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    //denyButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Aceptar traslado',
    //denyButtonText: 'Eliminar solicitud de traslado',
    cancelButtonText: 'Cancelar',
});

