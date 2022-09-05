<template>
	<tr :class="['text-center ', styleOC]" @click="seleccionar()">
		<th>{{ ordenCompra.orden_compra_sap }}</th>
		<td>{{ ordenCompra.idorden }}</td>
		<td>{{ ordenCompra.estado }}</td>
		<td>{{ ordenCompra.fecha_emision }}</td>
	</tr>
</template>

<script>
	export default {
		props: ["ordenCompra"],
		computed: {
			styleOC: function() {
				if (this.ordenCompra.orden_compra_sap == null) {
					return "bg-danger";
				}

				switch (this.ordenCompra.estado) {
					case "PENDIENTE":
						return "bg-success";
					case "RECIBIDA":
						return "bg-danger";
					case "GUARDADO":
						return "bg-warning";
				}
			},
		},
		methods: {
			seleccionar() {
				this.$emit("seleccion", this.ordenCompra.idorden);
			},
		},
	};
</script>

<style>
</style>
