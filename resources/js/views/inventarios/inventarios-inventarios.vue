<template>
  <div class="card">
    <div class="card-header">
      <h4 class="card-title">Inventarios</h4>
    </div>

    <div class="card-body">
      <div class="row">
        <div class="col-md-6">
          <div class="form-group">
            <label class="typo__label">Bodega</label>
            <multiselect
              selectedLabel="Seleccionado"
              selectLabel="Presiona enter para seleccionar"
              deselect-label="No se puede quitar este valor"
              track-by="nombre"
              label="nombre"
              placeholder="Seleccione una bodega"
              v-model="bodega"
              :options="mis_bodegas"
              :searchable="true"
              :allow-empty="false"
              :disabled="saving"
              @select="obtenerInventario"
            >
            </multiselect>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-12">
          <table class="table table-hover">
            <thead>
              <tr>
                <th>Codigo barras</th>
                <th>Producto</th>
                <th>Fecha vencimiento</th>
                <th>Existencia</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="lote in lotes"
                :key="lote.fecha_vencimiento + '' + lote.id"
              >
                <th scope="row">{{ lote.producto.codigo_barra }}</th>
                <td>
                  {{ lote.producto.nombre }} {{ lote.producto.marca }}
                  {{ lote.producto.descripcion }}
                </td>
                <td>{{ lote.expiracion }}</td>
                <td>{{ lote.existencia }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Multiselect from "vue-multiselect";
export default {
  props: {
    mis_bodegas: {
      type: Array,
      required: true,
    },
  },
  components: {
    Multiselect,
  },
  data() {
    return {
      lotes: [],
      saving: false,
      bodega:null
    };
  },
  methods: {
    obtenerInventario(bodega) {
      this.saving = true;
      axios.get(`/inventarios/inventarios/${bodega.id}`).then((response) => {
        this.lotes = response.data.lotes;
        this.saving = false;
      });
    },
  },
};
</script>

<style>
</style>