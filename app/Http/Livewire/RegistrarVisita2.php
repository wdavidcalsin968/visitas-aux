<?php

namespace App\Http\Livewire;

use App\Models\Oficinas;
use App\Models\Sedes;
use Error;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class RegistrarVisita2 extends Component
{
    public $dni = '';
    public $nombre = '';
    public $apellido = '';
    public $oficinas = [];

    public $nombreOficina = '';
    public $name_sede = '';
    public $piso = '';
    public $isDni = false;
    public $isOficina = false;

    public function addDni(string $dni)
    {
        $this->dni = $dni;

        // Validating the input field with name dni
        $this->validate([
            'dni' => 'required'
        ]);

        if ($this->dni != '') {
            $token = 'apis-token-4993.klloKYrpr53i2bi2Au-yQ-l3p266O8iB';

            $curl = curl_init();

            curl_setopt_array($curl, array(

                CURLOPT_URL => 'https://api.apis.net.pe/v2/reniec/dni?numero=' . $this->dni,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 2,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Referer: https://apis.net.pe/consulta-dni-api',
                    'Authorization: Bearer ' . $token
                ),
            ));

            $response = curl_exec($curl);
            $dataDni = json_decode($response);

            // dd($dataDni);

            curl_close($curl);

            if (!isset($dataDni->message)) {
                $dni = $dataDni->numeroDocumento;
                $this->nombre = $dataDni->nombres;
                $this->apellido = $dataDni->apellidoPaterno . ' ' . $dataDni->apellidoMaterno;
                $this->isDni = true;
            } else {
                $this->dni = '';
                $this->nombre = '';
                $this->apellido = '';
                $this->addError('dni', 'El DNI ingresado no existe');
                $this->isDni = false;
            }
        }
    }

    public function updatedNombreOficina($name)
    {
        $oficinaName = Oficinas::where('nombre_oficina', $name)->first();
        // dd($oficinaName);

        if (isset($oficinaName)) {
            $this->name_sede = $oficinaName->sede->nombre_sede;
            $this->piso = $oficinaName->piso;
            $this->isOficina = true;
        } else {
            $this->piso = '';
            $this->name_sede = '';
            $this->addError('oficina', 'La oficina ingresada no existe');
            $this->isOficina = false;
        }
    }

    public function render()
    {
        return view('livewire.registrar-visita2');
    }
}
