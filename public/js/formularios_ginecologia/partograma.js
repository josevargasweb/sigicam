function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _inheritsLoose(subClass, superClass) { subClass.prototype = Object.create(superClass.prototype); subClass.prototype.constructor = subClass; _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _classPrivateFieldLooseBase(receiver, privateKey) { if (!Object.prototype.hasOwnProperty.call(receiver, privateKey)) { throw new TypeError("attempted to use private field on non-instance"); } return receiver; }

var id = 0;

function _classPrivateFieldLooseKey(name) { return "__private_" + id++ + "_" + name; }

var _partograma = /*#__PURE__*/_classPrivateFieldLooseKey("partograma");

var _secciones = /*#__PURE__*/_classPrivateFieldLooseKey("secciones");

var _ejeLCF = /*#__PURE__*/_classPrivateFieldLooseKey("ejeLCF");

var _ejeDilatacion = /*#__PURE__*/_classPrivateFieldLooseKey("ejeDilatacion");

var _ejePlano = /*#__PURE__*/_classPrivateFieldLooseKey("ejePlano");

var _cargarImagenes = /*#__PURE__*/_classPrivateFieldLooseKey("cargarImagenes");

var _crearEjes = /*#__PURE__*/_classPrivateFieldLooseKey("crearEjes");

var _inicializarCanvas = /*#__PURE__*/_classPrivateFieldLooseKey("inicializarCanvas");

var _crearPanel = /*#__PURE__*/_classPrivateFieldLooseKey("crearPanel");

var _crearRejillaInicial = /*#__PURE__*/_classPrivateFieldLooseKey("crearRejillaInicial");

var _eventos = /*#__PURE__*/_classPrivateFieldLooseKey("eventos");

var _buscarSeccion = /*#__PURE__*/_classPrivateFieldLooseKey("buscarSeccion");

var Partograma = /*#__PURE__*/function () {
  "use strict";

  function Partograma() {
    Object.defineProperty(this, _buscarSeccion, {
      value: _buscarSeccion2
    });
    Object.defineProperty(this, _eventos, {
      value: _eventos2
    });
    Object.defineProperty(this, _crearRejillaInicial, {
      value: _crearRejillaInicial2
    });
    Object.defineProperty(this, _crearPanel, {
      value: _crearPanel2
    });
    Object.defineProperty(this, _inicializarCanvas, {
      value: _inicializarCanvas2
    });
    Object.defineProperty(this, _crearEjes, {
      value: _crearEjes2
    });
    Object.defineProperty(this, _secciones, {
      writable: true,
      value: void 0
    });
    Object.defineProperty(this, _ejeLCF, {
      writable: true,
      value: void 0
    });
    Object.defineProperty(this, _ejeDilatacion, {
      writable: true,
      value: void 0
    });
    Object.defineProperty(this, _ejePlano, {
      writable: true,
      value: void 0
    });
    this.camara = void 0;
    this.arrastrando = void 0;
    this.id_canvas = void 0;
    this.ocultar_panel = void 0;
    this.posicionar_ejes_derecha = void 0;
    this.callbackCambio = void 0;
    this.eventoMouseMoveAnterior = void 0;
    _classPrivateFieldLooseBase(this, _secciones)[_secciones] = [];
    this.margen_secciones = new Margen(40, 0, 100, 0);
    this.camara = new Camara();
    this.ocultar_panel = false;
  }

  var _proto = Partograma.prototype;

  _proto.ponerID = function ponerID(id_canvas) {
    this.id_canvas = id_canvas;

    _classPrivateFieldLooseBase(this, _inicializarCanvas)[_inicializarCanvas]();

    _classPrivateFieldLooseBase(this, _crearPanel)[_crearPanel]();

    _classPrivateFieldLooseBase(this, _crearRejillaInicial)[_crearRejillaInicial]();

    _classPrivateFieldLooseBase(this, _crearEjes)[_crearEjes]();

    _classPrivateFieldLooseBase(this, _eventos)[_eventos]();

    this.info = new Info(this.contexto);
    this.redibujar();
  };

  Partograma.instancia = function instancia() {
    if (!_classPrivateFieldLooseBase(this, _partograma)[_partograma]) {
      _classPrivateFieldLooseBase(this, _cargarImagenes)[_cargarImagenes]();

      _classPrivateFieldLooseBase(this, _partograma)[_partograma] = new Partograma();
    }

    return _classPrivateFieldLooseBase(this, _partograma)[_partograma];
  };

  _proto.calcularAnchoEje = function calcularAnchoEje(nombre) {
    switch (nombre) {
      case "LCF":
        return _classPrivateFieldLooseBase(this, _ejeLCF)[_ejeLCF].ancho;

      case "Dilatación":
        return _classPrivateFieldLooseBase(this, _ejeDilatacion)[_ejeDilatacion].ancho;

      case "Plano":
        return _classPrivateFieldLooseBase(this, _ejePlano)[_ejePlano].ancho;

      default:
        return 0;
    }
  };

  Partograma.windowToCanvas = function windowToCanvas(canvas, x, y) {
    var bbox = canvas.getBoundingClientRect();
    return {
      x: x - bbox.left * (canvas.width / bbox.width),
      y: y - bbox.top * (canvas.height / bbox.height)
    };
  };

  _proto.limpiar = function limpiar() {
    this.contexto.clearRect(0, 0, this.canvas.width, this.canvas.height);
  };

  _proto.agregarSeccion = function agregarSeccion(seccion) {
    _classPrivateFieldLooseBase(this, _secciones)[_secciones].push(seccion);

    if (this.callbackCambio) {
      this.callbackCambio(this.tieneInfoNueva());
    }
  }
  /**
  Calcula el margen para colocar la siguiente sección.
   */
  ;

  _proto.calcularAnchoSecciones = function calcularAnchoSecciones() {
    var ancho = this.margen_secciones.izquierdo;

    for (var i = 0; i < _classPrivateFieldLooseBase(this, _secciones)[_secciones].length; i++) {
      ancho += _classPrivateFieldLooseBase(this, _secciones)[_secciones][i].ancho_seccion;
    }

    return ancho;
  };

  _proto.calcularAnchoSeccionesYEjes = function calcularAnchoSeccionesYEjes() {
    return this.calcularAnchoSecciones() + 160;
  };

  _proto.traerSecciones = function traerSecciones() {
    return _classPrivateFieldLooseBase(this, _secciones)[_secciones];
  };

  _proto.redibujar = function redibujar() {
    this.canvas.width = this.canvas.parentNode.clientWidth - 12;
    this.limpiar();
    this.calcularXCamara();
    this.contexto.translate(this.camara.x_acumulado, this.camara.y_acumulado);

    for (var i = 0; i < _classPrivateFieldLooseBase(this, _secciones)[_secciones].length; i++) {
      _classPrivateFieldLooseBase(this, _secciones)[_secciones][i].dibujar();
    }

    this.reajustarEjes();

    _classPrivateFieldLooseBase(this, _ejeLCF)[_ejeLCF].dibujar();

    _classPrivateFieldLooseBase(this, _ejeDilatacion)[_ejeDilatacion].dibujar();

    _classPrivateFieldLooseBase(this, _ejePlano)[_ejePlano].dibujar();

    this.contexto.setTransform(1, 0, 0, 1, 0, 0);

    if (!this.ocultar_panel) {
      this.panel.dibujar();
    }

    this.info.calcularPosicion(this.panel);
    this.info.dibujar();
  };

  _proto.reajustarEjes = function reajustarEjes() {
    _classPrivateFieldLooseBase(this, _ejeDilatacion)[_ejeDilatacion].margen.izquierdo = this.calcularAnchoSecciones() + 10;
    _classPrivateFieldLooseBase(this, _ejePlano)[_ejePlano].margen.izquierdo = this.calcularAnchoSecciones() + 70;

    if (this.calcularAnchoSeccionesYEjes() > this.canvas.width) {
      _classPrivateFieldLooseBase(this, _ejeLCF)[_ejeLCF].margen.izquierdo = -this.camara.x_acumulado + 5;

      if (this.posicionar_ejes_derecha) {
        _classPrivateFieldLooseBase(this, _ejeDilatacion)[_ejeDilatacion].margen.izquierdo = this.margen_secciones.izquierdo + this.calcularBloquesVisibles() * this.ancho_seccion - this.camara.x_acumulado + 10;
        _classPrivateFieldLooseBase(this, _ejePlano)[_ejePlano].margen.izquierdo = this.margen_secciones.izquierdo + this.calcularBloquesVisibles() * this.ancho_seccion + 60 - this.camara.x_acumulado + 10;
        this.contexto.fillStyle = "white";
        this.contexto.fillRect(_classPrivateFieldLooseBase(this, _ejeDilatacion)[_ejeDilatacion].margen.izquierdo, 0, this.canvas.width, this.canvas.height);
        this.contexto.fillStyle = "black";
      } else {
        _classPrivateFieldLooseBase(this, _ejeDilatacion)[_ejeDilatacion].margen.izquierdo = this.canvas.width - this.camara.x_acumulado - 120;
        _classPrivateFieldLooseBase(this, _ejePlano)[_ejePlano].margen.izquierdo = this.canvas.width - this.camara.x_acumulado - 60;
      }
    }
  };

  _proto.calcularXCamara = function calcularXCamara() {
    var xmax = this.calcularAnchoSeccionesYEjes() > this.canvas.width ? this.calcularAnchoSeccionesYEjes() - this.canvas.width : 0;
    this.camara.revisarAcumulados(-xmax, 0, 0, 0);
  };

  _proto.datos = function datos() {
    var datos = [];

    _classPrivateFieldLooseBase(this, _secciones)[_secciones].forEach(function (seccion) {
      var bloque = {
        id: seccion.id,
        hora: seccion.hora,
        puntos: []
      };
      seccion.cuadrantes.forEach(function (valor) {
        var punto = {
          id: valor.id,
          x: valor.x,
          y: valor.y,
          valor: valor.tipo.nombre
        };
        bloque.puntos.push(punto);
      });
      datos.push(bloque);
    });

    return datos;
  };

  _proto.ponerSecciones = function ponerSecciones(bloques) {
    _classPrivateFieldLooseBase(this, _secciones)[_secciones] = [];

    for (var i = 0; i < bloques.length; i++) {
      var margenNuevo = new Margen(this.calcularAnchoSecciones(), 0, this.margen_secciones.superior, 0);
      var seccion = new Seccion(this.contexto, bloques[i].hora, margenNuevo, this.celda);
      seccion.id = bloques[i].id;
      seccion.usuario_responsable = bloques[i].usuario_responsable;
      this.agregarSeccion(seccion);
    }
  };

  _proto.ponerPuntos = function ponerPuntos(puntos) {
    for (var i = 0; i < puntos.length; i++) {
      var seccion = _classPrivateFieldLooseBase(this, _buscarSeccion)[_buscarSeccion](puntos[i].id_seccion);

      if (seccion) {
        var punto = new Cuadrante(puntos[i].x, puntos[i].y);
        punto.tipo = this.obtenerOpcion(puntos[i].opcion);
        punto.usuario_responsable = puntos[i].usuario_responsable;
        punto.id = puntos[i].id;
        seccion.agregarPunto(punto);
      }
    }
  };

  _proto.obtenerOpcion = function obtenerOpcion(nombre) {
    switch (nombre) {
      case "plano":
        return new OpcionPlano(this.contexto);

      case "dilatacion_cervical":
        return new OpcionDilatacionCervical(this.contexto);

      case "lcf":
        return new OpcionLCF(this.contexto);

      case "posicion0":
        return new OpcionPosicion(this.contexto, "0", "0");

      case "posicion45":
        return new OpcionPosicion(this.contexto, "45", "45");

      case "posicion90":
        return new OpcionPosicion(this.contexto, "90", "90");

      case "posicion135":
        return new OpcionPosicion(this.contexto, "135", "135");

      case "posicion180":
        return new OpcionPosicion(this.contexto, "180", "180");

      case "posicion225":
        return new OpcionPosicion(this.contexto, "225", "225");

      case "posicion270":
        return new OpcionPosicion(this.contexto, "270", "270");

      case "posicion315":
        return new OpcionPosicion(this.contexto, "315", "315");

      default:
        return null;
    }
  };

  Partograma.puntoDentro = function puntoDentro(x_actual, y_actual, x, y, ancho, alto) {
    return x_actual > x && x_actual < ancho + x && y_actual > y && y_actual < alto + y;
  };

  Partograma.alert = function (_alert) {
    function alert(_x) {
      return _alert.apply(this, arguments);
    }

    alert.toString = function () {
      return _alert.toString();
    };

    return alert;
  }(function (mensaje) {
    if (bootbox) {
      bootbox.alert(mensaje);
    } else {
      alert(mensaje);
    }
  });

  Partograma.prompt = function (_prompt) {
    function prompt(_x2) {
      return _prompt.apply(this, arguments);
    }

    prompt.toString = function () {
      return _prompt.toString();
    };

    return prompt;
  }(function (mensaje) {
    var promesa = new Promise(function (res, rej) {
      if (bootbox) {
        bootbox.prompt({
          title: mensaje,
          inputType: 'number',
          callback: function callback(result) {
            if (result === null) {
              return;
            }

            if (isNaN(parseInt(result))) {
              rej(result);
              return;
            }

            result = Math.round(result).toString();

            if (result >= 0 && result < 24) {
              res(result);
            } else {
              rej(result);
            }
          }
        });
      } else {
        var num = prompt(mensaje);

        if (isNaN(parseInt(num))) {
          rej(num);
          return;
        }

        num = Math.round(num).toString();

        if (num >= 0 && num < 24) {
          res(num);
        } else {
          rej(num);
        }
      }
    });
    return promesa;
  });

  _proto.calcularBloquesVisibles = function calcularBloquesVisibles() {
    var ancho_visible = this.canvas.width - this.calcularAnchoEje("Dilatación") - this.calcularAnchoEje("Plano");
    return Math.floor(ancho_visible / this.ancho_seccion);
  };

  _proto.obtenerImagenes = function obtenerImagenes() {
    var imagenes = [];
    this.ocultar_panel = true;
    this.camara.x_acumulado = 0;
    this.posicionar_ejes_derecha = true;
    this.redibujar();
    var camara = 0;
    var cantidad_visibles = this.calcularBloquesVisibles();
    var posicion_actual = this.camara.x_acumulado;

    var bloques = _classPrivateFieldLooseBase(this, _secciones)[_secciones];

    for (var i = 0; i < bloques.length; i++) {
      if (i == bloques.length - 1 || i + cantidad_visibles == bloques.length - 1) {
        this.posicionar_ejes_derecha = false;
        this.redibujar();
      }

      if (i % cantidad_visibles == 0) {
        var datos_imagen = this.canvas.toDataURL();
        imagenes.push(datos_imagen);
        camara += -bloques[i].ancho_seccion * cantidad_visibles;
        this.camara.x_acumulado = camara;
        this.calcularXCamara();
        this.redibujar();
      }
    }

    this.ocultar_panel = false;
    this.camara.x_acumulado = posicion_actual;
    this.posicionar_ejes_derecha = false;
    this.redibujar();
    return imagenes;
  };

  _proto.tieneInfoNueva = function tieneInfoNueva() {
    var datos = this.datos();

    for (var i = 0; i < datos.length; i++) {
      if (!datos[i].id) {
        return true;
      }

      for (var j = 0; j < datos[i].puntos.length; j++) {
        if (!datos[i].puntos[j].id) {
          return true;
        }
      }
    }

    return false;
  }
  /**
  Se lanza cuando se ejecuta una acción: clic con el puntero, goma o cualquier punto.
  Se pasa una función que acepta un argumento que es true si hay cambios o false si no los hay.
   */
  ;

  _proto.eventoCambio = function eventoCambio(funcion) {
    this.callbackCambio = funcion;
  };

  return Partograma;
}();
/**
Panel con opciones
 */


function _cargarImagenes2() {
  var lista = {};
  lista["borrar"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgMFSUnp+/Q/wAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAd0lEQVQ4y72USQ7AMAgDcf//Z/fQVKoUIGwqZzMQAxH5MVhJQgCGLpCJwkchC68xRWxYtAk44HupwyMUjVVRoVfFeJJmcWSXesGeZGBjIHMpX5gFRfT8NJgGRcyy86xeaPSkONmhC8166EK1KZf+yUWlDMYozI0bGpovBo/c3TsAAAAASUVORK5CYII=";
  lista["agregar"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgMFSYwDxEG+wAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAALElEQVQ4y2NgoDJgJELNf1L0MFHbhaMGDkIDGXEkDbLNG8FhOJr1Rg0cSAAAeOMDIriFXWYAAAAASUVORK5CYII=";
  lista["dilatacion"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAGlJREFUOE9jYBhxgBGfj7ksOP5jk/924gdOfTglcBkGswCXoVgNJGQYPkMxDCTWMFyGohhIqmHYDGXCFuiUiI0aSEnoQfTSNtnA3Eds8sGWWzBcSKyh2AwD6cVpIEgSl0txGUZ5DAxJEwAfjSQUqYl/awAAAABJRU5ErkJggg==";
  lista["plano"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAK9JREFUOE/VlNsNgCAMRa1xF5dwMEdwMJdwGqRGCLS9PIx+yA+hLYdeKKWhMOZ1d5b72Bay7GyDDgQLIARVwBpIZibBGbAXZmUbgU9hEjpJCWJNXlJm8gfz2nwsdlwZguwULJARlO+TemE16BgC0lnK7IkxgRag1fZT4P2KRZUo5v2yScrAKlZVi6UaZFbtpzgkDd3Hd82hIh0lNBTbV7oLfMkYIkHBoRpsCxTBeO8JvLtO5BAuRWIAAAAASUVORK5CYII=";
  lista["lcf"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABHNCSVQICAgIfAhkiAAAAK5JREFUOE+1lNENgCAMRMFt/HIKp3YKvxxHU2IN0OtRY+RHQ3uvd2BM6V77Op/6/vZZa7OI641lO8pedPXajJxFoUhrHKqzERTBRPPE8xpQdNbbnBdrVPCox1wAE7CaDjRAKSAhio7OGQIjUAQTnQtkUA8mmglF+bLnOhydo+cSAkcwTYCgBohgKmQ1HfLfhx2Zri5Yb3HIGrwb9zT//77QzXku+3SNFkVgoLpWay+GcHmIhTQSKwAAAABJRU5ErkJggg==";
  lista["posicion0"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgMFTAdVlbvWQAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAWElEQVQ4y81UywoAIAhT6f9/eV2DQnyGXp1jbiLR9GKlh8Ssm8yK8QEtWARsQzUZzllJBAotHASVXRwrQfpUJl0HXrZyFWlJymwRgg670OF9+Dl8e19zagOkbyDxhLmixAAAAABJRU5ErkJggg==";
  lista["posicion45"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNDhoBhfNUygAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAb0lEQVQ4y82UUQ7AIAhDaeP9r8wOILJWzTK+yQNtS4RXiK+rmpg3t00RuOxDsxkE6NRHY8BqmeyAx1AKH56OOBQbVeVb4BaUpt9eocMwcSpQ3s7xKMyKDXhpG7iKKsZ2oVP8qEZKzTLUp/zm4B7XA+uJGh1hR6FGAAAAAElFTkSuQmCC";
  lista["posicion90"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNDhgpgnCesgAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAWElEQVQ4y+2UQQoAIAgErf//ebt1U9dNgiBPQjpkNJo1xyBqIPS4ICR5CebBvRoaFt0KCiwbE1UY8277bHZ/mw98FAjFDlY5SUEQukHx+cScUlMIu7pgpVirDC3lucu+SQAAAABJRU5ErkJggg==";
  lista["posicion135"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNDhsBnOhliwAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAaklEQVQ4y9XUQQ7AIAgEwF3i/79Mz0ZUtnBouYoTVBD4evCw5g3GhPmbPB4qYwJbLGvAplzrxKIKS1gEljAF9OwprBMDgCE0c6aBaZ3TAMDHZaOLV7JtbPXlGYEUUb9NioJup4qFr4v4ZTy0sBocGhVQaAAAAABJRU5ErkJggg==";
  lista["posicion180"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNDhsnTuXgdgAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAWklEQVQ4y81U0RIAIARL5/9/ef1ACSvHq9kxY4zuIUYORG2YzIuJAT1YJGSDlQBLOg9Cg902Dt2GOdSwRGr8+drYepFAmG2D1W83cqazOmN/OT36OZS9rz6xAPi0H/cM+jNcAAAAAElFTkSuQmCC";
  lista["posicion225"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNDhwYt8JbjAAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAcElEQVQ4y82UUQrAMAhDjez+V86+BypGC51fpdhHatKa/b2Q7HNxNoQxWGc9n/JEGZrqWAEpXiWE+nQuGfQZmBFBmSm0heuhQhQwdkbiYt54AihBffiq2AVyC/VO+hVo5SrEKKH7QUh9OBBm2O2SFLwlPx8TMdVLDQAAAABJRU5ErkJggg==";
  lista["posicion270"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNDh0TOQuzRQAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAYUlEQVQ4y+2UwQoAIAhDV/T/v7zugeZGRIc8ynyIbQGHqxU0NGZCEIW+BbOgFDakeq8VykBTgq0QbjQAgC46YfvC/bQPP/BBoOpFZSYVZDG0Ni1HbiTpYJAOZqm5+sFaNQEs8iny8wPDaAAAAABJRU5ErkJggg==";
  lista["posicion315"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNDh0t+Gqu7gAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAbElEQVQ4y9WUSRLAIAgEhcr/v9y5R8Imh4SjSBfgjGt9PcTJcVi/wQjOzTvqdCbJ7vCAFEfZoHoAM/d4NZf8+mA6AXl2SBEmXl4HYKFs2rAISMcROgAj65QKTKwCippMGYGCjMh+P3Qs95+4Ac+yHxJoli9eAAAAAElFTkSuQmCC";
  lista["puntero"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNDjAKfUpBagAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAYElEQVQ4y9WUOw7AMAxCwer9r0z3KFJDYKk3D3l6jj8AIBSDC5ApcJZcbWAM5QeADcPIlsYjNgxt2zErUrPkoy+YYEK2Ik+rGY6hUiA3VroFMj0UYwCsOWRzl+Mb+K94AXxpDiBvOpLiAAAAAElFTkSuQmCC";
  lista["separador"] = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAMAAAAeCAYAAADpYKT6AAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH5QgNECsrjq0U1AAAAB1pVFh0Q29tbWVudAAAAAAAQ3JlYXRlZCB3aXRoIEdJTVBkLmUHAAAAHUlEQVQY02NgQAezZs36z8DAwMCELDjKoTYHBQAAN/0DBJgJiP8AAAAASUVORK5CYII=";
  var keys = Object.keys(lista);
  keys.forEach(function (elemento) {
    var img = new Image();
    img.src = lista[elemento];
    Partograma.imagenes[elemento] = img;
  });
}

function _crearEjes2() {
  _classPrivateFieldLooseBase(this, _ejeLCF)[_ejeLCF] = new Eje(this.contexto, "LCF", [180, 160, 140, 120, 100, 80], 4, new Margen(5, 0, this.margen_secciones.superior, 0), this.celda, 1);
  _classPrivateFieldLooseBase(this, _ejeDilatacion)[_ejeDilatacion] = new Eje(this.contexto, "Dilatación", [10, 9, 8, 7, 6, 5, 4, 3, 2, 1], 2, new Margen(this.calcularAnchoSecciones(), 0, this.margen_secciones.superior, 0), this.celda, 2);
  _classPrivateFieldLooseBase(this, _ejePlano)[_ejePlano] = new Eje(this.contexto, "Plano", ["Alta", "Apoyada", "I", "II", "III", "IV"], 4, new Margen(this.calcularAnchoSecciones() + 60, 0, this.margen_secciones.superior, 0), this.celda, 1);
}

function _inicializarCanvas2() {
  this.canvas = document.getElementById(this.id_canvas);
  this.canvas.width = this.canvas.parentNode.clientWidth - 12;
  this.canvas.height = "600";
  this.canvas.style.borderStyle = "solid";
  this.canvas.style.borderWidth = "1px";
  this.canvas.style.borderColor = "black";
  this.canvas.style.cursor = "none";
  this.contexto = this.canvas.getContext('2d');
}

function _crearPanel2() {
  var celdaPanel = new Celda(470, 50);
  var margenPanel = new Margen(0, 10, 20, 0);
  this.panel = new Panel(this.contexto, celdaPanel, margenPanel);
  var separador = new Separador(this.contexto);
  var op1 = new OpcionPlano(this.contexto);
  var op2 = new OpcionDilatacionCervical(this.contexto);
  var op3 = new OpcionLCF(this.contexto);
  var op4 = new OpcionAgregarSeccion(this.contexto);
  var op5 = new OpcionBorrar(this.contexto);
  var op6 = new OpcionPosicion(this.contexto, "0", "0");
  var op7 = new OpcionPosicion(this.contexto, "45", "45");
  var op8 = new OpcionPosicion(this.contexto, "90", "90");
  var op9 = new OpcionPosicion(this.contexto, "135", "135");
  var op10 = new OpcionPosicion(this.contexto, "180", "180");
  var op11 = new OpcionPosicion(this.contexto, "225", "225");
  var op12 = new OpcionPosicion(this.contexto, "270", "270");
  var op13 = new OpcionPosicion(this.contexto, "315", "315");
  var op14 = new OpcionPuntero(this.contexto); //símbolos del gráfico

  this.panel.agregarOpcion(op14);
  this.panel.agregarOpcion(separador);
  this.panel.agregarOpcion(op1);
  this.panel.agregarOpcion(op2);
  this.panel.agregarOpcion(op3);
  this.panel.agregarOpcion(separador); //símbolos del gráfico para la posición

  this.panel.agregarOpcion(op6);
  this.panel.agregarOpcion(op7);
  this.panel.agregarOpcion(op8);
  this.panel.agregarOpcion(op9);
  this.panel.agregarOpcion(op10);
  this.panel.agregarOpcion(op11);
  this.panel.agregarOpcion(op12);
  this.panel.agregarOpcion(op13);
  this.panel.agregarOpcion(separador); //opciones agregar - borrar

  this.panel.agregarOpcion(op4);
  this.panel.agregarOpcion(op5);
}

function _crearRejillaInicial2() {
  this.celda = new Celda(20, 20);
  var margen = new Margen(this.margen_secciones.izquierdo, 0, this.margen_secciones.superior, 0);
  var seccion = new Seccion(this.contexto, "00:00", margen, this.celda);

  _classPrivateFieldLooseBase(this, _secciones)[_secciones].push(seccion);

  this.ancho_seccion = this.celda.ancho * 12;
}

function _eventos2() {
  var _this4 = this;

  //eventos
  this.canvas.addEventListener("mousedown", function (event) {
    _this4.arrastrando = 1;
    var r = Partograma.windowToCanvas(_this4.canvas, event.clientX, event.clientY);

    if (Panel.opcion_seleccionada && Panel.opcion_seleccionada instanceof OpcionBorrar) {
      for (var i = 0; i < _classPrivateFieldLooseBase(_this4, _secciones)[_secciones].length; i++) {
        _classPrivateFieldLooseBase(_this4, _secciones)[_secciones][i].borrarCuadrante(r.x, r.y);
      }
    } else if (!(Panel.opcion_seleccionada instanceof OpcionPuntero)) {
      for (var i = 0; i < _classPrivateFieldLooseBase(_this4, _secciones)[_secciones].length; i++) {
        _classPrivateFieldLooseBase(_this4, _secciones)[_secciones][i].agregarCuadrante(r.x, r.y, Panel.opcion_seleccionada);
      }
    }

    if (_this4.callbackCambio) {
      _this4.callbackCambio(_this4.tieneInfoNueva());
    }

    var p = Partograma.instancia();
    p.redibujar(); //para ver el texto de la selección
  });
  this.canvas.addEventListener("mousemove", function (event) {
    if (_this4.arrastrando == 2) {
      if (typeof event.movementX !== 'undefined') {
        _this4.camara.actualizar(event.movementX, event.movementY);
      } else {
        if (!_this4.eventoMouseMoveAnterior) {
          _this4.eventoMouseMoveAnterior = event;
        } else {
          if (Math.abs(event.clientX - _this4.eventoMouseMoveAnterior.clientX) > 50) {
            _this4.eventoMouseMoveAnterior = null;
          } else {
            _this4.camara.actualizar(event.clientX - _this4.eventoMouseMoveAnterior.clientX, event.clientY - _this4.eventoMouseMoveAnterior.clientY);

            _this4.eventoMouseMoveAnterior = event;
          }
        }
      }
    }

    if (_this4.arrastrando == 1) {
      _this4.arrastrando = 2;
    }

    var r = Partograma.windowToCanvas(_this4.canvas, event.clientX, event.clientY);

    _this4.redibujar(); //para limpiar la guía y el movimiento de la opción


    for (var i = 0; i < _classPrivateFieldLooseBase(_this4, _secciones)[_secciones].length; i++) {
      _classPrivateFieldLooseBase(_this4, _secciones)[_secciones][i].dibujarCuadranteActual(r.x, r.y);
    }

    if (Panel.opcion_seleccionada) {
      if (!(Panel.opcion_seleccionada instanceof OpcionPuntero)) {
        _this4.contexto.drawImage(Panel.opcion_seleccionada.icono(), r.x - Panel.opcion_seleccionada.dimension.ancho / 2, r.y - Panel.opcion_seleccionada.dimension.alto / 2);
      } else {
        _this4.contexto.drawImage(Panel.opcion_seleccionada.icono(), r.x, r.y);
      }
    }

    var s = Info.verSeccion(r.x, r.y);

    if (s) {
      _this4.info.ponerUsuario(s.usuario_responsable);
    } else {
      _this4.info.ponerUsuario(null);
    }

    var c = Info.verCuadrante(r.x, r.y);

    if (c) {
      _this4.info.ponerPunto(c.usuario_responsable);
    } else {
      _this4.info.ponerPunto(null);
    }
  });
  this.canvas.addEventListener("mouseup", function (event) {
    _this4.arrastrando = 0;
  });
  this.canvas.addEventListener("mouseout", function (event) {
    _this4.arrastrando = 0;
  });
  window.addEventListener("resize", function () {
    Partograma.instancia().redibujar();
  });
}

function _buscarSeccion2(id) {
  for (var i = 0; i < _classPrivateFieldLooseBase(this, _secciones)[_secciones].length; i++) {
    if (_classPrivateFieldLooseBase(this, _secciones)[_secciones][i].id == id) {
      return _classPrivateFieldLooseBase(this, _secciones)[_secciones][i];
    }
  }

  return null;
}

Object.defineProperty(Partograma, _cargarImagenes, {
  value: _cargarImagenes2
});
Object.defineProperty(Partograma, _partograma, {
  writable: true,
  value: void 0
});
Partograma.imagenes = {};

var Panel = /*#__PURE__*/function () {
  "use strict";

  function Panel(contexto, celda, margen) {
    this.x = void 0;
    this.y = void 0;
    this.celda = celda;
    this.margen = margen;
    this.contexto = contexto;
    this.opciones = [];
    this.x = this.contexto.canvas.width - this.celda.ancho - this.margen.derecho;
    this.y = this.margen.superior;
    Panel.puntero = new OpcionPuntero(contexto);
    Panel.opcion_seleccionada = Panel.puntero;
  }

  var _proto2 = Panel.prototype;

  _proto2.dibujar = function dibujar() {
    this.x = this.contexto.canvas.width - this.celda.ancho - this.margen.derecho;
    this.y = this.margen.superior;
    this.dibujarSeleccion();
    var estilo_original = this.contexto.fillStyle;
    this.contexto.fillStyle = '#FFFFFF';
    this.contexto.fillRect(this.x, this.y, this.celda.ancho, this.celda.alto);
    this.contexto.strokeRect(this.x, this.y, this.celda.ancho, this.celda.alto);
    this.contexto.fillStyle = estilo_original;
    var x_actual = 0;

    for (var i = 0; i < this.opciones.length; i++) {
      var x = 0;

      if (i === 0) {
        x = this.x + 10; // espacio antes de la primera opción
      } else {
        x = this.x + (x_actual - this.x) + 10;
      }

      x_actual = x + this.opciones[i].dimension.ancho;
      var y = this.y + (this.celda.alto / 2 - this.opciones[i].dimension.alto / 2);
      var icono = this.opciones[i].icono();

      if (icono) {
        this.contexto.drawImage(icono, x, y);
        this.opciones[i].x = x;
        this.opciones[i].y = y;
      }
    }
  };

  _proto2.agregarOpcion = function agregarOpcion(opcion) {
    this.opciones.push(opcion);
  };

  _proto2.dibujarSeleccion = function dibujarSeleccion() {
    this.contexto.fillText("Selección: " + (Panel.opcion_seleccionada ? Panel.opcion_seleccionada.texto : "Ninguna"), this.x, this.y - 5);
  };

  return Panel;
}();
/**
Clase base para los botones del panel.
Se debe extender para cada opción.
 */


Panel.opcion_seleccionada = null;
Panel.puntero = null;

var _evento = /*#__PURE__*/_classPrivateFieldLooseKey("evento");

var Opcion = /*#__PURE__*/function () {
  "use strict";

  /**
  @param {object} contexto Contexto 2d del canvas
  @param {string} texto Texto de la opción
   */
  function Opcion(contexto, texto, nombre) {
    Object.defineProperty(this, _evento, {
      value: _evento2
    });
    this.dimension = void 0;
    this.cliqueable = void 0;
    this.texto = texto;
    this.nombre = nombre;
    this.contexto = contexto;
    this.dimension = new Celda(20, 20);
    this.cliqueable = true;

    _classPrivateFieldLooseBase(this, _evento)[_evento]();
  }

  var _proto3 = Opcion.prototype;

  _proto3.icono = function icono() {
    throw new Error("Debe implementar el método icono();");
  };

  return Opcion;
}();

function _evento2() {
  var _this5 = this;

  if (!this.cliqueable) {
    return;
  }

  this.contexto.canvas.addEventListener("mousedown", function (event) {
    var r = Partograma.windowToCanvas(_this5.contexto.canvas, event.clientX, event.clientY);

    if (_this5.x < r.x && _this5.y < r.y && _this5.x + _this5.dimension.ancho > r.x && _this5.y + _this5.dimension.alto > r.y) {
      if (Panel.opcion_seleccionada == _this5) {
        Panel.opcion_seleccionada = Panel.puntero;
      } else {
        Panel.opcion_seleccionada = _this5;
      }
    }
  });
}

var OpcionPlano = /*#__PURE__*/function (_Opcion) {
  "use strict";

  _inheritsLoose(OpcionPlano, _Opcion);

  function OpcionPlano(contexto) {
    return _Opcion.call(this, contexto, "Plano", "plano") || this;
  }

  var _proto4 = OpcionPlano.prototype;

  _proto4.icono = function icono() {
    return Partograma.imagenes["plano"];
  };

  return OpcionPlano;
}(Opcion);

var OpcionDilatacionCervical = /*#__PURE__*/function (_Opcion2) {
  "use strict";

  _inheritsLoose(OpcionDilatacionCervical, _Opcion2);

  function OpcionDilatacionCervical(contexto) {
    return _Opcion2.call(this, contexto, "Dilatación cervical", "dilatacion_cervical") || this;
  }

  var _proto5 = OpcionDilatacionCervical.prototype;

  _proto5.icono = function icono() {
    return Partograma.imagenes["dilatacion"];
  };

  return OpcionDilatacionCervical;
}(Opcion);

var OpcionLCF = /*#__PURE__*/function (_Opcion3) {
  "use strict";

  _inheritsLoose(OpcionLCF, _Opcion3);

  function OpcionLCF(contexto) {
    return _Opcion3.call(this, contexto, "LCF", "lcf") || this;
  }

  var _proto6 = OpcionLCF.prototype;

  _proto6.icono = function icono() {
    return Partograma.imagenes["lcf"];
  };

  return OpcionLCF;
}(Opcion);

var _agregarSeccion = /*#__PURE__*/_classPrivateFieldLooseKey("agregarSeccion");

var OpcionAgregarSeccion = /*#__PURE__*/function (_Opcion4) {
  "use strict";

  _inheritsLoose(OpcionAgregarSeccion, _Opcion4);

  function OpcionAgregarSeccion(contexto) {
    var _this;

    _this = _Opcion4.call(this, contexto, "Agregar hora", null) || this;
    Object.defineProperty(_assertThisInitialized(_this), _agregarSeccion, {
      value: _agregarSeccion2
    });

    _classPrivateFieldLooseBase(_assertThisInitialized(_this), _agregarSeccion)[_agregarSeccion]();

    return _this;
  }

  var _proto7 = OpcionAgregarSeccion.prototype;

  _proto7.icono = function icono() {
    return Partograma.imagenes["agregar"];
  };

  return OpcionAgregarSeccion;
}(Opcion);

function _agregarSeccion2() {
  this.contexto.canvas.addEventListener("mousedown", function (event) {
    if (!(Panel.opcion_seleccionada instanceof OpcionAgregarSeccion)) {
      return;
    }

    var hora = Partograma.prompt("Ingrese una hora (un número entre 0 y 23)");
    hora.then(function (valor) {
      if (valor) {
        var v = valor < 10 ? "0" + valor : valor;
        var p = Partograma.instancia();
        var margenNuevo = new Margen(p.calcularAnchoSecciones(), 0, p.margen_secciones.superior, 0);
        var nuevaSeccion = new Seccion(p.contexto, v + ":00", margenNuevo, p.celda);
        p.agregarSeccion(nuevaSeccion);
      }
    }).catch(function (valor) {
      Partograma.alert("Debe poner un número entre 0 y 23 (" + valor + ")");
    });
    Panel.opcion_seleccionada = Panel.puntero; //p.redibujar();
  });
}

var OpcionBorrar = /*#__PURE__*/function (_Opcion5) {
  "use strict";

  _inheritsLoose(OpcionBorrar, _Opcion5);

  function OpcionBorrar(contexto) {
    return _Opcion5.call(this, contexto, "Borrar", null) || this;
  }

  var _proto8 = OpcionBorrar.prototype;

  _proto8.icono = function icono() {
    return Partograma.imagenes["borrar"];
  };

  return OpcionBorrar;
}(Opcion);

var OpcionPosicion = /*#__PURE__*/function (_Opcion6) {
  "use strict";

  _inheritsLoose(OpcionPosicion, _Opcion6);

  function OpcionPosicion(contexto, nombre, rotacion) {
    var _this2;

    _this2 = _Opcion6.call(this, contexto, nombre, "posicion" + (!rotacion ? 0 : rotacion)) || this;
    _this2.rotacion = void 0;
    _this2.rotacion = rotacion;
    return _this2;
  }

  var _proto9 = OpcionPosicion.prototype;

  _proto9.icono = function icono() {
    return Partograma.imagenes["posicion" + this.rotacion];
  };

  return OpcionPosicion;
}(Opcion);

var OpcionPuntero = /*#__PURE__*/function (_Opcion7) {
  "use strict";

  _inheritsLoose(OpcionPuntero, _Opcion7);

  function OpcionPuntero(contexto) {
    return _Opcion7.call(this, contexto, "Puntero", null) || this;
  }

  var _proto10 = OpcionPuntero.prototype;

  _proto10.icono = function icono() {
    return Partograma.imagenes["puntero"];
  };

  return OpcionPuntero;
}(Opcion);

var Separador = /*#__PURE__*/function (_Opcion8) {
  "use strict";

  _inheritsLoose(Separador, _Opcion8);

  function Separador(contexto) {
    var _this3;

    _this3 = _Opcion8.call(this, contexto, "", null) || this;
    _this3.cliqueable = false;
    _this3.dimension.alto = 30;
    _this3.dimension.ancho = 3;
    return _this3;
  }

  var _proto11 = Separador.prototype;

  _proto11.icono = function icono() {
    return Partograma.imagenes["separador"];
  };

  return Separador;
}(Opcion);
/**
Representa a una rejilla de una hora de duración
 */


var _existeCuadrante = /*#__PURE__*/_classPrivateFieldLooseKey("existeCuadrante");

var Seccion = /*#__PURE__*/function () {
  "use strict";

  /**
  @param {object} contexto Contexto del canvas
  @param {string} hora Hora a la que corresponde esta sección
  @param {Margen} margen Margen de la sección
  @param {Celda} celda Tamaño de las celdas de esta sección
  */
  function Seccion(contexto, hora, margen, celda) {
    Object.defineProperty(this, _existeCuadrante, {
      value: _existeCuadrante2
    });
    this.celda = celda;
    this.hora = hora;
    this.contexto = contexto;
    this.margen = margen;
    this.cuadrantes = [];
    this.ancho_seccion = 0;
    this.alto_seccion = 0;

    for (var _i = 0; _i < 12; _i++) {
      this.ancho_seccion += this.celda.ancho;
    }

    for (var _i = 0; _i < Seccion.filas; _i++) {
      this.alto_seccion += this.celda.alto;
    }
  }

  var _proto12 = Seccion.prototype;

  _proto12.dibujarRejilla = function dibujarRejilla() {
    //líneas verticales
    var ancho_seccion = 0;

    for (var i = 0; i <= 12; i++) {
      if (i == 12) {
        this.contexto.lineWidth = 2;
      } else {
        ancho_seccion += this.celda.ancho;
      }

      this.contexto.beginPath();
      this.contexto.moveTo(this.margen.izquierdo + i * this.celda.ancho, this.margen.superior);
      this.contexto.lineTo(this.margen.izquierdo + i * this.celda.ancho, this.margen.superior + this.celda.alto * Seccion.filas);
      this.contexto.stroke();
    }

    this.ancho_seccion = ancho_seccion;
    this.contexto.lineWidth = 1; //líneas horizontales

    for (var i = 0; i <= Seccion.filas; i++) {
      if (i == 0) {
        this.contexto.lineWidth = 2;
      } else {
        this.contexto.lineWidth = 1;
      }

      this.contexto.beginPath();
      this.contexto.moveTo(this.margen.izquierdo, i * this.celda.alto + this.margen.superior);
      this.contexto.lineTo(this.margen.izquierdo + this.celda.ancho * 12, i * this.celda.alto + this.margen.superior);
      this.contexto.stroke();
    }
  };

  _proto12.dibujarHora = function dibujarHora() {
    this.contexto.fillText(this.hora, this.margen.izquierdo, this.margen.superior - 5);
  };

  _proto12.dibujarMinutos = function dibujarMinutos() {
    this.contexto.fillText("15", this.margen.izquierdo + this.celda.ancho * 3 - 4, this.margen.superior - 5);
    this.contexto.fillText("30", this.margen.izquierdo + this.celda.ancho * 6 - 4, this.margen.superior - 5);
    this.contexto.fillText("45", this.margen.izquierdo + this.celda.ancho * 9 - 4, this.margen.superior - 5);
  };

  _proto12.dibujarValores = function dibujarValores() {
    for (var i = 0; i < this.cuadrantes.length; i++) {
      var centrox = 0;
      var centroy = 0;
      centrox = this.celda.ancho / 2 - 10;
      centroy = this.celda.alto / 2 - 10;
      var a = this.cuadrantes[i].x * this.celda.ancho + this.margen.izquierdo - this.celda.ancho / 2 + centrox;
      var b = this.cuadrantes[i].y * this.celda.alto + this.margen.superior - this.celda.alto / 2 + centroy;
      var icono = this.cuadrantes[i].tipo.icono();

      if (icono) {
        this.contexto.drawImage(icono, a, b);
      }
    }
  };

  _proto12.dibujar = function dibujar() {
    this.dibujarRejilla();
    this.dibujarHora();
    this.dibujarMinutos();
    this.dibujarValores();
  };

  _proto12.obtenerUbicacionCuadrante = function obtenerUbicacionCuadrante(cuadrante) {
    for (var i = 0; i < this.cuadrantes.length; i++) {
      if (cuadrante.id == this.cuadrantes[i].id) {
        var centrox = 0;
        var centroy = 0;
        centrox = this.celda.ancho / 2 - 10;
        centroy = this.celda.alto / 2 - 10;
        var a = this.cuadrantes[i].x * this.celda.ancho + this.margen.izquierdo - this.celda.ancho / 2 + centrox;
        var b = this.cuadrantes[i].y * this.celda.alto + this.margen.superior - this.celda.alto / 2 + centroy;
        return {
          x: a,
          y: b,
          ancho: this.celda.ancho,
          alto: this.celda.alto
        };
      }
    }

    return null;
  };

  _proto12.dibujarCuadranteActual = function dibujarCuadranteActual(x, y) {
    var p = Partograma.instancia();
    p.calcularXCamara();
    var punto = this.canvasAPunto(x - p.camara.x_acumulado, y);

    if (!punto) {
      return;
    }

    var a = punto.x * this.celda.ancho + this.margen.izquierdo - this.celda.ancho / 2;
    var b = punto.y * this.celda.alto + this.margen.superior - this.celda.alto / 2;
    this.contexto.setLineDash([2, 2]);
    this.contexto.lineDashOffset = 1;
    this.contexto.strokeRect(a + p.camara.x_acumulado, b, this.celda.ancho, this.celda.alto);
    this.contexto.setLineDash([]);
  };

  _proto12.canvasAPunto = function canvasAPunto(x, y) {
    var a = Math.round((x - this.margen.izquierdo) / this.celda.ancho);
    var b = Math.round((y - this.margen.superior) / this.celda.alto); //cambiar b

    if (a < 0 || a >= 12 || b <= 0 || b > Seccion.filas) {
      return null;
    }

    return {
      x: a,
      y: b
    };
  };

  _proto12.agregarPunto = function agregarPunto(punto) {
    this.cuadrantes.push(punto);
  };

  _proto12.agregarCuadrante = function agregarCuadrante(x, y, opcion) {
    if (!opcion) {
      return;
    }

    var p = Partograma.instancia();
    p.calcularXCamara();
    var punto = this.canvasAPunto(x - p.camara.x_acumulado, y);

    if (!punto) {
      return;
    }

    if (_classPrivateFieldLooseBase(this, _existeCuadrante)[_existeCuadrante](punto.x, punto.y, opcion)) {
      Partograma.alert("El valor que está intentando ingresar ya existe.");
      return;
    }

    var cuadrante = new Cuadrante(punto.x, punto.y);
    cuadrante.tipo = opcion;
    this.cuadrantes.push(cuadrante);
  };

  _proto12.borrarCuadrante = function borrarCuadrante(x, y) {
    var p = Partograma.instancia();
    p.calcularXCamara();
    var punto = this.canvasAPunto(x - p.camara.x_acumulado, y);

    if (!punto) {
      return;
    }

    var encontrados = [];
    var indice_encontrados = [];

    for (var i = 0; i < this.cuadrantes.length; i++) {
      if (this.cuadrantes[i].x == punto.x && this.cuadrantes[i].y == punto.y) {
        encontrados.push(this.cuadrantes[i]);
        indice_encontrados.push(i);
      }
    }

    var indice = null;

    for (var i = 0; i < indice_encontrados.length; i++) {
      if (!encontrados[i].id) {
        indice = indice_encontrados[i];
        break;
      }
    }

    if (indice === null && encontrados.length > 0) {
      Partograma.alert("No puede borrar este punto.");
      return;
    }

    if (indice !== null) {
      this.cuadrantes.splice(indice, 1);
    }
  };

  return Seccion;
}();
/**
Representa las dimensiones de una celda en la rejilla
 */


function _existeCuadrante2(x, y, opcion) {
  for (var i = 0; i < this.cuadrantes.length; i++) {
    if (this.cuadrantes[i].tipo.nombre == opcion.nombre && this.cuadrantes[i].x == x && this.cuadrantes[i].y == y) {
      return true;
    }
  }

  return false;
}

Seccion.filas = 21;

var Celda = function Celda(ancho, alto) {
  "use strict";

  this.ancho = ancho;
  this.alto = alto;
};
/**
Representa márgenes
 */


var Margen = function Margen(izq, der, sup, inf) {
  "use strict";

  this.izquierdo = izq;
  this.derecho = der;
  this.superior = sup;
  this.inferior = inf;
};
/**
Clase para guardar los datos
La coordenada «a» se corresponde con «x».
La coordenada «b» se corresponde con «y».
 */


var Cuadrante = function Cuadrante(a, b) {
  "use strict";

  this.x = Math.abs(a);
  this.y = Math.abs(b);
};

var _calcularAncho = /*#__PURE__*/_classPrivateFieldLooseKey("calcularAncho");

var Eje = /*#__PURE__*/function () {
  "use strict";

  /**
  @param {object} contexto Contexto del canvas
  @param {string} nombre Nombre del eje. Se dibujará antes del margen superior
  @param {array} valores Arreglo con los valores del eje
  @param {int} contador Cada cuánto se dibuja el valor
  @param {Margen} margen Margen del eje. Los importantes son el izquierdo y el superior
  @param {Celda} celda Celda usada en las secciones
  @param {int} celda_inicial Numero de celda de la cual empezar a dibujar
   */
  function Eje(contexto, nombre, valores, contador, margen, celda, celda_inicial) {
    Object.defineProperty(this, _calcularAncho, {
      value: _calcularAncho2
    });
    this.valores = valores;
    this.contador = contador;
    this.contexto = contexto;
    this.nombre = nombre;
    this.margen = margen;
    this.celda = celda;
    this.celda_inicial = celda_inicial;
    this.ancho = 0;

    _classPrivateFieldLooseBase(this, _calcularAncho)[_calcularAncho]();
  }

  var _proto13 = Eje.prototype;

  _proto13.dibujar = function dibujar() {
    var y = this.margen.superior;
    var contador = this.celda_inicial;
    var indice = 0;
    var fs = this.contexto.fillStyle;
    this.contexto.fillStyle = 'rgba(255,255,255,1)';
    this.contexto.fillRect(this.margen.izquierdo - 10, y - 10, this.ancho + 20, this.celda.alto * Seccion.filas + 20);
    this.contexto.fillStyle = fs;

    for (var i = 0; i <= Seccion.filas; i++) {
      if (i == contador) {
        this.contexto.fillText(this.valores[indice], this.margen.izquierdo, y + 4);
        contador += this.contador;
        indice++;
      }

      y += this.celda.alto;
    }

    this.contexto.fillText(this.nombre, this.margen.izquierdo, this.margen.superior);
  };

  return Eje;
}();

function _calcularAncho2() {
  this.ancho = this.contexto.measureText(this.nombre).width;

  for (var i = 0; i < this.valores.length; i++) {
    this.ancho = Math.max(this.ancho, this.contexto.measureText(this.valores[i]).width);
  }
}

var Camara = /*#__PURE__*/function () {
  "use strict";

  function Camara() {
    //x,y: Delta del movimiento
    this.x = 0;
    this.y = 0; //x_actual,y_actual: movimiento acumulado

    this.x_acumulado = 0;
    this.y_acumulado = 0;
  }

  var _proto14 = Camara.prototype;

  _proto14.actualizar = function actualizar(x, y) {
    this.x = x;
    this.y = y;
    this.x_acumulado += x;
    this.y_acumulado += y;
  };

  _proto14.reiniciarAcumulados = function reiniciarAcumulados() {
    this.x_acumulado = 0;
    this.y_acumulado = 0;
  };

  _proto14.revisarAcumulados = function revisarAcumulados(xMin, xMax, yMin, yMax) {
    if (this.x_acumulado < xMin) {
      this.x_acumulado = xMin;
    }

    if (this.x_acumulado > xMax) {
      this.x_acumulado = xMax;
    }

    if (this.y_acumulado < yMin) {
      this.y_acumulado = yMin;
    }

    if (this.y_acumulado > yMax) {
      this.y_acumulado = yMax;
    }
  };

  return Camara;
}();

var Info = /*#__PURE__*/function () {
  "use strict";

  function Info(contexto) {
    this.usuario = void 0;
    this.punto = void 0;
    this.origen = void 0;
    this.textoUsuario = void 0;
    this.textoPunto = void 0;
    this.contexto = contexto;
    this.origen = new Margen(10, 0, 20, 0);
    this.textoUsuario = "Hora creada por: ";
    this.textoPunto = "Punto creado por: ";
  }

  var _proto15 = Info.prototype;

  _proto15.ponerUsuario = function ponerUsuario(nombre_usuario) {
    this.usuario = nombre_usuario;
  };

  _proto15.ponerPunto = function ponerPunto(opcion) {
    this.punto = opcion;
  }
  /**
  Devuelve la sección que está bajo el cursor
   */
  ;

  Info.verSeccion = function verSeccion(x_actual, y_actual) {
    var p = Partograma.instancia();
    p.calcularXCamara();
    var secciones = p.traerSecciones();

    for (var i = 0; i < secciones.length; i++) {
      if (Partograma.puntoDentro(x_actual, y_actual, secciones[i].margen.izquierdo + p.camara.x_acumulado, secciones[i].margen.superior, secciones[i].ancho_seccion, secciones[i].alto_seccion)) {
        return secciones[i];
      }
    }

    return null;
  }
  /**
  Devuelve el punto que está bajo el cursor
   */
  ;

  Info.verCuadrante = function verCuadrante(x_actual, y_actual) {
    var p = Partograma.instancia();
    p.calcularXCamara();
    var secciones = p.traerSecciones();

    for (var i = 0; i < secciones.length; i++) {
      for (var j = 0; j < secciones[i].cuadrantes.length; j++) {
        var ubicacion_cuadrante = secciones[i].obtenerUbicacionCuadrante(secciones[i].cuadrantes[j]);

        if (ubicacion_cuadrante) {
          if (Partograma.puntoDentro(x_actual, y_actual, ubicacion_cuadrante.x + p.camara.x_acumulado, ubicacion_cuadrante.y, ubicacion_cuadrante.ancho, ubicacion_cuadrante.alto)) {
            return secciones[i].cuadrantes[j];
          }
        }
      }
    }

    return null;
  }
  /**
  Calcula la posición donde aparecerá la información con respecto al panel.
  En resoluciones pequeñas ( < 1400) la información puede chocar con el panel
   */
  ;

  _proto15.calcularPosicion = function calcularPosicion(panel) {
    var extremoDerechoUsuario = this.contexto.measureText(this.textoUsuario + this.usuario).width + this.origen.izquierdo;
    var extremoDerechoPunto = this.contexto.measureText(this.textoPunto + this.punto).width + this.origen.izquierdo;
    var max = Math.max(extremoDerechoUsuario, extremoDerechoPunto);

    if (max >= panel.x) {
      this.origen.superior = this.contexto.canvas.height - 40;
    } else {
      this.origen.superior = 20;
    }
  };

  _proto15.dibujar = function dibujar() {
    if (this.usuario) {
      this.contexto.fillText(this.textoUsuario + this.usuario, this.origen.izquierdo, this.origen.superior);
    }

    if (this.punto) {
      this.contexto.fillText(this.textoPunto + this.punto, this.origen.izquierdo, this.origen.superior + 15);
    }
  };

  return Info;
}();