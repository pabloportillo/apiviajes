<?php

namespace App\Entity;

/**
 * Representa un vuelo "FlightSegment"
 */
class FlightSegment
{
    private string $originCode;
    private string $originName;
    private string $destinationCode;
    private string $destinationName;
    private string $start;
    private string $end;
    private string $transportNumber;
    private string $companyCode;
    private string $companyName;

    public function __construct(
        string $originCode,
        string $originName,
        string $destinationCode,
        string $destinationName,
        string $start,
        string $end,
        string $transportNumber,
        string $companyCode,
        string $companyName
    ) {
        $this->originCode       = $originCode;
        $this->originName       = $originName;
        $this->destinationCode  = $destinationCode;
        $this->destinationName  = $destinationName;
        $this->start            = $start;
        $this->end              = $end;
        $this->transportNumber  = $transportNumber;
        $this->companyCode      = $companyCode;
        $this->companyName      = $companyName;
    }

    public function toArray(): array
    {
        return [
            'originCode'       => $this->originCode,
            'originName'       => $this->originName,
            'destinationCode'  => $this->destinationCode,
            'destinationName'  => $this->destinationName,
            'start'            => $this->start,
            'end'              => $this->end,
            'transportNumber'  => $this->transportNumber,
            'companyCode'      => $this->companyCode,
            'companyName'      => $this->companyName,
        ];
    }
}
