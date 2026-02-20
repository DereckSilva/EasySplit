<?php

namespace App;

enum LogActions : string
{
    case CREATE = 'create';
    case UPDATE = 'update';
    case DELETE = 'delete';
}
