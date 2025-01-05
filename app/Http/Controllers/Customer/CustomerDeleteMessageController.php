<?php

declare(strict_types=1);

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Message as CustomerMessage;
use Illuminate\Http\Request;

class CustomerDeleteMessageController extends Controller
{
    public function delete(Request $request, int $id)
    {
        $message = CustomerMessage::findOrFail($id);

        // Podrías agregar una verificación aquí para asegurarte de que el usuario actual tiene permiso para eliminar el mensaje
        // Por ejemplo:
        // if($message->author_id !== Auth::user()->id) {
        //     return back()->withErrors(['You do not have permission to delete this message.']);
        // }

        // Borramos el mensaje
        $message->delete();

        // Redirigimos de vuelta a la página anterior
        return back(); // ->with('success', 'Message deleted successfully.');
    }
}
