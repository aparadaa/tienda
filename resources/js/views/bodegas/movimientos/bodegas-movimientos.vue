<template>
  <div v-if="movimiento" style="padding: 0">
    <div class="card">
      <div class="card-header">
        <h4 class="card-title">Movimiento</h4>
      </div>
      <div class="card-body">
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="typo__label">Bodega</label>
              <multiselect
                selectedLabel="Seleccionado"
                selectLabel="Presiona enter para seleccionar"
                v-model="movimiento.bodega"
                deselect-label="No se puede quitar este valor"
                track-by="nombre"
                label="nombre"
                placeholder="Seleccione uno"
                :options="mis_bodegas"
                :searchable="true"
                :allow-empty="false"
                :disabled="saving"
              >
              </multiselect>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="typo__label">Tipo de movimiento</label>
              <multiselect
                selectedLabel="Seleccionado"
                selectLabel="Presiona enter para seleccionar"
                v-model="movimiento.tipo"
                deselect-label="No se puede quitar este valor"
                track-by="nombre"
                label="nombre"
                placeholder="Seleccione uno"
                :options="tipo_movimientos"
                :searchable="true"
                :allow-empty="false"
                @select="verificarTipo"
                :disabled="saving"
              >
              </multiselect>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="typo__label">Estado</label>
              <multiselect
                selectedLabel="Seleccionado"
                selectLabel="Presiona enter para seleccionar"
                v-model="movimiento.estado"
                deselect-label="No se puede quitar este valor"
                track-by="descripcion"
                label="descripcion"
                placeholder="Seleccione uno"
                :options="estados"
                :searchable="true"
                :allow-empty="false"
                :disabled="true"
              >
              </multiselect>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="typo__label">Tipo de pago</label>
              <multiselect
                selectedLabel="Seleccionado"
                selectLabel="Presiona enter para seleccionar"
                v-model="movimiento.tipo_pago"
                deselect-label="No se puede quitar este valor"
                track-by="descripcion"
                label="descripcion"
                placeholder="Seleccione uno"
                :options="tipo_pagos"
                :searchable="true"
                :allow-empty="false"
                @select="verificarTipoPago"
                :disabled="saving"
              >
              </multiselect>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6">
            <div class="form-group">
              <label class="typo__label">Nit</label>
              <input
                type="text"
                class="form-control"
                v-model="movimiento.cliente.id"
                :disabled="saving || movimiento.id != '0'"
                @keydown.enter="obtenerCliente()"
              />
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label class="typo__label">Nombre</label>
              <input
                type="text"
                class="form-control"
                v-model="movimiento.cliente.nombres"
                :disabled="true"
              />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6" v-if="movimiento.tipo.nombre == 'Traslado'">
            <div class="form-group">
              <label class="typo__label">Bodega que recibe</label>
              <multiselect
                selectedLabel="Seleccionado"
                selectLabel="Presiona enter para seleccionar"
                v-model="movimiento.bodega_recibe"
                deselect-label="No se puede quitar este valor"
                track-by="nombre"
                label="nombre"
                placeholder="Seleccione uno"
                :options="bodegas"
                :searchable="true"
                :allow-empty="false"
                :disabled="saving"
              >
              </multiselect>
            </div>
          </div>
          <div
            class="col-md-6"
            v-if="movimiento.tipo_pago.descripcion == 'Electrónico'"
          >
            <div class="form-group">
              <label class="typo__label">Voucher</label>
              <input
                type="text"
                :class="errorClass('voucher')"
                v-model="movimiento.voucher"
                :disabled="saving"
              />
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              <label class="typo__label">Codigo de barras del producto</label>
              <input
                type="text"
                :class="errorClass('codigo_barra')"
                v-model="codigo_barra"
                @keyup.enter="agregarProducto"
                :disabled="saving || movimiento.id != '0'"
              />
            </div>
          </div>
          <div class="col-md-2">
            <button
              class="btn btn-primary"
              @click="save"
              :disabled="saving || movimiento.id != '0'"
              style="margin-top: 30px"
            >
              <i class="fas fa-save"></i>
            </button>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label class="typo__label" for="detalle">Detalles</label>
              <span
                class="text-danger"
                v-if="validationErrors['movimiento.detalles']"
              >
                <br />
                Debe de enviar al menos un producto
              </span>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th v-if="movimiento.tipo.nombre == 'Entrada'">
                      Costo Unitario
                    </th>
                    <th v-else>Precio</th>
                    <th v-if="movimiento.tipo.nombre == 'Entrada'">
                      Fecha vencimiento
                    </th>
                    <th v-else>Subtotal</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="(detalle, index) in movimiento.detalles"
                    :key="index"
                  >
                    <td>
                      <input
                        type="text"
                        class="form-control"
                        :value="
                          detalle.producto.nombre +
                          ' ' +
                          detalle.producto.marca +
                          ' ' +
                          detalle.producto.descripcion
                        "
                        :disabled="saving"
                      />
                    </td>
                    <td>
                      <input
                        type="number"
                        class="form-control"
                        v-model="detalle.cantidad"
                      />
                    </td>
                    <td v-if="movimiento.tipo.nombre == 'Entrada'">
                      <input
                        type="number"
                        class="form-control"
                        v-model="detalle.costo_unitario"
                        :disabled="saving"
                      />
                    </td>
                    <td v-else>
                      <input
                        type="number"
                        class="form-control"
                        v-model="detalle.producto.precio_venta"
                        :disabled="true"
                      />
                    </td>
                    <td v-if="movimiento.tipo.nombre == 'Entrada'">
                      <input
                        type="date"
                        class="form-control"
                        v-model="detalle.expiracion"
                      />
                    </td>
                    <td v-else>
                      <input
                        type="number"
                        class="form-control"
                        :value="
                          (
                            parseFloat(detalle.producto.precio_venta) *
                            parseFloat(detalle.cantidad)
                          ).toFixed(2)
                        "
                        :disabled="true"
                      />
                    </td>
                  </tr>
                  <tr v-if="movimiento.detalles.length > 0">
                    <td colspan="3" class="text-right">Total</td>
                    <td>
                      <input
                        type="number"
                        class="form-control"
                        :value="total"
                        :disabled="true"
                      />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div
      class="modal"
      id="create_cliente"
      tabindex="-1"
      role="dialog"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalCenteredLabel">
              Crear nuevo cliente
            </h5>
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
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <label class="typo__label">Nombres</label>
                  <input
                    type="text"
                    :class="errorClass('nombres')"
                    v-model="cliente.nombres"
                    autofocus
                  />
                </div>
                <span v-if="validationErrors.nombres" class="text-danger">{{
                  validationErrors.nombres[0]
                }}</span>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="typo__label">Correo</label>
                  <input
                    type="text"
                    :class="errorClass('email')"
                    v-model="cliente.email"
                  />
                </div>
                <span v-if="validationErrors.email" class="text-danger">{{
                  validationErrors.email[0]
                }}</span>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label class="typo__label">Direccion</label>
                  <textarea
                    type="text"
                    :class="errorClass('direccion')"
                    v-model="cliente.direccion"
                  ></textarea>
                </div>
                <span v-if="validationErrors.direccion" class="text-danger">{{
                  validationErrors.direccion[0]
                }}</span>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Cerrar
            </button>
            <button @click="agregarCliente" class="btn btn-primary">
              Guardar cambios
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div v-else>Loading...</div>
</template>
<script>
import { errorsMixin } from "@/mixins/errors.js";
import Multiselect from "vue-multiselect";

export default {
  props: [
    "id",
    "bodegas",
    "estados",
    "tipo_pagos",
    "tipo_movimientos",
    "mis_bodegas",
  ],
  components: {
    Multiselect,
  },
  data() {
    return {
      movimiento: null,
      codigo_barra: null,
      cliente: {
        nombres: null,
        direccion: null,
        email: null,
      },
      validationErrors: {},
      saving: false,
    };
  },
  mounted() {
    this.startTransaction();

    const params = {
      id: this.id,
    };

    axios
      .get(`/bodegas/movimientos/data_movimiento/detail`, { params })
      .then((res) => {
        this.movimiento = res.data;
        this.saving = false;
      })
      .catch((err) => {
        this.handleError(err);
      });
  },
  methods: {
    verificarTipoPago(selectedOption) {
      if (
        selectedOption.descripcion == "Traslado" &&
        this.movimiento.tipo.nombre != "Traslado"
      ) {
        this.showMessage(
          "error",
          "Upss!!!",
          'No se puede seleccionar "Traslado" como tipo de pago si el movimiento no es de tipo "Traslado"'
        );
        this.movimiento.tipo_pago = {
          id: 0,
          descripcion: "",
        };
      }

      if (
        selectedOption.descripcion != "Traslado" &&
        this.movimiento.tipo.nombre == "Traslado"
      ) {
        this.showMessage(
          "error",
          "Upss!!!",
          'No se puede seleccionar un tipo de pago diferente a "Traslado" si el movimiento es de tipo "Traslado"'
        );
        this.movimiento.tipo_pago = {
          id: 0,
          descripcion: "",
        };
      }

      if (selectedOption.descripcion != "Electrónico") {
        this.movimiento.voucher = null;
      }
    },
    verificarTipo(selectedOption) {
      if (selectedOption.nombre == "Traslado") {
        this.movimiento.tipo_pago = {
          id: 3,
          descripcion: "Traslado",
        };
      }
    },
    agregarProducto() {
      this.startTransaction();

      const params = {
        codigo_barra: this.codigo_barra,
        bodega_id: this.movimiento.bodega.id,
        tipo: this.movimiento.tipo,
      };

      axios
        .get(`/bodegas/movimientos/data_producto/detail`, { params })
        .then((res) => {
          const producto = res.data;
          if (this.movimiento.detalles.length == 0) {
            this.movimiento.detalles.push({
              producto: producto,
              cantidad: 1,
              costo_unitario: "",
              expiracion: "",
            });
          } else {
            let existe = false;
            this.movimiento.detalles.forEach((detalle, index) => {
              if (detalle.producto.id == producto.id) {
                existe = true;
                this.movimiento.detalles[index].cantidad =
                  parseInt(this.movimiento.detalles[index].cantidad) + 1;
              }
            });
            if (!existe) {
              this.movimiento.detalles.push({
                producto: producto,
                cantidad: 1,
                costo_unitario: "",
                expiracion: "",
              });
            }
          }

          this.saving = false;
        })
        .catch((err) => {
          this.handleError(err);
        });
    },
    obtenerCliente() {
      this.startTransaction();

      axios
        .get(`/clientes/clientes/${this.movimiento.cliente.id}`, {
          headers: {
            "Content-Type": "application/json",
          },
        })
        .then((res) => {
          if (_.isEmpty(res.data)) {
            $("#create_cliente").modal("show");
          } else {
            this.movimiento.cliente = res.data;
          }
          this.saving = false;
        })
        .catch((err) => {
          this.handleError(err);
        });
    },
    agregarCliente() {
      this.startTransaction();
      let cliente = this.cliente;
      cliente.id = this.movimiento.cliente.id;
      this.validationErrors = {};

      axios
        .post(`/clientes/clientes`, cliente, {
          headers: {
            "Content-Type": "application/json",
          },
        })
        .then((res) => {
          this.movimiento.cliente = res.data;
          this.saving = false;
          $("#create_cliente").modal("hide");
        })
        .catch((err) => {
          this.handleError(err);
        });
    },
    save() {
      this.startTransaction();
      const params = {
        movimiento: this.movimiento,
      };
      axios
        .post(`/bodegas/movimientos`, params)
        .then((res) => {
          this.movimiento.cliente = {
            id: "",
            nombres: "",
            direccion: "",
          };

          this.movimiento.estado = {
            id: 0,
            descripcion: "",
          };

          this.movimiento.tipo_pago = {
            id: 1,
            descripcion: "Efectivo",
          };

          this.movimiento.bodega_recibe = {
            id: 0,
            nombre: "",
          };
          this.codigo_barra = "";
          this.movimiento.detalles = [];
          this.showToast(res.data.message);
          this.saving = false;
        })
        .catch((err) => {
          this.handleError(err);
        });
    },
  },
  computed: {
    total() {
      let total = 0;
      this.movimiento.detalles.forEach((detalle) => {
        total +=
          parseFloat(detalle.producto.precio_venta) *
          parseFloat(detalle.cantidad);
      });
      return total.toFixed(2);
    },
  },
  mixins: [errorsMixin],
};
</script>
