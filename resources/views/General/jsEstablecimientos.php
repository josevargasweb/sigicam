$("#select-motivo-liberacion").on("change", function(){
	var value=$(this).val();
	console.log("value: ", value);

	if(value == "alta"){
		$("#inputProcedenciaExtra").val("");
		$("#id_procedenciaExtra").val("");
		$("#inputProcedenciaExtra").attr('disabled', true);
		$("#id_procedenciaExtra").attr('disabled', true);
		$(".extraOculto").hide();
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$("#inputProcedencia").attr('disabled', true);
		$("#id_procedencia").attr('disabled', true);
		$(".estabOculto").hide();
		$("#input-alta").val("");
		$("#input-alta").attr('disabled', true);
		$(".altaOculto").hide();
		$("#fallecimientofecha").addClass("hidden");
		$("#fechaFallecimiento").removeClass("required");
		$(".medicoOculto").show();
		$("#medicoAlta").attr('disabled', false);
		$("#id_medico").attr('disabled', false);
		$("#medicoAlta").addClass("required");
	}
	else if(value ==  "otro" || value == "Otro" || value == "traslado extrasistema" || value == "derivacion otra institucion"){
		$("#inputProcedenciaExtra").val("");
		$("#id_procedenciaExtra").val("");
		$("#inputProcedenciaExtra").attr('disabled', true);
		$("#id_procedenciaExtra").attr('disabled', true);
		$(".extraOculto").hide();
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$("#inputProcedencia").attr('disabled', true);
		$("#id_procedencia").attr('disabled', true);
		$(".estabOculto").hide();
		$("#input-alta").val("");
		$(".altaOculto").show();
		$("#input-alta").val("");
		$("#input-alta").attr('disabled', false);
		$("#input-alta").addClass("required");
		$("#fallecimientofecha").addClass("hidden");	
		$("#fechaFallecimiento").removeClass("required");
		$(".medicoOculto").show();
		$("#medicoAlta").attr('disabled', false);
		$("#id_medico").attr('disabled', false);
	}	
	else if(value == "derivación"){
		$("#inputProcedenciaExtra").val("");
		$("#id_procedenciaExtra").val("");
		$("#inputProcedenciaExtra").attr('disabled', true);
		$("#id_procedenciaExtra").attr('disabled', true);
		$(".extraOculto").hide();
		$("#input-alta").val("");
		$("#input-alta").attr('disabled', true);
		$(".altaOculto").hide();
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$(".estabOculto").show();
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$("#inputProcedencia").attr('disabled', false);
		$("#id_procedencia").attr('disabled', false);
		$("#inputProcedencia").addClass("required");
		$("#fallecimientofecha").addClass("hidden");
		$("#fechaFallecimiento").removeClass("required");
		$("#fechaFallecimiento").val("");
		$(".medicoOculto").show();
		$("#medicoAlta").attr('disabled', false);
		$("#medicoAlta").val("");
		$("#id_medico").attr('disabled', false);
		$("#id_medico").val("");
	}
	else if(value == "traslado extra sistema"){
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$("#inputProcedencia").attr('disabled', true);
		$("#id_procedencia").attr('disabled', true);
		$(".estabOculto").hide();
		$("#input-alta").val("");
		$("#input-alta").attr('disabled', true);
		$(".altaOculto").hide();
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$(".extraOculto").show();
		$("#inputProcedenciaExtra").val("");
		$("#id_procedenciaExtra").val("");
		$("#inputProcedenciaExtra").attr('disabled', false);
		$("#inputProcedenciaExtra").addClass("required");
		$("#id_procedenciaExtra").attr('disabled', false);
		$("#fallecimientofecha").addClass("hidden");
		$("#fechaFallecimiento").removeClass("required");
		$("#fechaFallecimiento").val("");
		$(".medicoOculto").show();
		$("#medicoAlta").attr('disabled', false);
		$("#medicoAlta").val("");
		$("#id_medico").attr('disabled', false);
		$("#id_medico").val("");		
	}
	else if(value == "fallecimiento"){
		$("#fallecimientofecha").removeClass("hidden");

		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$("#inputProcedencia").attr('disabled', true);
		$("#id_procedencia").attr('disabled', true);
		$(".estabOculto").hide();
		$("#input-alta").val("");
		$("#input-alta").attr('disabled', true);
		$(".altaOculto").hide();
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$(".extraOculto").hide();
		$("#inputProcedenciaExtra").val("");
		$("#id_procedenciaExtra").val("");
		$("#inputProcedenciaExtra").attr('disabled', true);
		$("#id_procedenciaExtra").attr('disabled', true);
		$("#fechaFallecimiento").addClass("required");
		$("#fechaFallecimiento").attr('disabled',false);
		$(".medicoOculto").show();
		$("#medicoAlta").attr('disabled', false);
        $("#id_medico").attr('disabled', false);
        $("#medicoAlta").addClass("required");
	}
	else if(value == "Liberación de responsabilidad" || value == "liberación de responsabilidad" || value == "Fuga" || value == "fuga"){
		$("#inputProcedenciaExtra").val("");
		$("#id_procedenciaExtra").val("");
		$("#inputProcedenciaExtra").attr('disabled', true);
		$("#id_procedenciaExtra").attr('disabled', true);
		$(".estabOculto").hide();
		$("#input-alta").val("");
		$("#input-alta").attr('disabled', true);
		$(".altaOculto").hide();
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$("#inputProcedencia").attr('disabled', true);
		$("#id_procedencia").attr('disabled', true);
		$(".extraOculto").hide();
		$("#fallecimientofecha").addClass("hidden");
		$("#fechaFallecimiento").removeClass("required");

		$(".medicoOculto").show();
		$("#medicoAlta").attr('disabled', false);
        $("#id_medico").attr('disabled', false);
        $("#medicoAlta").addClass("required");
	}
	else{
		console.log(value);
		$("#inputProcedenciaExtra").val("");
		$("#id_procedenciaExtra").val("");
		$("#inputProcedenciaExtra").attr('disabled', true);
		$("#id_procedenciaExtra").attr('disabled', true);
		$(".estabOculto").hide();
		$("#input-alta").val("");
		$("#input-alta").attr('disabled', true);
		$(".altaOculto").hide();
		$("#inputProcedencia").val("");
		$("#id_procedencia").val("");
		$("#inputProcedencia").attr('disabled', true);
		$("#id_procedencia").attr('disabled', true);
		$(".extraOculto").hide();
		$("#fallecimientofecha").addClass("hidden");
		$("#fechaFallecimiento").removeClass("required");
		$(".medicoOculto").show();
		$("#medicoAlta").attr('disabled', false);
		$("#id_medico").attr('disabled', false);
	}
	
});

$("#modalAllta").on('hidden.bs.modal', function () {
    $('#formDarAlta').bootstrapValidator('resetForm', true);   
});