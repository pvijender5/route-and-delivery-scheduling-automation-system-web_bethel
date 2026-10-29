<?php

namespace App\Constants;

class Roles
{

     //User role name for Webapp 
    const ADMIN = 'admin';
    const DRIVER = 'driver';
    const ROUTER = 'router';
    const MERCHANDISER = 'merchandiser';
    const CARRIEROWNER = 'carrier-owner';
    const SUBDRIVER = 'sub-driver';
    const INHOUSEDRIVER = 'in-house-driver';
    const LOADER = 'loader';
    const PARTYOWNER = 'party-owner';
    const DISPATCHER = 'dispatcher';
    
    //User role id for Webapp 
    const ADMIN_ID = 1;
    const ROUTER_ID = 7;
    const DISPATCHER_ID = 6;
    const SUPERADMIN_ID = 9;
    
    //User role for Mobile app
    const DRIVER_ID = 2;
    const CARRIEROWNER_ID = 3;
    const SUBDRIVER_ID = 4;
    const MERCHANDISER_ID = 5;

    const LOADER_ID = 8;
    
    //Constant Route Status
    const NEW = 'new';
    const NEW_SHIPMENT = 'new';
    const PLANNED = 'planned';
    const POSTED_TO_LOADBOARD = 'posted-to-loadboard';
    const DRIVER_ASSIGNED = 'driver-assigned';
    const LOADED = 'loaded';
    const LOADING = 'loading';
    const IN_TRANSIT_ON_TIME = 'in-transit-on-time';
    const IN_TRANSIT_DELAY = 'in-transit-delay';
    const IN_TRANSIT_ARRIVED = 'in-transit-arrived';
    const IN_TRANSIT_CANCELLED = 'in-transit-cancelled';
    const DELIVERED = 'delivered';
    const ASSIGNED_TO_WAREHOUSE = 'assigned-to-warehouse';
    const IN_PROGRESS = 'in-progress';
    const COMPLETED = 'completed';
    const CANCELED = 'canceled';
    const UNDELIVERED = 'undelivered';


    // 'planned','posted-to-loadboard','driver-assigned','loaded','loading','in-transit-on-time','in-transit-delay','in-transit-arrived','in-transit-cancelled','delivered','assigned-to-warehouse','in-progress','completed'


    //Constant Route Status for Plan
    const PLANROUTESTATUS = [
        'PLANNED' => 'planned',
        'POSTED_TO_LOADBOARD' => 'posted-to-loadboard',
        'DRIVER_ASSIGNED' => 'driver-assigned',
        'LOADED' => 'loaded',
        'LOADING' => 'loading',
    ];
    const TRACKROUTESTATUS = [
        'IN_TRANSIT_ON_TIME' => 'in-transit-on-time',
        'IN_TRANSIT_DELAY' => 'in-transit-delay',
        'IN_TRANSIT_ARRIVED' => 'in-transit-arrived',
        'IN_TRANSIT_CANCELLED' => 'in-transit-cancelled',
        'DELIVERED' => 'delivered',
        'COMPLETED' => 'completed',
    ];
    const ACUMETICA = [
        'PLANNING' => 'Planning',
        'COMPLETED' => 'Completed',
        'IN_PROGRESS' => 'In Progress',
    ];

    const TRACKROUTESTATUS_ARRAY = [
        'in-transit-on-time',
        'in-transit-delay',
        'in-transit-arrived',
        'in-transit-cancelled',
        'delivered',
        'completed'
    ];

    const PROGRESSROUTESTATUS_ARRAY = [
        'in-transit-on-time',
        'in-transit-delay',
        'in-transit-arrived',
        'in-transit-cancelled',
        'delivered'
    ];

    const INTRANSIRROUTESTATUS_ARRAY = [
        'in-transit-on-time',
        'in-transit-delay',
        'in-transit-arrived',
        'in-transit-cancelled'
    ];

    const PLANROUTESTATUS_ARRAY = [
        'planned',
        'posted-to-loadboard',
        'driver-assigned',
        'loaded',
        'loading'
    ];

    
    const ACTIVESHIPMENTS_ARRAY = [
        'planned', 'posted-to-loadboard', 'driver-assigned', 'in-progress', 'in-transit-on-time','in-transit-delay', 'delivered', 'canceled', 'undelivered', 'completed', 'loaded'
    ];
    

    const ACTIVEPICKUPSHIPMENTS_ARRAY = [
        'new', 'in-progress', 'canceled',  'completed', 'loaded'
    ];


    const PROGRESS_SHIPMENTS_ARRAY = [
        'planned', 'posted-to-loadboard', 'in-progress', 'driver-assigned', 'in-transit-on-time','in-transit-delay', 'loaded'
    ];

    const NOT_CANCELLED_SHIPMENTS_ARRAY = [
        'planned', 'posted-to-loadboard', 'driver-assigned', 'in-progress', 'in-transit-on-time','in-transit-delay', 'delivered',  'completed', 'loaded'
    ]; 
    
    const CANCELLED_SHIPMENTS_ARRAY = [
        'canceled', 'undelivered'
    ];

    const ACTIVE_ROUTE_ARRAY=
    ['loaded', 'in-transit-delay','in-transit-arrived', 'in-transit-on-time', 'in-transit-cancelled', 'delivered'];
}