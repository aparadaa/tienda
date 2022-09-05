<template>
	<tr :class="colorBodega">
		<td>{{detalle.generico.idgenerico}} - {{ detalle.generico.descripcion }}</td>
		<td>{{ detalle.cantidad }}</td>
		<td>{{ detalle.recibido }}</td>
		<td>{{ detalle.saldo }}</td>
		<td>{{ observacion }}</td>
		<td style="width: 200px">
			<multiselect v-model="detalle.uso" :allow-empty="false" :options="actividades" :close-on-select="true"
				:show-labels="false" label="descripcion" :disabled="esMaterial || detalle.saldo <= 0" placeholder="">
			</multiselect>
		</td>
		<td style="width: 200px">
			<multiselect v-model="detalle.centro_costo" :allow-empty="false" :options="centrosCosto"
				:close-on-select="true" :show-labels="false" label="descripcion"
				:disabled="esMaterial || detalle.saldo <= 0" placeholder=""></multiselect>
		</td>
		<td>
			<input type="number" v-model="detalle.recibir" :disabled="detalle.saldo <= 0 || recibeBodega"
				:max="detalle.saldo" min="0" style="width: 80px" />
		</td>
		<td>
			<input type="number" v-model="detalle.hectareas" style="width: 50px"
				:disabled="esMaterial || detalle.saldo <= 0" min="0" />
		</td>
		<td>
			<div class="form-check form-check-inline">
				<input class="form-check-input" v-model="detalle.check" :disabled="detalle.saldo <= 0 || recibeBodega"
					type="checkbox" />
			</div>
		</td>
	</tr>
</template>

<script>
	import Multiselect from "vue-multiselect";
	export default {
		props: ["detalle", "centrosCosto", "actividades", "bodega"],
		components: {
			Multiselect,
		},
		computed: {
			observacion: function() {
				return (this.detalle.bodega == null) ? " " : this.detalle.bodega.idcc + " - " + this.detalle.bodega
					.descripcion;
			},
			esMaterial: function() {
				return this.detalle.generico.idtipo_generico == 2 ? false : true;
			},
			colorBodega: function() {
				
				if(this.detalle.saldo <= 0){
					return "bg bg-secondary";
				}

				if (this.bodega == null) {
					return "bg bg-secondary";
				}

				if (this.bodega.id_bodega == this.detalle.bodega.id_bodega) {
					return "bg bg-success";
				} else if(this.bodega.es_central == 'S') {
					return "bg bg-success";
				}else{
					return "bg bg-secondary";
				}
			},
			recibeBodega: function() {
				if (this.bodega == null) {
					return true;
				}

				if(this.bodega.es_central == 'S'){
					return false;
				}

				if (this.bodega.id_bodega == this.detalle.bodega.id_bodega) {
					return false;
				}

				return true;
			}
		},
	};
</script>
