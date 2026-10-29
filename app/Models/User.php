<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

//use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Auth;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'email_otp',
        'optexpiretime',
        'user_id',
        'firstname',
        'lastname',
        'primary_contact_number',
        'username',
        'deviceType',
        'status',
        'defaultVehicle',
        'props',
        'device_token',
        'unlock_route'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    /**
     * User reletionship with role
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }

    public function driverDetail()
    {
        return $this->hasOne(Driverdetail::class, 'user_id');
    }
    public function merchandiserDetail()
    {
        return $this->hasOne(MerchandiserDetail::class, 'user_id');
    }
    public function routes()
    {
        return $this->hasMany(Routes::class, 'assigned_driver', 'id');
    }

    // public function warehouseTeamDetail()
    // {
    //     return $this->hasOne(WarehouseTeamDetail::class, 'warehouse_id', 'id');
    // }
    public function warehouseTeamDetail()
    {
        return $this->hasOne(WarehouseTeamDetail::class, 'team_member', 'id');
    }
    /**
     * Add Email database  validation 
     * 
     * @var email  
     */

    public  static function checkmail($emailOrPhone)
    {
        //return User::where('email', $email)->first();
         return User::where('email', $emailOrPhone)->orWhere('username', $emailOrPhone)->first();
    }
    public  static function checkOtp($otp, $email)
    {
       //return User::where('email', $email)->orWhere('primary_contact_number', $email)->where('email_otp', $otp)->first();
        return User::where('email_otp', $otp)
            ->where(function ($query) use ($email) {
                $query->where('username', $email)
                        ->orWhere('email', $email);
            })
            ->first();
        // return User::where('email_otp',$otp)
        //     ->where(function ($query) use ($email) {
        //         $query->orwhere(['primary_contact_number' => $email])
        //         ->orWhere(['email' => $email]);
        //     })->first();
    }

    public static function getAllUserList()
    {
        // Get all users
        return self::with([
            'role'
        ])->get();
    }
    
    /**
     * function to get
     * user which doesn't have 
     * role sub-driver' and
     *  carrier-owner
     */
    public static function getDatatableQuery()
    {
        // Get the query for datatable, but exclude users with specific roles
        if(Auth::user()->role->name == 'super-admin'){
            $excludedRoles = ['carrier-owner', 'sub-driver', 'super-admin'];
        }else if(Auth::user()->role->name == 'admin') {
            $excludedRoles = ['carrier-owner', 'sub-driver', 'admin', 'super-admin'];
        }   
        return self::query()->whereDoesntHave('role', function ($query) use ($excludedRoles) {
            $query->whereIn('name', $excludedRoles);
        })->orderBy('id', 'desc');        
    }


    /**
     * Get the query for datatable.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    // public static function getDatatableQuery()
    // {

    //     // Get the query for datatable
    //     return self::query();
    // }


    // Encode the  notes from JSON format

// 
/**
 * Get user based on Role id
 */

public static function getUserListBasedRoleid($role_id){
   return self::where(['role_id'=>$role_id,'status'=>'active'])->get(); 
}

/**
 * Get user based on  id
 */

 public static function getUserbyId($id){
    return self::where('id',$id)->first(); 
 }
 
}
