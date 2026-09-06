<?php

// Rango jerárquico de cada rol. Se usa SOLO para decidir quién puede aprobar
// una solicitud de anulación (debe ser rango igual o superior al solicitante,
// y nunca puede ser el rol "auxiliar").
return [
    'auxiliar' => 0,
    'coordinacion' => 1,
    'gerencia' => 2,
    'admin' => 3,
];
