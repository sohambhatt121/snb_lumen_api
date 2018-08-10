<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(definition="Token")
 */

class Token extends BaseModel
{
    protected $table='authtokens';
    
    public function user()
    {
        return $this->hasOne('App\User','user_email', 'user_email');      
    }

    public function admin()
    {
        return $this->hasOne('App\Admin','admin_email', 'user_email');      
    }

    public function adminid()
    {
        return $this->hasOne('App\Admin','admin_id', 'user_id');      
    }


    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $token_id;

    /**
     *
     * @var string
     * @Primary
     * @Identity
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $user_email;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $auth_token;

}