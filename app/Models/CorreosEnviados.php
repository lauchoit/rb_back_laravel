<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorreosEnviados extends Model
{
  // use SoftDeletes;

    protected $dates        = ['deleted_at'];
    protected $table 			  = 'correosEnviados';
    protected $primaryKey 	= 'idCorreos';
    protected $fillable 		= [
        'idCorreos', 'mensaje', 'fechaServicio' , 'cantHoras' , 'cantPernocta', 'cantCorreos', 'totalMonto', 'bono_finSemana', 'ODC', 'realizado_por', 'registrado_por', 'created_at'
    ];

    /**
     *
     * Relaciones de la tabla
     *
     */
    public function r_realizado()
    {
        return $this->belongsTo('App\Models\User', 'realizado_por', 'id');
    }

    public function r_registrado()
    {
        return $this->belongsTo('App\Models\User', 'registrado_por', 'id');
    }

    public function r_recorridos()
    {
        return $this->hasMany('App\Models\Recorridos', 'correo_id', 'idCorreos');
    }

    public function r_correos_facturas()
    {
        return $this->hasMany('App\Models\Correo_Factura', 'correo_id', 'idCorreos');
    }
    /**
     *
     * Method Static of CorreosEviados Method
     *
     */
    public static function datosCorreo($option)
    {
        switch ($option) {
          case 'todos':
            $correos = CorreosEnviados::select('*')
                ->orderBy('created_at', 'DESC');
              break;
          case 'pendientes':
            $correos = CorreosEnviados::select('*')
                ->orderBy('created_at', 'DESC')
                ->where('facturado', "NO");
              break;
          case 'facturados':
            $correos = CorreosEnviados::select('*')
                ->orderBy('created_at', 'DESC')
                ->where('facturado', "SI");
              break;

          default:
            $correos = CorreosEnviados::where('idCorreos', $option);
            break;
        }

        if (!$correos->count()) {
            return null;
        }
        $temp = $correos->sum('totalMonto');
        $correos = $correos->paginate(25);

        foreach ($correos as $key => $correo) {

            $correo->r_realizado;
            $correo->r_registrado;
            $correo->r_recorridos;
        }
        $correos[0]->totalGeneral = $temp;
        // return $correos->totalFacturado;
        return $correos;
    }
}
