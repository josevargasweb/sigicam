<template>
    <div class="row">
        <div class="col-md-12">
        <p>La fecha es: {{ fechaHoy }}</p>
        </div>
        <div class="col-md-12" v-if="camas.length == 0">
            <p>Faltan datos por mostrar</p>
        </div>
        <div class="col-md-12" v-else> 
            <table class="table" id="datatable">
                <thead>
                    <tr>
                        <!-- <th>Nombre</th> -->
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                        <th>Ocupadas</th>
                          <th>Ocupadas</th>
                        <th>Nombre Areas</th>
                        <th>Ocupadas</th>
                    </tr>
                </thead>
                <tbody>
                    <tr  v-for="cama in camas" :key="cama.id">
                        <!-- <td>{{cama.nombre}}</td> -->
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.ocupadas}}</td>
                        <td>{{cama.nombre_area}}</td>
                        <td>{{cama.ocupadas}}</td>
                    </tr>  
                </tbody>
            </table>
            <!-- <button></button> -->
        </div>
    </div>
</template>

<script>

    export default {
        props: ['fechaHoy'],
        data(){
            return {
                fecha_hoy:'',
                camas:[],
            }
        },
        mounted(){
            this.getCamas();
        },
        methods: {
            tabla(){
                this.$nextTick(function(){
                $('#datatable').DataTable({
                        "fixedColumns": true,
                        "bJQueryUI": true,
                        "scrollY": "48vh",
                        "scrollX": true,
                        "scrollCollapse": true,
                        "oLanguage": {
                            "sUrl": "../js/spanish.txt"
                        },
                    });
                });
            },
            getCamas(){
                axios.get("../resumenCamasIndex", {
                    params: {
                    ID: 1
                    }
                }).then(response =>{
                    this.camas = response.data.resumen;
                    this.tabla();
            });
            },
        },
    }
</script>
