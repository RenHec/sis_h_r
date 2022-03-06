<?php

namespace App\Http\Controllers\V1\Compartido\Menu;

use App\Models\V1\Seguridad\Menu;
use App\Http\Controllers\ApiController;

class MenuController extends ApiController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dato = Menu::all();
        return $this->showAll($dato);
    }
}
