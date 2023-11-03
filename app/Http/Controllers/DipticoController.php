<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\DocumentosDiptico;

use Auth;
use Log;
use DB;
use Carbon\Carbon;
use View;
use Session;
use UploadHandler;

class DipticoController extends Controller {

    public function documentosDiptico(Request $request, $caso = null){
        
        if(!$caso){
            $caso = $request->input("caso");
        }
        
        $documentos = DocumentosDiptico::where("caso","=",$caso)->where("visible",true)->get();

        $resp = View::make("Gestion/gestionEnfermeria/Diptico/verDocumentosDiptico", [
            "documentos" => $documentos
        ])->render();

        Session::put("caso_documento_diptico", $caso);

        return response()->json(["contenido" => $resp]);
    }

    public function fileuploadDiptico(){
        $options = array('upload_dir' => storage_path().'/data/documentosDiptico/','upload_url' => storage_path().'/data/documentosDiptico/');
        $guardar = new UploadHandler($options);
    }

    public function ingresarDocumentoDiptico(Request $request){
        try {
            DB::beginTransaction();
            $caso = Session::get("caso_documento_diptico");
            $documento = new DocumentosDiptico;
            $file = $request->input("files");
            $documento->caso = $caso;
            $documento->documento = $file[0]["name"];
            $documento->usuario_responsable = Auth::user()->id;
            $documento->fecha_creacion = Carbon::now()->format("Y-m-d H:i:s");
            $documento->url = $file[0]["url"];
            $documento->visible = true;
            $documento->save();
            DB::commit();
            return $this->documentosDiptico($request, $caso);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(["error" => "Error al agregar documento"]);
        }
    }

    public function quitarDocumentoDiptico($id){
        try {
            DB::beginTransaction();
            $documento = DocumentosDiptico::where("id",$id)->where("visible",true)->first();
            $documento->visible = false;
            $documento->usuario_modifica = Auth::user()->id;
            $documento->fecha_modificacion = Carbon::now()->format("Y-m-d H:i:s");
            $documento->save();

            $request = new Request([
                'caso' => $documento->caso
            ]);

            DB::commit();

            return response()->json([
				"exito" => "Documento eliminado exitosamente",
				"contenido" => $this->documentosDiptico($request, $documento->caso)
			]);
        } catch (Exception $ex) {
            DB::rollBack();
            return response()->json(["error" => "Error al eliminar documento"]);
        }
    }
}
