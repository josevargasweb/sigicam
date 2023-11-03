$.fn.bootstrapValidator.validators._not_past_datetime = {
    validate: function(validator, $field, options) {

        try {

            var value = $field.val();

            if(typeof value !== 'undefined' && value !== null && value.trim() !==""){

                var date = value.split(" ")[0];
                var time = value.split(" ")[1];
    
                var day = parseInt(date.split("/")[0]);
                var month = parseInt(date.split("/")[1]);
                var year = parseInt(date.split("/")[2]);
                var hour = parseInt(time.split(":")[0]);
                var minute = parseInt(time.split(":")[1]);
                var second = parseInt(time.split(":")[2]);
    
                var now = new Date();
                var input_date = new Date(year, month-1, day, hour, minute, second);
    
                if(input_date < now){ return false;}

            }


            return true;
        } 
        catch (error) {
            return false;
        }



    }
};

$.fn.bootstrapValidator.validators._not_future_datetime = {
    validate: function(validator, $field, options) {

        try {

            var value = $field.val();

            if(typeof value !== 'undefined' && value !== null && value.trim() !==""){

                var date = value.split(" ")[0];
                var time = value.split(" ")[1];
    
                var day = parseInt(date.split("/")[0]);
                var month = parseInt(date.split("/")[1]);
                var year = parseInt(date.split("/")[2]);
                var hour = parseInt(time.split(":")[0]);
                var minute = parseInt(time.split(":")[1]);
                var second = parseInt(time.split(":")[2]);
    
                var now = new Date();
                var input_date = new Date(year, month-1, day, hour, minute, second);
    
                if(input_date > now){ return false;}

            }


            return true;
        } 
        catch (error) {
            return false;
        }



    }
};

$.fn.bootstrapValidator.validators._not_future_date = {
    validate: function(validator, $field, options) {

        try {

            var value = $field.val();

            if(typeof value !== 'undefined' && value !== null && value.trim() !==""){

                var date = value.split(" ")[0];
    
                var day = parseInt(date.split("/")[0]);
                var month = parseInt(date.split("/")[1]);
                var year = parseInt(date.split("/")[2]);
    
                var now = new Date();
                var input_date = new Date(year, month-1, day);
    
                if(input_date > now){ return false;}

            }


            return true;
        } 
        catch (error) {
            return false;
        }



    }
};




function notInputExceptBackSpace(event){

    var char = event.which || event.keyCode;
    if (char != 8){event.preventDefault(); return false; }

}


function initFormValidation(form) {
    var bv = form.data('bootstrapValidator');

    if (bv != undefined){
        bv.destroy();
    }
    form.bootstrapValidator(bv_options)
    bv = form.data('bootstrapValidator');
    bv.validate();
    return bv;
}

/**
 * Check si un arreglo de enteros esta ordenado
 */
function sortedIntArr(arr){
    let second_index;
	for(let first_index = 0; first_index < arr.length; first_index++){
  	  second_index = first_index + 1;
      if(arr[second_index] - arr[first_index] < 0) return false;
    }
    return true;
}

/**
 * Cuenta decimales de un numero
 */
Number.prototype.countDecimals = function () {

    if (Math.floor(this.valueOf()) === this.valueOf()) return 0;

    var str = this.toString();
    if (str.indexOf(".") !== -1 && str.indexOf("-") !== -1) {
        return str.split("-")[1] || 0;
    } else if (str.indexOf(".") !== -1) {
        return str.split(".")[1].length || 0;
    }
    return str.split("-")[1] || 0;
}