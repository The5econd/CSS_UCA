<template>
    <main class="main" style="background-color: white;">
            <!-- Breadcrumb -->
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Inicio</li>
                <li class="breadcrumb-item active">Proyectos Disponibles</li>
            </ol>
            
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div v-if="loadTable==true" class="card" style="border: none;">
                    <table-loader></table-loader>
                </div>
                <div v-else class="card" style="border: none;">
                    <div style="margin: 20px 0px 0px 20px;" >
                            <b style="color:red">
                                <i v-if="ya_aplico_hoy == 0">  </i>
                                <i v-else>  No puede aplicar a otro proyecto este día. Inténtelo mañana nuevamente.  </i>
                                
                            </b>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-hover table-sm" style="font-size: 1.25em;">
                            <thead>
                                <tr>
                                    <th style="text-align: center; width: 10%;">Nombre</th>
                                    <th style="text-align: center;" id="disappear">Descripción</th>
                                    <th style="width: 10%; text-align: center;">Cupos</th>
                                    <th style="width: 10%; text-align: center;">Opciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="proyecto in arrayProyectos" :key="proyecto.idProyecto">
                                    <td v-text="proyecto.nombre" id="name_p" data-toggle="modal" data-target="#modal-info" @click="abrirModal('info', proyecto)"></td>
                                    <td id="disappear" v-text="proyecto.descripcion" data-toggle="modal" data-target="#modal-info" @click="abrirModal('info', proyecto)"></td>
                                    <td v-text="`${proyecto.cupos_act}${'/'}${proyecto.cupos}`" data-toggle="modal" data-target="#modal-info" @click="abrirModal('info', proyecto)" style="text-align: center;"></td>
                                    <td>
                                        <div class="button-container" style="margin: 8px 0px 8px 4px;">
                                            <div v-if="ya_aplico_hoy == 0" style="display: flex; margin: 0px 10px;">
                                                <button type="button" data-toggle="modal" data-target="#modal-aplicar" @click="abrirModal('aplicar', proyecto)" class="btn btn-success btn-sm" style="width: 100%; border-radius: 5px;">
                                                    <i class="icon-check"></i>
                                                    <span class="btn-label">Aplicar</span>
                                                </button> &nbsp;
                                            </div>
                                            <div v-else style="display: flex; margin: 0px 10px;">
                                                <button type="button" class="btn btn-success btn-sm" disabled style="width: 100%; border-radius: 5px;">
                                                    <i class="icon-check"></i>
                                                    <span class="btn-label">Aplicar</span>
                                                </button> &nbsp;
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <nav>
                            <ul class="pagination" style="float: right;">
                                <li class="page-item" v-if="pagination.current_page > 1">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page - 1)" style="display: flex; justify-content: center; align-items: center; width: 32px; height: 35px;"><img :src="ruta + '/img/icons/chevron_left_black_24dp.svg'" alt="chevron-left"></a>
                                </li>
                                <li class="page-item" v-for="page in pagesNumber" :key="page" :class="[page == isActived ? 'active' : '']">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(page)" v-text="page"></a>
                                </li>
                                <li class="page-item" v-if="pagination.current_page < pagination.last_page">
                                    <a class="page-link" href="#" @click.prevent="cambiarPagina(pagination.current_page + 1)" style="display: flex; justify-content: center; align-items: center; width: 32px; height: 35px;"><img :src="ruta + '/img/icons/chevron_right_black_24dp.svg'" alt="chevron-left"></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>
            <!--Inicio del modal aplicar a proyecto-->
            <div class="modal fade" tabindex="-1" id="modal-aplicar" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                <div v-if="loading==1">
                    <spinner></spinner>
                </div>
                <div v-if="loading == 0" class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">Aplicar a proyecto</h4>
                            <button type="button" class="close" data-dismiss="modal" @click="cerrarModal()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <h2>¿Esta seguro que desea aplicar a este proyecto?</h2>
                            <p><b style="color:red">IMPORTANTE: </b>Este proceso se puede realizar una vez por día. Se le notificará al encargado sobre su aplicación y luego 
                            se le notificará a usted por correo si ha sido aceptado o no para pasar al siguiente proceso de aplicación.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" data-dismiss="modal" @click ="aplicarProyecto()">Confirmar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->

            <!--Inicio del modal informacion de proyecto-->
            <div class="modal fade" tabindex="-1" role="dialog" id="modal-info" aria-hidden="true">
                <div class="modal-dialog modal-primary modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" v-text="modal_nombre">Aplicar a proyecto</h4>
                            <button type="button" class="close" data-dismiss="modal" @click="cerrarModalDos()" aria-label="Close">
                              <span aria-hidden="true">×</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <table class="table table-bordered table-sm" style="font-size: 1.35em; margin-top: 10px">
                                <tbody>                                    
                                    <tr>
                                        <th style="background-color: #dedede; width: 15%">Descripción</th>
                                        <td v-text="modal_desc" style="padding-left: 16px;"></td>
                                    </tr>
                                    <tr>
                                        <th style="background-color: #dedede; width: 15%">Tipo</th>
                                        <td v-text="modal_tipo_horas" style="padding-left: 16px;"></td>
                                    </tr>
                                    <tr>
                                        <th style="background-color: #dedede; width: 15%">Cupos</th>
                                        <td v-text="`${modal_cupos_act}${'/'}${modal_cupos}`" style="padding-left: 16px;"></td>
                                    </tr>
                                    <tr>
                                        <th style="background-color: #dedede; width: 15%">Horario</th>
                                        <td v-text="modal_horario" style="padding-left: 16px;"></td>
                                    </tr>
                                    <tr>
                                        <th style="background-color: #dedede; width: 15%">Encargado</th>
                                        <td v-text="modal_encargado" style="padding-left: 16px;"></td>
                                    </tr>
                                    <tr>
                                        <th style="background-color: #dedede; width: 15%">Fecha inicial</th>
                                        <td v-text="modal_fecha_in" style="padding-left: 16px;"></td>
                                    </tr>
                                    <tr>
                                        <th style="background-color: #dedede; width: 15%">Fecha final</th>
                                        <td v-text="modal_fecha_fin" style="padding-left: 16px;"></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" @click="cerrarModalDos()">Cerrar</button>
                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                <!-- /.modal-dialog -->
            </div>
            <!--Fin del modal-->
            <footer class="app-footer" id="footer" style="display: flex; flex-direction: column; justify-content: center; font-size: 15px; padding: 10px 0px">
                <span><a target="_blank" href="http://www.uca.edu.sv/servicio-social/">Centro de Servicio Social | UCA</a> &copy; 2021</span>
                <span>Desarrollado por <a href="#"></a>Grupo de Horas Sociales</span>
            </footer>
        </main>
</template>

<script>
import {API_HOST} from '../constants/endpoint.js';
import {API_HOST_ASSETS} from '../constants/endpoint.js';
    export default {
        data(){
            return{
                ruta : API_HOST_ASSETS,
                loading : 0,
                loadTable : false,
                user_id : 0,
                user_email: '',
                ya_aplico_hoy : 0,
                descripcion : '',
                arrayProyectos : [''],
                modal : 0,
                modal2 : 0,
                id_proyecto : 0,                
                modal_encargado : '',
                modal_nombre : '',
                modal_desc : '',
                modal_tipo_horas : '',
                modal_cupos_act : 0,
                modal_cupos : 0,
                modal_horario : '',
                modal_fecha_in : '',
                modal_fecha_fin : '',
                modal_estado : '',
                arrayPXE : [''],
                pagination : {
                    'total' : 0,
                    'current_page' : 0,
                    'per_page' : 0,
                    'last_page' : 0,
                    'from' : 0,
                    'to' : 0
                },
                offset : 3
            }
        },
        computed:{
            isActived : function(){
                return this.pagination.current_page;
            },
            pagesNumber : function(){
                if(!this.pagination.to){
                    return [];
                }
                var from = this.pagination.current_page - this.offset;
                if (from < 1) {
                    from = 1;
                }
                var to = from + (this.offset * 2);
                if(to >= this.pagination.last_page){
                    to = this.pagination.last_page;
                }
                var pagesArray = []
                while(from <= to){
                    pagesArray.push(from);
                    from++;
                }
                return pagesArray;
            }
        },
        methods:{
            bindData(page){
                let me = this;
                me.loadTable = true;
                axios.get(`${API_HOST}/get_user`).then(function (response) {
                    me.user_id = response.data.idUser;
                    me.user_email = response.data.correo;
                })
                .catch(function (error) {
                    console.log(error);
                });

                axios.get(`${API_HOST}/ya_aplico`).then(function (response) {
                    me.ya_aplico_hoy = response.data;
                })
                .catch(function (error) {
                    console.log(error);
                });
                axios.get(`${API_HOST}/pxe_estudiante`).then(function (response) {
                    me.arrayPXE = response.data;
                }).catch(function (error) {
                    console.log(error);
                });

                var url = `${API_HOST}/proyectos_carrera?page=${page}`;
                axios.get(url).then(function (response) {
                    var respuesta = response.data;
                    var proyectos = respuesta.proyectos.data;
                    me.arrayProyectos = proyectos;
                    me.pagination = respuesta.pagination;
                    me.loadTable = false;
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cambiarPagina(page){
                let me = this;
                me.pagination.current_page = page;
                me.bindData(page);
            },
            aplicarProyecto(){
                let me = this
                me.loading = 1;
                axios.post(`${API_HOST}/proyecto/aplicar`, {
                    'idProyecto' : this.id_proyecto,
                    'idUser' : this.user_id,
                    'estado' : 0,
                })
                .then(function (response) {
                    $('#modal-aplicar').modal('hide')
                    me.loading = 2;
                    me.cerrarModal();
                })
                .catch(function (error) {
                    console.log(error);
                });
            },
            cerrarModal(){
                this.modal = 0;
                this.id_proyecto = 0
                this.bindData();
            },
            cerrarModalDos(){
                this.modal2 = 0;
            },
            abrirModal(modelo, data = []){
                
                switch (modelo) {
                    case "aplicar":
                        {
                            this.modal = 1;
                            this.loading = 0;
                            this.id_proyecto = data.idProyecto
                            break;
                        }
                    case "info":
                    {
                        this.modal2 = 1;
                        this.id_proyecto = data.idProyecto;
                        this.modal_encargado = data.encargado;
                        this.modal_nombre = data.nombre;
                        this.modal_desc = data.descripcion;
                        this.modal_tipo_horas = data.tipo_horas;
                        this.modal_cupos_act = data.cupos_act;
                        this.modal_cupos = data.cupos;
                        this.modal_horario = data.horario;
                        this.modal_fecha_in = data.fecha_inicio;
                        this.modal_fecha_fin = data.fecha_fin;
                        this.modal_estado = data.estado;
                        break;
                    }
                    default:
                        break;
                }
            },
            logout(){
                var url = `${API_HOST}/logout`;
                axios.post(url).then(() => location.href = `${API_HOST}/`)
            }
        },
        mounted() {
            this.bindData();
        }
    }
</script>
<style>

@font-face {
    font-family: 'Abel';
    src: url(/css-proyecto/public/fonts/Abel-Regular.ttf);
}


.main{
    font-family: 'Abel';
}

.sidebar {
    background-color: #003C71;

}

@media screen and (min-width: 991px) {
    #logout {
        margin-right: 30px;
    }
}
@media screen and (max-width: 991px) {
    #logout {
        margin-right: 30px;
    }
}
    #footer{
    margin-left: 0px;
}

    .modal-primary .modal-header{
        background-color: #003C71;
    }
    .modal-content{
        width : 100% !important;
        position : absolute !important;
    }
    .mostrar{
        display : list-item !important;
        opacity : 1 !important;
        background-color: #3c29297a !important;
    }
    .sidebar-fixed .sidebar {
    height: 100%;
    }

    @media screen and (max-width: 991px) {
        .breadcrumb {
            margin-top: 55px;
        }

        #sidebarMenu {
            margin-top: 55px;
        }
        
        #logout {
            margin-right: 30px;
        }

        .main{
            overflow: scroll;
        }
        
    }

    @media screen and (max-width: 500px) {
        #disappear{
            display: none;
        }
        .btn-label {
            display: none;
        }
        
    }

</style>