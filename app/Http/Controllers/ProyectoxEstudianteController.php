<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProyectoxEstudiante;
use Illuminate\Support\Facades\Auth;

class ProyectoxEstudianteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if(!$request->ajax()) return redirect('/home');
        $proyectoXestudiantes = ProyectoxEstudiante::all();
        return $proyectoXestudiantes;
    }
    public function proyectosPorId(Request $request)
    {
        if(!$request->ajax()) return redirect('/home');
        $id = Auth()->user()->idUser;
        $proyectos = ProyectoxEstudiante::join('proyecto', 'proyecto.idProyecto', '=','proyectoxestudiante.idProyecto')
        ->join('users', 'proyectoxestudiante.idUser','=','users.idUser')
        ->select('proyecto.idProyecto', 'proyecto.nombre','proyecto.descripcion','proyecto.estado',
        'proyecto.tipo_horas', 'proyecto.cupos', 'proyecto.horario', 'proyecto.encargado','proyecto.fecha_inicio','proyecto.fecha_fin')
        ->where('proyectoxestudiante.idUser','=', $id)
        ->orderBy('proyecto.idProyecto', 'desc')->paginate(10);

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

    public function pxePorId (Request $request) {
        $req = $request->idUser;
        $pXe = ProyectoxEstudiante::query('SELECT * FROM proyectoxestudiante pxe WHERE pxe.idUser = :req')->get();
        return $pXe;
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
        $pXe = new ProyectoxEstudiante();
        $pXe->idProyecto = $request->idProyecto;
        $pXe->idUser = $request->idUser;
        $pXe->estado = $request->estado;
        $pXe->modifiedBy = $request->modifiedBy;
        $pXe->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // mostrar proyectos dependiendo de la carrera
        
    }

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

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {/*
        $proyecto = Proyecto::findOrFail($request->id);
        $proyecto->estado = $request->estado;
        $proyecto->contraparte = $request->contraparte;
        $proyecto->cupos = $request->cupos;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->encargado = $request->encargado;
        $proyecto->fecha_inicio = $request->fecha_inicio;
        $proyecto->fecha_fin = $request->fecha_fin;
        $proyecto->horario = $request->horario;
        $proyecto->nombre = $request->nombre;
        $proyecto->tipo_horas = $request->tipo_horas;
        $proyecto->modifiedBy = $request->modifiedBy;
        $proyecto->createdAt = $request->createdAt;
        $proyecto->save();*/
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    
    public function deleteRow(Request $request)
    {
        if(!$request->ajax()) return redirect('/home');
        $idUser = $request->idUser;
        $idProyecto = $request->idProyecto;
        ProyectoxEstudiante::where('idProyecto','=', $idProyecto)
        ->where('idUser','=', $idUser)
        ->delete();
    }
}
