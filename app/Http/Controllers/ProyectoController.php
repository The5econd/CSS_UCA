<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Proyecto;
use App\ProyectoxCarrera;
use App\Carrera;

class ProyectoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request->ajax()) return redirect('/home');
        $proyectos = Proyecto::where('estado','=','1')->paginate(5);
        return [
            'pagination' => [
                'total'         => $proyectos->total(),
                'current_page'  => $proyectos->currentPage(),
                'per_page'      => $proyectos->perPage(),
                'last_page'     => $proyectos->lastPage(),
                'from'          => $proyectos->firstItem(),
                'to'            => $proyectos->lastItem(),
            ],
            'proyectos' => $proyectos
        ];
    }

    public function proyectosNoDisponibles(Request $request)
    {
        if(!$request->ajax()) return redirect('/home');
        $proyectos = Proyecto::where('estado','=','0')->paginate(5);
        return [
            'pagination' => [
                'total'         => $proyectos->total(),
                'current_page'  => $proyectos->currentPage(),
                'per_page'      => $proyectos->perPage(),
                'last_page'     => $proyectos->lastPage(),
                'from'          => $proyectos->firstItem(),
                'to'            => $proyectos->lastItem(),
            ],
            'proyectos' => $proyectos
        ];
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!$request->ajax()) return redirect('/home');
        $proyecto = new Proyecto();
        $proyecto->nombre = $request->nombre;
        $proyecto->estado = $request->estado;
        $proyecto->contraparte = $request->contraparte;
        $proyecto->cupos_act = $request->cupos_act;
        $proyecto->cupos = $request->cupos;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->encargado = $request->encargado;
        $proyecto->fecha_inicio = $request->fecha_inicio;
        $proyecto->fecha_fin = $request->fecha_fin;
        $proyecto->horario = $request->horario;
        $proyecto->tipo_horas = $request->tipo_horas;
        $proyecto->correo_encargado = $request->correo_encargado;
        $proyecto->save();

        $arraycp = $request->carreraPerfil;

        for($i = 0; $i < count($arraycp); $i++){
            if($arraycp[$i][0] == -1 || $arraycp[$i][0] == -2){
                $this->todasLasCarreras($proyecto->idProyecto, $arraycp[$i]);
            }
            else{
                $pxc = new ProyectoxCarrera();
                $pxc->idProyecto = $proyecto->idProyecto;
                $pxc->idCarrera = $arraycp[$i][0];
                $pxc->limite_inf = $arraycp[$i][1];
                $pxc->limite_sup = $arraycp[$i][2];
                $pxc->save();
            }
        }
    }

    private function todasLasCarreras(int $idProyecto, array $options){
        $carreras = Carrera::all();
        for($i = 0; $i < count($carreras); $i++){
            if($options[0] == -1 || ($options[0] == -2 && ($carreras[$i]->idCarrera != 3 && $carreras[$i]->idCarrera != 9 && $carreras[$i]->idCarrera != 10))){
                $pxc = new ProyectoxCarrera();
                $pxc->idProyecto = $idProyecto;
                $pxc->idCarrera = $carreras[$i]->idCarrera;
                $pxc->limite_inf = $options[1];
                $pxc->limite_sup = $options[2];
                $pxc->save();
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    public function update(Request $request)
    {
        if(!$request->ajax()) return redirect('/home');
        $proyecto = Proyecto::findOrFail($request->idProyecto);
        $proyecto->contraparte = $request->contraparte;
        $proyecto->cupos = $request->cupos;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->encargado = $request->encargado;
        $proyecto->fecha_inicio = $request->fecha_inicio;
        $proyecto->fecha_fin = $request->fecha_fin;
        $proyecto->horario = $request->horario;
        $proyecto->nombre = $request->nombre;
        $proyecto->tipo_horas = $request->tipo_horas;
        $proyecto->correo_encargado = $request->correo_encargado;
        $proyecto->save();

        ProyectoxCarrera::where('idProyecto', '=', $request->idProyecto)->delete();

        $arraycp = $request->carreraPerfil;

        for($i = 0; $i < count($arraycp); $i++){
            if($arraycp[$i][0] == -1 || $arraycp[$i][0] == -2){
                $this->todasLasCarreras($proyecto->idProyecto, $arraycp[$i]);
            }
            else{
                $pxc = new ProyectoxCarrera();
                $pxc->idProyecto = $proyecto->idProyecto;
                $pxc->idCarrera = $arraycp[$i][0];
                $pxc->limite_inf = $arraycp[$i][1];
                $pxc->limite_sup = $arraycp[$i][2];
                $pxc->save();
            }
        }
    }
    
    public function state(Request $request)
    {
        if(!$request->ajax()) return redirect('/home');
        $proyecto = Proyecto::findOrFail($request->idProyecto);
        $proyecto->estado = $request->estado;
        $proyecto->save();
    }

    public function cuposActuales(Request $request){
        return Proyecto::select('proyecto.cupos_act', 'proyecto.cupos')->where('idProyecto', '=', $request->idProyecto)->first();
    }
}