<template>
	<div class="card text-white bg-white">
		<div class="card-header">
			<div class="btn-group" role="group">
				<button type="button" @click="mostrar = !mostrar" onclick="this.blur();" :title="ayudaMostrar"
					class="btn btn-secondary text-uppercase">
					<i :class="iconoMostrar"></i>
				</button>
				<button type="button" onclick="this.blur();" title="Buscar" @click="buscar"
					class="btn btn-secondary text-uppercase">
					<i class="fas fa-search"></i>
				</button>
				<button type="button" onclick="this.blur();" title="Limpiar" @click="limpiar"
					class="btn btn-secondary text-uppercase">
					<i class="fas fa-broom"></i>
				</button>
				<button type="button" onclick="this.blur();" title="Sincronizar" @click="sincronizar"
					class="btn btn-secondary text-uppercase">
					<i class="fas fa-sync"></i>
				</button>
			</div>
		</div>
		<div class="card-body row">
			<div class="card-body col-sm-3 rounded mt-0 overflow-auto" :style="tamanioPantalla(300)">
				<div v-if="!mostrar">
					<div class="form-group mt-0">
						<label>Bodega</label>
						<multiselect v-model="form.bodega" :allow-empty="false" :options="bodegas"
							:close-on-select="true" :show-labels="false" label="descripcion" placeholder="">
						</multiselect>
					</div>
					<div class="form-group mt-0">
						<label>Empresa</label>
						<multiselect v-model="form.empresa" :allow-empty="false" :options="empresas"
							:close-on-select="true" :show-labels="false" label="nombre" placeholder=""></multiselect>
					</div>
					<div class="form-group mt-0">
						<label>OC No.</label>
						<input type="number" min="0" v-model="form.numOC" class="form-control" />
					</div>
				</div>
				<table class="table table-sm table-hover">
					<thead>
						<tr>
							<th>Ref. SAP</th>
							<th>No. OC</th>
							<th>Estado</th>
							<th>Fecha</th>
						</tr>
					</thead>
					<tbody>
						<BodegasRecepcionOrdenCompra v-for="oc in ordenesCompra" :ordenCompra="oc" :key="oc.idorden"
							@seleccion="seleccionOC" />
					</tbody>
				</table>
			</div>
			<div class="card-body col-sm-9 rounded" v-if="oc != null">
				<div class="overflow-auto" :style="tamanioPantalla(300)">
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Orden de Compra</span>
						</div>
						<input type="text" class="form-control font-weight-bold" v-model="oc.idorden" aria-label="Small"
							aria-describedby="inputGroup-sizing-sm" readonly />
					</div>
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Referencia</span>
						</div>
						<input type="text" class="form-control font-weight-bold" v-model="ref" aria-label="Small"
							aria-describedby="inputGroup-sizing-sm" readonly />
					</div>
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Fecha</span>
						</div>
						<input type="text" class="form-control font-weight-bold" v-model="oc.fecha_enviada"
							aria-label="Small" aria-describedby="inputGroup-sizing-sm" readonly />
					</div>
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Estado</span>
						</div>
						<input type="text" class="form-control font-weight-bold" v-model="oc.estado" aria-label="Small"
							aria-describedby="inputGroup-sizing-sm" readonly />
					</div>
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Proveedor</span>
						</div>
						<input type="text" class="form-control font-weight-bold" v-model="proveedor" aria-label="Small"
							aria-describedby="inputGroup-sizing-sm" readonly />
					</div>
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Fecha recepcion</span>
						</div>
						<input type="date" class="form-control font-weight-bold" v-model="fecha_recepcion"
							:min="fecha_minima" :max="fecha_maxima" aria-label="Small"
							aria-describedby="inputGroup-sizing-sm" />
					</div>
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Mdm</span>
						</div>
						<input type="text" class="form-control font-weight-bold" v-model="oc.mdm" aria-label="Small"
							aria-describedby="inputGroup-sizing-sm" readonly />
					</div>
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<span class="input-group-text">Observaciones</span>
						</div>
						<input type="text" class="form-control" v-model="oc.observaciones" aria-label="Small"
							aria-describedby="inputGroup-sizing-sm" />
						<div class="input-group-append">
							<button class="btn btn-success" type="button" @click="guardar" :disabled="transaccion"
								v-if="validarEstado">
								<i class="fas fa-save"></i>
							</button>
						</div>
					</div>
					<div class="form-group-sm">
						<label>Centro operativo</label>
						<multiselect v-model="centro_operativo" :allow-empty="false" :options="centros_operativos"
							:close-on-select="true" :show-labels="false" label="nombre" placeholder=""></multiselect>
					</div>
					<table class="table table-sm table-hover font-weight-light text-center">
						<thead>
							<tr>
								<th>Descripcion</th>
								<th>Comprado</th>
								<th>Recibido</th>
								<th>Pendiente</th>
								<th>Obser.</th>
								<th>Actividad</th>
								<th>Destino</th>
								<th>A recibir</th>
								<th>Has</th>
								<th>Recibir</th>
							</tr>
						</thead>
						<tbody>
							<BodegasRecepcionOrdenCompraDetalles v-for="detalle in oc.detalles" :detalle="detalle"
								:key="detalle.id" :centrosCosto="centros_costo" :actividades="actividades"
								:bodega="form.bodega" />
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	import {
		errorsMixin
	} from "@/mixins/errors.js";
	import Multiselect from "vue-multiselect";
	import BodegasRecepcionOrdenCompra from "./bodegas-recepcion-orden-compra.vue";
	import BodegasRecepcionOrdenCompraDetalles from "./bodegas-recepcion-orden-compra-detalles.vue";
	export default {
		props: [
			"ocs",
			"empresas",
			"usuario",
			"bodegas",
			"centros_costo",
			"actividades",
			"centros_operativos",
		],
		mixins: [errorsMixin],
		components: {
			Multiselect,
			BodegasRecepcionOrdenCompra,
			BodegasRecepcionOrdenCompraDetalles,
		},
		data() {
			return {
				mostrar: false,
				ordenesCompra: this.ocs,
				form: {
					bodega: null,
					empresa: null,
					numOC: null,
				},
				oc: null,
				transaccion: false,
				fecha_recepcion: moment().format("YYYY-MM-DD"),
				fecha_maxima: moment().format("YYYY-MM-DD"),
				centro_operativo: null,
			};
		},
		methods: {
			seleccionOC: function(idorden) {
				if (this.transaccion) {
					alert("Por favor espere a que termine la transaccion en curso");
					return;
				}

				this.oc = null;
				this.transaccion = true;

				const form = {
					opcion: "orden",
					idorden,
				};
				axios
					.get(`/bodegas/recepcion/${JSON.stringify(form)}/detail`)
					.then((res) => {
						this.fecha_recepcion = moment().format("YYYY-MM-DD");
						this.oc = res.data;
						this.transaccion = false;
					})
					.catch((err) => {
						this.handleError(err);
						this.transaccion = false;
					});
			},
			cambiarEstadoOC: function(idorden, estado) {
				const indice = this.ordenesCompra.findIndex((elemento) => {
					if (elemento.idorden === idorden) {
						return true;
					}
				});

				this.ordenesCompra[indice].estado = estado;
			},
			buscar() {
				this.transaccion = true;
				this.oc = null;
				const form = {
					opcion: "buscar",
					numOC: this.form.numOC,
					empresa: this.form.empresa,
				};
				axios
					.get(`/bodegas/recepcion/${JSON.stringify(form)}/detail`)
					.then((res) => {
						this.transaccion = false;
						this.ordenesCompra = res.data;
					})
					.catch((err) => {
						this.transaccion = false;
						this.handleError(err);
					});
			},
			limpiar() {
				this.form.bodega = null;
				this.form.empresa = null;
				this.form.numOC = null;
				this.form.estado = "Pendientes";
			},
			sincronizar() {
				this.transaccion = true;
				const form = {
					opcion: "sincronizar",
				};
				axios
					.get(`/bodegas/recepcion/${JSON.stringify(form)}/detail`)
					.then((res) => {
						this.transaccion = false;
						alert("Sincronizando!");
					})
					.catch((err) => {
						this.transaccion = false;
						this.handleError(err);
					});
			},
			guardar: function() {
				this.transaccion = true;
				axios
					.post("/bodegas/recepcion", {
						oc: this.oc,
						bodega: this.form.bodega,
						empresa: this.form.empresa,
						fecha_recepcion: this.fecha_recepcion,
						centro_operativo: this.centro_operativo,
					})
					.then((res) => {
						this.oc.detalles = [];
						this.oc.estado = res.data.estado;
						this.oc.detalles = res.data.detalles;
						this.fecha_recepcion = moment().format("YYYY-MM-DD");
						this.cambiarEstadoOC(this.oc.idorden, this.oc.estado);
						alert("trasaccion guardada con exito.");
						this.transaccion = false;
					})
					.catch((err) => {
						this.transaccion = false;
						this.handleError(err);
					});
			},
			tamanioPantalla: function(tam) {
				return `max-height: ${screen.availHeight - tam}px`;
			}
		},
		computed: {
			iconoMostrar: function() {
				return this.mostrar ? "fas fa-eye" : "fas fa-eye-slash";
			},
			fecha_minima: function() {
				return (this.form.bodega == null) ? moment().subtract(3, "days").format("YYYY-MM-DD") : moment().subtract(this.form.bodega.dias_pasado, "days").format("YYYY-MM-DD");
			},
			ayudaMostrar: function() {
				return this.mostrar ? "Mostrar" : "Ocultar";
			},
			ref: function() {
				const text = "Observaciones SAP: ";
				let observacion = "Sin observaciones";
				if (this.oc.solicitud) {
					observacion = this.oc.solicitud.observaciones_sap;
				}
				return text + observacion;
			},
			proveedor: function() {
				return this.oc.card_name_proveedor != null ?
					this.oc.card_name_proveedor :
					this.oc.proveedor.nombre;
			},
			validarEstado: function() {
				return this.oc.estado == "RECIBIDA" ? false : true;
			},
		},
	};
</script>
