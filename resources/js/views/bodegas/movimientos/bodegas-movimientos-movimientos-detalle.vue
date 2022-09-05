<template>
    <tr :class="styleMigrado">
        <td>{{ detalle.hectareas }}</td>
        <td>{{ detalle.dosis }}</td>
        <td>
            {{ detalle.producto.idespecifico }} -
            {{ detalle.producto.descripcion }}
            {{ matsap }}
        </td>
        <td>{{ detalle.medida.descripcion }}</td>
        <td>
            <input
                type="number"
                :disabled="validarCambioCantidad"
                v-model="detalle.cantidad"
                style="width:100px"
            />
        </td>
        <td>{{ uso }}</td>
        <td>{{ destino }}</td>
        <td>{{ centrocostosap }}</td>
        <td>
            <span
                class="d-inline-block"
                tabindex="0"
                data-toggle="tooltip"
                :title="observaciones"
            >
                <button type="button" disabled class="btn btn-info">
                    <i class="far fa-comment-dots"></i>
                </button>
            </span>
            <button
                type="button"
                :disabled="validarEstado || detalle.aprobacion_usuario != null"
                class="btn btn-success"
                v-if="serie == 'PEDIDO'"
                @click.prevent="agregarFechas()"
            >
                <i class="far fa-calendar-plus"></i>
            </button>
            <button
                type="button"
                :disabled="validarEstado"
                class="btn btn-danger"
                @click.prevent="borrarDetalle()"
            >
                <i class="fas fa-trash-alt"></i>
            </button>
        </td>
        <div
            class="modal fade"
            :id="'exampleModal' + i"
            tabindex="-1"
            aria-labelledby="exampleModalLabel"
            aria-hidden="true"
        >
            <div class="modal-dialog" style="max-width: 50%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button
                            type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close"
                        >
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div>
                            <div class="form-row">
                                <div class="form-group col-sm-5">
                                    <label
                                        for="message-text"
                                        class="col-form-label"
                                        >Fecha:</label
                                    >
                                    <input
                                        type="date"
                                        v-model="fecha"
                                        :min="fechaMinima"
                                        class="form-control"
                                        @keydown.enter.prevent="agregarFecha"
                                    />
                                </div>
                                <div class="form-group col-sm-5">
                                    <label
                                        for="recipient-name"
                                        class="col-form-label"
                                        >Cantidad:</label
                                    >
                                    <input
                                        type="number"
                                        v-model="cantidad"
                                        :min="0"
                                        class="form-control"
                                        @keydown.enter.prevent="agregarFecha"
                                    />
                                </div>
                                <div class="form-group col-sm-1">
                                    <label
                                        for="message-text"
                                        class="col-form-label"
                                        >Agregar</label
                                    >
                                    <button
                                        type="button"
                                        @click.prevent="agregarFecha"
                                        :disabled="sumarCantidadesFecha"
                                        class="btn btn-primary"
                                    >
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <table
                            class="table table-sm"
                            v-if="detalle.fechasentregas.length > 0"
                        >
                            <thead>
                                <tr>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">-</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="(fechaentrega,
                                    i) in detalle.fechasentregas
                                        .slice()
                                        .reverse()"
                                    :key="i"
                                >
                                    <td>{{ fechaentrega.cantidad }}</td>
                                    <td>{{ fechaentrega.fecha }}</td>
                                    <td>
                                        <button
                                            type="button"
                                            @click.prevent="eliminarFecha(i)"
                                            class="btn btn-secondary"
                                        >
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </tr>
</template>
<script>
import { errorsMixin } from "@/mixins/errors.js";
import Multiselect from "vue-multiselect";
export default {
    props: ["detalle", "validationErrors", "i", "serie", "fechaMinima"],
    mixins: [errorsMixin],
    components: {
        Multiselect
    },
    data() {
        return {
            isLoadingResponsable: false,
            fecha: null,
            cantidad: null
        };
    },
    methods: {
        claseProducto() {
            if (this.serie == "PEDIDO") {
                return "bg-success";
            } else {
                if (
                    parseFloat(this.detalle.existencia) <= 0 ||
                    this.detalle.existencia == undefined
                ) {
                    return "bg-danger";
                } else {
                    return "bg-success";
                }
            }
        },
        agregarFecha() {
            const fecha = this.fecha;
            const cantidad = this.cantidad;

            if (
                fecha == null ||
                cantidad == null ||
                cantidad == "" ||
                cantidad == 0
            ) {
                alert("Error, no se puede agregar un item vacio.");
                return;
            }

            let total = this.detalle.fechasentregas.reduce(
                (sum, value) => sum + parseInt(value.cantidad),
                0
            );
            let resta = this.detalle.cantidad - total;
            if (resta - cantidad < 0) {
                alert(
                    `Se excede por la cantidad de ${cantidad -
                        resta}, por favor revisar nuevamente`
                );
                return;
            }
            //validar que no exista la fecha que esta ingresando en el array
            var existeFecha = this.detalle.fechasentregas.filter(
                item => item.fecha == fecha
            );
            if (existeFecha.length == 1) {
                alert(
                    `Error, no se puede agregar este item, ya existe una dato con la fecha ingresada.`
                );
                return;
            }

            this.detalle.fechasentregas.push({
                fecha,
                cantidad
            });

            this.fecha = null;
            this.cantidad = null;
        },
        eliminarFecha(i) {
            const tam = this.detalle.fechasentregas.length - 1;
            if (i == 0) {
                this.detalle.fechasentregas.splice(tam, 1);
            } else {
                this.detalle.fechasentregas.splice(tam - i, 1);
            }
        },
        agregarFechas() {
            $(`#exampleModal${this.i}`).modal("show");
            $(`#exampleModal${this.i}`).modal("handleUpdate");
        },
        borrarDetalle() {
            this.$emit("borrar", this.i);
        }
    },
    computed: {
        sumarCantidadesFecha() {
            let total = this.detalle.fechasentregas.reduce(
                (sum, value) => sum + parseInt(value.cantidad),
                0
            );
            let cantidad = this.detalle.cantidad;

            if (cantidad - total > 0) {
                return false;
            } else {
                return true;
            }
        },
        validarEstado() {
            switch (this.serie) {
                case "PEDIDO":
                    return this.detalle.estado == "CONSOLIDADO" ||
                        this.detalle.estado == "COMPRAR" ||
                        this.detalle.estado == "ANULADO" ||
                        this.detalle.estado == "RECHAZADO"
                        ? true
                        : false;
                default:
                    return true;
            }
        },
        validarCambioCantidad() {
            if (this.detalle.estado != "SOLICITADO") {
                return true;
            } else {
                return false;
            }
        },
        centrocostosap() {
            return this.detalle.centrocosto.dimension3 == null
                ? " - "
                : this.detalle.centrocosto.dimension3;
        },
        destino() {
            if (
                (this.detalle.aprobacion_usuario == null ||
                    this.detalle.aprobacion_usuario == "") &&
                this.detalle.centrocosto.idcc != null
            ) {
                return `${this.detalle.centrocosto.idcc} - ${this.detalle.centrocosto.descripcion}`;
            } else if (
                (this.detalle.aprobacion_usuario == null ||
                    this.detalle.aprobacion_usuario == "") &&
                this.detalle.centrocosto.idcc == null
            ) {
                return "-";
            } else {
                return "Varias Regiones";
            }
        },
        uso() {
            return this.detalle.uso != null
                ? `${this.detalle.uso.id_actividad} - ${this.detalle.uso.descripcion}`
                : " - ";
        },
        matsap() {
            return this.detalle.genericoempresa == null
                ? ""
                : " - " + this.detalle.genericoempresa.codigo_sap;
        },
        styleMigrado() {
            return this.detalle.fecha_sync_sap == null
                ? ""
                : "bg-gradient-warning";
        },
        observaciones() {
            let migrado = " - MIGRADO";

            if (this.detalle.fecha_sync_sap == null) {
                migrado = " - NO MIGRADO";
            }

            return this.detalle.observaciones + migrado;
        }
    }
};
</script>
