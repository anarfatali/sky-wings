<?php

namespace App\Models\enums;

enum Aircraft: string
{
    case BOEING_737 = 'Boeing 737';
    case BOEING_777 = 'Boeing 777';
    case AIRBUS_A350 = 'Airbus A350';
    case BOEING_787 = 'Boeing 787';
    case AIRBUS_A380 = 'Airbus A380';
    case BOEING_767 = 'Boeing 767';
    case AIRBUS_A330 = 'Airbus A330';
    case BOEING_747 = 'Boeing 747';
    case AIRBUS_A320 = 'Airbus A320';
    case AIRBUS_A321 = 'Airbus A321';
    case AIRBUS_A319 = 'Airbus A319';
}
