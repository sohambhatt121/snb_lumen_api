<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(definition="Admin")
 */
class Admin extends BaseModel
{

    protected $table='admin';
    
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $admin_id;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $admin_firstname;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $admin_lastname;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $admin_email;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $admin_contact;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $admin_user_name;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $admin_password;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=false)
     * @SWG\Property()
     */
    public $admin_type;

}