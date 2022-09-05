<template>
  <div>
    <div v-if="solicitud">
      <div class="form-row bg-white rounded" v-if="solicitud">
        <div class="col-sm-12">
          <button
            :class="buttonClass('btn-primary')"
            @click="nuevaSolicitud"
            :disabled="saving"
          >
            Nuevo
          </button>
          <button
            :class="buttonClass('btn-primary')"
            v-if="mostrarBotonGuardar"
            :disabled="saving"
            @click="guardar"
          >
            Guardar
          </button>
          <button
            class="btn btn-primary"
            v-if="validarFinalizar"
            :disabled="saving"
            @click="finalizar"
          >
            Finalizar
          </button>
          <button
            class="btn btn-primary"
            v-if="validarAnular"
            :disabled="saving"
            @click="anular"
          >
            Anular
          </button>
          <button class="btn btn-primary" @click="ocultarEncabezado">
            {{ oculto }}
          </button>
          <button
            class="btn btn-primary"
            v-if="validarReporte"
            @click="reporte('resumido')"
          >
            Imprimir resumido
          </button>
          <button
            class="btn btn-primary"
            v-if="validarReporte"
            @click="reporte('detallado')"
          >
            Imprimir detallado
          </button>
        </div>
        <div class="col-sm-12" id="encabezado">
          <div class="form-group mt-2">
            <label>Bodega</label>
            <multiselect
              @input="cambioBodega"
              v-model="solicitud.bodega"
              :options="bodegas"
              :close-on-select="true"
              :show-labels="false"
              label="descripcion"
              :allow-empty="false"
              :disabled="this.solicitud.estado != 'SOLICITADO'"
              placeholder=""
            >
            </multiselect>
          </div>
          <form>
            <div class="form-row">
              <div class="form-group col-sm-3">
                <label>Solicitud</label>
                <input
                  v-model="solicitud.idsolicitud"
                  type="text"
                  readonly
                  class="form-control"
                />
              </div>
              <div class="form-group col-sm-3">
                <label>MDM</label>
                <input
                  v-model="solicitud.mdm"
                  type="text"
                  readonly
                  class="form-control"
                />
              </div>
              <div class="form-group col-sm-3">
                <label>Departamento</label>
                <multiselect
                  v-model="solicitud.departamento"
                  label="descripcion"
                  track-by="idcc"
                  placeholder=""
                  open-direction="bottom"
                  :options="departamentos"
                  :searchable="true"
                  :disabled="departamentos.length <= 0"
                  :internal-search="true"
                  :clear-on-select="false"
                  :close-on-select="true"
                  :show-no-results="true"
                  :hide-selected="true"
                >
                </multiselect>
                <!-- <input type="text" class="form-control" v-model="solicitud.bodega.idcc" readonly> -->
              </div>
              <div class="form-group col-sm-3">
                <label>Estado</label>
                <input
                  type="text"
                  v-model="solicitud.estado"
                  class="form-control"
                  readonly
                />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-sm-3">
                <label>Fecha</label>
                <input
                  type="date"
                  v-model="solicitud.fecha"
                  class="form-control"
                />
              </div>
              <div class="form-group col-sm-3">
                <label>Tipo</label>
                <multiselect
                  v-model="solicitud.serie"
                  :allow-empty="false"
                  @input="cambioTipo"
                  :options="series"
                  :disabled="
                    this.solicitud.bodega == null || series.length <= 0
                  "
                  :close-on-select="true"
                  :show-labels="false"
                  placeholder=""
                ></multiselect>
              </div>
              <div class="form-group col-sm-3">
                <label>Destino</label>
                <multiselect
                  v-model="solicitud.destino"
                  :options="bodegasDestino"
                  :close-on-select="true"
                  :show-labels="false"
                  label="descripcion"
                  :allow-empty="false"
                  placeholder=""
                  :disabled="validarTipo"
                >
                </multiselect>
              </div>
              <div class="form-group col-sm-3">
                <label>MDM (idsolicitud)</label>
                <input
                  v-model="solicitud.idsolicitud_ref"
                  type="number"
                  :readonly="mdmsolicitud"
                  class="form-control"
                />
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-sm-3">
                <label>Motivo</label>
                <multiselect
                  v-model="solicitud.tipo_operacion"
                  :options="motivos"
                  :close-on-select="true"
                  :show-labels="false"
                  :allow-empty="false"
                  placeholder=""
                >
                </multiselect>
              </div>
              <div class="form-group col-sm-3">
                <label>Responsable</label>
                <multiselect
                  v-model="solicitud.responsable"
                  label="cue_nombre_completo"
                  track-by="cue"
                  placeholder=""
                  open-direction="bottom"
                  :options="empleados"
                  :searchable="true"
                  :internal-search="true"
                  :clear-on-select="false"
                  :close-on-select="true"
                  :show-no-results="true"
                  :hide-selected="true"
                >
                </multiselect>
              </div>
              <div class="form-group col-sm-3">
                <label>Autoriza</label>
                <multiselect
                  v-model="solicitud.autoriza"
                  label="cue_nombre_completo"
                  track-by="cue"
                  placeholder=""
                  open-direction="bottom"
                  :options="empleados"
                  :searchable="true"
                  :internal-search="true"
                  :clear-on-select="false"
                  :close-on-select="true"
                  :show-no-results="true"
                  :hide-selected="true"
                >
                </multiselect>
              </div>
              <div class="form-group col-sm-3">
                <label>Centro Operativo</label>
                <multiselect
                  v-model="solicitud.centrooperativo"
                  :options="centrosoperativos"
                  :close-on-select="true"
                  :show-labels="false"
                  label="nombre"
                  :allow-empty="false"
                  placeholder=""
                >
                </multiselect>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-sm-6">
                <label>Observacion</label>
                <textarea
                  v-model="solicitud.observaciones"
                  row="3"
                  class="form-control"
                  aria-label="With textarea"
                ></textarea>
              </div>
              <div class="form-group col-sm-6">
                <label>Produccion</label>
                <multiselect
                  v-model="solicitud.produccion"
                  :options-limit="500"
                  :options="produccion"
                  :close-on-select="true"
                  :show-labels="false"
                  label="descripciones"
                  :allow-empty="false"
                  placeholder=""
                >
                </multiselect>
              </div>
            </div>
          </form>
        </div>
      </div>
      <div class="form-row bg-white rounded mt-2" v-if="carga">
        <div
          :class="['form-row col-sm-3 rounded ', stylePrecio()]"
          style="display: block"
          v-if="!validarEstadoBoton"
        >
          <table class="rounded">
            <tbody>
              <tr>
                <td><label>Codigo</label></td>
                <td>
                  <input
                    type="text"
                    v-model="form.idespecifico"
                    class="form-control form-control-sm"
                    @keydown.tab="buscarProducto()"
                    ref="codigo"
                  />
                </td>
              </tr>
              <tr>
                <td><label>Descripcion</label></td>
                <td>
                  <input
                    type="text"
                    v-model="form.descripcion"
                    class="form-control form-control-sm"
                    ref="descripcion"
                    data-toggle="tooltip"
                    data-placement="top"
                    :title="form.descripcion"
                    readonly
                  />
                </td>
              </tr>
              <tr>
                <td><label>Medida</label></td>
                <td>
                  <input
                    type="text"
                    v-model="form.medida.descripcion"
                    class="form-control form-control-sm"
                    ref="medida"
                    readonly
                  />
                </td>
              </tr>
              <tr>
                <td><label>Existencia</label></td>
                <td>
                  <input
                    v-if="solicitud.serie == 'PEDIDO'"
                    type="number"
                    class="form-control form-control-sm"
                    ref="existencia"
                    readonly
                  />
                  <input
                    v-else
                    type="text"
                    v-model="form.existencia"
                    class="form-control form-control-sm"
                    ref="existencia"
                    readonly
                  />
                </td>
              </tr>
              <tr>
                <td><label>Cantidad</label></td>
                <td>
                  <input
                    type="number"
                    v-model="form.cantidad"
                    class="form-control form-control-sm"
                    ref="cantidad"
                  />
                </td>
              </tr>
              <tr>
                <td><label>Hectareas</label></td>
                <td>
                  <input
                    type="number"
                    v-model="form.hectareas"
                    class="form-control form-control-sm"
                    ref="hectareas"
                  />
                </td>
              </tr>
              <tr>
                <td><label>Dosis</label></td>
                <td>
                  <input
                    type="number"
                    v-model="form.dosis"
                    class="form-control"
                    ref="dosis"
                  />
                </td>
              </tr>
              <tr>
                <td><label>Region</label></td>
                <td>
                  <multiselect
                    v-model="form.region"
                    :options="centrosCosto"
                    :close-on-select="true"
                    :show-labels="false"
                    label="descripcion"
                    :allow-empty="true"
                    placeholder=""
                  >
                  </multiselect>
                </td>
              </tr>
              <tr>
                <td><label>Varias Regiones</label></td>
                <td>
                  <multiselect
                    v-model="form.variasregiones"
                    label="descripcion"
                    track-by="idcc"
                    placeholder=""
                    open-direction="bottom"
                    :multiple="true"
                    :options="centrosCosto"
                    :searchable="true"
                    :internal-search="true"
                    :clear-on-select="false"
                    :close-on-select="false"
                    :show-no-results="false"
                    :hide-selected="true"
                  >
                  </multiselect>
                </td>
              </tr>
              <tr>
                <td><label>Actividad</label></td>
                <td>
                  <multiselect
                    v-model="form.uso"
                    :options="usos"
                    :close-on-select="true"
                    :show-labels="false"
                    placeholder=""
                    label="descripcion"
                  ></multiselect>
                </td>
              </tr>
              <tr>
                <td><label>Observaciones</label></td>
                <td>
                  <input
                    v-model="form.observaciones"
                    type="text"
                    class="form-control"
                    ref="observaciones"
                  />
                </td>
              </tr>
              <tr>
                <td><label>Fecha de Entrega</label></td>
                <td>
                  <input
                    v-model="form.fecha_entrega"
                    :min="fechaMinima"
                    type="date"
                    class="form-control"
                    ref="fechaentrega"
                    :disabled="validarSerie"
                  />
                </td>
              </tr>
              <tr>
                <td>
                  <button
                    :class="buttonClass('btn-primary')"
                    :disabled="validarEstadoBoton"
                    @click="agregarDetalle()"
                  >
                    Agregar
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
        <div
          :class="['form-row ', tamGrid()]"
          style="display: block"
          v-if="carga"
        >
          <div class="overflow-auto col-md-12" style="max-height: 450px">
            <small
              class="text-danger"
              v-if="
                validationErrors['solicitud.detalles'] &&
                solicitud.detalles.length == 0
              "
              >al menos un detalle es requerido</small
            >
            <table class="table table-sm rounded">
              <thead class="thead-dark">
                <tr>
                  <th>Has</th>
                  <th>Dosis</th>
                  <th>Material</th>
                  <th>Unidad</th>
                  <th>Cantidad</th>
                  <th>Actividad</th>
                  <th>Destino</th>
                  <th>Ccsap</th>
                  <th>Opciones</th>
                </tr>
              </thead>
              <tbody class="table table-center">
                <bodegas-movimientos-movimientos-detalle
                  @borrar="borrarDetalle"
                  v-for="(detalle, i) in solicitud.detalles"
                  :detalle="detalle"
                  :key="i"
                  :fechaMinima="fechaMinima"
                  :serie="solicitud.serie"
                  :validationErrors="validationErrors"
                  :i="i"
                />
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div v-else>Loading...</div>
  </div>
</template>
<script>
import { errorsMixin } from "@/mixins/errors.js";
import Multiselect from "vue-multiselect";

export default {
  props: ["id"],
  components: {
    Multiselect,
  },
  data() {
    return {
      solicitud: null,
      departamentos: [],
      form: {
        idespecifico: null,
        tipo: null,
        existencia: null,
        cantidad: null,
        hectareas: null,
        dosis: null,
        region: null,
        variasregiones: null,
        uso: null,
        observaciones: null,
        fechaentrega: null,
        medida: { idunidad_medida: null, descripcion: null },
        fecha_entrega: moment().add(20, "days").format("YYYY-MM-DD"),
        genericoempresa: null,
      },
      cables: [],
      fechaMinima: moment().add(1, "days").format("YYYY-MM-DD"),
      bodegas: null,
      series: [],
      bodegasDestino: null,
      centrosoperativos: null,
      saving: false,
      validationErrors: {
        solicitud: null,
      },
      empleados: [],
      isLoadingAutoriza: false,
      isLoadingResponsable: false,
      motivos: [
        //Estos datos no se encuentran en la DB, es por eso que se dejó de esta manera
        "NORMAL",
        "Evaporacion combustible",
      ],
      produccion: [],
      carga: false,
      centrosCosto: [],
      usos: [],
      bodegasDestino: [],
      oculto: "Ocultar",
      bodeguero: null,
      empresa_id: null,
    };
  },
  mounted() {
    const dato = {
      opcion: "nuevo",
      id: this.id,
    };
    axios
      .get(`/bodegas/movimientos/movimientos/${JSON.stringify(dato)}/detail`)
      .then((res) => {
        this.solicitud = res.data.solicitud;
        this.bodegas = res.data.bodegas;
        this.bodegasDestino = res.data.bodegasDestino;
        this.centrosoperativos = res.data.centrosoperativos;
        this.empleados = res.data.empleados;
        this.bodeguero = res.data.bodeguero;
        this.empresa_id = res.data.empresa_id;
        // this.series = res.data.series;
        if (this.id != 0) {
          this.produccion = res.data.produccion.contenedores;
          this.centrosCosto = res.data.produccion.centrosCosto;
          this.usos = res.data.produccion.usos;
          this.carga = true;
        }
      })
      .catch((err) => {
        alert(err.message);
      });
  },
  methods: {
    stylePrecio() {
      if (this.solicitud.serie == "PEDIDO") {
        return "bg-success";
      } else {
        if (this.form.descripcion != null) {
          const color = this.form.existencia <= 0 ? "bg-danger" : "bg-success";
          return color;
        } else {
          return "bg-success";
        }
      }
    },
    reporte(tipo) {
      if (tipo == "resumido") {
        window.open(
          `http://192.168.4.23/ReportServer/Pages/ReportViewer.aspx?%2fProduccion%2freporte_mdm_resumido&rs:Command=Render&idsolicitud=${this.solicitud.idsolicitud}&usuario=${this.bodeguero}`
        );
      } else {
        window.open(
          `http://192.168.4.23/ReportServer/Pages/ReportViewer.aspx?%2fProduccion%2fReporte_mdm_detallado&rs:Command=Render&idsolicitud=${this.solicitud.idsolicitud}&usuario=${this.bodeguero}`
        );
      }
    },
    nuevaSolicitud() {
      this.saving = true;
      if (window.location.pathname == "/bodegas/movimientos/movimientos") {
        this.solicitud.idsolicitud = 0;
        this.solicitud.serie = null;
        this.solicitud.destino = null;
        this.solicitud.estado = "SOLICITADO";
        this.solicitud.detalles = [];
        this.solicitud.observaciones = null;
        this.solicitud.mdm = null;
        this.solicitud.idsolicitud_ref = null;
        this.solicitud.tipo_operacion = "NORMAL";
        this.solicitud.idsolicitante = null;
        this.solicitud.idautoriza = null;
        this.solicitud.centroOperativo = null;
        this.solicitud.bodega = null;
        this.solicitud.destino = null;
        this.solicitud.produccion = null;
        this.solicitud.responsable = null;
        this.solicitud.autoriza = null;
        this.carga = false;
      } else {
        window.location.href = `${window.location.protocol}//${window.location.host}/bodegas/movimientos/movimientos`;
      }
      this.saving = false;
    },
    agregarDetalle() {
      if (this.solicitud.bodega == "") {
        alert("Por favor seleccione una bodega");
        return;
      }
      if (
        this.solicitud.estado == "ENTREGADO" ||
        this.solicitud.estado == "FINALIZADO" ||
        this.solicitud.estado == "ANULADO" ||
        this.solicitud.estado == "ENVIADO"
      ) {
        alert(
          "No es permitido agregar mas Items cuando el estado es ENTREGADO, FINALIZADO, ENVIADO ó ANULADO "
        );
        return;
      }

      if (
        this.form.cantidad == "" ||
        this.form.cantidad == null ||
        this.form.cantidad == 0
      ) {
        alert(
          "Por favor ingresar la cantidad, no puede ir con valor vacio o valor 0"
        );
        return;
      }

      if (this.solicitud.serie != "PEDIDO" && this.form.uso == null) {
        alert("Por favor ingregar el actividad, ya que es obligatorio");
        return;
      }

      let region = { idcc: null, descripcion: null, dimension3: null };
      if (this.form.region != null) {
        const cc = this.form.region.descripcion.split(" - ");
        region.idcc = this.form.region.idcc;
        region.descripcion = cc[1];
        region.dimension3 = this.form.region.dimension3;
      }

      if (this.form.variasregiones != null) {
        if (this.form.variasregiones.length <= 0) {
          this.form.variasregiones = null;
        }
      }

      if (this.form.tipo.idtipo_generico == 2 && !this.form.uso) {
        alert("Uso es obligatorio en un servicio, por favor revisar");
        return;
      }

      this.form.hectareas =
        this.form.hectareas == null || this.form.hectareas == ""
          ? 0
          : this.form.hectareas;
      this.form.dosis =
        this.form.dosis == null || this.form.dosis == "" ? 0 : this.form.dosis;
      // this.form.region.descripcion.descripcion = cc;
      this.solicitud.detalles.push({
        aprobacion_fecha: null,
        aprobacion_usuario: this.form.variasregiones,
        cantidad: this.form.cantidad,
        cantidad_convertida: null,
        centrocosto: region,
        consolidacion: null,
        consumo: null,
        costo_inventario: null,
        costo_promedio_global: null,
        dosis: this.form.dosis,
        estado: "SOLICITADO",
        existencia: this.form.existencia,
        factor_conversion: null,
        fecha: null,
        hectareas: this.form.hectareas,
        hora_ejecucion: null,
        idcc: null,
        idcomprador: null,
        iddetalle: null,
        idespecifico: null,
        idespecifico_temporal: null,
        idgenerico: null,
        idsolicitud: null,
        idtipo_caopex: null,
        idtipogenerico: null,
        idunidad_medida: null,
        iduso: null,
        indautorizado: null,
        indcotizado: null,
        medida: {
          idunidad_medida: this.form.medida.idunidad_medida,
          descripcion: this.form.medida.descripcion,
        },
        monto: null,
        observaciones: this.form.observaciones,
        orden: null,
        precio: null,
        producto: {
          idespecifico: this.form.idespecifico,
          descripcion: this.form.descripcion,
        },
        genericoempresa: this.form.genericoempresa,
        saldo: null,
        saldo_destino: null,
        saldo_nuevo_global: null,
        saldo_nuevo_region: null,
        semanas_inventario: null,
        uso: this.form.uso,
        fecha_entrega: this.form.fecha_entrega,
        fechasentregas: [],
      });
      this.limpiarForm();
      this.$refs.codigo.focus();
    },
    buscarProducto() {
      this.saving = true;
      if (this.form.idespecifico == "" || this.form.idespecifico == null) {
        return;
      }

      this.$refs.existencia.focus();
      let dato = {
        opcion: "buscarProducto",
        tipo: this.solicitud.serie,
        idregion: this.solicitud.bodega.idcc,
        idproducto: this.form.idespecifico,
        empresa_id: this.empresa_id,
      };

      axios
        .get(`/bodegas/movimientos/movimientos/${JSON.stringify(dato)}/detail`)
        .then((response) => {
          this.form.idespecifico = response.data.idespecifico;
          this.form.descripcion = response.data.descripcion;
          this.form.genericoempresa = response.data.genericoempresa;
          this.form.existencia =
            response.data.existencia == 0 || response.data.existencia == null
              ? 0
              : response.data.existencia;
          this.form.medida.descripcion = response.data.medida;
          this.form.medida.idunidad_medida = response.data.idunidad_medida;
          this.form.tipo = response.data.tipo;
          this.saving = false;
        })
        .catch((e) => {
          this.limpiarForm();
          this.$refs.codigo.focus();
          this.handleError(e);
          this.saving = false;
        });
    },
    tamGrid() {
      const estado = this.validarEstadoBoton;
      return estado ? "col-sm-12" : "col-sm-9";
    },
    limpiarForm() {
      this.form.idespecifico = null;
      this.form.existencia = null;
      this.form.cantidad = null;
      this.form.hectareas = null;
      this.form.descripcion = null;
      this.form.dosis = null;
      this.form.variasregiones = null;
      this.form.uso = null;
      this.form.fechaentrega = null;
      this.form.tipo = null;
      this.form.medida = { idunidad_medida: null, descripcion: null };
      this.form.fecha_entrega = moment().add(20, "days").format("YYYY-MM-DD");
      this.form.genericoempresa = null;
    },
    ocultarEncabezado() {
      $("#encabezado").toggle();
      this.oculto = this.oculto == "Ocultar" ? "Mostrar" : "Ocultar";
    },
    borrarDetalle(i) {
      this.solicitud.detalles.splice(i, 1);
    },
    finalizar() {
      this.saving = true;
      const dato = {
        opcion: "finalizar",
        idsolicitud: this.solicitud.idsolicitud,
      };
      axios
        .get(`/bodegas/movimientos/movimientos/${JSON.stringify(dato)}/detail`)
        .then((response) => {
          this.solicitud = response.data;
          alert("trasaccion finalizada con exito.");
          this.saving = false;
        })
        .catch((e) => {
          this.handleError(e);
          this.saving = false;
        });
    },
    anular() {
      this.saving = true;
      const dato = {
        opcion: "anular",
        idsolicitud: this.solicitud.idsolicitud,
        empresa_id: this.empresa_id,
      };
      axios
        .get(`/bodegas/movimientos/movimientos/${JSON.stringify(dato)}/detail`)
        .then((response) => {
          this.solicitud = response.data;
          alert("transaccion anulada con exito.");
          this.saving = false;
        })
        .catch((e) => {
          this.handleError(e);
          this.saving = false;
        });
    },
    guardar() {
      this.saving = true;
      axios
        .post("/bodegas/movimientos/movimientos", {
          solicitud: this.solicitud,
          estado: this.solicitud.estado,
        })
        .then((res) => {
          this.solicitud = res.data;
          alert("trasaccion guardada con exito.");
          this.saving = false;
        })
        .catch((err) => {
          this.handleError(err);
          this.saving = false;
        });
    },
    cambioBodega(value) {
      this.saving = true;
      this.produccion = [];
      this.centrosCosto = [];
      this.solicitud.detalles = [];
      this.usos = [];
      this.series = [];

      let dato = {
        opcion: "SERIES",
        idcc: value.idcc,
      };

      axios
        .get(`/bodegas/movimientos/movimientos/${JSON.stringify(dato)}/detail`)
        .then((response) => {
          this.series = response.data.series;
          this.departamentos = response.data.departamentos;
          this.saving = false;
        })
        .catch((e) => {
          this.handleError(e);
          this.saving = false;
        });

      dato = {
        opcion: "produccion",
        idcc: value.idcc,
        tipo: this.solicitud.serie,
      };

      axios
        .get(`/bodegas/movimientos/movimientos/${JSON.stringify(dato)}/detail`)
        .then((response) => {
          this.produccion = response.data.contenedores;
          this.centrosCosto = response.data.centrosCosto;
          this.usos = response.data.usos;
          this.saving = false;
        })
        .catch((e) => {
          this.handleError(e);
          this.saving = false;
        });
    },
    cambioTipo(value) {
      this.carga = true;
    },
  },
  computed: {
    mdmsolicitud() {
      if (
        this.solicitud.serie == "DEVOLUCION" ||
        this.solicitud.serie == "ENVIO" ||
        this.solicitud.serie == "DEVPROV"
      ) {
        return false;
      } else {
        return true;
      }
    },
    validarSerie() {
      if (this.solicitud.serie == "PEDIDO") {
        return false;
      } else {
        return true;
      }
    },
    validarTipo() {
      if (this.solicitud.serie == "TRASLADO") {
        return false;
      } else {
        return true;
      }
    },
    validarAnular() {
      if (this.solicitud.fecha_sync_sap != null) {
        return false;
      }

      switch (this.solicitud.serie) {
        case "PEDIDO":
          return this.solicitud.estado == "ENVIADO" &&
            this.solicitud.idsolicitud > 0
            ? true
            : false;
        default:
          return false;
      }
    },
    validarReporte() {
      if (this.solicitud.idsolicitud > 0) {
        return true;
      } else {
        return false;
      }
    },
    validarFinalizar() {
      return this.solicitud.estado == "SOLICITADO" &&
        this.solicitud.idsolicitud > 0
        ? true
        : false;
    },
    validarEstado() {
      if (
        this.solicitud.estado == "ENTREGADO" ||
        this.solicitud.estado == "FINALIZADO" ||
        this.solicitud.estado == "ANULADO" ||
        this.solicitud.estado == "ENVIADO"
      ) {
        return true;
      } else {
        return false;
      }
    },
    validarEstadoBoton() {
      const estado = this.solicitud.estado;
      switch (this.solicitud.serie) {
        case "PEDIDO":
        case "SALIDA":
        case "VALE":
          return estado == "ENTREGADO" ||
            estado == "FINALIZADO" ||
            estado == "ANULADO" ||
            estado == "ENVIADO" ||
            estado == "RECIBIDA"
            ? true
            : false;

        default:
          return true;
      }
    },
    mostrarBotonGuardar() {
      if (this.solicitud.fecha_sync_sap != null) {
        return false;
      }

      const estado = this.solicitud.estado;
      switch (this.solicitud.serie) {
        case "PEDIDO":
          return estado == "SOLICITADO" || estado == "ENVIADO" ? true : false;
        case "VALE":
        case "SALIDA":
          return estado == "SOLICITADO" && this.solicitud.idsolicitud == 0
            ? true
            : false;
        default:
          return false;
      }
    },
  },
  mixins: [errorsMixin],
};
</script>
