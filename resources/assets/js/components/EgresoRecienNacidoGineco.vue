<template>
    <div>
        <br>
        <div class="formulario" style="width:auto">
            <div class="panel panel-default">
                <div class="panel-heading panel-info">
                    <h4>Egreso Recien nacido</h4>
                    <button type="button" @click="abrirModal('agregar')" class="btn btn-success pull-right" style="margin-top: -37px;">Agregar</button>
                </div>
                <div class="panel-body">
                    <legend>Listado de recien Nacidos</legend>
                    <div class="pull-right">
                        <input type="text" v-model="buscar" @keyup="obtenerEgresosRecienNacidoCaso(caso,buscar,1)" class="form-control input-sm" placeholder="Texto a buscar">
                    </div>
                    <table id="tableRecienNacidos" class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Opciones</th>
                                <th>Run</th>
                                <th>Nombre Completo</th>
                                <th>Fecha egreso</th>
                                <th>Destino</th>
                                <th>Fecha control CESFAM</th>
                            </tr>
                        </thead>
                        <tbody v-if="rnegresados.length">
                            <tr v-for="rne in rnegresados" :key="rne.id">
                                <td style="width: 10%;">
                                    <button type="button" title="Actualizar" @click="abrirModal('actualizar',rne)" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-edit"></i></button>
                                    <button type="button" title="Eliminar" @click="eliminar(rne.id)" class="btn btn-danger btn-sm"><i class="glyphicon glyphicon-trash"></i></button>
                                </td>
                                <td>{{rne.run}} - {{rne.dv}}</td>
                                <td>{{rne.nombre}} {{rne.paterno}} {{rne.materno}}</td>
                                <td>{{rne.fecha_egreso | formatDate}}</td>
                                <td>{{rne.destino}}</td>
                                <td>{{rne.fecha_cesfam | formatDate}}</td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr>
                                <td colspan="6"><p style="text-align: center;">Sin información</p></td>
                            </tr>
                        </tbody>
                    </table>
                    <nav>
                        <ul class="pagination pull-right">
                            <li class="page-item" v-if="pagination.current_page > 1">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1,buscar)">Ant</a>
                            </li>
                            <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(page,buscar)" v-text="page"></a>
                            </li>
                            <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1,buscar)">Sig</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <br><br><br><br><br><br><br><br><br><br>
        </div>
        <div class="modal fade" tabindex="-1" :class="{'mostrar' : modal}" role="dialog" aria-labelledby="myModalLabel" style="display: none; overflow-y: scroll;" aria-hidden="true" id="myModal">
            <div class="modal-dialog modal-xl" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" @click="cerrarModal()" aria-label="Close">
                            <span aria-hidden="true">X</span>
                        </button>
                        <h4 class="modal-title" v-text="tituloModal"></h4>
                    </div>
                    <div class="modal-body">
                        <form action="" id="formEgresoRecienNacido">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group"> 
                                        <label>Run</label>
                                        <div class="input-group" style="z-index: 0;">
                                            <input type="text" v-model="run" class="form-control" name="run" id="run" @keyup="revalidarRun()">
                                            <span class="input-group-addon"> - </span>
                                            <input type="text" v-model="dv" class="form-control" name="dv" id="dv" style="width: 40px;">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-md-offset-1">
                                    <div class="form-group">
                                        <label>Numero ficha</label>
                                        <input type="text" v-model="ficha" class="form-control" name="ficha" id="ficha">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Nombre(s)</label>
                                        <input type="text" v-model="nombre" class="form-control" name="nombre" id="nombre">
                                    </div>
                                </div>
                                <div class="col-md-3 col-md-offset-1">
                                    <div class="form-group">
                                        <label>Apellido Paterno</label>
                                        <input type="text" v-model="paterno" class="form-control" name="paterno" id="paterno">
                                    </div>
                                </div>
                                <div class="col-md-3 col-md-offset-1">
                                    <div class="form-group">
                                        <label>Apellido Materno</label>
                                        <input type="text" v-model="materno" class="form-control" name="materno" id="materno">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Nombre de cuidador o familiar</label>
                                        <input type="text" v-model="cuidador" class="form-control" name="cuidador" id="cuidador">
                                    </div>
                                </div>
                                <div class="col-md-3 col-md-offset-1">
                                    <div class="form-group">
                                        <label>Vinculo familiar</label>
                                        <input type="text" v-model="vinculo" class="form-control" name="vinculo" id="vinculo">
                                    </div>
                                </div>
                                <div class="col-md-3 col-md-offset-1">
                                    <div class="form-group">
                                        <label>Telefono de cuidador o familiar</label>
                                        <input type="text" v-model="telefono_cuidador" class="form-control" name="telefono_cuidador" id="telefono_cuidador">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-11">
                                    <div class="form-group">
                                        <label>Diagnóstico médico</label>
                                        <input type="text" v-model="diagnostico_medico" class="form-control" name="diagnostico_medico" id="diagnostico_medico">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-11">
                                    <label>Otros diagnosticos</label>
                                    <table class="table table-striped table-bordered table-hover" id="tablaDiagnosticos">
                                        <thead>
                                            <tr>
                                                <th>N°</th>
                                                <th>Diagnóstico</th>
                                                <th>Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(diagnostico, index) in otros_diagnosticos" :key="index">
                                                <td>{{index + 1}}</td>
                                                <td><input type="text" class="form-control form-group" v-bind:id="index" v-model="diagnostico.diagnostico"></td>
                                                <td><button type="button" class="btn btn-danger" @click="eliminarDiagnostico(index,diagnostico)">Eliminar</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary" @click="agregarDiagnostico()">+ Diagnósticos</button>
                                </div>
                            </div>
                            <div class="row">
                                <br>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <ValidationProvider name="fecha egreso" rules="required" tag="div" v-slot="{ errors }">
                                            <label>Fecha de egreso</label>
                                            <date-picker v-model="fecha_egreso" type="datetime" valueType="format" :format="momentFormat" name="fecha_egreso" id="fecha_egreso"></date-picker>
                                            <br><span style="color: #a94442">{{ (errors[0]) ? "El campo es obligatorio" : "" }}</span>
                                        </ValidationProvider>
                                    </div>
                                </div>
                                <div class="col-md-3 col-md-offset-1">
                                    <div class="form-group">
                                        <label>Destino</label>
                                        <select class="form-control" v-model="destino" name="destino" id="destino">
                                            <option value="" selected>Seleccione</option>
                                            <option value="mortinato">Mortinato</option>
                                            <option value="rn sano">RN sano</option>
                                            <option value="rn con patología">RN con patología</option>
                                            <option value="traslado">Traslado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 col-md-offset-1">
                                    <div class="form-group">
                                        <ValidationProvider name="fecha cesfam" rules="required" v-slot="{ errors }" tag="div">
                                            <label>Fecha de control en CESFAM</label>
                                            <date-picker v-model="fecha_cesfam" type="datetime" valueType="format" :format="momentFormat" name="fecha_cesfam" id="fecha_cesfam"></date-picker>
                                            <br><span style="color: #a94442">{{ (errors[0]) ? "El campo es obligatorio" : "" }}</span>
                                        </ValidationProvider>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-11">
                                    <label>Cuidados al alta</label>
                                    <table class="table table-striped table-bordered table-hover" id="tablaCuidados">
                                        <thead>
                                            <tr>
                                                <th>N°</th>
                                                <th>Cuidados</th>
                                                <th>Opciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(cuidado, index) in cuidados_alta" :key="index">
                                                <td>{{index + 1}}</td>
                                                <td><input type="text" class="form-control" v-bind:id="index" v-model="cuidado.cuidado"></td>
                                                <td><button type="button" class="btn btn-danger" @click="eliminarCuidado(index,cuidado)">Eliminar</button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-primary" @click="agregarCuidado">+ Cuidados</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" @click="cerrarModal()">Cerrar</button>
                        <button type="button" v-if="tipoAccion==1" class="btn btn-primary" @click="egresar()">Guardar</button>
                        <button type="button" v-if="tipoAccion==2" class="btn btn-primary" @click="actualizar()">Actualizar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import DatePicker from 'vue2-datepicker';
    import 'vue2-datepicker/index.css';
    // import { Validator } from 'vee-validate';
    // import es from 'vee-validate/dist/locale/es.json';
    import { ValidationProvider } from 'vee-validate/dist/vee-validate.full';
    export default {
            // components: { DatePicker, VeeValidate, ValidationProvider },11
            components: { DatePicker, ValidationProvider },
            props: ['idCaso'], //para recibir el caso entregado al momento de llamar al componente
            data(){
                return {
                    id_egreso_rn: '',
                    caso: '',
                    modal: 0, //variable para ver mostrar o no el modal
                    run: '',
                    dv: '',
                    ficha: '',
                    nombre: '',
                    materno: '',
                    paterno: '',
                    cuidador: '',
                    vinculo: '',
                    telefono_cuidador: '',
                    diagnostico_medico: '',
                    otros_diagnosticos: [],
                    fecha_egreso: '',
                    destino: '',
                    fecha_cesfam: '',
                    cuidados_alta: [],
                    tipoAccion: 0,
                    tituloModal: '',
                    rnegresados: [],
                    pagination : {
                        'total' : 0,
                        'current_page' : 0,
                        'per_page' : 0,
                        'last_page' : 0,
                        'from' : 0,
                        'to' : 0,
                    },
                    offset: 5,
                    buscar: '',
                    momentFormat: 'DD-MM-YYYY HH:mm',
                    // errors: []
                }
            },
            computed:{
                isActived: function(){
                    return this.pagination.current_page;
                },
                pagesNumber: function(){
                    if(!this.pagination.to){
                        return [];
                    }

                    var from = this.pagination.current_page - this.offset;
                    if(from < 1){
                        from = 1;
                    }

                    var to = from + (this.offset * 2);
                    if(to >= this.pagination.last_page){
                        to = this.pagination.last_page;
                    }

                    var pagesArray = [];
                    while(from <= to){
                        pagesArray.push(from);
                        from++;
                    }
                    return pagesArray;
                }
            },
            mounted(){
                this.caso = this.idCaso;
                this.obtenerEgresosRecienNacidoCaso(this.caso,this.buscar,1);
                this.recargarEgresosRecienNacidoCaso();
                this.bindValidation();
            },
            methods: {
                cambiarPagina(page,buscar){
                    let me = this;
                    me.pagination.current_page = page;
                    me.obtenerEgresosRecienNacidoCaso(me.caso,buscar, page);
                },
                obtenerEgresosRecienNacidoCaso(caso, buscar, page){
                    let me = this;
                    axios.get('../obtenerEgresosRn?page='+ page +'&caso=' + caso + '&buscar=' + buscar).then(function (response) {
                        me.rnegresados = [];
                        me.rnegresados = response.data.egresos;
                        me.pagination= response.data.pagination;
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
                recargarEgresosRecienNacidoCaso(){
                    let me = this;
                $("#EgresoRn").click(function(){
                    me.obtenerEgresosRecienNacidoCaso(me.caso,me.buscar,1);
                });
                },
                abrirModal(accion, data = []){
                    switch(accion){
                        case 'agregar': {
                            this.modal = 1;
                            // var modal = document.getElementById("myModal");
                            // modal.style.display = "block";
                            this.tituloModal = 'Agregar egreso recien nacido';
                            this.run = '';
                            this.dv = '';
                            this.ficha = '';
                            this.nombre = '';
                            this.paterno = '';
                            this.materno = '';
                            this.cuidador = '';
                            this.vinculo = '';
                            this.telefono_cuidador = '';
                            this.diagnostico_medico = '';
                            this.fecha_egreso = '';
                            this.destino = '';
                            this.fecha_cesfam = '';
                            this.tipoAccion = 1;
                            break;
                        }

                        case 'actualizar':{
                            this.modal = 1;
                            this.tituloModal = 'Editar egreso recien nacido';
                            this.run = data['run'];
                            this.dv = data['dv'];
                            this.ficha = data['ficha'];
                            this.nombre = data['nombre'];
                            this.paterno = data['paterno'];
                            this.materno = data['materno'];
                            this.cuidador = data['cuidador'];
                            this.vinculo = data['vinculo'];
                            this.telefono_cuidador = data['telefono_cuidador'];
                            this.diagnostico_medico = data['diagnostico_medico'];
                            this.otros_diagnosticos = data['otros_diagnosticos'];
                            this.fecha_egreso = moment(data['fecha_egreso']).format('DD-MM-YYYY HH:mm');
                            this.destino = data['destino'];
                            this.fecha_cesfam = moment(data['fecha_cesfam']).format('DD-MM-YYYY HH:mm');
                            this.cuidados_alta = data['cuidados_alta'];
                            this.id_egreso_rn = data["id"];
                            this.tipoAccion = 2;
                            break;
                        }
                    }
                },
                cerrarModal(){
                    this.modal = 0;
                    this.run = '';
                    this.dv = '';
                    this.ficha = '';
                    this.nombre = '';
                    this.paterno = '';
                    this.materno = '';
                    this.cuidador = '';
                    this.vinculo = '';
                    this.telefono_cuidador = '';
                    this.diagnostico_medico = '';
                    this.otros_diagnosticos = [];
                    this.fecha_egreso = '';
                    this.destino = '';
                    this.fecha_cesfam = '';
                    this.cuidados_alta = [];
                    this.resetValidator();
                    this.obtenerEgresosRecienNacidoCaso(this.caso,this.buscar,1);
                    this.error_o_diag = '';
                    this.error_c_alta = '';
                },
                agregarDiagnostico() {
                    this.otros_diagnosticos.push({});
                },
                eliminarDiagnostico(index, diagnostico) {
                    var idx = this.otros_diagnosticos.indexOf(diagnostico);
                    if(idx > -1){
                        this.otros_diagnosticos.splice(idx,1);
                    }
                },
                agregarCuidado() {
                    this.cuidados_alta.push({});
                },
                eliminarCuidado(index, cuidado) {
                    var idx = this.cuidados_alta.indexOf(cuidado);
                    if(idx > -1){
                        this.cuidados_alta.splice(idx,1);
                    }
                },
                egresar(){
                    let me = this;        
                    $("#formEgresoRecienNacido").data('bootstrapValidator').validate();
                    if($("#formEgresoRecienNacido").data('bootstrapValidator').isValid()){ 
                        axios.post('../egresarRecienNacido', {
                            'caso': this.caso,
                            'run': this.run,
                            'dv': this.dv,
                            'ficha': this.ficha,
                            'nombre': this.nombre,
                            'paterno': this.paterno,
                            'materno': this.materno,
                            'cuidador': this.cuidador,
                            'vinculo': this.vinculo,
                            'telefono_cuidador': this.telefono_cuidador,
                            'diagnostico_medico': this.diagnostico_medico,
                            'fecha_egreso': this.fecha_egreso,
                            'destino': this.destino,
                            'fecha_cesfam': this.fecha_cesfam,
                            'otros_diagnosticos':  this.otros_diagnosticos,
                            'cuidados_alta': this.cuidados_alta
                        }).then(function(response){
                            if(response.data.exito){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: response.data.exito,
                                });
                                me.obtenerEgresosRecienNacidoCaso(me.caso,me.buscar,1);
                                me.cerrarModal();
                            }
                            if(response.data.error){
                                swalError.fire({
                                    title: 'Error',
                                    text: response.data.error
                                });
                            }
                            if(response.data.errores){
                                let ul = '';
                                ul = "<ul style='text-align:left'>";
                                $.each( response.data.errores, function( key, value ) {
                                    ul +="<li style='list-style:none'>"+value+"</li>";
                                });
                                ul += "</ul>";
                                swalError.fire({
                                    title: 'Error',
                                    html:ul
                                });
                            }
                        }).catch(function (error){
                            console.log("error: ",error);
                        });
                    }
                },
                actualizar(){ 
                    $("#formEgresoRecienNacido").data('bootstrapValidator').validate();
                    if($("#formEgresoRecienNacido").data('bootstrapValidator').isValid()){
                        let me = this;
                        axios.put('../actualizarRecienNacido',{
                            'caso': this.caso,
                            'run': this.run,
                            'dv': this.dv,
                            'ficha': this.ficha,
                            'nombre': this.nombre,
                            'paterno': this.paterno,
                            'materno': this.materno,
                            'cuidador': this.cuidador,
                            'vinculo': this.vinculo,
                            'telefono_cuidador': this.telefono_cuidador,
                            'diagnostico_medico': this.diagnostico_medico,
                            'fecha_egreso': this.fecha_egreso,
                            'destino': this.destino,
                            'fecha_cesfam': this.fecha_cesfam,
                            'otros_diagnosticos':  this.otros_diagnosticos,
                            'cuidados_alta': this.cuidados_alta,
                            'id': this.id_egreso_rn
                        }).then(function (response) {
                            console.log(response);
                            if(response.data.exito){
                                swalExito.fire({
                                    title: 'Exito!',
                                    text: response.data.exito,
                                });
                                me.obtenerEgresosRecienNacidoCaso(me.caso,me.buscar,1);
                                me.cerrarModal();
                            }
                            if(response.data.error){
                                swalError.fire({
                                    title: 'Error',
                                    text:response.data.error
                                });
                            }
                            if(response.data.errores){
                                let ul = '';
                                ul = "<ul style='text-align:left'>";
                                $.each( response.data.errores, function( key, value ) {
                                    ul +="<li style='list-style:none'>"+value+"</li>";
                                });
                                ul += "</ul>";
                                swalError.fire({
                                    title: 'Error',
                                    html:ul
                                });
                            }
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                },
                eliminar(id){
                    let me = this;
                    axios.put('../eliminarRecienNacido',{
                            'id': id
                    }).then(function (response) {
                        if(response.data.exito){
                            swalExito.fire({
                                title: 'Exito!',
                                text: response.data.exito,
                            });
                            me.obtenerEgresosRecienNacidoCaso(me.caso,me.buscar,1);
                        }

                        if(response.data.error){
                            swalError.fire({
                                title: 'Error',
                                text:data.error
                            }).then(function(result) {
                                if (result.isDenied) {
                                }
                            });
                        }
                    }).catch(function (error) {
                        console.log(error);
                    });
                },
                bindValidation(){
                    $("#formEgresoRecienNacido").bootstrapValidator({
                        excluded: ':disabled',
                        fields: {
                            run: {
                                validators: {
                                    integer: {
                                        message: 'Debe ingresar solo números'
                                    }
                                }
                            },
                            dv: {
                                validators: {
                                    regexp: {
                                        regexp: /([0-9]|k)/i,
                                        message: 'Dígito verificador no valido'
                                    },
                                    callback: {
                                        callback: function(value, validator, $field){
                                            var field_rut = $("#run");
                                            var dv = $("#dv");
                                            if(field_rut.val() == '' && dv.val() == '') {
                                                return true;
                                            }
                                            if(field_rut.val() != '' && dv.val() == ''){
                                                return {valid: false, message: "Debe ingresar el dígito verificador"};
                                            }
                                            if(field_rut.val() == '' && dv.val() != ''){
                                                return {valid: false, message: "Debe ingresar el run"};
                                            }
                                            var rut = $.trim(field_rut.val());
                                            var esValido=esRutValido(field_rut.val(), dv.val());
                                            if(!esValido){
                                                return {valid: false, message: "Dígito verificador no coincide con el run"};
                                            }
                                            else{
                                                // getPacienteRut(rut);
                                            }
                                            return true;
                                        }
                                    }
                                }
                            },
                            nombre: {
                                validators: {
                                    notEmpty: {
                                        message: 'Debe ingresar el nombre del recien nacido'
                                    }
                                }
                            },
                            paterno: {
                                validators: {
                                    notEmpty: {
                                        message: 'Debe ingresar el apellido paterno del recien nacido'
                                    }
                                }
                            },
                            materno: {
                                validators: {
                                    notEmpty: {
                                        message: 'Debe ingresar el apellido materno del recien nacido'
                                    }
                                }
                            },
                            // fecha_egreso: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Debe ingresar una fecha de egreso'
                            //         },
                            //         callback: {
                            //             callback: function(value, validator, $field){
                            //                 console.log(value,validator,$field);
                            //                 return true;
                            //             }
                            //         }
                            //     }
                            // },
                            diagnostico_medico: {
                                validators: {
                                    notEmpty: {
                                        message: 'Debe ingresar el diagnóstico médico'
                                    }
                                }
                            },
                            destino: {
                                validators: {
                                    notEmpty: {
                                        message: 'Debe seleccionar una opción de destino'
                                    }
                                }
                            },
                            // fecha_cesfam: {
                            //     validators: {
                            //         notEmpty: {
                            //             message: 'Debe ingresar una fecha de control en cesfam'
                            //         }
                            //     }
                            // }
                        }
                    });
                },
                revalidarRun(){
                    $('#formEgresoRecienNacido').bootstrapValidator('revalidateField', 'dv');
                },
                resetValidator(){
                    $("#formEgresoRecienNacido").data('bootstrapValidator').destroy();
                    $("#formEgresoRecienNacido").data('bootstrapValidator', null);
                    this.bindValidation();
                    $("#formEgresoRecienNacido")[0].reset();
                }
            },
        }
</script>
<style>    
    .modal-content{
        width: 100% !important;
        position: absolute !important;
    }
    .mostrar{
        display: list-item !important;
        opacity: 1 !important;
        position: fixed !important;
        /* background-color: #3c29297a !important; */
    }
    /* .div-error{
        display: flex;
        justify-content: center;
    }
    .text-error{
        color: red !important;
        font-weight: bold;
    } */
    .table>thead:first-child>tr:first-child>th {
        color: cornsilk;
        vertical-align: middle;
        font-size: 12px !important;
        font-weight: normal !important;
    }

    .table>tbody>tr>td {
        color: rgb(88, 86, 86);
        vertical-align: middle;
        font-size: 12px !important;
        font-weight: bold !important;
    }

    .table>thead>tr {
        background: #399865;
    }
</style>

