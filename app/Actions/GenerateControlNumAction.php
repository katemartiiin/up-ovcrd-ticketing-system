<?php
namespace App\Actions;

use Carbon\Carbon;

use App\Models\Tickets\Ticket;

class GenerateControlNumAction {

    public function execute()
    {
        $controlNumber = "";
        $controlNumber .= Carbon::now()->format('Ym') . '-';

        $lastTicket = Ticket::orderBy('id', 'desc')->first();

        $nextId = $lastTicket ? ($lastTicket->id) + 1 : 1;

        switch ($nextId) {
            case $nextId <= 9:
                $controlNumber .= '000' . $nextId;
                break;
            case $nextId >= 10 && $nextId <= 99:
                $controlNumber .= '00' . $nextId;
                break;
            case $nextId >= 100 && $nextId <= 999:
                $controlNumber .= '0' . $nextId;
                break;
            case $nextId >= 1000 && $nextId <= 9999:
                $controlNumber .= $nextId;
                break;
            default:
                break;
        }

        return $controlNumber;
    }
}