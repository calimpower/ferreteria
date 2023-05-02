<?php
include_once'../Controlador/lisProducto.php';
?>
<style type="text/css">
    .Rojo{
        color: red;
        font-weight: bold;
    }
    .Azul{
        color: blue;
        font-weight: bold; 
    }
    .Verde{
      color: green;
        font-weight: bold; 
    }
    .Naranja{
      color: #CA7208;
        font-weight: bold; 
    }
</style>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Productos</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#agregarProducto">Nuevo producto</button>
            <a type="button" class="btn btn-sm btn-outline-secondary" href="../Reportes/reportProducto.php" target="_blank">Imprimir</a>
          </div>
        </div>
      </div>
      <!--<canvas class="my-4 w-100" id="myChart" width="900" height="380"></canvas>-->
      
    <div class="row">
      <div class="col-sm-9">
        <h6 class="h6" style="color:grey">Todos los productos</h6>
      </div>
      <div class="col-sm-1">
      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Buscar:
      </div>
      <div class="col-sm-2">
        <input type="text" id="codigo" class="form-control form-control-sm" placeholder=""><br>
      </div>
    </div>

      <div class="table-responsive">
        <table class="table table-sm table-bordered table-hover text-center">
        <thead style="background: #DC823B">
            <tr>
              <th>Producto</th>
              <th>Categoria</th>
              <th>Lote</th>
              <th>Stock</th>
              <th>Precio</th>
              <th>Proveedor</th>
              <th>Presentación</th>
              <th>Observación</th>
              <th>Foto</th>
              <th>Acción</th>
            </tr>
          </thead>
          <tbody id="table">
            <?php
              while($d=mysqli_fetch_row($productos)){
                if($d[3]>=$d[16]){
                  $colorStock="Azul";
                }
                else{
                  $colorStock="Rojo";
                }
                //Fecha vencimiento
                $fechaP = new DateTime($d[6]);
                $hoy = new DateTime();
                $actualidad = $hoy->diff($fechaP);
                $año=$actualidad->y;
                $mes=$actualidad->m;
                $dia=$actualidad->d;

                $ConvAñoMes=$año*365;
                $ConvMesDia=$ConvAñoMes+round(($mes/12)*365);
                $totDia=$ConvMesDia+$dia;

                if($totDia>=60){
                  $colorFecha="Verde";
                }
                elseif($totDia>=30){
                  $colorFecha="Naranja";
                }
                else{
                  $colorFecha="Rojo";
                }

                //-----------------
                echo"<tr><td>$d[1]<td>$d[11]<td>$d[15]<td class='$colorStock'>$d[3]<td>".number_format($d[4])."<td>$d[12]<td>$d[9]<td>$d[17]<td>";

                $datos=$d[0].'||'.$d[1].'||'.$d[2].'||'.$d[4].'||'.$d[5].'||'.$d[3].'||'.$d[7].
                  '||'.$d[6].'||'.$d[13].'||'.$d[16].'||'.$d[8].'||'.$d[9].'||'.$d[10].'||'.$d[14].
                  '||'.$d[15].'||'.$d[17].'||'.$d[18].'||'.$d[19].'||'.$d[11];
            ?>
            <button href="#" onclick="formularioFotoProducto('<?=$datos?>')" data-bs-toggle="modal" data-bs-target="#modificarFotoProducto"><img src="../Imagenes/Productos/<?=$d[10]?>" height="42px" width="40px"></button><td>
            <div class="dropdown">
              <button style="background: #9A8C82" class="btn btn-light dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                Acción
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <li><button class="dropdown-item" href="#" onclick="formularioProducto('<?=$datos?>')" data-bs-toggle="modal" data-bs-target="#modificarProducto">
                  <img src="../Imagenes/Herramientas/modificar.png" width="16px"> Modificar
                </button></li>
                <li><button class="dropdown-item" href="#" onclick="preguntarProducto('<?=$d[0]?>')">
                  <img src="../Imagenes/Herramientas/eliminar.png" width="16px"> Eliminar
                </button></li>
              </ul>
            </div>
            <?php
              }              
            ?>
          </tbody>
        </table>
      <br>

        <form name="frm" method="post" action="../Controlador/productExcel.php" enctype="multipart/form-data" accept-charset="UTF-8" id="LayoutGrid1">
        <div class="row">
        <div class="col-sm-6"></div>
          <div class="col-sm-4">
            <div id="file_archivo">
              <input class="form-control form-control-sm" type="file" name="archivo">
            </div>
          </div>
          <div class="col-sm-2">
            <input type="submit" id="btn_cargar" name="importar" value="Importar" class="btn btn-secondary btn-sm">
            <input type="submit" id="btn_exportar" name="exportar" value="Exportar" style="background: #DC823B;" class="btn btn-sm"> 
          </div>
        </div>
        </form>
<br>
    </div>
</main>

<script>

  $(document).ready(function () {
                  $("#file_archivo :file").on('change', function () {
                      var input = $(this).parents('.input-group').find(':text');
                      input.val($(this).val());
                  });
              });
   $(document).ready(function(){
          $("#codigo").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#table tr").filter(function() {
              $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
          });
        });

  function modificarProducto(){
    datos= new FormData($("#formProducto2")[0]);
    $.ajax({
        data:datos,
        url: "../Controlador/editarProducto.php",
        type:"post",
        contentType: false,
        processData: false,
        success:function(r){
            if(r==0){
                $('#panel').load("productos.php");
                alertify.success("Producto editado con exito :)");
            }
            else{
                alertify.error("Falló el servidor");
            }
        }
    });
};
function modificarFoto(){
    datos= new FormData($("#formProductoFoto")[0]);
    $.ajax({
        data:datos,
        url: "../Controlador/editarFotoProducto.php",
        type:"post",
        contentType: false,
        processData: false,
        success:function(r){
            if(r==0){
                $('#panel').load("productos.php");
                alertify.success("Foto editada con exito :)");
            }
            else{
                alertify.error("Falló el servidor");
            }
        }
    });
};
function formularioFotoProducto(producto) {
    d = producto.split('||');
    $('#id_producto').val(d[0]);
    //$('#productoFoto').val(d[12]);
}

  function formularioProducto(producto) {
    d = producto.split('||');
    $('#codiProducto').val(d[0]);
    $('#nombreProducto').val(d[1]);
    $('#unidadProducto').val(d[2]);
    $('#pVenProducto').val(d[3]);
    $('#pComProducto').val(d[4]);
    $('#stockProducto').val(d[5]);
    $('#codBarras').val(d[6]);
    $('#fVenciProducto').val(d[7]);
    $('#pActivo').val(d[8]);
    $('#loteProducto').val(d[14]);
    $('#codigoDigemid').val(d[10]);
    $('#presProducto').val(d[11]);
    //$('#fotProducto').val(d[12]);
    $('#catProducto').val(d[18]);
    $('#pvProducto').val(d[13]);
    $('#stockminimo').val(d[9]);
    $('#obsProducto').val(d[15]);
    $('#regiSaProducto').val(d[16]);
    $('#preBlisProducto').val(d[17]);
}



</script>
