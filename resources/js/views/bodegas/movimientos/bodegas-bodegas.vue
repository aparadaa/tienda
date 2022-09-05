<template>
  <div class="card col-sm-12">
    <div class="card-header">
      <div class="form-row col-md-12">
        <div class="form-group col-sm-10">
          <label>Bodega</label>
          <multiselect
            @input="cambioBodega"
            v-model="busqueda.region"
            :options="bodegas"
            :close-on-select="true"
            :show-labels="false"
            label="descripcion"
            :allow-empty="false"
            placeholder=""
          >
          </multiselect>
        </div>
        <div class="form-group col-sm-2">
          <label>Cantidad</label>
          <input
            v-model="busqueda.cantidad"
            type="text"
            class="form-control form-control"
          />
        </div>
      </div>
      <div v-if="carga == 'LISTO'">
        <div class="form-row col-md-12">
          <div class="form-group col-sm-3">
            <label>Solicitud</label>
            <input
              v-model="busqueda.solicitud"
              type="text"
              class="form-control form-control"
            />
          </div>
          <div class="form-group col-sm-3">
            <label>MDM</label>
            <input
              v-model="busqueda.mdm"
              type="text"
              class="form-control form-control"
            />
          </div>
          <div class="form-group col-sm-3">
            <label>Tipo</label>
            <multiselect
              v-model="busqueda.tipo"
              :options="series"
              :searchable="true"
              :close-on-select="true"
              :show-labels="false"
              placeholder=""
            >
            </multiselect>
          </div>
          <div class="form-group col-sm-3">
            <label>Producto</label>
            <input
              v-model="busqueda.producto"
              type="text"
              class="form-control form-control"
            />
          </div>
        </div>
        <div class="form-row col-md-12">
          <div class="form-group col-sm-3">
            <label>Fecha desde</label>
            <input
              v-model="busqueda.fechad"
              type="date"
              class="form-control form-control"
            />
          </div>
          <div class="form-group col-sm-3">
            <label>Fecha hasta</label>
            <input
              v-model="busqueda.fechaa"
              type="date"
              class="form-control form-control"
            />
          </div>
          <div class="form-group col-sm-3">
            <label>Destino</label>
            <multiselect
              v-model="busqueda.destino"
              :options="destinos"
              :searchable="true"
              :close-on-select="true"
              label="descripcion"
              :show-labels="false"
              placeholder=""
            >
            </multiselect>
          </div>
          <div class="form-group col-sm-3">
            <label>Estado</label>
            <multiselect
              v-model="busqueda.estado"
              :options="estados"
              :searchable="true"
              :close-on-select="true"
              :show-labels="false"
              placeholder=""
            >
            </multiselect>
          </div>
        </div>
        <div class="form-row col-md-12">
          <div class="form-group col-sm-3">
            <label>Solicitante</label>
            <input
              v-model="busqueda.solicitante"
              type="text"
              class="form-control form-control"
            />
          </div>
          <div class="form-group col-sm-3">
            <label>Obsevacion</label>
            <input
              v-model="busqueda.observacion"
              type="textarea"
              class="form-control form-control"
            />
          </div>

          <div class="form-inline col-sm-1">
            <button
              @click="filtrar(false)"
              type="button"
              class="btn btn-primary"
            >
              <i class="fas fa-search"></i>
            </button>
          </div>
          <div class="form-inline col-sm-2">
            <button
              @click="filtrar(true)"
              v-if="busqueda.tipo == 'PEDIDO'"
              type="button"
              class="btn btn-primary"
            >
              Busqueda especial
            </button>
          </div>
        </div>
      </div>
      <div v-else-if="carga">
        <label>Obteniendo datos...</label>
      </div>
    </div>
    <div v-if="carga == 'LISTO'" class="card-body">
      <label v-if="movimientos.length == 0">No se encontraron datos.</label>
      <div v-else class="overflow-auto col-md-12" style="max-height: 450px">
        <table class="table table-sm table-hover">
          <thead>
            <tr>
              <th scope="col">idSolicitud</th>
              <th scope="col">Usuario</th>
              <th scope="col"># Mdm</th>
              <th scope="col">Tipo</th>
              <th scope="col">Estado</th>
              <th scope="col">Fecha</th>
              <th scope="col">Solicitante</th>
              <th scope="col">Destino</th>
              <th scope="col">-</th>
            </tr>
          </thead>
          <tbody>
            <BodegasBodegasSolicitudes
              v-for="movimiento in movimientos"
              :key="movimiento.idsolicitud"
              :movimiento="movimiento"
              :bodegas="destinos"
            />
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import BodegasBodegasSolicitudes from "./bodegas-bodegas-solicitudes";
import Multiselect from "vue-multiselect";

export default {
  props: ["bodegas", "usuario", "destinos", "estados"],
  components: {
    BodegasBodegasSolicitudes,
    Multiselect,
  },
  data() {
    return {
      busqueda: {
        cantidad: 30,
        solicitud: null,
        mdm: null,
        tipo: null,
        region: null,
        fechad: null,
        fechaa: null,
        destino: null,
        estado: null,
        solicitante: null,
        observacion: null,
        producto: null,
        especial: false,
        opcion: "BUSQUEDA",
      },
      movimientos: null,
      carga: null,
      series: null,
    };
  },
  methods: {
    cambioBodega(bodega) {
      this.movimientos = [];
      this.series = [];
      this.carga = true;
      axios
        .post("/bodegas/movimientos/bodegas/data", {
          bodega,
          cantidad: this.busqueda.cantidad,
        })
        .then((res) => {
          this.carga = false;
          this.carga = "LISTO";
          this.movimientos = res.data.solicitudes;
          this.series = res.data.series;
        })
        .catch((error) => {
          this.carga = false;
          this.carga = "LISTO";
          console.log(error.message.data.message);
        });
    },
    filtrar(especial) {
      this.movimientos = [];
      this.busqueda.especial = especial;
      this.carga = true;
      axios
        .get(
          `/bodegas/movimientos/bodegas/${JSON.stringify(this.busqueda)}/detail`
        )
        .then((res) => {
          this.carga = "LISTO";
          this.movimientos = res.data;
        })
        .catch((e) => {
          this.carga = "LISTO";
          console.log(e.message.data.message);
        });
    },
  },
};
</script>
