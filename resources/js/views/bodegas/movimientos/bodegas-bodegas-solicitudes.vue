<template>
  <tr>
    <th>{{ movimiento.idsolicitud }}</th>
    <td>{{ movimiento.usuario }}</td>
    <td>{{ movimiento.mdm }}</td>
    <td>{{ movimiento.serie }}</td>
    <td :class="styleEstado">{{ movimiento.estado }}</td>
    <td>{{ movimiento.fecha }}</td>
    <td>
      {{ responsable }}
    </td>
    <td>{{ destino }}</td>
    <td>
      <div class="form-inline col-sm-1">
        <button @click="abrirVentana()" class="btn btn-info">
          <i class="fas fa-eye"></i>
        </button>
      </div>
      <div class="form-inline col-sm-1">
        <button
          v-if="esPedido"
          class="btn btn-info"
          @click="copiarA()"
          data-toggle="modal"
          data-target="#exampleModal"
        >
          <i class="fas fa-copy"></i>
        </button>
      </div>
    </td>
    <div
      class="modal fade"
      :id="`exampleModal${movimiento.idsolicitud}`"
      tabindex="-1"
      aria-labelledby="exampleModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog" style="max-width: 50%">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Copiar pedido a: salida</h5>
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
                  <label for="message-text" class="col-form-label"
                    >Bodega:</label
                  >
                  <multiselect
                    v-model="bodega"
                    :options="bodegas"
                    :searchable="true"
                    :close-on-select="true"
                    label="descripcion"
                    :show-labels="false"
                    placeholder=""
                  >
                  </multiselect>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              @click.prevent="confirmarSalida()"
              class="btn btn-primary"
            >
              Confirmar
            </button>
          </div>
        </div>
      </div>
    </div>
  </tr>
</template>

<script>
import Multiselect from "vue-multiselect";
import { errorsMixin } from "@/mixins/errors.js";
export default {
  props: ["movimiento", "bodegas"],
  mixins: [errorsMixin],
  components: {
    Multiselect,
  },
  data() {
    return {
      bodega: null,
    };
  },
  methods: {
    abrirVentana() {
      window.open(
        `${window.location.protocol}//${window.location.host}/bodegas/movimientos/movimientos/${this.movimiento.hash}/edit/`
      );
    },
    copiarA() {
      $(`#exampleModal${this.movimiento.idsolicitud}`).modal("show");
      $(`#exampleModal${this.movimiento.idsolicitud}`).modal("handleUpdate");
    },
    confirmarSalida() {
      if (this.bodega == null) {
        alert("Bodega es un campo requerido, por favor seleccione uno.");
        return;
      }
      if (!confirm("Confirme esta transaccion para copiar a salida.")) {
        this.ocultarModal();
        return;
      }

      const params = {
        opcion: "SALIDA",
        idcc: this.bodega.idcc,
        id_bodega: this.bodega.id_bodega,
        idsolicitud: this.movimiento.idsolicitud,
        idinventario: this.bodega.idinventario,
      };

      axios
        .get(`/bodegas/movimientos/bodegas/${JSON.stringify(params)}/detail`)
        .then((res) => {
          alert(res.data);
          this.ocultarModal();
        })
        .catch((e) => {
          this.handleError(e);
          this.ocultarModal();
        });
    },
    ocultarModal() {
      $(`#exampleModal${this.movimiento.idsolicitud}`).modal("hide");
      this.bodega = null;
    },
  },
  computed: {
    styleEstado() {
      switch (this.movimiento.estado) {
        case "ENTREGADO":
        case "FINALIZADO":
        case "ENVIADO":
          return "bg-primary text-white";
        case "ANULADO":
          return "bg-dark text-white";
        default:
          return "bg-success text-white";
      }
    },
    destino() {
      return this.movimiento.destino != null
        ? `${this.movimiento.destino.idcc} - ${this.movimiento.destino.descripcion}`
        : "N/A";
    },
    esPedido() {
      return this.movimiento.serie == "PEDIDO" &&
        this.movimiento.estado != "ANULADO" &&
        this.movimiento.estado != "SOLICITADO"
        ? true
        : false;
    },
    responsable() {
      return this.movimiento.responsable == null
        ? "NULL"
        : `${this.movimiento.responsable.cue} - ${this.movimiento.responsable.nombre_completo}`;
    },
  },
};
</script>